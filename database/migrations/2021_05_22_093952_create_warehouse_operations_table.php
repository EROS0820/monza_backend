<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseOperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_operations', function (Blueprint $table) {
            $table->id();
            $table->integer('assortment');
            $table->integer('warehouse');
            $table->integer('unit');
            $table->integer('measure_unit');
            $table->integer('contractor');
            $table->date('date');
            $table->string('receipt_value');
            $table->string('issue_amount');
            $table->string('reception_frequency');
            $table->string('release_frequency');
            $table->string('inventory');
            $table->string('order_quantity');
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
        Schema::dropIfExists('warehouse_operations');
    }
}
