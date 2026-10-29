<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
use InProsper\Core\Auth;
use InProsper\Core\Csrf;
use InProsper\Repositories\UserRepository;
use InProsper\Repositories\MerchantRepository;
use InProsper\Repositories\ProductRepository;
use InProsper\Repositories\OrderRepository;
use InProsper\Repositories\NotificationRepository;
use InProsper\Services\NotificationService;
use InProsper\Services\OrderService;
use InProsper\Controllers\AuthController;
use InProsper\Controllers\ProductController;
use InProsper\Controllers\OrderController;
use InProsper\Controllers\AdminController;
use InProsper\Controllers\NotificationController;

try {
    $users=new UserRepository(); $merchants=new MerchantRepository(); $products=new ProductRepository(); $orders=new OrderRepository(); $notificationRepo=new NotificationRepository(); $notifications=new NotificationService($notificationRepo);
    $orderService=new OrderService($products,$orders,$merchants,$notifications);
    $authController=new AuthController($users,$merchants,$notifications); $productController=new ProductController($products,$merchants); $orderController=new OrderController($orders,$products,$merchants,$orderService); $adminController=new AdminController($merchants,$orders,$products); $notificationController=new NotificationController($notifications);
    if ($_SERVER['REQUEST_METHOD']==='POST') {
        require_csrf(); $action=(string)($_POST['action']??'');
        switch($action) {
            case 'login': $authController->login(); break;
            case 'register': $authController->register(); break;
            case 'logout': $authController->logout(); break;
            case 'save_product': $productController->save(); break;
            case 'deactivate_product': $productController->deactivate(); break;
            case 'create_order': $orderController->create(); break;
            case 'simulate_payment': $orderController->simulatePayment(); break;
            case 'cancel_order': $orderController->cancel(); break;
            case 'merchant_status': $orderController->merchantStatus(); break;
            case 'review_merchant': $adminController->review(); break;
            case 'mark_all_read': $notificationController->markAllRead(); break;
            default: throw new RuntimeException('Aksi tidak dikenal.');
        }
    }
    $page=(string)($_GET['page']??'catalog');
    switch($page) {
        case 'login': render('auth/login',['title'=>'Masuk ke InProsper']); break;
        case 'register': render('auth/register',['title'=>'Daftar akun']); break;
        case 'logout': Auth::logout(); redirect('?page=login');
        case 'catalog': case 'home': $productController->catalog(); break;
        case 'product': $productController->detail(); break;
        case 'product-form': $productController->form(); break;
        case 'checkout': $orderController->checkout(); break;
        case 'payment': $orderController->payment(); break;
        case 'orders': $orderController->history(); break;
        case 'merchant': $orderController->merchantDashboard(); break;
        case 'merchant-orders': $orderController->merchantOrders(); break;
        case 'admin': $adminController->dashboard(); break;
        case 'notifications': $notificationController->index(); break;
        default: http_response_code(404); render('errors/404',['title'=>'Halaman tidak ditemukan']);
    }
} catch (Throwable $e) {
    if ($e instanceof PDOException) {
        http_response_code(500);
        render('errors/database',['title'=>'Database belum siap','message'=>'Periksa konfigurasi config/database.local.php, pastikan MySQL aktif, lalu impor database/schema.sql dan jalankan database/seed.php.']);
        exit;
    }
    flash('error',$e->getMessage());
    $back='?page=catalog'; $referer=$_SERVER['HTTP_REFERER']??'';
    if ($referer!=='') {
        $refHost=parse_url($referer,PHP_URL_HOST); $currentHost=explode(':',$_SERVER['HTTP_HOST']??'')[0]; $refPath=parse_url($referer,PHP_URL_PATH)??'/'; $refQuery=parse_url($referer,PHP_URL_QUERY);
        if (($refHost===null || strtolower((string)$refHost)===strtolower($currentHost)) && in_array($refPath,['/','/index.php'],true) && $refQuery) $back='?'.$refQuery;
    }
    redirect($back);
}
