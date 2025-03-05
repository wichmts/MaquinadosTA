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
use App\Maquina;
use App\Herramental;
use App\Componente;
use App\Notificacion;
use App\Fabricacion;
use App\PruebaDiseno;
use App\PruebaProceso;
use App\Hoja;
use App\MovimientoHoja;
use App\SeguimientoTiempo;
use App\Solicitud;
use App\SolicitudExterna;
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
    public function __construct(){
        Carbon::setLocale('es');
    }    
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
    private function getToken($email, $codigo_acceso){
        $token = null;
        try {
            if (!$token = \JWTAuth::attempt(['email' => $email, 'password' => $codigo_acceso])) {
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
    public function consultarUsuarios(Request $request){
        $tipos = [ 'DIRECCION', 'ALMACENISTA', 'AUXILIAR DE DISEÑO', 'JEFE DE AREA', 'PROGRAMADOR', 'OPERADOR', 'MATRICERO', 'FINANZAS', 'PROYECTOS', 'PROCESOS', 'EXTERNO', 'DISEÑO'];
        $tipos = $request->tipo_usuario == -1 ? $tipos : [$request->tipo_usuario];
        $usuarios = User::whereHas('roles', function ($query) use ($tipos) {
            $query->whereIn('name', $tipos);
        })->get();

        return response()->json([
          'usuarios' => $usuarios,
          'success' => true,
        ], 200);
    }
    public function guardarUsuario(Request $request) {
        $datos = $request->json()->all();

        $user = User::where('email', $datos['email'])->orWhere('codigo_acceso', $datos['codigo_acceso'])->first();

        if ($user) {
            return response()->json([
                'title' => 'Error al registrarse',
                'message' => 'El correo electrónico o el código de acceso ya están registrados.',
                'success' => false,
            ], 200);
        }
    

        $user = new User();
        $user->nombre = $datos['nombre'];
        $user->ap_paterno = $datos['ap_paterno'];
        $user->ap_materno = $datos['ap_materno'];
        $user->email = $datos['email'];
        $user->codigo_acceso = $datos['codigo_acceso'];
        $user->maquinas = json_encode($datos['maquinas']);
        $user->active = $datos['active'];
        $user->api_token = Str::random(60);
        $user->save();
        $user->syncRoles($datos['roles'] ?? []);
        $user->syncPermissions($datos['permisos'] ?? []);

        return response()->json([
            'success' => true,
            'message' => 'Usuario registrado exitosamente',
        ], 200);
    }
    public function editarUsuario(Request $request, $id) {
        $datos = $request->json()->all();
        $user = User::findOrFail($id);
        $user_exist = User::where('email', $datos['email'])
                        ->where('id', '!=', $id)
                        ->first();

        if ($user_exist) {
            return response()->json([
                'title' => 'Error al actualizar',
                'message' => 'El correo electrónico ya está registrado para otro usuario.',
                'success' => false,
            ], 200);
        }

        $codigo_exist = User::where('codigo_acceso', $datos['codigo_acceso'])
                            ->where('id', '!=', $id)
                            ->first();
        if ($codigo_exist) {
            return response()->json([
                'title' => 'Error al actualizar',
                'message' => 'El código de acceso ya está registrado para otro usuario.',
                'success' => false,
            ], 200);
        }
        $nombreAntiguoProyecto = $user->id . '.' . $user->nombre_completo;

        $user->nombre = $datos['nombre'];
        $user->ap_paterno = $datos['ap_paterno'];
        $user->ap_materno = $datos['ap_materno'];
        $user->email = $datos['email'];
        $user->codigo_acceso = $datos['codigo_acceso'];
        $user->active = $datos['active'];
        $user->maquinas = json_encode($datos['maquinas']);
        $user->save();

        $nuevoNombreProyecto = $user->id . '.' . $user->nombre_completo;
        $proyecto = Proyecto::where('nombre', $nombreAntiguoProyecto)->first();
        if ($proyecto) {
            $proyecto->nombre = $nuevoNombreProyecto;
            $proyecto->save();
        }


        $user->syncRoles($datos['roles'] ?? []);
        $user->syncPermissions($datos['permisos'] ?? []);

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado correctamente',
        ], 200);
    }
    public function eliminarUsuario($id){
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
          'success' => true,
        ], 200);
    }
    public function obtenerMateriales(){
        $materiales = Material::all();

        return response()->json([
            'success' => true,
            'materiales' => $materiales
        ]);
    }
    public function obtenerMaquinas(Request $request){
        $usuario_id = $request->operador??null;
        if($usuario_id){
            $usuario = User::findOrFail($usuario_id);   
            $maquinas = Maquina::whereIn('id', $usuario->maquinas ? json_decode($usuario->maquinas) : [])->get();
        }else{
            $maquinas = Maquina::all();
        }
        return response()->json([
            'success' => true,
            'maquinas' => $maquinas
        ]);
    }
    public function guardarMaquina(Request $request){
        $datos = $request->json()->all();

        $maquina = new Maquina();
        $maquina->nombre = $datos['nombre'];
        $maquina->tipo_proceso = $datos['tipo_proceso'];
        $maquina->save();

        return response()->json([
            'success' => true,
            'message' => 'Máquina guardada correctamente',
        ], 200);
    }
    public function editarMaquina(Request $request, $id){
        $datos = $request->json()->all();
        $maquina = Maquina::findOrFail($id);

        $maquina->nombre = $datos['nombre'];
        $maquina->tipo_proceso = $datos['tipo_proceso'];
        $maquina->save();

        return response()->json([
            'success' => true,
            'message' => 'Máquina actualizada correctamente',
        ], 200);
    }
    public function eliminarMaquina($id) {
        $maquina = Maquina::findOrFail($id);
        $maquina->delete();

        return response()->json([
            'success' => true,
            'message' => 'Máquina eliminada correctamente',
        ], 200);
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
    public function obtenerPorProyecto(Request $request, $proyecto){
        $band = isset($request->area);
        if($band)
            $herramentales = Herramental::where('proyecto_id', $proyecto)->where('estatus_ensamble', '!=', 'finalizado')->get();
        else
            $herramentales = Herramental::where('proyecto_id', $proyecto)->get();

        return response()->json([
            'success' => true,
            'herramentales' => $herramentales
        ]);
    }
    public function obtenerProgramadores(){
        $programadores = User::whereHas('roles', function ($query) {
            $query->where('name', 'PROGRAMADOR');
        })->get();
        
        return response()->json([
            'success' => true,
            'programadores' => $programadores
        ]);

    }
    public function obtenerComponente($componente_id){
        $componente = Componente::findOrFail($componente_id);
        
        return response()->json([
            'success' => true,
            'componente' => $componente
        ]);
    }
    public function obtenerComponentesMaquina($maquina_id){
        $componentes = Componente::whereHas('fabricaciones', function ($query) use ($maquina_id) {
        $query->where('maquina_id', $maquina_id)
                ->where('fabricado', false)
                ->whereColumn('orden', '=', 'componentes.estatus_fabricacion'); // Validar que el orden coincide
        })
        ->where('cargado', true)
        ->where('enrutado', true)
        ->where('programado', true)
        ->where('cortado', true)
        ->where('refabricado', '!=', true)
        ->where(function ($query) {
              $query->where(function ($query) {
                    $query->where('requiere_temple', false)
                        ->orWhereNull('requiere_temple'); // Agregar la validación de null
                })
                ->orWhere(function ($query) {
                    $query->where('requiere_temple', true)
                        ->whereNotNull('fecha_solicitud_temple') // Tiene fecha de solicitud de temple
                        ->whereNotNull('fecha_recibido_temple'); // Y ya fue recibido
                })
                ->orWhere(function ($query) {
                    $query->where('requiere_temple', true) // Requiere temple
                        ->whereNull('fecha_solicitud_temple') // Pero no tiene fecha de solicitud
                        ->whereNull('fecha_recibido_temple'); // Ni fecha de recibido
                });
        })
        ->get();


        $componentes->each(function ($componente) use ($maquina_id) {
            $componente->fabricaciones = $componente->fabricaciones()
                ->where('maquina_id', $maquina_id)
                ->where('fabricado', false)
                ->where('orden', $componente->estatus_fabricacion) // Comparar con estatus_fabricacion del componente
                ->orderBy('orden', 'asc')
                ->get();
        });

        return response()->json([
            'success' => true,
            'componentes' => $componentes
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
        if($area == 'temple'){
            $estatus = $request->estatusTemple == '-1' ? null : $request->estatusTemple;
            $componentes = Componente::where('herramental_id', $herramental)
            ->where('requiere_temple', true)
            ->whereNotNull('fecha_solicitud_temple')
            ->when($estatus, function ($query, $estatus) {
                switch ($estatus) {
                    case '1':
                        return $query->whereNull('fecha_envio_temple');
                    break;
                    case '2':
                        return $query->whereNull('fecha_estimada_temple');
                    break;
                    case '3':
                        return $query->whereNull('fecha_recibido_temple');
                    break;
                }
            })
            ->get();
        }
        if($area == 'enrutador'){
            $componentes = Componente::where('herramental_id', $herramental)
            ->where('cargado', true)
            ->where('es_compra', false)
            ->orderBy('nombre', 'asc')
            ->get();

        }
        if($area == 'corte'){
            $estatus = $request->estatusCorte == '-1' ? null : $request->estatusCorte;
            $componentes = Componente::where('herramental_id', $herramental)
            ->where('es_compra', false)
            ->where('cargado', true)
            ->where('enrutado', true)
            ->where('refabricado', '!=', true)
            ->when($estatus, function ($query, $estatus) {
                return $query->where('estatus_corte', $estatus);
            })
            ->get();
        }
        if($area == 'programador'){
            $componentes = Componente::where('herramental_id', $herramental)
            ->where('cargado', true)
            ->where('enrutado', true)
            ->where('es_compra', false)
            ->where('refabricado', '!=', true)
            ->when(!auth()->user()->hasRole('JEFE DE AREA'), function($query) {
                $query->where('programador_id', auth()->user()->id);
            })
            ->orderBy('nombre', 'asc')
            ->get();
        }
        if($area == 'ensamble' || $area == 'pruebas'){
            $componentes = Componente::where('herramental_id', $herramental)
            ->where('refabricado', false)
            ->where('cargado', true)
            ->orderBy('nombre', 'asc')
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
        $herramental->estatus_ensamble = 'inicial';
        $herramental->estatus_pruebas_diseno = 'inicial';
        $herramental->estatus_pruebas_proceso = 'inicial';
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
    public function obtenerHerramental($id){
        $herramental = Herramental::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'herramental' => $herramental
        ]);
    }
    public function actualizarHerramental(Request $request, $id, $tipo) {
        $datos = $request->json()->all();
        
        switch ($tipo) {
            case 'formato':
                $herramental = Herramental::findOrFail($id);
                if ($request->hasFile('archivo')) {
                    $file = $request->file('archivo');
                    $name = uniqid().'_'.$file->getClientOriginalName();
                    Storage::disk('public')->put($herramental->proyecto_id . '/' . $herramental->id . '/formato2/' . $name, \File::get($file));
                    $herramental->archivo2 = $name;
                    $herramental->estatus_ensamble = 'checklist';
                    $herramental->save();
                }else{
                    return response()->json([
                        'success' => false,
                        'message' => 'Herramental no se acualizo correctamente',
                    ], 200);
                }
            break;
            case 'checklist':
                $herramental = Herramental::findOrFail($id);
                $checklist = $datos;
                $allChecked = true;

                foreach ($checklist as $item) {
                    if (!$item['checked']) {
                        $allChecked = false;
                        break;
                    }
                }
                $herramental->checklist = json_encode($checklist);
                if ($allChecked)
                    $herramental->estatus_ensamble = 'proceso';
                $herramental->save();
            break;
            case 'foto':
                $componente = Componente::findOrFail($id);
                if ($request->hasFile('foto')) {
                    if ($componente->foto_matricero) {
                        Storage::disk('public')->delete('fotos_matricero/' . $componente->foto_matricero);
                    }
                    $file = $request->file('foto');
                    $name = uniqid().'_'.$file->getClientOriginalName();
                    Storage::disk('public')->put('fotos_matricero/' . $name, \File::get($file));
                    $componente->foto_matricero = $name;
                    $componente->save();
                }
            break;
            case 'estatus-ensamblado':
                 $componente = Componente::findOrFail($id);
                $componente->ensamblado = $datos['ensamblado'];
                if($datos['ensamblado']){
                    $componente->fecha_ensamblado = date('Y-m-d H:i');
                }else{
                    $componente->fecha_ensamblado = null;
                }
                $componente->save();
            break;
        }
        return response()->json([
            'success' => true,
            'message' => 'Herramental actualizado correctamente',
        ], 200);
    }
    public function emptyToNull($value) {
        return $value === '' ? null : $value;
    }
    public function guardarComponenteFabricacion(Request $request, $fabricacion_id, $liberar){
        $liberar = filter_var($liberar, FILTER_VALIDATE_BOOLEAN);

        $data = json_decode($request->data, true);

        $fabricacion = Fabricacion::findOrFail($fabricacion_id);
        $componente = Componente::findOrFail($fabricacion->componente_id);
        $fabricacion->comentarios_terminado = $data['comentarios_terminado'];
        $fabricacion->registro_medidas = $data['registro_medidas'];
        $fabricacion->checklist_fabricadas = json_encode($data['checklist_fabricadas']);
        $fabricacion->save();
        
        $archivo = $request->file('fotografia');
        if ($archivo) {
            $name = time() . '_' . $archivo->getClientOriginalName();
            Storage::disk('public')->put("fabricaciones/" . $name, \File::get($archivo));
            $fabricacion->foto = $name;
            $fabricacion->save();
        }

        
        if ($liberar) {
            $procesos = [
                ['id' => 1, 'prioridad' => 1, 'nombre' => 'Cortar'],
                ['id' => 2, 'prioridad' => 2, 'nombre' => 'Programar'],
                ['id' => 3, 'prioridad' => 3, 'nombre' => 'Maquinar'],
                ['id' => 4, 'prioridad' => 4, 'nombre' => 'Tornear'],
                ['id' => 5, 'prioridad' => 5, 'nombre' => 'Roscar/Rebabear'],
                ['id' => 6, 'prioridad' => 6, 'nombre' => 'Templar'],
                ['id' => 7, 'prioridad' => 7, 'nombre' => 'Rectificar'],
                ['id' => 8, 'prioridad' => 8, 'nombre' => 'EDM']
            ];

            $fabricacion->motivo_retraso = $data['motivo_retraso'];
            $fabricacion->estatus_fabricacion = 'detenido';
            $fabricacion->fabricado = true;
            $fabricacion->usuario_id = auth()->user()->id;
            $fabricacion->save();

            $componente->estatus_fabricacion += 1;
            $componente->save();

            $fabricacionesPendientes = Fabricacion::where('componente_id', $componente->id)
                ->where('fabricado', false)
                ->get();

            $herramental = Herramental::findOrFail($componente->herramental_id);
            $proyecto = Proyecto::findOrFail($herramental->proyecto_id);
            $cliente = Cliente::findOrFail($proyecto->cliente_id);
            $anio = Anio::findOrFail($cliente->anio_id);



            // Si ya no hay fabricaciones pendientes
            if ($fabricacionesPendientes->isEmpty()) {
                if ($componente->requiere_temple && !$componente->fecha_recibido_temple) { //si requiere temple y no ha sido realizado el temple     
                    $procesoActual = collect($procesos)->firstWhere('id', $fabricacion->maquina->tipo_proceso);
                    $templeProceso = collect($procesos)->firstWhere('nombre', 'Templar');
                    
                    if ($templeProceso && $procesoActual && $procesoActual['prioridad'] < $templeProceso['prioridad'] ) {
                        $componente->fecha_solicitud_temple = date('Y-m-d');
                        $componente->save();

                        $notificacionAlmacen = new Notificacion();
                        $notificacionAlmacen->roles = json_encode(['ALMACENISTA']);
                        $notificacionAlmacen->url_base = '/temple';
                        $notificacionAlmacen->anio_id = $anio->id;
                        $notificacionAlmacen->cliente_id = $cliente->id;
                        $notificacionAlmacen->proyecto_id = $proyecto->id;
                        $notificacionAlmacen->herramental_id = $herramental->id;
                        $notificacionAlmacen->componente_id = $componente->id;
                        $notificacionAlmacen->descripcion = 'UN COMPONENTE REQUIERE TEMPLE';
                        $notificacionAlmacen->save();

                        $usuariosAlmacen = User::role('ALMACENISTA')->get();
                        foreach ($usuariosAlmacen as $usuario) {
                            $usuario->hay_notificaciones = true;
                            $usuario->save();
                        }
                    }
                }else{
                    if($componente->esComponenteExterno()){
                        
                        $componente->fecha_terminado = date('Y-m-d H:i');
                        $componente->save();

                        $solicitud = SolicitudExterna::where('componente_id', $componente->id)->first();
                        $solicitud->fecha_real_entrega = date('Y-m-d');
                        $solicitud->save();
                        
                        $user = User::findOrFail($solicitud->solicitante_id);
                        $user->hay_notificaciones = true;
                        $user->save();
                        $rolesSolicitante = User::findOrFail($solicitud->solicitante_id)->roles->pluck('name')->toArray();


                        $notificacion = new Notificacion();
                        $notificacion->url_base = '/orden-trabajo';
                        $notificacion->roles = json_encode($rolesSolicitante, JSON_UNESCAPED_UNICODE);
                        $notificacion->anio_id = $anio->id;
                        $notificacion->cliente_id = $cliente->id;
                        $notificacion->proyecto_id = $proyecto->id;
                        $notificacion->herramental_id = $herramental->id;
                        $notificacion->componente_id = $componente->id;
                        $notificacion->responsables = json_encode([$solicitud->solicitante_id]);
                        $notificacion->descripcion = 'EL COMPONENTE EXTERNO ESTÁ LISTO';
                        $notificacion->save();
                        

                    }else{
                        $notificacion = new Notificacion();
                        $notificacion->roles = json_encode(['MATRICERO']);
                        $notificacion->url_base = '/matricero/lista-componentes';
                        $notificacion->anio_id = $anio->id;
                        $notificacion->cliente_id = $cliente->id;
                        $notificacion->proyecto_id = $proyecto->id;
                        $notificacion->herramental_id = $herramental->id;
                        $notificacion->componente_id = $componente->id;
                        $notificacion->descripcion = 'COMPONENTE LISTO PARA ENSAMBLE';
                        $notificacion->save();
        
                        $componente->fecha_terminado = date('Y-m-d H:i');
                        $componente->save();
        
                        $users = User::role('MATRICERO')->get();
                        foreach ($users as $user) {
                            $user->hay_notificaciones = true;
                            $user->save();
                        }
        
                        $herramentalListo = true;
                        $componentes = Componente::where('herramental_id', $herramental->id)->where('refabricado', false)->with('fabricaciones')->get();
                        foreach ($componentes as $comp) {
                            foreach ($comp->fabricaciones as $fabriComp) {
                                if (!$fabriComp->fabricado) {
                                    $herramentalListo = false;
                                    break 2;
                                }
                            }
                        }
        
                        if ($herramentalListo) {
                            $notificacionHerramental = new Notificacion();
                            $notificacionHerramental->roles = json_encode(['MATRICERO']);
                            $notificacionHerramental->url_base = '/matricero';
                            $notificacionHerramental->anio_id = $anio->id;
                            $notificacionHerramental->cliente_id = $cliente->id;
                            $notificacionHerramental->proyecto_id = $proyecto->id;
                            $notificacionHerramental->herramental_id = $herramental->id;
                            $notificacionHerramental->descripcion = 'HERRAMENTAL COMPLETO Y LISTO PARA ENSAMBLE';
                            $notificacionHerramental->save();
    
                            $herramental->inicio_ensamble = date('Y-m-d H:i');
                            $herramental->save();
        
                            foreach ($users as $user) {
                                $user->hay_notificaciones = true;
                                $user->save();
                            }
                        }
                    }
                }
            } else {
                $siguienteFabricacion = $fabricacionesPendientes->firstWhere('orden', $componente->estatus_fabricacion);
                if ($siguienteFabricacion ) {
                    if ($componente->requiere_temple && !$componente->fecha_recibido_temple) { //si requiere temple y no ha sido realizado el temple 
                        
                        $procesoActual = collect($procesos)->firstWhere('id', $fabricacion->maquina->tipo_proceso);
                        $templeProceso = collect($procesos)->firstWhere('nombre', 'Templar');
                        $siguienteProceso = collect($procesos)->firstWhere('id', $siguienteFabricacion->maquina->tipo_proceso);
                        
                        if ($templeProceso && $siguienteProceso && $procesoActual && $siguienteProceso['prioridad'] > $templeProceso['prioridad'] && $procesoActual['prioridad'] < $templeProceso['prioridad'] ) {
                            $componente->fecha_solicitud_temple = date('Y-m-d');
                            $componente->save();

                            $notificacionAlmacen = new Notificacion();
                            $notificacionAlmacen->roles = json_encode(['ALMACENISTA']);
                            $notificacionAlmacen->url_base = '/temple';
                            $notificacionAlmacen->anio_id = $anio->id;
                            $notificacionAlmacen->cliente_id = $cliente->id;
                            $notificacionAlmacen->proyecto_id = $proyecto->id;
                            $notificacionAlmacen->herramental_id = $herramental->id;
                            $notificacionAlmacen->componente_id = $componente->id;
                            $notificacionAlmacen->descripcion = 'UN COMPONENTE REQUIERE TEMPLE';
                            $notificacionAlmacen->save();

                            $usuariosAlmacen = User::role('ALMACENISTA')->get();
                            foreach ($usuariosAlmacen as $usuario) {
                                $usuario->hay_notificaciones = true;
                                $usuario->save();
                            }
                        }

                    }else{ //si no requiere temple o ya se realizo el temple
                        $usuarios = User::role('OPERADOR')->get();
                        $usuariosNotificados = [];
                        foreach ($usuarios as $usuario) {
                            $maquinas = json_decode($usuario->maquinas, true);
                            if (is_array($maquinas) && in_array($siguienteFabricacion->maquina_id, $maquinas)) {
                                $usuariosNotificados[] = $usuario->id;
                                $usuario->hay_notificaciones = true;
                                $usuario->save();
                            }
                        }
    
                        if (!empty($usuariosNotificados)) {
                            $notificacion = new Notificacion();
                            $notificacion->roles = json_encode(['OPERADOR']);
                            $notificacion->url_base = '/visor-operador';
                            $notificacion->anio_id = $anio->id;
                            $notificacion->cliente_id = $cliente->id;
                            $notificacion->proyecto_id = $proyecto->id;
                            $notificacion->herramental_id = $herramental->id;
                            $notificacion->fabricacion_id = $siguienteFabricacion->id;
                            $notificacion->maquina_id = $siguienteFabricacion->maquina_id;
                            $notificacion->componente_id = $componente->id;
                            $notificacion->cantidad = $componente->cantidad;
                            $notificacion->responsables = json_encode($usuariosNotificados);
                            $notificacion->descripcion = 'COMPONENTE LIBERADO PARA FABRICACION, MAQUINA: ' . $siguienteFabricacion->maquina->nombre . ' PROGRAMA: ' . $siguienteFabricacion->getArchivoShow();
                            $notificacion->save();
                        }
                    }


                }
            }            
            
            $maquina = Maquina::findOrFail($fabricacion->maquina_id);
            $ultimoSeguimiento = SeguimientoTiempo::where('componente_id', $componente->id)
                ->where('accion', 'fabricacion')
                ->where('accion_id', $maquina->tipo_proceso)
                ->orderBy('id', 'desc') 
                ->first();

            if ($ultimoSeguimiento && $ultimoSeguimiento->tipo == true) {
                $seguimiento = new SeguimientoTiempo();
                $seguimiento->accion_id = $maquina->tipo_proceso;
                $seguimiento->accion = 'fabricacion';
                $seguimiento->tipo = false;
                $seguimiento->fecha = date('Y-m-d');
                $seguimiento->hora = date('H:i');
                $seguimiento->componente_id = $componente->id;
                $seguimiento->usuario_id = auth()->user()->id;
                $seguimiento->fabricacion_id = isset($fabricacion) ? $fabricacion->id : null;
                $seguimiento->save();
            }
        }
       
        return response()->json([
            'success' => true,
        ], 200);
    }
    public function guardarComponenteProgramacion(Request $request, $componente_id, $liberar){

        $liberar = filter_var($liberar, FILTER_VALIDATE_BOOLEAN);
        $data = json_decode($request->data, true);

        $componente = Componente::findOrFail($componente_id);
        $componente->descripcion_trabajo = $data['descripcion_trabajo'];
        $componente->herramientas_corte = $data['herramientas_corte'];
        $componente->save();

        $archivoIds = $request->input('archivo_ids', []);
        $archivosMaquinas = collect($archivoIds);

        Fabricacion::where('componente_id', $componente_id)
        ->whereNotIn('id', $archivosMaquinas->flatten())
        ->get()
        ->each(function ($fabricacion) {
            Storage::disk('public')->delete("programas/" . $fabricacion->nombre);
            $fabricacion->delete();
        });
        
        foreach ($request->file('archivo') as $maquinaId => $archivos) {
            foreach ($archivos as $index => $file) {
                $archivoId = $archivoIds[$maquinaId][$index] ?? null;

                if ($archivoId) {
                    $fabricacion = Fabricacion::find($archivoId);

                    if ($fabricacion) {
                        Storage::disk('public')->delete("programas/" . $fabricacion->nombre);
                        $name = time() . '_' . $file->getClientOriginalName();
                        Storage::disk('public')->put("programas/" . $name, \File::get($file));
                        $fabricacion->archivo = $name;
                        $fabricacion->fabricado = false;
                        $fabricacion->save();
                    }
                } else {
                    $name = time() . '_' . $file->getClientOriginalName();
                    Storage::disk('public')->put("programas/" . $name, \File::get($file));

                    $fabricacion = new Fabricacion();
                    $fabricacion->componente_id = $componente_id;
                    $fabricacion->maquina_id = $maquinaId; 
                    $fabricacion->archivo = $name;
                    $fabricacion->estatus_fabricacion = 'inicial';
                    $fabricacion->fabricado = false;
                    $fabricacion->save();                
                }
            }
        }

        
        if($liberar){
            $herramental = Herramental::findOrFail($componente->herramental_id);
            $proyecto = Proyecto::findOrFail($herramental->proyecto_id);
            $cliente = Cliente::findOrFail($proyecto->cliente_id);
            $anio = Anio::findOrFail($cliente->anio_id);

            $componente->retraso_programacion = $data['retraso_programacion'];
            $componente->estatus_programacion = 'detenido';
            $componente->programado = true;
            $componente->save();

            $procesos = [
                ['id' => 1, 'prioridad' => 1, 'nombre' => 'Cortar'],
                ['id' => 2, 'prioridad' => 2, 'nombre' => 'Programar'],
                ['id' => 3, 'prioridad' => 3, 'nombre' => 'Maquinar'],
                ['id' => 4, 'prioridad' => 4, 'nombre' => 'Tornear'],
                ['id' => 5, 'prioridad' => 5, 'nombre' => 'Roscar/Rebabear'],
                ['id' => 6, 'prioridad' => 6, 'nombre' => 'Templar'],
                ['id' => 7, 'prioridad' => 7, 'nombre' => 'Rectificar'],
                ['id' => 8, 'prioridad' => 8, 'nombre' => 'EDM']
            ];
            $prioridades = collect($procesos)->pluck('prioridad', 'id');
            $fabricaciones = Fabricacion::where('componente_id', $componente_id)
                ->with('maquina')
                ->get();

            $fabricaciones = $fabricaciones->map(function ($fabricacion) use ($prioridades) {
                $tipoProceso = $fabricacion->maquina->tipo_proceso;
                $fabricacion->prioridad_proceso = $prioridades->get($tipoProceso, 999);
                return $fabricacion;
            });
            $fabricacionesOrdenadas = $fabricaciones->sort(function ($a, $b) {
                if ($a->prioridad_proceso == $b->prioridad_proceso) {
                    return $a->id <=> $b->id;
                }
                return $a->prioridad_proceso <=> $b->prioridad_proceso;
            });
            $orden = 1;
            foreach ($fabricacionesOrdenadas as $fabricacion) {
                unset($fabricacion->prioridad_proceso);
                $fabricacion->orden = $orden++;
                $fabricacion->save();
            }

            // Si el componente termino el proceso de corte y programacion entonces avisar al operador o al matricero...
            if($componente->cortado){




                $fabricacionOrden1 = $fabricacionesOrdenadas->first(function ($fabricacion) {
                    return !$fabricacion->fabricado;
                });
                if ($fabricacionOrden1) {
                    $componente->estatus_fabricacion = $fabricacionOrden1->orden;
                    $componente->save();
    
                    $maquinaId = $fabricacionOrden1->maquina_id;
                    $arrayUsuariosNotificados = [];
    
                    $usuarios = User::role('OPERADOR')->get();
                    foreach ($usuarios as $usuario) {
                        $maquinas = json_decode($usuario->maquinas, true);
    
                        if (is_array($maquinas) && in_array($maquinaId, $maquinas)) {
                            $arrayUsuariosNotificados[] = $usuario->id;
                            $usuario->hay_notificaciones = true;
                            $usuario->save();
                        }
                    }
    
                    if (!empty($arrayUsuariosNotificados)) {
                        $notificacion = new Notificacion();
                        $notificacion->roles = json_encode(['OPERADOR']);
                        $notificacion->url_base = '/visor-operador';
                        $notificacion->anio_id = $anio->id;
                        $notificacion->cliente_id = $cliente->id;
                        $notificacion->proyecto_id = $proyecto->id;
                        $notificacion->herramental_id = $herramental->id;
                        $notificacion->fabricacion_id = $fabricacionOrden1->id;
                        $notificacion->maquina_id = $maquinaId;
                        $notificacion->componente_id = $componente->id;
                        $notificacion->cantidad = $componente->cantidad;
                        $notificacion->responsables = json_encode($arrayUsuariosNotificados);
                        $notificacion->descripcion = 'COMPONENTE LIBERADO PARA FABRICACION, MAQUINA: ' . $fabricacionOrden1->maquina->nombre . ' PROGRAMA: ' . $fabricacionOrden1->getArchivoShow();
                        $notificacion->save();
                    }
                } else {
                    $notificacionHerramental = new Notificacion();
                    $notificacionHerramental->roles = json_encode(['MATRICERO']);
                    $notificacionHerramental->url_base = '/matricero';
                    $notificacionHerramental->anio_id = $anio->id;
                    $notificacionHerramental->cliente_id = $cliente->id;
                    $notificacionHerramental->proyecto_id = $proyecto->id;
                    $notificacionHerramental->herramental_id = $herramental->id;
                    $notificacionHerramental->descripcion = 'HERRAMENTAL COMPLETO Y LISTO PARA ENSAMBLE';
                    $notificacionHerramental->save();
    
                    foreach ($users as $user) {
                        $user->hay_notificaciones = true;
                        $user->save();
                    }
                }
            }
            
            $ultimoSeguimiento = SeguimientoTiempo::where('componente_id', $componente_id)
                ->where('accion', 'programacion')
                ->orderBy('id', 'desc') 
                ->first();

            if ($ultimoSeguimiento && $ultimoSeguimiento->tipo == true) {
                $seguimiento = new SeguimientoTiempo();
                $seguimiento->accion_id = 2;
                $seguimiento->accion = 'programacion';
                $seguimiento->tipo = false;
                $seguimiento->fecha = date('Y-m-d');
                $seguimiento->hora = date('H:i');
                $seguimiento->componente_id = $componente_id;
                $seguimiento->usuario_id = auth()->user()->id;
                $seguimiento->save();
            }
        }
       

        return response()->json([
            'success' => true,
        ], 200);
    }
    public function obtenerLineaTiempoComponente($componente_id){
        $seguimiento = SeguimientoTiempo::where('componente_id', $componente_id)->orderBy('id', 'ASC')->get();
        $notificaciones = Notificacion::where('componente_id', $componente_id)->orderBy('id', 'ASC')->get();
    
        return response()->json([
            'seguimiento' => $seguimiento,
            'notificaciones' => $notificaciones,
            'success' => true,
        ], 200);
    }
    private function formatSize($sizeInBytes){
        $sizeInMB = $sizeInBytes / (1024 * 1024);
        return number_format($sizeInMB, 2) . ' MB';
    }
    public function componenteRefaccion($componente_id, $band){
        $band = filter_var($band, FILTER_VALIDATE_BOOLEAN);
        $componente = Componente::findOrFail($componente_id);
        $componente->refaccion = $band;
        $componente->save();

        return response()->json([
            'success' => true,
        ], 200);
    }
    public function guardarComponenteEnrutamiento(Request $request, $componente_id, $liberar){
        $datos = $request->json()->all();
        $liberar = filter_var($liberar, FILTER_VALIDATE_BOOLEAN);
        $componente = Componente::findOrFail($componente_id);


        if(!empty($datos['hay_retrabajo']) && $datos['hay_retrabajo'] == true){
            $componente->ruta = json_encode($datos['ruta']);
            $componente->save();

            $herramental = Herramental::findOrFail($componente->herramental_id);
            $proyecto = Proyecto::findOrFail($herramental->proyecto_id);
            $cliente = Cliente::findOrFail($proyecto->cliente_id);
            $anio = Anio::findOrFail($cliente->anio_id);

            $notificacion = new Notificacion();
            $notificacion->roles = json_encode(['PROGRAMADOR']);
            $notificacion->url_base = '/visor-programador';
            $notificacion->anio_id = $anio->id;
            $notificacion->cliente_id = $cliente->id;
            $notificacion->proyecto_id = $proyecto->id;
            $notificacion->herramental_id = $herramental->id;
            $notificacion->componente_id = $componente->id;
            $notificacion->cantidad = $componente->cantidad;
            $notificacion->responsables = json_encode([$datos['programador_id']]);
            $notificacion->descripcion = 'UN COMPONENTE REQUIERE RETRABAJO, DESCRIPCION: ' . $datos['notificacion_texto'];
            $notificacion->save();

            $user = User::findOrFail($datos['programador_id']);
            $user->hay_notificaciones = true;
            $user->save(); 
        }
        elseif(!empty($datos['hay_modificacion']) && $datos['hay_modificacion'] == true){

            $herramental = Herramental::findOrFail($componente->herramental_id);
            $proyecto = Proyecto::findOrFail($herramental->proyecto_id);
            $cliente = Cliente::findOrFail($proyecto->cliente_id);
            $anio = Anio::findOrFail($cliente->anio_id);

            $componente->ensamblado = false;        
            $componente->programado = false;
            $componente->save();

            if($componente->esComponenteExterno()){
                $solicitud = SolicitudExterna::where('componente_id', $componente->id)->first();
                $user = User::findOrFail($solicitud->solicitante_id);
                $user->hay_notificaciones = true;
                $user->save();
                $rolesSolicitante = User::findOrFail($solicitud->solicitante_id)->roles->pluck('name')->toArray();

                $notificacion = new Notificacion();
                $notificacion->roles = json_encode($rolesSolicitante, JSON_UNESCAPED_UNICODE);
                $notificacion->url_base = '/orden-trabajo';
                $notificacion->anio_id = $anio->id;
                $notificacion->cliente_id = $cliente->id;
                $notificacion->proyecto_id = $proyecto->id;
                $notificacion->herramental_id = $herramental->id;
                $notificacion->componente_id = $componente->id;
                $notificacion->cantidad = $componente->cantidad;
                $notificacion->responsables = json_encode([$solicitud->solicitante_id]);
                $notificacion->descripcion = 'UN COMPONENTE EXTERNO REQUIERE UNA MODIFICACIÓN, DESCRIPCION: ' . $datos['notificacion_texto'];
                $notificacion->save();

            }else{
                $users = User::role('AUXILIAR DE DISEÑO')->get();
                $responsables = [];
                foreach ($users as $user) {
                    $user->hay_notificaciones = true;
                    $user->save(); 
                    $responsables[] = $user->id;
                }
    
                $notificacion = new Notificacion();
                $notificacion->roles = json_encode(['AUXILIAR DE DISEÑO'], JSON_UNESCAPED_UNICODE);
                $notificacion->url_base = '/carga-componentes';
                $notificacion->anio_id = $anio->id;
                $notificacion->cliente_id = $cliente->id;
                $notificacion->proyecto_id = $proyecto->id;
                $notificacion->herramental_id = $herramental->id;
                $notificacion->componente_id = $componente->id;
                $notificacion->cantidad = $componente->cantidad;
                $notificacion->responsables = json_encode($responsables);
                $notificacion->descripcion = 'UN COMPONENTE REQUIERE MODIFICACION, DESCRIPCION: ' . $datos['notificacion_texto'];
                $notificacion->save();
            }
        }
        else{
            $componente->prioridad = $datos['prioridad'];
            $componente->requiere_temple = $datos['requiere_temple'];
            $componente->programador_id = $datos['programador_id'];
            $componente->ruta = json_encode($datos['ruta']);
            $componente->enrutado = $liberar;
            $componente->save();
            
            if($liberar){
                
                $herramental = Herramental::findOrFail($componente->herramental_id);
                $proyecto = Proyecto::findOrFail($herramental->proyecto_id);
                $cliente = Cliente::findOrFail($proyecto->cliente_id);
                $anio = Anio::findOrFail($cliente->anio_id);
    
                $notificacion = new Notificacion();
                $notificacion->roles = json_encode(['ALMACENISTA']);
                $notificacion->url_base = '/corte';
                $notificacion->anio_id = $anio->id;
                $notificacion->cliente_id = $cliente->id;
                $notificacion->proyecto_id = $proyecto->id;
                $notificacion->herramental_id = $herramental->id;
                $notificacion->componente_id = $componente->id;
                $notificacion->cantidad = $componente->cantidad;
                $notificacion->descripcion = ' COMPONENTE LIBERADO PARA CORTE';
                $notificacion->save();
    
                $users = User::role('ALMACENISTA')->get();
                foreach ($users as $user) {
                    $user->hay_notificaciones = true;
                    $user->save();
                }
    
                $notificacion = new Notificacion();
                $notificacion->roles = json_encode(['PROGRAMADOR']);
                $notificacion->url_base = '/visor-programador';
                $notificacion->anio_id = $anio->id;
                $notificacion->cliente_id = $cliente->id;
                $notificacion->proyecto_id = $proyecto->id;
                $notificacion->herramental_id = $herramental->id;
                $notificacion->componente_id = $componente->id;
                $notificacion->cantidad = $componente->cantidad;
                $notificacion->responsables = json_encode([$datos['programador_id']]);
                $notificacion->descripcion = 'COMPONENTE LIBERADO PARA PROGRAMACION';
                $notificacion->save();
    
                $user = User::findOrFail($datos['programador_id']);
                $user->hay_notificaciones = true;
                $user->save(); 
    
            }
        }

        return response()->json([
            'success' => true,
        ], 200);
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

                $notificacion = new Notificacion();
                $notificacion->roles = json_encode(['MATRICERO']);
                $notificacion->url_base = '/matricero/lista-componentes';
                $notificacion->anio_id = $anio->id;
                $notificacion->cliente_id = $cliente->id;
                $notificacion->proyecto_id = $proyecto->id;
                $notificacion->herramental_id = $herramental->id;
                $notificacion->componente_id = $nuevoComponente->id;
                $notificacion->cantidad = $nuevoComponente->cantidad;
                $notificacion->descripcion = 'COMPONENTE LISTO PARA ENSAMBLE';
                $notificacion->save();

                $users = User::role('MATRICERO')->get();
                foreach ($users as $user) {
                    $user->hay_notificaciones = true;
                    $user->save();
                }
            }
        }

        return response()->json([
            'success' => true,
        ], 200);
    }
    public function guardarComponentesTemple(Request $request, $herramental_id){
        $herramental = Herramental::findOrFail($herramental_id);
        $proyecto = Proyecto::findOrFail($herramental->proyecto_id);
        $cliente = Cliente::findOrFail($proyecto->cliente_id);
        $anio = Anio::findOrFail($cliente->anio_id);

        $componentes = json_decode($request->data, true);
        
        foreach ($componentes as $index => $componente) {

            $nuevoComponente = Componente::findOrFail($componente['id']);
            
            $recibidoOld = $nuevoComponente->fecha_recibido_temple != null;
            $nuevoComponente->fecha_solicitud_temple = $this->emptyToNull($componente['fecha_solicitud_temple']);
            $nuevoComponente->fecha_envio_temple = $this->emptyToNull($componente['fecha_envio_temple']);
            $nuevoComponente->fecha_estimada_temple = $this->emptyToNull($componente['fecha_estimada_temple']);
            $nuevoComponente->fecha_recibido_temple = $this->emptyToNull($componente['fecha_recibido_temple']);
            $nuevoComponente->save();

            $recibidoNew = $nuevoComponente->fecha_recibido_temple != null;
            

            if(!$recibidoOld && $recibidoNew){
                $fabricacionesPendientes = Fabricacion::where('componente_id', $nuevoComponente->id)
                ->where('fabricado', false)
                ->get();
                

                if ($fabricacionesPendientes->isEmpty()) {

                    if($nuevoComponente->esComponenteExterno()){
                        
                        $nuevoComponente->fecha_terminado = date('Y-m-d H:i');
                        $nuevoComponente->save();

                        $solicitud = SolicitudExterna::where('componente_id', $nuevoComponente->id)->first();
                        $solicitud->fecha_real_entrega = date('Y-m-d');
                        $solicitud->save();
                        
                        $user = User::findOrFail($solicitud->solicitante_id);
                        $user->hay_notificaciones = true;
                        $user->save();
                        
                        $rolesSolicitante = User::findOrFail($solicitud->solicitante_id)->roles->pluck('name')->toArray();

                        $notificacion = new Notificacion();
                        $notificacion->url_base = '/orden-trabajo';
                        $notificacion->roles = json_encode($rolesSolicitante, JSON_UNESCAPED_UNICODE);
                        $notificacion->anio_id = $anio->id;
                        $notificacion->cliente_id = $cliente->id;
                        $notificacion->proyecto_id = $proyecto->id;
                        $notificacion->herramental_id = $herramental->id;
                        $notificacion->componente_id = $nuevoComponente->id;
                        $notificacion->responsables = json_encode([$solicitud->solicitante_id]);
                        $notificacion->descripcion = 'EL COMPONENTE EXTERNO ESTÁ LISTO';
                        $notificacion->save();


                    }else{

                        $notificacion = new Notificacion();
                        $notificacion->roles = json_encode(['MATRICERO']);
                        $notificacion->url_base = '/matricero/lista-componentes';
                        $notificacion->anio_id = $anio->id;
                        $notificacion->cliente_id = $cliente->id;
                        $notificacion->proyecto_id = $proyecto->id;
                        $notificacion->herramental_id = $herramental->id;
                        $notificacion->componente_id = $nuevoComponente->id;
                        $notificacion->descripcion = 'COMPONENTE LISTO PARA ENSAMBLE';
                        $notificacion->save();
        
                        $nuevoComponente->fecha_terminado = date('Y-m-d H:i');
                        $nuevoComponente->save();
        
                        $users = User::role('MATRICERO')->get();

                        foreach ($users as $user) {
                            $user->hay_notificaciones = true;
                            $user->save();
                        }
                        $herramentalListo = true;
                        $otrosComponentes = Componente::where('herramental_id', $herramental->id)->where('refabricado', false)->with('fabricaciones')->get();

                        foreach ($otrosComponentes as $comp) {
                            foreach ($comp->fabricaciones as $fabriComp) {
                                if (!$fabriComp->fabricado) {
                                    $herramentalListo = false;
                                    break 2;
                                }
                            }
                        }
                        if ($herramentalListo) {
                            $notificacionHerramental = new Notificacion();
                            $notificacionHerramental->roles = json_encode(['MATRICERO']);
                            $notificacionHerramental->url_base = '/matricero';
                            $notificacionHerramental->anio_id = $anio->id;
                            $notificacionHerramental->cliente_id = $cliente->id;
                            $notificacionHerramental->proyecto_id = $proyecto->id;
                            $notificacionHerramental->herramental_id = $herramental->id;
                            $notificacionHerramental->descripcion = 'HERRAMENTAL COMPLETO Y LISTO PARA ENSAMBLE';
                            $notificacionHerramental->save();
                            $herramental->inicio_ensamble = date('Y-m-d H:i');
                            $herramental->save();
                        
        
                            foreach ($users as $user) {
                                $user->hay_notificaciones = true;
                                $user->save();
                            }
                        }
                    }
                }else{
                    $siguienteFabricacion = $fabricacionesPendientes->firstWhere('orden', $nuevoComponente->estatus_fabricacion);
                    if($siguienteFabricacion){

                        $usuarios = User::role('OPERADOR')->get();
                        $usuariosNotificados = [];
                        foreach ($usuarios as $usuario) {
                            $maquinas = json_decode($usuario->maquinas, true);
                            if (is_array($maquinas) && in_array($siguienteFabricacion->maquina_id, $maquinas)) {
                                $usuariosNotificados[] = $usuario->id;
                                $usuario->hay_notificaciones = true;
                                $usuario->save();
                            }
                        }

                        if (!empty($usuariosNotificados)) {
                            $notificacion = new Notificacion();
                            $notificacion->roles = json_encode(['OPERADOR']);
                            $notificacion->url_base = '/visor-operador';
                            $notificacion->anio_id = $anio->id;
                            $notificacion->cliente_id = $cliente->id;
                            $notificacion->proyecto_id = $proyecto->id;
                            $notificacion->herramental_id = $herramental->id;
                            $notificacion->fabricacion_id = $siguienteFabricacion->id;
                            $notificacion->maquina_id = $siguienteFabricacion->maquina_id;
                            $notificacion->componente_id = $nuevoComponente->id;
                            $notificacion->cantidad = $nuevoComponente->cantidad;
                            $notificacion->responsables = json_encode($usuariosNotificados);
                            $notificacion->descripcion = 'COMPONENTE LIBERADO PARA FABRICACION, MAQUINA: ' . $siguienteFabricacion->maquina->nombre . ' PROGRAMA: ' . $siguienteFabricacion->getArchivoShow();
                            $notificacion->save();
                        }
                    }
                }
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

                if(!$nuevoComponente->refabricado && isset($componente['refabricado']) && $componente['refabricado'] == true) {
                    $nuevoComponente->refabricado = true;
                    $nuevoComponente->save();

                    $herramental = Herramental::findOrFail($herramental_id);
                    $proyecto = Proyecto::findOrFail($herramental->proyecto_id);
                    $cliente = Cliente::findOrFail($proyecto->cliente_id);
                    $anio = Anio::findOrFail($cliente->anio_id);

                    $notificacion = new Notificacion();
                    $notificacion->roles = json_encode(['JEFE DE AREA']);
                    $notificacion->url_base = '/enrutador';
                    $notificacion->anio_id = $anio->id;
                    $notificacion->cliente_id = $cliente->id;
                    $notificacion->proyecto_id = $proyecto->id;
                    $notificacion->herramental_id = $herramental->id;
                    $notificacion->componente_id = $nuevoComponente->id;
                    $notificacion->cantidad = $nuevoComponente->cantidad;
                    $notificacion->descripcion = 'SE HA GENERADO UNA NUEVA VERSION DEL COMPONENTE DEBIDO A UNA REFABRICACION.';
                    $notificacion->save();

                    $users = User::role('JEFE DE AREA')->get();
                    foreach ($users as $user) {
                        $user->hay_notificaciones = true;
                        $user->save();
                    }
                }
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
                $nuevoComponente->refabricado = false;
                $nuevoComponente->refaccion = false;
                
                $nuevoComponente->herramental_id = $herramental_id;
                $nuevoComponente->area = 'diseno-carga';
                $nuevoComponente->estatus_corte = 'inicial';
                $nuevoComponente->estatus_programacion = 'inicial';
                $nuevoComponente->estatus_fabricacion = 1;
                $nuevoComponente->cargado = false;
                $nuevoComponente->comprado = false;
                $nuevoComponente->enrutado = false;
                $nuevoComponente->programado = false;
                $nuevoComponente->cortado = false;
                $nuevoComponente->ensamblado = false;
                $nuevoComponente->save();
                $this->actualizarVersiones($herramental->id);
                
            }
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
    public function actualizarVersiones($herramental_id){
        $componentes = Componente::where('herramental_id', $herramental_id)
            ->orderBy('nombre') // Agrupar por nombre
            ->orderBy('created_at') // Ordenar por fecha de creación
            ->get();

        $componentesAgrupados = $componentes->groupBy('nombre');

        foreach ($componentesAgrupados as $nombre => $grupo) {
            $version = 1; // Inicializamos la versión en 1

            foreach ($grupo as $componente) {
                $componente->version = $version;
                $componente->save();

                $version++; // Incrementamos la versión para el siguiente componente
            }
        }
    }
    public function retrabajoComponente($componente_id){
        $componente = Componente::findOrFail($componente_id);
        $componente->programado = false;
        $componente->save();

        $herramental = Herramental::findOrFail($componente->herramental_id);
        $proyecto = Proyecto::findOrFail($herramental->proyecto_id);
        $cliente = Cliente::findOrFail($proyecto->cliente_id);
        $anio = Anio::findOrFail($cliente->anio_id);

        $notificacion = new Notificacion();
        $notificacion->roles = json_encode(['JEFE DE AREA']);
        $notificacion->url_base = '/enrutador';
        $notificacion->anio_id = $anio->id;
        $notificacion->cliente_id = $cliente->id;
        $notificacion->proyecto_id = $proyecto->id;
        $notificacion->herramental_id = $herramental->id;
        $notificacion->componente_id = $componente->id;
        $notificacion->cantidad = $componente->cantidad;
        $notificacion->descripcion = 'EL DISEÑO DEL COMPONENTE HA SIDO MODIFICADO, SE REQUIERE UN RETRABAJO.';
        $notificacion->save();

        $users = User::role('JEFE DE AREA')->get();
        foreach ($users as $user) {
            $user->hay_notificaciones = true;
            $user->save();
        }

        return response()->json([
            'success' => true,
        ], 200);
    }
    public function limpiarRuta($componente){
        $rutaOriginal = json_decode($componente->ruta, true);
        $rutaLimpia = array_map(function ($proceso) {
            $proceso['time'] = array_filter($proceso['time'], function ($time) {
                return $time['type'] === 'normal';
            });
            return $proceso;
        }, $rutaOriginal);

        $rutaLimpia = array_filter($rutaLimpia, function ($proceso) {
            return !empty($proceso['time']);
        });
        $componente->ruta = json_encode($rutaLimpia, JSON_PRETTY_PRINT);
        $componente->save();
    }
    public function refabricacionComponente($componente_id){
        $componente = Componente::findOrFail($componente_id);
        $componente->refabricado = true;
        $componente->save();

        $herramental = Herramental::findOrFail($componente->herramental_id);
        $proyecto = Proyecto::findOrFail($herramental->proyecto_id);
        $cliente = Cliente::findOrFail($proyecto->cliente_id);
        $anio = Anio::findOrFail($cliente->anio_id);
        

        $nuevoComponente = new Componente();
        $nuevoComponente->nombre = $componente->nombre;
        // $nuevoComponente->archivo_2d = $componente->archivo_2d;
        // $nuevoComponente->archivo_3d = $componente->archivo_3d;
        // $nuevoComponente->archivo_explosionado = $componente->archivo_explosionado;
        $nuevoComponente->cantidad = $componente->cantidad;
        $nuevoComponente->largo = $componente->largo;
        $nuevoComponente->ancho = $componente->ancho;
        $nuevoComponente->alto = $componente->alto;
        $nuevoComponente->peso = $componente->peso;
        $nuevoComponente->precio_kilo = $componente->precio_kilo;
        $nuevoComponente->fecha_cargado = date('Y-m-d H:i');
        $nuevoComponente->prioridad = $componente->prioridad;
        $nuevoComponente->ruta = $componente->ruta;
        $nuevoComponente->maquina = $componente->maquina;
        $nuevoComponente->descripcion_trabajo = $componente->descripcion_trabajo;
        $nuevoComponente->herramientas_corte = $componente->herramientas_corte;
        $nuevoComponente->refabricado = false;
        $nuevoComponente->refaccion = false;
        $nuevoComponente->es_compra = $componente->es_compra;
        $nuevoComponente->area = $componente->area;
        $nuevoComponente->cargado = true;
        $nuevoComponente->comprado = false;
        $nuevoComponente->programado = false;
        $nuevoComponente->cortado = false;
        $nuevoComponente->enrutado = false;
        $nuevoComponente->ensamblado = false;
        $nuevoComponente->estatus_corte = 'inicial';
        $nuevoComponente->estatus_programacion ='inicial';
        $nuevoComponente->estatus_fabricacion = 1;
        $nuevoComponente->programador_id = $componente->programador_id;
        $nuevoComponente->herramental_id = $componente->herramental_id;
        $nuevoComponente->material_id = $componente->material_id;
        
        $rutaBase = "{$herramental->proyecto_id}/{$herramental->id}/componentes/";
        if ($componente->archivo_2d) {
            $nuevoNombre2D = $this->generarNuevoNombre($componente->archivo_2d);
            Storage::disk('public')->copy(
                "{$rutaBase}{$componente->archivo_2d}", 
                "{$rutaBase}{$nuevoNombre2D}"
            );
            $nuevoComponente->archivo_2d = $nuevoNombre2D;
        }

        if ($componente->archivo_3d) {
            $nuevoNombre3D = $this->generarNuevoNombre($componente->archivo_3d);
            Storage::disk('public')->copy(
                "{$rutaBase}{$componente->archivo_3d}", 
                "{$rutaBase}{$nuevoNombre3D}"
            );
            $nuevoComponente->archivo_3d = $nuevoNombre3D;
        }

        if ($componente->archivo_explosionado) {
            $nuevoNombreExplosionado = $this->generarNuevoNombre($componente->archivo_explosionado);
            Storage::disk('public')->copy(
                "{$rutaBase}{$componente->archivo_explosionado}", 
                "{$rutaBase}{$nuevoNombreExplosionado}"
            );
            $nuevoComponente->archivo_explosionado = $nuevoNombreExplosionado;
        }
        $nuevoComponente->save();
        
        $this->limpiarRuta($nuevoComponente);
        $this->actualizarVersiones($componente->herramental_id);

        $notificacion = new Notificacion();
        $notificacion->roles = json_encode(['JEFE DE AREA']);
        $notificacion->url_base = '/enrutador';
        $notificacion->anio_id = $anio->id;
        $notificacion->cliente_id = $cliente->id;
        $notificacion->proyecto_id = $proyecto->id;
        $notificacion->herramental_id = $herramental->id;
        $notificacion->componente_id = $componente->id;
        $notificacion->cantidad = $componente->cantidad;
        $notificacion->descripcion = 'HA GENERADO UNA NUEVA REFABRICACION PARA EL COMPONENTE.';
        $notificacion->save();

        $users = User::role('JEFE DE AREA')->get();
        foreach ($users as $user) {
            $user->hay_notificaciones = true;
            $user->save();
        }

        return response()->json([
            'id' => $nuevoComponente->id,
            'success' => true,
        ], 200);
    }
    public function generarNuevoNombre($nombreArchivo) {
        if (strpos($nombreArchivo, '_') !== false) {
            $parteSinPrefijo = substr($nombreArchivo, strpos($nombreArchivo, '_') + 1); // Elimina la parte antes del primer "_"
            return uniqid() . '_' . $parteSinPrefijo; // Genera un nuevo nombre con el identificador único
        }
        return uniqid() . '_' . $nombreArchivo; // Si no hay "_", simplemente añade el identificador único
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
                $users = User::role('ALMACENISTA')->get();
                foreach ($users as $user) {
                    $user->hay_notificaciones = true;
                    $user->save();
                }
            }
            if($conteoCortes > 0){    
                $notificacion = new Notificacion();
                $notificacion->roles = json_encode(['JEFE DE AREA']);
                $notificacion->url_base = '/enrutador';
                $notificacion->anio_id = $anio->id;
                $notificacion->cliente_id = $cliente->id;
                $notificacion->proyecto_id = $proyecto->id;
                $notificacion->herramental_id = $herramental->id;
                $notificacion->componente_id = null;
                $notificacion->cantidad = null;
                $notificacion->descripcion = $conteoCortes . ' COMPONENTES HAN SIDO LIBERADOS PARA RUTA';
                $notificacion->save();

                $users = User::role('JEFE DE AREA')->get();
                foreach ($users as $user) {
                    $user->hay_notificaciones = true;
                    $user->save();
                }

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
    public function liberarHerramentalEnsamble($herramental_id){
        $herramental = Herramental::findOrFail($herramental_id);

        $herramental->estatus_ensamble = 'finalizado';
        $herramental->termino_ensamble = date('Y-m-d H:i');
        $herramental->save();  
        
        $proyecto = Proyecto::findOrFail($herramental->proyecto_id);
        $cliente = Cliente::findOrFail($proyecto->cliente_id);
        $anio = Anio::findOrFail($cliente->anio_id);

        $notificacion = new Notificacion();
        $notificacion->roles = json_encode(['JEFE DE AREA', 'DISEÑO'], JSON_UNESCAPED_UNICODE);
        $notificacion->url_base = '/visor-pruebas';
        $notificacion->anio_id = $anio->id;
        $notificacion->cliente_id = $cliente->id;
        $notificacion->proyecto_id = $proyecto->id;
        $notificacion->herramental_id = $herramental->id;
        $notificacion->descripcion = 'SE HA LIBERADO UN HERRAMENTAL PARA PRUEBAS DE DISEÑO.';
        $notificacion->save();

        $users = User::role('JEFE DE AREA')->get();
        foreach ($users as $user) {
            $user->hay_notificaciones = true;
            $user->save();
        }
        $users = User::role('DISEÑO')->get();
        foreach ($users as $user) {
            $user->hay_notificaciones = true;
            $user->save();
        }

        return response()->json([
            'success' => true,
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

        // ya no se uso area, ahora es con varias banderas para saber a donde se va
        // if($componente->area == 'almacenista'){
        //     $notificacion = new Notificacion();
        //     $notificacion->roles = json_encode(['ALMACENISTA']);
        //     $notificacion->url_base = $componente->area == 'corte' ? '/corte' : '/compra-componentes';
        //     $notificacion->anio_id = $anio->id;
        //     $notificacion->cliente_id = $cliente->id;
        //     $notificacion->proyecto_id = $proyecto->id;
        //     $notificacion->herramental_id = $herramental->id;
        //     $notificacion->componente_id = $componente->id;
        //     $notificacion->cantidad = $componente->cantidad;
        //     $notificacion->descripcion = 'COMPONENTE CANCELADO';
        //     $notificacion->save();
        // }
        // if($componente->area == 'jefe de area'){
        //     $notificacion = new Notificacion();
        //     $notificacion->roles = json_encode(['JEFE DE AREA']);
        //     $notificacion->url_base = '/enrutador';
        //     $notificacion->anio_id = $anio->id;
        //     $notificacion->cliente_id = $cliente->id;
        //     $notificacion->proyecto_id = $proyecto->id;
        //     $notificacion->herramental_id = $herramental->id;
        //     $notificacion->componente_id = $componente->id;
        //     $notificacion->cantidad = $componente->cantidad;
        //     $notificacion->descripcion = 'COMPONENTE CANCELADO';
        //     $notificacion->save();
        // }

        return response()->json([
            'success' => true,
        ], 200);
    }
    public function liberarComponenteCargar(Request $request, $herramental_id){
        $id = $request->componente;
        
        $componente = Componente::findOrFail($id);
        $herramental = Herramental::findOrFail($herramental_id);
        $proyecto = Proyecto::findOrFail($herramental->proyecto_id);
        $cliente = Cliente::findOrFail($proyecto->cliente_id);
        $anio = Anio::findOrFail($cliente->anio_id);


        $componente->area = $componente->es_compra ? 'almacenista' : 'jefe de area';
        $componente->cargado = true;
        $componente->fecha_solicitud = $componente->es_compra ? date('Y-m-d') : null;
        $componente->save();

        if($componente->es_compra){
            $componente->fecha_cargado = date('Y-m-d H:i');
            $componente->save();

            $notificacion = new Notificacion();
            $notificacion->roles = json_encode(['ALMACENISTA']);
            $notificacion->url_base = '/compra-componentes';
            $notificacion->anio_id = $anio->id;
            $notificacion->cliente_id = $cliente->id;
            $notificacion->proyecto_id = $proyecto->id;
            $notificacion->herramental_id = $herramental->id;
            $notificacion->componente_id = $componente->id;
            $notificacion->cantidad = $componente->cantidad;
            $notificacion->descripcion = 'COMPONENTE LIBERADO PARA COMPRA';
            $notificacion->save();
            $users = User::role('ALMACENISTA')->get();
            foreach ($users as $user) {
                $user->hay_notificaciones = true;
                $user->save();
            }
        }else{
            $componente->fecha_cargado = date('Y-m-d H:i');
            $componente->save();

            $notificacion = new Notificacion();
            $notificacion->roles = json_encode(['JEFE DE AREA']);
            $notificacion->url_base = '/enrutador';
            $notificacion->anio_id = $anio->id;
            $notificacion->cliente_id = $cliente->id;
            $notificacion->proyecto_id = $proyecto->id;
            $notificacion->herramental_id = $herramental->id;
            $notificacion->componente_id = $componente->id;
            $notificacion->cantidad = $componente->cantidad;
            $notificacion->descripcion = 'COMPONENTE LIBERADO PARA RUTA';
            $notificacion->save();
            $users = User::role('JEFE DE AREA')->get();
            foreach ($users as $user) {
                $user->hay_notificaciones = true;
                $user->save();
            }
        }


        return response()->json([
            'success' => true,
        ], 200);
    
    }
    public function ultimasNotificaciones() {
        $roles = auth()->user()->roles->pluck('name');

        $notificaciones = Notificacion::where(function ($query) use ($roles) {
            foreach ($roles as $role) {
                $query->orWhere('roles', 'LIKE', '%"'.$role.'"%');
            }
        })->latest('id')->get();

        $notificacionesFiltradas = $notificaciones->filter(function ($notificacion) {
            return $this->verificarResponsables($notificacion);
        })->take(6);


        return response()->json([
            'notificaciones' => $notificacionesFiltradas,
            'success' => true,
        ], 200);
    }
    public function verificarResponsables($notificacion) {
        $userId = auth()->id();
        if(!$notificacion->responsables){return true;}

        $responsables = json_decode($notificacion->responsables, true);
        if (is_array($responsables)) {
            if (in_array($userId, $responsables)) {
                return true; 
            }
        }        
        return false;
    }
    public function notificaciones() {
        $roles = auth()->user()->roles->pluck('name');

        $notificaciones = Notificacion::where(function ($query) use ($roles) {
            foreach ($roles as $role) {
                $query->orWhere('roles', 'LIKE', '%"'.$role.'"%');
            }
        })->latest('id')->get();

        $notificacionesFiltradas = $notificaciones->filter(function ($notificacion) {
            return $this->verificarResponsables($notificacion);
        });

        return response()->json([
            'notificaciones' => $notificacionesFiltradas,
            'success' => true,
        ], 200);

    }
    public function verNotificaciones(){
        $user = auth()->user();
        $user->hay_notificaciones = false;
        $user->save();

        return response()->json([
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

        $seguimiento = new SeguimientoTiempo();
        $seguimiento->accion_id = 1;
        $seguimiento->accion = 'corte';
        $seguimiento->tipo = $request->estatus == 'proceso' ? true : false;
        $seguimiento->fecha = date('Y-m-d');
        $seguimiento->hora = date('H:i');
        $seguimiento->componente_id = $id;
        $seguimiento->usuario_id = auth()->user()->id;
        $seguimiento->save();

        return response()->json([
            'success' => true,
        ], 200);
    }
    public function cambiarEstatusProgramacion(Request $request, $id){
        $componente = Componente::findOrFail($id);
        $componente->estatus_programacion = $request->estatus;
        $componente->save();

        $seguimiento = new SeguimientoTiempo();
        $seguimiento->accion_id = 2;
        $seguimiento->accion = 'programacion';
        $seguimiento->tipo = $request->estatus == 'proceso' ? true : false;
        $seguimiento->fecha = date('Y-m-d');
        $seguimiento->hora = date('H:i');
        $seguimiento->componente_id = $id;
        $seguimiento->usuario_id = auth()->user()->id;
        $seguimiento->save();

        return response()->json([
            'success' => true,
        ], 200);
    }
    public function cambiarEstatusFabricacion(Request $request, $id){
        $fabricacion = Fabricacion::findOrFail($id);
        $maquina = Maquina::findOrFail($fabricacion->maquina_id);
        $fabricacion->estatus_fabricacion = $request->estatus;
        $fabricacion->save(); 

        $seguimiento = new SeguimientoTiempo();
        $seguimiento->accion_id = $maquina->tipo_proceso;
        $seguimiento->accion = 'fabricacion';
        $seguimiento->tipo = $request->estatus == 'proceso' ? true : false;
        $seguimiento->fecha = date('Y-m-d');
        $seguimiento->hora = date('H:i');
        $seguimiento->componente_id = $fabricacion->componente_id;
        $seguimiento->fabricacion_id = $fabricacion->id;
        $seguimiento->usuario_id = auth()->user()->id;
        $seguimiento->save();

        return response()->json([
            'success' => true,
        ], 200);
    }
    public function finalizarCorte(Request $request, $id){

        $componente = Componente::findOrFail($id);
        $herramental = Herramental::findOrFail($componente->herramental_id);
        
        $componente->estatus_corte = 'finalizado';
        $componente->cortado = true;
        $componente->retraso_corte = !empty($request->movimiento['motivo_retraso']) ? $request->movimiento['motivo_retraso'] : null;
        $componente->save();

        $ultimoSeguimiento = SeguimientoTiempo::where('componente_id', $id)
                ->where('accion', 'corte')
                ->orderBy('id', 'desc') 
                ->first();

        if ($ultimoSeguimiento && $ultimoSeguimiento->tipo == true) {
            $seguimiento = new SeguimientoTiempo();
            $seguimiento->accion_id = 1;
            $seguimiento->accion = 'corte';
            $seguimiento->tipo = false;
            $seguimiento->fecha = date('Y-m-d');
            $seguimiento->hora = date('H:i');
            $seguimiento->componente_id = $id;
            $seguimiento->usuario_id = auth()->user()->id;
            $seguimiento->save();
        }


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


        // Notificar al operador en caso de que la programacion ya haya terminado, o al matricero...
        
        if($componente->programado){
            $fabricacionesOrdenadas = Fabricacion::where('componente_id', $id)->orderBy('orden', 'asc')->get();

            $fabricacionOrden1 = $fabricacionesOrdenadas->first(function ($fabricacion) {
                return !$fabricacion->fabricado;
            });
            if ($fabricacionOrden1) {
                $componente->estatus_fabricacion = $fabricacionOrden1->orden;
                $componente->save();

                $maquinaId = $fabricacionOrden1->maquina_id;
                $arrayUsuariosNotificados = [];

                $usuarios = User::role('OPERADOR')->get();
                foreach ($usuarios as $usuario) {
                    $maquinas = json_decode($usuario->maquinas, true);

                    if (is_array($maquinas) && in_array($maquinaId, $maquinas)) {
                        $arrayUsuariosNotificados[] = $usuario->id;
                        $usuario->hay_notificaciones = true;
                        $usuario->save();
                    }
                }

                if (!empty($arrayUsuariosNotificados)) {
                    $notificacion = new Notificacion();
                    $notificacion->roles = json_encode(['OPERADOR']);
                    $notificacion->url_base = '/visor-operador';
                    $notificacion->anio_id = $anio->id;
                    $notificacion->cliente_id = $cliente->id;
                    $notificacion->proyecto_id = $proyecto->id;
                    $notificacion->herramental_id = $herramental->id;
                    $notificacion->fabricacion_id = $fabricacionOrden1->id;
                    $notificacion->maquina_id = $maquinaId;
                    $notificacion->componente_id = $componente->id;
                    $notificacion->cantidad = $componente->cantidad;
                    $notificacion->responsables = json_encode($arrayUsuariosNotificados);
                    $notificacion->descripcion = 'COMPONENTE LIBERADO PARA FABRICACION, MAQUINA: ' . $fabricacionOrden1->maquina->nombre . ' PROGRAMA: ' . $fabricacionOrden1->getArchivoShow();
                    $notificacion->save();
                }
            }
        }

        return response()->json([
            'success' => true,
        ], 200);
    }
    public function registrarParo(Request $request, $id){
        $data = $request->json()->all();
        $componente = Componente::findOrFail($id);

        switch($data['tipo']){
            case 'corte_paro':
                $componente->estatus_corte = 'paro';
                $componente->save();

                $ultimoSeguimiento = SeguimientoTiempo::where('componente_id', $id)
                    ->where('accion', 'corte')
                    ->orderBy('id', 'desc')
                    ->first();

                if ($ultimoSeguimiento && $ultimoSeguimiento->tipo == true) {
                    $seguimiento = new SeguimientoTiempo();
                    $seguimiento->accion_id = 1;
                    $seguimiento->accion = 'corte';
                    $seguimiento->tipo = false;
                    $seguimiento->fecha = date('Y-m-d');
                    $seguimiento->hora = date('H:i');
                    $seguimiento->componente_id = $id;
                    $seguimiento->usuario_id = auth()->user()->id;
                    $seguimiento->save();
                }
            break;
            case 'fabricacion_paro':
                $fabricacion = Fabricacion::findOrFail($data['fabricacion_id']);
                $maquina = Maquina::findOrFail($fabricacion->maquina_id);
                $fabricacion->estatus_fabricacion = 'paro';
                $fabricacion->save();

                $ultimoSeguimiento = SeguimientoTiempo::where('componente_id', $id)
                    ->where('accion', 'fabricacion')
                    ->where('accion_id', $maquina->tipo_proceso)
                    ->orderBy('id', 'desc')
                    ->first();

                if ($ultimoSeguimiento && $ultimoSeguimiento->tipo == true) {
                    $seguimiento = new SeguimientoTiempo();
                    $seguimiento->accion_id = $maquina->tipo_proceso;
                    $seguimiento->accion = 'fabricacion';
                    $seguimiento->tipo = false;
                    $seguimiento->fecha = date('Y-m-d');
                    $seguimiento->hora = date('H:i');
                    $seguimiento->componente_id = $id;
                    $seguimiento->usuario_id = auth()->user()->id;
                    $seguimiento->fabricacion_id = $fabricacion->id;
                    $seguimiento->save();
                }
            break;
        }

        $seguimiento = new SeguimientoTiempo();
        $seguimiento->accion_id = -1;
        $seguimiento->accion = $data['tipo'];
        $seguimiento->tipo = true;
        $seguimiento->fecha = date('Y-m-d');
        $seguimiento->hora = date('H:i');
        $seguimiento->componente_id = $id;
        $seguimiento->comentarios_paro = $data['comentarios_paro'];
        $seguimiento->tipo_paro = $data['tipo_paro'];
        $seguimiento->usuario_id = auth()->user()->id;
        $seguimiento->fabricacion_id = isset($fabricacion) ? $fabricacion->id : null;
        $seguimiento->save();

        return response()->json([
            'success' => true,
        ], 200);
    }
    public function eliminarParo($id, $tipo){
        
        switch($tipo){
            case 'corte_paro':
                $componente = Componente::findOrFail($id);
                $componente->estatus_corte = 'pausado';
                $componente->save();
            break;

            case 'fabricacion_paro':
                $fabricacion = Fabricacion::findOrFail($id);
                $componente = Componente::findOrFail($fabricacion->componente_id);
                $fabricacion->estatus_fabricacion = 'detenido';
                $fabricacion->save();
            break;
        }

        $seguimiento = new SeguimientoTiempo();
        $seguimiento->accion_id = -1;
        $seguimiento->accion = $tipo;
        $seguimiento->tipo = false;
        $seguimiento->fecha = date('Y-m-d');
        $seguimiento->hora = date('H:i');
        $seguimiento->componente_id = $componente->id;
        $seguimiento->usuario_id = auth()->user()->id;
        $seguimiento->fabricacion_id = isset($fabricacion) ? $fabricacion->id : null;
        $seguimiento->save();

        return response()->json([
            'success' => true,
        ], 200);
    }
    public function obtenerAvanceHR($herramental_id) {
        $herramental = Herramental::findOrFail($herramental_id);
        $componentes = Componente::where('herramental_id', $herramental_id)
            ->orderBy('nombre') 
            ->orderBy('version', 'asc') 
            ->get();

        $componentesAgrupados = $componentes->groupBy('nombre');

        $resultados = [];

        foreach ($componentesAgrupados as $nombre => $grupo) {
            $grupoOrdenado = $grupo->sortBy('version');

            $time = [];
            $totalVersiones = $grupoOrdenado->count();

            $grupoOrdenado->values()->each(function ($componente, $index) use (&$time, $grupoOrdenado, $totalVersiones) {
                $next = $index + 1 < $totalVersiones ? $grupoOrdenado->get($index + 1) : null; 

                $time[] = [
                    'version' => $componente->version, 
                    'info' => $componente, 
                    'type' => $componente->es_compra ? 'normal' : ($componente->refabricado ? 'rework' : 'normal'),
                    'dia_inicio' => $componente->es_compra 
                        ? date('Y-m-d H:i', strtotime($componente->fecha_solicitud)) 
                        : date('Y-m-d H:i', strtotime($componente->fecha_cargado)),
                    'dia_termino' => $componente->es_compra 
                        ? ($componente->fecha_real 
                            ? date('Y-m-d H:i', strtotime($componente->fecha_real)) 
                            : date('Y-m-d H:i')) 
                        : ($componente->fecha_terminado 
                            ? date('Y-m-d H:i', strtotime($componente->fecha_terminado)) 
                            : ($next ? date('Y-m-d H:i', strtotime($next->fecha_cargado)) : date('Y-m-d H:i'))),
                ];
            });
            $componenteActual = $grupoOrdenado->last();

            $resultados[] = [
                'componente' => $nombre,
                'componente_id' => $componenteActual->id,
                'tieneRetrasos' => $componenteActual->tieneRetrasos(),
                'tieneRetrabajos' => $componenteActual->tieneRetrabajos(),
                'tieneRefabricaciones' => $componenteActual->tieneRefabricaciones(),
                'esRefaccion' => $componenteActual->refaccion??false,
                'time' => $time, 
            ];
        }

        if ($herramental->inicio_ensamble) {
            $ensambleTime = [
                'type' => 'normal',
                'dia_inicio' => $herramental->inicio_ensamble,
                'dia_termino' => $herramental->termino_ensamble ?? date('Y-m-d H:i'), 
                'info' => [
                    'es_compra' => false,
                ],
            ];

            $resultados[] = [
                'componente' => 'ENSAMBLE',
                'componente_id' => -1, 
                'time' => [$ensambleTime], 
            ];
        }

        $pruebasDiseno = PruebaDiseno::where('herramental_id', $herramental_id)->get();
        foreach ($pruebasDiseno as $prueba) {
            $resultados[] = [
                'componente' =>  strtoupper($prueba->nombre) . " - DÑO",
                'componente_id' => -2, 
                'prueba_id' => $prueba->id, 
                'time' => [
                    [
                        'type' => 'normal',
                        'dia_inicio' => $prueba->fecha_inicio,
                        'dia_termino' => $prueba->fecha_liberada ? $prueba->fecha_liberada: date('Y-m-d H:i'),
                        'info' => [
                            'es_compra' => false,
                        ],
                    ],
                ], 
            ];
        }
        $pruebasProceso = PruebaProceso::where('herramental_id', $herramental_id)->get();
        foreach ($pruebasProceso as $prueba) {
            $resultados[] = [
                'componente' =>  strtoupper($prueba->nombre) . " - PROC",
                'componente_id' => -3, 
                'prueba_id' => $prueba->id, 
                'time' => [
                    [
                        'type' => 'normal',
                        'dia_inicio' => $prueba->fecha_inicio,
                        'dia_termino' => $prueba->fecha_liberada ? $prueba->fecha_liberada: date('Y-m-d H:i'),
                        'info' => [
                            'es_compra' => false,
                        ],
                    ],
                ], 
            ];
        }

        $pruebasDiseno = PruebaDiseno::where('herramental_id', $herramental_id)->get();
        $pruebasProceso = PruebaProceso::where('herramental_id', $herramental_id)->get();
        $componentes = Componente::where('herramental_id', $herramental_id)
            ->orderBy('nombre') 
            ->get();

        return response()->json([
            'success' => true,
            'tasks' => $resultados,
            'herramental' => $herramental,
            'pruebasDiseno' => $pruebasDiseno,
            'pruebasProceso' => $pruebasProceso,
            'componentes' => $componentes,
        ]);
    }
    public function registrarSolicitudHerramental(Request $request, $id){
        $data = $request->json()->all();
        $herramental = Herramental::findOrFail($id);
        $herramental->estatus_ensamble = 'proceso';
        $herramental->estatus_pruebas_diseno = 'inicial';
        $herramental->estatus_pruebas_proceso = 'inicial';
        $herramental->save();

        $pruebas = PruebaDiseno::where('herramental_id', $herramental->id)->get();
        foreach ($pruebas as $prueba) {
            $prueba->liberada = true;
            $prueba->fecha_liberada = date('Y-m-d H:i');
            $prueba->save();
        }
        $pruebas = PruebaProceso::where('herramental_id', $herramental->id)->get();
        foreach ($pruebas as $prueba) {
            $prueba->liberada = true;
            $prueba->fecha_liberada = date('Y-m-d H:i');
            $prueba->save();
        }

        $componentesSeleccionados = Componente::whereIn('id', $data['componentes'])->get();
        $proyecto = Proyecto::findOrFail($herramental->proyecto_id);
        $cliente = Cliente::findOrFail($proyecto->cliente_id);
        $anio = Anio::findOrFail($cliente->anio_id);

        foreach ($componentesSeleccionados as $componente) {
            $componente->ensamblado = false;        
            $componente->programado = false;
            $componente->save();

            $solicitud = new Solicitud();
            $solicitud->tipo = $data['tipo'];
            $solicitud->componente_id = $componente->id;
            $solicitud->comentarios = $data['comentarios'];
            $solicitud->area_solicitante = $data['area_solicitante'];
            $solicitud->usuario_id = auth()->user()->id;
            $solicitud->save();
    
            $notificacion = new Notificacion();
            $notificacion->roles = json_encode(['JEFE DE AREA']);
            $notificacion->url_base = '/enrutador';
            $notificacion->anio_id = $anio->id;
            $notificacion->cliente_id = $cliente->id;
            $notificacion->proyecto_id = $proyecto->id;
            $notificacion->herramental_id = $herramental->id;
            $notificacion->componente_id = $componente->id;
            $notificacion->cantidad = $componente->cantidad;
            $notificacion->descripcion = 'SOLICITUD DE MODIFICACION, AREA: ' . $solicitud->area_solicitante . ', COMENTARIOS:' . $solicitud->comentarios;
            $notificacion->save();
    
            $users = User::role('JEFE DE AREA')->get();
            foreach ($users as $user) {
                $user->hay_notificaciones = true;
                $user->save();
            }
        }

        return response()->json([
            'success' => true,
        ], 200);

    }
    public function registrarSolicitud(Request $request, $id){
        $data = $request->json()->all();
        
        $componente = Componente::findOrFail($id);
        $componente->ensamblado = false;        
        $componente->programado = false;
        $componente->save();
        // $fabricaciones = Fabricacion::where('componente_id', $componente->id)->get();
        
        $solicitud = new Solicitud();
        $solicitud->tipo = $data['tipo'];
        $solicitud->comentarios = $data['comentarios'];
        $solicitud->area_solicitante = $data['area_solicitante'];
        $solicitud->usuario_id = auth()->user()->id;
        $solicitud->componente_id = $componente->id;
        $solicitud->fabricacion_id = $data['fabricacion_id'];
        $solicitud->save();
        
        // para la notificacion
        $herramental = Herramental::findOrFail($componente->herramental_id);
        $proyecto = Proyecto::findOrFail($herramental->proyecto_id);
        $cliente = Cliente::findOrFail($proyecto->cliente_id);
        $anio = Anio::findOrFail($cliente->anio_id);
        // 
        
        switch($solicitud->tipo){
            case 'modificacion': //viene del operador
                if($solicitud->fabricacion_id) {
                    $fabricacion = Fabricacion::findOrFail($solicitud->fabricacion_id);
                }

                $notificacion = new Notificacion();
                $notificacion->roles = json_encode(['JEFE DE AREA']);
                $notificacion->url_base = '/enrutador';
                $notificacion->anio_id = $anio->id;
                $notificacion->cliente_id = $cliente->id;
                $notificacion->proyecto_id = $proyecto->id;
                $notificacion->herramental_id = $herramental->id;
                $notificacion->componente_id = $componente->id;
                $notificacion->fabricacion_id = $solicitud->fabricacion_id;
                $notificacion->maquina_id = $fabricacion?$fabricacion->maquina_id: null;
                $notificacion->cantidad = $componente->cantidad;
                $notificacion->descripcion = 'SOLICITUD DE MODIFICACION, AREA: ' . $solicitud->area_solicitante . ', COMENTARIOS:' . $solicitud->comentarios;
                $notificacion->save();

                $users = User::role('JEFE DE AREA')->get();
                foreach ($users as $user) {
                    $user->hay_notificaciones = true;
                    $user->save();
                }
            break;
            // viene del operador 
            case 'retrabajo':
                if($solicitud->fabricacion_id) {
                    $fabricacion = Fabricacion::findOrFail($solicitud->fabricacion_id);
                    $descripcion = 'SOLICITUD DE RETRABAJO, AREA: ' . $solicitud->area_solicitante . ', PROGRAMA:' . $fabricacion->getArchivoShow() .', COMENTARIOS:' . $solicitud->comentarios;
                }else{
                    $descripcion = 'SOLICITUD DE RETRABAJO, AREA: ' . $solicitud->area_solicitante . ', COMENTARIOS:' . $solicitud->comentarios;
                }
                $notificacion = new Notificacion();
                $notificacion->roles = json_encode(['JEFE DE AREA']);
                $notificacion->url_base = '/enrutador';
                $notificacion->anio_id = $anio->id;
                $notificacion->cliente_id = $cliente->id;
                $notificacion->proyecto_id = $proyecto->id;
                $notificacion->herramental_id = $herramental->id;
                $notificacion->componente_id = $componente->id;
                $notificacion->fabricacion_id = $solicitud->fabricacion_id;
                $notificacion->maquina_id = $fabricacion?$fabricacion->maquina_id: null;
                $notificacion->cantidad = $componente->cantidad;
                $notificacion->descripcion = $descripcion;
                $notificacion->save();

                $users = User::role('JEFE DE AREA')->get();
                foreach ($users as $user) {
                    $user->hay_notificaciones = true;
                    $user->save();
                }
            break;
            // viene del matricero
            case 'ajuste':
                $notificacion = new Notificacion();
                $notificacion->roles = json_encode(['JEFE DE AREA']);
                $notificacion->url_base = '/enrutador';
                $notificacion->anio_id = $anio->id;
                $notificacion->cliente_id = $cliente->id;
                $notificacion->proyecto_id = $proyecto->id;
                $notificacion->herramental_id = $herramental->id;
                $notificacion->componente_id = $componente->id;
                $notificacion->cantidad = $componente->cantidad;
                $notificacion->descripcion = 'SOLICITUD DE AJUSTE, AREA: ' . $solicitud->area_solicitante . ', COMENTARIOS:' . $solicitud->comentarios;
                $notificacion->save();

                $users = User::role('JEFE DE AREA')->get();
                foreach ($users as $user) {
                    $user->hay_notificaciones = true;
                    $user->save();
                }
            break;
            // viene del matricero
            case 'rechazo':
                $userIds = User::role('JEFE DE AREA')->pluck('id')->toArray();
                
                $notificacion = new Notificacion();
                $notificacion->roles = json_encode(['JEFE DE AREA']);
                $notificacion->url_base = '/enrutador';
                $notificacion->anio_id = $anio->id;
                $notificacion->cliente_id = $cliente->id;
                $notificacion->proyecto_id = $proyecto->id;
                $notificacion->herramental_id = $herramental->id;
                $notificacion->componente_id = $componente->id;
                $notificacion->cantidad = $componente->cantidad;
                $notificacion->responsables = json_encode($userIds);
                $notificacion->descripcion = 'SE HA RECHAZADO UN COMPONENTE, AREA: ' . $solicitud->area_solicitante . ', COMENTARIOS:' . $solicitud->comentarios;
                $notificacion->save();
                $users = User::whereIn('id', $userIds)->get();
                foreach ($users as $user) {
                    $user->hay_notificaciones = true;
                    $user->save();
                }
                
                $userIds = Fabricacion::where('componente_id', $componente->id)->pluck('usuario_id')->toArray();
                $notificacion = new Notificacion();
                $notificacion->roles = json_encode(['OPERADOR']);
                $notificacion->url_base = null;
                $notificacion->anio_id = $anio->id;
                $notificacion->cliente_id = $cliente->id;
                $notificacion->proyecto_id = $proyecto->id;
                $notificacion->herramental_id = $herramental->id;
                $notificacion->componente_id = $componente->id;
                $notificacion->cantidad = $componente->cantidad;
                $notificacion->responsables = json_encode($userIds);
                $notificacion->descripcion = 'SE HA RECHAZADO UN COMPONENTE, AREA: ' . $solicitud->area_solicitante  . ', COMENTARIOS:' . $solicitud->comentarios;;
                $notificacion->save();
                $users = User::whereIn('id', $userIds)->get();
                foreach ($users as $user) {
                    $user->hay_notificaciones = true;
                    $user->save();
                }
                
                $user = $componente->programador_id;
                $notificacion = new Notificacion();
                $notificacion->roles = json_encode(['PROGRAMADOR']);
                $notificacion->url_base = '/visor-programador';
                $notificacion->anio_id = $anio->id;
                $notificacion->cliente_id = $cliente->id;
                $notificacion->proyecto_id = $proyecto->id;
                $notificacion->herramental_id = $herramental->id;
                $notificacion->componente_id = $componente->id;
                $notificacion->cantidad = $componente->cantidad;
                $notificacion->responsables = json_encode([$user->id]);
                $notificacion->descripcion = 'SE HA RECHAZADO UN COMPONENTE, AREA: ' . $solicitud->area_solicitante  . ', COMENTARIOS:' . $solicitud->comentarios;;
                $notificacion->save();
                $user->hay_notificaciones = true;
                $user->save();
            break;
        }

        return response()->json([
            'success' => true,
        ], 200);
    }
    public function obtenerSolicitudes($componente_id){
        $solicitudes = Solicitud::where('componente_id', $componente_id)->get();
        return response()->json([
            'solicitudes' => $solicitudes,
            'success' => true,
        ], 200);
    }
    public function obtenerPruebasDiseno($herramental_id){
        $pruebas = PruebaDiseno::where('herramental_id', $herramental_id)->get();
         return response()->json([
            'pruebas' => $pruebas,
            'success' => true,
        ], 200);
    }
    public function generarPruebaDiseno(Request $request, $herramental_id) {
        $ultimaPrueba = PruebaDiseno::where('herramental_id', $herramental_id)->orderBy('id', 'desc')->first();
        $numeroPrueba = $ultimaPrueba ? intval(substr($ultimaPrueba->nombre, 7)) + 1 : 1;
        $nombrePrueba = 'Prueba ' . $numeroPrueba;

        $prueba = new PruebaDiseno();
        $prueba->nombre = $nombrePrueba;
        $prueba->fecha_inicio = date('Y-m-d H:i');
        $prueba->herramental_id = $herramental_id;
        $prueba->liberada = false;
        $prueba->usuario_id = auth()->user()->id;
        $prueba->save();

        $herramental = Herramental::findOrFail($herramental_id);
        $herramental->estatus_pruebas_diseno = 'proceso';
        $herramental->save();

        return response()->json([
            'success' => true,
            'id' => $prueba->id,
        ], 200);
    }
    public function guardarPruebaDiseno(Request $request, $prueba_id, $liberar){
        $liberar = filter_var($liberar, FILTER_VALIDATE_BOOLEAN);
        $prueba = PruebaDiseno::findOrFail($prueba_id);
        $data = json_decode($request->data, true);
        $herramental = Herramental::findOrFail($prueba->herramental_id);

        $prueba->descripcion = $data['descripcion'];
        $prueba->involucrados = $data['involucrados'];
        $prueba->herramienta_medicion = $data['herramienta_medicion'];
        $prueba->hallazgos = $data['hallazgos'];
        $prueba->plan_accion = $data['plan_accion'];
        $prueba->altura_cierre = $data['altura_cierre'];
        $prueba->altura_medidas = $data['altura_medidas'];
        $prueba->checklist = json_encode($data['checklist']);
        $prueba->save();

        $archivo = $request->file('archivo_dimensional');
        if ($archivo) {
            $name = time() . '_' . $archivo->getClientOriginalName();
            Storage::disk('public')->put("pruebas-diseno/" . $name, \File::get($archivo));
            $prueba->archivo_dimensional = $name;
            $prueba->save();
        }

        if($liberar){
            $prueba->fecha_liberada = date('Y-m-d H:i');
            $prueba->liberada = true;
            $prueba->save();
        }

        return response()->json([
            'success' => true,
        ], 200);
        
    }
    public function liberarHerramentalPruebasDiseno($herramental_id) {
        $herramental = Herramental::findOrFail($herramental_id);
        $herramental->estatus_pruebas_diseno = 'finalizado';
        $herramental->save();

        $proyecto = Proyecto::findOrFail($herramental->proyecto_id);
        $cliente = Cliente::findOrFail($proyecto->cliente_id);
        $anio = Anio::findOrFail($cliente->anio_id);

        $notificacion = new Notificacion();
        $notificacion->roles = json_encode(['PROCESOS']);
        $notificacion->url_base = '/pruebas-proceso';
        $notificacion->anio_id = $anio->id;
        $notificacion->cliente_id = $cliente->id;
        $notificacion->proyecto_id = $proyecto->id;
        $notificacion->herramental_id = $herramental->id;
        $notificacion->descripcion = 'SE HA LIBERADO UN HERRAMENTAL PARA PRUEBAS DE PROCESO.';
        $notificacion->save();

        $usuarios = User::role('PROCESOS')->get();
        foreach ($usuarios as $usuario) {
            $usuario->hay_notificaciones = true;
            $usuario->save();
        }

        return response()->json([
            'success' => true,
        ], 200);
    }
    public function liberarHerramentalPruebasProceso($herramental_id){
        $herramental = Herramental::findOrFail($herramental_id);
        $herramental->estatus_pruebas_proceso = 'finalizado';
        $herramental->fecha_terminado = date('Y-m-d H:i');
        $herramental->save();

        $proyecto = Proyecto::findOrFail($herramental->proyecto_id);
        $cliente = Cliente::findOrFail($proyecto->cliente_id);
        $anio = Anio::findOrFail($cliente->anio_id);

        $notificacion = new Notificacion();
        $notificacion->roles = json_encode(['JEFE DE AREA']);
        $notificacion->url_base = '/visor-avance-hr';
        $notificacion->anio_id = $anio->id;
        $notificacion->cliente_id = $cliente->id;
        $notificacion->proyecto_id = $proyecto->id;
        $notificacion->herramental_id = $herramental->id;
        $notificacion->descripcion = 'EL HERRAMENTAL HA SIDO FINALIZADO';
        $notificacion->save();

        $usuarios = User::role('JEFE DE AREA')->get();
        foreach ($usuarios as $usuario) {
            $usuario->hay_notificaciones = true;
            $usuario->save();
        }

        return response()->json([
            'success' => true,
        ], 200);

    }
    public function obtenerPruebasProceso($herramental_id){
         $pruebas = PruebaProceso::where('herramental_id', $herramental_id)->get();
         return response()->json([
            'pruebas' => $pruebas,
            'success' => true,
        ], 200);
    }
    public function generarPruebaProceso(Request $request, $herramental_id) {

        $ultimaPrueba = PruebaProceso::where('herramental_id', $herramental_id)->orderBy('id', 'desc')->first();
        $numeroPrueba = $ultimaPrueba ? intval(substr($ultimaPrueba->nombre, 7)) + 1 : 1;
        $nombrePrueba = 'Prueba ' . $numeroPrueba;

        $prueba = new PruebaProceso();
        $prueba->nombre = $nombrePrueba;
        $prueba->fecha_inicio = date('Y-m-d H:i');
        $prueba->herramental_id = $herramental_id;
        $prueba->liberada = false;
        $prueba->usuario_id = auth()->user()->id;
        $prueba->save();

        $herramental = Herramental::findOrFail($herramental_id);
        $herramental->estatus_pruebas_proceso = 'proceso';
        $herramental->save();

        return response()->json([
            'success' => true,
            'id' => $prueba->id,
        ], 200);
    }
    public function guardarPruebaProceso(Request $request, $prueba_id, $liberar){
        $liberar = filter_var($liberar, FILTER_VALIDATE_BOOLEAN);
        $prueba = PruebaProceso::findOrFail($prueba_id);
        $data = json_decode($request->data, true);
        $herramental = Herramental::findOrFail($prueba->herramental_id);

        $prueba->lista_refacciones = $data['lista_refacciones'];
        $prueba->kit_conversion = $data['kit_conversion'];
        $prueba->descripcion = $data['descripcion'];
        $prueba->comentarios = $data['comentarios'];
        $prueba->plan_accion = $data['plan_accion'];
        $prueba->save();

        $archivo = $request->file('archivo');
        if ($archivo) {
            $name = time() . '_' . $archivo->getClientOriginalName();
            Storage::disk('public')->put("pruebas-proceso/" . $name, \File::get($archivo));
            $prueba->archivo = $name;
            $prueba->save();
        }

        $foto = $request->file('foto');
        if ($foto) {
            $name = time() . '_' . $foto->getClientOriginalName();
            Storage::disk('public')->put("pruebas-proceso/" . $name, \File::get($foto));
            $prueba->foto = $name;
            $prueba->save();
        }

        if($liberar){
            $prueba->fecha_liberada = date('Y-m-d H:i');
            $prueba->liberada = true;
            $prueba->save();

        }

        return response()->json([
            'success' => true,
        ], 200);
        
    }
    public function misSolicitudesExternas() {
        $solicitudes = SolicitudExterna::where('solicitante_id', auth()->user()->id)->get();

        return response()->json([
            'solicitudes' => $solicitudes,
            'success' => true,
        ], 200);
    }
    public function generarOrdenTrabajo(Request $request) {
        $data = json_decode($request->data, true);

        $ordenTrabajo = new SolicitudExterna();
        $ordenTrabajo->fecha_solicitud = $data['fecha_solicitud'];
        $ordenTrabajo->fecha_deseada_entrega = $data['fecha_deseada_entrega'];
        $ordenTrabajo->fecha_real_entrega = null;
        $ordenTrabajo->solicitante_id = $data['solicitante_id'];
        $ordenTrabajo->area_solicitud = $data['area_solicitud'];
        $ordenTrabajo->numero_hr = $data['numero_hr'];
        $ordenTrabajo->numero_componente = $data['numero_componente'];
        $ordenTrabajo->cantidad = $data['cantidad'];
        $ordenTrabajo->tratamiento_termico = $data['tratamiento_termico'];
        $ordenTrabajo->comentarios = $data['comentarios'];
        $ordenTrabajo->desde = $data['desde'];
        $ordenTrabajo->material_id = $data['material_id'];
        $ordenTrabajo->save();

        if ($request->hasFile('archivo_2d')) {
            $file2D = $request->file('archivo_2d');
            $name2D = uniqid() . '_' . $file2D->getClientOriginalName();
            Storage::disk('public')->put('ordenes_trabajo/' . $name2D, \File::get($file2D));
            $ordenTrabajo->archivo_2d = $name2D;
        }

        if ($request->hasFile('archivo_3d')) {
            $file3D = $request->file('archivo_3d');
            $name3D = uniqid() . '_' . $file3D->getClientOriginalName();
            Storage::disk('public')->put('ordenes_trabajo/' . $name3D, \File::get($file3D));
            $ordenTrabajo->archivo_3d = $name3D;
        }

        if ($request->hasFile('dibujo')) {
            $fileDibujo = $request->file('dibujo');
            $nameDibujo = uniqid() . '_' . $fileDibujo->getClientOriginalName();
            Storage::disk('public')->put('ordenes_trabajo/' . $nameDibujo, \File::get($fileDibujo));
            $ordenTrabajo->dibujo = $nameDibujo;
        }
        $ordenTrabajo->save();

        $anio = Anio::firstOrCreate(['nombre' => date('Y')]);
        $cliente = Cliente::firstOrCreate(['nombre' => 'ORDENES EXTERNAS'], ['anio_id' => $anio->id]);
        $nombreProyecto = auth()->user()->id . '.'.  auth()->user()->nombre_completo;
        
        $proyecto = Proyecto::firstOrCreate(
            ['nombre' => $nombreProyecto, 'cliente_id' => $cliente->id]
        );

        $nombreHerramental = "HR" . $data['numero_hr'];
        $herramental = Herramental::where('nombre', $nombreHerramental)
            ->where('proyecto_id', $proyecto->id)
            ->first();

        if (!$herramental) {
            $herramental = new Herramental();
            $herramental->nombre = $nombreHerramental;
            $herramental->proyecto_id = $proyecto->id;
            $herramental->estatus_ensamble = 'inicial';
            $herramental->estatus_pruebas_diseno = 'inicial';
            $herramental->estatus_pruebas_proceso = 'inicial';
            $herramental->save();
        }
        
        $nombreComponente = $nombreHerramental . '-' . $data['numero_componente'];
        $componenteExistente = Componente::where('nombre', $nombreComponente)->where('herramental_id', $herramental->id)->exists();

        if ($componenteExistente) {
            $ordenTrabajo->delete();
            return response()->json([
                'success' => false,
                'message' => 'El componente ya existe, verifique el numero de componente e intentelo nuevamente',
            ]);
        }

        $nuevoComponente = new Componente();
        $nuevoComponente->nombre = $herramental->nombre. '-' .$data['numero_componente'];
        $nuevoComponente->version = 1;
        $nuevoComponente->cantidad = $data['cantidad'];
        $nuevoComponente->fecha_cargado = date('Y-m-d H:i');
        $nuevoComponente->prioridad = 'A';
        $nuevoComponente->refabricado = false;
        $nuevoComponente->refaccion = false;
        $nuevoComponente->es_compra = false;
        $nuevoComponente->cargado = true;
        $nuevoComponente->comprado = false;
        $nuevoComponente->programado = false;
        $nuevoComponente->cortado = false;
        $nuevoComponente->enrutado = false;
        $nuevoComponente->ensamblado = false;
        $nuevoComponente->estatus_corte = 'inicial';
        $nuevoComponente->estatus_programacion ='inicial';
        $nuevoComponente->estatus_fabricacion = 1;
        $nuevoComponente->herramental_id = $herramental->id;
        $nuevoComponente->material_id = $data['material_id'];

        $rutaBase = "{$herramental->proyecto_id}/{$herramental->id}/componentes/";
        Storage::disk('public')->makeDirectory($rutaBase);
        
        if ($ordenTrabajo->archivo_2d) {
            $nuevoNombre2D = $this->generarNuevoNombre($ordenTrabajo->archivo_2d);
            Storage::disk('public')->copy(
                "ordenes_trabajo/{$ordenTrabajo->archivo_2d}", // Ruta correcta de origen
                "{$rutaBase}{$nuevoNombre2D}" // Ruta de destino
            );
            $nuevoComponente->archivo_2d = $nuevoNombre2D;
        }

        if ($ordenTrabajo->archivo_3d) {
            $nuevoNombre3D = $this->generarNuevoNombre($ordenTrabajo->archivo_3d);
            Storage::disk('public')->copy(
                "ordenes_trabajo/{$ordenTrabajo->archivo_3d}", 
                "{$rutaBase}{$nuevoNombre3D}"
            );
            $nuevoComponente->archivo_3d = $nuevoNombre3D;
        }

        if ($ordenTrabajo->dibujo) {
            $nuevoNombreExplosionado = $this->generarNuevoNombre($ordenTrabajo->dibujo);
            Storage::disk('public')->copy(
                "ordenes_trabajo/{$ordenTrabajo->dibujo}", 
                "{$rutaBase}{$nuevoNombreExplosionado}"
            );
            $nuevoComponente->archivo_explosionado = $nuevoNombreExplosionado;
        }
        $nuevoComponente->save();
        $ordenTrabajo->componente_id = $nuevoComponente->id;
        $ordenTrabajo->save();

        
        $notificacion = new Notificacion();
        $notificacion->roles = json_encode(['JEFE DE AREA']);
        $notificacion->url_base = '/enrutador';
        $notificacion->anio_id = $anio->id;
        $notificacion->cliente_id = $cliente->id;
        $notificacion->proyecto_id = $proyecto->id;
        $notificacion->herramental_id = $herramental->id;
        $notificacion->componente_id = $nuevoComponente->id;
        $notificacion->cantidad = $nuevoComponente->cantidad;
        $notificacion->descripcion = 'SE HA LIBERADO UN NUEVO COMPONENTE PARA ENRUTAMIENTO DESDE ORDENES DE TRABAJO EXTERNAS.';
        $notificacion->save();

        $users = User::role('JEFE DE AREA')->get();
        foreach ($users as $user) {
            $user->hay_notificaciones = true;
            $user->save();
        }

        return response()->json([
            'success' => true,
        ], 200);
    }
    public function editarOrdenTrabajo(Request $request, $id){
        $data = json_decode($request->data, true);
        $ordenTrabajo = SolicitudExterna::findOrFail($id);
        
        $componente = Componente::findOrFail($ordenTrabajo->componente_id);
        $herramental = Herramental::findOrFail($componente->herramental_id);
        $proyecto = Proyecto::findOrFail($herramental->proyecto_id);
        $cliente = Cliente::findOrFail($proyecto->cliente_id);
        $anio = Anio::findOrFail($cliente->anio_id);

        $ordenTrabajo->fecha_deseada_entrega = $data['fecha_deseada_entrega'];
        $ordenTrabajo->comentarios = $data['comentarios'];
        $ordenTrabajo->save();

        $rutaBaseSolicitud = "ordenes_trabajo/";
        $rutaBaseComponente = "{$proyecto->id}/{$herramental->id}/componentes/";

        Storage::disk('public')->makeDirectory($rutaBaseComponente); // Asegurar directorio

        if ($request->hasFile('archivo_2d')) {
            if ($componente->archivo_2d) {
                Storage::disk('public')->delete("{$rutaBaseComponente}{$componente->archivo_2d}");
            }
            $archivo2D = $request->file('archivo_2d');
            $nombre2D = $this->generarNuevoNombre($archivo2D->getClientOriginalName());
            $archivo2D->storeAs($rutaBaseSolicitud, $nombre2D, 'public');
            Storage::disk('public')->copy("{$rutaBaseSolicitud}{$nombre2D}", "{$rutaBaseComponente}{$nombre2D}");
            $componente->archivo_2d = $nombre2D;
            $ordenTrabajo->archivo_2d = $nombre2D;
        }

        if ($request->hasFile('archivo_3d')) {
            if ($componente->archivo_3d) {
                Storage::disk('public')->delete("{$rutaBaseComponente}{$componente->archivo_3d}");
            }
            $archivo3D = $request->file('archivo_3d');
            $nombre3D = $this->generarNuevoNombre($archivo3D->getClientOriginalName());
            $archivo3D->storeAs($rutaBaseSolicitud, $nombre3D, 'public');
            Storage::disk('public')->copy("{$rutaBaseSolicitud}{$nombre3D}", "{$rutaBaseComponente}{$nombre3D}");
            $componente->archivo_3d = $nombre3D;
            $ordenTrabajo->archivo_3d = $nombre3D;
        }

        if ($request->hasFile('dibujo')) {
            if ($componente->archivo_explosionado) {
                Storage::disk('public')->delete("{$rutaBaseComponente}{$componente->archivo_explosionado}");
            }
            $archivoExplosionado = $request->file('dibujo');
            $nombreExplosionado = $this->generarNuevoNombre($archivoExplosionado->getClientOriginalName());
            $archivoExplosionado->storeAs($rutaBaseSolicitud, $nombreExplosionado, 'public');
            Storage::disk('public')->copy("{$rutaBaseSolicitud}{$nombreExplosionado}", "{$rutaBaseComponente}{$nombreExplosionado}");
            $componente->archivo_explosionado = $nombreExplosionado;
            $ordenTrabajo->dibujo = $nombreExplosionado;
        }
        $componente->save();
        $ordenTrabajo->save();

        $notificacion = new Notificacion();
        $notificacion->roles = json_encode(['JEFE DE AREA']);
        $notificacion->url_base = '/enrutador';
        $notificacion->anio_id = $anio->id;
        $notificacion->cliente_id = $cliente->id;
        $notificacion->proyecto_id = $proyecto->id;
        $notificacion->herramental_id = $herramental->id;
        $notificacion->componente_id = $componente->id;
        $notificacion->cantidad = $componente->cantidad;
        $notificacion->descripcion = 'EL DISEÑO DE UN COMPONENTE EXTERNO HA SIDO MODIFICADO, SE REQUIERE UN RETRABAJO / REFABRICACIÓN';
        $notificacion->save();

        $users = User::role('JEFE DE AREA')->get();
        foreach ($users as $user) {
            $user->hay_notificaciones = true;
            $user->save();
        }

        return response()->json([
            'success' => true,
        ], 200);
    }
    public function obtenerSolicitudExterna($componente_id){
        $solicitud = SolicitudExterna::where('componente_id', $componente_id)->first();
        if ($solicitud) {
            $solicitudArray = $solicitud->toArray(); // Convertir a array
            unset($solicitudArray['componente']); // Eliminar el campo
        } else {
            $solicitudArray = null;
        }

        return response()->json([
            'solicitud' => $solicitudArray,
            'success' => true,
        ], 200);
    }
    public function obtenerHerramentales(){
        $herramentales = Herramental::all();

        return response()->json([
            'success' => true,
            'herramentales' => $herramentales
        ]);
    }
    public function obtenerSolicitudesEnsamble($herramental_id) {
        $solicitudes = Solicitud::join('componentes', 'solicitudes.componente_id', '=', 'componentes.id')
            ->where('componentes.herramental_id', $herramental_id)
            ->where('solicitudes.area_solicitante', 'ENSAMBLE')
            ->select('solicitudes.*') // Solo selecciona los campos de Solicitud
            ->get();

        return response()->json([
            'success' => true,
            'solicitudes' => $solicitudes
        ]);
    }


}

