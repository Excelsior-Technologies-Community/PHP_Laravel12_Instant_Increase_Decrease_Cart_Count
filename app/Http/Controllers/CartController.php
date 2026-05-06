<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function add($id)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);

        if(isset($cart[$id])) {
            if($cart[$id]['quantity'] < $product->stock) {
                $cart[$id]['quantity']++;
                $msg = 'Quantity increased!';
                $status = 'success';
            } else {
                return response()->json(['cart' => $cart, 'status' => 'error', 'message' => 'Out of Stock!']);
            }
        } else {
            if($product->stock > 0) {
                $cart[$id] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => $product->image,
                    'stock' => $product->stock,
                    'quantity' => 1
                ];
                $msg = 'Product added to cart!';
                $status = 'success';
            } else {
                return response()->json(['cart' => $cart, 'status' => 'error', 'message' => 'Product is Out of Stock!']);
            }
        }

        session()->put('cart', $cart);
        return response()->json(['cart' => $cart, 'status' => $status, 'message' => $msg]);
    }

    public function update($id, $type)
    {
        $cart = session()->get('cart', []);
        
        if(isset($cart[$id])) {
            if($type == 'inc') {
                if($cart[$id]['quantity'] < $cart[$id]['stock']) {
                    $cart[$id]['quantity']++;
                    $msg = 'Quantity updated!';
                    $status = 'success';
                } else {
                    return response()->json(['cart' => $cart, 'status' => 'warning', 'message' => 'Maximum stock limit reached!']);
                }
            }
            if($type == 'dec') {
                $cart[$id]['quantity']--;
                if($cart[$id]['quantity'] <= 0) {
                    unset($cart[$id]);
                    $msg = 'Item removed from cart!';
                } else {
                    $msg = 'Quantity decreased!';
                }
                $status = 'success';
            }
        }
        
        session()->put('cart', $cart);
        return response()->json(['cart' => $cart, 'status' => $status ?? 'success', 'message' => $msg ?? 'Cart updated']);
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);
        
        if(isset($cart[$id])) {
            unset($cart[$id]);
        }
        
        session()->put('cart', $cart);
        return response()->json(['cart' => $cart, 'status' => 'success', 'message' => 'Item removed completely!']);
    }

    public function checkout()
    {
        $cart = session()->get('cart', []);
        $totalQty = collect($cart)->sum('quantity');
        $totalPrice = collect($cart)->sum(function($item){ 
            return $item['price'] * $item['quantity']; 
        });

        return view('cart.checkout', compact('cart','totalQty','totalPrice'));
    }
}