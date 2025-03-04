<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PruebaProceso extends Model
{
    protected $table = 'pruebas_proceso';

    public function toArray()
    {
        Carbon::setLocale('es');
        $data = parent::toArray();

        $result1 = Carbon::createFromFormat('Y-m-d H:i', $this->fecha_inicio)->isoFormat('D/M/YYYY h:mm a');
        $result2 = Carbon::createFromFormat('Y-m-d H:i', $this->fecha_liberada)->isoFormat('D/M/YYYY h:mm a');
        $data['fecha_inicio_show'] = $result1;
        $data['fecha_liberada_show'] = $result2;
        
        $data['archivo_show'] = $this->getArchivoShow();
        return $data;
    }

    public function getArchivoShow()
    {
        $archivo = $this->archivo;
        $pos = strpos($archivo, '_');
        if ($pos !== false) {
            return substr($archivo, $pos + 1);
        }
        return $archivo;
    }
}
