<?php
require_once __DIR__ . '/functions.php';
$pageTitle = $pageTitle ?? 'LMS EAD';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | LMS EAD</title>
    <link rel="stylesheet" href="style.css?v=3">
</head>
<body>
<header class="topbar">
    <a class="brand" href="index.php"><span class="brand-mark">L</span><span>LMS EAD</span></a>
    <nav class="nav-links" aria-label="Navigasi utama">
        <a class="<?= $currentPage === 'index.php' ? 'active' : '' ?>" href="index.php">Beranda</a>
        <a class="<?= $currentPage === 'mata_kuliah.php' ? 'active' : '' ?>" href="mata_kuliah.php">Mata Kuliah</a>
        <a class="<?= $currentPage === 'materi.php' ? 'active' : '' ?>" href="materi.php">Materi</a>
        <a class="<?= $currentPage === 'tugas.php' ? 'active' : '' ?>" href="tugas.php">Tugas</a>
    </nav>
</header>
<main class="container">
<?php if ($notice = getFlash()): ?>
    <div class="alert <?= e($notice['type']) ?>"><?= e($notice['message']) ?></div>
<?php endif; ?>
