<?php
// Sesuaikan host, username, password, dan nama database dengan pengaturan lokal.
$conn = mysqli_connect('127.0.0.1', 'root', '', 'paw_modul2_coffee_cafe');
if (!$conn) {
    exit('Koneksi database gagal. Periksa layanan MySQL dan konfigurasi koneksi.php.');
}
mysqli_set_charset($conn, 'utf8mb4');
?>
