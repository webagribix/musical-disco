CREATE TABLE IF NOT EXISTS rules (
  id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name              VARCHAR(255)    NOT NULL,
  metric            ENUM('mortality','feed','eggs','weight','temperature','water') NOT NULL,
  condition_op      ENUM('gt','lt','drop_pct')                                    NOT NULL DEFAULT 'gt',
  threshold_value   DECIMAL(10,4)   NOT NULL,
  time_window_hours INT UNSIGNED    NOT NULL DEFAULT 48,
  severity          ENUM('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  is_active         TINYINT(1)      NOT NULL DEFAULT 1,
  created_at        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_is_active (is_active),
  INDEX idx_metric    (metric)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE rules ADD COLUMN IF NOT EXISTS `condition` VARCHAR(20) GENERATED ALWAYS AS (`condition_op`) VIRTUAL;
