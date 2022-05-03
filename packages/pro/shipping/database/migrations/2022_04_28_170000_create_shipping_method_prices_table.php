<?php

use GetCandy\Base\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShippingMethodPricesTable extends Migration
{
    public function up()
    {
        Schema::create($this->prefix . 'shipping_method_prices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('shipping_method_id')->constrained(
                $this->prefix . 'shipping_methods'
            );
            $table->foreignId('currency_id')->constrained(
                $this->prefix . 'currencies'
            );
            $table->foreignId('customer_group_id')->constrained(
                $this->prefix . 'customer_groups'
            )->nullable();
            $table->integer('price')->unsigned()->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists($this->prefix . 'shipping_method_prices');
    }
}
