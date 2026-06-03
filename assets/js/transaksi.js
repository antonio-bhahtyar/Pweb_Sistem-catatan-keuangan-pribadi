// assets/js/transaksi.js - Transaction page enhancements

document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit filter on select change
    var filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.querySelectorAll('select').forEach(function(sel) {
            sel.addEventListener('change', function() {
                filterForm.submit();
            });
        });
    }

    // Confirm delete
    var deleteButtons = document.querySelectorAll('.btn-delete-transaksi');
    deleteButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            if (!confirm('Apakah Anda yakin ingin menghapus transaksi ini?')) {
                e.preventDefault();
            }
        });
    });
});
