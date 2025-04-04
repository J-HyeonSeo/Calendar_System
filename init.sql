-- 데이터베이스 생성 (이미 존재하면 생성되지 않음)
CREATE DATABASE IF NOT EXISTS calendar;
USE calendar;

-- member 테이블 생성
CREATE TABLE IF NOT EXISTS `member` (
    `member_id` int unsigned NOT NULL AUTO_INCREMENT,
    `username` varchar(20) NOT NULL,
    `password` varchar(255) NOT NULL,
    `nickname` varchar(10) NOT NULL,
    PRIMARY KEY (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- schedule 테이블 생성
CREATE TABLE IF NOT EXISTS `schedule` (
    `schedule_id` int unsigned NOT NULL AUTO_INCREMENT,
    `title` varchar(30) NOT NULL,
    `type` varchar(10) NOT NULL,
    `place` varchar(20) NOT NULL,
    `start_dt` datetime NOT NULL,
    `end_dt` datetime NOT NULL,
    PRIMARY KEY (`schedule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- role 테이블 생성
CREATE TABLE IF NOT EXISTS `role` (
    `role_id` int unsigned NOT NULL AUTO_INCREMENT,
    `role_name` varchar(20) NOT NULL,
    PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- participant 테이블 생성
CREATE TABLE IF NOT EXISTS `participant` (
    `participant_id` int unsigned NOT NULL AUTO_INCREMENT,
    `member_id` int unsigned NOT NULL,
    `schedule_id` int unsigned NOT NULL,
    PRIMARY KEY (`participant_id`),
    KEY `participant_member_id_fk_idx` (`member_id`),
    KEY `participant_schedule_id_fk_idx` (`schedule_id`),
    CONSTRAINT `participant_member_id_fk` FOREIGN KEY (`member_id`) REFERENCES `member` (`member_id`) ON DELETE CASCADE,
    CONSTRAINT `participant_schedule_id_fk` FOREIGN KEY (`schedule_id`) REFERENCES `schedule` (`schedule_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- member_to_role 테이블 생성
CREATE TABLE IF NOT EXISTS `member_to_role` (
    `member_to_role_id` int unsigned NOT NULL AUTO_INCREMENT,
    `member_id` int unsigned NOT NULL,
    `role_id` int unsigned NOT NULL,
    PRIMARY KEY (`member_to_role_id`),
    KEY `member_to_role_member_id_fk_idx` (`member_id`),
    KEY `member_to_role_role_id_fk_idx` (`role_id`),
    CONSTRAINT `member_to_role_member_id_fk` FOREIGN KEY (`member_id`) REFERENCES `member` (`member_id`) ON DELETE CASCADE,
    CONSTRAINT `member_to_role_role_id_fk` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 초기 데이터 삽입
INSERT INTO `member` (`username`, `password`, `nickname`) VALUES
    ('admin', '$2y$10$tiQE/ekV32V8y83mPXnrnuzypWdYpxQLSK6LpVdst/FQHORG1XGbG', 'Admin'),
    ('jerry', '$2y$10$tiQE/ekV32V8y83mPXnrnuzypWdYpxQLSK6LpVdst/FQHORG1XGbG', 'Jerry');

INSERT INTO `role` (`role_name`) VALUES
     ('ADMIN'),
     ('USER');

INSERT INTO `member_to_role` (`member_id`, `role_id`) VALUES
      (1, 1),
      (2, 2);
