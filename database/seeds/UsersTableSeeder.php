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
        $user->assignRole('DIRECCION');

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

        $user = new User();
        $user->nombre = 'Jefe';
        $user->ap_paterno = 'de';
        $user->ap_materno = 'Area';
        $user->email = 'jefedearea@maquinados.com';
        $user->codigo_acceso = '0935';
        $user->api_token = Str::random(60);
        $user->token = Str::random(60);
        $user->celular = '';
        $user->active = true;
        $user->maquinas = "[1]";
        $user->save();
        $user->assignRole('JEFE DE AREA');
        $user->assignRole('PROGRAMADOR');
        $user->assignRole('OPERADOR');

        $user = new User();
        $user->nombre = 'FIDEL';
        $user->ap_paterno = '';
        $user->ap_materno = '';
        $user->email = 'programador@maquinados.com';
        $user->codigo_acceso = '0936';
        $user->api_token = Str::random(60);
        $user->token = Str::random(60);
        $user->celular = '';
        $user->active = true;
        $user->save();
        $user->assignRole('PROGRAMADOR');

        $user = new User();
        $user->nombre = 'OPERADOR';
        $user->ap_paterno = '';
        $user->ap_materno = '';
        $user->email = 'operador@maquinados.com';
        $user->codigo_acceso = '0937';
        $user->api_token = Str::random(60);
        $user->token = Str::random(60);
        $user->celular = '';
        $user->active = true;
        $user->maquinas = "[2, 3]";
        $user->save();
        $user->assignRole('OPERADOR');

        $user = new User();
        $user->nombre = 'MATRICERO';
        $user->ap_paterno = '';
        $user->ap_materno = '';
        $user->email = 'matricero@maquinados.com';
        $user->codigo_acceso = '0938';
        $user->api_token = Str::random(60);
        $user->token = Str::random(60);
        $user->celular = '';
        $user->active = true;
        $user->save();
        $user->assignRole('MATRICERO');

        $user = new User();
        $user->nombre = 'PRUEBAS DE DISEÑO';
        $user->ap_paterno = '';
        $user->ap_materno = '';
        $user->email = 'diseño@maquinados.com';
        $user->codigo_acceso = '0939';
        $user->api_token = Str::random(60);
        $user->token = Str::random(60);
        $user->celular = '';
        $user->active = true;
        $user->save();
        $user->assignRole('DISEÑO');

        $user = new User();
        $user->nombre = 'PRUEBAS DE PROCESOS';
        $user->ap_paterno = '';
        $user->ap_materno = '';
        $user->email = 'procesos@maquinados.com';
        $user->codigo_acceso = '0940';
        $user->api_token = Str::random(60);
        $user->token = Str::random(60);
        $user->celular = '';
        $user->active = true;
        $user->save();
        $user->assignRole('PROCESOS');


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
