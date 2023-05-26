<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

    public function __construct()
    {
        //$this->middleware(['role: Super-Admin|User Admin']);
        $this->middleware(['auth']); 
    }



    protected function index(){
        $roles = Role::all();
        return view('auth.register')->with(compact('roles'));
    }

    public function resetPassword(Request $request){
       $user = User::find($request->user_id);
       $user->update(['password'=>Hash::make($user->cedula)]);
       return $user->cedula;
    }

    protected function store(UserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'cedula' => $request->cedula,
            'password' => Hash::make($request->password)
        ]);

        $user->assignRole($request->rol_user);

        foreach ($request->empresa_nit as $empresa_nit){
            DB::table('user_empresa')->insert(['user_id'=>$user->id, 'empresa_nit'=>$empresa_nit]);
        }
        return $user;
    }
}
