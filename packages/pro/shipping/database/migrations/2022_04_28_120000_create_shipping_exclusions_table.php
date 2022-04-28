<?php

use GetCandy\Base\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShippingExclusionsTable extends Migration
{
    public function up()
    {
        Schema::create($this->prefix.'shipping_exclusions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('shipping_method_id')->constrained(
                $this->prefix.'shipping_methods'
            );
            $table->morphs('purchasable', 'shipping_exclusions_purchasable_type_purchasable_id_index');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists($this->prefix.'shipping_exclusions');
    }
}
