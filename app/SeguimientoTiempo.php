<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SeguimientoTiempo extends Model
{

    public function usuario(){
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function fabricacion(){
        return $this->belongsTo(Fabricacion::class, 'fabricacion_id');
    }
    
    public function toArray(){
        $data = parent::toArray();
        Carbon::setLocale('es');
        $result1 = Carbon::createFromFormat('Y-m-d', $this->fecha)->isoFormat('D/M/YYYY');
        $result2 = Carbon::createFromFormat('G:i', $this->hora)->isoFormat('h:mm a');
        $data['fecha_show'] = $result1;
        $data['hora_show'] = $result2;
        $data['usuario'] = $this->usuario->nombre_completo;
        $data['maquina'] = $this->fabricacion && $this->fabricacion->maquina ? $this->fabricacion->maquina->nombre : null;
        return $data;
    }
}
