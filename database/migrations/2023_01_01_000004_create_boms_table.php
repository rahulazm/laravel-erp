<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('boms', function (Blueprint $table) {
    $table->id();

    $table->string('bom_number')->unique();
    $table->string('name');

    $table->foreignId('assembly_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->foreignId('sales_order_id')
          ->nullable()
          ->constrained()
          ->nullOnDelete();

    $table->enum('status', [
        'draft','pending_approval','approved','rejected','obsolete'
    ])->default('draft');

    $table->integer('version')->default(1);
    $table->boolean('is_current')->default(true);

    $table->foreignId('created_by')
          ->constrained('users');

    $table->foreignId('approved_by')
          ->nullable()
          ->constrained('users');

    $table->timestamp('approved_at')->nullable();

    $table->softDeletes();
    $table->timestamps();
});


        Schema::create('bom_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bom_id')->constrained();
            $table->integer('version');
            $table->json('data'); // Stores complete BOM data snapshot
            $table->text('changes_description')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bom_versions');
        Schema::dropIfExists('boms');
    }
};