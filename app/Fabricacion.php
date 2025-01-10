<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fabricacion extends Model
{
    protected $table = 'fabricaciones';

    public function componente(){
        return $this->belongsTo(Componente::class);
    }

    public function maquina(){
        return $this->belongsTo(Maquina::class);
    }


    public function toArray()
    {
  		$data = parent::toArray();
        $data['archivo_show'] = $this->getArchivoShow();
        $data['proceso_id'] = $this->maquina? $this->maquina->tipo_proceso : null;
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
