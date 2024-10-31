<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Hoja extends Model
{
    public function movimientos(){
        return $this->hasMany(MovimientoHoja::class);
    }

    public function getUltimoMovimientoAttribute(){
        return $this->movimientos()->latest('created_at')->first();
    }

    public function toArray(){
        Carbon::setLocale('es');
        $data = parent::toArray();
        $data['fecha_entrada'] = $this->fecha_entrada  ? Carbon::parse($this->fecha_entrada)->isoFormat('D/M/YYYY')  : '';
        $data['ultimo_movimiento'] = $this->ultimo_movimiento ? $this->ultimo_movimiento->created_at->isoFormat('D/M/YYYY h:mm a') : '';
        return $data;
    }
}
