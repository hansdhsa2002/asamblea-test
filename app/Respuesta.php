<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Respuesta extends Model
{
    protected $fillable = ['user_id', 'pregunta_id', 'valor'];
}
