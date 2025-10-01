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
        return $array;
    }
}
