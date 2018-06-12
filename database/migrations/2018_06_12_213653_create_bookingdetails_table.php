<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingdetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookingdetails', function (Blueprint $table) {
            $table->increments('id');
            $table->string('hotel_id');
            $table->string('nombre_hotel');
            $table->string('direccion');
            $table->text  ('descripcion');
            $table->string('checkin');
            $table->string('checkout');
            $table->integer('noches');
            $table->string('tipo_habitacion');
            $table->string('precio');
            $table->string('email');
            $table->string('nombre');
            $table->string('nombre_huesped');
            $table->string('telefono');
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
        Schema::dropIfExists('bookingdetails');

    }
}
