<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Herramental extends Model
{
    protected $table = 'herramentales';

    public function proyecto(){
        return $this->belongsTo(Proyecto::class);
    }

    public function toArray(){
        $array = parent::toArray();
        $array['checklist'] = $this->checklist?json_decode($this->checklist):[];
        $array['archivo2_show'] = preg_replace('/^[^_]*_/', '', $this->archivo2);
        return $array;
    }
}
