CREATE TABLE IF NOT EXISTS batch_metrics_daily (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  batch_id      INT UNSIGNED   NOT NULL,
  date          DATE           NOT NULL,
  cost_per_bird DECIMAL(15,2)  NOT NULL DEFAULT 0.00,
  cost_per_egg  DECIMAL(15,2)  NOT NULL DEFAULT 0.00,
  fcr           DECIMAL(8,3)   NOT NULL DEFAULT 0.000,
  live_count    INT UNSIGNED   NOT NULL DEFAULT 0,
  created_at    TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_batch_date (batch_id, date),
  INDEX idx_batch_id (batch_id),
  CONSTRAINT fk_metrics_batch FOREIGN KEY (batch_id) REFERENCES batches(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
