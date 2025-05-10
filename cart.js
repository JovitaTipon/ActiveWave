function loadCart() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const container = document.getElementById('cart-container');
  
    if (!cart.length) {
      container.innerHTML = '<p>Your cart is empty.</p>';
      return;
    }
  
    let html = `<table>
      <thead><tr><th>Item</th><th>Price</th><th>Qty</th><th>Subtotal</th><th>Remove</th></tr></thead><tbody>`;
    let total = 0;
  
    cart.forEach((item, i) => {
      const subtotal = item.price * item.quantity;
      total += subtotal;
      html += `<tr>
        <td>${item.product}</td>
        <td>₱${item.price}</td>
        <td><input type="number" value="${item.quantity}" min="1" onchange="updateQty(${i}, this.value)"/></td>
        <td>₱${subtotal}</td>
        <td><button onclick="removeItem(${i})">Remove</button></td>
      </tr>`;
    });
  
    html += `</tbody></table><div class="total">Total: ₱${total}</div>
      <button onclick="checkout()">Checkout with Stripe</button>`;
  
    container.innerHTML = html;
  }
  
  function updateQty(index, qty) {
    const cart = JSON.parse(localStorage.getItem('cart'));
    cart[index].quantity = parseInt(qty);
    localStorage.setItem('cart', JSON.stringify(cart));
    loadCart();
  }
  
  function removeItem(index) {
    const cart = JSON.parse(localStorage.getItem('cart'));
    cart.splice(index, 1);
    localStorage.setItem('cart', JSON.stringify(cart));
    loadCart();
  }
  
  function checkout() {
    // Ideally this sends the cart to a backend for Stripe Checkout session creation.
    alert("Stripe checkout triggered! (Server logic required)");
    // Example: fetch('/create-checkout-session', { method: 'POST', ... })
  }
  
  loadCart();
  