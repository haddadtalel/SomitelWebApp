/**
 * Shopping Cart JavaScript
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize cart functionality
    initCart();
    
    // Check cart count on page load
    fetchCartCount();
});

/**
 * Initialize cart functionality
 */
function initCart() {
    // Add event listeners for "Add to Cart" buttons on product list pages
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            
            addToCart(productId, 1);
        });
    });
}

/**
 * Add a product to the cart
 */
function addToCart(productId, quantity = 1) {
    fetch(`/cart/add/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success toast
            showToast('Product added to cart!', 'success');
            
            // Update cart count
            updateCartCount(data.cartCount);
        }
    })
    .catch(error => {
        console.error('Error adding to cart:', error);
        showToast('Failed to add product to cart', 'error');
    });
}

/**
 * Fetch current cart count
 */
function fetchCartCount() {
    fetch('/cart/count', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        updateCartCount(data.count);
    })
    .catch(error => {
        console.error('Error fetching cart count:', error);
    });
}

/**
 * Update cart count in the UI
 */
function updateCartCount(count) {
    const cartCountElements = document.querySelectorAll('.cart-count');
    const cartIconContainers = document.querySelectorAll('.cart-icon-container');
    
    // Update all cart count elements
    cartCountElements.forEach(element => {
        element.textContent = count;
    });
    
    // Show/hide cart icon based on count
    cartIconContainers.forEach(container => {
        if (count > 0) {
            container.classList.remove('d-none');
        } else {
            container.classList.add('d-none');
        }
    });
}

/**
 * Show a toast notification
 */
function showToast(message, type = 'info') {
    // Check if toast container exists, if not create it
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    // Create toast element
    const toastEl = document.createElement('div');
    toastEl.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'primary'} border-0`;
    toastEl.setAttribute('role', 'alert');
    toastEl.setAttribute('aria-live', 'assertive');
    toastEl.setAttribute('aria-atomic', 'true');
    
    // Toast content
    toastEl.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    
    // Add toast to container
    toastContainer.appendChild(toastEl);
    
    // Initialize and show toast
    const toast = new bootstrap.Toast(toastEl, {
        autohide: true,
        delay: 3000
    });
    toast.show();
    
    // Remove toast after it's hidden
    toastEl.addEventListener('hidden.bs.toast', function() {
        toastEl.remove();
    });
}
