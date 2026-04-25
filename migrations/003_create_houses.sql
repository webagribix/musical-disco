CREATE TABLE IF NOT EXISTS houses (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(255)               NOT NULL,
  capacity   INT UNSIGNED               NOT NULL DEFAULT 0 COMMENT 'maximum bird capacity',
  house_type ENUM('brooding','layer','broiler','general') NOT NULL DEFAULT 'general',
  notes      TEXT                       NULL,
  created_at TIMESTAMP                  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP                  NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
