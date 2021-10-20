<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFavoriteProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_favorite_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');   // 互联网项目一般不使用外键约束
            $table->unsignedInteger('product_id');
            // $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_favorite_products');
    }
}
