CREATE TABLE IF NOT EXISTS alerts (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  batch_id    INT UNSIGNED   NULL,
  type        VARCHAR(100)   NOT NULL COMMENT 'mortality_outbreak, rule_feed, rule_eggs, etc.',
  severity    ENUM('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  message     TEXT           NOT NULL,
  is_resolved TINYINT(1)     NOT NULL DEFAULT 0,
  resolved_at TIMESTAMP      NULL,
  resolved_by INT UNSIGNED   NULL,
  created_at  TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_batch_id    (batch_id),
  INDEX idx_is_resolved (is_resolved),
  INDEX idx_created_at  (created_at),
  INDEX idx_type        (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
