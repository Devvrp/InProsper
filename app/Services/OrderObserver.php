<?php
namespace InProsper\Services;
interface OrderObserver { public function onOrderEvent(array $event): void; }
