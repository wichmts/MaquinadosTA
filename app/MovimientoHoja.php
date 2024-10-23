<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MovimientoHoja extends Model
{
    
    public function componente(){
        return $this->belongsTo(Componente::class);
    }

    public function proyecto(){
        return $this->belongsTo(Proyecto::class);
    }

     public function toArray(){
        Carbon::setLocale('es');
        $data = parent::toArray();
        $data['fecha'] = $this->created_at->isoFormat('D/M/YYYY');
        $data['hora'] = $this->created_at->isoFormat('h:mm a');   
        $data['py'] = $this->componente ? $this->componente->nombre : null;   
        $data['hr'] = $this->proyecto ? $this->proyecto->nombre : null;   

        return $data;
    }
}
