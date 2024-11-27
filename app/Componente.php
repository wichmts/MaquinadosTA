<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Componente extends Model
{
    public function material(){
        return $this->belongsTo(Material::class);
    }

    public function herramental(){
        return $this->belongsTo(Herramental::class);
    }

    public function toArray(){
  		$data = parent::toArray();
        $data['material_nombre'] = $this->material ? $this->material->nombre : '';
        $data['archivo_2d_public'] = $this->archivo_2d ? $this->herramental->proyecto_id .'/' . $this->herramental->id . '/componentes/'. $this->archivo_2d : '';
        $data['archivo_3d_public'] = $this->archivo_3d ? $this->herramental->proyecto_id .'/' . $this->herramental->id . '/componentes/'. $this->archivo_3d : '';
        $data['archivo_explosionado_public'] = $this->archivo_explosionado ? $this->herramental->proyecto_id .'/' . $this->herramental->id . '/componentes/'. $this->archivo_explosionado : '';
        return $data;
    }

}
