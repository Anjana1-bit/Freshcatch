CREATE DATABASE IF NOT EXISTS freshcatch CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE freshcatch;

CREATE TABLE users (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL, email VARCHAR(150) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL, role ENUM('buyer','seller','admin') NOT NULL DEFAULT 'buyer',
 phone VARCHAR(25), status ENUM('active','suspended') NOT NULL DEFAULT 'active', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
CREATE TABLE seller_profiles (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id INT UNSIGNED NOT NULL UNIQUE, shop_name VARCHAR(120) NOT NULL,
 description TEXT, location VARCHAR(150), approval_status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
 approved_at DATETIME NULL, FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
CREATE TABLE buyer_addresses (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id INT UNSIGNED NOT NULL, label VARCHAR(50) NOT NULL, recipient_name VARCHAR(100) NOT NULL,
 phone VARCHAR(25) NOT NULL, address_line TEXT NOT NULL, city VARCHAR(80) NOT NULL, postal_code VARCHAR(20), is_default TINYINT(1) DEFAULT 0,
 FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE, INDEX(user_id)
) ENGINE=InnoDB;
CREATE TABLE categories (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(80) NOT NULL UNIQUE, description VARCHAR(255), is_active TINYINT(1) DEFAULT 1, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB;
CREATE TABLE products (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, seller_id INT UNSIGNED NOT NULL, category_id INT UNSIGNED NOT NULL, name VARCHAR(140) NOT NULL,
 description TEXT NOT NULL, image_path VARCHAR(255), price DECIMAL(10,2) NOT NULL, stock DECIMAL(10,2) NOT NULL DEFAULT 0,
 min_quantity DECIMAL(10,2) NOT NULL DEFAULT 1, freshness_info VARCHAR(180), is_available TINYINT(1) DEFAULT 1,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 FOREIGN KEY(seller_id) REFERENCES seller_profiles(id) ON DELETE CASCADE, FOREIGN KEY(category_id) REFERENCES categories(id), INDEX(category_id), INDEX(seller_id), INDEX(price)
) ENGINE=InnoDB;
CREATE TABLE carts (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id INT UNSIGNED NOT NULL UNIQUE, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE) ENGINE=InnoDB;
CREATE TABLE cart_items (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, cart_id INT UNSIGNED NOT NULL, product_id INT UNSIGNED NOT NULL, quantity DECIMAL(10,2) NOT NULL, UNIQUE(cart_id,product_id), FOREIGN KEY(cart_id) REFERENCES carts(id) ON DELETE CASCADE, FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE) ENGINE=InnoDB;
CREATE TABLE orders (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, order_number VARCHAR(32) NOT NULL UNIQUE, buyer_id INT UNSIGNED NOT NULL, address_id INT UNSIGNED NULL,
 delivery_type ENUM('delivery','pickup') NOT NULL, delivery_name VARCHAR(100), delivery_phone VARCHAR(25), delivery_address TEXT,
 subtotal DECIMAL(10,2) NOT NULL, delivery_fee DECIMAL(10,2) NOT NULL DEFAULT 0, total DECIMAL(10,2) NOT NULL,
 payment_method ENUM('UPI_DEMO','COD') NOT NULL DEFAULT 'COD', payment_status ENUM('pending','paid','failed') NOT NULL DEFAULT 'pending',
 status ENUM('Pending','Accepted','Preparing','Ready for Pickup','Out for Delivery','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY(buyer_id) REFERENCES users(id), FOREIGN KEY(address_id) REFERENCES buyer_addresses(id) ON DELETE SET NULL, INDEX(buyer_id)
) ENGINE=InnoDB;
CREATE TABLE seller_orders (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, order_id INT UNSIGNED NOT NULL, seller_id INT UNSIGNED NOT NULL, subtotal DECIMAL(10,2) NOT NULL,
 status ENUM('Pending','Accepted','Rejected','Preparing','Ready for Pickup','Out for Delivery','Delivered','Cancelled') NOT NULL DEFAULT 'Pending', updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 UNIQUE(order_id,seller_id), FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE CASCADE, FOREIGN KEY(seller_id) REFERENCES seller_profiles(id), INDEX(seller_id)
) ENGINE=InnoDB;
CREATE TABLE order_items (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, seller_order_id INT UNSIGNED NOT NULL, product_id INT UNSIGNED NULL, product_name VARCHAR(140) NOT NULL, quantity DECIMAL(10,2) NOT NULL, unit_price DECIMAL(10,2) NOT NULL, line_total DECIMAL(10,2) NOT NULL, FOREIGN KEY(seller_order_id) REFERENCES seller_orders(id) ON DELETE CASCADE, FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE SET NULL) ENGINE=InnoDB;
CREATE TABLE reviews (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, buyer_id INT UNSIGNED NOT NULL, product_id INT UNSIGNED NOT NULL, rating TINYINT UNSIGNED NOT NULL, comment TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, UNIQUE(buyer_id,product_id), FOREIGN KEY(buyer_id) REFERENCES users(id), FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE, CHECK(rating BETWEEN 1 AND 5)) ENGINE=InnoDB;
CREATE TABLE reports (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, reporter_id INT UNSIGNED NOT NULL, product_id INT UNSIGNED NULL, reported_user_id INT UNSIGNED NULL, reason TEXT NOT NULL, status ENUM('open','resolved','dismissed') DEFAULT 'open', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY(reporter_id) REFERENCES users(id), FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE SET NULL, FOREIGN KEY(reported_user_id) REFERENCES users(id) ON DELETE SET NULL) ENGINE=InnoDB;
CREATE TABLE notifications (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id INT UNSIGNED NOT NULL, message VARCHAR(255) NOT NULL, is_read TINYINT(1) DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE) ENGINE=InnoDB;

INSERT INTO users (name,email,password_hash,role,phone) VALUES
('FreshCatch Admin','admin@freshcatch.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.','admin','9000000001'),
('Ananya Buyer','buyer@freshcatch.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.','buyer','9000000002'),
('Coastal Catch','seller1@freshcatch.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.','seller','9000000003'),
('Harbor Fresh','seller2@freshcatch.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.','seller','9000000004');
INSERT INTO seller_profiles(user_id,shop_name,description,location,approval_status,approved_at) VALUES (3,'Coastal Catch','Wild-caught fish delivered from the morning auction.','Kochi','approved',NOW()),(4,'Harbor Fresh','Reliable local seafood, cleaned to order.','Chennai','approved',NOW());
INSERT INTO categories(name,description) VALUES ('Freshwater Fish','Farm and river fresh fish'),('Marine Fish','Fresh sea catch'),('Shellfish','Clams, mussels and more'),('Prawns','Fresh prawns'),('Crabs','Live and cleaned crabs'),('Other Seafood','Seasonal seafood');
INSERT INTO products(seller_id,category_id,name,description,image_path,price,stock,min_quantity,freshness_info) VALUES
(1,2,'Premium Seer Fish','Firm, flavourful steaks cut fresh to order.','assets/images/fish-seer.svg',780,18,0.5,'Landed this morning'),(1,2,'Indian Mackerel','Oily, delicious everyday fish.','assets/images/fish-mackerel.svg',260,35,0.5,'Morning catch'),(2,1,'Fresh Rohu','Cleaned freshwater rohu, ideal for curry.','assets/images/fish-rohu.svg',220,28,1,'Farm fresh today'),(2,4,'Tiger Prawns','Large shell-on tiger prawns.','assets/images/fish-prawns.svg',620,12,0.5,'Chilled, never frozen');
INSERT INTO buyer_addresses(user_id,label,recipient_name,phone,address_line,city,postal_code,is_default) VALUES(2,'Home','Ananya Buyer','9000000002','12 Lake View Road, Panampilly Nagar','Kochi','682036',1);
