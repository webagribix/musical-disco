CREATE TABLE IF NOT EXISTS expenses (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  batch_id     INT UNSIGNED   NOT NULL,
  category     ENUM('feed','chicks','medication','vaccination','labor','utilities','equipment','vet','transport','marketing','other') NOT NULL DEFAULT 'other',
  description  VARCHAR(500)   NULL,
  amount       DECIMAL(15,2)  NOT NULL,
  expense_date DATE           NOT NULL,
  supplier_id  INT UNSIGNED   NULL,
  receipt_ref  VARCHAR(100)   NULL,
  recorded_by  INT UNSIGNED   NULL,
  deleted_at   TIMESTAMP      NULL,
  created_at   TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_batch_id    (batch_id),
  INDEX idx_category    (category),
  INDEX idx_expense_date(expense_date),
  INDEX idx_deleted_at  (deleted_at),
  CONSTRAINT fk_expense_batch FOREIGN KEY (batch_id) REFERENCES batches(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
