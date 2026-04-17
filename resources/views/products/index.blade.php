<!DOCTYPE html>
<html>

<head>
    <title>Product List</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Optional custom styles */
        body {
            background-color: #f8f9fa;
        }

        .table-wrapper {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        .search-filter .form-control,
        .search-filter .form-select {
            height: 45px;
        }

        .cart-btn {
            position: relative;
        }

        .cart-btn .badge {
            position: absolute;
            top: -5px;
            right: -10px;
            font-size: 0.8rem;
        }

        .cart-item {
            border: 1px solid #dee2e6;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
</head>

<body>

    <div class="container mt-5">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">📦 Products List</h3>
            <button class="btn btn-primary cart-btn" data-bs-toggle="modal" data-bs-target="#cartModal">
                🛒 Cart <span id="cartCount" class="badge bg-danger">0</span>
            </button>
        </div>

        <!-- Add Product Button -->
        <div class="mb-3">
            <a href="{{ route('products.create') }}" class="btn btn-success">➕ Add Product</a>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Search + Filter Form -->
        <div class="card mb-4 p-3 search-filter">
            <form method="GET" action="{{ route('products.index') }}" class="row g-3 align-items-center">
                <div class="col-md-5">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="Search by name, description...">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Search</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary w-100">Reset</a>
                </div>
            </form>
        </div>

        <!-- Product Table -->
        <div class="table-wrapper">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
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
                                <td>{{ number_format($p->price, 2) }}</td>
                                <td>
                                    <span
                                        class="badge {{ $p->status == 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($p->status) }}</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-success" onclick="addToCart({{ $p->id }})">Add</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>

            <!-- Total Amount -->
            <div class="mt-3 text-end fw-bold">
                Total: ₹<span id="total">0.00</span>
            </div>
        </div>

    </div>

    <!-- Cart Modal -->
    <div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">🛒 Cart Items</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="cartItems"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">Dashboard</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Cart JS -->
    <script>
        function addToCart(id) {
            $.get(`/cart/add/${id}`, function (data) {
                loadCart(data.cart);
            });
        }
        function updateCart(id, type) {
            $.get(`/cart/update/${id}/${type}`, function (data) {
                loadCart(data.cart);
            });
        }
        function removeItem(id) {
            $.get(`/cart/remove/${id}`, function (data) {
                loadCart(data.cart);
            });
        }
        function loadCart(cart) {
            let html = '';
            let totalQty = 0;
            let totalPrice = 0;
            for (let id in cart) {
                totalQty += cart[id].quantity;
                totalPrice += cart[id].quantity * cart[id].price;
                html += `
        <div class="cart-item">
            <div>${cart[id].name} - ₹${parseFloat(cart[id].price).toFixed(2)} x ${cart[id].quantity}</div>
            <div>
                <button class="btn btn-sm btn-primary" onclick="updateCart(${id}, 'inc')">+</button>
                <button class="btn btn-sm btn-warning" onclick="updateCart(${id}, 'dec')">-</button>
                <button class="btn btn-sm btn-danger" onclick="removeItem(${id})">Remove</button>
            </div>
        </div>`;
            }
            document.getElementById('cartItems').innerHTML = html;
            document.getElementById('cartCount').innerText = totalQty;
            document.getElementById('total').innerText = totalPrice.toFixed(2);
        }
        loadCart(@json(session('cart', [])));
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>