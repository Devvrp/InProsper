<?php
namespace InProsper\Repositories;
use InProsper\Core\Database;
use PDO;
final class MerchantRepository {
    private PDO $db; public function __construct(){ $this->db=Database::connection(); }
    public function byUser(int $userId): ?array { $s=$this->db->prepare('SELECT m.*,u.name,u.email FROM merchants m JOIN users u ON u.user_id=m.user_id WHERE m.user_id=?'); $s->execute([$userId]); return $s->fetch() ?: null; }
    public function byId(int $id): ?array { $s=$this->db->prepare('SELECT m.*,u.name,u.email FROM merchants m JOIN users u ON u.user_id=m.user_id WHERE m.merchant_id=?'); $s->execute([$id]); return $s->fetch() ?: null; }
    public function create(int $userId,string $businessName,string $address,string $category,?string $document): int {
        $s=$this->db->prepare('INSERT INTO merchants (user_id,business_name,business_address,business_category,document_url,verification_status,created_at) VALUES (?,?,?,?,?,"menunggu",NOW())');
        $s->execute([$userId,$businessName,$address,$category,$document]); return (int)$this->db->lastInsertId();
    }
    public function pending(): array { return $this->db->query('SELECT m.*,u.name,u.email,u.phone FROM merchants m JOIN users u ON u.user_id=m.user_id WHERE m.verification_status="menunggu" ORDER BY m.created_at ASC')->fetchAll(); }
    public function review(int $merchantId,int $adminId,string $status,?string $reason): void {
        $s=$this->db->prepare('UPDATE merchants SET verification_status=?,verified_by_user_id=?,rejection_reason=? WHERE merchant_id=?'); $s->execute([$status,$adminId,$reason,$merchantId]);
    }
    public function verifiedCount(): int { return (int)$this->db->query('SELECT COUNT(*) FROM merchants WHERE verification_status="disetujui"')->fetchColumn(); }
}
