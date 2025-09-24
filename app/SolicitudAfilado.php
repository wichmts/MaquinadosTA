<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class SolicitudAfilado extends Model
{
    protected $table = 'solicitud_afilados';
    
    public function usuario()
    {
        return $this->belongsTo('App\User', 'usuario_id');
    }
    
    public function solicitante()
    {
        return $this->belongsTo('App\User', 'solicitante_id');
    }

    public function componente()
    {
        return $this->belongsTo('App\Componente', 'componente_id');
    }

    public function unidadMedida()
    {
        return $this->belongsTo('App\UnidadDeMedida', 'unidad_medida_id');
    }
    
    public function toArray()
    {
        $array = parent::toArray();
        Carbon::setLocale('es');
        $array['fecha_solicitud_show'] = $this->fecha_solicitud ? Carbon::createFromFormat('Y-m-d', $this->fecha_solicitud)->isoFormat('D/MM/YYYY') : '-';
        $array['fecha_deseada_show'] = $this->fecha_deseada_entrega ? Carbon::createFromFormat('Y-m-d', $this->fecha_deseada_entrega)->isoFormat('D/MM/YYYY') : '-';
        $array['fecha_real_show'] = $this->fecha_real_entrega ? Carbon::createFromFormat('Y-m-d', $this->fecha_real_entrega)->isoFormat('D/MM/YYYY') : '-';
        $array['usuario'] = $this->usuario;
        $array['solicitante'] = $this->solicitante;
        $array['componente'] = $this->componente;
        $array['unidad_de_medida'] = $this->unidadMedida->abreviatura;
        return $array;
    }

}
