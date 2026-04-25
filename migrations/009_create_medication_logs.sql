CREATE TABLE IF NOT EXISTS medication_logs (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  batch_id        INT UNSIGNED   NOT NULL,
  drug_name       VARCHAR(255)   NOT NULL,
  dosage          VARCHAR(100)   NULL,
  route           ENUM('water','feed','injection','topical','oral') NOT NULL DEFAULT 'water',
  duration_days   INT UNSIGNED   NOT NULL DEFAULT 1,
  reason          VARCHAR(255)   NULL,
  administered_by INT UNSIGNED   NULL,
  administered_at TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  withdrawal_date DATE           NULL COMMENT 'withholding period end',
  notes           TEXT           NULL,
  created_at      TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_batch_id (batch_id),
  CONSTRAINT fk_med_batch FOREIGN KEY (batch_id) REFERENCES batches(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
