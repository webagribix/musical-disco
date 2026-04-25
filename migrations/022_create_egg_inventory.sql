CREATE TABLE IF NOT EXISTS egg_inventory (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  batch_id        INT UNSIGNED   NOT NULL,
  date            DATE           NOT NULL,
  opening_balance INT UNSIGNED   NOT NULL DEFAULT 0,
  collected       INT UNSIGNED   NOT NULL DEFAULT 0,
  sold            INT UNSIGNED   NOT NULL DEFAULT 0,
  damaged         INT UNSIGNED   NOT NULL DEFAULT 0,
  closing_balance INT UNSIGNED   NOT NULL DEFAULT 0,
  created_at      TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_batch_date (batch_id, date),
  CONSTRAINT fk_egg_inv_batch FOREIGN KEY (batch_id) REFERENCES batches(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
