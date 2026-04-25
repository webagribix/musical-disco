CREATE TABLE IF NOT EXISTS water_consumption_logs (
  id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  batch_id         INT UNSIGNED   NOT NULL,
  quantity_liters  DECIMAL(12,3)  NOT NULL,
  recorded_by      INT UNSIGNED   NULL,
  recorded_at      TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  notes            TEXT           NULL,
  created_at       TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_batch_id  (batch_id),
  INDEX idx_recorded_at(recorded_at),
  CONSTRAINT fk_water_batch FOREIGN KEY (batch_id) REFERENCES batches(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
