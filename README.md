# PHP_Laravel12_Instant_Increase_Decrease_Cart_Count

PHP_Laravel12_Instant_Increase_Decrease_Cart_Count is a beginner-to-intermediate Laravel 12 project designed to simulate a real-time shopping cart system. It demonstrates how to manage products and a session-based cart with instant increase/decrease functionality using AJAX (Fetch API), without requiring page reloads.

---

## Project Features

* Laravel 12 project structure
* Product add page 
* Add to cart instantly
* Increase / decrease cart quantity without reload
* Remove item from cart
* Session‑based cart system
* Clean Bootstrap UI


---

## Tech Stack

* **Backend**: Laravel 12
* **Frontend**: Blade + Bootstrap 5
* **AJAX**: Fetch API
* **Database**: MySQL
* **Cart Storage**: Laravel Session

---

## Project Name

```
PHP_Laravel12_Instant_Increase_Decrease_Cart_Count
```

---

## Step 1: Create Laravel 12 Project

```bash
composer create-project laravel/laravel PHP_Laravel12_Instant_Increase_Decrease_Cart_Count "12.*"
cd PHP_Laravel12_Instant_Increase_Decrease_Cart_Count
php artisan serve
```

---


## Step 2: Database Configuration

Update `.env` file:

```env
DB_DATABASE=cart_count_db
DB_USERNAME=root
DB_PASSWORD=
```

Create database using command:

```bash
php artisan serve
```

---


## Step 3: Create Product Migration & Model

```bash
php artisan make:model Product -m
```

**Product Migration Table**

### database/migrations/xxxx_create_products_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Create 'products' table
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Primary key: 'id'

            $table->string('name'); // Product name
            $table->text('description')->nullable(); // Product description (optional)

            $table->integer('price'); // Product price (integer, can change to decimal if needed)

            // Enum status field with default 'active'
            $table->enum('status', ['active', 'inactive', 'deleted'])
                  ->default('active');

            // Track which user created/updated the product (nullable for now)
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Soft delete support (adds 'deleted_at' column)
            $table->softDeletes();

            $table->timestamps(); // Adds 'created_at' and 'updated_at'
        });
    }

    public function down(): void
    {
        // Drop the 'products' table if rollback
        Schema::dropIfExists('products');
    }
};
```

Run migration:

```bash
php artisan migrate
```



**Product Model**

### app/Models/Product.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes; // Enables soft delete functionality (adds deleted_at handling)

    // Mass assignable fields
    protected $fillable = [
        'name',        // Product name
        'description', // Product description
        'price',       // Product price
        'status',      // Product status: active, inactive, deleted
        'created_by',  // User ID who created the product
        'updated_by',  // User ID who last updated the product
    ];
}
```

---



## Step 4: Create Product Controller

```bash
php artisan make:controller ProductController
```

File: app/Http/Controllers/ProductController.php

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    // Product index page: Display all products in ascending order by ID
    public function index()
    {
        $products = Product::orderBy('id', 'asc')->get(); // Fetch products ordered by ID ascending
        return view('products.index', compact('products')); // Pass products to the index view
    }

    // Product create page: Show form to add a new product
    public function create()
    {
        return view('products.create'); // Load the create product form
    }

    // Store product: Save new product into database
    public function store(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'name'        => 'required|string|max:255',           // Product name is required
            'description' => 'nullable|string',                  // Description is optional
            'price'       => 'required|integer|min:1',           // Price must be a positive integer
            'status'      => 'required|in:active,inactive,deleted' // Status must be one of the defined enums
        ]);

        // Create product in database
        Product::create([
            'name'        => $request->name,        // Assign name
            'description' => $request->description, // Assign description
            'price'       => $request->price,       // Assign price
            'status'      => $request->status,      // Assign status
            'created_by'  => 1,                      // Static user ID for demo purposes
        ]);

        // Redirect to product index page with success message
        return redirect()->route('products.index')->with('success', 'Product added successfully');
    }
}
```


---


## Step 5: Create Cart Controller

```bash
php artisan make:controller CartController
```

File: app/Http/Controllers/CartController.php

```php
<?php

namespace App\Http\Controllers;

use App\Models\Product;

class CartController extends Controller
{
    
