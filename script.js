const registerForm = document.getElementById("registerForm");

registerForm.addEventListener("submit", function(event) {
    event.preventDefault();

    const nama = document.getElementById("nama").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirmPassword").value;

    if (!nama || !email || !password || !confirmPassword) {
        alert("Semua kolom wajib diisi!");
        return;
    }

    if (password.length < 8) {
        alert("Kata sandi minimal harus 8 karakter!");
        return;
    }

    if (password !== confirmPassword) {
        alert("Konfirmasi kata sandi tidak cocok!");
        return;
    }

    alert(
        "Registrasi berhasil!\n\n" +
        "Selamat datang, " + nama + "!"
    );

    registerForm.reset();
});
