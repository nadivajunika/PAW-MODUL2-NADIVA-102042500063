
<?php
require_once __DIR__ . '/functions.php';

// Kode koneksi database PDO kamu tetap di bawah sini.

$host = 'localhost';
$dbname = 'lms_ead';
$username = 'root';
$password = ''; // Default Laragon biasanya kosong

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit(
        '<h2>Koneksi database gagal</h2>' .
        '<p>Pastikan MySQL di Laragon sudah berjalan dan database <b>lms_ead</b> sudah di-import.</p>' .
        '<p>Detail: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>'
    );
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('flash')) {
    function flash($type, $message)
    {
        $_SESSION['flash'][$type] = $message;
    }
}

if (!function_exists('redirect')) {
    function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('e')) {
    function e($value)
    {
        return htmlspecialchars(
            (string) $value,
            ENT_QUOTES,
            'UTF-8'
        );
    }
}