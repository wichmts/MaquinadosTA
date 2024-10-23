<?php

use App\User;
use App\Material;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->nombre = 'Administrador';
        $user->ap_paterno = 'del';
        $user->ap_materno = 'Sistema';
        $user->email = 'admin@maquinados.com';
        $user->codigo_acceso = '0932';
        $user->api_token = Str::random(60);
        $user->token = Str::random(60);
        $user->celular = '';
        $user->active = true;
        $user->save();
        $user->assignRole('ADMINISTRADOR');

        $user = new User();
        $user->nombre = 'Almacenista';
        $user->ap_paterno = 'del';
        $user->ap_materno = 'Sistema';
        $user->email = 'almacenista@maquinados.com';
        $user->codigo_acceso = '0933';
        $user->api_token = Str::random(60);
        $user->token = Str::random(60);
        $user->celular = '';
        $user->active = true;
        $user->save();
        $user->assignRole('ALMACENISTA');

        $user = new User();
        $user->nombre = 'Auxiliar';
        $user->ap_paterno = 'de';
        $user->ap_materno = 'Diseno';
        $user->email = 'auxiliardiseno@maquinados.com';
        $user->codigo_acceso = '0934';
        $user->api_token = Str::random(60);
        $user->token = Str::random(60);
        $user->celular = '';
        $user->active = true;
        $user->save();
        $user->assignRole('AUXILIAR DE DISEÑO');


        $material = new Material();
        $material->nombre = 'PLANCHON';
        $material->save();
        
        $material = new Material();
        $material->nombre = 'PLACA';
        $material->save();

        $material = new Material();
        $material->nombre = 'REDONDO';
        $material->save();

        $material = new Material();
        $material->nombre = 'CUADRADO';
        $material->save();

        $material = new Material();
        $material->nombre = 'SOLERA';
        $material->save();



    }
}
