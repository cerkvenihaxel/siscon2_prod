<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwilioSenderLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twilio_sender_logs', function (Blueprint $table) {
            $table->id();
            $table->string('id_afiliado');
            $table->string('telefono');
            $table->string('endpoint');
            $table->json('message_vars');
            $table->boolean('exitoso')->default(false);
            $table->text('respuesta')->nullable();
            $table->text('error')->nullable();
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
        Schema::dropIfExists('twilio_sender_logs');
    }
}
