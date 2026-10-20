<?php
require_once __DIR__ . '/config.php';

$pageTitle = 'Materi';

// Lanjutkan kode materi.php kamu di bawah sini.


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = post('action');
    if ($action === 'add') {
        $mataKuliahId = (int)post('mata_kuliah_id');
        $judul = post('judul');
        $deskripsi = post('deskripsi');
        $isi = post('isi');
        $link = post('link');

        if ($mataKuliahId < 1 || $judul === '') {
            flash('error', 'Mata kuliah dan judul materi wajib diisi.');
        } else {
            $stmt = $pdo->prepare('INSERT INTO materi (mata_kuliah_id, judul, deskripsi, isi, link) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$mataKuliahId, $judul, $deskripsi ?: null, $isi ?: null, $link ?: null]);
            flash('success', 'Materi berhasil ditambahkan.');
        }
        redirect('materi.php');
    }

    if ($action === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM materi WHERE id = ?');
        $stmt->execute([(int)post('id')]);
        flash('success', 'Materi berhasil dihapus.');
        redirect('materi.php');
    }
}

$courses = $pdo->query('SELECT id, kode, nama FROM mata_kuliah ORDER BY nama')->fetchAll();
$selectedCourse = (int)($_GET['mata_kuliah_id'] ?? 0);
$search = trim((string)($_GET['q'] ?? ''));

$sql = 'SELECT materi.*, mata_kuliah.kode, mata_kuliah.nama AS nama_mk
        FROM materi JOIN mata_kuliah ON materi.mata_kuliah_id = mata_kuliah.id';
$params = [];
$conditions = [];
if ($selectedCourse > 0) {
    $conditions[] = 'materi.mata_kuliah_id = ?';
    $params[] = $selectedCourse;
}
if ($search !== '') {
    $conditions[] = '(materi.judul LIKE ? OR materi.deskripsi LIKE ? OR mata_kuliah.nama LIKE ?)';
    $like = '%' . $search . '%';
    array_push($params, $like, $like, $like);
}
if ($conditions) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}
$sql .= ' ORDER BY materi.id DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$materials = $stmt->fetchAll();

require 'header.php';
?>
<section class="page-heading">
    <div><p class="eyebrow">SUMBER BELAJAR</p><h1>Materi Pembelajaran</h1><p class="muted">Tambahkan dan kelola materi untuk setiap mata kuliah.</p></div>
    <span class="count-pill"><?= count($materials) ?> materi</span>
</section>

<section class="panel form-panel">
    <div class="panel-heading"><div><h2>Tambah Materi</h2><p class="muted">Masukkan informasi materi pembelajaran.</p></div></div>
    <?php if (!$courses): ?>
        <div class="alert error">Tambahkan mata kuliah terlebih dahulu sebelum membuat materi.</div>
    <?php else: ?>
    <form method="post" class="form-grid">
        <input type="hidden" name="action" value="add">
        <div class="form-field"><label for="mata_kuliah_id">Mata Kuliah *</label><select id="mata_kuliah_id" name="mata_kuliah_id" required><option value="">Pilih mata kuliah</option><?php foreach ($courses as $course): ?><option value="<?= (int)$course['id'] ?>"><?= e($course['kode'] . ' — ' . $course['nama']) ?></option><?php endforeach; ?></select></div>
        <div class="form-field"><label for="judul">Judul Materi *</label><input id="judul" name="judul" required maxlength="180" placeholder="Contoh: Pengenalan PHP"></div>
        <div class="form-field"><label for="link">Link Materi</label><input id="link" name="link" type="url" placeholder="https://..."></div>
        <div class="form-field"><label for="deskripsi">Deskripsi Singkat</label><input id="deskripsi" name="deskripsi" placeholder="Ringkasan materi"></div>
        <div class="form-field form-field-full"><label for="isi">Isi Materi</label><textarea id="isi" name="isi" rows="4" placeholder="Tulis catatan atau penjelasan materi..."></textarea></div>
        <div class="form-actions"><button type="submit" class="button">+ Tambah Materi</button></div>
    </form>
    <?php endif; ?>
</section>

<section class="panel">
    <div class="panel-heading"><div><h2>Daftar Materi</h2><p class="muted">Cari atau filter materi berdasarkan mata kuliah.</p></div></div>
    <form method="get" class="filter-form">
        <input type="search" name="q" value="<?= e($search) ?>" placeholder="Cari judul atau deskripsi...">
        <select name="mata_kuliah_id"><option value="0">Semua Mata Kuliah</option><?php foreach ($courses as $course): ?><option value="<?= (int)$course['id'] ?>" <?= $selectedCourse === (int)$course['id'] ? 'selected' : '' ?>><?= e($course['nama']) ?></option><?php endforeach; ?></select>
        <button class="button" type="submit">Cari</button>
        <a class="button button-muted" href="materi.php">Reset</a>
    </form>
    <?php if (!$materials): ?>
        <div class="empty-state"><span>▧</span><p>Materi tidak ditemukan.</p></div>
    <?php else: ?>
    <div class="material-grid">
        <?php foreach ($materials as $item): ?>
            <article class="material-card">
                <div class="material-card-top"><span class="list-icon purple">▧</span><span class="code-pill"><?= e($item['kode']) ?></span></div>
                <h3><?= e($item['judul']) ?></h3>
                <p class="muted"><?= e($item['nama_mk']) ?></p>
                <?php if ($item['deskripsi']): ?><p><?= e($item['deskripsi']) ?></p><?php endif; ?>
                <?php if ($item['isi']): ?><details><summary>Baca catatan materi</summary><div class="material-body"><?= nl2br(e($item['isi'])) ?></div></details><?php endif; ?>
                <div class="material-actions">
                    <?php if ($item['link']): ?><a class="button button-small" href="<?= e($item['link']) ?>" target="_blank" rel="noopener noreferrer">Buka Materi ↗</a><?php endif; ?>
                    <form method="post" class="inline-form" onsubmit="return confirm('Hapus materi ini?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$item['id'] ?>"><button type="submit" class="button button-danger button-small">Hapus</button></form>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>
<?php require 'footer.php'; ?>
