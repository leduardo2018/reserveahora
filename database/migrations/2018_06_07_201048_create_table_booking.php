<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBooking extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre_usuario');
            $table->string('ciudad');
            $table->string('nombre_hotel');
            $table->string('email_usuario');
            $table->string('telefono_usuario');

            $table->string('direccion_hotel');
            $table->string('telefono_company');


            $table->interger('nigth');
            $table->string('cheking');
            $table->string('checkout');
            $table->string('opciones');

            
            $table->string('tipo_habitacion');
            $table->string('ocupacion');
            $table->string('precio');            
            $table->string('email_company');        
            $table->string('imagen');
            $table->string('numero_confirmacion');



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
        Schema::dropIfExists('booking');
    }
}
