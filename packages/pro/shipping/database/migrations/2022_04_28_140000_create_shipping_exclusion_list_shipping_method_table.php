<?php

use GetCandy\Base\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShippingExclusionListShippingMethodTable extends Migration
{
    public function up()
    {
        Schema::create($this->prefix.'shipping_exclusion_shipping_method', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('exclusion_id')->constrained(
                $this->prefix.'shipping_exclusions'
            );
            $table->foreignId('method_id')->constrained(
                $this->prefix.'shipping_methods'
            );
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists($this->prefix.'shipping_exclusion_shipping_method');
    }
}
