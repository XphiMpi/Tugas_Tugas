document.getElementById('biodataForm').addEventListener('submit', function(event) {
    let errors = [];

    // Ambil nilai dari setiap input
    const nama = document.getElementById('nama').value.trim();
    const pin = document.getElementById('pin').value;
    const email = document.getElementById('email').value.trim();
    const usia = document.getElementById('usia').value;
    const tglLahir = document.getElementById('tgl_lahir').value;
    const jk = document.querySelector('input[name="jk"]:checked');
    const hobi = document.querySelectorAll('input[name="hobi[]"]:checked');
    const pendidikan = document.getElementById('pendidikan').value;
    const foto = document.getElementById('foto').value;
    const alamat = document.getElementById('alamat').value.trim();

    // 1. Validasi Nama (wajib diisi & hanya huruf/spasi/petik/titik)
    const regexNama = /^[a-zA-Z\s'.]+$/;
    if (nama === "") {
        errors.push("- Nama Lengkap wajib diisi.");
    } else if (!regexNama.test(nama)) {
        errors.push("- Nama Lengkap tidak boleh mengandung angka atau karakter khusus.");
    }

    // 2. Validasi PIN (minimal 6 karakter)
    if (pin.length < 6) {
        errors.push("- PIN / Kode Akses minimal harus 6 karakter.");
    }

    // 3. Validasi Format Email
    const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!regexEmail.test(email)) {
        errors.push("- Format Alamat Email tidak valid.");
    }

    // 4. Validasi Usia
    if (usia === "" || usia < 15 || usia > 100) {
        errors.push("- Usia harus diisi antara 15 hingga 100 tahun.");
    }

    // 5. Validasi Tanggal Lahir
    if (tglLahir === "") {
        errors.push("- Tanggal Lahir wajib diisi.");
    }

    // 6. Validasi Radio Button (Jenis Kelamin)
    if (!jk) {
        errors.push("- Silakan pilih Jenis Kelamin.");
    }

    // 7. Validasi Checkbox (Hobi minimal 1)
    if (hobi.length === 0) {
        errors.push("- Pilih minimal satu Hobi.");
    }

    // 8. Validasi Select (Pendidikan)
    if (pendidikan === "") {
        errors.push("- Silakan pilih Pendidikan Terakhir.");
    }

    // 9. Validasi File Upload
    if (foto === "") {
        errors.push("- Silakan unggah Foto Profil.");
    }

    // 10. Validasi Textarea (Alamat)
    if (alamat === "") {
        errors.push("- Alamat Lengkap wajib diisi.");
    }

    // Jika ada error, batalkan submit dan tampilkan alert
    if (errors.length > 0) {
        event.preventDefault();
        alert("Mohon perbaiki kesalahan berikut:\n\n" + errors.join("\n"));
    }
});