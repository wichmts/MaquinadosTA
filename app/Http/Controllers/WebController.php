<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Proveedor;
use App\Embarque;
use App\SolicitudCotizacion;
use App\Cotizacion;
use Barryvdh\DomPDF\Facade as PDF;


class WebController extends Controller
{
    public function configuracion(){
        return view('admin-panel.configuracion');
    }
    public function usuarios(){
      if (auth()->user()->hasPermissionTo('Usuarios y Vendedores'))
          return view('admin-panel.usuarios');
      return redirect()->route('home')->with('error', 'No cuenta con los permisos necesarios para acceder este recurso.');
    }

    public function cargaComponentes(){
        if (auth()->user()->roles()->first()->name == 'AUXILIAR DE DISEÑO')
            return view('auxiliar-diseno.carga-componentes');
        return redirect()->route('home')->with('error', 'No cuenta con los permisos necesarios para acceder este recurso.');
    }

    public function compraComponentes(){
        if (auth()->user()->roles()->first()->name == 'ALMACENISTA')
            return view('almacenista.compra-componentes');
        return redirect()->route('home')->with('error', 'No cuenta con los permisos necesarios para acceder este recurso.');
    }
    public function almacenMP(){
        if (auth()->user()->roles()->first()->name == 'ALMACENISTA')
            return view('almacenista.almacen-mp');
        return redirect()->route('home')->with('error', 'No cuenta con los permisos necesarios para acceder este recurso.');
    }
    public function corte(){
         if (auth()->user()->roles()->first()->name == 'ALMACENISTA')
            return view('almacenista.corte');
        return redirect()->route('home')->with('error', 'No cuenta con los permisos necesarios para acceder este recurso.');   
    }

}
