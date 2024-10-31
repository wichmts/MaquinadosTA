<?php

namespace App\Http\Controllers;

if (version_compare(PHP_VERSION, '7.2.0', '>=')) {
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}

use App\User;
use App\Documento;
use App\Anio;
use App\Cliente;
use App\Proyecto;
use App\Material;
use App\Herramental;
use App\Componente;
use App\Notificacion;
use App\Hoja;
use App\MovimientoHoja;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Facturapi\Facturapi;
use Illuminate\Support\Facades\Storage;
use App\Mail\CotizacionMailable;
use Illuminate\Support\Facades\Mail; 




class APIController extends Controller
{
    public function __construct()
    {
        Carbon::setLocale('es');
    }

    // CONSULTAS GENERALES

    public function consultarConfiguracion(){
        $configuracion = Configuracion::first();

        return response()->json([
            'configuracion' => $configuracion,
            'success' => true
        ]);
    }
    public function guardarConfiguracion(Request $request){
        $datos = $request->json()->all();
        $configuracion = Configuracion::first();
        if(!$configuracion){
            $configuracion = new Configuracion();
        }
        $configuracion->razon_social = $datos['razon_social'];
        $configuracion->rfc = $datos['rfc'];
        $configuracion->estado = $datos['estado'];
        $configuracion->ciudad = $datos['ciudad'];
        $configuracion->colonia = $datos['colonia'];
        $configuracion->calle = $datos['calle'];
        $configuracion->codigo_postal = $datos['codigo_postal'];
        $configuracion->save();
        
         return response()->json([
            'success' => true
        ]);
    }
    public function guardarConfiguracionLogo(Request $request){

        $configuracion = Configuracion::first();
        if(!$configuracion){
            $configuracion = new Configuracion();
        }
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $name = uniqid().'_'.$file->getClientOriginalName();
            Storage::disk('public')->put('logo/'.$name, \File::get($file));
            $configuracion->logo = $name;
            $configuracion->save();
        }

