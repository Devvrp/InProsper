<?php
namespace InProsper\Services;
use InProsper\Repositories\NotificationRepository;
final class NotificationService {
    public function __construct(private NotificationRepository $repo) {}
    public function send(int $userId,string $message,?int $orderId=null): void { $this->repo->create($userId,$message,$orderId); }
    public function all(int $userId): array { return $this->repo->forUser($userId); }
    public function markAllRead(int $userId): void { $this->repo->markAllRead($userId); }
}
