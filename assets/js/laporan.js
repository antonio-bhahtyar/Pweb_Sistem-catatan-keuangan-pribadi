// assets/js/laporan.js - Report page enhancements

document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit period selector on change
    var laporanForm = document.getElementById('laporanForm');
    if (laporanForm) {
        laporanForm.querySelectorAll('select, input[type="number"]').forEach(function(el) {
            el.addEventListener('change', function() {
                laporanForm.submit();
            });
        });
    }
});
