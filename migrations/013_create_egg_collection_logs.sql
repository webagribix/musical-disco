CREATE TABLE IF NOT EXISTS egg_collection_logs (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  batch_id      INT UNSIGNED   NOT NULL,
  good_eggs     INT UNSIGNED   NOT NULL DEFAULT 0,
  cracked_eggs  INT UNSIGNED   NOT NULL DEFAULT 0,
  dirty_eggs    INT UNSIGNED   NOT NULL DEFAULT 0,
  small_eggs    INT UNSIGNED   NOT NULL DEFAULT 0,
  floor_eggs    INT UNSIGNED   NOT NULL DEFAULT 0,
  collected_by  INT UNSIGNED   NULL,
  collected_at  TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  notes         TEXT           NULL,
  created_at    TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_batch_id    (batch_id),
  INDEX idx_collected_at(collected_at),
  CONSTRAINT fk_egg_batch FOREIGN KEY (batch_id) REFERENCES batches(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
