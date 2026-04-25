CREATE TABLE IF NOT EXISTS users (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name         VARCHAR(255)                                      NOT NULL,
  email        VARCHAR(255)                                      NOT NULL UNIQUE,
  password     VARCHAR(255)                                      NOT NULL,
  role         ENUM('owner','manager','worker')                  NOT NULL DEFAULT 'worker',
  api_token    VARCHAR(80)                                       NULL     UNIQUE,
  phone        VARCHAR(30)                                       NULL,
  feature_mode ENUM('basic','advanced')                         NOT NULL DEFAULT 'basic',
  is_active    TINYINT(1)                                        NOT NULL DEFAULT 1,
  deleted_at   TIMESTAMP                                         NULL,
  created_at   TIMESTAMP                                         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   TIMESTAMP                                         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_email      (email),
  INDEX idx_api_token  (api_token),
  INDEX idx_role       (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
