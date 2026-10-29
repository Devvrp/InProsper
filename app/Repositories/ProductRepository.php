<?php
namespace InProsper\Repositories;
use InProsper\Core\Database;
use PDO;
final class ProductRepository {
    private PDO $db; public function __construct(){ $this->db=Database::connection(); }
    public function search(array $f=[]): array {
        $sql='SELECT p.*,m.business_name,m.business_address FROM products p JOIN merchants m ON m.merchant_id=p.merchant_id WHERE m.verification_status="disetujui" AND p.is_active=1 AND p.stock>0 AND CONCAT(p.expiry_date," ",COALESCE(p.expiry_time,"23:59:59")) >= NOW()';
        $args=[];
        if (!empty($f['q'])) { $sql.=' AND (p.name LIKE ? OR p.description LIKE ? OR m.business_name LIKE ?)'; $v='%'.$f['q'].'%'; array_push($args,$v,$v,$v); }
        if (!empty($f['category'])) { $sql.=' AND p.category=?'; $args[]=$f['category']; }
        if (isset($f['max_price']) && $f['max_price']!=='') { $sql.=' AND (p.normal_price * (1 - p.discount_percent/100)) <= ?'; $args[]=(float)$f['max_price']; }
        $sort=($f['sort']??'expiry')==='price'?'(p.normal_price * (1 - p.discount_percent/100)) ASC':'p.expiry_date ASC, p.expiry_time ASC';
        $sql.=" ORDER BY $sort LIMIT 100";
        $s=$this->db->prepare($sql); $s->execute($args); return $s->fetchAll();
    }
    public function findPublic(int $id): ?array {
        $s=$this->db->prepare('SELECT p.*,m.business_name,m.business_address,m.merchant_id FROM products p JOIN merchants m ON m.merchant_id=p.merchant_id WHERE p.product_id=? AND m.verification_status="disetujui" AND p.is_active=1'); $s->execute([$id]); return $s->fetch() ?: null;
    }
    public function findForMerchant(int $id,int $merchantId): ?array { $s=$this->db->prepare('SELECT * FROM products WHERE product_id=? AND merchant_id=?'); $s->execute([$id,$merchantId]); return $s->fetch() ?: null; }
    public function byMerchant(int $merchantId): array { $s=$this->db->prepare('SELECT * FROM products WHERE merchant_id=? ORDER BY created_at DESC'); $s->execute([$merchantId]); return $s->fetchAll(); }
    public function availableStock(int $productId, ?int $excludeOrderId=null): int {
        $sql='SELECT p.stock - COALESCE((SELECT SUM(od.quantity) FROM order_details od JOIN orders o ON o.order_id=od.order_id JOIN transactions t ON t.order_id=o.order_id WHERE od.product_id=p.product_id AND o.status="menunggu_pembayaran" AND t.payment_status="menunggu" AND t.expires_at>NOW()'.($excludeOrderId?' AND o.order_id<>?':'').'),0) FROM products p WHERE p.product_id=?';
        $s=$this->db->prepare($sql); $args=[]; if ($excludeOrderId) $args[]=$excludeOrderId; $args[]=$productId; $s->execute($args); return max(0,(int)$s->fetchColumn());
    }
    public function create(int $merchantId,array $d): int {
        $s=$this->db->prepare('INSERT INTO products (merchant_id,name,description,category,normal_price,discount_percent,stock,photo_url,expiry_date,expiry_time,is_active,created_at) VALUES (?,?,?,?,?,?,?,?,?,?,1,NOW())');
        $s->execute([$merchantId,$d['name'],$d['description'],$d['category'],$d['normal_price'],$d['discount_percent'],$d['stock'],$d['photo_url'],$d['expiry_date'],$d['expiry_time']?:null]); return (int)$this->db->lastInsertId();
    }
    public function update(int $productId,int $merchantId,array $d): void {
        $sql='UPDATE products SET name=?,description=?,category=?,normal_price=?,discount_percent=?,stock=?,photo_url=COALESCE(?,photo_url),expiry_date=?,expiry_time=?,is_active=? WHERE product_id=? AND merchant_id=?';
        $s=$this->db->prepare($sql); $s->execute([$d['name'],$d['description'],$d['category'],$d['normal_price'],$d['discount_percent'],$d['stock'],$d['photo_url'],$d['expiry_date'],$d['expiry_time']?:null,$d['is_active']?1:0,$productId,$merchantId]);
    }
    public function deactivate(int $id,int $merchantId): void { $s=$this->db->prepare('UPDATE products SET is_active=0 WHERE product_id=? AND merchant_id=?'); $s->execute([$id,$merchantId]); }
    public function decrementStock(int $id,int $qty): void { $s=$this->db->prepare('UPDATE products SET stock=stock-? WHERE product_id=? AND stock>=?'); $s->execute([$qty,$id,$qty]); if ($s->rowCount()!==1) throw new \RuntimeException('Stok tidak lagi mencukupi.'); }
    public function incrementStock(int $id,int $qty): void { $s=$this->db->prepare('UPDATE products SET stock=stock+? WHERE product_id=?'); $s->execute([$qty,$id]); }
    public function countRescuedMeals(): int { return (int)$this->db->query('SELECT COALESCE(SUM(od.quantity),0) FROM order_details od JOIN orders o ON o.order_id=od.order_id WHERE o.status IN ("dibayar","diproses","selesai")')->fetchColumn(); }
}
