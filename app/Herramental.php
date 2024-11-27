<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Herramental extends Model
{
    protected $table = 'herramentales';

    public function proyecto(){
        return $this->belongsTo(Proyecto::class);
    }
}
