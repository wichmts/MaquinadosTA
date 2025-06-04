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
            ['nombre' => 'CM20-1', 'tipo_proceso' => 4, 'requiere_programa' => true],
            ['nombre' => 'CM20-2', 'tipo_proceso' => 4, 'requiere_programa' => true],
            ['nombre' => 'CM24-1', 'tipo_proceso' => 4, 'requiere_programa' => true],
            ['nombre' => 'CM24-2', 'tipo_proceso' => 4, 'requiere_programa' => true],
            ['nombre' => 'CM40-1', 'tipo_proceso' => 4, 'requiere_programa' => true],
            ['nombre' => 'FC21-1', 'tipo_proceso' => 4, 'requiere_programa' => true],
            ['nombre' => 'FC21-2', 'tipo_proceso' => 4, 'requiere_programa' => true],
            ['nombre' => 'TP10-5', 'tipo_proceso' => 5, 'requiere_programa' => true],
            ['nombre' => 'TP25-1', 'tipo_proceso' => 5, 'requiere_programa' => true],
            ['nombre' => 'TP25-4', 'tipo_proceso' => 5, 'requiere_programa' => true],
            ['nombre' => 'TP11-3', 'tipo_proceso' => 5, 'requiere_programa' => true],
            ['nombre' => 'TC-121', 'tipo_proceso' => 5, 'requiere_programa' => true],
            ['nombre' => 'AMS TECH', 'tipo_proceso' => 9, 'requiere_programa' => true],
            ['nombre' => 'SODICK', 'tipo_proceso' => 9, 'requiere_programa' => true],
            ['nombre' => 'RM1-2', 'tipo_proceso' => 8, 'requiere_programa' => true],
            ['nombre' => 'RM1-3', 'tipo_proceso' => 8, 'requiere_programa' => true],
            ['nombre' => 'RM5-3', 'tipo_proceso' => 8, 'requiere_programa' => true],
            ['nombre' => 'RC10-1', 'tipo_proceso' => 8, 'requiere_programa' => true],
            ['nombre' => 'RIVER 300', 'tipo_proceso' => 9, 'requiere_programa' => true],
            ['nombre' => 'DM323', 'tipo_proceso' => 4, 'requiere_programa' => true],
            ['nombre' => 'FV-15-1', 'tipo_proceso' => 4, 'requiere_programa' => true],
            ['nombre' => 'FV5-1', 'tipo_proceso' => 4, 'requiere_programa' => true],
            ['nombre' => 'MACHUELEADOR', 'tipo_proceso' => 6, 'requiere_programa' => true],
            ['nombre' => 'CAREO MANUAL', 'tipo_proceso' => 3, 'requiere_programa' => false],
        ];
        Maquina::insert($maquinas);
    }
}
