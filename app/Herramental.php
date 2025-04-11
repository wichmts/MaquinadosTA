<?php

namespace App;
use App\Componente;
use App\Cliente;
use App\Anio;
use App\PruebaProceso;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Herramental extends Model
{
    protected $table = 'herramentales';

    public function proyecto(){
        return $this->belongsTo(Proyecto::class);
    }

    public function esHerramentalExterno(){
        return Componente::where('herramental_id', $this->id)
            ->get()
            ->first(function($componente) {
                return $componente->esComponenteExterno();
            }) !== null;
    }

    public function fechaFinalizado()
    {
        
        if ($this->esHerramentalExterno()) {
            $componentes = Componente::where('herramental_id', $this->id)
            ->where('refabricado', false)
            ->get();
            
            foreach ($componentes as $componente) {
                if (!$componente->fecha_terminado) {
                    return 'En proceso...';
                }
            }
            $fecha = $componentes->max('fecha_terminado');
            if($fecha){
                return Carbon::createFromFormat('Y-m-d H:i', $fecha)->isoFormat('DD/MM/YYYY h:mm a');
            }else{
                return 'En proceso';
            }
        } else {
            if ($this->fecha_terminado) {            
                return Carbon::createFromFormat('Y-m-d H:i', $this->fecha_terminado)->isoFormat('DD/MM/YYYY h:mm a');
            }else{
                // return $this->updated_at->isoFormat('DD/MM/YYYY h:mm a');
                return 'Sin fecha';
            }
        }
    }


    
    public function toArray(){
        $array = parent::toArray();
        $cliente = Cliente::findOrFail($this->proyecto->cliente_id);
        $anio = Anio::findOrFail($cliente->anio_id);

        $array['archivo_explosionado_show'] = $this->archivo_explosionado ? preg_replace('/^[^_]+_/', '', $this->archivo_explosionado) : null;
        $array['anio'] = $anio->nombre;
        $array['cliente'] = $cliente->nombre;
        $array['proyecto'] = $this->proyecto->nombre;
        $array['rutaHerramental'] = "?a={$anio->id}&c={$cliente->id}&p={$this->proyecto->id}&h={$this->id}";
        $array['fecha_creacion'] = $this->created_at->isoFormat('DD/MM/YYYY h:mm a');
        $array['fecha_finalizado'] = $this->fechaFinalizado();
        if($this->fecha_limite)
             $array['fecha_limite_show'] = Carbon::createFromFormat('Y-m-d', $this->fecha_limite)->isoFormat('DD/MM/YYYY');

        $array['checklist'] = $this->checklist?json_decode($this->checklist):[];
        $array['archivo2_show'] = preg_replace('/^[^_]*_/', '', $this->archivo2);

        


        return $array;
    }
}
