<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'username' => 'admin_user',
            'nombre' => 'Admin',
            'apellido' => 'User',
            'ubicacion' => 'Oficina Principal',
            'unidad_funcional' => 'IT',
            'departamento' => 'Sistemas',
            'area' => 'Desarrollo',
            'cargo' => 'Gerente de IT',
            'estatus' => 1, // Activo por defecto
            'rol' => 'admin',
            'email' => 'admin@example.com', // Correo único
            'password' =>  Hash::make('password'), // ¡Importante hashear la contraseña!
            'email_verified_at' => now(), // Opcional: simula que el correo está verificado
            'remember_token' => \Illuminate\Support\Str::random(10), // Token de recordar
        ]);
 
        // Puedes añadir más usuarios si lo necesitas
        User::create([
            'username' => 'regular_user',
            'nombre' => 'Usuario',
            'apellido' => 'Normal',
            'ubicacion' => 'Sucursal A',
            'unidad_funcional' => 'Ventas',
            'departamento' => 'Atención al Cliente',
            'area' => 'Soporte',
            'cargo' => 'Representante de Ventas',
            'estatus' => 1,
            'rol' => 'user', // Rol por defecto
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'remember_token' => \Illuminate\Support\Str::random(10),
        ]);
    }
}
