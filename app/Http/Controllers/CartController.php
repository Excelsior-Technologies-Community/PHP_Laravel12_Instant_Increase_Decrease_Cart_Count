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

        if(isset($cart[$id])){
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                'id'=>$product->id,
                'name'=>$product->name,
                'price'=>$product->price,
                'quantity'=>1
            ];
        }

        session()->put('cart', $cart);

        return response()->json(['cart'=>$cart]);
    }

    public function update($id, $type)
    {
        $cart = session()->get('cart', []);
        if(isset($cart[$id])){
            if($type == 'inc') $cart[$id]['quantity']++;
            if($type == 'dec') $cart[$id]['quantity'] = max(1, $cart[$id]['quantity']-1);
        }
        session()->put('cart', $cart);

        return response()->json(['cart'=>$cart]);
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);
        if(isset($cart[$id])) unset($cart[$id]);
        session()->put('cart', $cart);

        return response()->json(['cart'=>$cart]);
    }

    public function checkout()
    {
        $cart = session()->get('cart', []);
        $totalQty = collect($cart)->sum('quantity');
        $totalPrice = collect($cart)->sum(function($item){ return $item['price'] * $item['quantity']; });

        return view('cart.checkout', compact('cart','totalQty','totalPrice'));
    }
}