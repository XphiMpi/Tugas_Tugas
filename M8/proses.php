<?php
$file_txt = "data_biodata.txt";
$pembatas = "==========================================";
$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';
$pesan_status = "";

if ($aksi == 'hapus' && isset($_GET['index'])) {
    $index_hapus = (int) $_GET['index'];

    if (file_exists($file_txt)) {
        $isi_file = file_get_contents($file_txt);
        $daftar_data = explode($pembatas, trim($isi_file));

        // Bersihkan elemen kosong & rapikan indeks array
        $daftar_data = array_values(array_filter($daftar_data, function($item) {
            return trim($item) !== "";
        }));

        if (isset($daftar_data[$index_hapus])) {
            // Hapus file foto jika ada di folder uploads
            if (preg_match('/File Foto\s*:\s*(.+)/', $daftar_data[$index_hapus], $matches)) {
                $nama_foto = trim($matches[1]);
                if ($nama_foto !== "Tidak ada file" && file_exists("uploads/" . $nama_foto)) {
                    unlink("uploads/" . $nama_foto);
                }
            }

            // Hapus entri data dari array
            unset($daftar_data[$index_hapus]);

            // Rakit kembali sisa data menjadi string dengan pembatas
            if (count($daftar_data) > 0) {
                $isi_baru = "";
                foreach ($daftar_data as $blok) {
                    $isi_baru .= "\n" . trim($blok) . "\n" . $pembatas . "\n";
                }
                file_put_contents($file_txt, $isi_baru, LOCK_EX);
            } else {
                file_put_contents($file_txt, "", LOCK_EX);
            }

            $pesan_status = "<div class='alert-success'>Data berhasil dihapus dari file.</div>";
        }
    }

    $aksi = 'lihat';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proses & Data Biodata</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <!-- Menu Navigasi -->
        <nav class="menu-nav">
            <a href="index.html" class="btn-menu">Input Biodata</a>
            <a href="proses.php?aksi=lihat" class="btn-menu <?php echo ($aksi == 'lihat') ? 'active' : ''; ?>">Lihat Data Tersimpan</a>
        </nav>

        <?php
        if (!empty($pesan_status)) {
            echo $pesan_status;
        }

        // ==========================================================
        // BAGIAN 1: MENAMPILKAN, MENCARI & MENGHAPUS DATA TXT
        // ==========================================================
        if ($aksi == 'lihat') {
            $kata_kunci = isset($_GET['cari']) ? trim($_GET['cari']) : '';
        ?>
            <h2>Daftar Biodata Tersimpan</h2>
            <p class="subtitle">Data dibaca langsung dari file <code><?php echo $file_txt; ?></code>.</p>

            <!-- Form Pencarian Nama -->
            <form action="proses.php" method="GET" class="search-form">
                <input type="hidden" name="aksi" value="lihat">
                <input type="text" name="cari" placeholder="Cari berdasarkan nama..." value="<?php echo htmlspecialchars($kata_kunci); ?>">
                <button type="submit" class="btn-search">Cari</button>
                <?php if ($kata_kunci !== ''): ?>
                    <a href="proses.php?aksi=lihat" class="btn-reset-search">Reset</a>
                <?php endif; ?>
            </form>

            <?php
            if (file_exists($file_txt) && filesize($file_txt) > 0) {
                $isi_file = file_get_contents($file_txt);
                $daftar_data = explode($pembatas, trim($isi_file));

                // Bersihkan elemen kosong dan rapikan indeks asli
                $daftar_data = array_values(array_filter($daftar_data, function($item) {
                    return trim($item) !== "";
                }));

                $ditemukan = 0;

                foreach ($daftar_data as $index_asli => $data) {
                    $tampilkan = true;

                    if ($kata_kunci !== '') {
                        if (preg_match('/Nama Lengkap\s*:\s*(.+)/i', $data, $matches)) {
                            $nama_entri = trim($matches[1]);
                            if (stripos($nama_entri, $kata_kunci) === false) {
                                $tampilkan = false;
                            }
                        } else {
                            $tampilkan = false;
                        }
                    }

                    if ($tampilkan) {
                        $ditemukan++;
                        $nomor_tampil = $index_asli + 1;
                        echo "<div class='data-box'>";
                        echo "  <div class='data-header'>";
                        echo "      <strong>DATA #$nomor_tampil</strong>";
                        echo "      <a href='proses.php?aksi=hapus&index=$index_asli' class='btn-hapus' onclick=\"return confirm('Yakin ingin menghapus Data #$nomor_tampil ini?');\">Hapus Data</a>";
                        echo "  </div>";
                        echo "  <div class='data-content'>" . htmlspecialchars(trim($data)) . "</div>";
                        echo "</div>";
                    }
                }

                if ($ditemukan === 0) {
                    echo "<div class='alert-error'>Tidak ada data yang cocok dengan pencarian <b>\"" . htmlspecialchars($kata_kunci) . "\"</b>.</div>";
                }
            } else {
                echo "<div class='alert-error'>Belum ada data biodata yang tersimpan.</div>";
            }

        // ==========================================================
        // BAGIAN 2: MEMPROSES SUBMIT FORM & MENYIMPAN KE TXT
        // ==========================================================
        } elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nama       = htmlspecialchars(trim($_POST['nama'] ?? ''));
            $pin        = $_POST['pin'] ?? '';
            $email      = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $usia       = $_POST['usia'] ?? '';
            $tgl_lahir  = $_POST['tgl_lahir'] ?? '';
            $jk         = $_POST['jk'] ?? '';
            $hobi_arr   = $_POST['hobi'] ?? [];
            $pendidikan = $_POST['pendidikan'] ?? '';
            $warna      = $_POST['warna'] ?? '';
            $alamat     = htmlspecialchars(trim($_POST['alamat'] ?? ''));

            // Validasi Server-Side
            $server_errors = [];
            if (empty($nama) || !preg_match("/^[a-zA-Z\s'.]+$/", $nama)) {
                $server_errors[] = "Nama tidak valid atau kosong.";
            }
            if (strlen($pin) < 6) {
                $server_errors[] = "PIN minimal 6 karakter.";
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $server_errors[] = "Format email tidak valid.";
            }
            if (empty($usia) || empty($tgl_lahir) || empty($jk) || empty($hobi_arr) || empty($pendidikan) || empty($alamat)) {
                $server_errors[] = "Semua field bertanda bintang (*) wajib diisi lengkap.";
            }

            // Proses Upload Foto
            $nama_file_foto = "Tidak ada file";
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                $folder_upload = "uploads/";
                if (!is_dir($folder_upload)) {
                    mkdir($folder_upload, 0777, true);
                }
                $ekstensi = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                $ekstensi_diizinkan = ['jpg', 'jpeg', 'png'];

                if (in_array($ekstensi, $ekstensi_diizinkan)) {
                    $nama_file_foto = time() . "_" . basename($_FILES['foto']['name']);
                    move_uploaded_file($_FILES['foto']['tmp_name'], $folder_upload . $nama_file_foto);
                } else {
                    $server_errors[] = "Format foto harus JPG, JPEG, atau PNG.";
                }
            } else {
                $server_errors[] = "Foto profil wajib diunggah.";
            }

            // Cek apakah ada error validasi
            if (!empty($server_errors)) {
                echo "<div class='alert-error'><strong>Gagal Menyimpan Data:</strong><ul>";
                foreach ($server_errors as $err) {
                    echo "<li>$err</li>";
                }
                echo "</ul></div>";
                echo "<a href='index.html' class='btn-menu'>&larr; Kembali ke Form</a>";
            } else {
                $hobi_str    = implode(", ", $hobi_arr);
                $pin_masked  = str_repeat("*", strlen($pin));
                $waktu_input = date("Y-m-d H:i:s");

                $format_simpan  = "\nWaktu Input   : $waktu_input\n";
                $format_simpan .= "Nama Lengkap  : $nama\n";
                $format_simpan .= "PIN Akses     : $pin_masked\n";
                $format_simpan .= "Email         : $email\n";
                $format_simpan .= "Usia          : $usia Tahun\n";
                $format_simpan .= "Tanggal Lahir : $tgl_lahir\n";
                $format_simpan .= "Jenis Kelamin : $jk\n";
                $format_simpan .= "Hobi          : $hobi_str\n";
                $format_simpan .= "Pendidikan    : $pendidikan\n";
                $format_simpan .= "Warna Favorit : $warna\n";
                $format_simpan .= "File Foto     : $nama_file_foto\n";
                $format_simpan .= "Alamat        : $alamat\n";
                $format_simpan .= $pembatas . "\n";

                if (file_put_contents($file_txt, $format_simpan, FILE_APPEND | LOCK_EX)) {
                    echo "<div class='alert-success'><strong>Berhasil!</strong> Data biodata atas nama <b>$nama</b> telah disimpan ke dalam <code>$file_txt</code>.</div>";
                    echo "<div class='button-group'>";
                    echo "<a href='index.html' class='btn-menu'>+ Input Data Baru</a>";
                    echo "<a href='proses.php?aksi=lihat' class='btn-menu active'>Lihat Semua Data</a>";
                    echo "</div>";
                } else {
                    echo "<div class='alert-error'>Terjadi kesalahan saat menulis ke file TXT. Periksa izin folder (permission).</div>";
                }
            }
        } else {
            header("Location: index.html");
            exit();
        }
        ?>
    </div>
</body>
</html>