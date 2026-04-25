CREATE TABLE IF NOT EXISTS vet_visits (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  batch_id      INT UNSIGNED   NOT NULL,
  vet_name      VARCHAR(255)   NULL,
  visit_date    DATE           NOT NULL,
  diagnosis     TEXT           NULL,
  prescription  TEXT           NULL,
  follow_up_date DATE          NULL,
  cost          DECIMAL(15,2)  NOT NULL DEFAULT 0.00,
  notes         TEXT           NULL,
  created_at    TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_batch_id  (batch_id),
  INDEX idx_visit_date(visit_date),
  CONSTRAINT fk_vet_batch FOREIGN KEY (batch_id) REFERENCES batches(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
