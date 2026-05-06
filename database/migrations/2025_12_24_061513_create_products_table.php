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

            // --- નવું ઉમેરેલું (Image અને Stock) ---
            $table->string('image')->nullable(); // Product image (optional)
            $table->integer('stock')->default(0); // Available stock quantity
            // ---------------------------------------

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