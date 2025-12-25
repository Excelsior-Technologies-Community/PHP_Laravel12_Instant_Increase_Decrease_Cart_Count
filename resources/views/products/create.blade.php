<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
    <!-- Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card p-4 shadow">
        <h4 class="mb-3">➕ Add Product</h4>

        <!-- Display success message after form submission -->
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Product creation form -->
        <form method="POST" action="{{ route('products.store') }}">
            @csrf <!-- CSRF token for security -->

            <!-- Product Name input -->
            <div class="mb-3">
                <label>Product Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <!-- Product Description textarea -->
            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control"></textarea>
            </div>

            <!-- Product Price input -->
            <div class="mb-3">
                <label>Price</label>
                <input type="number" name="price" class="form-control" required>
            </div>

            <!-- Product Status select -->
            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <!-- Submit button -->
            <button class="btn btn-primary">Save Product</button>
        </form>
    </div>
</div>

</body>
</html>