        return response()->json([
            'success' => true,
        ], 200);
    }
    private function getToken($email, $password){
        $token = null;
        try {
            if (!$token = \JWTAuth::attempt(['email' => $email, 'password' => $password])) {
                return response()->json([
                  'response' => 'error',
                  'message' => 'Password or email is invalid',
                  'token' => $token,
                ]);
            }
        } catch (\JWTAuthException $e) {
            return response()->json([
                'response' => 'error',
                'message' => 'Token creation failed',
            ]);
        }

        return $token;
    }
    public function download(Request $request, $folder, $filename){
        $file_path = storage_path('app/public/'.$folder.'/'.$filename);
        if (file_exists($file_path)) {
            return Response::download($file_path, $filename, [
                'Content-Length: '.filesize($file_path),
            ]);
        } else {
            exit('Requested file does not exist on our server!');
        }
    }
    public function subirDocumentos(Request $request, $modelo, $id_modelo){
        if ($request->hasFile('documentos')) {
            foreach ($request->file('documentos') as $file) {
                $name = time().'_'.$file->getClientOriginalName();
                Storage::disk('public')->put("{$modelo}/{$name}", \File::get($file));
                $d = new Documento();
                $d->modelo = $modelo;
                $d->id_modelo = $id_modelo;
                $d->nombre = $name;
                $d->usuario_id = auth()->user()->id;

                $sizeInBytes = $file->getSize();
                $sizeInMB = $sizeInBytes / (1024 * 1024);
                $formattedSizeInMB = number_format($sizeInMB, 2); // Formatear a 2 decimales
                $d->tamano = $formattedSizeInMB . ' MB'; // Guardar como string con 'MB'
                $d->save();
            }
        }
        $documentos = Documento::where('modelo', $modelo)->where('id_modelo', $id_modelo)->get();
        
        return response()->json([
            'documentos' => $documentos,
            'success' => true,
        ]);
    }
    public function eliminarDocumento($id){
        $documento = Documento::findOrFail($id);
        $modelo = $documento->modelo;
        $id_modelo = $documento->id_modelo;
        Storage::disk('public')->delete($modelo .'/'. $documento->nombre);
        $documento->delete();
        
        $documentos = Documento::where('modelo', $modelo)->where('id_modelo', $id_modelo)->get();
        return response()->json([
            'documentos' => $documentos,
            'success' => true,
        ]);
    }

        // USUARIOS
    public function consultarUsuarios(Request $request){
        $tipos = ['ADMINISTRADOR', 'VENDEDOR'];
        $tipos = $request->tipo_usuario == -1 ? ['ADMINISTRADOR', 'VENDEDOR'] : [$request->tipo_usuario];
        $usuarios = User::whereHas('roles', function ($query) use ($tipos) {
            $query->whereIn('name', $tipos);
        })->get();

        return response()->json([
          'usuarios' => $usuarios,
          'success' => true,
        ], 200);
    }
    public function guardarUsuario(Request $request){
        $datos = $request->json()->all();
        $user = User::where('email', $datos['email'])->get()->first();

        if ($user) {
            return response()->json([
                  'title' => 'Error al registrarse',
                  'message' => 'El correo electronico ya se encuentra registrado para otro usuario.',
                  'success' => false,
              ], 200);
        }

        $user = new User();
        $user->nombre = $datos['nombre'];
        $user->ap_paterno = $datos['ap_paterno'];
        $user->ap_materno = $datos['ap_materno'];
        $user->email = $datos['email'];
        $user->password = bcrypt($datos['password']);
        $user->api_token = Str::random(60);
        $user->token = '';

        if ($user->save()) {
            $token = self::getToken($datos['email'], $datos['password']);
            if (!is_string($token)) {
                return response()->json([
                  'success' => false,
                  'title' => 'Error al registrarse',
                  'message' => 'Token generation failed'], 200);
            }

            $user = User::where('email', $datos['email'])->get()->first();
            $user->token = $token;
            $user->save();
            $user->assignRole($datos['role']);
            $user->active = $datos['active'];
            $user->syncPermissions($datos['permisos']);
            $user->save();

            try {
                $user->notify(new CuentaCreada($datos['password']));
            } catch (\Throwable $th) {
            }

            return response()->json([
                'success' => true,
            ], 200);
        } else {
            return response()->json([
                'titulo' => 'Error al registrarse',
                'mensaje' => 'El registro no pudo completarse, intentelo mas tarde.',
                'success' => false,
              ], 200);
        }
    }
    public function editarUsuario(Request $request, $id){
        $datos = $request->json()->all();
        $user = User::findOrFail($id);
        $user_exist = User::where('email', $datos['email'])->get()->first();

        if ($user_exist && $id != $user_exist->id) {
            return response()->json([
                  'title' => 'Error al registrarse',
                  'message' => 'El correo electronico ya se encuentra registrado para otro usuario.',
                  'success' => false,
              ], 200);
        }

        $user->nombre = $datos['nombre'];
        $user->ap_paterno = $datos['ap_paterno'];
        $user->ap_materno = $datos['ap_materno'];
        $user->email = $datos['email'];
        $user->token = '';
        $user->active = $datos['active'];
        $user->save();
        $user->syncRoles([]);
        $user->assignRole($datos['role']);
        $user->syncPermissions($datos['permisos']);

        if ($datos['password'] && $datos['password'] != '') {
            $user->password = bcrypt($datos['password']);
            $user->save();
            $token = self::getToken($datos['email'], $datos['password']);
            if (!is_string($token)) {
                return response()->json([
                  'success' => false,
                  'title' => 'Error al registrarse',
                  'message' => 'Token generation failed'], 200);
            }

            $user->token = $token;
            $user->save();
        }

        return response()->json([
            'success' => true,
        ], 200);
    }
    public function eliminarUsuario($id){
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
          'success' => true,
        ], 200);
    }


    // CONSULTAS SIDEBAR

    public function obtenerMateriales(){
        $materiales = Material::all();

        return response()->json([
            'success' => true,
            'materiales' => $materiales
        ]);
    }

    public function obtenerAnios(){
        $anios = Anio::all();

        return response()->json([
            'success' => true,
            'anios' => $anios
        ]);
    }
    public function obtenerPorAnio($anio){
        $clientes = Cliente::where('anio_id', $anio)->get();

        return response()->json([
            'success' => true,
            'clientes' => $clientes
        ]);
    }
    public function obtenerPorCliente($cliente){
        $proyectos = Proyecto::where('cliente_id', $cliente)->get();

        return response()->json([
            'success' => true,
            'proyectos' => $proyectos
        ]);
    }
    public function obtenerPorProyecto($proyecto){
        $herramentales = Herramental::where('proyecto_id', $proyecto)->get();

        return response()->json([
            'success' => true,
            'herramentales' => $herramentales
        ]);
    }
    public function obtenerPorHerramental(Request $request, $herramental){
        $area = $request->area;

        if($area == 'cargar-componentes'){
            $componentes = Componente::where('herramental_id', $herramental)->get();
        }
        if($area == 'compras'){
            $estatus = $request->estatusCompra == '-1' ? null : $request->estatusCompra;
            $componentes = Componente::where('herramental_id', $herramental)
            ->where('es_compra', true)
            ->where('cargado', true)
            ->when($estatus, function ($query, $estatus) {
                switch ($estatus) {
                    case '1':
                        return $query->whereNull('fecha_pedido');
                    break;
                    case '2':
                        return $query->whereNull('fecha_estimada');
                    break;
                    case '3':
                        return $query->whereNull('fecha_real');
                    break;
                }
            })
            ->get();
        }
        if($area == 'corte'){
            $estatus = $request->estatusCorte == '-1' ? null : $request->estatusCorte;
            $componentes = Componente::where('herramental_id', $herramental)
            ->where('es_compra', false)
            ->where('cargado', true)
            ->when($estatus, function ($query, $estatus) {
                return $query->where('estatus_corte', $estatus);
            })
            ->get();
        }

        return response()->json([
            'success' => true,
            'componentes' => $componentes
        ]);
    }

    public function guardarAnio(Request $request){
        $datos = $request->json()->all();
        
        $anio = new Anio();
        $anio->nombre = $datos['nombre'];
        $anio->save();

        return response()->json([
            'id' => $anio->id,
            'success' => true,
        ]);
    }
    public function guardarCliente(Request $request, $anio){
        
        $datos = $request->json()->all();
        $cliente = new Cliente();
        $cliente->nombre = $datos['nombre'];
        $cliente->anio_id = $anio;
        $cliente->save();

        return response()->json([
            'id' => $cliente->id,
            'success' => true,
        ]);
    }
    
    public function guardarProyecto(Request $request, $cliente){
        $datos = $request->json()->all();
        $proyecto = new Proyecto();
        $proyecto->nombre = $datos['nombre'];
        $proyecto->cliente_id = $cliente;
        $proyecto->save();
        return response()->json([
            'id' => $proyecto->id,
            'success' => true,
        ]);
    }


    public function guardarHerramental(Request $request, $proyecto_id){

        $ultimo = Herramental::where('proyecto_id', $proyecto_id)->latest('id')->first();
        $siguiente = $ultimo ? intval(substr($ultimo->nombre, 2)) + 1 : 1;
        $siguiente = 'HR' . str_pad($siguiente, 2, "0", STR_PAD_LEFT);

        $herramental = new Herramental();
        $herramental->nombre = $siguiente;
        $herramental->proyecto_id = $proyecto_id;
        $herramental->save();

         if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $name = uniqid().'_'.$file->getClientOriginalName();
            Storage::disk('public')->put($proyecto_id . '/' . $herramental->id . '/formato/' . $name, \File::get($file));
            $herramental->archivo = $name;
            $herramental->save();
        }

        return response()->json([
            'id' => $herramental->id,
            'success' => true,
        ], 200);

    }
    public function emptyToNull($value) {
        return $value === '' ? null : $value;
    }
    public function guardarComponentesCompras(Request $request, $herramental_id){

        $herramental = Herramental::findOrFail($herramental_id);
        $proyecto = Proyecto::findOrFail($herramental->proyecto_id);
        $cliente = Cliente::findOrFail($proyecto->cliente_id);
        $anio = Anio::findOrFail($cliente->anio_id);
        

        $componentes = json_decode($request->data, true);
        
        foreach ($componentes as $index => $componente) {

            $nuevoComponente = Componente::findOrFail($componente['id']);
            $comprado = $nuevoComponente->comprado;

            $nuevoComponente->cantidad = $this->emptyToNull($componente['cantidad']);
            $nuevoComponente->material_id = $this->emptyToNull($componente['material_id']);
            $nuevoComponente->fecha_solicitud = $this->emptyToNull($componente['fecha_solicitud']);
            $nuevoComponente->fecha_pedido = $this->emptyToNull($componente['fecha_pedido']);
            $nuevoComponente->fecha_estimada = $this->emptyToNull($componente['fecha_estimada']);
            $nuevoComponente->fecha_real = $this->emptyToNull($componente['fecha_real']);
            $nuevoComponente->comprado = $this->emptyToNull($componente['fecha_real']) != null;
            $nuevoComponente->save();
            
            if(!$comprado && $nuevoComponente->comprado){
                $nuevoComponente->area ='ensamble';
                $nuevoComponente->save();

                $notificacion = new Notificacion();
                $notificacion->roles = json_encode(['MATRICERO']);
                $notificacion->url_base = '/ensamble';
                $notificacion->anio_id = $anio->id;
                $notificacion->cliente_id = $cliente->id;
                $notificacion->proyecto_id = $proyecto->id;
                $notificacion->herramental_id = $herramental->id;
                $notificacion->componente_id = $nuevoComponente->id;
                $notificacion->cantidad = $nuevoComponente->cantidad;
                $notificacion->descripcion = 'COMPONENTE LISTO PARA ENSAMBLE';
                $notificacion->save();
            }
        }

        return response()->json([
            'success' => true,
        ], 200);
    }
    public function guardarComponentes(Request $request, $herramental_id){
        $herramental = Herramental::findOrFail($herramental_id);
        $componentes = json_decode($request->data, true);

        $componentesExistentes = Componente::where('herramental_id', $herramental_id)->pluck('id')->toArray();

        $idsEnviados = [];
        foreach ($componentes as $index => $componente) {
            if (isset($componente['id'])) {
                $nuevoComponente = Componente::findOrFail($componente['id']);
                $idsEnviados[] = $componente['id'];  // Agregar ID al array
            } else {
                $nuevoComponente = new Componente();
            }

            if(!$nuevoComponente->cargado){
                $nuevoComponente->nombre = $this->emptyToNull($componente['nombre']);
                $nuevoComponente->largo = $this->emptyToNull($componente['largo']);
                $nuevoComponente->ancho = $this->emptyToNull($componente['ancho']);
                $nuevoComponente->alto = $this->emptyToNull($componente['alto']);
                $nuevoComponente->es_compra = $this->emptyToNull($componente['es_compra']);
                $nuevoComponente->cantidad = $this->emptyToNull($componente['cantidad']);
                $nuevoComponente->material_id = $this->emptyToNull($componente['material_id']);
                $nuevoComponente->fecha_solicitud = $this->emptyToNull($componente['fecha_solicitud']);
                $nuevoComponente->fecha_pedido = $this->emptyToNull($componente['fecha_pedido']);
                $nuevoComponente->fecha_estimada = $this->emptyToNull($componente['fecha_estimada']);
                $nuevoComponente->fecha_real = $this->emptyToNull($componente['fecha_real']);
    
                $nuevoComponente->herramental_id = $herramental_id;
                $nuevoComponente->area = 'diseno-carga';
                $nuevoComponente->estatus_corte = 'inicial';
                $nuevoComponente->cargado = false;
                $nuevoComponente->comprado = false;
                $nuevoComponente->cortado = false;
                $nuevoComponente->ensamblado = false;
    
                if ($request->hasFile("files.{$index}.vista2D")) {
                    if ($nuevoComponente->archivo_2d)
                        Storage::disk('public')->delete("{$herramental->proyecto_id}/{$herramental->id}/componentes/{$nuevoComponente->archivo_2d}");
                    $file2D = $request->file("files.{$index}.vista2D");
                    $name2D = uniqid() . '_' . $file2D->getClientOriginalName();
                    Storage::disk('public')->put("{$herramental->proyecto_id}/{$herramental->id}/componentes/{$name2D}", \File::get($file2D));
                    $nuevoComponente->archivo_2d = $name2D;
                }
    
                if ($request->hasFile("files.{$index}.vista3D")) {
                    if ($nuevoComponente->archivo_3d)
                        Storage::disk('public')->delete("{$herramental->proyecto_id}/{$herramental->id}/componentes/{$nuevoComponente->archivo_3d}");
                    $file3D = $request->file("files.{$index}.vista3D");
                    $name3D = uniqid() . '_' . $file3D->getClientOriginalName();
                    Storage::disk('public')->put("{$herramental->proyecto_id}/{$herramental->id}/componentes/{$name3D}", \File::get($file3D));
                    $nuevoComponente->archivo_3d = $name3D;
                }
    
                if ($request->hasFile("files.{$index}.vistaExplosionada")) {
                    if ($nuevoComponente->archivo_explosionado)
                        Storage::disk('public')->delete("{$herramental->proyecto_id}/{$herramental->id}/componentes/{$nuevoComponente->archivo_explosionado}");
                    $fileExplosionada = $request->file("files.{$index}.vistaExplosionada");
                    $nameExplosionada = uniqid() . '_' . $fileExplosionada->getClientOriginalName();
                    Storage::disk('public')->put("{$herramental->proyecto_id}/{$herramental->id}/componentes/{$nameExplosionada}", \File::get($fileExplosionada));
                    $nuevoComponente->archivo_explosionado = $nameExplosionada;
                }
    
                $nuevoComponente->save();
            }

        }

        $componentesEliminados = array_diff($componentesExistentes, $idsEnviados);

        foreach ($componentesEliminados as $componenteId) {
            $componenteAEliminar = Componente::find($componenteId);
            if ($componenteAEliminar) {
                if ($componenteAEliminar->archivo_2d)
                    Storage::disk('public')->delete("{$herramental->proyecto_id}/{$herramental->id}/componentes/{$componenteAEliminar->archivo_2d}");
                if ($componenteAEliminar->archivo_3d)
                    Storage::disk('public')->delete("{$herramental->proyecto_id}/{$herramental->id}/componentes/{$componenteAEliminar->archivo_3d}");
                if ($componenteAEliminar->archivo_explosionado)
                    Storage::disk('public')->delete("{$herramental->proyecto_id}/{$herramental->id}/componentes/{$componenteAEliminar->archivo_explosionado}");
                $componenteAEliminar->delete();
            }
        }

        return response()->json([
            'success' => true,
        ], 200);
    }

    public function liberarHerramentalCargar($herramental_id){
        $herramental = Herramental::findOrFail($herramental_id);
        $proyecto = Proyecto::findOrFail($herramental->proyecto_id);
        $cliente = Cliente::findOrFail($proyecto->cliente_id);
        $anio = Anio::findOrFail($cliente->anio_id);
        
        $componentes = Componente::where('herramental_id', $herramental_id)->where('cargado', false)->get();

        if(count($componentes) > 0){
            $conteoCompras = 0;
            $conteoCortes = 0;

            foreach ($componentes as $componente) {
                if($componente->es_compra): $conteoCompras+=1; endif; 
                if(!$componente->es_compra): $conteoCortes+=1; endif; 

                $componente->area = $componente->es_compra ? 'compras' : 'corte';
                $componente->cargado = true;
                $componente->fecha_solicitud = $componente->es_compra ? date('Y-m-d') : null;
                $componente->save();
            }

            if($conteoCompras > 0){
                $notificacion = new Notificacion();
                $notificacion->roles = json_encode(['ALMACENISTA']);
                $notificacion->url_base = '/compra-componentes';
                $notificacion->anio_id = $anio->id;
                $notificacion->cliente_id = $cliente->id;
                $notificacion->proyecto_id = $proyecto->id;
                $notificacion->herramental_id = $herramental->id;
                $notificacion->componente_id = null;
                $notificacion->cantidad = null;
                $notificacion->descripcion = $conteoCompras . ' COMPONENTES HAN SIDO LIBERADOS PARA COMPRA';
                $notificacion->save();
            }
            if($conteoCortes > 0){
                $notificacion = new Notificacion();
                $notificacion->roles = json_encode(['ALMACENISTA']);
                $notificacion->url_base = '/corte';
                $notificacion->anio_id = $anio->id;
                $notificacion->cliente_id = $cliente->id;
                $notificacion->proyecto_id = $proyecto->id;
                $notificacion->herramental_id = $herramental->id;
                $notificacion->componente_id = null;
                $notificacion->cantidad = null;
                $notificacion->descripcion = $conteoCortes . ' COMPONENTES HAN SIDO LIBERADOS PARA CORTE';
                $notificacion->save();
            }

            return response()->json([
                'success' => true,
            ], 200);
        }

        return response()->json([
                'success' => false,
                'message' => 'No hay componentes para liberar.'
        ], 200);

    }
    public function cancelarComponenteCargar(Request $request, $componenteId){
        $componente = Componente::findOrFail($componenteId);
        $herramental = Herramental::findOrFail($componente->herramental_id);
        $proyecto = Proyecto::findOrFail($herramental->proyecto_id);
        $cliente = Cliente::findOrFail($proyecto->cliente_id);
        $anio = Anio::findOrFail($cliente->anio_id);

        $componente->cancelado = true;
        $componente->save();

        if($componente->area == 'corte' || $componente->area == 'compras'){
            $notificacion = new Notificacion();
            $notificacion->roles = json_encode(['ALMACENISTA']);
            $notificacion->url_base = $componente->area == 'corte' ? '/corte' : '/compra-componentes';
            $notificacion->anio_id = $anio->id;
            $notificacion->cliente_id = $cliente->id;
            $notificacion->proyecto_id = $proyecto->id;
            $notificacion->herramental_id = $herramental->id;
            $notificacion->componente_id = $componente->id;
            $notificacion->cantidad = $componente->cantidad;
            $notificacion->descripcion = 'COMPONENTE CANCELADO';
            $notificacion->save();
        }

        return response()->json([
            'success' => true,
        ], 200);
    }
    public function liberarComponenteCargar(Request $request, $herramental_id){
        $nombre = $request->componente;
        
        $componente = Componente::where('herramental_id', $herramental_id)->where('nombre', $nombre)->first();
        $herramental = Herramental::findOrFail($herramental_id);
        $proyecto = Proyecto::findOrFail($herramental->proyecto_id);
        $cliente = Cliente::findOrFail($proyecto->cliente_id);
        $anio = Anio::findOrFail($cliente->anio_id);


        $componente->area = $componente->es_compra ? 'compras' : 'corte';
        $componente->cargado = true;
        $componente->fecha_solicitud = $componente->es_compra ? date('Y-m-d') : null;
        $componente->save();

        $notificacion = new Notificacion();
        $notificacion->roles = json_encode(['ALMACENISTA']);
        $notificacion->url_base = $componente->es_compra ? '/compra-componentes' : '/corte';
        $notificacion->anio_id = $anio->id;
        $notificacion->cliente_id = $cliente->id;
        $notificacion->proyecto_id = $proyecto->id;
        $notificacion->herramental_id = $herramental->id;
        $notificacion->componente_id = $componente->id;
        $notificacion->cantidad = $componente->cantidad;
        $notificacion->descripcion = $componente->es_compra ? 'COMPONENTE LIBERADO PARA COMPRA' : 'COMPONENTE LIBERADO PARA CORTE';
        $notificacion->save();

        return response()->json([
            'success' => true,
        ], 200);
    
    }
    // public function liberarHerramentalCompras($herramental_id){
    //     $herramental = Herramental::findOrFail($herramental_id);
    //     $componentes = Componente::where('herramental_id', $herramental_id)->where('es_compra', true)->get();

    //     foreach ($componentes as $componente) {
    //         $componente->area ='ensamble';
    //         $componente->comprado = true;
    //         $componente->save();

    //         $notificacion = new Notificacion();
    //         $notificacion->roles = json_encode(['MATRICERO']);
    //         $notificacion->herramental_id = null;
    //         $notificacion->componente_id = $componente->id;
    //         $notificacion->cantidad = $componente->cantidad;
    //         $notificacion->descripcion = 'LISTO PARA ENSAMBLE';
    //         $notificacion->save();
    //     }

    //     return response()->json([
    //         'success' => true,
    //     ], 200);
    
    // }
    public function ultimasNotificaciones() {
        $role = auth()->user()->roles()->first()->name; 
        $notificaciones = Notificacion::where('roles', 'LIKE', '%"'.$role.'"%')->latest('id')->limit(6)->get(); 

        return response()->json([
            'notificaciones' => $notificaciones,
            'success' => true,
        ], 200);
    }

    public function notificaciones() {
        $role = auth()->user()->roles()->first()->name; 
        $notificaciones = Notificacion::where('roles', 'LIKE', '%"'.$role.'"%')->latest('id')->get(); 

        return response()->json([
            'notificaciones' => $notificaciones,
            'success' => true,
        ], 200);
    }
    public function bajaHoja(Request $request, $hoja_id, $estatus){
        $hoja = Hoja::findOrFail($hoja_id);
        $hoja->estatus = filter_var($estatus, FILTER_VALIDATE_BOOLEAN);;
        $hoja->save();
        
        return response()->json([
            'success' => true,
        ], 200);
    } 
    public function obtenerHojas(Request $request, $material_id){
        $estatus = $request->estatus == '-1' ? null : $request->estatus;


        $hojas = Hoja::where('material_id', $material_id)
        ->when($estatus, function ($query, $estatus) {
            return $query->where('estatus', $estatus == 'activo');
        })
        ->orderBy('id', 'DESC')
        ->get();
        
        return response()->json([
            'hojas' => $hojas,
            'success' => true,
        ], 200);
    }
    public function obtenerMovimientosHoja(Request $request, $hoja_id){
        $movimientos = MovimientoHoja::where('hoja_id', $hoja_id)->get();
        return response()->json([
            'movimientos' => $movimientos,
            'success' => true,
        ], 200);

    }
    public function guardarHoja(Request $request){
        $data = json_decode($request->data, true);

        $ultimo = Hoja::where('material_id', $data['material_id'])->latest('id')->first();
        $siguiente = $ultimo ? intval($ultimo->consecutivo) + 1 : 1;

        $hoja = new Hoja();
        $hoja->consecutivo = $siguiente;
        $hoja->calidad = $data['calidad'];
        $hoja->espesor = $data['espesor'];
        $hoja->largo_entrada = $data['largo_entrada'];
        $hoja->ancho_entrada = $data['ancho_entrada'];
        $hoja->peso_entrada = $data['peso_entrada'];
        $hoja->largo_saldo = $data['largo_entrada'];
        $hoja->ancho_saldo = $data['ancho_entrada'];
        $hoja->peso_saldo = $data['peso_entrada'];
        $hoja->precio_kilo = $data['precio_kilo'];
        $hoja->fecha_entrada = $data['fecha_entrada'];
        $hoja->fecha_salida = $data['fecha_salida']??null;
        $hoja->material_id = $data['material_id'];
        $hoja->estatus = true;

        if ($request->hasFile('factura')) {
            $file = $request->file('factura');
            $name = uniqid().'_'.$file->getClientOriginalName();
            Storage::disk('public')->put('facturas/' . $name, \File::get($file));
            $hoja->factura = $name;
        }
        $hoja->save();

        return response()->json([
            'success' => true,
        ], 200);

    }
    public function cambiarEstatusCorte(Request $request, $id){
        $componente = Componente::findOrFail($id);
        $componente->estatus_corte = $request->estatus;
        $componente->save();

        return response()->json([
            'success' => true,
        ], 200);
    }

    public function finalizarCorte(Request $request, $id){
        $componente = Componente::findOrFail($id);
        $herramental = Herramental::findOrFail($componente->herramental_id);
        
        $componente->estatus_corte = 'finalizado';
        $componente->cortado = true;
        $componente->save();

        $hoja = Hoja::findOrFail($request->movimiento['hoja_id']);
        $hoja->peso_saldo = $request->movimiento['peso'];
        $hoja->largo_saldo = $request->movimiento['largo'];
        $hoja->ancho_saldo = $request->movimiento['ancho'];
        $hoja->save();

        $movimiento = new MovimientoHoja();
        $movimiento->largo = $request->movimiento['largo'];
        $movimiento->ancho = $request->movimiento['ancho'];
        $movimiento->peso = $request->movimiento['peso'];
        $movimiento->hoja_id = $request->movimiento['hoja_id'];
        $movimiento->proyecto_id = $herramental->proyecto_id;
        $movimiento->componente_id = $componente->id;
        $movimiento->save();

        return response()->json([
            'success' => true,
        ], 200);
    }


}
