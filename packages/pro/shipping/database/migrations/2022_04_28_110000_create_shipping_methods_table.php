<?php

use GetCandy\Base\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShippingMethodsTable extends Migration
{
    public function up()
    {
        Schema::create($this->prefix.'shipping_methods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('shipping_zone_id')->constrained(
                $this->prefix.'shipping_zones'
            );
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('code')->unique();
            $table->json('data')->nullable();
            $table->string('driver');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists($this->prefix.'shipping_methods');
    }
}
