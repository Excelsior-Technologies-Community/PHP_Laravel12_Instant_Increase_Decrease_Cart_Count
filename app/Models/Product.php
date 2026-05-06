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
        'image',       // Product image 
        'stock',       // Product stock 
        'status',      // Product status: active, inactive, deleted
        'created_by',  // User ID who created the product
        'updated_by',  // User ID who last updated the product
    ];
}