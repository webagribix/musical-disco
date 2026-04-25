CREATE TABLE IF NOT EXISTS purchases (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  supplier_id   INT UNSIGNED   NULL,
  item_name     VARCHAR(255)   NOT NULL,
  category      VARCHAR(100)   NULL,
  quantity      DECIMAL(12,3)  NULL,
  unit          VARCHAR(50)    NULL,
  unit_cost     DECIMAL(15,2)  NULL,
  total_amount  DECIMAL(15,2)  NOT NULL,
  purchase_date DATE           NOT NULL,
  invoice_ref   VARCHAR(100)   NULL,
  notes         TEXT           NULL,
  recorded_by   INT UNSIGNED   NULL,
  created_at    TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_purchase_date(purchase_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
