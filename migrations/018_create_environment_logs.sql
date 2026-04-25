CREATE TABLE IF NOT EXISTS environment_logs (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  house_id      INT UNSIGNED   NOT NULL,
  temperature   DECIMAL(5,2)   NULL COMMENT 'Celsius',
  humidity      DECIMAL(5,2)   NULL COMMENT 'percentage',
  light_hours   DECIMAL(4,2)   NULL,
  co2_ppm       DECIMAL(8,2)   NULL,
  ammonia_ppm   DECIMAL(8,2)   NULL,
  recorded_by   INT UNSIGNED   NULL,
  recorded_at   TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  notes         TEXT           NULL,
  created_at    TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_house_id   (house_id),
  INDEX idx_recorded_at(recorded_at),
  CONSTRAINT fk_env_house FOREIGN KEY (house_id) REFERENCES houses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
