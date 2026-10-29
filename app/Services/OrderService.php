<?php
namespace InProsper\Services;
use InProsper\Core\Database;
use InProsper\Repositories\ProductRepository;
use InProsper\Repositories\OrderRepository;
use InProsper\Repositories\MerchantRepository;
final class OrderService {
    private NotificationObserver $observer;
    private PaymentGatewayAdapter $gateway;
    public function __construct(private ProductRepository $products, private OrderRepository $orders, private MerchantRepository $merchants, private NotificationService $notifications) {
        $this->observer=new NotificationObserver($notifications); $this->gateway=new PaymentGatewayAdapter();
    }
    private function selectStrategy(string $method): FulfillmentStrategy {
        return match($method) { 'self-pickup'=>new SelfPickupStrategy(), 'delivery'=>new DeliveryStrategy(), default=>throw new \RuntimeException('Metode pemenuhan tidak valid.') };
    }
    public function createOrder(int $consumerId,int $productId,int $quantity,string $method,string $destination): int {
        if ($quantity < 1 || $quantity > 50) throw new \RuntimeException('Jumlah pesanan harus antara 1 dan 50.');
        if (!in_array($method,['self-pickup','delivery'],true)) throw new \RuntimeException('Metode pemenuhan tidak valid.');
        if ($method==='delivery' && trim($destination)==='') throw new \RuntimeException('Alamat pengiriman wajib diisi untuk metode delivery.');
        $strategy=$this->selectStrategy($method); $db=Database::connection(); $db->beginTransaction();
        try {
            $s=$db->prepare('SELECT p.*,m.verification_status,m.business_name,m.business_address FROM products p JOIN merchants m ON m.merchant_id=p.merchant_id WHERE p.product_id=? FOR UPDATE'); $s->execute([$productId]); $p=$s->fetch();
            if(!$p || !$p['is_active'] || $p['verification_status']!=='disetujui') throw new \RuntimeException('Produk tidak tersedia.');
            if (strtotime($p['expiry_date'].' '.($p['expiry_time']?:'23:59:59')) < time()) throw new \RuntimeException('Batas layak konsumsi produk telah terlewat.');
            $available=$this->products->availableStock($productId);
            if ($available < $quantity) throw new \RuntimeException('Stok tersedia tidak mencukupi. Stok saat ini: '.$available.'.');
            $unit=(float)$p['normal_price']*(1-(float)$p['discount_percent']/100); $subtotal=round($unit*$quantity,2);
            $fee=$method==='delivery' ? (new CourierServiceAdapter())->estimateFee((string)$p['business_address'],$destination) : $strategy->calculateFee();
            $total=round($subtotal+$fee,2);
            $id=$this->orders->create(['consumer_id'=>$consumerId,'total'=>$total],['product_id'=>$productId,'quantity'=>$quantity,'unit_price'=>$unit,'subtotal'=>$subtotal],$method,trim($destination));
            $db->commit(); return $id;
        } catch (\Throwable $e) { if($db->inTransaction())$db->rollBack(); throw $e; }
    }
    public function confirmPayment(int $orderId,int $consumerId,string $result): void {
        $payment=$this->gateway->simulate($result); $success=$payment['success'];
        $db=Database::connection(); $db->beginTransaction();
        try {
            $order=$this->orders->aggregate($orderId);
            if(!$order || (int)$order['consumer_id']!==$consumerId) throw new \RuntimeException('Pesanan tidak ditemukan.');
            if($order['status']!=='menunggu_pembayaran') throw new \RuntimeException('Status pesanan sudah berubah.');
            if(!empty($order['expires_at']) && strtotime((string)$order['expires_at']) < time()) {
                $this->orders->updatePayment($orderId,'kedaluwarsa'); $this->orders->updateStatus($orderId,'dibatalkan');
                $db->commit();
                $this->notifications->send($consumerId,"Batas pembayaran pesanan #ORD-$orderId telah berakhir. Pesanan dibatalkan.",$orderId);
                throw new \RuntimeException('Batas waktu pembayaran 15 menit telah berakhir. Buat pesanan baru jika produk masih tersedia.');
            }
            if(!$success) {
                $this->orders->updatePayment($orderId,'gagal'); $this->orders->updateStatus($orderId,'dibatalkan');
                $db->commit(); $this->notifications->send($consumerId,"Pembayaran pesanan #ORD-$orderId gagal. Pesanan dibatalkan.",$orderId); return;
            }
            foreach($order['items'] as $item) {
                $s=$db->prepare('SELECT stock FROM products WHERE product_id=? FOR UPDATE'); $s->execute([$item['product_id']]); $stock=(int)$s->fetchColumn();
                if ($stock < (int)$item['quantity']) throw new \RuntimeException('Stok tidak mencukupi untuk mengonfirmasi pembayaran.');
                $this->products->decrementStock((int)$item['product_id'],(int)$item['quantity']);
            }
            $this->orders->updatePayment($orderId,'berhasil'); $this->orders->updateStatus($orderId,'dibayar');
            $strategy=$this->selectStrategy((string)$order['fulfillment_method']); $this->orders->updateDelivery($orderId,$strategy->initialStatus());
            $merchant=$this->merchants->byId((int)$order['items'][0]['merchant_id']);
            $this->observer->onOrderEvent(['order_id'=>$orderId,'consumer_id'=>$consumerId,'merchant_user_id'=>$merchant['user_id']??null,'notify_merchant'=>true,'message'=>"Pembayaran pesanan #ORD-$orderId berhasil. Pesanan telah diteruskan ke merchant."]);
            $db->commit();
        } catch(\Throwable $e) { if($db->inTransaction())$db->rollBack(); throw $e; }
    }
    public function cancelOrder(int $orderId,int $userId,string $role,string $reason=''): void {
        $order=$this->orders->aggregate($orderId);
        if(!$order) throw new \RuntimeException('Pesanan tidak ditemukan.');
        $ownsConsumer=$role==='konsumen' && (int)$order['consumer_id']===$userId; $ownsMerchant=false;
        if($role==='merchant') { $m=$this->merchants->byUser($userId); foreach(($order['items']??[]) as $it){ if($m && (int)$it['merchant_id']===(int)$m['merchant_id'])$ownsMerchant=true; } }
        if(!$ownsConsumer && !$ownsMerchant) throw new \RuntimeException('Kamu tidak memiliki akses untuk pesanan ini.');
        if(!in_array($order['status'],['menunggu_pembayaran','dibayar'],true)) throw new \RuntimeException('Pesanan yang sudah diproses tidak dapat dibatalkan.');
        $db=Database::connection(); $db->beginTransaction();
        try {
            if($order['status']==='dibayar') { foreach($order['items'] as $item)$this->products->incrementStock((int)$item['product_id'],(int)$item['quantity']); $this->orders->updatePayment($orderId,'dikembalikan'); }
            else $this->orders->updatePayment($orderId,'gagal');
            $this->orders->updateStatus($orderId,'dibatalkan');
            $merchant=$this->merchants->byId((int)$order['items'][0]['merchant_id']);
            $msg="Pesanan #ORD-$orderId dibatalkan. ".($reason?'Alasan: '.$reason.'. ':'').($order['status']==='dibayar'?'Pengembalian dana disimulasikan.':'');
            $this->observer->onOrderEvent(['order_id'=>$orderId,'consumer_id'=>(int)$order['consumer_id'],'merchant_user_id'=>$merchant['user_id']??null,'notify_merchant'=>true,'message'=>$msg]);
            $db->commit();
        } catch(\Throwable $e) { if($db->inTransaction())$db->rollBack(); throw $e; }
    }
}
