# Traceability Matrix InProsper

## 1. Tujuan

Matriks keterunutan ini menunjukkan hubungan antara kebutuhan fungsional InProsper (SKPL-F) dengan rancangan sistem yang mendukungnya, meliputi Activity Diagram (ACT), Sequence Diagram (SEQ), class, tabel basis data, dan antarmuka pengguna (UI).

Tanda `-` menunjukkan bahwa pada dokumen DPPL tidak terdapat diagram atau komponen yang dicantumkan untuk kebutuhan tersebut.

## 2. Traceability Matrix

| Kode Kebutuhan | Activity Diagram | Sequence Diagram | Class | Tabel Basis Data | UI |
|---|---|---|---|---|---|
| **SKPL-F-001 – Verifikasi Akun Merchant** | ACT-001 | SEQ-001, SEQ-001A | MerchantController, User, Merchant, UserRepository, MerchantRepository, NotificationService | users, merchants | UI-002, UI-012 |
| **SKPL-F-002 – Manajemen Listing Produk** | ACT-002 | SEQ-002 | ProductController, Product, Merchant, ProductRepository, MerchantRepository | products, merchants | UI-004, UI-009, UI-010 |
| **SKPL-F-003 – Pemesanan Produk** | ACT-003 | SEQ-003, SEQ-003A, SEQ-003B | OrderController, WebhookController, OrderService, Order, OrderDetail, Transaction, Delivery, FulfillmentStrategy, SelfPickupStrategy, DeliveryStrategy, PaymentGatewayAdapter, CourierServiceAdapter, OrderObserver, NotificationObserver, OrderEvent, OrderRepository, ProductRepository, MerchantRepository, NotificationService | orders, order_details, products, transactions, deliveries | UI-003, UI-004, UI-005, UI-006, UI-011 |
| **SKPL-F-004 – Registrasi dan Autentikasi Pengguna** | - | SEQ-004, SEQ-004A | AuthController, User, Merchant, UserRepository, MerchantRepository | users | UI-001, UI-002 |
| **SKPL-F-005 – Pencarian dan Filter Produk** | - | SEQ-005 | ProductController, Product, ProductRepository | products, merchants | UI-003 |
| **SKPL-F-006 – Riwayat Transaksi** | - | SEQ-006 | OrderController, Order, OrderRepository, MerchantRepository | orders, order_details, transactions, deliveries | UI-007, UI-011 |
| **SKPL-F-007 – Pembatalan Pesanan** | ACT-004 | SEQ-007 | OrderController, OrderService, Order, Transaction, OrderRepository, ProductRepository, PaymentGatewayAdapter, NotificationObserver | orders, transactions | UI-007, UI-011 |
| **SKPL-F-008 – Notifikasi Sistem** | - | SEQ-008 (notifikasi dibuat pada SEQ-001A, SEQ-003A, dan SEQ-007) | NotificationController, Notification, NotificationService, NotificationRepository, NotificationObserver | notifications, orders | UI-008 |
| **SKPL-F-009 – Dashboard Admin** | - | SEQ-009 | DashboardController, MerchantRepository, OrderRepository | merchants, orders, transactions | UI-012 |

## 3. Ringkasan Aturan Bisnis (SKPL-B)

| Kode Aturan | Aturan Bisnis | Kebutuhan Terkait |
|---|---|---|
| **SKPL-B-001** | Diskon produk harus berada pada rentang 30% sampai 70% dari harga normal. | SKPL-F-002 |
| **SKPL-B-002** | Hanya Merchant yang sudah diverifikasi Admin yang dapat membuat listing dan berjualan. | SKPL-F-001, SKPL-F-002 |
| **SKPL-B-003** | Platform memotong komisi 15% dari setiap transaksi yang berhasil. | SKPL-F-003 |
| **SKPL-B-004** | Stok dipotong permanen hanya setelah pembayaran berhasil; jika pembayaran gagal, stok dikembalikan. | SKPL-F-003 |
| **SKPL-B-005** | Pesanan hanya dapat dibatalkan sebelum berstatus "diproses". | SKPL-F-007 |
| **SKPL-B-006** | Notifikasi hanya berupa notifikasi in-app (tanpa email atau push notification). | SKPL-F-008 |

## 4. Catatan

1. Kode kebutuhan dan aturan bisnis mengikuti penomoran pada Subbab 2.3 DPPL.
2. Kode ACT, SEQ, nama class, nama tabel, dan kode UI dirangkum dari Matriks Keterunutan pada Bab 6 DPPL.
3. Pada tabel Bab 6 DPPL, kode kebutuhan untuk baris pertama tertulis `KPL-F-001`. Penulisan pada file ini dirapikan menjadi `SKPL-F-001` agar konsisten dengan kode kebutuhan lain dan penamaan pada Subbab 2.3.
4. Nama tabel menggunakan bentuk jamak huruf kecil sebagaimana dicantumkan pada rancangan basis data.
5. Matriks ini merupakan ringkasan keterunutan rancangan, bukan pengganti uraian kebutuhan, diagram, class diagram, atau rancangan antarmuka pada dokumen DPPL.