CREATE DATABASE IF NOT EXISTS db_alat_elektromedis
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE db_alat_elektromedis;

CREATE TABLE IF NOT EXISTS alat_elektromedis (
  id INT(11) NOT NULL AUTO_INCREMENT,
  nama_alat VARCHAR(100) NOT NULL,
  tahun YEAR NOT NULL,
  merek VARCHAR(100) NOT NULL,
  lokasi VARCHAR(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS users (
  id INT(11) NOT NULL AUTO_INCREMENT,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO alat_elektromedis (id, nama_alat, tahun, merek, lokasi) VALUES
(2, 'Patient Monitor', 2024, 'Mindray', 'Ruang ICU'),
(3, 'Electrocardiograph (ECG)', 2023, 'GE Healthcare', 'Ruang Jantung'),
(4, 'Infusion Pump', 2022, 'B. Braun', 'Ruang Rawat Inap'),
(5, 'Defibrillator', 2024, 'ZOLL', 'Ruang IGD'),
(6, 'Suction Pump', 2023, 'Yuwell', 'Ruang Operasi')
ON DUPLICATE KEY UPDATE
nama_alat=VALUES(nama_alat), tahun=VALUES(tahun), merek=VALUES(merek), lokasi=VALUES(lokasi);
