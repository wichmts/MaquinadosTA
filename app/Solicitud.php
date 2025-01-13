<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Solicitud extends Model
{
    protected $table = 'solicitudes';

    public function usuario()
    {
        return $this->belongsTo('App\User', 'usuario_id');
    }

    public function fabricacion()
    {
        return $this->belongsTo('App\Fabricacion', 'fabricacion_id');
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['usuario'] = $this->usuario;
        $array['fabricacion'] = $this->fabricacion;
        Carbon::setLocale('es');
        $result1 = Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->isoFormat('D/M/YYYY');
        $result2 = Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->isoFormat('h:mm a');
        $array['fecha_show'] = $result1;
        $array['hora_show'] = $result2;

        return $array;
    }
}
