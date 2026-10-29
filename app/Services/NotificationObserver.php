<?php
namespace InProsper\Services;
final class NotificationObserver implements OrderObserver {
    public function __construct(private NotificationService $notifications) {}
    public function onOrderEvent(array $event): void {
        $message=(string)($event['message']??'Ada pembaruan pesanan.'); $orderId=isset($event['order_id'])?(int)$event['order_id']:null;
        if (!empty($event['consumer_id'])) $this->notifications->send((int)$event['consumer_id'],$message,$orderId);
        if (!empty($event['merchant_user_id']) && !empty($event['notify_merchant'])) $this->notifications->send((int)$event['merchant_user_id'],$message,$orderId);
    }
}
