<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contractors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->string('GLN');
            $table->string('address');
            $table->string('postal_code');
            $table->string('city');
            $table->string('order_fulfillment_time');
            $table->string('delivery_time_deviation');
            $table->string('minimum_order_quantity');
            $table->string('minimum_order_value');
            $table->string('description');
            $table->boolean('active');
            $table->boolean('supplier');
            $table->boolean('recipient');
            $table->boolean('supplier_transport');
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
        Schema::dropIfExists('contractors');
    }
}
