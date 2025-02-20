<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PruebaProceso extends Model
{
    protected $table = 'pruebas_proceso';

    public function toArray()
    {
        $data = parent::toArray();
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
