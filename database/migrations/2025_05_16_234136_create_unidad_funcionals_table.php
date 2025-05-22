<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('unidad_funcionals', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->timestamps();
        });

        $unidad_funcionals = [
            ["nombre" => "IT"],
            ["nombre" => "Desarrollo"],
            ["nombre" => "Ventas"],
            ["nombre" => "Sistemas"],
            
        ];

        DB::table('unidad_funcionals')->insert($unidad_funcionals);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidad_funcionals');
    }
};
