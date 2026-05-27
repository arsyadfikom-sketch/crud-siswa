CREATE DATABASE sekolah;

USE sekolah;

CREATE TABLE siswa (

    id INT AUTO_INCREMENT PRIMARY KEY,

    nis VARCHAR(20),
    nama VARCHAR(100),
    jk VARCHAR(20),
    alamat TEXT,
    jurusan VARCHAR(100),

    foto VARCHAR(255),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

);
