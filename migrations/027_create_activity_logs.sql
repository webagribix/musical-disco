CREATE TABLE IF NOT EXISTS activity_logs (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id    INT UNSIGNED   NULL,
  action     VARCHAR(100)   NOT NULL,
  entity     VARCHAR(100)   NOT NULL,
  entity_id  INT UNSIGNED   NULL,
  old_values JSON           NULL,
  new_values JSON           NULL,
  ip_address VARCHAR(45)    NOT NULL DEFAULT '0.0.0.0',
  created_at TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_user_id   (user_id),
  INDEX idx_entity    (entity),
  INDEX idx_entity_id (entity_id),
  INDEX idx_created_at(created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
