CREATE TABLE IF NOT EXISTS feed_inventory (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  feed_type    VARCHAR(255)   NOT NULL COMMENT 'e.g. Starter, Grower, Layer',
  brand        VARCHAR(255)   NULL,
  quantity_kg  DECIMAL(12,3)  NOT NULL DEFAULT 0.000,
  unit_cost    DECIMAL(15,2)  NOT NULL DEFAULT 0.00 COMMENT 'KES per kg',
  supplier_id  INT UNSIGNED   NULL,
  purchase_date DATE          NULL,
  expiry_date  DATE           NULL,
  notes        TEXT           NULL,
  created_at   TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_feed_type (feed_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
