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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->nullable();
            $table->string('nombre')->nullable();
            $table->string('apellido')->nullable();

            $table->string('ubicacion')->nullable();
            $table->string('unidad_funcional')->nullable();
            $table->string('departamento')->nullable();
            $table->string('area')->nullable();
            $table->string('cargo')->nullable();
            $table->boolean('estatus')->default(1)->comment('por defecto activo 1, para inactivo 0');

            $table->enum('rol', ['admin', 'user'])->default('user');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->softDeletes();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
