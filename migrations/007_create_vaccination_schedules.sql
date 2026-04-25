CREATE TABLE IF NOT EXISTS vaccination_schedules (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  batch_id     INT UNSIGNED   NOT NULL,
  vaccine_name VARCHAR(255)   NOT NULL,
  due_date     DATE           NOT NULL,
  age_days     INT UNSIGNED   NULL,
  route        ENUM('eye_drop','drinking_water','injection','spray','oral') NOT NULL DEFAULT 'drinking_water',
  status       ENUM('pending','done','skipped')                             NOT NULL DEFAULT 'pending',
  completed_at TIMESTAMP      NULL,
  notes        TEXT           NULL,
  created_at   TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_batch_due (batch_id, due_date),
  INDEX idx_status    (status),
  CONSTRAINT fk_vsched_batch FOREIGN KEY (batch_id) REFERENCES batches(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
