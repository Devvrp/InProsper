<?php
namespace InProsper\Services;
interface FulfillmentStrategy {
    public function calculateFee(): float;
    public function initialStatus(): string;
}
