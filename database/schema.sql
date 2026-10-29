CREATE DATABASE IF NOT EXISTS inprosper_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE inprosper_db;
CREATE TABLE IF NOT EXISTS users (
 user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 email VARCHAR(255) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 role VARCHAR(20) NOT NULL,
 phone VARCHAR(30) NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CHECK (role IN ('konsumen','merchant','admin'))
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS merchants (
 merchant_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id INT UNSIGNED NOT NULL UNIQUE,
 verified_by_user_id INT UNSIGNED NULL,
 business_name VARCHAR(150) NOT NULL,
 business_address TEXT NOT NULL,
 business_category VARCHAR(80) NOT NULL DEFAULT 'Lainnya',
 document_url VARCHAR(255) NULL,
 verification_status VARCHAR(20) NOT NULL DEFAULT 'menunggu',
 rejection_reason TEXT NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_merchants_user FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE,
 CONSTRAINT fk_merchants_admin FOREIGN KEY(verified_by_user_id) REFERENCES users(user_id) ON DELETE SET NULL,
 CHECK (verification_status IN ('menunggu','disetujui','ditolak'))
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS products (
 product_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 merchant_id INT UNSIGNED NOT NULL,
 name VARCHAR(150) NOT NULL,
 description TEXT NULL,
 category VARCHAR(80) NOT NULL DEFAULT 'Lainnya',
 normal_price DECIMAL(12,2) NOT NULL,
 discount_percent DECIMAL(5,2) NOT NULL,
 stock INT NOT NULL DEFAULT 0,
 photo_url VARCHAR(255) NULL,
 expiry_date DATE NOT NULL,
 expiry_time TIME NULL,
 is_active TINYINT(1) NOT NULL DEFAULT 1,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_products_merchant FOREIGN KEY(merchant_id) REFERENCES merchants(merchant_id) ON DELETE CASCADE,
 CHECK (normal_price > 0), CHECK (discount_percent >= 30 AND discount_percent <= 70), CHECK (stock >= 0),
 INDEX idx_products_category(category), INDEX idx_products_expiry(expiry_date,expiry_time), INDEX idx_products_active(is_active)
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS orders (
 order_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 consumer_id INT UNSIGNED NOT NULL,
 order_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 status VARCHAR(30) NOT NULL DEFAULT 'menunggu_pembayaran',
 total_amount DECIMAL(12,2) NOT NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_orders_consumer FOREIGN KEY(consumer_id) REFERENCES users(user_id),
 CHECK (status IN ('menunggu_pembayaran','dibayar','diproses','selesai','dibatalkan')),
 INDEX idx_orders_consumer(consumer_id,order_date), INDEX idx_orders_status(status)
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS order_details (
 order_detail_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 order_id INT UNSIGNED NOT NULL,
 product_id INT UNSIGNED NOT NULL,
 quantity INT NOT NULL,
 unit_price DECIMAL(12,2) NOT NULL,
 subtotal DECIMAL(12,2) NOT NULL,
 CONSTRAINT fk_details_order FOREIGN KEY(order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
 CONSTRAINT fk_details_product FOREIGN KEY(product_id) REFERENCES products(product_id),
 CHECK (quantity > 0), INDEX idx_details_product(product_id)
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS transactions (
 transaction_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 order_id INT UNSIGNED NOT NULL UNIQUE,
 payment_reference VARCHAR(255) NULL,
 amount DECIMAL(12,2) NOT NULL,
 payment_status VARCHAR(30) NOT NULL DEFAULT 'menunggu',
 expires_at DATETIME NOT NULL,
 paid_at TIMESTAMP NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_transactions_order FOREIGN KEY(order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
 CHECK (payment_status IN ('menunggu','berhasil','gagal','kedaluwarsa','dikembalikan'))
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS deliveries (
 delivery_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 order_id INT UNSIGNED NOT NULL UNIQUE,
 fulfillment_method VARCHAR(20) NOT NULL,
 destination_address TEXT NULL,
 courier_reference VARCHAR(255) NULL,
 delivery_status VARCHAR(30) NOT NULL DEFAULT 'belum_diproses',
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_deliveries_order FOREIGN KEY(order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
 CHECK (fulfillment_method IN ('self-pickup','delivery')),
 CHECK (delivery_status IN ('belum_diproses','siap_diambil','dalam_pengantaran','selesai'))
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS notifications (
 notification_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id INT UNSIGNED NOT NULL,
 order_id INT UNSIGNED NULL,
 message TEXT NOT NULL,
 is_read TINYINT(1) NOT NULL DEFAULT 0,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_notifications_user FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE,
 CONSTRAINT fk_notifications_order FOREIGN KEY(order_id) REFERENCES orders(order_id) ON DELETE SET NULL,
 INDEX idx_notifications_user(user_id,is_read,created_at)
) ENGINE=InnoDB;
