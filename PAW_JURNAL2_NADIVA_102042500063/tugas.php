<?php
require_once 'config.php';
$pageTitle = 'Tugas';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = post('action');
    if ($action === 'add') {
        $mataKuliahId = (int)post('mata_kuliah_id');
        $judul = post('judul');
        $deskripsi = post('deskripsi');
        $deadline = post('deadline');
        $link = post('link');

        if ($mataKuliahId < 1 || $judul === '') {
            flash('error', 'Mata kuliah dan judul tugas wajib diisi.');
        } else {
            $stmt = $pdo->prepare('INSERT INTO tugas (mata_kuliah_id, judul, deskripsi, deadline, link) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$mataKuliahId, $judul, $deskripsi ?: null, $deadline ?: null, $link ?: null]);
            flash('success', 'Tugas berhasil ditambahkan.');
        }
        redirect('tugas.php');
    }

    if ($action === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM tugas WHERE id = ?');
        $stmt->execute([(int)post('id')]);
        flash('success', 'Tugas berhasil dihapus.');
        redirect('tugas.php');
    }
}

$courses = $pdo->query('SELECT id, kode, nama FROM mata_kuliah ORDER BY nama')->fetchAll();
$search = trim((string)($_GET['q'] ?? ''));
$statusFilter = (string)($_GET['status'] ?? 'all');

$sql = 'SELECT tugas.*, mata_kuliah.kode, mata_kuliah.nama AS nama_mk
        FROM tugas JOIN mata_kuliah ON tugas.mata_kuliah_id = mata_kuliah.id';
$params = [];
$conditions = [];
if ($search !== '') {
    $conditions[] = '(tugas.judul LIKE ? OR tugas.deskripsi LIKE ? OR mata_kuliah.nama LIKE ?)';
    $like = '%' . $search . '%';
    array_push($params, $like, $like, $like);
}
if ($statusFilter === 'upcoming') {
    $conditions[] = '(tugas.deadline IS NULL OR tugas.deadline >= CURDATE())';
} elseif ($statusFilter === 'passed') {
    $conditions[] = 'tugas.deadline < CURDATE()';
}
if ($conditions) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}
$sql .= ' ORDER BY (tugas.deadline IS NULL), tugas.deadline ASC, tugas.id DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tasks = $stmt->fetchAll();

require 'header.php';
?>
<section class="page-heading">
    <div><p class="eyebrow">PENGELOLAAN PENUGASAN</p><h1>Tugas</h1><p class="muted">Catat instruksi tugas dan pantau tenggat pengumpulan.</p></div>
    <span class="count-pill"><?= count($tasks) ?> tugas</span>
</section>

<section class="panel form-panel">
    <div class="panel-heading"><div><h2>Tambah Tugas</h2><p class="muted">Masukkan detail tugas dan deadline jika tersedia.</p></div></div>
    <?php if (!$courses): ?>
        <div class="alert error">Tambahkan mata kuliah terlebih dahulu sebelum membuat tugas.</div>
    <?php else: ?>
    <form method="post" class="form-grid">
        <input type="hidden" name="action" value="add">
        <div class="form-field"><label for="mata_kuliah_id">Mata Kuliah *</label><select id="mata_kuliah_id" name="mata_kuliah_id" required><option value="">Pilih mata kuliah</option><?php foreach ($courses as $course): ?><option value="<?= (int)$course['id'] ?>"><?= e($course['kode'] . ' — ' . $course['nama']) ?></option><?php endforeach; ?></select></div>
        <div class="form-field"><label for="judul">Judul Tugas *</label><input id="judul" name="judul" required maxlength="180" placeholder="Contoh: Jurnal Praktikum 2"></div>
        <div class="form-field"><label for="deadline">Deadline</label><input id="deadline" name="deadline" type="date"></div>
        <div class="form-field"><label for="link">Link Pengumpulan / Referensi</label><input id="link" name="link" type="url" placeholder="https://..."></div>
        <div class="form-field form-field-full"><label for="deskripsi">Deskripsi Tugas</label><textarea id="deskripsi" name="deskripsi" rows="4" placeholder="Tulis instruksi tugas..."></textarea></div>
        <div class="form-actions"><button type="submit" class="button">+ Tambah Tugas</button></div>
    </form>
    <?php endif; ?>
</section>

<section class="panel">
    <div class="panel-heading"><div><h2>Daftar Tugas</h2><p class="muted">Cari tugas dan lihat deadline.</p></div></div>
    <form method="get" class="filter-form">
        <input type="search" name="q" value="<?= e($search) ?>" placeholder="Cari tugas atau mata kuliah...">
        <select name="status"><option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>Semua deadline</option><option value="upcoming" <?= $statusFilter === 'upcoming' ? 'selected' : '' ?>>Belum lewat</option><option value="passed" <?= $statusFilter === 'passed' ? 'selected' : '' ?>>Sudah lewat</option></select>
        <button class="button" type="submit">Cari</button>
        <a class="button button-muted" href="tugas.php">Reset</a>
    </form>
    <?php if (!$tasks): ?>
        <div class="empty-state"><span>✓</span><p>Tidak ada tugas yang cocok.</p></div>
    <?php else: ?>
    <div class="table-wrapper"><table>
        <thead><tr><th>Nama Tugas</th><th>Mata Kuliah</th><th>Deadline</th><th>Status</th><th>Link</th><th>Aksi</th></tr></thead>
        <tbody><?php foreach ($tasks as $task): 
            $isPassed = !empty($task['deadline']) && $task['deadline'] < date('Y-m-d');
        ?>
            <tr>
                <td><strong><?= e($task['judul']) ?></strong><?php if ($task['deskripsi']): ?><small class="table-subtext"><?= e($task['deskripsi']) ?></small><?php endif; ?></td>
                <td><?= e($task['nama_mk']) ?></td>
                <td><?= $task['deadline'] ? e(date('d M Y', strtotime($task['deadline']))) : '—' ?></td>
                <td><span class="status-badge <?= $isPassed ? 'status-passed' : 'status-open' ?>"><?= $isPassed ? 'Deadline lewat' : 'Aktif' ?></span></td>
                <td><?php if ($task['link']): ?><a href="<?= e($task['link']) ?>" target="_blank" rel="noopener noreferrer">Buka link ↗</a><?php else: ?>—<?php endif; ?></td>
                <td><form method="post" class="inline-form" onsubmit="return confirm('Hapus tugas ini?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$task['id'] ?>"><button type="submit" class="button button-danger button-small">Hapus</button></form></td>
            </tr>
        <?php endforeach; ?></tbody>
    </table></div>
    <?php endif; ?>
</section>
<?php require 'footer.php'; ?>
