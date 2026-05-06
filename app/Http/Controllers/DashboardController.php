<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        
        $totalQty = collect($cart)->sum('quantity');
        
        $totalPrice = collect($cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
        
        $lastAdded = end($cart) ?: null;

        return view('dashboard', compact('cart', 'totalQty', 'totalPrice', 'lastAdded'));
    }
}