<?php

require 'koneksi.php';

$cari = trim($_GET['cari'] ?? '');

$kata = mysqli_real_escape_string($conn, $cari);

$sql = "
    SELECT *
    FROM menu_kopi
    WHERE nama_menu LIKE '%$kata%'
        OR merek_menu LIKE '%$kata%'
        OR kategori_menu LIKE '%$kata%'
    ORDER BY id DESC
";

$hasil = mysqli_query($conn, $sql);

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

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>KopiKita Cafe — Katalog Menu Kopi</title>

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

        <section class="hero">

            <p class="eyebrow">
                Coffee, comfort, and good vibes
            </p>

            <h1>
                Temukan langkahmu,
                <br>
                mulai dari secangkir kopi yang tepat.
            </h1>

            <p>
                Kelola katalog menu dan minuman cafe
                dalam satu tempat. Cari menu, cek stok,
                dan perbarui informasi dengan mudah.
            </p>

        </section>

        <?php if (isset($_GET['pesan'])): ?>

            <div class="notice">
                Perubahan data menu berhasil disimpan.
            </div>

        <?php endif; ?>

        <div class="toolbar">

            <div>

                <p class="eyebrow">
                    Coffee menu collection
                </p>

                <h2>
                    Menu Coffee & Cafe
                </h2>

            </div>

            <form class="search" method="get">

                <input
                    type="search"
                    name="cari"
                    placeholder="Cari nama menu, merek, kategori..."
                    value="<?= h($cari) ?>"
                >

                <button
                    class="btn"
                    type="submit"
                >
                    Cari
                </button>

                <?php if ($cari !== ''): ?>

                    <a
                        class="btn secondary"
                        href="index.php"
                    >
                        Reset
                    </a>

                <?php endif; ?>

            </form>

        </div>

        <p class="meta">
            Jumlah hasil:
            <?= mysqli_num_rows($hasil) ?>
            produk
        </p>

        <?php if (mysqli_num_rows($hasil) === 0): ?>

            <div class="empty">

                <h3>
                    Menu kopi belum ditemukan
                </h3>

                <p>
                    Coba kata kunci lain atau tambahkan menu baru.
                </p>

                <a
                    class="btn"
                    href="tambah.php"
                >
                    Tambah Menu
                </a>

            </div>

        <?php else: ?>

            <div class="cards">

                <?php while ($menu_kopi = mysqli_fetch_assoc($hasil)): ?>

                    <article class="product-card">

                        <div
                            class="product-art"
                            aria-hidden="true"
                        ></div>

                        <span class="pill">
                            <?= h($menu_kopi['kategori_menu']) ?>
                        </span>

                        <h3>
                            <?= h($menu_kopi['nama_menu']) ?>
                        </h3>

                        <p class="meta">
                            <?= h($menu_kopi['merek_menu']) ?>
                            ·
                            <?= h($menu_kopi['varian_menu']) ?>
                        </p>

                        <p class="price">
                            <?= rupiah($menu_kopi['harga_menu']) ?>
                        </p>

                        <p class="stock">
                            Stok tersedia:
                            <?= (int) $menu_kopi['stok_menu'] ?>
                        </p>

                        <div class="card-actions">

                            <a
                                href="detail.php?id=<?= (int) $menu_kopi['id'] ?>"
                            >
                                Lihat detail
                            </a>

                            <a
                                href="edit.php?id=<?= (int) $menu_kopi['id'] ?>"
                            >
                                Edit
                            </a>

                            <a
                                href="hapus.php?id=<?= (int) $menu_kopi['id'] ?>"
                            >
                                Hapus
                            </a>

                        </div>

                    </article>

                <?php endwhile; ?>

            </div>

        <?php endif; ?>

    </main>

    <footer class="footer">
        KopiKita Cafe · Praktikum PAW Modul 2 · PHP & MySQL
    </footer>

</body>
</html>