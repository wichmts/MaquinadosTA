<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ComponenteCompra extends Model
{
    protected $table = 'componentes_compra';

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at_show'] = Carbon::parse($this->created_at)->format('d/m/Y H:i a');
        return $array;
    }
}
