<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceToCartItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // 'price' কলামটি যোগ করা হচ্ছে। এটি float অথবা decimal হতে পারে।
            // nullable(true) দেওয়া হয়েছে যাতে বিদ্যমান row গুলোর জন্য error না দেয়।
            $table->decimal('price', 10, 2)->nullable()->after('quantity'); // 'quantity' কলামের পরে যোগ করা হলো।
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn('price'); // Rollback করার সময় কলামটি drop করা হবে।
        });
    }
}
