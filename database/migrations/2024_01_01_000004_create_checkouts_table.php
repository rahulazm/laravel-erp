<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('checkouts', function (Blueprint $table) {
            $table->id();
            $table->morphs('checkoutable'); // Polymorphic: can checkout BOMs, Items, etc.
            $table->foreignId('user_id')->constrained();
            $table->timestamp('checked_out_at');
            $table->timestamp('checked_in_at')->nullable();
            $table->text('purpose')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('checkouts');
    }
};