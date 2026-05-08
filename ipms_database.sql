-- ============================================================
-- IPMS - Import Product Management System
-- Rwanda Revenue Authority (RRA) - Customs Division
-- ============================================================
-- HOW TO USE:
--   1. Open: http://localhost/phpmyadmin
--   2. Click "Import" tab
--   3. Choose this file → click "Go"
--   Done! Database ipms_db will be created automatically.
-- ============================================================

CREATE DATABASE IF NOT EXISTS ipms_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE ipms_db;

-- ── roles ────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS roles (
  role_id    INT AUTO_INCREMENT PRIMARY KEY,
  role_name  VARCHAR(50) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── users ────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
  user_id       INT AUTO_INCREMENT PRIMARY KEY,
  full_name     VARCHAR(100) NOT NULL,
  email         VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role_id       INT NOT NULL,
  is_active     TINYINT(1)  DEFAULT 1,
  last_login    DATETIME    NULL,
  created_at    TIMESTAMP   DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP   DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (role_id) REFERENCES roles(role_id)
);

-- ── hs_codes ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS hs_codes (
  hs_code_id       INT AUTO_INCREMENT PRIMARY KEY,
  code             VARCHAR(20)  NOT NULL UNIQUE,
  description      VARCHAR(300) NOT NULL,
  import_duty_rate DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  vat_rate         DECIMAL(5,2) NOT NULL DEFAULT 18.00,
  excise_duty_rate DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  category         VARCHAR(100),
  is_active        TINYINT(1) DEFAULT 1,
  created_at       TIMESTAMP  DEFAULT CURRENT_TIMESTAMP
);

-- ── suppliers ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS suppliers (
  supplier_id    INT AUTO_INCREMENT PRIMARY KEY,
  company_name   VARCHAR(200) NOT NULL,
  contact_person VARCHAR(100),
  email          VARCHAR(150),
  phone          VARCHAR(30),
  country        VARCHAR(100) NOT NULL,
  is_active      TINYINT(1) DEFAULT 1,
  created_at     TIMESTAMP  DEFAULT CURRENT_TIMESTAMP
);

-- ── products ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS products (
  product_id      INT AUTO_INCREMENT PRIMARY KEY,
  product_name    VARCHAR(200) NOT NULL,
  hs_code_id      INT NOT NULL,
  supplier_id     INT NULL,
  unit_of_measure VARCHAR(50) DEFAULT 'Unit',
  description     TEXT,
  is_active       TINYINT(1) DEFAULT 1,
  created_at      TIMESTAMP  DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (hs_code_id)  REFERENCES hs_codes(hs_code_id),
  FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id)
);

-- ── import_records ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS import_records (
  import_id         INT AUTO_INCREMENT PRIMARY KEY,
  reference_no      VARCHAR(50)   NOT NULL UNIQUE,
  product_id        INT NOT NULL,
  importer_id       INT NOT NULL,
  quantity          DECIMAL(12,2) NOT NULL,
  unit_price        DECIMAL(15,2) NOT NULL,
  country_of_origin VARCHAR(100)  NOT NULL,
  import_date       DATE NOT NULL,
  border_post       VARCHAR(100),
  status            ENUM('PENDING','VERIFIED','APPROVED','REJECTED','CLEARED') DEFAULT 'PENDING',
  notes             TEXT,
  created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id)  REFERENCES products(product_id),
  FOREIGN KEY (importer_id) REFERENCES users(user_id)
);

-- ── tax_calculations ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS tax_calculations (
  calc_id          INT AUTO_INCREMENT PRIMARY KEY,
  import_id        INT NOT NULL UNIQUE,
  taxable_value    DECIMAL(15,2) NOT NULL,
  import_duty_rate DECIMAL(5,2)  NOT NULL,
  import_duty_amt  DECIMAL(15,2) NOT NULL,
  vat_rate         DECIMAL(5,2)  NOT NULL,
  vat_amt          DECIMAL(15,2) NOT NULL,
  excise_duty_rate DECIMAL(5,2)  NOT NULL DEFAULT 0,
  excise_duty_amt  DECIMAL(15,2) NOT NULL DEFAULT 0,
  total_tax        DECIMAL(15,2) NOT NULL,
  total_payable    DECIMAL(15,2) NOT NULL,
  calculated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (import_id) REFERENCES import_records(import_id) ON DELETE CASCADE
);

