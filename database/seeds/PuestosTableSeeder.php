<?php

use Illuminate\Database\Seeder;
use App\Puesto;


class PuestosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $puestos = [
           ['nombre' => 'JEFE DE MAQUINADOS'],
           ['nombre' => 'OPERADOR DE CNC'],
           ['nombre' => 'AUXILIAR DE CNC'],
           ['nombre' => 'APARATISTA'],
           ['nombre' => 'AYUDANTE GENERAL'],
           ['nombre' => 'EXTERNO - TORNERO']
        ];
    
        Puesto::insert($puestos);
    }
}
