<?php
namespace InProsper\Controllers;
use InProsper\Core\Auth;
use InProsper\Repositories\OrderRepository;
use InProsper\Repositories\ProductRepository;
use InProsper\Repositories\MerchantRepository;
use InProsper\Services\OrderService;
final class OrderController {
    public function __construct(private OrderRepository $orders, private ProductRepository $products, private MerchantRepository $merchants, private OrderService $service) {}
    public function checkout(): void { Auth::requireRole('konsumen'); $p=$this->products->findPublic((int)($_GET['id']??0)); if(!$p){flash('error','Produk tidak tersedia.');redirect('?page=catalog');} $p['available_stock']=$this->products->availableStock((int)$p['product_id']); render('orders/checkout',['title'=>'Checkout','product'=>$p,'quantity'=>max(1,(int)($_GET['qty']??1))]); }
    public function create(): void { Auth::requireRole('konsumen'); $id=$this->service->createOrder((int)Auth::user()['user_id'],(int)($_POST['product_id']??0),(int)($_POST['quantity']??1),(string)($_POST['fulfillment_method']??''),(string)($_POST['destination_address']??'')); flash('success','Pesanan dibuat. Selesaikan pembayaran pada halaman berikutnya.'); redirect('?page=payment&id='.$id); }
    public function payment(): void { Auth::requireRole('konsumen'); $this->orders->expireStalePending((int)Auth::user()['user_id']); $o=$this->orders->aggregate((int)($_GET['id']??0)); if(!$o || (int)$o['consumer_id']!==(int)Auth::user()['user_id']) {flash('error','Pesanan tidak ditemukan.');redirect('?page=orders');} render('orders/payment',['title'=>'Pembayaran','order'=>$o]); }
    public function simulatePayment(): void { Auth::requireRole('konsumen'); $id=(int)($_POST['order_id']??0); $result=(string)($_POST['result']??'success'); $success=$result==='success'; $this->service->confirmPayment($id,(int)Auth::user()['user_id'],$result); flash($success?'success':'error',$success?'Pembayaran simulasi berhasil. Pesanan telah tercatat.':'Pembayaran simulasi gagal dan pesanan dibatalkan.'); redirect('?page=orders'); }
    public function history(): void { Auth::requireRole('konsumen'); render('orders/history',['title'=>'Riwayat pesanan','orders'=>$this->orders->forConsumer((int)Auth::user()['user_id'])]); }
    public function cancel(): void { Auth::requireRole('konsumen','merchant'); $this->service->cancelOrder((int)($_POST['order_id']??0),(int)Auth::user()['user_id'],Auth::user()['role'],trim((string)($_POST['reason']??''))); flash('success','Pesanan berhasil dibatalkan sesuai aturan sistem.'); redirect(Auth::user()['role']==='merchant'?'?page=merchant-orders':'?page=orders'); }
    public function merchantDashboard(): void {
        Auth::requireRole('merchant'); $m=$this->merchants->byUser((int)Auth::user()['user_id']); if(!$m) {flash('error','Profil merchant tidak ditemukan.');redirect('?page=catalog');}
        $products=$this->products->byMerchant((int)$m['merchant_id']); $orders=$this->orders->forMerchant((int)$m['merchant_id']);
        render('merchant/dashboard',['title'=>'Dashboard merchant','merchant'=>$m,'products'=>$products,'orders'=>array_slice($orders,0,5)]);
    }
    public function merchantOrders(): void { Auth::requireRole('merchant'); $m=$this->merchants->byUser((int)Auth::user()['user_id']); if(!$m)redirect('?page=catalog'); render('merchant/orders',['title'=>'Kelola pesanan','merchant'=>$m,'orders'=>$this->orders->forMerchant((int)$m['merchant_id'])]); }
    public function merchantStatus(): void {
        Auth::requireRole('merchant'); $id=(int)($_POST['order_id']??0); $action=(string)($_POST['status_action']??''); $o=$this->orders->aggregate($id); $m=$this->merchants->byUser((int)Auth::user()['user_id']);
        if(!$o || !$m || (int)$o['items'][0]['merchant_id']!==(int)$m['merchant_id']) throw new \RuntimeException('Pesanan tidak ditemukan atau bukan milik tokomu.');
        if($action==='process' && $o['status']==='dibayar') { $this->orders->updateStatus($id,'diproses'); $courierRef=$o['fulfillment_method']==='delivery'?(new \InProsper\Services\CourierServiceAdapter())->requestPickup($id):null; $this->orders->updateDelivery($id,$o['fulfillment_method']==='self-pickup'?'siap_diambil':'dalam_pengantaran',$courierRef); $this->notificationsToOrder($o,"Pesanan #ORD-$id sedang diproses."); flash('success','Pesanan mulai diproses. Pembatalan tidak lagi tersedia.'); }
        elseif($action==='complete' && $o['status']==='diproses') { $this->orders->updateStatus($id,'selesai'); $this->orders->updateDelivery($id,'selesai'); $this->notificationsToOrder($o,"Pesanan #ORD-$id telah selesai."); flash('success','Pesanan ditandai selesai.'); }
        else throw new \RuntimeException('Transisi status pesanan tidak valid.');
        redirect('?page=merchant-orders');
    }
    private function notificationsToOrder(array $o,string $message): void { $r=new \InProsper\Repositories\NotificationRepository(); $r->create((int)$o['consumer_id'],$message,(int)$o['order_id']); $r->create((int)$o['merchant_user_id'],$message,(int)$o['order_id']); }
}
