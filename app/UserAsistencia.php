<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAsistencia extends Model
{
    protected $table = 'users_asistencias';
    protected $fillable = ['user_id'];
}
