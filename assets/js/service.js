document.addEventListener("DOMContentLoaded", function () {
    let navbar = document.querySelector(".navbar");

    window.addEventListener("scroll", function () {
        if (window.scrollY > 50) {
            navbar.classList.add("navbar-scrolled");
        } else {
            navbar.classList.remove("navbar-scrolled");
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const serviceLinks = document.querySelectorAll('.service-link');

    serviceLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const serviceId = this.getAttribute('data-id');

            fetch(`/service/${serviceId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('service-title').textContent = data.title;
                    document.getElementById('service-description').textContent = data.description;
                })
                .catch(error => console.error('Error fetching service details:', error));
        });
    });
});