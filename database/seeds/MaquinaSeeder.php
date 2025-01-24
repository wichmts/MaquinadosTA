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
            'CM20-1',
            'CM20-2',
            'CM24-1',
            'CM24-2',
            'CM40-1',
            'FC21-1',
            'FC21-2',
            'TP10-5',
            'TP25-1',
            'TP25-4',
            'TP11-3',
            'TC-121',
            'AMS TECH',
            'SODICK',
            'RM1-2',
            'RM1-3',
            'RM5-3',
            'RC10-1',
            'RIVER 300',
            'DM323',
            'FV-15-1',
            'FV5-1',
            'MACHUELEADOR'
        ];

        foreach ($maquinas as $nombre) {
            Maquina::create([
                'nombre' => $nombre,
                'tipo_proceso' => 1, 
            ]);
        }
    }
}
