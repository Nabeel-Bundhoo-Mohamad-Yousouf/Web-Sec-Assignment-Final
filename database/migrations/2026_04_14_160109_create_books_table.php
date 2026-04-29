<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id("book_id");
            $table->string("title");
            $table->string("author");
            $table->string("genre");
            $table->text("book_description");
            $table->decimal("price", total:6, places:2);
            $table->decimal("rental_fee", total:6, places:2);
            $table->integer("stock_num");
            $table->string("img_url");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
