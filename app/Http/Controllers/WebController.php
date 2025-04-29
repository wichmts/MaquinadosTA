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
    public function tiemposPersonal(){
        return view('admin-panel.tiempos-personal');
    }
    public function finanzas(){
        return view('finanzas.finanzas');
    }
    public function tiemposMaquinas(){
        return view('admin-panel.tiempos-maquinas');
    }
    public function configuracion(){
        return view('admin-panel.configuracion');
    }
    public function usuarios(){
        if (auth()->user()->hasRole('DIRECCION'))
          return view('generales.usuarios');
      return redirect()->route('home')->with('error', 'No cuenta con los permisos necesarios para acceder este recurso.');
    }
    public function herramentales(){
        if(auth()->user()->hasAnyRole(['DIRECCION', 'JEFE DE AREA', 'PROCESOS', 'PROYECTOS']))
          return view('generales.herramentales');
      return redirect()->route('home')->with('error', 'No cuenta con los permisos necesarios para acceder este recurso.');
    }

    public function matricero(){
        if (auth()->user()->hasRole('MATRICERO'))
          return view('matricero.matricero');
      return redirect()->route('home')->with('error', 'No cuenta con los permisos necesarios para acceder este recurso.');
    }
    public function listaComponentesMatricero(){
        if (auth()->user()->hasRole('MATRICERO'))
          return view('matricero.lista-componentes');
      return redirect()->route('home')->with('error', 'No cuenta con los permisos necesarios para acceder este recurso.');
    }
    public function maquinas(){
        if (auth()->user()->hasRole('DIRECCION'))
          return view('generales.maquinas');
      return redirect()->route('home')->with('error', 'No cuenta con los permisos necesarios para acceder este recurso.');
    }
    public function visorProgramador(){
        if(auth()->user()->hasAnyRole(['JEFE DE AREA', 'PROGRAMADOR'])) 
            return view('programador.visor-programador');
        return redirect()->route('home')->with('error', 'No cuenta con los permisos necesarios para acceder este recurso.');
    }

    public function visorOperador(){
        if(auth()->user()->hasAnyRole(['OPERADOR', 'JEFE DE AREA'])) 
            return view('operador.visor-operador');
        return redirect()->route('home')->with('error', 'No cuenta con los permisos necesarios para acceder este recurso.');
    }

     public function enrutador(){
        if(auth()->user()->hasRole('JEFE DE AREA')) 
            return view('jefe-area.enrutador');
        return redirect()->route('home')->with('error', 'No cuenta con los permisos necesarios para acceder este recurso.');
    }

    public function visorAvanceHR(){
        if(auth()->user()->hasAnyRole(['JEFE DE AREA', 'PROGRAMADOR', 'MATRICERO', 'AUXILIAR DE DISEÑO', 'DIRECCION', 'PROCESOS', 'PROYECTOS'])) 
            return view('jefe-area.visor-avance-hr');
        return redirect()->route('home')->with('error', 'No cuenta con los permisos necesarios para acceder este recurso.');
    }

    public function visorPruebas()
    {
        if (auth()->user()->hasAnyRole(['JEFE DE AREA', 'DISEÑO']))
        return view('jefe-area.visor-pruebas');
        return redirect()->route('home')->with('error', 'No cuenta con los permisos necesarios para acceder este recurso.');
    }
    
    public function pruebasProceso(){
        if(auth()->user()->hasRole('PROCESOS')) 
            return view('procesos.pruebas-proceso');
        return redirect()->route('home')->with('error', 'No cuenta con los permisos necesarios para acceder este recurso.');
    }

    public function ordenTrabajo(){
        return view('generales.orden-trabajo');
    }
 
    public function cargaComponentes(){
        if(auth()->user()->hasRole('AUXILIAR DE DISEÑO')) 
            return view('auxiliar-diseno.carga-componentes');
        return redirect()->route('home')->with('error', 'No cuenta con los permisos necesarios para acceder este recurso.');
    }

    public function compraComponentes(){
        if(auth()->user()->hasRole('ALMACENISTA')) 
            return view('almacenista.compra-componentes');
        return redirect()->route('home')->with('error', 'No cuenta con los permisos necesarios para acceder este recurso.');
    }
     public function temple(){
        if(auth()->user()->hasRole('ALMACENISTA')) 
            return view('almacenista.temple');
        return redirect()->route('home')->with('error', 'No cuenta con los permisos necesarios para acceder este recurso.');
    }
    public function almacenMP(){
        if(auth()->user()->hasRole('ALMACENISTA')) 
            return view('almacenista.almacen-mp');
        return redirect()->route('home')->with('error', 'No cuenta con los permisos necesarios para acceder este recurso.');
    }
    public function corte(){
        if(auth()->user()->hasRole('ALMACENISTA')) 
            return view('almacenista.corte');
        return redirect()->route('home')->with('error', 'No cuenta con los permisos necesarios para acceder este recurso.');   
    }

    public function centroNotificaciones(){
        $user = auth()->user();
        $user->hay_notificaciones = false;
        $user->save();

        return view('generales.centro-notificaciones');
    }

}
