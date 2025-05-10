let cart = JSON.parse(localStorage.getItem('cart')) || [];

function updateCart() {
  const container = document.getElementById('cart-items');
  const checkoutBtn = document.getElementById('checkout');

  if (!container) return;

  container.innerHTML = '';

  if (cart.length === 0) {
    container.innerHTML = `<p class="empty-cart-msg">Your cart is empty. Start shopping now!</p>`;
    if (checkoutBtn) checkoutBtn.style.display = 'none'; //Hide button
    return;
  }

  if (checkoutBtn) checkoutBtn.style.display = 'inline-block'; //Show button

  cart.forEach(item => {
    const div = document.createElement('div');
    div.classList.add('cart-item');
    div.innerHTML = `
      <p>${item.name} - $${item.price}</p>
      <input type="number" value="${item.quantity}" min="1" onchange="changeQuantity('${item.id}', this.value)">
      <button onclick="removeItem('${item.id}')">Remove</button>
    `;
    container.appendChild(div);
  });
}

function addToCart(id, name, price) {
  const existing = cart.find(item => item.id === id);
  if (existing) {
    existing.quantity++;
  } else {
    cart.push({ id, name, price, quantity: 1 });
  }
  localStorage.setItem('cart', JSON.stringify(cart));
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

    if (cart.length === 0) {
      alert("Your cart is empty!");
      return;
    }

    try {
      const response = await fetch('payment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ cart, email })
      });

      const result = await response.json();
      if (result.url) {
        localStorage.removeItem('cart'); //Clear cart after Stripe session created
        window.location.href = result.url; //Redirect to Stripe Checkout
      } else {
        alert("Unable to create checkout session.");
      }
    } catch (error) {
      console.error("Checkout error:", error);
      alert("An error occurred while redirecting to payment.");
    }
  });
}

updateCart();


