<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Componente extends Model
{
    public function material(){
        return $this->belongsTo(Material::class);
    }

    public function toArray(){
  		$data = parent::toArray();
        $data['material_nombre'] = $this->material ? $this->material->nombre : '';
        return $data;
    }

}
