<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .dashboard-header {
            margin-bottom: 30px;
        }
        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
        }
        .card-value {
            font-size: 2rem;
            font-weight: bold;
            color: #0d6efd;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center dashboard-header">
        <div>
            <h1>📊 Dashboard</h1>
            <p class="text-muted mb-0">Summary of your cart</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Back to Products</a>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card p-4 text-center">
                <div class="card-title">Total Items in Cart</div>
                <div class="card-value">{{ $totalQty }}</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-4 text-center">
                <div class="card-title">Total Price</div>
                <div class="card-value">₹{{ number_format($totalPrice, 2) }}</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-4 text-center">
                <div class="card-title">Last Added Product</div>
                @if($lastAdded)
                    @if(isset($lastAdded['image']) && $lastAdded['image'])
                        <div class="mb-2">
                            <img src="{{ asset($lastAdded['image']) }}" alt="{{ $lastAdded['name'] }}" style="height: 60px; width: 60px; object-fit: cover; border-radius: 8px;">
                        </div>
                    @endif
                    <div class="card-value" style="font-size: 1.5rem;">{{ $lastAdded['name'] }}</div>
                    <small class="text-muted">₹{{ number_format($lastAdded['price'], 2) }} × {{ $lastAdded['quantity'] }}</small>
                @else
                    <div class="card-value">-</div>
                @endif
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>