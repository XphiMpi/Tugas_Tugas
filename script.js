const registerForm = document.getElementById("registerForm");

registerForm.addEventListener("submit", function(event) {
    event.preventDefault();

    // Mengambil nilai input
    const nama = document.getElementById("nama").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirmPassword").value;

    // Validasi input kosong
    if (!nama || !email || !password || !confirmPassword) {
        alert("Semua kolom wajib diisi!");
        return;
    }

    // Validasi panjang password
    if (password.length < 8) {
        alert("Kata sandi minimal harus 8 karakter!");
        return;
    }

    // Validasi kesamaan password
    if (password !== confirmPassword) {
        alert("Konfirmasi kata sandi tidak cocok!");
        return;
    }

    // Registrasi berhasil (simulasi)
    alert(
        "Registrasi berhasil!\n\n" +
        "Selamat datang, " + nama + "!"
    );

    // Mengosongkan form
    registerForm.reset();
});
