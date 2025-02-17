document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.collapse').forEach(collapse => {
        collapse.addEventListener('show.bs.collapse', function() {
            this.parentElement.style.overflow = 'visible';
        });

        collapse.addEventListener('hidden.bs.collapse', function() {
            this.parentElement.style.overflow = 'hidden';
        });
    });
});