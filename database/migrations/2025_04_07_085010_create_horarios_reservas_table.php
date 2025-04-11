<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('horarios_reservas', function (Blueprint $table) {
            $table->id();

            $table->dateTime('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->foreignId('fk_idReserva')->index();
            $table->foreignId('fk_idSala')->index();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios_reservas');
    }
};
