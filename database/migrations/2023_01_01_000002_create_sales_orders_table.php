<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->text('shipping_address');
            $table->string('valve_type');
            $table->foreignId('valve_category_id')->constrained('valve_categories');
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['draft', 'pending', 'confirmed', 'in_production', 'completed', 'cancelled'])->default('draft');
            $table->date('delivery_date');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sales_orders');
    }
};