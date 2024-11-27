<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Especialidad;


class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $role = Role::create(['name' => 'ADMINISTRADOR']);
      $role = Role::create(['name' => 'JEFE DE AREA']);
      $role = Role::create(['name' => 'AUXILIAR DE DISEÑO']);
      $role = Role::create(['name' => 'ALMACENISTA']);
      $role = Role::create(['name' => 'PROGRAMADOR']);
    }
}
