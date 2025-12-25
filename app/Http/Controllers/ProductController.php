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
