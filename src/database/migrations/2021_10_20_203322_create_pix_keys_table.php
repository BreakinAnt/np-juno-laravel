<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePixKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pix_keys', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('key');
            $table->timestamp('creation_date_time')->nullable();
            $table->timestamp('ownership_date_time')->nullable();
            $table->string('idempotency_key');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pix_keys');
    }
}
