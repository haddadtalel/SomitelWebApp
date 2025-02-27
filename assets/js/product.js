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
    // Product Filtering Logic
    const filterProducts = () => {
        let selectedCategories = [...document.querySelectorAll('.category-filter:checked')].map(el => el.value);
        let selectedBrands = [...document.querySelectorAll('.brand-filter:checked')].map(el => el.value);
        let maxPrice = document.getElementById('priceRange').value;

        document.querySelectorAll('.product-item').forEach(item => {
            let category = item.getAttribute('data-category');
            let brand = item.getAttribute('data-brand');
            let price = parseFloat(item.getAttribute('data-price'));

            let categoryMatch = selectedCategories.length === 0 || selectedCategories.includes(category);
            let brandMatch = selectedBrands.length === 0 || selectedBrands.includes(brand);
            let priceMatch = price <= maxPrice;

            item.style.display = (categoryMatch && brandMatch && priceMatch) ? 'block' : 'none';
        });
    };

    document.getElementById('applyFilters').addEventListener('click', filterProducts);
    document.getElementById('priceRange').addEventListener('input', filterProducts);
});
