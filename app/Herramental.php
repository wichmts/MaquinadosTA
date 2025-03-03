<?php

namespace App;
use App\Componente;
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



    public function toArray(){
        $array = parent::toArray();
        $array['checklist'] = $this->checklist?json_decode($this->checklist):[];
        $array['archivo2_show'] = preg_replace('/^[^_]*_/', '', $this->archivo2);
        return $array;
    }
}
