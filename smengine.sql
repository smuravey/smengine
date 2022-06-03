CREATE TABLE `articles` (
  `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `link` VARCHAR(100) NOT NULL COLLATE 'utf8mb4_unicode_ci',
  `title` VARCHAR(100) NOT NULL COLLATE 'utf8mb4_unicode_ci',
  `description` VARCHAR(2000) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
  `article` TEXT NOT NULL COLLATE 'utf8mb4_unicode_ci',
  `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('0','1','2') NOT NULL DEFAULT '0' COLLATE 'utf8mb4_unicode_ci',
  `comments` BIT(1) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `code` (`link`) USING BTREE
)
COLLATE='utf8mb4_unicode_ci'
ENGINE=InnoDB;

CREATE TABLE `comments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `aid` SMALLINT UNSIGNED NOT NULL,
  `path` VARCHAR(100) NOT NULL DEFAULT '0' COLLATE 'utf8mb4_unicode_ci',
  `name` VARCHAR(60) NOT NULL COLLATE 'utf8mb4_unicode_ci',
  `message` VARCHAR(2000) NOT NULL COLLATE 'utf8mb4_unicode_ci',
  `date` DATETIME NOT NULL,
  `status` ENUM('0','1','2') NOT NULL DEFAULT '0' COLLATE 'utf8mb4_unicode_ci',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `aid` (`aid`) USING BTREE,
  INDEX `name` (`name`) USING BTREE
)
COLLATE='utf8mb4_unicode_ci'
ENGINE=InnoDB;

CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(30) NOT NULL COLLATE 'utf8mb4_unicode_ci',
  `password` VARCHAR(100) NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_ci',
  `role` ENUM('user','admin') NOT NULL DEFAULT 'user' COLLATE 'utf8mb4_unicode_ci',
  `email` VARCHAR(50) NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_ci',
  `regdate` DATETIME NOT NULL,
  `visit` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name` (`name`) USING BTREE
)
COLLATE='utf8mb4_unicode_ci'
ENGINE=InnoDB;