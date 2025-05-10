function addToCart(product, price) {
  let cart = JSON.parse(localStorage.getItem('cart')) || [];
  const index = cart.findIndex(item => item.product === product);

  if (index !== -1) {
    cart[index].quantity += 1;
  } else {
    cart.push({ product, price, quantity: 1 });
  }

  localStorage.setItem('cart', JSON.stringify(cart));
  alert(`${product} added to cart.`);
}
