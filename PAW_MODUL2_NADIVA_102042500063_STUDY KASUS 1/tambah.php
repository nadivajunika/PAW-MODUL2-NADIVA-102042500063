
<?php
require 'koneksi.php';

$kode = $nama = $brand = $kategori = '';
$jenis = $harga = $stok = $deskripsi = '';
$pesan = '';

function h($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}

if (isset($_POST['simpan'])) {
    $kode = trim($_POST['kode_menu'] ?? '');
    $nama = trim($_POST['nama_menu'] ?? '');
    $brand = trim($_POST['merek_menu'] ?? '');
    $kategori = trim($_POST['kategori_menu'] ?? '');
    $jenis = trim($_POST['varian_menu'] ?? '');
    $harga = trim($_POST['harga_menu'] ?? '');
    $stok = trim($_POST['stok_menu'] ?? '');
    $deskripsi = trim($_POST['deskripsi_menu'] ?? '');

    if (
        $kode === '' ||
        $nama === '' ||
        $brand === '' ||
        $kategori === '' ||
        $jenis === '' ||
        $deskripsi === ''
    ) {
        $pesan = 'Semua kolom wajib diisi.';
    } elseif (
        !is_numeric($harga) ||
        $harga <= 0 ||
        $harga > 9999999999
    ) {
        $pesan = 'Harga harus berupa angka positif tanpa Rp atau titik.';
    } elseif (
        filter_var($stok, FILTER_VALIDATE_INT) === false ||
        $stok < 0
    ) {
        $pesan = 'Stok harus berupa bilangan bulat nol atau lebih.';
    } else {
        $stmt = mysqli_prepare(
            $conn,
            'SELECT id FROM menu_kopi WHERE kode_menu = ?'
        );

        mysqli_stmt_bind_param($stmt, 's', $kode);
        mysqli_stmt_execute($stmt);

        $cek = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($cek) > 0) {
            $pesan = 'Kode menu sudah digunakan.';
        } else {
            $stmt = mysqli_prepare(
                $conn,
                'INSERT INTO menu_kopi
                (
                    kode_menu,
                    nama_menu,
                    merek_menu,
                    kategori_menu,
                    varian_menu,
                    harga_menu,
                    stok_menu,
                    deskripsi_menu
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );

            mysqli_stmt_bind_param(
                $stmt,
                'sssssdis',
                $kode,
                $nama,
                $brand,
                $kategori,
                $jenis,
                $harga,
                $stok,
                $deskripsi
            );

            if (mysqli_stmt_execute($stmt)) {
                header('Location: index.php?pesan=1');
                exit;
            }

            $pesan = 'Menu gagal disimpan. Periksa kembali data.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Tambah Menu — KopiKita Cafe</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

    <header class="topbar">

        <a class="brandmark" href="index.php">
            kopikita<span> cafe.</span>
        </a>

        <nav class="navlinks">
            <a href="index.php">Katalog</a>
            <a href="tambah.php">Tambah Menu</a>
        </nav>

    </header>

    <main class="wrap">

        <p class="eyebrow">
            Menu kopi management
        </p>

        <h1 class="page-title">
            Tambah Menu
        </h1>

        <p class="meta">
            Isi detail menu atau minuman yang ingin dimasukkan ke katalog.
        </p>

        <section class="panel">

            <?php if ($pesan !== ''): ?>

                <div class="error">
                    <?= h($pesan) ?>
                </div>

            <?php endif; ?>

            <form method="post">

                <div class="form-grid">

                    <div>
                        <label for="kode">
                            Kode menu
                        </label>

                        <input
                            id="kode"
                            name="kode_menu"
                            maxlength="20"
                            required
                            value="<?= h($kode) ?>"
                            placeholder="KOPI-004"
                        >
                    </div>

                    <div>
                        <label for="nama">
                            Nama menu
                        </label>

                        <input
                            id="nama"
                            name="nama_menu"
                            maxlength="100"
                            required
                            value="<?= h($nama) ?>"
                        >
                    </div>

                    <div>
                        <label for="brand">
                            Merek
                        </label>

                        <input
                            id="brand"
                            name="merek_menu"
                            maxlength="50"
                            required
                            value="<?= h($brand) ?>"
                        >
                    </div>

                    <div>
                        <label for="kategori">
                            Kategori menu
                        </label>

                        <select
                            id="kategori"
                            name="kategori_menu"
                            required
                        >
                            <option value="">
                                Pilih kategori menu
                            </option>

                            <?php
                            foreach (
                                [
                                    'Coffee based',
                                    'Non-coffee',
                                    'Tea based',
                                    'Signature',
                                    'Pastry',
                                    'Snack'
                                ] as $opt
                            ):
                            ?>

                                <option
                                    value="<?= h($opt) ?>"
                                    <?= $kategori === $opt ? 'selected' : '' ?>
                                >
                                    <?= h($opt) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div>
                        <label for="jenis">
                            Jenis minuman
                        </label>

                        <select
                            id="jenis"
                            name="varian_menu"
                            required
                        >
                            <option value="">
                                Pilih jenis menu
                            </option>

                            <?php
                            foreach (
                                [
                                    'Hot',
                                    'Iced',
                                    'Blended',
                                    'Less sugar',
                                    'Regular',
                                    'Large'
                                ] as $opt
                            ):
                            ?>

                                <option
                                    value="<?= h($opt) ?>"
                                    <?= $jenis === $opt ? 'selected' : '' ?>
                                >
                                    <?= h($opt) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div>
                        <label for="harga">
                            Harga (angka saja)
                        </label>

                        <input
                            id="harga"
                            name="harga_menu"
                            type="number"
                            min="1"
                            step="1"
                            required
                            value="<?= h($harga) ?>"
                            placeholder="89000"
                        >
                    </div>

                    <div>
                        <label for="stok">
                            Stok
                        </label>

                        <input
                            id="stok"
                            name="stok_menu"
                            type="number"
                            min="0"
                            step="1"
                            required
                            value="<?= h($stok) ?>"
                            placeholder="20"
                        >
                    </div>

                    <div class="full">
                        <label for="deskripsi">
                            Deskripsi menu
                        </label>

                        <textarea
                            id="deskripsi"
                            name="deskripsi_menu"
                            required
                        ><?= h($deskripsi) ?></textarea>
                    </div>

                </div>

                <div class="actions">

                    <button
                        class="btn"
                        name="simpan"
                        type="submit"
                    >
                        Simpan menu
                    </button>

                    <a
                        class="btn secondary"
                        href="index.php"
                    >
                        Batal
                    </a>

                </div>

            </form>

        </section>

    </main>

    <footer class="footer">
        KopiKita Cafe · Praktikum PAW Modul 2
    </footer>

</body>
</html>