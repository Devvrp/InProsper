<?php
namespace InProsper\Services;
final class DeliveryStrategy implements FulfillmentStrategy {
    // Tarif real akan dihitung melalui CourierServiceAdapter setelah penyedia kurir dipilih.
    public function calculateFee(): float { return 0.0; }
    public function initialStatus(): string { return 'belum_diproses'; }
}
