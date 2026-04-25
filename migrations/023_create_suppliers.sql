CREATE TABLE IF NOT EXISTS suppliers (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(255)   NOT NULL,
  phone      VARCHAR(30)    NULL,
  email      VARCHAR(255)   NULL,
  address    TEXT           NULL,
  category   VARCHAR(100)   NULL COMMENT 'feed, chicks, equipment, medicine',
  notes      TEXT           NULL,
  created_at TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
