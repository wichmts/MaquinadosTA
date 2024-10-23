<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PDF;

use App\User;
use App\SolicitudCotizacion;
use App\Cotizacion;
use App\Referencia;

class UserController extends Controller
{
    /**
     * Display a listing of the users
     *
     * @param  \App\User  $model
     * @return \Illuminate\View\View
     */
   
    public function terminos(){
      return view('webpage.terminos');
    }
    public function verificarCuenta($token){
      $user = User::where('confirmation_token', $token)->firstOrFail();
      $user->active = true;
      $user->save();
      return view('webpage.verificar-cuenta', compact('user'));
    }
    
    public function confirmarServicioCliente($referencia, $token){
      $referencia = Referencia::where('referencia', $referencia)->where('token_cliente', $token)->firstOrFail();
      return view('webpage.confirmar-servicio-cliente', compact('referencia'));
    }
    
    public function store(Request $request){
      $user = User::where('email', $request->email)->get()->first();
      if($user){
        \Session::flash('error', 'Lo sentimos! El usuario ya existe en la base de datos.');
        return redirect()->back();
      }
      $user = new User();
      $user->nombre   =  $request->nombre;
      $user->ap_paterno   =  $request->ap_paterno;
      $user->ap_materno   =  $request->apellido_materno;
      $user->email  =   $request->email;
      $user->celular  =   $request->celular;
      $user->estatus = boolval($request->estatus);
      $user->password = bcrypt($request->password);
      $user->api_token = Str::random(60);
      $user->save();

      $user->assignRole('ADMINISTRADOR');
      $user->syncPermissions($request['permisos']);
      \Session::flash('message', 'Usuario registrado correctamente');
      return redirect('user');
    }

    public function update(Request $request, $id){
      $user = User::findOrfail($id);
      $user->nombre   =  $request->nombre;
      $user->ap_paterno   =  $request->ap_paterno;
      $user->ap_materno   =  $request->ap_materno;
      $user->email  =   $request->email;
      $user->celular  =   $request->celular;
      $user->estatus = boolval($request->estatus);

      if(!is_null($request->password)){
        $user->password = bcrypt($request->password);
      }
      $user->removeRole($user->roles()->first()->name);
      $user->assignRole('ADMINISTRADOR');
      $user->syncPermissions($request['permisos']);
      $user->save();
      \Session::flash('message', 'Usuario modificado correctamente.');
      return redirect('user');

    }
    public function destroy($id){
      $user = User::findOrfail($id);
      $user->delete();
      \Session::flash('message', "Usuario eliminado correctamente");
      return redirect('user');
    }
}
