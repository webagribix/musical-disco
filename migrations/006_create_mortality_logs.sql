CREATE TABLE IF NOT EXISTS mortality_logs (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  batch_id    INT UNSIGNED                                     NOT NULL,
  count       INT UNSIGNED                                     NOT NULL DEFAULT 1,
  cause       ENUM('disease','injury','unknown','culling','other') NOT NULL DEFAULT 'unknown',
  notes       TEXT                                             NULL,
  recorded_by INT UNSIGNED                                     NULL,
  recorded_at TIMESTAMP                                        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_at  TIMESTAMP                                        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_batch_id    (batch_id),
  INDEX idx_recorded_at (recorded_at),
  CONSTRAINT fk_mortality_batch FOREIGN KEY (batch_id) REFERENCES batches(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
