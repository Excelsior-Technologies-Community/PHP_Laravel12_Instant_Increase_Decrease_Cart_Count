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
