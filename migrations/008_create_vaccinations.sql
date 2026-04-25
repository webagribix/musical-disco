CREATE TABLE IF NOT EXISTS vaccinations (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  batch_id        INT UNSIGNED   NOT NULL,
  schedule_id     INT UNSIGNED   NULL,
  vaccine_name    VARCHAR(255)   NOT NULL,
  dosage          VARCHAR(100)   NULL,
  route           ENUM('eye_drop','drinking_water','injection','spray','oral') NOT NULL DEFAULT 'drinking_water',
  administered_by INT UNSIGNED   NULL,
  administered_at TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  batch_count     INT UNSIGNED   NULL COMMENT 'birds vaccinated',
  notes           TEXT           NULL,
  created_at      TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_batch_id (batch_id),
  CONSTRAINT fk_vac_batch    FOREIGN KEY (batch_id)    REFERENCES batches(id) ON DELETE CASCADE,
  CONSTRAINT fk_vac_schedule FOREIGN KEY (schedule_id) REFERENCES vaccination_schedules(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
