CREATE DATABASE IF NOT EXISTS lms_ead
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE lms_ead;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS tugas;
DROP TABLE IF EXISTS materi;
DROP TABLE IF EXISTS mata_kuliah;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE mata_kuliah (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(30) NOT NULL UNIQUE,
    nama VARCHAR(150) NOT NULL,
    dosen VARCHAR(150) NULL,
    deskripsi TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE materi (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mata_kuliah_id INT UNSIGNED NOT NULL,
    judul VARCHAR(180) NOT NULL,
    deskripsi TEXT NULL,
    isi MEDIUMTEXT NULL,
    link VARCHAR(2048) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_materi_mata_kuliah
        FOREIGN KEY (mata_kuliah_id) REFERENCES mata_kuliah(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE tugas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mata_kuliah_id INT UNSIGNED NOT NULL,
    judul VARCHAR(180) NOT NULL,
    deskripsi TEXT NULL,
    deadline DATE NULL,
    link VARCHAR(2048) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tugas_mata_kuliah
        FOREIGN KEY (mata_kuliah_id) REFERENCES mata_kuliah(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

INSERT INTO mata_kuliah (kode, nama, dosen, deskripsi) VALUES
('PAW101', 'Pemrograman Aplikasi Web', 'Dosen PAW', 'Pembelajaran pengembangan aplikasi web menggunakan PHP dan database.'),
('SI201', 'Analisis dan Perancangan Sistem Informasi', 'Dosen SI', 'Pembelajaran analisis kebutuhan dan perancangan sistem informasi.');

INSERT INTO materi (mata_kuliah_id, judul, deskripsi, isi, link) VALUES
(1, 'Pengenalan PHP', 'Materi dasar PHP untuk aplikasi web.', 'PHP adalah bahasa pemrograman sisi server yang dapat digunakan untuk membangun aplikasi web dinamis.', NULL);

INSERT INTO tugas (mata_kuliah_id, judul, deskripsi, deadline, link) VALUES
(1, 'Jurnal Praktikum 2', 'Selesaikan latihan dan dokumentasikan hasil praktikum.', NULL, NULL);
