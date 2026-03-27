-- ============================================
-- IMSIT SHOP — База данных интернет-магазина
-- НАН ЧОУ ВО Академия ИМСИТ
-- ============================================

CREATE DATABASE IF NOT EXISTS imsit_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE imsit_shop;

-- Категории товаров
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Товары
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255),
    is_featured TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Пользователи
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('user','admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Заказы
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
    payment_method ENUM('card','cash') DEFAULT 'cash',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Позиции заказа
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT,
    product_name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- Корзина (для гостей и зарегистрированных)
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(100),
    user_id INT,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================
-- Начальные данные
-- ============================================

INSERT INTO categories (name, slug, description) VALUES
('Ручки и карандаши', 'ruchki', 'Шариковые, гелевые, перьевые ручки и карандаши'),
('Тетради и блокноты', 'tetradi', 'Тетради, блокноты, записные книжки'),
('Папки и файлы', 'papki', 'Папки, файлы, скоросшиватели'),
('Бумага', 'bumaga', 'Офисная бумага, цветная бумага'),
('Маркеры и фломастеры', 'markery', 'Маркеры, фломастеры, выделители'),
('Степлеры и дыроколы', 'steplery', 'Степлеры, дыроколы, скобы');

INSERT INTO products (category_id, name, slug, description, price, stock, is_featured) VALUES
(1, 'Ручка гелевая IMSIT синяя', 'ruchka-gelevaya-imsit', 'Качественная гелевая ручка с логотипом академии ИМСИТ. Синие чернила, мягкое письмо.', 45.00, 200, 1),
(1, 'Набор карандашей HB (12 шт)', 'nabor-karandashey-hb', 'Классические графитные карандаши твёрдости HB. Идеальны для письма и черчения.', 120.00, 150, 1),
(1, 'Ручка шариковая IMSIT красная', 'ruchka-sharkovaya-krasnaya', 'Шариковая ручка с логотипом академии. Красные чернила.', 40.00, 180, 0),
(2, 'Тетрадь 48 л. IMSIT клетка', 'tetrad-48l-kletka', 'Тетрадь в клетку 48 листов с обложкой в фирменном стиле академии ИМСИТ.', 85.00, 300, 1),
(2, 'Тетрадь 96 л. IMSIT линейка', 'tetrad-96l-lineyka', 'Тетрадь в линейку 96 листов. Плотная обложка, белая бумага 80 г/м².', 130.00, 250, 1),
(2, 'Блокнот А5 IMSIT', 'bloknot-a5-imsit', 'Стильный блокнот А5 с логотипом академии. 100 листов в клетку.', 195.00, 120, 1),
(3, 'Папка-скоросшиватель IMSIT', 'papka-skorosshivatel', 'Пластиковая папка-скоросшиватель с логотипом ИМСИТ. Формат А4.', 65.00, 200, 0),
(3, 'Файлы А4 (100 шт)', 'fayly-a4-100sht', 'Прозрачные файлы-вкладыши А4. Упаковка 100 штук.', 250.00, 80, 0),
(4, 'Бумага А4 IMSIT (500 л)', 'bumaga-a4-500l', 'Офисная бумага А4 80 г/м². 500 листов с логотипом академии на упаковке.', 380.00, 100, 1),
(5, 'Маркер перманентный IMSIT', 'marker-permanentnyy', 'Перманентный маркер с логотипом академии. Чёрный цвет, толщина линии 1-3 мм.', 75.00, 160, 0),
(5, 'Набор маркеров-выделителей (5 цв)', 'nabor-markery-videliteley', 'Набор флуоресцентных маркеров-выделителей. 5 ярких цветов.', 220.00, 90, 1),
(6, 'Степлер IMSIT №24/6', 'stepler-imsit', 'Металлический степлер с логотипом академии. Скобы №24/6, до 30 листов.', 350.00, 60, 0);

-- Администратор по умолчанию (пароль: admin123)
INSERT INTO users (name, email, password, role) VALUES
('Администратор', 'admin@imsit.ru', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
