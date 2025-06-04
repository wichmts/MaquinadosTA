<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Maquina extends Model
{
    protected $fillable = ['nombre', 'tipo_proceso', 'requiere_programa'];
}
