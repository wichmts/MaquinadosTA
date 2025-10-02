<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DocumentacionTecnica extends Model
{
    protected $table = 'documentacion_tecnica';

    public function toArray()
    {
        $array = parent::toArray();     
        $array['archivo_public'] = $this->archivo ? '/herramental/'. $this->archivo : '';
        $array['archivo_show'] = $this->archivo ? preg_replace('/^[^_]+_/', '', $this->archivo) : null;
        return $array;
    }
}
