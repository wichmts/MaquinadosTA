<?php

use Illuminate\Database\Seeder;
use App\Maquina;


class MaquinaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $maquinas = [
            ['nombre' => 'CM20-1', 'tipo_proceso' => 4],
            ['nombre' => 'CM20-2', 'tipo_proceso' => 4],
            ['nombre' => 'CM24-1', 'tipo_proceso' => 4],
            ['nombre' => 'CM24-2', 'tipo_proceso' => 4],
            ['nombre' => 'CM40-1', 'tipo_proceso' => 4],
            ['nombre' => 'FC21-1', 'tipo_proceso' => 4],
            ['nombre' => 'FC21-2', 'tipo_proceso' => 4],
            ['nombre' => 'TP10-5', 'tipo_proceso' => 5],
            ['nombre' => 'TP25-1', 'tipo_proceso' => 5],
            ['nombre' => 'TP25-4', 'tipo_proceso' => 5],
            ['nombre' => 'TP11-3', 'tipo_proceso' => 5],
            ['nombre' => 'TC-121', 'tipo_proceso' => 5],
            ['nombre' => 'AMS TECH', 'tipo_proceso' => 9],
            ['nombre' => 'SODICK', 'tipo_proceso' => 9],
            ['nombre' => 'RM1-2', 'tipo_proceso' => 8],
            ['nombre' => 'RM1-3', 'tipo_proceso' => 8],
            ['nombre' => 'RM5-3', 'tipo_proceso' => 8],
            ['nombre' => 'RC10-1', 'tipo_proceso' => 8],
            ['nombre' => 'RIVER 300', 'tipo_proceso' => 9],
            ['nombre' => 'DM323', 'tipo_proceso' => 4],
            ['nombre' => 'FV-15-1', 'tipo_proceso' => 4],
            ['nombre' => 'FV5-1', 'tipo_proceso' => 4],
            ['nombre' => 'MACHUELEADOR', 'tipo_proceso' => 6],
            ['nombre' => 'CAREO MANUAL', 'tipo_proceso' => 3],
        ];
        Maquina::insert($maquinas);
    }
}
