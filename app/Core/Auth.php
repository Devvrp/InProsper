<?php
namespace InProsper\Core;
final class Auth {
    public static function user(): ?array { return $_SESSION['user'] ?? null; }
    public static function check(): bool { return self::user() !== null; }
    public static function login(array $user): void {
        session_regenerate_id(true);
        $_SESSION['user'] = ['user_id'=>(int)$user['user_id'], 'name'=>$user['name'], 'email'=>$user['email'], 'role'=>$user['role']];
    }
    public static function logout(): void { unset($_SESSION['user']); session_regenerate_id(true); }
    public static function requireLogin(): void { if (!self::check()) { flash('error','Silakan masuk terlebih dahulu.'); redirect('?page=login'); } }
    public static function requireRole(string ...$roles): void {
        self::requireLogin();
        if (!in_array(self::user()['role'], $roles, true)) { http_response_code(403); render('errors/403', ['title'=>'Akses ditolak']); exit; }
    }
}
