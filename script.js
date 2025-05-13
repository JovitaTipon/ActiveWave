let cart = JSON.parse(localStorage.getItem('cart')) || [];

function updateCart() {
    const container = document.getElementById('cart-items');
    const checkoutBtn = document.getElementById('checkout');

    if (!container) return;

    container.innerHTML = '';

    if (cart.length === 0) {
        container.innerHTML = `<p class="empty-cart-msg">Your cart is empty. Start shopping now!</p>`;
        if (checkoutBtn) checkoutBtn.style.display = 'none';
        updateOrderSummary(); // Clear order summary when cart is empty
        return;
    }

    if (checkoutBtn) checkoutBtn.style.display = 'inline-block';

    cart.forEach(item => {
        const div = document.createElement('div');
        div.classList.add('cart-item', 'mb-3');
        div.innerHTML = `
            <p>${item.name} - $${item.price}</p>
            <input type="number" value="${item.quantity}" min="1" onchange="changeQuantity('${item.id}', this.value)">
            <button onclick="removeItem('${item.id}')">Remove</button>
        `;
        container.appendChild(div);
    });

    updateOrderSummary(); // Update summary each time cart changes
}

function updateOrderSummary() {
    const subtotalEl = document.getElementById('subtotal');
    const shippingEl = document.getElementById('shipping');
    const taxEl = document.getElementById('tax');
    const totalEl = document.getElementById('total');

    const subtotal = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
    const shipping = subtotal > 0 ? 10.00 : 0.00;
    const tax = +(subtotal * 0.1).toFixed(2); // 10% tax
    const total = subtotal + shipping + tax;

    if (subtotalEl) subtotalEl.textContent = `$${subtotal.toFixed(2)}`;
    if (shippingEl) shippingEl.textContent = `$${shipping.toFixed(2)}`;
    if (taxEl) taxEl.textContent = `$${tax.toFixed(2)}`;
    if (totalEl) totalEl.textContent = `$${total.toFixed(2)}`;

    // Store these values temporarily so they can be sent to the server
    localStorage.setItem('shippingCost', shipping);
    localStorage.setItem('taxAmount', tax);
}

function addToCart(id, name, price) {
    const existing = cart.find(item => item.id === id);
    if (existing) {
        existing.quantity++;
    } else {
        cart.push({ id, name, price, quantity: 1 });
    }
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCart();
    alert('Item added to cart!');
}

function removeItem(id) {
    cart = cart.filter(item => item.id !== id);
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCart();
}

function changeQuantity(id, qty) {
    const item = cart.find(i => i.id === id);
    if (item) {
        item.quantity = parseInt(qty);
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCart();
    }
}

document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', () => {
        addToCart(
            button.getAttribute('data-product-id'),
            button.getAttribute('data-product-name'),
            parseFloat(button.getAttribute('data-product-price'))
        );
    });
});

const checkoutBtn = document.getElementById('checkout');
if (checkoutBtn) {
    checkoutBtn.addEventListener('click', async () => {
        const email = prompt("Enter your email:");
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const shipping = parseFloat(localStorage.getItem('shippingCost')) || 0;
        const tax = parseFloat(localStorage.getItem('taxAmount')) || 0;

        if (cart.length === 0) {
            alert("Your cart is empty!");
            return;
        }

        try {
            const response = await fetch('payment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ cart, email, shipping, tax }) // Include shipping and tax
            });

            const result = await response.json();
            if (result.url) {
                localStorage.removeItem('cart');
                localStorage.removeItem('shippingCost'); // Clean up local storage
                localStorage.removeItem('taxAmount');    // Clean up local storage
                window.location.href = result.url;
            } else {
                alert("Unable to create checkout session.");
            }
        } catch (error) {
            console.error("Checkout error:", error);
            alert("An error occurred while redirecting to payment.");
        }
    });
}

updateCart(); // Initial load