// Konfirmasi sebelum delete user
function confirmDelete() {
    return confirm("Apakah Anda yakin ingin menghapus data ini?");
}

// Tambahan kecil: highlight row saat mouse hover
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("table tr").forEach(row => {
        row.addEventListener("mouseover", () => row.style.background = "#f1f1f1");
        row.addEventListener("mouseout", () => row.style.background = "white");
    });
});
