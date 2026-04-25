CREATE TABLE IF NOT EXISTS weight_logs (
  id                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  batch_id           INT UNSIGNED   NOT NULL,
  average_weight_kg  DECIMAL(8,3)   NOT NULL,
  sample_count       INT UNSIGNED   NOT NULL DEFAULT 30,
  weighed_by         INT UNSIGNED   NULL,
  weighed_at         TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  notes              TEXT           NULL,
  created_at         TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_batch_id (batch_id),
  INDEX idx_weighed_at(weighed_at),
  CONSTRAINT fk_weight_batch FOREIGN KEY (batch_id) REFERENCES batches(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
