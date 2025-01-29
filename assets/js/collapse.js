document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".expand-btn").forEach(function (button) {
        button.addEventListener("click", function () {
            const target = document.querySelector(this.getAttribute("data-bs-target"));

            if (target.style.display === "inline") {
                target.style.display = "none";
                this.innerHTML = '<i class="bi bi-arrow-down-circle"></i>';
            } else {
                target.style.display = "inline";
                this.innerHTML = '<i class="bi bi-arrow-up-circle"></i>';
            }
        });
    });
});
