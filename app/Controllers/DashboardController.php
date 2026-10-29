<?php
namespace InProsper\Controllers;

use InProsper\Repositories\MerchantRepository;
use InProsper\Repositories\OrderRepository;
use InProsper\Repositories\ProductRepository;

final class DashboardController {
    public function __construct(
        private MerchantRepository $merchants,
        private OrderRepository $orders,
        private ProductRepository $products
    ) {}

    public function index(): void {
        // Metrik global platform (berdasarkan SDD SKPL-F-009 & Bab 2.3)
        $verifiedCount = $this->merchants->verifiedCount();
        $rescuedMeals = $this->products->countRescuedMeals();
        $successfulOrders = $this->orders->countSuccessful();
        $grossTransactions = $this->orders->grossTransactions();

        // Ambil produk surplus terdekat dengan masa kedaluwarsa (Near-expiry)
        $activeProducts = $this->products->search(['sort' => 'expiry']);
        $totalActive = count($activeProducts);
        $featuredProducts = array_slice($activeProducts, 0, 6);

        // Riwayat aktivitas food rescue publik terkini
        $recentOrders = $this->orders->recentPublicOrders(5);

        render('dashboard/index', [
            'title' => 'Dashboard Platform · InProsper Food Rescue',
            'verified_count' => $verifiedCount,
            'rescued_meals' => $rescuedMeals,
            'successful_orders' => $successfulOrders,
            'gross_transactions' => $grossTransactions,
            'total_active' => $totalActive,
            'featured_products' => $featuredProducts,
            'recent_orders' => $recentOrders,
        ]);
    }
}

