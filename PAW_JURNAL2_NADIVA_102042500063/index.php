<?php
require_once 'config.php';
$pageTitle = 'Beranda';

$totalCourses = (int)$pdo->query('SELECT COUNT(*) FROM mata_kuliah')->fetchColumn();
$totalMaterials = (int)$pdo->query('SELECT COUNT(*) FROM materi')->fetchColumn();
$totalTasks = (int)$pdo->query('SELECT COUNT(*) FROM tugas')->fetchColumn();

$recentMaterials = $pdo->query(
    'SELECT materi.id, materi.judul, materi.deskripsi, materi.created_at, mata_kuliah.nama AS nama_mk
     FROM materi JOIN mata_kuliah ON materi.mata_kuliah_id = mata_kuliah.id
     ORDER BY materi.id DESC LIMIT 5'
)->fetchAll();

$upcomingTasks = $pdo->query(
    'SELECT tugas.id, tugas.judul, tugas.deadline, mata_kuliah.nama AS nama_mk
     FROM tugas JOIN mata_kuliah ON tugas.mata_kuliah_id = mata_kuliah.id
     ORDER BY (tugas.deadline IS NULL), tugas.deadline ASC, tugas.id DESC LIMIT 5'
)->fetchAll();

require 'header.php';
?>
<section class="hero">
    <div>
        <p class="eyebrow">LEARNING MANAGEMENT SYSTEM</p>
        <h1>Selamat datang di LMS EAD</h1>
        <p class="hero-text">Kelola mata kuliah, pelajari materi, dan pantau tugas dalam satu tempat.</p>
        <a class="button button-light" href="mata_kuliah.php">Lihat Mata Kuliah <span aria-hidden="true">→</span></a>
    </div>
    <div class="hero-art" aria-hidden="true">
        <div class="hero-circle"></div>
        <div class="hero-card"><span class="hero-card-icon">✓</span><strong>Belajar lebih teratur</strong><small>Materi dan tugas tersusun rapi</small></div>
        <div class="hero-dot"></div>
    </div>
</section>

<section class="section-heading">
    <div><p class="eyebrow">RINGKASAN</p><h2>Ringkasan LMS</h2></div>
    <span class="muted">Data terbaru dari database</span>
</section>

<div class="stats-grid">
    <article class="stat-card">
        <div class="stat-icon blue">▤</div>
        <p class="stat-label">Mata Kuliah</p>
        <strong class="stat-number"><?= $totalCourses ?></strong>
        <a href="mata_kuliah.php">Lihat daftar <span>→</span></a>
    </article>
    <article class="stat-card">
        <div class="stat-icon purple">▧</div>
        <p class="stat-label">Materi</p>
        <strong class="stat-number"><?= $totalMaterials ?></strong>
        <a href="materi.php">Lihat materi <span>→</span></a>
    </article>
    <article class="stat-card">
        <div class="stat-icon orange">✓</div>
        <p class="stat-label">Tugas</p>
        <strong class="stat-number"><?= $totalTasks ?></strong>
        <a href="tugas.php">Lihat tugas <span>→</span></a>
    </article>
</div>

<div class="dashboard-columns">
    <section class="panel">
        <div class="panel-heading"><div><p class="eyebrow">PEMBELAJARAN</p><h2>Materi terbaru</h2></div><a class="text-link" href="materi.php">Semua materi →</a></div>
        <?php if (!$recentMaterials): ?>
            <div class="empty-state"><span>▧</span><p>Belum ada materi. Tambahkan materi pertama kamu.</p><a href="materi.php">Tambah materi</a></div>
        <?php else: ?>
            <div class="item-list">
                <?php foreach ($recentMaterials as $item): ?>
                    <article class="list-item">
                        <div class="list-icon purple">▧</div>
                        <div class="list-content"><strong><?= e($item['judul']) ?></strong><p><?= e($item['nama_mk']) ?></p><?php if ($item['deskripsi']): ?><small><?= e($item['deskripsi']) ?></small><?php endif; ?></div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
    <section class="panel">
        <div class="panel-heading"><div><p class="eyebrow">DEADLINE</p><h2>Daftar tugas</h2></div><a class="text-link" href="tugas.php">Semua tugas →</a></div>
        <?php if (!$upcomingTasks): ?>
            <div class="empty-state"><span>✓</span><p>Belum ada tugas yang tercatat.</p><a href="tugas.php">Tambah tugas</a></div>
        <?php else: ?>
            <div class="item-list">
                <?php foreach ($upcomingTasks as $item): ?>
                    <article class="list-item">
                        <div class="list-icon orange">✓</div>
                        <div class="list-content"><strong><?= e($item['judul']) ?></strong><p><?= e($item['nama_mk']) ?></p><small>Deadline: <?= $item['deadline'] ? e(date('d M Y', strtotime($item['deadline']))) : 'Belum ditentukan' ?></small></div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>
<?php require 'footer.php'; ?>
