<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->nullable()->constrained('users')->nullOnDelete(); // vendor owner
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->nullable()->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('low_stock_threshold')->default(5);
            $table->timestamps();

            // Full-text indices (MySQL/MariaDB)
            // MySQL fulltext indexes must be created via raw statement in some versions; add after table creation if needed.
        });
        // Add fulltext index if supported
        if (Schema::getConnection()->getDoctrineSchemaManager()->getDatabasePlatform()->getName() === 'mysql') {
            \DB::statement('ALTER TABLE products ADD FULLTEXT fulltext_index (name, description, sku)');
        }
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
