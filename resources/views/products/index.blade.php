<!DOCTYPE html>
<html>

<head>
    <title>Product List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
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

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">📦 Products List</h3>
            <button class="btn btn-primary cart-btn" data-bs-toggle="modal" data-bs-target="#cartModal">
                🛒 Cart <span id="cartCount" class="badge bg-danger">0</span>
            </button>
        </div>

        <div class="mb-3">
            <a href="{{ route('products.create') }}" class="btn btn-success">➕ Add Product</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

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

        <div class="table-wrapper">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Image</th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price (₹)</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $p)
                            <tr>
                                <td>
                                    @if($p->image)
                                        <img src="{{ asset($p->image) }}" alt="img" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>
                                <td>{{ $p->id }}</td>
                                <td>{{ $p->name }}</td>
                                <td>{{ $p->description ?? '-' }}</td>
                                <td>{{ number_format($p->price, 2) }}</td>
                                <td>
                                    @if($p->stock > 0)
                                        <span class="badge bg-info text-dark">{{ $p->stock }} in stock</span>
                                    @else
                                        <span class="badge bg-danger">Out of Stock</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $p->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ ucfirst($p->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($p->stock > 0 && $p->status == 'active')
                                        <button class="btn btn-sm btn-success" onclick="addToCart({{ $p->id }})">Add</button>
                                    @else
                                        <button class="btn btn-sm btn-secondary" disabled>Unavailable</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>

            <div class="mt-3 text-end fw-bold">
                Total: ₹<span id="total">0.00</span>
            </div>
        </div>

    </div>

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

    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
        });

        function handleResponse(data) {
            loadCart(data.cart);
            Toast.fire({
                icon: data.status,
                title: data.message
            });
        }

        function addToCart(id) {
            $.get(`/cart/add/${id}`, function (data) {
                handleResponse(data);
            });
        }

        function updateCart(id, type) {
            $.get(`/cart/update/${id}/${type}`, function (data) {
                handleResponse(data);
            });
        }

        function removeItem(id) {
            $.get(`/cart/remove/${id}`, function (data) {
                handleResponse(data);
            });
        }

        function loadCart(cart) {
            let html = '';
            let totalQty = 0;
            let totalPrice = 0;
            let cartItemsCount = Object.keys(cart).length;

            if (cartItemsCount === 0) {
                html = `<div class="text-center p-4">
                            <h4 class="text-muted">🛒 Your cart is empty!</h4>
                        </div>`;
            } else {
                for (let id in cart) {
                    totalQty += cart[id].quantity;
                    totalPrice += cart[id].quantity * cart[id].price;
                    
                    let imgHtml = cart[id].image ? `<img src="/${cart[id].image}" style="width:40px;height:40px;object-fit:cover;border-radius:4px;margin-right:10px;">` : '';

                    html += `
                    <div class="cart-item">
                        <div class="d-flex align-items-center">
                            ${imgHtml}
                            <div>
                                <strong>${cart[id].name}</strong><br>
                                <small class="text-muted">₹${parseFloat(cart[id].price).toFixed(2)} x ${cart[id].quantity}</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-sm btn-primary me-1" onclick="updateCart(${id}, 'inc')">+</button>
                            <span class="mx-2 fw-bold">${cart[id].quantity}</span>
                            <button class="btn btn-sm btn-warning me-1" onclick="updateCart(${id}, 'dec')">-</button>
                            <button class="btn btn-sm btn-danger ms-2" onclick="removeItem(${id})">🗑️</button>
                        </div>
                    </div>`;
                }
            }

            document.getElementById('cartItems').innerHTML = html;
            document.getElementById('cartCount').innerText = totalQty;
            document.getElementById('total').innerText = totalPrice.toFixed(2);
        }

        loadCart(@json(session('cart', [])));
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>