    // Add a product to the cart
    public function add($id)
    {
        $product = Product::findOrFail($id); // Find product by ID or fail with 404
        $cart = session()->get('cart', []); // Get current cart from session

        $cart[$id]['name'] = $product->name; // Set product name in cart
        $cart[$id]['price'] = $product->price; // Set product price in cart
        $cart[$id]['quantity'] = ($cart[$id]['quantity'] ?? 0) + 1; // Increment quantity if exists, otherwise set to 1

        session()->put('cart', $cart); // Save updated cart to session
        return response()->json(['cart' => $cart]); // Return cart as JSON for AJAX
    }

    // Update product quantity in the cart
    public function update($id, $type)
    {
        $cart = session()->get('cart'); // Get current cart

        if ($type === 'inc') $cart[$id]['quantity']++; // Increase quantity
        if ($type === 'dec' && $cart[$id]['quantity'] > 1) $cart[$id]['quantity']--; // Decrease quantity (min 1)

        session()->put('cart', $cart); // Save updated cart
        return response()->json(['cart' => $cart]); // Return cart as JSON for AJAX
    }

    // Remove a product from the cart
    public function remove($id)
    {
        $cart = session()->get('cart'); // Get current cart
        unset($cart[$id]); // Remove product by ID
        session()->put('cart', $cart); // Save updated cart
        return response()->json(['cart' => $cart]); // Return cart as JSON for AJAX
    }
}
```

---


## Step 6: Define Routes

File: routes/web.php

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;

// Default welcome page
Route::get('/', function () {
    return view('welcome'); // Loads the default Laravel welcome view
});

// Product Routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index'); // Show all products
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create'); // Show form to create a new product
Route::post('/products/store', [ProductController::class, 'store'])->name('products.store'); // Store new product in database

// Cart Routes
Route::get('/cart/add/{id}', [CartController::class,'add']); // Add product to cart via AJAX
Route::get('/cart/update/{id}/{type}', [CartController::class,'update']); // Update cart quantity (increase/decrease) via AJAX
Route::get('/cart/remove/{id}', [CartController::class,'remove']); // Remove product from cart via AJAX
```

---


## Step 7: Product Create Page

resources/views/products/create.blade.php

Explaination: This page is the Product Create Form where users can add a new product to the database. It includes fields for name, 

description, price, and status, and displays a success message after submission. The form uses Bootstrap for styling and CSRF protection 

for security.


```
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
```

## Step 8: Product Index Page

resources/views/products/index.blade.php

Explaination: This page displays a list of products with an Add to Cart button for each item. Users can click the cart button to open a
 
modal showing all cart items, where they can increase, decrease, or remove quantities instantly using AJAX. The page also shows a cart
 
count badge that updates in real time, all styled with Bootstrap. 

```
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
```

---


## Project Structure

```
PHP_Laravel12_Instant_Increase_Decrease_Cart_Count/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── ProductController.php
│   │       └── CartController.php
│   └── Models/
│       └── Product.php
├── bootstrap/
├── config/
├── database/
│   ├── migrations/
│   │   └── xxxx_create_products_table.php
│   
├── public/
├── resources/
│   ├── views/
│   │   └── products/
│   │       ├── create.blade.php
│   │       └── index.blade.php
│   └── css/
├── routes/
│   └── web.php
├── .env
└── ...
```

Run Project:

```bash
php artisan serve
```

Open in browser:

```
http://127.0.0.1:8000/products
```

---


## Output

#### Product Create Page 

<img width="1919" height="1027" alt="Screenshot 2025-12-25 100301" src="https://github.com/user-attachments/assets/2f69d6bf-bf6d-4e17-b31d-12584302376e" />


#### Product Index Page

<img width="1919" height="1030" alt="Screenshot 2025-12-25 100514" src="https://github.com/user-attachments/assets/97bfcdda-dca6-48c1-9172-e881c24337d4" />


#### Instant Increase Cart Count

<img width="1919" height="1030" alt="Screenshot 2025-12-25 100559" src="https://github.com/user-attachments/assets/2e2cbf54-9562-4e11-97ba-3f9e1dc0510d" />


#### Instant Decrease Cart Count

<img width="1916" height="1027" alt="Screenshot 2025-12-25 100621" src="https://github.com/user-attachments/assets/e009f213-8560-4aaf-b5da-16026e5b2e73" />


---

Your PHP_Laravel12_Instant_Increase_Decrease_Cart_Count Project is Now Ready!
