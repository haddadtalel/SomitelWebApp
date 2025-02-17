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
