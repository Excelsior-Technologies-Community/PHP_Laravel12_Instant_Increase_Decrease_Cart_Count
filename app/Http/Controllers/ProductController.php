<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    // List products with search, filter, and pagination
    public function index(Request $request)
    {
        $query = Product::query();

        // Search by any field (name, description, price)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhere('price', 'like', "%$search%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Paginate results (4 per page)
        $products = $query->paginate(4)->withQueryString();

        return view('products.index', compact('products'));
    }

    // Show create form
    public function create()
    {
        return view('products.create');
    }

    // Store product
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|string',
            'price'=>'required|numeric',
            'status'=>'required|in:active,inactive'
        ]);

        Product::create($request->all());

        return redirect()->route('products.index')->with('success','Product added successfully');
    }
}