CREATE TABLE IF NOT EXISTS tasks (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  batch_id    INT UNSIGNED   NULL,
  task_type   ENUM('feeding','vaccination','cleaning','egg_collection','water_check','weight_check','vet_visit','medication') NOT NULL,
  description TEXT           NULL,
  due_date    DATE           NOT NULL,
  status      ENUM('pending','done','skipped') NOT NULL DEFAULT 'pending',
  assigned_to INT UNSIGNED   NULL,
  priority    ENUM('low','normal','high')      NOT NULL DEFAULT 'normal',
  notes       TEXT           NULL,
  created_at  TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_due_date   (due_date),
  INDEX idx_status     (status),
  INDEX idx_assigned_to(assigned_to)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
