<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotelDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotel_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('hotel_id');
            $table->string('nombre_hotel');
            $table->string('puntuacion');
            $table->string('direccion');
            $table->text('descripcion');
            $table->string('servicios');
            $table->string('imagenes');
            $table->string('tipo_habitacion');
            $table->string('servicios_por_tipo_habitacion');
            $table->string('precio');
            $table->string('ocupacion');
            $table->string('opciones');
            $table->string('disponibilidad');
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
        Schema::dropIfExists('hotel_details');
    }
}
