<?php

require 'koneksi.php';

function h($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}

function rupiah($value)
{
    return 'Rp ' . number_format(
        (float) $value,
        0,
        ',',
        '.'
    );
}

$id = (int) ($_GET['id'] ?? 0);

$stmt = mysqli_prepare(
    $conn,
    'SELECT * FROM menu_kopi WHERE id = ?'
);

mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$menu_kopi = mysqli_fetch_assoc($hasil);

if (!$menu_kopi) {
    http_response_code(404);

    exit(
        'Menu tidak ditemukan. ' .
        '<a href="index.php">Kembali ke katalog</a>.'
    );
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

    <title>Detail Menu Kopi — KopiKita Cafe</title>

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
            Menu kopi details
        </p>

        <h1 class="page-title">
            Detail Menu
        </h1>

        <section class="panel">

            <div class="detail-grid">

                <div
                    class="detail-art"
                    aria-hidden="true"
                ></div>

                <div>

                    <span class="pill">
                        <?= h($menu_kopi['kategori_menu']) ?>
                    </span>

                    <h2
                        class="page-title"
                        style="font-size: 30px; margin-top: 12px;"
                    >
                        <?= h($menu_kopi['nama_menu']) ?>
                    </h2>

                    <p class="meta">
                        <?= h($menu_kopi['merek_menu']) ?>
                    </p>

                    <p class="price">
                        <?= rupiah($menu_kopi['harga_menu']) ?>
                    </p>

                    <dl class="detail-list">

                        <dt>Kode menu</dt>

                        <dd>
                            <?= h($menu_kopi['kode_menu']) ?>
                        </dd>

                        <dt>Kategori menu</dt>

                        <dd>
                            <?= h($menu_kopi['kategori_menu']) ?>
                        </dd>

                        <dt>Jenis minuman</dt>

                        <dd>
                            <?= h($menu_kopi['varian_menu']) ?>
                        </dd>

                        <dt>Stok</dt>

                        <dd>
                            <?= (int) $menu_kopi['stok_menu'] ?>
                            porsi
                        </dd>

                        <dt>Deskripsi</dt>

                        <dd>
                            <?= nl2br(h($menu_kopi['deskripsi_menu'])) ?>
                        </dd>

                    </dl>

                    <div class="actions">

                        <a
                            class="btn"
                            href="edit.php?id=<?= (int) $menu_kopi['id'] ?>"
                        >
                            Edit Menu
                        </a>

                        <a
                            class="btn secondary"
                            href="index.php"
                        >
                            Kembali ke Katalog
                        </a>

                        <a
                            class="btn secondary"
                            href="hapus.php?id=<?= (int) $menu_kopi['id'] ?>"
                        >
                            Hapus
                        </a>

                    </div>

                </div>

            </div>

        </section>

    </main>

    <footer class="footer">
        KopiKita Cafe  Praktikum PAW Modul 2
    </footer>

</body>
</html>