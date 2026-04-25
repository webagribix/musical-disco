CREATE TABLE IF NOT EXISTS breeds (
  id                       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name                     VARCHAR(255)       NOT NULL,
  target_weight_by_week    JSON               NOT NULL COMMENT 'kg by week: {"1":0.1,"2":0.2,...}',
  expected_egg_rate        DECIMAL(5,2)       NOT NULL DEFAULT 70.00 COMMENT 'laying percentage',
  feed_intake_curve        JSON               NOT NULL COMMENT 'kg/week by age week',
  vaccination_template_id  INT UNSIGNED       NULL,
  notes                    TEXT               NULL,
  created_at               TIMESTAMP          NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at               TIMESTAMP          NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
