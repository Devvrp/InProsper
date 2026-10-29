<?php
namespace InProsper\Controllers;
use InProsper\Core\Auth;
use InProsper\Repositories\NotificationRepository;
use InProsper\Services\NotificationService;
final class NotificationController {
    public function __construct(private NotificationService $service) {}
    public function index(): void { Auth::requireLogin(); render('notifications/index',['title'=>'Notifikasi','notifications'=>$this->service->all((int)Auth::user()['user_id'])]); }
    public function markAllRead(): void { Auth::requireLogin(); $this->service->markAllRead((int)Auth::user()['user_id']); flash('success','Semua notifikasi ditandai sudah dibaca.'); redirect('?page=notifications'); }
}
