<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PruebaDiseno extends Model
{
    protected $table = "pruebas_diseno";

    public function toArray()
    {
        $data = parent::toArray();
        $data['archivo_dimensional_show'] = $this->getArchivoShow();
        $data['checklist'] = $this->checklist ?  json_decode($this->checklist) : null;
        return $data;
    }

    public function getArchivoShow()
    {
        $archivo = $this->archivo_dimensional;
        $pos = strpos($archivo, '_');
        if ($pos !== false) {
            return substr($archivo, $pos + 1);
        }
        return $archivo;
    }
}
