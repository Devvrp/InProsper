<?php
use InProsper\Core\Auth;
use InProsper\Core\Csrf;
function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function redirect(string $to): never { header('Location: ' . $to); exit; }
function flash(string $key, ?string $message = null) {
    if ($message !== null) { $_SESSION['_flash'][$key] = $message; return null; }
    $value = $_SESSION['_flash'][$key] ?? null; unset($_SESSION['_flash'][$key]); return $value;
}
function csrf_field(): string { return '<input type="hidden" name="_token" value="'.e(Csrf::token()).'">'; }
function money($n): string { return 'Rp ' . number_format((float)$n, 0, ',', '.'); }
function discount_price(array $p): float { return (float)$p['normal_price'] * (1 - ((float)$p['discount_percent'] / 100)); }
function url(string $page, array $params = []): string { return '?' . http_build_query(array_merge(['page'=>$page], $params)); }
function upload_file(string $field, string $folder, array $allowedMime, int $maxBytes = 3145728): ?string {
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) return null;
    $f = $_FILES[$field];
    if ($f['error'] !== UPLOAD_ERR_OK || $f['size'] > $maxBytes) throw new RuntimeException('File gagal diunggah atau ukurannya terlalu besar (maksimal 3 MB).');
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    if (!in_array($mime, $allowedMime, true)) throw new RuntimeException('Format file tidak didukung.');
    $extMap = ['application/pdf'=>'pdf','image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
    $name = bin2hex(random_bytes(16)) . '.' . ($extMap[$mime] ?? 'bin');
    $base = __DIR__ . '/../storage/uploads/' . trim($folder, '/');
    if (!is_dir($base)) mkdir($base, 0775, true);
    if (!move_uploaded_file($f['tmp_name'], $base . '/' . $name)) throw new RuntimeException('Tidak dapat menyimpan file yang diunggah.');
    return 'storage/uploads/' . trim($folder, '/') . '/' . $name;
}
function render(string $view, array $data = []): void {
    $title = $data['title'] ?? 'InProsper';
    $user = Auth::user();
    extract($data, EXTR_SKIP);
    ob_start();
    $file = __DIR__ . '/../views/' . $view . '.php';
    if (!is_file($file)) $file = __DIR__ . '/../views/errors/404.php';
    require $file;
    $content = ob_get_clean();
    require __DIR__ . '/../views/layout.php';
}
function require_csrf(): void {
    if (!\InProsper\Core\Csrf::valid($_POST['_token'] ?? null)) {
        http_response_code(419); exit('Sesi formulir kedaluwarsa. Muat ulang halaman lalu coba lagi.');
    }
}
function current_page(): string { return (string)($_GET['page'] ?? 'catalog'); }
