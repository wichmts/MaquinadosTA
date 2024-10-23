<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'Programacion de cargas']);
        Permission::create(['name' => 'Licitaciones']);
        Permission::create(['name' => 'Clientes']);
        Permission::create(['name' => 'Prospectos']);
        Permission::create(['name' => 'Salidas y Destinos']);
        Permission::create(['name' => 'Conductores']);
        Permission::create(['name' => 'Camiones y  Remolques']);
        Permission::create(['name' => 'Transportistas externos']);
        Permission::create(['name' => 'Usuarios y Vendedores']);
    }
}
