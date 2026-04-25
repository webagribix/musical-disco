CREATE TABLE IF NOT EXISTS batch_transfers (
  id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  batch_id       INT UNSIGNED   NOT NULL,
  from_house_id  INT UNSIGNED   NULL,
  to_house_id    INT UNSIGNED   NULL,
  count          INT UNSIGNED   NOT NULL,
  reason         VARCHAR(255)   NULL,
  transferred_at TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  recorded_by    INT UNSIGNED   NULL,
  created_at     TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_batch_id (batch_id),
  CONSTRAINT fk_transfer_batch FOREIGN KEY (batch_id) REFERENCES batches(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
