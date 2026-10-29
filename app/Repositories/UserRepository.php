<?php
namespace InProsper\Repositories;
use InProsper\Core\Database;
use PDO;
final class UserRepository {
    private PDO $db; public function __construct(){ $this->db=Database::connection(); }
    public function findByEmail(string $email): ?array { $s=$this->db->prepare('SELECT * FROM users WHERE email=? LIMIT 1'); $s->execute([$email]); return $s->fetch() ?: null; }
    public function findById(int $id): ?array { $s=$this->db->prepare('SELECT * FROM users WHERE user_id=?'); $s->execute([$id]); return $s->fetch() ?: null; }
    public function create(string $name,string $email,string $password,string $role,string $phone=''): int {
        $s=$this->db->prepare('INSERT INTO users (name,email,password_hash,role,phone,created_at) VALUES (?,?,?,?,?,NOW())');
        $s->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT),$role,$phone]); return (int)$this->db->lastInsertId();
    }
}
