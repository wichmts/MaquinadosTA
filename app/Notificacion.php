<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notificacion extends Model
{
    protected $table = 'notificaciones';

    public function componente(){
        return $this->belongsTo(Componente::class);
    }

    public function herramental(){
        return $this->belongsTo(Herramental::class);
    }

    public function proyecto(){
        return $this->belongsTo(Proyecto::class);
    }

    public function cliente(){
        return $this->belongsTo(Cliente::class);
    }

    public function anio(){
        return $this->belongsTo(Anio::class);
    }


    public function toArray(){
        Carbon::setLocale('es');
        $data = parent::toArray();
        $data['fecha'] = $this->created_at->isoFormat('D/M/YYYY');
        $data['hora'] = $this->created_at->isoFormat('h:mm a');   
        $data['componente'] = $this->componente ? $this->componente->nombre : null;   
        $data['herramental'] = $this->herramental ? $this->herramental->nombre : null;   
        $data['proyecto'] = $this->proyecto ? $this->proyecto->nombre : null;   
        $data['cliente'] = $this->cliente ? $this->cliente->nombre : null;   
        $data['anio'] = $this->anio ? $this->anio->nombre : null;   
        return $data;
    }
}
