<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJunoTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('juno_tokens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('access_token')->nullable();
            $table->string('bearer')->nullable();
            $table->integer('expires_in')->nullable();
            $table->string('scope')->nullable();
            $table->string('user_name')->nullable();
            $table->text('jti')->nullable();
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
        Schema::dropIfExists('juno_tokens');
    }
}
