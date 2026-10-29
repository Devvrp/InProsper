<?php
namespace InProsper\Services;
final class SelfPickupStrategy implements FulfillmentStrategy {
    public function calculateFee(): float { return 0.0; }
    public function initialStatus(): string { return 'belum_diproses'; }
}
