<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type')->default('raw_material'); // raw_material, sub_assembly, finished_good
            $table->string('unit_of_measure');
            $table->decimal('unit_cost', 10, 2);
            $table->decimal('weight', 8, 2)->nullable();
            $table->string('material_grade')->nullable();
            $table->json('specifications')->nullable();
            $table->integer('version')->default(1);
            $table->boolean('is_current')->default(true);
            $table->boolean('is_obsolete')->default(false);
            $table->foreignId('created_by')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('item_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained();
            $table->integer('version');
            $table->json('data'); // Stores complete item data snapshot
            $table->text('changes_description')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('item_versions');
        Schema::dropIfExists('items');
    }
};