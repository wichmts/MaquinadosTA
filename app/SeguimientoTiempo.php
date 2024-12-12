<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SeguimientoTiempo extends Model
{

    
    public function toArray(){
        $data = parent::toArray();
        Carbon::setLocale('es');
        $result1 = Carbon::createFromFormat('Y-m-d', $this->fecha)->isoFormat('D/M/YYYY');
        $result2 = Carbon::createFromFormat('G:i', $this->hora)->isoFormat('h:mm a');
        $data['fecha_show'] = $result1;
        $data['hora_show'] = $result2;
        return $data;
    }
}
