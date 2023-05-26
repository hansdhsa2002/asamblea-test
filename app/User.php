<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'cedula',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = ['asistio'];

    public function empresas(){
        return $this->belongsToMany(
            Empresa::class,
            'user_empresa',
            'user_id',
            'empresa_nit');
    }
    public function getAsistioAttribute(){
        return UserAsistencia::where('user_id', $this->id)->first();
    }
}
