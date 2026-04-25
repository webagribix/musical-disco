CREATE TABLE IF NOT EXISTS feed_consumption_logs (
  id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  batch_id         INT UNSIGNED   NOT NULL,
  feed_inventory_id INT UNSIGNED  NULL,
  feed_type        VARCHAR(255)   NOT NULL,
  quantity_kg      DECIMAL(12,3)  NOT NULL,
  fed_by           INT UNSIGNED   NULL,
  fed_at           TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  notes            TEXT           NULL,
  created_at       TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_batch_id (batch_id),
  INDEX idx_fed_at   (fed_at),
  CONSTRAINT fk_feed_log_batch FOREIGN KEY (batch_id) REFERENCES batches(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
