<!DOCTYPE html>
<html>
<head>
    <title>Product List</title>
    <!-- Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery for DOM manipulation and AJAX if needed -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>📦 Products List</h3>
        <!-- Cart Button with badge showing cart count -->
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cartModal">
            🛒 Cart <span id="cartCount" class="badge bg-danger">0</span>
        </button>
    </div>

    <!-- Button to add new product -->
    <a href="{{ route('products.create') }}" class="btn btn-success mb-3">➕ Add Product</a>

    <!-- Success message after adding product -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Products Table -->
    <table class="table table-bordered bg-white shadow-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price (₹)</th>
                <th>Status</th>
                <th>Add to Cart</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $p)
            <tr>
                <td>{{ $p->id }}</td>
                <td>{{ $p->name }}</td>
                <td>{{ $p->description ?? '-' }}</td>
                <td>{{ number_format($p->price,2) }}</td>
                <td>{{ ucfirst($p->status) }}</td>
                <td>
                    <!-- Add product to cart -->
                    <button class="btn btn-sm btn-success" onclick="addToCart({{ $p->id }})">Add</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Cart Modal -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">🛒 Cart Items</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <!-- Cart items will be dynamically loaded here -->
      <div class="modal-body" id="cartItems"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- JavaScript for cart functionality -->
<script>
function addToCart(id){
    // Add product to cart via AJAX
    fetch(`/cart/add/${id}`)
        .then(res => res.json())
        .then(data => loadCart(data.cart));
}

function updateCart(id,type){
    // Increase or decrease product quantity
    fetch(`/cart/update/${id}/${type}`)
        .then(res => res.json())
        .then(data => loadCart(data.cart));
}

function removeItem(id){
    // Remove product from cart
    fetch(`/cart/remove/${id}`)
        .then(res => res.json())
        .then(data => loadCart(data.cart));
}

function loadCart(cart){
    // Render cart items in modal and update cart count
    let html='';
    let totalQty = 0;
    for(let id in cart){
        totalQty += cart[id].quantity;
        html += `
            <div class="border p-2 mb-2 d-flex justify-content-between align-items-center">
                <div>
                    ${cart[id].name} - ₹${cart[id].price.toFixed(2)} x ${cart[id].quantity}
                </div>
                <div>
                    <button class="btn btn-sm btn-primary" onclick="updateCart(${id}, 'inc')">+</button>
                    <button class="btn btn-sm btn-warning" onclick="updateCart(${id}, 'dec')">-</button>
                    <button class="btn btn-sm btn-danger" onclick="removeItem(${id})">Remove</button>
                </div>
            </div>`;
    }
    document.getElementById('cartItems').innerHTML = html;
    document.getElementById('cartCount').innerText = totalQty;
}

// Load cart items from session on page load
loadCart(@json(session('cart', [])));
</script>

<!-- Bootstrap JS for modal functionality -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
