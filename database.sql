CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('user','admin') NOT NULL DEFAULT 'user',
  balance DECIMAL(14,2) NOT NULL DEFAULT 0,
  wager_remaining DECIMAL(14,2) NOT NULL DEFAULT 0,
  iban VARCHAR(64) NULL,
  is_banned TINYINT(1) NOT NULL DEFAULT 0,
  reset_token VARCHAR(80) NULL,
  reset_expires_at DATETIME NULL,
  created_at DATETIME NOT NULL
);

CREATE TABLE transactions (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  type ENUM('deposit','withdraw','bonus','bet') NOT NULL,
  amount DECIMAL(14,2) NOT NULL,
  status VARCHAR(30) NOT NULL,
  description VARCHAR(255) NULL,
  created_at DATETIME NOT NULL,
  INDEX idx_user_created (user_id, created_at),
  CONSTRAINT fk_tx_user FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE withdrawals (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  amount DECIMAL(14,2) NOT NULL,
  iban VARCHAR(64) NOT NULL,
  status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  note VARCHAR(255) NULL,
  created_at DATETIME NOT NULL,
  processed_at DATETIME NULL,
  INDEX idx_status_created (status, created_at),
  CONSTRAINT fk_withdraw_user FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE bonuses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(40) NOT NULL UNIQUE,
  name VARCHAR(120) NOT NULL,
  type ENUM('fixed','percent') NOT NULL DEFAULT 'fixed',
  value DECIMAL(14,2) NOT NULL,
  wager_multiplier DECIMAL(8,2) NOT NULL DEFAULT 1,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL
);

CREATE TABLE bonus_history (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  bonus_code VARCHAR(40) NOT NULL,
  bonus_name VARCHAR(120) NOT NULL,
  bonus_amount DECIMAL(14,2) NOT NULL,
  wager_required DECIMAL(14,2) NOT NULL,
  created_at DATETIME NOT NULL,
  INDEX idx_bonus_user (user_id, created_at),
  CONSTRAINT fk_bonus_user FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE site_settings (
  setting_key VARCHAR(80) PRIMARY KEY,
  setting_value TEXT NULL
);

CREATE TABLE login_attempts (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  ip_address VARCHAR(64) NOT NULL,
  email VARCHAR(190) NOT NULL,
  success TINYINT(1) NOT NULL,
  created_at DATETIME NOT NULL,
  INDEX idx_login_attempts (ip_address, email, created_at)
);

INSERT INTO users (name, email, password, role, balance, created_at) VALUES
('Admin', 'admin@casino.local', '$2y$12$zqsS30xUAiC3hySCui1UD.EKCMhDy8ZFexRelrr8ELaOkJ6gEvzZe', 'admin', 10000.00, NOW());

INSERT INTO bonuses (code, name, type, value, wager_multiplier, is_active, created_at) VALUES
('welcome', 'Hoş Geldin Bonusu', 'fixed', 5000.00, 5.00, 1, NOW()),
('daily', 'Günlük Bonus', 'fixed', 50.00, 1.50, 1, NOW());

INSERT INTO site_settings (setting_key, setting_value) VALUES
('site_name', 'RoyalGold Casino'),
('theme_accent', '#D4AF37'),
('logo_url', ''),
('live_support_script', '');
