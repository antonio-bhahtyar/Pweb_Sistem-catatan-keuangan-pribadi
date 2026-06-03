// assets/js/kategori.js - Category page enhancements

document.addEventListener('DOMContentLoaded', function() {
    // Handle delete modal - populate hidden fields
    var deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var nama = button.getAttribute('data-nama');
            var idInput = document.getElementById('deleteKategoriId');
            var namaEl = document.getElementById('deleteKategoriNama');
            if (idInput) idInput.value = id;
            if (namaEl) namaEl.textContent = nama;
        });
    }
});
