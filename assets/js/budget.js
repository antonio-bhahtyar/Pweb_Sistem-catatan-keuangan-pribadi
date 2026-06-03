// assets/js/budget.js - Budget page enhancements

document.addEventListener('DOMContentLoaded', function() {
    // Color progress bars based on percentage
    document.querySelectorAll('.budget-progress .progress-bar').forEach(function(bar) {
        var width = parseFloat(bar.style.width);
        if (width >= 100) {
            bar.classList.remove('bg-success', 'bg-warning');
            bar.classList.add('bg-danger');
        } else if (width >= 80) {
            bar.classList.remove('bg-success', 'bg-danger');
            bar.classList.add('bg-warning');
        } else {
            bar.classList.remove('bg-warning', 'bg-danger');
            bar.classList.add('bg-success');
        }
    });
});
