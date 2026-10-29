<?php
namespace InProsper\Repositories;
use InProsper\Core\Database;
use PDO;
final class NotificationRepository {
    private PDO $db; public function __construct(){ $this->db=Database::connection(); }
    public function create(int $userId,string $message,?int $orderId=null): void { $s=$this->db->prepare('INSERT INTO notifications (user_id,order_id,message,is_read,created_at) VALUES (?,?,?,0,NOW())'); $s->execute([$userId,$orderId,$message]); }
    public function forUser(int $userId): array { $s=$this->db->prepare('SELECT * FROM notifications WHERE user_id=? ORDER BY created_at DESC LIMIT 100'); $s->execute([$userId]); return $s->fetchAll(); }
    public function unreadCount(int $userId): int { $s=$this->db->prepare('SELECT COUNT(*) FROM notifications WHERE user_id=? AND is_read=0'); $s->execute([$userId]); return (int)$s->fetchColumn(); }
    public function markAllRead(int $userId): void { $s=$this->db->prepare('UPDATE notifications SET is_read=1 WHERE user_id=?'); $s->execute([$userId]); }
}
