<?php
namespace InProsper\Services;
/** Adapter stub: tidak membuat booking kurir sungguhan. */
final class CourierServiceAdapter {
    public function estimateFee(string $businessAddress, string $destinationAddress): float { return 0.0; }
    public function requestPickup(int $orderId): string { return 'SIM-COURIER-'.str_pad((string)$orderId,6,'0',STR_PAD_LEFT); }
}