-- ── payments ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS payments (
  payment_id     INT AUTO_INCREMENT PRIMARY KEY,
  import_id      INT NOT NULL,
  receipt_no     VARCHAR(50) NOT NULL UNIQUE,
  amount_paid    DECIMAL(15,2) NOT NULL,
  payment_method ENUM('BANK_TRANSFER','MTN_MOBILE','AIRTEL_MONEY','CASH','CHEQUE') DEFAULT 'BANK_TRANSFER',
  payment_status ENUM('PENDING','COMPLETED','FAILED','REFUNDED') DEFAULT 'COMPLETED',
  payment_date   DATE NOT NULL,
  bank_reference VARCHAR(100),
  verified_by    INT NULL,
  created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (import_id)   REFERENCES import_records(import_id),
  FOREIGN KEY (verified_by) REFERENCES users(user_id)
);

-- ── inventory ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS inventory (
  inventory_id       INT AUTO_INCREMENT PRIMARY KEY,
  product_id         INT NOT NULL UNIQUE,
  stock_quantity     DECIMAL(12,2) NOT NULL DEFAULT 0,
  reorder_level      DECIMAL(12,2) DEFAULT 10,
  warehouse_location VARCHAR(100),
  last_updated       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

-- ── audit_logs ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS audit_logs (
  log_id     BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT NULL,
  action     VARCHAR(200) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- ============================================================
-- SEED DATA
-- ============================================================

INSERT INTO roles (role_name) VALUES
('ADMIN'),('IMPORTER'),('FINANCE_OFFICER'),('WAREHOUSE_MANAGER'),('CUSTOMER');

-- ⚠️  Password for ALL demo users is:  Password123
-- Stored as plain text below — system auto-upgrades to bcrypt on first login
INSERT INTO users (full_name, email, password_hash, role_id) VALUES
('System Admin',        'admin@rra.gov.rw',     'Password123', 1),
('Jean Bosco Habimana', 'importer@rra.gov.rw',  'Password123', 2),
('Marie Claire Uwase',  'finance@rra.gov.rw',   'Password123', 3),
('Patrick Nkurunziza',  'warehouse@rra.gov.rw', 'Password123', 4),
('Alice Mukamana',      'customer@gmail.com',   'Password123', 5);

INSERT INTO hs_codes (code, description, import_duty_rate, vat_rate, excise_duty_rate, category) VALUES
('8471.30','Laptop computers and portable machines',      0.00, 18.00,  0.00,'Electronics'),
('8517.12','Telephones for cellular networks',            0.00, 18.00,  0.00,'Electronics'),
('2203.00','Beer made from malt',                        25.00, 18.00, 70.00,'Beverages'),
('1001.99','Wheat and meslin (other)',                    5.00, 18.00,  0.00,'Agriculture'),
('3004.90','Medicaments — pharmaceuticals',               0.00, 18.00,  0.00,'Pharma'),
('8703.23','Motor cars spark-ignition 1500-3000cc',      25.00, 18.00, 15.00,'Vehicles'),
('6203.42','Mens trousers and shorts cotton',            25.00, 18.00,  0.00,'Textiles'),
('1701.14','Raw cane sugar',                             25.00, 18.00,  0.00,'Food'),
('2710.19','Petroleum oils diesel and fuel oil',         10.00, 18.00, 40.00,'Fuel'),
('8528.72','Colour television receivers',                25.00, 18.00,  0.00,'Electronics');

INSERT INTO suppliers (company_name, contact_person, email, phone, country) VALUES
('Lenovo Technology China',   'Wang Lei',           'sales@lenovo.cn',      '+86-755-8888-0000','China'),
('Samsung Electronics Korea', 'Kim Jae-won',        'export@samsung.kr',    '+82-2-2255-0114',  'South Korea'),
('AB InBev Africa',           'John Smith',         'africa@abinbev.com',   '+27-11-782-3300',  'South Africa'),
('Uganda Grain Traders Ltd',  'David Okello',       'grain@ugtl.co.ug',    '+256-41-4344000',  'Uganda'),
('Cipla Quality Chemical',    'Dr. Aisha Namutebi', 'info@cqciafrica.com', '+256-41-4567890',  'Uganda'),
('Toyota East Africa',        'Rajesh Patel',       'sales@toyota-ea.com', '+254-20-6951000',  'Kenya');

INSERT INTO products (product_name, hs_code_id, supplier_id, unit_of_measure) VALUES
('Lenovo ThinkPad X1 Carbon',    1, 1,'Unit'),
('Samsung Galaxy A54 5G',         2, 2,'Unit'),
('Primus Beer 500ml',             3, 3,'Carton (24)'),
('Hard Red Wheat Grain',          4, 4,'Metric Ton'),
('Amoxicillin 500mg Capsules',    5, 5,'Box'),
('Toyota Hilux Double Cab 2024',  6, 6,'Unit'),
('Mens Chino Trousers',           7, NULL,'Dozen'),
('Refined Cane Sugar 50kg',       8, NULL,'Bag (50kg)'),
('Diesel Fuel AGO',               9, NULL,'Litre'),
('LG 55 inch Smart TV 4K UHD',  10, 2,'Unit');

INSERT INTO import_records (reference_no,product_id,importer_id,quantity,unit_price,country_of_origin,import_date,border_post,status) VALUES
('IMP-2024-00001',1,2,50,    850.00,'China',        '2024-01-10','Kigali Airport','CLEARED'),
('IMP-2024-00002',2,2,100,   320.00,'South Korea',  '2024-01-15','Kigali Airport','CLEARED'),
('IMP-2024-00003',3,2,500,    18.50,'South Africa', '2024-02-03','Rusumo',        'CLEARED'),
('IMP-2024-00004',4,2,20000,   0.38,'Uganda',       '2024-02-18','Kagitumba',     'CLEARED'),
('IMP-2024-00005',5,2,1000,   45.00,'Uganda',       '2024-03-05','Cyanika',       'APPROVED'),
('IMP-2024-00006',6,2,5,   28000.00,'Kenya',        '2024-03-20','Kigali Airport','PENDING'),
('IMP-2024-00007',7,2,200,    35.00,'China',        '2024-04-01','Rubavu',        'VERIFIED'),
('IMP-2024-00008',10,2,30,   950.00,'South Korea',  '2024-04-10','Kigali Airport','PENDING');

INSERT INTO tax_calculations (import_id,taxable_value,import_duty_rate,import_duty_amt,vat_rate,vat_amt,excise_duty_rate,excise_duty_amt,total_tax,total_payable) VALUES
(1, 42500.00, 0.00,    0.00, 18.00, 7650.00,  0.00,    0.00,  7650.00, 50150.00),
(2, 32000.00, 0.00,    0.00, 18.00, 5760.00,  0.00,    0.00,  5760.00, 37760.00),
(3,  9250.00,25.00, 2312.50, 18.00, 2074.50, 70.00, 6475.00, 10862.00, 20112.00),
(4,  7600.00, 5.00,  380.00, 18.00, 1440.40,  0.00,    0.00,  1820.40,  9420.40),
(5, 45000.00, 0.00,    0.00, 18.00, 8100.00,  0.00,    0.00,  8100.00, 53100.00);

INSERT INTO payments (import_id,receipt_no,amount_paid,payment_method,payment_status,payment_date,bank_reference,verified_by) VALUES
(1,'RCT-2024-00001', 50150.00,'BANK_TRANSFER','COMPLETED','2024-01-12','BK-TXN-884433',3),
(2,'RCT-2024-00002', 37760.00,'BANK_TRANSFER','COMPLETED','2024-01-17','BK-TXN-884501',3),
(3,'RCT-2024-00003', 20112.00,'MTN_MOBILE',   'COMPLETED','2024-02-05','MTN-20240205', 3),
(4,'RCT-2024-00004',  9420.40,'BANK_TRANSFER','COMPLETED','2024-02-20','BK-TXN-885100',3);

INSERT INTO inventory (product_id,stock_quantity,reorder_level,warehouse_location) VALUES
( 1,   50,   10,'Warehouse A - Shelf 1'),
( 2,  100,   20,'Warehouse A - Shelf 2'),
( 3,  500,   50,'Warehouse B - Cold Storage'),
( 4,20000, 2000,'Silos - Bay 3'),
( 5, 1000,  100,'Pharmacy Store'),
( 7,  200,   30,'Warehouse C - Rack 4'),
(10,   30,    5,'Electronics Bay');
