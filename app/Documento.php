<?php

namespace App;
use Carbon\Carbon;


use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    public function toArray(){
        Carbon::setLocale('es');
        $result = Carbon::createFromFormat('Y-m-d G:i', substr($this->created_at, 0, 16))->isoFormat('D/M/YYYY h:mm a');
  		$data = parent::toArray();
        $data['nombre_public'] = substr($this->nombre, strpos($this->nombre, '_') + 1 , strlen($this->nombre));
        $data['fecha_subida'] = $result;
        $data['extension'] = pathinfo($this->nombre, PATHINFO_EXTENSION);
        return $data;
    }
}
