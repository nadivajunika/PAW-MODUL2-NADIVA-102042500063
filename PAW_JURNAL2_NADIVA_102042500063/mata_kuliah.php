
<?php
require_once 'config.php';

$pageTitle = 'Mata Kuliah';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = trim($_POST['action'] ?? '');

    // TAMBAH MATA KULIAH
    if ($action === 'add') {
        $kode = strtoupper(trim($_POST['kode'] ?? ''));
        $nama = trim($_POST['nama'] ?? '');
        $dosen = trim($_POST['dosen'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');

        if ($kode === '' || $nama === '') {
            flash('error', 'Kode dan nama mata kuliah wajib diisi.');
        } else {
            try {
                $stmt = $pdo->prepare(
                    'INSERT INTO mata_kuliah
                    (kode, nama, dosen, deskripsi)
                    VALUES (?, ?, ?, ?)'
                );

                $stmt->execute([
                    $kode,
                    $nama,
                    $dosen !== '' ? $dosen : null,
                    $deskripsi !== '' ? $deskripsi : null
                ]);

                flash('success', 'Mata kuliah berhasil ditambahkan.');
            } catch (PDOException $e) {
                if ($e->getCode() === '23000') {
                    flash('error', 'Kode mata kuliah sudah digunakan.');
                } else {
                    flash('error', 'Mata kuliah gagal ditambahkan.');
                }
            }
        }

        redirect('mata_kuliah.php');
        exit;
    }

    // HAPUS MATA KULIAH
    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            flash('error', 'ID mata kuliah tidak valid.');
            redirect('mata_kuliah.php');
            exit;
        }

        try {
            $stmt = $pdo->prepare(
                'DELETE FROM mata_kuliah WHERE id = ?'
            );

            $stmt->execute([$id]);

            if ($stmt->rowCount() > 0) {
                flash(
                    'success',
                    'Mata kuliah berhasil dihapus.'
                );
            } else {
                flash('error', 'Mata kuliah tidak ditemukan.');
            }
        } catch (PDOException $e) {
            flash(
                'error',
                'Mata kuliah gagal dihapus. Periksa data materi dan tugas terkait.'
            );
        }

        redirect('mata_kuliah.php');
        exit;
    }
}

// AMBIL DATA MATA KULIAH
$courses = $pdo->query(
    'SELECT
        mk.*,
        (
            SELECT COUNT(*)
            FROM materi m
            WHERE m.mata_kuliah_id = mk.id
        ) AS jumlah_materi,
        (
            SELECT COUNT(*)
            FROM tugas t
            WHERE t.mata_kuliah_id = mk.id
        ) AS jumlah_tugas
    FROM mata_kuliah mk
    ORDER BY mk.id DESC'
)->fetchAll(PDO::FETCH_ASSOC);

require 'header.php';
?>

<section class="page-heading">
    <div>
        <p class="eyebrow">PENGELOLAAN AKADEMIK</p>
        <h1>Mata Kuliah</h1>
        <p class="muted">
            Kelola daftar mata kuliah yang tersedia di LMS.
        </p>
    </div>

    <span class="count-pill">
        <?= count($courses) ?> mata kuliah
    </span>
</section>

<!-- FORM TAMBAH MATA KULIAH -->
<section class="panel form-panel">
    <div class="panel-heading">
        <div>
            <h2>Tambah Mata Kuliah</h2>
            <p class="muted">
                Lengkapi informasi mata kuliah baru.
            </p>
        </div>
    </div>

    <form method="post" class="form-grid">
        <input type="hidden" name="action" value="add">

        <div class="form-field">
            <label for="kode">Kode Mata Kuliah *</label>
            <input
                id="kode"
                name="kode"
                type="text"
                placeholder="Contoh: PAW101"
                required
                maxlength="30"
            >
        </div>

        <div class="form-field">
            <label for="nama">Nama Mata Kuliah *</label>
            <input
                id="nama"
                name="nama"
                type="text"
                placeholder="Contoh: Pemrograman Aplikasi Web"
                required
                maxlength="150"
            >
        </div>

        <div class="form-field">
            <label for="dosen">Dosen Pengampu</label>
            <input
                id="dosen"
                name="dosen"
                type="text"
                placeholder="Nama dosen"
                maxlength="150"
            >
        </div>

        <div class="form-field">
            <label for="deskripsi">Deskripsi</label>
            <input
                id="deskripsi"
                name="deskripsi"
                type="text"
                placeholder="Deskripsi singkat"
            >
        </div>

        <div class="form-actions">
            <button type="submit" class="button">
                + Tambah Mata Kuliah
            </button>
        </div>
    </form>
</section>

<!-- DAFTAR MATA KULIAH -->
<section class="panel">
    <div class="panel-heading">
        <div>
            <h2>Daftar Mata Kuliah</h2>
            <p class="muted">
                Informasi mata kuliah yang tersimpan.
            </p>
        </div>
    </div>

    <?php if (empty($courses)): ?>

        <div class="empty-state">
            <span>▤</span>
            <p>Belum ada mata kuliah.</p>
        </div>

    <?php else: ?>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Mata Kuliah</th>
                        <th>Dosen</th>
                        <th>Materi</th>
                        <th>Tugas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($courses as $course): ?>
                        <tr>
                            <td>
                                <span class="code-pill">
                                    <?= e($course['kode']) ?>
                                </span>
                            </td>

                            <td>
                                <strong>
                                    <?= e($course['nama']) ?>
                                </strong>

                                <?php if (!empty($course['deskripsi'])): ?>
                                    <small class="table-subtext">
                                        <?= e($course['deskripsi']) ?>
                                    </small>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?= e($course['dosen'] ?: '—') ?>
                            </td>

                            <td>
                                <?= (int) $course['jumlah_materi'] ?>
                            </td>

                            <td>
                                <?= (int) $course['jumlah_tugas'] ?>
                            </td>

                            <td>
                                <form
                                    method="post"
                                    class="inline-form"
                                    onsubmit="return confirm('Hapus mata kuliah ini beserta materi dan tugas terkait?')"
                                >
                                    <input
                                        type="hidden"
                                        name="action"
                                        value="delete"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int) $course['id'] ?>"
                                    >

                                    <button
                                        class="button button-danger button-small"
                                        type="submit"
                                    >
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>
</section>

<?php require 'footer.php'; ?>
