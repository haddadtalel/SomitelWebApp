window.addEventListener("scroll", function() {
    let navbar = document.querySelector(".service-detail nav");
    if (window.scrollY > 50) {
        navbar.classList.add("navbar-scrolled");
    } else {
        navbar.classList.remove("navbar-scrolled");
    }
});

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