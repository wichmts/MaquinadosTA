<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UnidadDeMedida extends Model{

    protected $table = 'unidad_de_medida';

    public function solicitudAfilado()
    {
        return $this->hasMany('App\SolicitudAfilado', 'unidad_medida_id');
    }

    public function toArray()
    {
        $array = parent::toArray();
        Carbon::setLocale('es');
        return $array;
    }    
}