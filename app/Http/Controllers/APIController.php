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
use App\ComponenteCompra;
use App\PruebaProceso;
use App\Hoja;
use App\MovimientoHoja;
use App\SeguimientoTiempo;
use App\Solicitud;
use App\Puesto;
use App\SolicitudExterna;
use App\SolicitudAfilado;
use App\UnidadDeMedida;
use App\DocumentacionTecnica;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
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
    protected function reglasTiposDeArchivo(){
        return [
            'solidworks' => [
                'ext' => ['sldprt', 'sldasm'],
                 'mime' => [
                    'application/x-sldworks',   
                    'application/octet-stream', 
                    'application/solidworks'    
                ],
                'max' => 700
            ],
            'pdf' => [
                'ext' => ['pdf'],
                'mime' => ['application/pdf'],
                'max' => 200
            ],
            'imagen' => [
                'ext' => ['jpg', 'jpeg', 'png'],
                'mime' => ['image/jpeg', 'image/jpg', 'image/png'],
                'max' => 50
            ],
            'texto' => [
                'ext' => ['txt', 'nc', 'ncc', 'mpf'],
                'mime' => ['text/plain'],
                'max' => 20
            ],
            'autocad' => [
                'ext' => ['dxf', 'dwg'],
                'mime' => [
                    'image/vnd.dxf',
                    'image/vnd.dwg',
                    'application/acad',
                    'application/x-acad',
                    'application/autocad_dwg',
                    'application/x-dwg',
                    'application/octet-stream' 
                ],
                'max' => 700
            ],
        ];
    }
    protected function validarArchivo($file){
        if (!$file || !$file->isValid()) {
            return [
                'success' => false,
                'message' => 'Archivo inválido o no se pudo cargar.',
            ];
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $mime = $file->getMimeType();
        $sizeMB = $file->getSize() / 1024 / 1024;

        $rules = $this->reglasTiposDeArchivo(); 

        foreach ($rules as $tipo => $config) {
            if (in_array($mime, $config['mime'])) {
                if ($sizeMB <= $config['max']) {
                    return [
                        'success' => true,
                        'validado' => true,
                        'tipo_detectado' => $tipo,
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => "El archivo supera el tamaño máximo de {$config['max']} MB.",
                    ];
                }
            }
        }

        return [
            'success' => false,
            'message' => 'Tipo de archivo o MIME no permitido.',
        ];
    }
    public function obtenerPuestos(){
        $puestos = Puesto::orderBy('nombre', 'asc')->get();

        return response()->json([
            'puestos' => $puestos,
            'success' => true
        ]);
    }
    public function guardarPuesto(Request $request){
        $datos = request()->json()->all();

        $puesto = new Puesto();
        $puesto->nombre = $datos['nombre'];
        $puesto->pago_hora = $datos['pago_hora'];
        $puesto->save();

        return response()->json([
            'success' => true,
        ]);
    }
    public function editarPuesto($id){
        $datos = request()->json()->all();
        $puesto = Puesto::findOrFail($id);
        $puesto->nombre = $datos['nombre'];
        $puesto->pago_hora = $datos['pago_hora'];
        $puesto->save();

        return response()->json([
            'success' => true,
        ]);
    }
    public function editarCostoMaquina($id){
        $datos = request()->json()->all();
        $maquina = Maquina::findOrFail($id);
        $maquina->pago_hora = $datos['pago_hora'];
        $maquina->save();

        return response()->json([
            'success' => true,
        ]);
    }
    public function eliminarPuesto($id){
        $puesto = Puesto::findOrFail($id);
        if (User::where('puesto_id', $id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el puesto porque hay usuarios asignados a él.'
            ], 400);
        }
        $puesto->delete();

        return response()->json([
            'success' => true,
            'puesto' => $puesto
        ]);
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
        $tipos = [ 'DIRECCION', 'ALMACENISTA', 'AUXILIAR DE DISEÑO', 'JEFE DE AREA', 'PROGRAMADOR', 'OPERADOR', 'MATRICERO', 'FINANZAS', 'PROYECTOS', 'PROCESOS', 'EXTERNO', 'DISEÑO', 'HERRAMENTALES', 'MANTENIMIENTO', 'INFRAESTRUCTURA', 'METROLOGIA'];
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

        $user = User::where('codigo_acceso', $datos['codigo_acceso'])->first();

        if ($user) {
            return response()->json([
                'title' => 'Error al registrarse',
                'message' => 'El código de acceso ya están registrados.',
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
        $user->puesto_id = $datos['puesto_id'];
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
        $user->puesto_id = $datos['puesto_id'];
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

        $relaciones = [ 
            'componentesMatricero' => 'componentes',
            'componentesProgramador' => 'componentes',
            'fabricaciones' => 'fabricaciones',
            'documentos' => 'documentos',
            'notificaciones' => 'notificaciones',
            'solicitudes' => 'solicitudes',
            'pruebasDeProceso' => 'pruebas de proceso',
            'pruebasDeDiseno' => 'pruebas de diseño',
        ];
        
        foreach ($relaciones as $relacion => $nombre) {
            if ($user->$relacion()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => "No se puede eliminar el usuario porque tiene registros en {$nombre}."
                ], 400);
            }
        }
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
        $maquina->requiere_programa = $datos['requiere_programa'];
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
        $maquina->requiere_programa = $datos['requiere_programa'];
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
        if($cliente == '-1')
            $proyectos = Proyecto::all();
        else
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
            ->where(function ($query) {
                  $query->where('cancelado', false)
                        ->orWhereNull('cancelado');
            })
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
            ->where(function ($query) {
                  $query->where('cancelado', false)
                        ->orWhereNull('cancelado');
            })
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
        $existe = Herramental::where('proyecto_id', $proyecto_id)
            ->where('nombre', $request->input('nombre'))
            ->exists();

        if ($existe) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe un herramental con ese nombre en este proyecto.',
            ]);
        }
        
        try {
            DB::beginTransaction();
            $herramental = new Herramental();
            $herramental->nombre = $request->input('nombre');
            $herramental->proyecto_id = $proyecto_id;
            $herramental->estatus_ensamble = 'inicial';
            $herramental->estatus_pruebas_diseno = 'inicial';
            $herramental->estatus_pruebas_proceso = 'inicial';
            $herramental->save();

            if ($request->hasFile('archivo')) {
                $file = $request->file('archivo');

                // Validación del archivo
                $resultado = $this->validarArchivo($file);
                if (!$resultado['success']) {
                    return response()->json([
                        'success' => false,
                        'message' => $resultado['message'],
                    ], 200);
                }

                $name = uniqid().'_'.$file->getClientOriginalName();
                Storage::disk('public')->put($proyecto_id . '/' . $herramental->id . '/formato/' . $name, \File::get($file));
                $herramental->archivo = $name;
                $herramental->save();
            }

            DB::commit();
            return response()->json([
                'id' => $herramental->id,
                'success' => true,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado.',
                'error' => $e->getMessage(), 
            ], 500);
        }
    }
    public function obtenerHerramental($id){
        $herramental = Herramental::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'herramental' => $herramental
        ]);
    }
    public function actualizarHerramental(Request $request, $id, $tipo) { // desde ensamble
        $datos = $request->json()->all();
        
        switch ($tipo) {
            case 'formato':
                $herramental = Herramental::findOrFail($id);
                if ($request->hasFile('archivo')) {
                    $file = $request->file('archivo');

                    $resultado = $this->validarArchivo($file); 
                    if (!$resultado['success']) {
                        return response()->json([
                            'success' => false,
                            'message' => $resultado['message'],
                        ], 200);
                    }


                    $name = uniqid().'_'.$file->getClientOriginalName();
                    Storage::disk('public')->put($herramental->proyecto_id . '/' . $herramental->id . '/formato2/' . $name, \File::get($file));
                    $herramental->archivo2 = $name;
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
                  
                }else{
                    return response()->json([
                        'success' => false,
                        'message' => 'Herramental no se acualizo correctamente',
                    ], 200);
                }
            break;
            case 'formato-finalizado':
                $herramental = Herramental::findOrFail($id);
                if ($request->hasFile('archivo')) {
                    $file = $request->file('archivo');

                    $resultado = $this->validarArchivo($file); 
                    if (!$resultado['success']) {
                        return response()->json([
                            'success' => false,
                            'message' => $resultado['message'],
                        ], 200);
                    }

                    $name = uniqid().'_'.$file->getClientOriginalName();
                    Storage::disk('public')->put($herramental->proyecto_id . '/' . $herramental->id . '/formato2/' . $name, \File::get($file));
                    $herramental->archivo2 = $name;
                    $herramental->save();  
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
                if ($allChecked){
                    $herramental->estatus_ensamble = 'documento';
                    foreach ($checklist as $item) {
                        if (!isset($item['id']))
                            continue; 

                        $comp = Componente::find($item['id']);
                        if ($comp && $comp->es_compra && isset($item['cantidad_sobrantes']) && $item['cantidad_sobrantes'] > 0) {
                            $comp->cantidad_reutilizable = (int)$item['cantidad_sobrantes'];
                            $comp->save();

                            $compCompra = ComponenteCompra::where('nombre', $comp->nombre)->first();
                            if($compCompra){
                                $compCompra->cantidad += (int) $item['cantidad_sobrantes'];
                                $compCompra->save();
                            }else{
                                $compCompra = new ComponenteCompra();
                                $compCompra->nombre = $comp->nombre;
                                $compCompra->descripcion = $comp->descripcion;
                                $compCompra->proveedor = $comp->proveedor;
                                $compCompra->componente_id = $comp->id;
                                $compCompra->cantidad = (int) $item['cantidad_sobrantes'];
                                $compCompra->save();


                            }
                        }
                    }
                }
                $herramental->save();
            break;
            case 'foto':
                $componente = Componente::findOrFail($id);
                if ($request->hasFile('foto')) {
                    $file = $request->file("foto");

                    $resultado = $this->validarArchivo($file); 
                    if (!$resultado['success']) {
                        return response()->json([
                            'success' => false,
                            'message' => $resultado['message'],
                        ], 200);
                    }
                    if ($componente->foto_matricero) {
                        Storage::disk('public')->delete('fotos_matricero/' . $componente->foto_matricero);
                    }
                    $name = uniqid().'_'.$file->getClientOriginalName();
                    Storage::disk('public')->put('fotos_matricero/' . $name, \File::get($file));
                    $componente->foto_matricero = $name;
                    $componente->save();
                }
            break;
            case 'estatus-ensamblado':
                $componente = Componente::findOrFail($id);
                $herramental = Herramental::findOrFail($componente->herramental_id);

                $componente->ensamblado = $datos['ensamblado'];
                if($datos['ensamblado']){
                    $componente->fecha_ensamblado = date('Y-m-d H:i');
                    $componente->matricero_id = auth()->user()->id;
                    if($herramental->inicio_ensamble == null){
                        $herramental->inicio_ensamble = date('Y-m-d H:i');
                        $herramental->save();
                    }

                }else{
                    $componente->fecha_ensamblado = null;
                }
                $componente->save();

                $faltantes = Componente::where('herramental_id', $componente->herramental_id)
                    ->where('refabricado', false)
                    ->where('cargado', true)
                    ->where(function ($query) {
                        $query->where('cancelado', false)
                                ->orWhereNull('cancelado');
                    })
                    ->whereNull('fecha_ensamblado')
                    ->count();

                if($faltantes == 0){
                    $herramental->estatus_ensamble = 'checklist';
                    $herramental->save();

                    $ultimoSeguimiento = SeguimientoTiempo::where('herramental_id', $componente->herramental_id)
                        ->where('accion', 'ensamble')
                        ->orderBy('id', 'desc') 
                        ->first();

                    if ($ultimoSeguimiento && $ultimoSeguimiento->tipo == true) {
                        $seguimiento = new SeguimientoTiempo();
                        $seguimiento->accion_id = 0;
                        $seguimiento->accion = 'ensamble';
                        $seguimiento->tipo = false;
                        $seguimiento->fecha = date('Y-m-d');
                        $seguimiento->hora = date('H:i');
                        $seguimiento->componente_id = $componente->id;
                        $seguimiento->herramental_id = $herramental->id;
                        $seguimiento->usuario_id = auth()->user()->id;
                        $seguimiento->save();
                    }
                }
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
        $fabricacion->checklist_fabricadas = json_encode($data['checklist_fabricadas']);
        $fabricacion->save();
        
        $componente->cuotas_criticas = isset($data['cuotas_criticas']) ? json_encode($data['cuotas_criticas']) : null;
        $componente->save();
        
        $archivo = $request->file('fotografia');
        if ($archivo) {
            $resultado = $this->validarArchivo($archivo); 
            if (!$resultado['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $resultado['message'],
                ], 200);
            }
            $name = time() . '_' . $archivo->getClientOriginalName();
            Storage::disk('public')->put("fabricaciones/" . $name, \File::get($archivo));
            $fabricacion->foto = $name;
            $fabricacion->save();
        }

        
        if ($liberar) {

            $fabricacion->motivo_retraso = $data['motivo_retraso'];
            $fabricacion->estatus_fabricacion = 'detenido';
            $fabricacion->fabricado = true;
            $fabricacion->usuario_id = auth()->user()->id;
            $fabricacion->save();

            $componente->estatus_fabricacion += 1; //puede haber error
            $componente->save();

            $fabricacionesPendientes = Fabricacion::where('componente_id', $componente->id)
                ->where('fabricado', false)
                ->get();
            
            if (!$fabricacionesPendientes->isEmpty()) {
                $nextFabrication = $fabricacionesPendientes->sortBy('orden')->firstWhere('fabricado', false);
                if ($nextFabrication) {
                    $componente->estatus_fabricacion = $nextFabrication->orden;
                    $componente->save();
                }
            }

            $herramental = Herramental::findOrFail($componente->herramental_id);
            $proyecto = Proyecto::findOrFail($herramental->proyecto_id);
            $cliente = Cliente::findOrFail($proyecto->cliente_id);
            $anio = Anio::findOrFail($cliente->anio_id);

            // Si ya no hay fabricaciones pendientes
            if ($fabricacionesPendientes->isEmpty()) {
                if ($componente->requiere_temple && !$componente->fecha_recibido_temple) {          //si requiere temple y no ha sido realizado el temple   
                    
                    $procesos = collect(json_decode($componente->ruta, true));
                    $templeProceso = collect($procesos)->firstWhere('name', 'Templar');

                    if ($templeProceso) {
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
                    if($componente->esComponenteExterno()){    //si es componente externo, refaccion: finalizar el componente
                        
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
                        $notificacion->descripcion = 'EL COMPONENTE EXTERNO O DE REFACCIÓN ESTÁ LISTO';
                        $notificacion->save();
                    }else if($componente->esComponenteAfilado())
                    {
                        $componente->fecha_terminado = date('Y-m-d H:i');
                        $componente->save();

                        $solicitud = SolicitudAfilado::where('componente_id', $componente->id)->first();
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
                        $notificacion->descripcion = 'EL COMPONENTE DE AFILADO ESTÁ LISTO';
                        $notificacion->save();
                    }
                    else{
                        $this->verificarEnsambleOSiguientePaso($componente);
                    }
                }
            } else { //si mis fabricaciones no estan vacías
                $siguienteFabricacion = $fabricacionesPendientes->firstWhere('orden', $componente->estatus_fabricacion);
                if ($siguienteFabricacion ) {
                    if ($componente->requiere_temple && !$componente->fecha_recibido_temple) { //si requiere temple y no ha sido realizado el temple 

                        $procesos = collect(json_decode($componente->ruta, true));
                        $templeProceso = collect($procesos)->firstWhere('name', 'Templar');
                        $siguienteProceso = collect($procesos)->firstWhere('uuid', $siguienteFabricacion->proceso_uuid);
                        
                        $indexTemple = $templeProceso ? array_search($templeProceso['uuid'], array_column($procesos->all(), 'uuid')) : false;
                        $indexSiguiente = array_search($siguienteProceso['uuid'], array_column($procesos->all(), 'uuid'));
                        
                        if ($templeProceso && $indexTemple !== false && $indexTemple < $indexSiguiente) {
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
                        }else{
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
            $ultimoSeguimiento = SeguimientoTiempo::where('componente_id', $componente->id)
                ->where('accion', 'fabricacion')
                ->where('accion_id', $fabricacion->proceso_uuid)
                ->orderBy('id', 'desc') 
                ->first();

            if ($ultimoSeguimiento && $ultimoSeguimiento->tipo == true) {
                $seguimiento = new SeguimientoTiempo();
                $seguimiento->accion_id = isset($fabricacion) ?  $fabricacion->proceso_uuid : 0;
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

        $maquinas = $request->input('maquinas', []);
        $todosIds = collect($maquinas)->pluck('archivo_ids')->flatten()->filter();
        Fabricacion::where('componente_id', $componente_id)
            ->whereNotIn('id', $todosIds)
            ->get()
            ->each(function ($fabricacion) {
                Storage::disk('public')->delete("programas/" . $fabricacion->nombre);
                $fabricacion->delete();
            });

        foreach ($maquinas as $i => $maquinaData) {
            $maquinaId = $maquinaData['maquina_id'];
            $uuid = $maquinaData['uuid'] ?? null;
            
            $archivos = $request->file("maquinas.$i.archivos");

            if ($archivos) {
                foreach ($request->file("maquinas.$i.archivos") as $j => $file) {
                    
                    $resultado = $this->validarArchivo($file);

                    if (!$resultado['success']) {
                        return response()->json([
                            'success' => false,
                            'message' => "Error al subir algunos archivos al sistema:" . $resultado['message'],
                        ], 200);
                    }

                    $archivoId = $maquinaData['archivo_ids'][$j] ?? null;

                    if ($archivoId) {
                        $fabricacion = Fabricacion::find($archivoId);
                        if ($fabricacion) {
                            Storage::disk('public')->delete("programas/" . $fabricacion->nombre);
                            $name = uniqid() . '_' . $file->getClientOriginalName();
                            Storage::disk('public')->put("programas/" . $name, \File::get($file));
                            $fabricacion->archivo = $name;
                            $fabricacion->fabricado = false;
                            $fabricacion->save();
                        }
                    } else {
                        $name = uniqid() . '_' . $file->getClientOriginalName();
                        Storage::disk('public')->put("programas/" . $name, \File::get($file));

                        $fabricacion = new Fabricacion();
                        $fabricacion->componente_id = $componente_id;
                        $fabricacion->maquina_id = $maquinaId;
                        $fabricacion->archivo = $name;
                        $fabricacion->estatus_fabricacion = 'inicial';
                        $fabricacion->fabricado = false;
                        $fabricacion->proceso_uuid = $uuid;
                        $fabricacion->save();
                    }
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
            $componente->fecha_programado = date('Y-m-d H:i');
            $componente->save();
            
            
            $this->ordenarFabricacionesPorRuta($componente);
           
            $fabricacionesOrdenadas = Fabricacion::where('componente_id', $componente_id)
                ->where('fabricado', false)
                ->orderBy('orden', 'asc')
                ->get();
            
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
                    // Caso cuando no hay fabricaciones pendientes
                    $this->verificarTempleOSiguientePaso($componente);
                }
            }
            
            $ultimoSeguimiento = SeguimientoTiempo::where('componente_id', $componente_id)
                ->where('accion', 'programacion')
                ->orderBy('id', 'desc') 
                ->first();

            if ($ultimoSeguimiento && $ultimoSeguimiento->tipo == true) {
                $uuid = $this->obtenerUUID($componente->ruta, "Programar");
                $seguimiento = new SeguimientoTiempo();
                $seguimiento->accion_id = $uuid;
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
    public function ordenarFabricacionesPorRuta($componente){
        $ruta = json_decode($componente->ruta, true);
        if (!$ruta || !is_array($ruta)) {
            return;
        }

        $ordenMap = [];
        foreach ($ruta as $index => $proceso) {
            if (isset($proceso['uuid'])) {
            $ordenMap[$proceso['uuid']] = $index + 1;
            }
        }

        $fabricaciones = Fabricacion::where('componente_id', $componente->id)->get();

        foreach ($fabricaciones as $fabricacion) {
            if (isset($ordenMap[$fabricacion->proceso_uuid])) {
                $fabricacion->orden = $ordenMap[$fabricacion->proceso_uuid];
                $fabricacion->save();
            }
        }
    }
    protected function verificarTempleOSiguientePaso($componente) {
        $herramental = Herramental::findOrFail($componente->herramental_id);
        $proyecto = Proyecto::findOrFail($herramental->proyecto_id);
        $cliente = Cliente::findOrFail($proyecto->cliente_id);
        $anio = Anio::findOrFail($cliente->anio_id);

        // Si requiere temple y no se ha realizado
        if ($componente->requiere_temple && !$componente->fecha_recibido_temple) {
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
        } else {
            $this->verificarEnsambleOSiguientePaso($componente);
        }
    }
    protected function verificarEnsambleOSiguientePaso($componente){
        $herramental = Herramental::findOrFail($componente->herramental_id);
        $proyecto = Proyecto::findOrFail($herramental->proyecto_id);
        $cliente = Cliente::findOrFail($proyecto->cliente_id);
        $anio = Anio::findOrFail($cliente->anio_id);

        if($componente->requiere_ensamble){   //si requiere ensamble, notificar al matricero
            $notificacion = new Notificacion();
            $notificacion->roles = json_encode(['MATRICERO']);
            $notificacion->url_base = '/matricero';
            $notificacion->anio_id = $anio->id;
            $notificacion->cliente_id = $cliente->id;
            $notificacion->proyecto_id = $proyecto->id;
            $notificacion->herramental_id = $herramental->id;
            $notificacion->componente_id = $componente->id;
            $notificacion->descripcion = 'COMPONENTE LISTO PARA ENSAMBLE';
            $notificacion->save();


            $componente->fecha_terminado = date('Y-m-d H:i');
            $componente->save();

            // preparo para pruebas y pruebas
            $herramental->estatus_ensamble = 'inicial';
            $herramental->estatus_pruebas_diseno = 'inicial';
            $herramental->estatus_pruebas_proceso = 'inicial';
            $herramental->save();

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

                foreach ($users as $user) {
                    $user->hay_notificaciones = true;
                    $user->save();
                }
            }
        }elseif($componente->requiere_pruebas){    // el componente no requiere ensamble pero si pruebas: notificar al jefe de area y a diseño

            $componente->fecha_terminado = date('Y-m-d H:i');
            $componente->ensamblado = true;
            $componente->fecha_ensamblado = date('Y-m-d H:i');
            $componente->save();

            // termino ensamble
            $herramental->estatus_ensamble = 'finalizado';
            $herramental->inicio_ensamble = date('Y-m-d H:i');
            $herramental->termino_ensamble = date('Y-m-d H:i');
            
            // preparo para pruebas
            $herramental->estatus_pruebas_diseno = 'inicial';
            $herramental->estatus_pruebas_proceso = 'inicial';
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
        }
        else{   //si no requiere ensamble ni pruebas, finalizar el componente y el herrmental y notificar al jefe de area
           
            $componente->fecha_terminado = date('Y-m-d H:i');
            $componente->ensamblado = true;
            $componente->fecha_ensamblado = date('Y-m-d H:i');
            $componente->save();

            $herramental->estatus_ensamble = 'finalizado';
            $herramental->inicio_ensamble = date('Y-m-d H:i');
            $herramental->termino_ensamble = date('Y-m-d H:i');
            $herramental->estatus_pruebas_diseno = 'finalizado';
            $herramental->estatus_pruebas_proceso = 'finalizado';
            $herramental->fecha_terminado = date('Y-m-d H:i');
            $herramental->save();

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
        }
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
            $componente_liberado = $componente->enrutado;

            if($componente_liberado && !$liberar){
                
                $componente->prioridad = $datos['prioridad'];
                $componente->comentarios = $datos['comentarios'];
                $componente->largo = $this->emptyToNull($datos['largo']);
                $componente->ancho = $this->emptyToNull($datos['ancho']);
                $componente->espesor = $this->emptyToNull($datos['espesor']);
                $componente->longitud = $this->emptyToNull($datos['longitud']);
                $componente->diametro = $this->emptyToNull($datos['diametro']);                              
                $componente->save();

                return response()->json([
                    'success' => true,
                ], 200);
            }

            $componente->prioridad = $datos['prioridad'];            
            $componente->comentarios = $datos['comentarios'];
            $componente->requiere_temple = $datos['requiere_temple'];
            $componente->requiere_pruebas = $datos['requiere_pruebas'];
            $componente->requiere_ensamble = $datos['requiere_ensamble'];
            $componente->programador_id = $datos['programador_id'];
            $componente->ruta = json_encode($datos['ruta']);
            $componente->enrutado = $liberar;
            $componente->largo = $this->emptyToNull($datos['largo']);
            $componente->ancho = $this->emptyToNull($datos['ancho']);
            $componente->espesor = $this->emptyToNull($datos['espesor']);
            $componente->longitud = $this->emptyToNull($datos['longitud']);
            $componente->diametro = $this->emptyToNull($datos['diametro']);
            $componente->save();
            
            if($liberar){
                $herramental = Herramental::findOrFail($componente->herramental_id);
                $proyecto = Proyecto::findOrFail($herramental->proyecto_id);
                $cliente = Cliente::findOrFail($proyecto->cliente_id);
                $anio = Anio::findOrFail($cliente->anio_id);

                $ruta = json_decode($componente->ruta, true); 
                $contieneCorte = collect($ruta)->contains(function ($proceso) {
                    return strtolower($proceso['name']) === 'cortar';
                });

                // si el componente no tiene el proceso de corte pasarlo a cortado automaticamente.
                if(!$contieneCorte){
                    $componente->estatus_corte = 'finalizado';
                    $componente->cortado = true;
                    $componente->save();
                }else{
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
            $nuevoComponente->reusados = $this->emptyToNull($componente['reusados']);
            $nuevoComponente->material_id = $this->emptyToNull($componente['material_id']);
            // $nuevoComponente->fecha_solicitud = $this->emptyToNull($componente['fecha_solicitud']);
            $nuevoComponente->costo_unitario = $this->emptyToNull($componente['costo_unitario']);
            $nuevoComponente->fecha_pedido = $this->emptyToNull($componente['fecha_pedido']);
            $nuevoComponente->fecha_estimada = $this->emptyToNull($componente['fecha_estimada']);
            $nuevoComponente->fecha_real = $this->emptyToNull($componente['fecha_real_liberada']);
            $nuevoComponente->comprado = $this->emptyToNull($componente['fecha_real_liberada']) != null;
            $nuevoComponente->save();
            
            if(!$comprado && $nuevoComponente->comprado){

                $notificacion = new Notificacion();
                $notificacion->roles = json_encode(['MATRICERO']);
                $notificacion->url_base = '/matricero';
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
    public function actualizarMedidasComponente(Request $request, $componente_id){
        $componente = Componente::findOrFail($componente_id);
        $componente->largo = $this->emptyToNull($request->largo);
        $componente->ancho = $this->emptyToNull($request->ancho);
        $componente->espesor = $this->emptyToNull($request->espesor);
        $componente->longitud = $this->emptyToNull($request->longitud);
        $componente->diametro = $this->emptyToNull($request->diametro);
        $componente->save();

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
            $nuevoComponente->fecha_recibido_temple = $this->emptyToNull($componente['fecha_real_temple']);
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
                    }
                    else if($nuevoComponente->esComponenteAfilado()){
                        $nuevoComponente->fecha_terminado = date('Y-m-d H:i');
                        $nuevoComponente->save();

                        $solicitud = SolicitudAfilado::where('componente_id', $nuevoComponente->id)->first();
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
                        $notificacion->descripcion = 'EL COMPONENTE DE AFILADO ESTÁ LISTO';
                        $notificacion->save();
                    }
                    else{
                        $this->verificarEnsambleOSiguientePaso($nuevoComponente);
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
    public function cargarVistaExplosionada(Request $request, $id){
        $herramental = Herramental::findOrFail($id);
        if ($request->hasFile("archivo")) {
            
            $file = $request->file("archivo");
            $resultado = $this->validarArchivo($file); 
            if (!$resultado['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $resultado['message'],
                ], 200);
            }

            if ($herramental->archivo_explosionado){
                Storage::disk('public')->delete("{$herramental->proyecto_id}/{$herramental->id}/componentes/{$herramental->archivo_explosionado}");
            }
            $fileExplosionada = $request->file("archivo");
            $nameExplosionada = uniqid() . '_' . $fileExplosionada->getClientOriginalName();
            Storage::disk('public')->put("{$herramental->proyecto_id}/{$herramental->id}/componentes/{$nameExplosionada}", \File::get($fileExplosionada));
            $herramental->archivo_explosionado = $nameExplosionada;
            $herramental->save();
        }
        return response()->json([
            'success' => true,
        ], 200);
    }
    public function guardarComponentes(Request $request, $herramental_id){
        $herramental = Herramental::findOrFail($herramental_id);
        $componentes = json_decode($request->data, true);

        $componentesExistentes = Componente::where('herramental_id', $herramental_id)->pluck('id')->toArray();
        $validos = true;
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
                    $notificacion->descripcion = 'SE HA GENERADO UNA NUEVA VERSIÓN DEL COMPONENTE DEBIDO A UNA REFABRICACIÓN.';
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
                //aqui estaban las medidas
                $nuevoComponente->es_compra = $this->emptyToNull($componente['es_compra']);
                $nuevoComponente->cantidad = $this->emptyToNull($componente['cantidad']);
                $nuevoComponente->proveedor = $this->emptyToNull($componente['proveedor']);
                $nuevoComponente->descripcion = $this->emptyToNull($componente['descripcion']);
                $nuevoComponente->material_id = $this->emptyToNull($componente['material_id']);
                $nuevoComponente->otro_material = $this->emptyToNull($componente['otro_material']);
                $nuevoComponente->fecha_solicitud = $this->emptyToNull($componente['fecha_solicitud']);
                $nuevoComponente->fecha_pedido = $this->emptyToNull($componente['fecha_pedido']);
                $nuevoComponente->fecha_estimada = $this->emptyToNull($componente['fecha_estimada']);
                $nuevoComponente->fecha_real = $this->emptyToNull($componente['fecha_real']);
                $nuevoComponente->cuotas_criticas = isset($componente['cuotas_criticas']) ? json_encode($componente['cuotas_criticas']) : null;
                $nuevoComponente->costo_unitario = 0;
                $nuevoComponente->reusados = 0;
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

            if (isset($componente['id'])) {
                $idKey = $componente['id'];
            } else {
                $idKey = "tmp_{$index}";
            }

            if ($request->hasFile("files.{$idKey}.vista2D")) {
                $file = $request->file("files.{$idKey}.vista2D");

                $resultado = $this->validarArchivo($file); 

                if (!$resultado['success']) {
                    $validos = false;
                }else{
                    if ($nuevoComponente->archivo_2d)
                        Storage::disk('public')->delete("{$herramental->proyecto_id}/{$herramental->id}/componentes/{$nuevoComponente->archivo_2d}");
                    $file2D = $request->file("files.{$idKey}.vista2D");
                    $name2D = uniqid() . '_' . $file2D->getClientOriginalName();
                    Storage::disk('public')->put("{$herramental->proyecto_id}/{$herramental->id}/componentes/{$name2D}", \File::get($file2D));
                    $nuevoComponente->archivo_2d = $name2D;
                }
            }

           if ($request->hasFile("files.{$idKey}.vista3D")) {
                $file = $request->file("files.{$idKey}.vista3D");
                $resultado = $this->validarArchivo($file); 

                if (!$resultado['success']) {
                    $validos = false;
                }else{
                    if ($nuevoComponente->archivo_3d)
                        Storage::disk('public')->delete("{$herramental->proyecto_id}/{$herramental->id}/componentes/{$nuevoComponente->archivo_3d}");
                    $file3D = $request->file("files.{$idKey}.vista3D");
                    $name3D = uniqid() . '_' . $file3D->getClientOriginalName();
                    Storage::disk('public')->put("{$herramental->proyecto_id}/{$herramental->id}/componentes/{$name3D}", \File::get($file3D));
                    $nuevoComponente->archivo_3d = $name3D;
                }
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
            'validos' => $validos,
        ], 200);
    }
    public function actualizarVersiones($herramental_id){
        $componentes = Componente::where('herramental_id', $herramental_id)
            ->whereNull('cancelado')
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

        $solicitud = new Solicitud();
        $solicitud->tipo = 'retrabajo';
        $solicitud->componente_id = $componente->id;
        $solicitud->comentarios = 'El diseño del componente ha sido modificado, se requiere un retrabajo / refabricación';
        $solicitud->area_solicitante = 'DISEÑO';
        $solicitud->usuario_id = auth()->user()->id;
        $solicitud->save();

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
        
        $cuotasOriginales = json_decode($componente->cuotas_criticas, true); 
        foreach ($cuotasOriginales as &$cuota) {
            $cuota['valor_real'] = ""; // Limpiamos el campo
        }
        $nuevoComponente = new Componente();
        $nuevoComponente->nombre = $componente->nombre;
        $nuevoComponente->cuotas_criticas = $componente->cuotas_criticas ? json_encode($cuotasOriginales) : null;
        $nuevoComponente->cantidad = $componente->cantidad;
        $nuevoComponente->largo = $componente->largo;
        $nuevoComponente->ancho = $componente->ancho;
        $nuevoComponente->espesor = $componente->espesor;
        $nuevoComponente->longitud = $componente->longitud;
        $nuevoComponente->diametro = $componente->diametro;
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
        $nuevoComponente->otro_material = $componente->otro_material;
        $nuevoComponente->requiere_ensamble = $componente->requiere_ensamble;
        $nuevoComponente->requiere_pruebas = $componente->requiere_pruebas;
        
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

        $solicitud = SolicitudExterna::where('componente_id', $componente->id)->first();
        if($solicitud) {
            $solicitud->componente_id = $nuevoComponente->id;
            $solicitud->save();
        }

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
                $componente->fecha_cargado = date('Y-m-d H:i');
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
        $componente->fecha_terminado = date('Y-m-d');
        $componente->cancelado = true;
        $componente->estatus_corte = 'finalizado';
        $componente->estatus_programacion = 'detenido';
        $componente->save();


        $ultimoSeguimiento = SeguimientoTiempo::where('componente_id', $id)
            ->where('accion', 'corte')
            ->orderBy('id', 'desc') 
            ->first();

        if ($ultimoSeguimiento && $ultimoSeguimiento->tipo == true) {
            $uuid = $this->obtenerUUID($componente->ruta, "Cortar");
            $seguimiento = new SeguimientoTiempo();
            $seguimiento->accion_id = $uuid;
            $seguimiento->accion = 'corte';
            $seguimiento->tipo = false;
            $seguimiento->fecha = date('Y-m-d');
            $seguimiento->hora = date('H:i');
            $seguimiento->componente_id = $id;
            $seguimiento->usuario_id = auth()->user()->id;
            $seguimiento->save();
        }

        $ultimoSeguimiento = SeguimientoTiempo::where('componente_id', $componente_id)
            ->where('accion', 'programacion')
            ->orderBy('id', 'desc') 
            ->first();

        if ($ultimoSeguimiento && $ultimoSeguimiento->tipo == true) {
            $uuid = $this->obtenerUUID($componente->ruta, "Programar");
            $seguimiento = new SeguimientoTiempo();
            $seguimiento->accion_id = $uuid;
            $seguimiento->accion = 'programacion';
            $seguimiento->tipo = false;
            $seguimiento->fecha = date('Y-m-d');
            $seguimiento->hora = date('H:i');
            $seguimiento->componente_id = $componente_id;
            $seguimiento->usuario_id = auth()->user()->id;
            $seguimiento->save();
        }

        $notificacion = new Notificacion();
        $notificacion->roles = json_encode(['JEFE DE AREA']);
        $notificacion->url_base = '/enrutador';
        $notificacion->anio_id = $anio->id;
        $notificacion->cliente_id = $cliente->id;
        $notificacion->proyecto_id = $proyecto->id;
        $notificacion->herramental_id = $herramental->id;
        $notificacion->componente_id = $componente->id;
        $notificacion->cantidad = $componente->cantidad;
        $notificacion->descripcion = 'SE HA CANCELADO UN COMPONENTE';
        $notificacion->save();
        $users = User::role('JEFE DE AREA')->get();
        foreach ($users as $user) {
            $user->hay_notificaciones = true;
            $user->save();
        }
        
        if($componente->enrutado){
            $notificacion = new Notificacion();
            $notificacion->roles = json_encode(['ALMACENISTA']);
            $notificacion->url_base = $componente->es_compra ? '/compra-componentes' : '/corte';
            $notificacion->anio_id = $anio->id;
            $notificacion->cliente_id = $cliente->id;
            $notificacion->proyecto_id = $proyecto->id;
            $notificacion->herramental_id = $herramental->id;
            $notificacion->componente_id = $componente->id;
            $notificacion->cantidad = $componente->cantidad;
            $notificacion->descripcion = 'SE HA CANCELADO UN COMPONENTE';
            $notificacion->save();
            $users = User::role('ALMACENISTA')->get();
            foreach ($users as $user) {
                $user->hay_notificaciones = true;
                $user->save();
            }
        }

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
            $componente->fecha_solicitud = date('Y-m-d');
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
        ], 200)
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache')
        ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
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
        ], 200)
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache')
        ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }
    public function verNotificaciones(){
        $user = auth()->user();
        $user->hay_notificaciones = false;
        $user->save();

        return response()->json([
            'success' => true,
        ], 200)
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache')
        ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }
    public function updateAtendida(Request $request, $id){
        $notificacion = Notificacion::findOrFail($id);
        $notificacion->atendida = $request->input('atendida') ? 1 : 0;
        $notificacion->save();

        return response()->json([
            'success' => true,
            'atendida' => $notificacion->atendida
        ]);
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

        try {
            DB::beginTransaction();
             $hoja = new Hoja();
            $hoja->consecutivo = $siguiente;
            $hoja->calidad = $data['calidad'];
            $hoja->espesor = $data['espesor'];
            $hoja->largo_entrada = $data['largo_entrada'];
            $hoja->ancho_entrada = $data['ancho_entrada'];
            $hoja->longitud_entrada = $data['longitud_entrada'];
            $hoja->diametro_entrada = $data['diametro_entrada'];
            $hoja->peso_entrada = $data['peso_entrada'];
            $hoja->largo_saldo = $data['largo_entrada'];
            $hoja->ancho_saldo = $data['ancho_entrada'];
            $hoja->longitud_saldo = $data['longitud_entrada'];
            $hoja->diametro_saldo = $data['diametro_entrada'];
            $hoja->peso_saldo = $data['peso_entrada'];
            $hoja->precio_kilo = $data['precio_kilo'];
            $hoja->fecha_entrada = $data['fecha_entrada'];
            $hoja->fecha_salida = $data['fecha_salida']??null;
            $hoja->material_id = $data['material_id'];
            $hoja->estatus = true;

            if ($request->hasFile('factura')) {
                $file = $request->file('factura');

                $resultado = $this->validarArchivo($file);
                if (!$resultado['success']) {
                    return response()->json([
                        'success' => false,
                        'message' => $resultado['message'],
                    ], 200);
                }
                $name = uniqid().'_'.$file->getClientOriginalName();
                Storage::disk('public')->put('facturas/' . $name, \File::get($file));
                $hoja->factura = $name;
            }
            $hoja->save();
            
            DB::commit();
            return response()->json([
                'success' => true,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado.',
                'error' => $e->getMessage(), 
            ], 500);
        }

    }
    public function cambiarEstatusCorte(Request $request, $id){
        $componente = Componente::findOrFail($id);
        $componente->estatus_corte = $request->estatus;
        $componente->save();
        
        $uuid = $this->obtenerUUID($componente->ruta, "Cortar");
        $seguimiento = new SeguimientoTiempo();
        $seguimiento->accion_id = $uuid;
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

        $uuid = $this->obtenerUUID($componente->ruta, "Programar");
        $seguimiento = new SeguimientoTiempo();
        $seguimiento->accion_id = $uuid;
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
        // $seguimiento->accion_id = $maquina->tipo_proceso;
        $seguimiento->accion_id = $fabricacion->proceso_uuid;
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
    public function cambiarEstatusEnsamble(Request $request, $herramental_id, $componente_id){
        $herramental = Herramental::findOrFail($herramental_id);
        $herramental->estatus_ensamble = $request->estatus;
        $herramental->save();

        $seguimiento = new SeguimientoTiempo();
        $seguimiento->accion_id = 0;
        $seguimiento->accion = 'ensamble';
        $seguimiento->tipo = $request->estatus == 'proceso' ? true : false;
        $seguimiento->fecha = date('Y-m-d');
        $seguimiento->hora = date('H:i');
        $seguimiento->componente_id = $componente_id;
        $seguimiento->herramental_id = $herramental_id;
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
            $uuid = $this->obtenerUUID($componente->ruta, "Cortar");
            $seguimiento = new SeguimientoTiempo();
            $seguimiento->accion_id = $uuid;
            $seguimiento->accion = 'corte';
            $seguimiento->tipo = false;
            $seguimiento->fecha = date('Y-m-d');
            $seguimiento->hora = date('H:i');
            $seguimiento->componente_id = $id;
            $seguimiento->usuario_id = auth()->user()->id;
            $seguimiento->save();
        }


        $hoja = Hoja::findOrFail($request->movimiento['hoja_id']);
        $saldoPeso = $hoja->peso_saldo;
        $hoja->peso_saldo = $request->movimiento['peso'];
        $hoja->largo_saldo = $request->movimiento['largo'];
        $hoja->ancho_saldo = $request->movimiento['ancho'];
        $hoja->longitud_saldo = $request->movimiento['longitud'];
        $hoja->diametro_saldo = $request->movimiento['diametro'];
        $hoja->save();

        if($hoja->peso_saldo <= 0){
            $hoja->estatus = false; 
            $hoja->save();
        }

        $movimiento = new MovimientoHoja();
        $movimiento->largo = $request->movimiento['largo'];
        $movimiento->ancho = $request->movimiento['ancho'];
        $movimiento->peso_inicial = $saldoPeso;
        $movimiento->peso = $request->movimiento['peso'];
        $movimiento->longitud = $request->movimiento['longitud'];
        $movimiento->diametro = $request->movimiento['diametro'];
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
            else{
                $this->verificarTempleOSiguientePaso($componente);
            }
        }

        return response()->json([
            'success' => true,
        ], 200);
    }

    // obtiene el uuid del proceso $tipo de un componente.
    public function obtenerUUID($ruta, $tipo){
        $procesos = json_decode($ruta, true);

        if (!is_array($procesos)) {
            return null;
        }

        foreach ($procesos as $proceso) {
            if (isset($proceso['name']) && strcasecmp($proceso['name'], $tipo) === 0) {
                return $proceso['uuid'] ?? null;
            }
        }

        return null; // si no encontró el tipo
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
                    $uuid = $this->obtenerUUID($componente->ruta, "Cortar");
                    $seguimiento = new SeguimientoTiempo();
                    $seguimiento->accion_id = $uuid;
                    $seguimiento->accion = 'corte';
                    $seguimiento->tipo = false; //lo apago
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
                    // $seguimiento->accion_id = $maquina->tipo_proceso;
                    $seguimiento->accion_id = $fabricacion->proceso_uuid;
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
            ->where('cargado', true)
            ->whereNull('cancelado')
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
        $herramental->estatus_ensamble = 'inicial';
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
            case 'modificacion': //operador
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
            case 'refabricacion': //operador
                if($solicitud->fabricacion_id) {
                    $fabricacion = Fabricacion::findOrFail($solicitud->fabricacion_id);
                }
                $descripcion = 'SOLICITUD DE REFABRICACIÓN, AREA: ' . $solicitud->area_solicitante . ', COMENTARIOS:' . $solicitud->comentarios;
                
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
            case 'retrabajo': //operador
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
            case 'ajuste': //matricero
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

                $herramental->estatus_ensamble = 'inicial';
                $herramental->save();

                $ultimoSeguimiento = SeguimientoTiempo::where('herramental_id', $herramental->id)
                    ->where('accion', 'ensamble')
                    ->orderBy('id', 'desc') 
                    ->first();

                if ($ultimoSeguimiento && $ultimoSeguimiento->tipo == true) {
                    $seguimiento = new SeguimientoTiempo();
                    $seguimiento->accion_id = 0;
                    $seguimiento->accion = 'ensamble';
                    $seguimiento->tipo = false;
                    $seguimiento->fecha = date('Y-m-d');
                    $seguimiento->hora = date('H:i');
                    $seguimiento->componente_id = $componente->id;
                    $seguimiento->herramental_id = $herramental->id;
                    $seguimiento->usuario_id = auth()->user()->id;
                    $seguimiento->save();
                }

                $users = User::role('JEFE DE AREA')->get();
                foreach ($users as $user) {
                    $user->hay_notificaciones = true;
                    $user->save();
                }
            break;
            case 'rechazo': //matricero
                $notificacion = new Notificacion();
                $notificacion->roles = json_encode(['JEFE DE AREA']);
                $notificacion->url_base = '/enrutador';
                $notificacion->anio_id = $anio->id;
                $notificacion->cliente_id = $cliente->id;
                $notificacion->proyecto_id = $proyecto->id;
                $notificacion->herramental_id = $herramental->id;
                $notificacion->componente_id = $componente->id;
                $notificacion->cantidad = $componente->cantidad;
                $notificacion->descripcion = 'SE HA RECHAZADO UN COMPONENTE, AREA: ' . $solicitud->area_solicitante . ', COMENTARIOS:' . $solicitud->comentarios;
                $notificacion->save();
                
                $users = User::role('JEFE DE AREA')->get();
                foreach ($users as $user) {
                    $user->hay_notificaciones = true;
                    $user->save();
                }
                
                $user = User::findOrFail($componente->programador_id);
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

                $herramental->estatus_ensamble = 'inicial';
                $herramental->save();

                $ultimoSeguimiento = SeguimientoTiempo::where('herramental_id', $herramental->id)
                    ->where('accion', 'ensamble')
                    ->orderBy('id', 'desc') 
                    ->first();

                if ($ultimoSeguimiento && $ultimoSeguimiento->tipo == true) {
                    $seguimiento = new SeguimientoTiempo();
                    $seguimiento->accion_id = 0;
                    $seguimiento->accion = 'ensamble';
                    $seguimiento->tipo = false;
                    $seguimiento->fecha = date('Y-m-d');
                    $seguimiento->hora = date('H:i');
                    $seguimiento->componente_id = $componente->id;
                    $seguimiento->herramental_id = $herramental->id;
                    $seguimiento->usuario_id = auth()->user()->id;
                    $seguimiento->save();
                }
            break;
        }

        //Pausar el seguimiento en caso de que haya quedado activo en las fabricaciones
        if($solicitud->fabricacion_id){
            $fabricacion = Fabricacion::findOrFail($solicitud->fabricacion_id);     
            $fabricacion->estatus_fabricacion = 'detenido';
            $fabricacion->save();

            // $maquina = Maquina::findOrFail($fabricacion->maquina_id);
            $ultimoSeguimiento = SeguimientoTiempo::where('componente_id', $componente->id)
                ->where('accion', 'fabricacion')
                // ->where('accion_id', $maquina->tipo_proceso)
                ->where('accion_id', $fabricacion->proceso_uuid)
                ->orderBy('id', 'desc') 
                ->first();
    
            if ($ultimoSeguimiento && $ultimoSeguimiento->tipo == true) {
                $seguimiento = new SeguimientoTiempo();
                // $seguimiento->accion_id = $maquina->tipo_proceso;
                $seguimiento->accion_id = $fabricacion->proceso_uuid;
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
    public function obtenerSolicitudes($componente_id){
        $solicitudes = Solicitud::where('componente_id', $componente_id)->orderBy('created_at', 'DESC')->get();
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
            $resultado = $this->validarArchivo($archivo); 
            
            if (!$resultado['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $resultado['message'],
                ], 200);
            }

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
            $resultado = $this->validarArchivo($archivo); 
            
            if (!$resultado['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $resultado['message'],
                ], 200);
            }

            $name = time() . '_' . $archivo->getClientOriginalName();
            Storage::disk('public')->put("pruebas-proceso/" . $name, \File::get($archivo));
            $prueba->archivo = $name;
            $prueba->save();
        }

        $foto = $request->file('foto');
        if ($foto) {

            $resultado = $this->validarArchivo($foto); 
            if (!$resultado['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $resultado['message'],
                ], 200);
            }

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
    public function misSolicitudesAfilado() {
        $solicitudes = SolicitudAfilado::where('solicitante_id', auth()->user()->id)->get();

        return response()->json([
            'solicitudes' => $solicitudes,
            'success' => true,
        ], 200);
    }
    public function generarOrdenRefaccion(Request $request, $id){
        $data = $request->json()->all();

        $componente = Componente::findOrFail($id);
        $herramental = Herramental::findOrFail($componente->herramental_id);

        $numeroComp = $componente->nombre;
        $pos = strrpos($componente->nombre, '-');
        $numeroComp = substr($componente->nombre, $pos + 1);

        $ruta = $componente->ruta ? json_decode($componente->ruta, true) : [];
        $requiereTemple = false;

        foreach ($ruta as $proceso) {
            if (isset($proceso['name']) && stripos($proceso['name'], 'temple') !== false) {
                $requiereTemple = true;
                break;
            }
        }
        $ordenTrabajo = new SolicitudExterna();
        $ordenTrabajo->fecha_solicitud = date('Y-m-d');
        $ordenTrabajo->fecha_deseada_entrega = $data['fecha_deseada_entrega']; // pedir
        $ordenTrabajo->fecha_real_entrega = null;
        $ordenTrabajo->solicitante_id = $data['solicitante_id']; // pedir
        $ordenTrabajo->area_solicitud = $data['area_solicitud']; // pedir
        $ordenTrabajo->numero_hr = $herramental->nombre;    
        $ordenTrabajo->numero_componente = $numeroComp . 'R';  
        $ordenTrabajo->cantidad = $data['cantidad']; // pedir
        $ordenTrabajo->tratamiento_termico = $requiereTemple;
        $ordenTrabajo->comentarios = $data['comentarios']; // pedir
        $ordenTrabajo->desde = $data['desde']; // pedir
        $ordenTrabajo->material_id = $componente->material_id;  
        $ordenTrabajo->save();

        // Copiar archivo 2D del componente
        if ($componente->archivo_2d) {
            $origen2D = "{$herramental->proyecto_id}/{$herramental->id}/componentes/{$componente->archivo_2d}";
            $nombreNuevo2D = uniqid() . '_' . $componente->archivo_2d;
            $destino2D = "ordenes_trabajo/{$nombreNuevo2D}";
            if (Storage::disk('public')->exists($origen2D)) {
                Storage::disk('public')->copy($origen2D, $destino2D);
                $ordenTrabajo->archivo_2d = $nombreNuevo2D;
            }
        }

        // Copiar archivo 3D del componente
        if ($componente->archivo_3d) {
            $origen3D = "{$herramental->proyecto_id}/{$herramental->id}/componentes/{$componente->archivo_3d}";
            $nombreNuevo3D = uniqid() . '_' . $componente->archivo_3d;
            $destino3D = "ordenes_trabajo/{$nombreNuevo3D}";
            if (Storage::disk('public')->exists($origen3D)) {
                Storage::disk('public')->copy($origen3D, $destino3D);
                $ordenTrabajo->archivo_3d = $nombreNuevo3D;
            }
        }

        // El campo dibujo se deja en null
        $ordenTrabajo->dibujo = null;
        $ordenTrabajo->save();


        $anio = Anio::firstOrCreate(['nombre' => date('Y')]);
        $cliente = Cliente::firstOrCreate(['nombre' => 'REFACCIONES'], ['anio_id' => $anio->id]);
        $nombreProyecto = auth()->user()->id . '. REF '.  auth()->user()->nombre_completo;
        
        $proyecto = Proyecto::firstOrCreate(
            ['nombre' => $nombreProyecto, 'cliente_id' => $cliente->id]
        );

        $nombreHerramental = $herramental->nombre;
        $nuevoHerramental = Herramental::where('nombre', $nombreHerramental)
            ->where('proyecto_id', $proyecto->id)
            ->first();

        if (!$nuevoHerramental) {
            $nuevoHerramental = new Herramental();
            $nuevoHerramental->nombre = $nombreHerramental;
            $nuevoHerramental->archivo_explosionado = $herramental->archivo_explosionado;
            $nuevoHerramental->proyecto_id = $proyecto->id;
            $nuevoHerramental->estatus_ensamble = 'inicial';
            $nuevoHerramental->estatus_pruebas_diseno = 'inicial';
            $nuevoHerramental->estatus_pruebas_proceso = 'inicial';
            $nuevoHerramental->save();
        }

        $baseNombreComponente = $nombreHerramental . '-' . $numeroComp . 'R';
        $nombreComponente = $baseNombreComponente;
        $contador = 1;
        while (Componente::where('nombre', $nombreComponente)->where('herramental_id', $nuevoHerramental->id)->exists()) {
            $nombreComponente = $baseNombreComponente . $contador;
            $contador++;
        }

        $cuotasOriginales = json_decode($componente->cuotas_criticas, true); 
        foreach ($cuotasOriginales as &$cuota) {
            $cuota['valor_real'] = ""; // Limpiamos el campo
        }
        $nuevoComponente = new Componente();
        $nuevoComponente->nombre = $nombreComponente;
        $nuevoComponente->version = 1;
        $nuevoComponente->cantidad = $data['cantidad'];
        $nuevoComponente->ruta = $componente->ruta;
        $nuevoComponente->fecha_cargado = date('Y-m-d H:i');
        $nuevoComponente->largo = $componente->largo;
        $nuevoComponente->ancho = $componente->ancho;
        $nuevoComponente->espesor = $componente->espesor;
        $nuevoComponente->longitud = $componente->longitud;
        $nuevoComponente->diametro = $componente->diametro;
        $nuevoComponente->otro_material = $componente->otro_material;
        $nuevoComponente->cuotas_criticas = $componente->cuotas_criticas ? json_encode($cuotasOriginales) : null;
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
        $nuevoComponente->herramental_id = $nuevoHerramental->id;
        $nuevoComponente->material_id = $componente->material_id;

        $rutaBase = "{$nuevoHerramental->proyecto_id}/{$nuevoHerramental->id}/componentes/";
        Storage::disk('public')->makeDirectory($rutaBase); // Crea la carpeta si no existe

        // Copiar archivo 2D
        if ($ordenTrabajo->archivo_2d && Storage::disk('public')->exists("ordenes_trabajo/{$ordenTrabajo->archivo_2d}")) {
            $nuevoNombre2D = $this->generarNuevoNombre($ordenTrabajo->archivo_2d);
            Storage::disk('public')->copy(
                "ordenes_trabajo/{$ordenTrabajo->archivo_2d}",
                "{$rutaBase}{$nuevoNombre2D}"
            );
            $nuevoComponente->archivo_2d = $nuevoNombre2D;
        }

        // Copiar archivo 3D
        if ($ordenTrabajo->archivo_3d && Storage::disk('public')->exists("ordenes_trabajo/{$ordenTrabajo->archivo_3d}")) {
            $nuevoNombre3D = $this->generarNuevoNombre($ordenTrabajo->archivo_3d);
            Storage::disk('public')->copy(
                "ordenes_trabajo/{$ordenTrabajo->archivo_3d}",
                "{$rutaBase}{$nuevoNombre3D}"
            );
            $nuevoComponente->archivo_3d = $nuevoNombre3D;
        }

        // Copiar archivo dibujo/explosionado
        if ($herramental->archivo_explosionado && Storage::disk('public')->exists("{$rutaBase}{$herramental->archivo_explosionado}")) {
            $nuevoNombreExplosionado = $this->generarNuevoNombre($herramental->archivo_explosionado);
            Storage::disk('public')->copy(
                "{$herramental->proyecto_id}/{$herramental->id}/componentes/",
                "{$rutaBase}{$nuevoNombreExplosionado}"
            );
            $nuevoHerramental->archivo_explosionado = $nuevoNombreExplosionado;
            $nuevoHerramental->save();
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
        $notificacion->herramental_id = $nuevoHerramental->id;
        $notificacion->componente_id = $nuevoComponente->id;
        $notificacion->cantidad = $nuevoComponente->cantidad;
        $notificacion->descripcion = 'SE HA LIBERADO UN NUEVO COMPONENTE PARA REFACCION';
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
    public function generarOrdenTrabajo(Request $request) {
        $data = json_decode($request->data, true);

        try {
                DB::beginTransaction();
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
                    $resultado = $this->validarArchivo($file2D); 
                    if (!$resultado['success']) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => $resultado['message'],
                        ], 200);
                    }
                    
                    $name2D = uniqid() . '_' . $file2D->getClientOriginalName();
                    Storage::disk('public')->put('ordenes_trabajo/' . $name2D, \File::get($file2D));
                    $ordenTrabajo->archivo_2d = $name2D;
                }

                if ($request->hasFile('archivo_3d')) {
                    $file3D = $request->file('archivo_3d');
                    $resultado = $this->validarArchivo($file3D); 
                    if (!$resultado['success']) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => $resultado['message'],
                        ], 200);
                    }

                    $name3D = uniqid() . '_' . $file3D->getClientOriginalName();
                    Storage::disk('public')->put('ordenes_trabajo/' . $name3D, \File::get($file3D));
                    $ordenTrabajo->archivo_3d = $name3D;
                }

                if ($request->hasFile('dibujo')) {
                    $fileDibujo = $request->file('dibujo');
                    $resultado = $this->validarArchivo($fileDibujo); 
                    if (!$resultado['success']) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => $resultado['message'],
                        ], 200);
                    }

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

                $nombreHerramental = $data['numero_hr'];
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
                    $herramental->archivo_explosionado = $nuevoNombreExplosionado;
                    $herramental->save();
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
                DB::commit();
                return response()->json([
                    'success' => true,
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error inesperado.',
                    'error' => $e->getMessage(), 
                ], 500);
        }

        
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

        $solicitud = new Solicitud();
        $solicitud->tipo = 'retrabajo';
        $solicitud->componente_id = $componente->id;
        $solicitud->comentarios = 'El diseño de un componente externo ha sido modificado, se requiere un retrabajo / refabricación';
        $solicitud->area_solicitante = 'DISEÑO';
        $solicitud->usuario_id = auth()->user()->id;
        $solicitud->save();


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

    public function obtenerSolicitudAfilado($componente_id){
        $solicitud = SolicitudAfilado::where('componente_id', $componente_id)->first();
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

    public function obtenerUnidadDeMedida(){
        $medidas = UnidadDeMedida::all();

        return response()->json([
            'medidas' => $medidas,
            'success' => true,
        ], 200);
    }

    public function nuevaUnidadDeMedida(Request $request){        
        try{
            $nuevaMedida = new UnidadDeMedida;
            $nuevaMedida->nombre = $request->nombre;
            $nuevaMedida->abreviatura = $request->abreviatura;
            $nuevaMedida->save();

            return response()->json([
                'success' => true,
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error inesperado al agregar la medida.',
                    'error' => $e->getMessage(), 
                ], 500);
        }
    }

    public function editarUnidadDeMedida(Request $request, $medidaId){        
        try {
            $medida = UnidadDeMedida::findOrFail($medidaId);
            $medida->nombre = $request->nombre;
            $medida->abreviatura = $request->abreviatura;
            $medida->save();
            return response()->json([
                'success' => true,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al agregar medida.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function eliminarUnidadDeMedida($medidaId){
        $medida = UnidadDeMedida::findOrFail($medidaId);
        if ($medida->solicitudAfilado()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar la unidad de medida porque está siendo utilizada por uno o mas registros.'
            ], 200);
        }

        $medida->delete();

        return response()->json([
            'success' => true,
        ]);
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
    public function solicitudAtendida(Request $request, $id){
        $atendida = filter_var($request->atendida, FILTER_VALIDATE_BOOLEAN);
        $solicitud = Solicitud::findOrFail($id);
        $solicitud->atendida = $atendida;
        $solicitud->save();

        return response()->json([
            'success' => true,
        ]);

    }
    public function fechaLimiteHerramental(Request $request, $id){
        $herramental = Herramental::findOrFail($id);
        $anterior = $herramental->fecha_limite;
        $otras = json_decode($herramental->otras_fechas, true) ?? [];
        if ($anterior) {
            $otras[] = $anterior;
        }
        $otras = array_unique($otras);
        $herramental->fecha_limite = $request->fechaLimite;
        $herramental->otras_fechas = json_encode(array_values($otras)); // asegúrate de que sea un array limpio
        $herramental->save();

        return response()->json([
            'success' => true,
        ], 200);
    }
    public function minutosEsperadosFechas($desde, $hasta, $turno){
        // Definición de horarios por turno
        $turnoHorario = [
            1 => ['08:00', '17:30'],
            2 => ['17:31', '23:59']
        ];

        // Validar que el turno exista
        if (!isset($turnoHorario[$turno])) {
            return 0;
        }

        // Parsear horas del turno
        [$inicio, $fin] = $turnoHorario[$turno];
        $inicioTurno = Carbon::createFromFormat('H:i', $inicio);
        $finTurno = Carbon::createFromFormat('H:i', $fin);

        // Calcular minutos por día en ese turno
        $minutosPorDia = $inicioTurno->diffInMinutes($finTurno);

        // Calcular cantidad de días en el rango (inclusive)
        $periodo = CarbonPeriod::create($desde, $hasta);
        $cantidadDias = iterator_count($periodo);

        // Total de minutos esperados en ese turno y rango
        return $cantidadDias * $minutosPorDia;
    }
    public function tiemposMaquinas(Request $request){
        $desde = $request->input('desde'); // formato: '2025-04-11'
        $hasta = $request->input('hasta'); // formato: '2025-04-11'
        $turno = intval($request->input('turno')); // 1 = matutino, 2 = vespertino


        $turnoHorario = [
            1 => ['08:00', '17:30'],
            2 => ['17:31', '23:59']
        ];

        $horarioInicio = $turnoHorario[$turno][0];
        $horarioFin = $turnoHorario[$turno][1];

        // Procesos válidos
        $procesosValidos = [3, 4, 5, 6, 8, 9, 11];

        // Paso 1: obtener máquinas válidas
        $maquinas = Maquina::whereIn('tipo_proceso', $procesosValidos)->get();

        $resultados = [];

        foreach ($maquinas as $maquina) {
            // Paso 2: buscar fabricaciones por máquina
            $fabricaciones = Fabricacion::where('maquina_id', $maquina->id)->pluck('id');
            
            // Paso 3: registros de seguimiento de fabricación en el rango
            $seguimientos = SeguimientoTiempo::whereIn('fabricacion_id', $fabricaciones)
                ->where('accion', 'fabricacion')
                ->whereBetween('fecha', [$desde, $hasta])
                ->orderBy('fabricacion_id')
                ->orderBy('fecha')
                ->orderBy('hora')
                ->get();

            // Paso 4: registros de paros de fabricación en el rango
            $paros = SeguimientoTiempo::whereIn('fabricacion_id', $fabricaciones)
                ->where('accion', 'fabricacion_paro')
                ->whereBetween('fecha', [$desde, $hasta])
                ->orderBy('fabricacion_id')
                ->orderBy('fecha')
                ->orderBy('hora')
                ->get();

            $tiempoTotal = [
                'minutos_matutino' => 0,
                'minutos_vespertino' => 0,
                'minutos_paro_matutino' => 0,
                'minutos_paro_vespertino' => 0,
            ];

            // Agrupar por fabricacion_id para trabajar por cada fabricación
            $agrupados = $seguimientos->groupBy('fabricacion_id');
            $parosAgrupados = $paros->groupBy('fabricacion_id');
            
            $detalleParos  = SeguimientoTiempo::whereIn('fabricacion_id', $fabricaciones)
                ->where('accion', 'fabricacion_paro')
                ->where('tipo', 1)
                ->whereBetween('fecha', [$desde, $hasta])
                ->whereRaw("STR_TO_DATE(hora, '%H:%i') BETWEEN STR_TO_DATE(?, '%H:%i') AND STR_TO_DATE(?, '%H:%i')", [$horarioInicio, $horarioFin])
                ->orderBy('fabricacion_id')
                ->orderBy('fecha')
                ->orderBy('hora')
                ->get()->toArray();

            foreach ($agrupados as $registros) {
                $inicio = null;

                foreach ($registros as $registro) {
                    $fechaHora = Carbon::createFromFormat('Y-m-d H:i', $registro->fecha . ' ' . $registro->hora);

                    if ($registro->tipo == 1) {
                        $inicio = $fechaHora;
                    } elseif ($registro->tipo == 0 && $inicio) {
                        $fin = $fechaHora;

                        // Validar si el rango de tiempo cae dentro del turno solicitado
                        $inicioTurno = Carbon::createFromFormat('Y-m-d H:i', $inicio->format('Y-m-d') . ' ' . $horarioInicio);
                        $finTurno = Carbon::createFromFormat('Y-m-d H:i', $inicio->format('Y-m-d') . ' ' . $horarioFin);

                        // Intersección del tiempo trabajado con el turno
                        $rangoInicio = $inicio->copy()->greaterThan($inicioTurno) ? $inicio : $inicioTurno;
                        $rangoFin = $fin->copy()->lessThan($finTurno) ? $fin : $finTurno;

                        if ($rangoInicio < $rangoFin) {
                            $minutos = $rangoInicio->diffInMinutes($rangoFin);
                            if ($turno == 1) {
                                $tiempoTotal['minutos_matutino'] += $minutos;
                            } else {
                                $tiempoTotal['minutos_vespertino'] += $minutos;
                            }
                        }

                        $inicio = null; // reiniciar para el siguiente ciclo
                    }
                }
            }

            // Ahora, calculamos los minutos de paro
            foreach ($parosAgrupados as $registros) {
                $inicio = null;

                foreach ($registros as $registro) {
                    $fechaHora = Carbon::createFromFormat('Y-m-d H:i', $registro->fecha . ' ' . $registro->hora);

                    if ($registro->tipo == 1) {
                        $inicio = $fechaHora;
                    } elseif ($registro->tipo == 0 && $inicio) {
                        $fin = $fechaHora;

                        // Validar si el rango de tiempo cae dentro del turno solicitado
                        $inicioTurno = Carbon::createFromFormat('Y-m-d H:i', $inicio->format('Y-m-d') . ' ' . $horarioInicio);
                        $finTurno = Carbon::createFromFormat('Y-m-d H:i', $inicio->format('Y-m-d') . ' ' . $horarioFin);

                        // Intersección del tiempo trabajado con el turno
                        $rangoInicio = $inicio->copy()->greaterThan($inicioTurno) ? $inicio : $inicioTurno;
                        $rangoFin = $fin->copy()->lessThan($finTurno) ? $fin : $finTurno;

                        if ($rangoInicio < $rangoFin) {
                            $minutos = $rangoInicio->diffInMinutes($rangoFin);
                            if ($turno == 1) {
                                $tiempoTotal['minutos_paro_matutino'] += $minutos;
                            } else {
                                $tiempoTotal['minutos_paro_vespertino'] += $minutos;
                            }
                        }

                        $inicio = null; // reiniciar para el siguiente ciclo
                    }
                }
            }


            // Convertir minutos a horas:minutos
            $resultados[] = [
                'maquina_id' => $maquina->id,
                'costo_hora' => $maquina->pago_hora ?? 0,
                'maquina' => $maquina->nombre ?? 'Sin nombre',

                'horas_activa' => $turno == 1 ? intdiv($tiempoTotal['minutos_matutino'], 60) : intdiv($tiempoTotal['minutos_vespertino'], 60),
                'minutos_activa' => $turno == 1 ? $tiempoTotal['minutos_matutino'] % 60 : $tiempoTotal['minutos_vespertino'] % 60,

                'horas_paro' => $turno == 1 ? intdiv($tiempoTotal['minutos_paro_matutino'], 60) : intdiv($tiempoTotal['minutos_paro_vespertino'], 60),
                'minutos_paro' => $turno == 1 ? $tiempoTotal['minutos_paro_matutino'] % 60 : $tiempoTotal['minutos_paro_vespertino'] % 60,

                'minutos_activa_totales' => $turno == 1 ? $tiempoTotal['minutos_matutino'] : $tiempoTotal['minutos_vespertino'],
                'minutos_paro_totales' => $turno == 1 ? $tiempoTotal['minutos_paro_matutino'] : $tiempoTotal['minutos_paro_vespertino'],
                'minutos_esperados' =>  $this->minutosEsperadosFechas($desde, $hasta, $turno),
                'detalle_paros'=> $detalleParos
            ];
        }

        return response()->json([
            'success' => true,
            'tiempos' => $resultados
        ]);
    }
    public function retrasosProyecto(Request $request, $proyecto_id){
        $proyecto = Proyecto::findOrFail($proyecto_id);
        $herramentalIds = Herramental::where('proyecto_id', $proyecto_id)->pluck('id')->toArray();
        $componentes = Componente::whereIn('herramental_id', $herramentalIds)->get(['id', 'nombre', 'version', 'herramental_id']);
        $componentesMap = $componentes->keyBy('id');

        $getRutaVisor = function ($herramental_id, $componente_id) {
            $herramental = Herramental::findOrFail($herramental_id);
            $proyecto = Proyecto::findOrFail($herramental->proyecto_id);
            $cliente = Cliente::findOrFail($proyecto->cliente_id);
            $anio = Anio::findOrFail($cliente->anio_id);

            return "?a={$anio->id}&c={$cliente->id}&p={$proyecto->id}&h={$herramental->id}&co={$componente_id}";
        };

        $retrabajos = Solicitud::whereIn('componente_id', $componentes->pluck('id'))
            ->where('tipo', 'retrabajo')
            ->get()
            ->map(function ($solicitud) use ($componentesMap, $getRutaVisor) {
                $componente = $componentesMap[$solicitud->componente_id];
                return [
                    'componente' => $componente->nombre,
                    'version' => $componente->version,
                    'created_at' => $solicitud->created_at->format('Y-m-d H:i'),
                    'area_solicitante' => $solicitud->area_solicitante,
                    'rutaVisor' => $getRutaVisor($componente->herramental_id, $componente->id),
                ];
            });

        $modificaciones = Solicitud::whereIn('componente_id', $componentes->pluck('id'))
            ->where('tipo', 'modificacion')
            ->get()
            ->map(function ($solicitud) use ($componentesMap, $getRutaVisor) {
                $componente = $componentesMap[$solicitud->componente_id];
                return [
                    'componente' => $componente->nombre,
                    'version' => $componente->version,
                    'created_at' => $solicitud->created_at->format('Y-m-d H:i'),
                    'area_solicitante' => $solicitud->area_solicitante,
                    'rutaVisor' => $getRutaVisor($componente->herramental_id, $componente->id),
                ];
            });

        $paros = SeguimientoTiempo::whereIn('componente_id', $componentes->pluck('id'))
            ->whereIn('accion', ['fabricacion_paro', 'corte_paro'])
            ->where('tipo', 1)
            ->get()
            ->map(function ($paro) use ($componentesMap, $getRutaVisor) {
                $componente = $componentesMap[$paro->componente_id];
                return [
                    'componente' => $componente->nombre,
                    'version' => $componente->version,
                    'tipo_paro' => $paro->tipo_paro,
                    'fecha' => $paro->fecha,
                    'hora' => $paro->hora,
                    'rutaVisor' => $getRutaVisor($componente->herramental_id, $componente->id),
                ];
            });

        return response()->json([
            'retrasos' => [
                'retrabajos' => $retrabajos,
                'modificaciones' => $modificaciones,
                'paros' => $paros,
            ]
        ]);
    }
     public function retrasosHerramental(Request $request, $herramental_id){
        $componentes = Componente::where('herramental_id', $herramental_id)->get(['id', 'nombre', 'version', 'herramental_id']);
        $componentesMap = $componentes->keyBy('id');

        $getRutaVisor = function ($herramental_id, $componente_id) {
            $herramental = Herramental::findOrFail($herramental_id);
            $proyecto = Proyecto::findOrFail($herramental->proyecto_id);
            $cliente = Cliente::findOrFail($proyecto->cliente_id);
            $anio = Anio::findOrFail($cliente->anio_id);

            return "?a={$anio->id}&c={$cliente->id}&p={$proyecto->id}&h={$herramental->id}&co={$componente_id}";
        };

        $retrabajos = Solicitud::whereIn('componente_id', $componentes->pluck('id'))
            ->where('tipo', 'retrabajo')
            ->get()
            ->map(function ($solicitud) use ($componentesMap, $getRutaVisor) {
                $componente = $componentesMap[$solicitud->componente_id];
                return [
                    'componente' => $componente->nombre,
                    'version' => $componente->version,
                    'created_at' => $solicitud->created_at->format('Y-m-d H:i'),
                    'area_solicitante' => $solicitud->area_solicitante,
                    'rutaVisor' => $getRutaVisor($componente->herramental_id, $componente->id),
                ];
            });

        $modificaciones = Solicitud::whereIn('componente_id', $componentes->pluck('id'))
            ->where('tipo', 'modificacion')
            ->get()
            ->map(function ($solicitud) use ($componentesMap, $getRutaVisor) {
                $componente = $componentesMap[$solicitud->componente_id];
                return [
                    'componente' => $componente->nombre,
                    'version' => $componente->version,
                    'created_at' => $solicitud->created_at->format('Y-m-d H:i'),
                    'area_solicitante' => $solicitud->area_solicitante,
                    'rutaVisor' => $getRutaVisor($componente->herramental_id, $componente->id),
                ];
            });

        $paros = SeguimientoTiempo::whereIn('componente_id', $componentes->pluck('id'))
            ->whereIn('accion', ['fabricacion_paro', 'corte_paro'])
            ->where('tipo', 1)
            ->get()
            ->map(function ($paro) use ($componentesMap, $getRutaVisor) {
                $componente = $componentesMap[$paro->componente_id];
                return [
                    'componente' => $componente->nombre,
                    'version' => $componente->version,
                    'tipo_paro' => $paro->tipo_paro,
                    'fecha' => $paro->fecha,
                    'hora' => $paro->hora,
                    'rutaVisor' => $getRutaVisor($componente->herramental_id, $componente->id),
                ];
            });

        return response()->json([
            'retrasos' => [
                'retrabajos' => $retrabajos,
                'modificaciones' => $modificaciones,
                'paros' => $paros,
            ]
        ]);
    }
    public function tiemposPersonal(Request $request){
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');
        $turno = intval($request->input('turno'));

        $turnoHorario = [
            1 => ['08:00', '17:30'],
            2 => ['17:31', '23:59']
        ];

        $horarioInicio = $turnoHorario[$turno][0];
        $horarioFin = $turnoHorario[$turno][1];

        $rolesPermitidos = ['OPERADOR', 'PROGRAMADOR', 'ALMACENISTA', 'MATRICERO'];

        $accionesPorRol = [
            'OPERADOR' => ['activo' => 'fabricacion', 'paro' => 'fabricacion_paro'],
            'PROGRAMADOR' => ['activo' => 'programacion', 'paro' => null],
            'ALMACENISTA' => ['activo' => 'corte', 'paro' => 'corte_paro'],
            'MATRICERO' => ['activo' => 'ensamble', 'paro' => null],
        ];

        $usuarios = User::with('roles')->get();
        $resultados = [];

        foreach ($usuarios as $usuario) {
            $rolesUsuario = $usuario->roles->pluck('name')->map(function ($r) {
                return strtoupper($r);
            })->toArray();

            $rolesDetalles = [];
            $tieneRolPermitido = false;

            foreach ($rolesUsuario as $nombreRol) {
                if (!in_array($nombreRol, $rolesPermitidos)) continue;

                $tieneRolPermitido = true;
                $acciones = $accionesPorRol[$nombreRol];

                $tiempoActivoRol = 0;
                $tiempoParoRol = 0;

                // ACTIVO
                $seguimientos = SeguimientoTiempo::where('usuario_id', $usuario->id)
                    ->where('accion', $acciones['activo'])
                    ->whereBetween('fecha', [$desde, $hasta])
                    ->orderBy('fabricacion_id')
                    ->orderBy('fecha')
                    ->orderBy('hora')
                    ->get();

                $agrupados = $seguimientos->groupBy('fabricacion_id');

                foreach ($agrupados as $registros) {
                    $inicio = null;
                    foreach ($registros as $registro) {
                        $fechaHora = Carbon::createFromFormat('Y-m-d H:i', $registro->fecha . ' ' . $registro->hora);

                        if ($registro->tipo == 1) {
                            $inicio = $fechaHora;
                        } elseif ($registro->tipo == 0 && $inicio) {
                            $fin = $fechaHora;

                            $inicioTurno = Carbon::createFromFormat('Y-m-d H:i', $inicio->format('Y-m-d') . ' ' . $horarioInicio);
                            $finTurno = Carbon::createFromFormat('Y-m-d H:i', $inicio->format('Y-m-d') . ' ' . $horarioFin);

                            $rangoInicio = $inicio->copy()->greaterThan($inicioTurno) ? $inicio : $inicioTurno;
                            $rangoFin = $fin->copy()->lessThan($finTurno) ? $fin : $finTurno;

                            if ($rangoInicio < $rangoFin) {
                                $tiempoActivoRol += $rangoInicio->diffInMinutes($rangoFin);
                            }

                            $inicio = null;
                        }
                    }
                }

                // PARO
                if ($acciones['paro']) {
                    $paros = SeguimientoTiempo::where('usuario_id', $usuario->id)
                        ->where('accion', $acciones['paro'])
                        ->whereBetween('fecha', [$desde, $hasta])
                        ->orderBy('fabricacion_id')
                        ->orderBy('fecha')
                        ->orderBy('hora')
                        ->get();

                    $parosAgrupados = $paros->groupBy('fabricacion_id');

                    foreach ($parosAgrupados as $registros) {
                        $inicio = null;
                        foreach ($registros as $registro) {
                            $fechaHora = Carbon::createFromFormat('Y-m-d H:i', $registro->fecha . ' ' . $registro->hora);

                            if ($registro->tipo == 1) {
                                $inicio = $fechaHora;
                            } elseif ($registro->tipo == 0 && $inicio) {
                                $fin = $fechaHora;

                                $inicioTurno = Carbon::createFromFormat('Y-m-d H:i', $inicio->format('Y-m-d') . ' ' . $horarioInicio);
                                $finTurno = Carbon::createFromFormat('Y-m-d H:i', $inicio->format('Y-m-d') . ' ' . $horarioFin);

                                $rangoInicio = $inicio->copy()->greaterThan($inicioTurno) ? $inicio : $inicioTurno;
                                $rangoFin = $fin->copy()->lessThan($finTurno) ? $fin : $finTurno;

                                if ($rangoInicio < $rangoFin) {
                                    $tiempoParoRol += $rangoInicio->diffInMinutes($rangoFin);
                                }

                                $inicio = null;
                            }
                        }
                    }
                }

                $rolesDetalles[] = [
                    'nombre' => $nombreRol,
                    'minutos_activo' => $tiempoActivoRol,
                    'minutos_paro' => $tiempoParoRol,
                ];
            }

            if ($tieneRolPermitido) {
                $detalleParos = SeguimientoTiempo::where('usuario_id', $usuario->id)
                ->whereIn('accion', array_filter(array_column($accionesPorRol, 'paro'))) // todos los posibles paros de los roles
                ->whereBetween('fecha', [$desde, $hasta])
                ->whereRaw("STR_TO_DATE(hora, '%H:%i') BETWEEN STR_TO_DATE(?, '%H:%i') AND STR_TO_DATE(?, '%H:%i')", [$horarioInicio, $horarioFin])
                ->where('tipo', 1)
                ->orderBy('fecha')
                ->orderBy('hora')
                ->get()
                ->toArray();

                $puesto = Puesto::find($usuario->puesto_id);
                $costo_hora = 0;
                if ($puesto)
                    $costo_hora = $puesto->pago_hora ?? 0;


                $resultados[] = [
                    'id' => $usuario->id,
                    'costo_hora' => $costo_hora,
                    'usuario_id' => $usuario->id,
                    'nombre' => $usuario->nombre_completo,
                    'roles' => $rolesDetalles,
                    'minutos_activo' => collect($rolesDetalles)->sum('minutos_activo'),
                    'minutos_paro' => collect($rolesDetalles)->sum('minutos_paro'),
                    'minutos_totales' => $this->minutosEsperadosFechas($desde, $hasta, $turno),
                    'detalle_paros' => $detalleParos,
                ];
            }
        }




        $desde = Carbon::parse($request->input('desde'))->startOfDay();
        $hasta = Carbon::parse($request->input('hasta'))->endOfDay();
        
        [$horaInicio, $horaFin] = $turnoHorario[$turno];

        $resumen = DB::table('componentes')
            ->selectRaw("DATE(STR_TO_DATE(fecha_ensamblado, '%Y-%m-%d %H:%i')) as fecha, COUNT(*) as total")
            ->whereRaw("STR_TO_DATE(fecha_ensamblado, '%Y-%m-%d %H:%i') BETWEEN ? AND ?", [$desde, $hasta])
            ->whereRaw("TIME(STR_TO_DATE(fecha_ensamblado, '%Y-%m-%d %H:%i')) BETWEEN ? AND ?", [$horaInicio, $horaFin])
            ->groupByRaw("DATE(STR_TO_DATE(fecha_ensamblado, '%Y-%m-%d %H:%i'))")
            ->orderBy('fecha')
            ->get();

            $periodo = [];
            $totalComponentes = 0;
            $fechaActual = $desde->copy();

            while ($fechaActual <= $hasta) {
                $fecha = $fechaActual->toDateString();
                $registro = $resumen->firstWhere('fecha', $fecha);

                $periodo[] = [
                    'fecha' => Carbon::parse($fecha)->format('d/m/Y'),
                    'turno' => $turno,
                    'total' => $registro ? $registro->total : 0,
                ];

                $totalComponentes += $registro ? $registro->total : 0;
                $fechaActual->addDay();
            }
    
        return response()->json([
            'success' => true,
            'periodo' => $periodo,
            'totalComponentes' => $totalComponentes,
            'tiempos' => $resultados
        ]);
    }
    public function tiemposFinanzasPY(Request $request, $proyecto_id){
        $proyecto = Proyecto::findOrFail($proyecto_id);
        $herramentalIds = Herramental::where('proyecto_id', $proyecto->id)->pluck('id')->toArray();
        $componenteIds = Componente::whereIn('herramental_id', $herramentalIds)->pluck('id')->toArray();

        if (empty($componenteIds)) {
            return response()->json(['success' => false, 'message' => 'No se encontraron componentes para el proyecto especificado.'], 404);
        }

        $seguimientos = SeguimientoTiempo::whereIn('componente_id', $componenteIds)
            ->where('accion', 'fabricacion') // Solo queremos "fabricacion"
            ->orderBy('componente_id')
            ->orderBy('fabricacion_id')
            ->orderBy('fecha')
            ->orderBy('hora')
            ->get();

        $totalSegundos = 0;
        $componentesAgrupados = $seguimientos->groupBy('fabricacion_id');

        foreach ($componentesAgrupados as $seguimientosComponente) {
            $inicio = null;
            foreach ($seguimientosComponente as $seguimiento) {
                if ($seguimiento->tipo == 1) { // Inicio
                    $inicio = Carbon::createFromFormat('Y-m-d H:i', $seguimiento->fecha . ' ' . $seguimiento->hora);
                } elseif ($seguimiento->tipo == 0 && $inicio) { // Fin
                    $fin = Carbon::createFromFormat('Y-m-d H:i', $seguimiento->fecha . ' ' . $seguimiento->hora);
                    $totalSegundos += $inicio->diffInSeconds($fin);
                    $inicio = null;
                }
            }
        }
        $totalHorasMaquinado = floor($totalSegundos / 3600); // horas completas
        $restoSegundos = $totalSegundos % 3600; // lo que sobra después de las horas
        $totalMinutosMaquinado = floor($restoSegundos / 60); // minutos completos

          // COSTOS POR MAQUINA
        $costoTotalMaquinado = 0;
        $costosAgrupados = []; // clave = maquina_id

        foreach ($componentesAgrupados as $fabricacion_id => $seguimientosComponente) {
            $inicio = null;
            $segundosFabricacion = 0;

            foreach ($seguimientosComponente as $seguimiento) {
                if ($seguimiento->tipo == 1) {
                    $inicio = Carbon::createFromFormat('Y-m-d H:i', $seguimiento->fecha . ' ' . $seguimiento->hora);
                } elseif ($seguimiento->tipo == 0 && $inicio) {
                    $fin = Carbon::createFromFormat('Y-m-d H:i', $seguimiento->fecha . ' ' . $seguimiento->hora);
                    $segundosFabricacion += $inicio->diffInSeconds($fin);
                    $inicio = null;
                }
            }

            $fabricacion = Fabricacion::find($fabricacion_id);
            if (!$fabricacion || !$fabricacion->maquina_id) continue;

            $maquina = Maquina::find($fabricacion->maquina_id);
            if (!$maquina || !$maquina->pago_hora) continue;

            $minutos = $segundosFabricacion / 60;
            $costoPorMinuto = $maquina->pago_hora / 60;
            $costo = $minutos * $costoPorMinuto;
            $costoTotalMaquinado += $costo;

            if (!isset($costosAgrupados[$maquina->id])) {
                $costosAgrupados[$maquina->id] = [
                    'maquina_id' => $maquina->id,
                    'nombre' => $maquina->nombre,
                    'costo' => 0,
                    'tiempo_segundos' => 0,
                ];
            }

            $costosAgrupados[$maquina->id]['costo'] += $costo;
            $costosAgrupados[$maquina->id]['tiempo_segundos'] += $segundosFabricacion;
        }

        // Redondeo y conversión de tiempo
        $costosMaquinas = array_map(function ($item) {
            $horas = floor($item['tiempo_segundos'] / 3600);
            $minutos = floor(($item['tiempo_segundos'] % 3600) / 60);
            return [
                'maquina_id' => $item['maquina_id'],
                'nombre' => $item['nombre'],
                'costo' => round($item['costo'], 2),
                'tiempo_horas' => $horas,
                'tiempo_minutos' => $minutos,
            ];
        }, array_values($costosAgrupados));


        // OBTENER TIEMPO RETRABAJOS
        $componentes = Componente::whereIn('id', $componenteIds)->get();
        $totalMinutosRetrabajo = 0;
        foreach ($componentes as $componente) {
            $totalMinutosRetrabajo += $this->calcularRetrabajoMinutos($componente);
        }
        $horasRet = intdiv($totalMinutosRetrabajo, 60);
        $minutosRet = $totalMinutosRetrabajo % 60;


        // OBTENER TIEMPO DE PAROS
        $seguimientos = SeguimientoTiempo::whereIn('componente_id', $componenteIds)
            ->whereIn('accion', ['fabricacion_paro', 'corte_paro'])
            ->orderBy('fabricacion_id')
            ->orderBy('accion')
            ->orderBy('fecha')
            ->orderBy('hora')
            ->get();

        $totalSegundos = 0;
        $componentesAgrupados = $seguimientos->groupBy('fabricacion_id');

        foreach ($componentesAgrupados as $seguimientosComponente) {
            $inicio = null;
            foreach ($seguimientosComponente as $seguimiento) {
                if ($seguimiento->tipo == 1) { // Inicio
                    $inicio = Carbon::createFromFormat('Y-m-d H:i', $seguimiento->fecha . ' ' . $seguimiento->hora);
                } elseif ($seguimiento->tipo == 0 && $inicio) { // Fin
                    $fin = Carbon::createFromFormat('Y-m-d H:i', $seguimiento->fecha . ' ' . $seguimiento->hora);
                    $totalSegundos += $inicio->diffInSeconds($fin);
                    $inicio = null;
                }
            }
        }
        $totalHorasParo = floor($totalSegundos / 3600); // horas completas
        $restoSegundos = $totalSegundos % 3600; // lo que sobra después de las horas
        $totalMinutosParo = floor($restoSegundos / 60); // minutos completos


        // MODIFICACIONES

       $notificaciones = Notificacion::whereIn('componente_id', $componenteIds)
        ->where(function($query) {
            $query->where('descripcion', 'like', 'UN COMPONENTE REQUIERE MODIFICACION%')
                ->orWhere('descripcion', 'SE HA GENERADO UNA NUEVA VERSIÓN DEL COMPONENTE DEBIDO A UNA REFABRICACIÓN.')
                ->orWhere('descripcion', 'EL DISEÑO DEL COMPONENTE HA SIDO MODIFICADO, SE REQUIERE UN RETRABAJO.');
        })
        ->orderBy('componente_id')
        ->orderBy('created_at')
        ->get();

        $tiemposPorComponente = []; 

        foreach ($notificaciones as $notificacion) {
            $componenteId = $notificacion->componente_id;
            $descripcion = $notificacion->descripcion;

            if (str_starts_with($descripcion, 'UN COMPONENTE REQUIERE MODIFICACION')) {
                $tiemposPorComponente[$componenteId]['inicio'] = $notificacion->created_at;
            } elseif (
                $descripcion === 'SE HA GENERADO UNA NUEVA VERSIÓN DEL COMPONENTE DEBIDO A UNA REFABRICACIÓN.' ||
                $descripcion === 'EL DISEÑO DEL COMPONENTE HA SIDO MODIFICADO, SE REQUIERE UN RETRABAJO.'
            ) {
                if (isset($tiemposPorComponente[$componenteId]['inicio'])) {
                    $inicio = $tiemposPorComponente[$componenteId]['inicio'];
                    $fin = $notificacion->created_at;
                    $diferenciaSegundos = strtotime($fin) - strtotime($inicio);
                    $tiemposPorComponente[$componenteId]['tiempo'] = ($tiemposPorComponente[$componenteId]['tiempo'] ?? 0) + $diferenciaSegundos;
                    unset($tiemposPorComponente[$componenteId]['inicio']);
                }
            }
        }
        $totalSegundos = 0;
        foreach ($tiemposPorComponente as $datos) {
            if (isset($datos['tiempo'])) {
                $totalSegundos += $datos['tiempo'];
            }
        }

        $totalMinutos = intdiv($totalSegundos, 60);
        $horasMod = intdiv($totalMinutos, 60);
        $minutosMod = $totalMinutos % 60;

        // OBTENER TIEMPO DE PRUEBAS
        $segundosPruebasDiseño = $this->obtenerTiempoPruebasDiseno($herramentalIds);
        $segundosPruebasProceso = $this->obtenerTiempoPruebasProceso($herramentalIds);
        
        $horasDiseño = intdiv($segundosPruebasDiseño, 3600);
        $minutosDiseño = intdiv($segundosPruebasDiseño % 3600, 60);
        $horasProceso = intdiv($segundosPruebasProceso, 3600);
        $minutosProceso = intdiv($segundosPruebasProceso % 3600, 60);
        
        $pruebasDiseno = $this->obtenerDetallePruebasDiseno($herramentalIds);
        $pruebasProceso = $this->obtenerDetallePruebasProceso($herramentalIds);

        //OBTENER PRECIO MATERIA PRIMA
        $movimientos = MovimientoHoja::where('proyecto_id', $proyecto_id)->get();
        $hojasIds = $movimientos->pluck('hoja_id')->unique()->toArray();
        $hojas = Hoja::whereIn('id', $hojasIds)->get()->keyBy('id');
        $materialIds = $hojas->pluck('material_id')->unique()->toArray();
        $materiales = Material::whereIn('id', $materialIds)->get()->keyBy('id');

        $agrupados = $movimientos->groupBy(function ($mov) use ($hojas) {
            $hoja = $hojas[$mov->hoja_id] ?? null;
            $materialId = $hoja ? $hoja->material_id : 'desconocido';
            return $materialId . '-' . $mov->hoja_id;
        });

        $reporte_materia_prima = [];
        $granTotal = 0;

        foreach ($agrupados as $key => $grupo) {
            $hojaId = $grupo->first()->hoja_id;
            $hoja = $hojas[$hojaId] ?? null;

            if (!$hoja) continue;

            $materialId = $hoja->material_id;
            $materialNombre = $materiales[$materialId]->nombre ?? 'Desconocido';
            $precioKilo = $hoja->precio_kilo;

            $pesoTotal = $grupo->sum(function ($mov) {
                return ($mov->peso_inicial ?? 0) - ($mov->peso ?? 0);
            });

            $costoTotal = $pesoTotal * $precioKilo;
            $granTotal += $costoTotal;

            $reporte_materia_prima[] = [
                'material' => $materialNombre,
                'hoja_descripcion' => 'Consec. ' . $hoja->consecutivo .  ($hoja->material_id == 6 ? ', Material ' : ', Calidad ')  . $hoja->calidad,
                'peso_total' => round($pesoTotal, 2),
                'precio_kilo' => round($precioKilo, 2),
                'costo_total' => round($costoTotal, 2),
            ];
        }

         // compras
        $componentesYaRecibidos = Componente::whereIn('id', $componenteIds)
            ->where('es_compra', true)
            ->whereNotNull('fecha_real')
            ->get();
        $costos = $componentesYaRecibidos->reduce(function ($carry, $comp) {
            $costo = is_numeric($comp->costo_unitario) ? $comp->costo_unitario : 0;
            $subtotal = $costo * ($comp->cantidad - $comp->reusados);

            if ($comp->fecha_real) {
                if ($comp->refabricado) {
                    $carry['refabricacion'] += $subtotal;
                } else {
                    $carry['real'] += $subtotal;
                }
            }
            return $carry;
        }, [
            'real' => 0,
            'refabricacion' => 0,
        ]);
        $costos['total'] = $costos['real'] + $costos['refabricacion'];


        return response()->json([
            'success' => true,
            'tiempos' => [
                'total_componentes_comprados' => $componentesYaRecibidos->sum(function ($comp) {
                    return $comp->cantidad ?? 0;
                }),
                'total_componentes_reutilizados' => $componentesYaRecibidos->sum(function ($comp) {
                    return $comp->reusados ?? 0;
                }),
                'total_componentes_pagados' => $componentesYaRecibidos->sum(function ($comp) {
                    return max(($comp->cantidad ?? 0) - ($comp->reusados ?? 0), 0);
                }),
                'total_costo_compras' => round($costos['total'], 2),
                // 'total_costo_compras_refabricados' => round($costos['refabricacion'], 2),
                // 'total_costo_compras_normales' => round($costos['real'], 2),
                'reporte_materia_prima' => $reporte_materia_prima,
                'total_materia_prima' => round($granTotal, 2),
                'maquinado_horas' => $totalHorasMaquinado,
                'maquinado_minutos' => $totalMinutosMaquinado,
                'paro_horas' => $totalHorasParo,
                'paro_minutos' => $totalMinutosParo,
                'retrabajo_horas' => $horasRet,
                'retrabajo_minutos' => $minutosRet,
                'modificacion_horas' => $horasMod,
                'modificacion_minutos' => $minutosMod,
                'diseno_horas' => $horasDiseño,
                'diseno_minutos' => $minutosDiseño,
                'proceso_horas' => $horasProceso,
                'proceso_minutos' => $minutosProceso,
                'pruebasDiseno' => $pruebasDiseno,
                'pruebasProceso' => $pruebasProceso,
                 'costoTotalMaquinado' => round($costoTotalMaquinado, 2),
                'costosMaquinas' => $costosMaquinas,
            ]
        ]);
    }
    public function tiemposFinanzasHR(Request $request, $herramental_id){
        $componenteIds = Componente::where('herramental_id', $herramental_id)->pluck('id')->toArray();

        if (empty($componenteIds)) {
            return response()->json(['success' => false, 'message' => 'No se encontraron componentes para el proyecto especificado.'], 404);
        }

        $seguimientos = SeguimientoTiempo::whereIn('componente_id', $componenteIds)
            ->where('accion', 'fabricacion') // Solo queremos "fabricacion"
            ->orderBy('componente_id')
            ->orderBy('fabricacion_id')
            ->orderBy('fecha')
            ->orderBy('hora')
            ->get();

        $totalSegundos = 0;
        $componentesAgrupados = $seguimientos->groupBy('fabricacion_id');

        foreach ($componentesAgrupados as $seguimientosComponente) {
            $inicio = null;
            foreach ($seguimientosComponente as $seguimiento) {
                if ($seguimiento->tipo == 1) { // Inicio
                    $inicio = Carbon::createFromFormat('Y-m-d H:i', $seguimiento->fecha . ' ' . $seguimiento->hora);
                } elseif ($seguimiento->tipo == 0 && $inicio) { // Fin
                    $fin = Carbon::createFromFormat('Y-m-d H:i', $seguimiento->fecha . ' ' . $seguimiento->hora);
                    $totalSegundos += $inicio->diffInSeconds($fin);
                    $inicio = null;
                }
            }
        }
        $totalHorasMaquinado = floor($totalSegundos / 3600); // horas completas
        $restoSegundos = $totalSegundos % 3600; // lo que sobra después de las horas
        $totalMinutosMaquinado = floor($restoSegundos / 60); // minutos completos


        // COSTOS POR MAQUINA
        $costoTotalMaquinado = 0;
        $costosAgrupados = []; // clave = maquina_id

        foreach ($componentesAgrupados as $fabricacion_id => $seguimientosComponente) {
            $inicio = null;
            $segundosFabricacion = 0;

            foreach ($seguimientosComponente as $seguimiento) {
                if ($seguimiento->tipo == 1) {
                    $inicio = Carbon::createFromFormat('Y-m-d H:i', $seguimiento->fecha . ' ' . $seguimiento->hora);
                } elseif ($seguimiento->tipo == 0 && $inicio) {
                    $fin = Carbon::createFromFormat('Y-m-d H:i', $seguimiento->fecha . ' ' . $seguimiento->hora);
                    $segundosFabricacion += $inicio->diffInSeconds($fin);
                    $inicio = null;
                }
            }

            $fabricacion = Fabricacion::find($fabricacion_id);
            if (!$fabricacion || !$fabricacion->maquina_id) continue;

            $maquina = Maquina::find($fabricacion->maquina_id);
            if (!$maquina || !$maquina->pago_hora) continue;

            $minutos = $segundosFabricacion / 60;
            $costoPorMinuto = $maquina->pago_hora / 60;
            $costo = $minutos * $costoPorMinuto;
            $costoTotalMaquinado += $costo;

            if (!isset($costosAgrupados[$maquina->id])) {
                $costosAgrupados[$maquina->id] = [
                    'maquina_id' => $maquina->id,
                    'nombre' => $maquina->nombre,
                    'costo' => 0,
                    'tiempo_segundos' => 0,
                ];
            }

            $costosAgrupados[$maquina->id]['costo'] += $costo;
            $costosAgrupados[$maquina->id]['tiempo_segundos'] += $segundosFabricacion;
        }

        // Redondeo y conversión de tiempo
        $costosMaquinas = array_map(function ($item) {
            $horas = floor($item['tiempo_segundos'] / 3600);
            $minutos = floor(($item['tiempo_segundos'] % 3600) / 60);
            return [
                'maquina_id' => $item['maquina_id'],
                'nombre' => $item['nombre'],
                'costo' => round($item['costo'], 2),
                'tiempo_horas' => $horas,
                'tiempo_minutos' => $minutos,
            ];
        }, array_values($costosAgrupados));


        // OBTENER TIEMPO RETRABAJOS
        $componentes = Componente::whereIn('id', $componenteIds)->get();
        $totalMinutosRetrabajo = 0;
        foreach ($componentes as $componente) {
            $totalMinutosRetrabajo += $this->calcularRetrabajoMinutos($componente);
        }
        $horasRet = intdiv($totalMinutosRetrabajo, 60);
        $minutosRet = $totalMinutosRetrabajo % 60;


        // OBTENER TIEMPO DE PAROS
        $seguimientos = SeguimientoTiempo::whereIn('componente_id', $componenteIds)
            ->whereIn('accion', ['fabricacion_paro', 'corte_paro'])
            ->orderBy('fabricacion_id')
            ->orderBy('accion')
            ->orderBy('fecha')
            ->orderBy('hora')
            ->get();

        $totalSegundos = 0;
        $componentesAgrupados = $seguimientos->groupBy('fabricacion_id');

        foreach ($componentesAgrupados as $seguimientosComponente) {
            $inicio = null;
            foreach ($seguimientosComponente as $seguimiento) {
                if ($seguimiento->tipo == 1) { // Inicio
                    $inicio = Carbon::createFromFormat('Y-m-d H:i', $seguimiento->fecha . ' ' . $seguimiento->hora);
                } elseif ($seguimiento->tipo == 0 && $inicio) { // Fin
                    $fin = Carbon::createFromFormat('Y-m-d H:i', $seguimiento->fecha . ' ' . $seguimiento->hora);
                    $totalSegundos += $inicio->diffInSeconds($fin);
                    $inicio = null;
                }
            }
        }
        $totalHorasParo = floor($totalSegundos / 3600); // horas completas
        $restoSegundos = $totalSegundos % 3600; // lo que sobra después de las horas
        $totalMinutosParo = floor($restoSegundos / 60); // minutos completos


        // MODIFICACIONES

       $notificaciones = Notificacion::whereIn('componente_id', $componenteIds)
        ->where(function($query) {
            $query->where('descripcion', 'like', 'UN COMPONENTE REQUIERE MODIFICACION%')
                ->orWhere('descripcion', 'SE HA GENERADO UNA NUEVA VERSIÓN DEL COMPONENTE DEBIDO A UNA REFABRICACIÓN.')
                ->orWhere('descripcion', 'EL DISEÑO DEL COMPONENTE HA SIDO MODIFICADO, SE REQUIERE UN RETRABAJO.');
        })
        ->orderBy('componente_id')
        ->orderBy('created_at')
        ->get();

        $tiemposPorComponente = []; 

        foreach ($notificaciones as $notificacion) {
            $componenteId = $notificacion->componente_id;
            $descripcion = $notificacion->descripcion;

            if (str_starts_with($descripcion, 'UN COMPONENTE REQUIERE MODIFICACION')) {
                $tiemposPorComponente[$componenteId]['inicio'] = $notificacion->created_at;
            } elseif (
                $descripcion === 'SE HA GENERADO UNA NUEVA VERSIÓN DEL COMPONENTE DEBIDO A UNA REFABRICACIÓN.' ||
                $descripcion === 'EL DISEÑO DEL COMPONENTE HA SIDO MODIFICADO, SE REQUIERE UN RETRABAJO.'
            ) {
                if (isset($tiemposPorComponente[$componenteId]['inicio'])) {
                    $inicio = $tiemposPorComponente[$componenteId]['inicio'];
                    $fin = $notificacion->created_at;
                    $diferenciaSegundos = strtotime($fin) - strtotime($inicio);
                    $tiemposPorComponente[$componenteId]['tiempo'] = ($tiemposPorComponente[$componenteId]['tiempo'] ?? 0) + $diferenciaSegundos;
                    unset($tiemposPorComponente[$componenteId]['inicio']);
                }
            }
        }
        $totalSegundos = 0;
        foreach ($tiemposPorComponente as $datos) {
            if (isset($datos['tiempo'])) {
                $totalSegundos += $datos['tiempo'];
            }
        }

        $totalMinutos = intdiv($totalSegundos, 60);
        $horasMod = intdiv($totalMinutos, 60);
        $minutosMod = $totalMinutos % 60;

        // OBTENER TIEMPO DE PRUEBAS
        $segundosPruebasDiseño = $this->obtenerTiempoPruebasDisenoHR($herramental_id);
        $segundosPruebasProceso = $this->obtenerTiempoPruebasProcesoHR($herramental_id);
        
        $horasDiseño = intdiv($segundosPruebasDiseño, 3600);
        $minutosDiseño = intdiv($segundosPruebasDiseño % 3600, 60);
        $horasProceso = intdiv($segundosPruebasProceso, 3600);
        $minutosProceso = intdiv($segundosPruebasProceso % 3600, 60);
        
        $pruebasDiseno = $this->obtenerDetallePruebasDisenoHR($herramental_id);
        $pruebasProceso = $this->obtenerDetallePruebasProcesoHR($herramental_id);


        //OBTENER PRECIO MATERIA PRIMA
        $movimientos = MovimientoHoja::whereIn('componente_id', $componenteIds)->get();
        $hojasIds = $movimientos->pluck('hoja_id')->unique()->toArray();
        $hojas = Hoja::whereIn('id', $hojasIds)->get()->keyBy('id');
        $materialIds = $hojas->pluck('material_id')->unique()->toArray();
        $materiales = Material::whereIn('id', $materialIds)->get()->keyBy('id');

        $agrupados = $movimientos->groupBy(function ($mov) use ($hojas) {
            $hoja = $hojas[$mov->hoja_id] ?? null;
            $materialId = $hoja ? $hoja->material_id : 'desconocido';
            return $materialId . '-' . $mov->hoja_id;
        });

        $reporte_materia_prima = [];
        $granTotal = 0;

        foreach ($agrupados as $key => $grupo) {
            $hojaId = $grupo->first()->hoja_id;
            $hoja = $hojas[$hojaId] ?? null;

            if (!$hoja) continue;

            $materialId = $hoja->material_id;
            $materialNombre = $materiales[$materialId]->nombre ?? 'Desconocido';
            $precioKilo = $hoja->precio_kilo;

            $pesoTotal = $grupo->sum(function ($mov) {
                return ($mov->peso_inicial ?? 0) - ($mov->peso ?? 0);
            });

            $costoTotal = $pesoTotal * $precioKilo;
            $granTotal += $costoTotal;

            $reporte_materia_prima[] = [
                'material' => $materialNombre,
                'hoja_descripcion' => 'Consec. ' . $hoja->consecutivo . ($hoja->material_id == 6 ? ', Material ' : ', Calidad ') . $hoja->calidad,
                'peso_total' => round($pesoTotal, 2),
                'precio_kilo' => round($precioKilo, 2),
                'costo_total' => round($costoTotal, 2),
            ];
        }

        // compras
        $componentesYaRecibidos = Componente::whereIn('id', $componenteIds)
            ->where('es_compra', true)
            ->whereNotNull('fecha_real')
            ->get();
        $costos = $componentesYaRecibidos->reduce(function ($carry, $comp) {
            $costo = is_numeric($comp->costo_unitario) ? $comp->costo_unitario : 0;
            $subtotal = $costo * ($comp->cantidad - $comp->reusados);

            if ($comp->fecha_real) {
                if ($comp->refabricado) {
                    $carry['refabricacion'] += $subtotal;
                } else {
                    $carry['real'] += $subtotal;
                }
            }
            return $carry;
        }, [
            'real' => 0,
            'refabricacion' => 0,
        ]);
        $costos['total'] = $costos['real'] + $costos['refabricacion'];


        return response()->json([
            'success' => true,
            'tiempos' => [
                'total_componentes_comprados' => $componentesYaRecibidos->sum(function ($comp) {
                    return $comp->cantidad ?? 0;
                }),
                'total_componentes_reutilizados' => $componentesYaRecibidos->sum(function ($comp) {
                    return $comp->reusados ?? 0;
                }),
                'total_componentes_pagados' => $componentesYaRecibidos->sum(function ($comp) {
                    return max(($comp->cantidad ?? 0) - ($comp->reusados ?? 0), 0);
                }),
                'total_costo_compras' => round($costos['total'], 2),
                'reporte_materia_prima' => $reporte_materia_prima,
                'total_materia_prima' => round($granTotal, 2),
                'maquinado_horas' => $totalHorasMaquinado,
                'maquinado_minutos' => $totalMinutosMaquinado,
                'paro_horas' => $totalHorasParo,
                'paro_minutos' => $totalMinutosParo,
                'retrabajo_horas' => $horasRet,
                'retrabajo_minutos' => $minutosRet,
                'modificacion_horas' => $horasMod,
                'modificacion_minutos' => $minutosMod,
                'diseno_horas' => $horasDiseño,
                'diseno_minutos' => $minutosDiseño,
                'proceso_horas' => $horasProceso,
                'proceso_minutos' => $minutosProceso,
                'pruebasDiseno' => $pruebasDiseno,
                'pruebasProceso' => $pruebasProceso,
                'costoTotalMaquinado' => round($costoTotalMaquinado, 2),
                'costosMaquinas' => $costosMaquinas,
            ]
        ]);
    }
    public function calcularRetrabajoMinutos($componente){
        $ruta = json_decode($componente->ruta, true);

        if (!$ruta) {
            return 0; // Si no hay ruta o falla el JSON, no hay retrabajo
        }

        $totalMinutos = 0;

        foreach ($ruta as $proceso) {
            if (isset($proceso['time'])) {
                foreach ($proceso['time'] as $tiempo) {
                    if (isset($tiempo['type']) && $tiempo['type'] === 'rework') {
                        $horas = (int)($tiempo['horas'] ?? 0);
                        $minutos = (int)($tiempo['minutos'] ?? 0);

                        $totalMinutos += ($horas * 60) + $minutos;
                    }
                }
            }
        }

        return $totalMinutos;
    }
    public function obtenerTiempoPruebasDiseno($herramentalIds){
        $totalSegundos = 0;

        $pruebas = PruebaDiseno::whereIn('herramental_id', $herramentalIds)->get();

        foreach ($pruebas as $prueba) {
            if ($prueba->fecha_inicio && $prueba->fecha_liberada) {
                $inicio = Carbon::createFromFormat('Y-m-d H:i', $prueba->fecha_inicio);
                $fin = Carbon::createFromFormat('Y-m-d H:i', $prueba->fecha_liberada);

                $diferencia = $inicio->diffInSeconds($fin);
                $totalSegundos += $diferencia;
            }
        }

        return $totalSegundos;
    }
    public function obtenerTiempoPruebasProceso($herramentalIds){
        $totalSegundos = 0;

        $pruebas = PruebaProceso::whereIn('herramental_id', $herramentalIds)->get();

        foreach ($pruebas as $prueba) {
            if ($prueba->fecha_inicio && $prueba->fecha_liberada) {
                $inicio = Carbon::createFromFormat('Y-m-d H:i', $prueba->fecha_inicio);
                $fin = Carbon::createFromFormat('Y-m-d H:i', $prueba->fecha_liberada);

                $diferencia = $inicio->diffInSeconds($fin);
                $totalSegundos += $diferencia;
            }
        }

        return $totalSegundos;
    }
    public function obtenerDetallePruebasDiseno($herramentalIds){
        $pruebas = PruebaDiseno::whereIn('herramental_id', $herramentalIds)->get();
        $tiemposAgrupados = [];

        foreach ($pruebas as $prueba) {
            if ($prueba->fecha_inicio && $prueba->fecha_liberada) {
                $inicio = Carbon::createFromFormat('Y-m-d H:i', $prueba->fecha_inicio);
                $fin = Carbon::createFromFormat('Y-m-d H:i', $prueba->fecha_liberada);

                $segundos = $inicio->diffInSeconds($fin);
                $herramentalId = $prueba->herramental_id;

                if (!isset($tiemposAgrupados[$herramentalId])) {
                    $tiemposAgrupados[$herramentalId] = 0;
                }

                $tiemposAgrupados[$herramentalId] += $segundos;
            }
        }

        $herramentales = Herramental::whereIn('id', array_keys($tiemposAgrupados))
            ->pluck('nombre', 'id');

        $detalles = [];
        foreach ($tiemposAgrupados as $id => $totalSegundos) {
            $horas = intdiv($totalSegundos, 3600);
            $minutos = intdiv($totalSegundos % 3600, 60);

            $detalles[] = [
                'nombre' => $herramentales[$id] ?? 'Desconocido',
                'horas' => $horas,
                'minutos' => $minutos,
            ];
        }

        return $detalles;
    }
    public function obtenerDetallePruebasProceso($herramentalIds){
        $pruebas = PruebaProceso::whereIn('herramental_id', $herramentalIds)->get();

        $tiemposAgrupados = [];

        foreach ($pruebas as $prueba) {
            if ($prueba->fecha_inicio && $prueba->fecha_liberada) {
                $inicio = Carbon::createFromFormat('Y-m-d H:i', $prueba->fecha_inicio);
                $fin = Carbon::createFromFormat('Y-m-d H:i', $prueba->fecha_liberada);

                $segundos = $inicio->diffInSeconds($fin);
                $herramentalId = $prueba->herramental_id;

                if (!isset($tiemposAgrupados[$herramentalId])) {
                    $tiemposAgrupados[$herramentalId] = 0;
                }

                $tiemposAgrupados[$herramentalId] += $segundos;
            }
        }

        // Obtener solo los herramentales que tienen pruebas
        $herramentales = Herramental::whereIn('id', array_keys($tiemposAgrupados))
            ->pluck('nombre', 'id');

        $detalles = [];
        foreach ($tiemposAgrupados as $id => $totalSegundos) {
            $horas = intdiv($totalSegundos, 3600);
            $minutos = intdiv($totalSegundos % 3600, 60);

            $detalles[] = [
                'nombre' => $herramentales[$id] ?? 'Desconocido',
                'horas' => $horas,
                'minutos' => $minutos,
            ];
        }

        return $detalles;
    }
    public function obtenerTiempoPruebasDisenoHR($herramentalIds){
        $totalSegundos = 0;

        $pruebas = PruebaDiseno::where('herramental_id', $herramentalIds)->get();

        foreach ($pruebas as $prueba) {
            if ($prueba->fecha_inicio && $prueba->fecha_liberada) {
                $inicio = Carbon::createFromFormat('Y-m-d H:i', $prueba->fecha_inicio);
                $fin = Carbon::createFromFormat('Y-m-d H:i', $prueba->fecha_liberada);

                $diferencia = $inicio->diffInSeconds($fin);
                $totalSegundos += $diferencia;
            }
        }

        return $totalSegundos;
    }
    public function obtenerTiempoPruebasProcesoHR($herramentalIds){
        $totalSegundos = 0;

        $pruebas = PruebaProceso::where('herramental_id', $herramentalIds)->get();

        foreach ($pruebas as $prueba) {
            if ($prueba->fecha_inicio && $prueba->fecha_liberada) {
                $inicio = Carbon::createFromFormat('Y-m-d H:i', $prueba->fecha_inicio);
                $fin = Carbon::createFromFormat('Y-m-d H:i', $prueba->fecha_liberada);

                $diferencia = $inicio->diffInSeconds($fin);
                $totalSegundos += $diferencia;
            }
        }

        return $totalSegundos;
    }
    public function obtenerDetallePruebasDisenoHR($herramentalIds){
        $detalles = [];

        $pruebas = PruebaDiseno::where('herramental_id', $herramentalIds)->get();

        foreach ($pruebas as $prueba) {
            if ($prueba->fecha_inicio && $prueba->fecha_liberada) {
                $inicio = Carbon::createFromFormat('Y-m-d H:i', $prueba->fecha_inicio);
                $fin = Carbon::createFromFormat('Y-m-d H:i', $prueba->fecha_liberada);

                $segundos = $inicio->diffInSeconds($fin);
                $horas = intdiv($segundos, 3600);
                $minutos = intdiv($segundos % 3600, 60);

                $detalles[] = [
                    'nombre' => $prueba->nombre, // o cambia por otro campo representativo
                    'horas' => $horas,
                    'minutos' => $minutos,
                ];
            }
        }

        return $detalles;
    }
    public function obtenerDetallePruebasProcesoHR($herramentalIds){
        $detalles = [];

        $pruebas = PruebaProceso::where('herramental_id', $herramentalIds)->get();

        foreach ($pruebas as $prueba) {
            if ($prueba->fecha_inicio && $prueba->fecha_liberada) {
                $inicio = Carbon::createFromFormat('Y-m-d H:i', $prueba->fecha_inicio);
                $fin = Carbon::createFromFormat('Y-m-d H:i', $prueba->fecha_liberada);

                $segundos = $inicio->diffInSeconds($fin);
                $horas = intdiv($segundos, 3600);
                $minutos = intdiv($segundos % 3600, 60);

                $detalles[] = [
                    'nombre' => $prueba->nombre,
                    'horas' => $horas,
                    'minutos' => $minutos,
                ];
            }
        }

        return $detalles;
    }
    public function obtenerComponentesReutilizables(){
        $componentes = ComponenteCompra::all();
        return response()->json([
            'success' => true,
            'componentes' => $componentes
        ]);
    }
    public function guardarComponentesReutilizables(Request $request){
        $componentes = $request->json()->all();
        foreach ($componentes as $comp) {
            $componente = ComponenteCompra::find($comp['id']);
            if ($componente) {
                $componente->cantidad = $comp['cantidad'];
                $componente->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Componentes actualizados correctamente.'
        ]);
    }

    public function nuevoComponenteReutilizable(Request $request){
        $data = $request->json()->all();

        if(ComponenteCompra::where('nombre', $data['nombre'])->exists()){
            return response()->json([
                'success' => false,
                'message' => 'Ya existe un componente con ese nombre.'
            ], 200);
        }
        $componente = new ComponenteCompra();
        $componente->nombre = $data['nombre'];
        $componente->descripcion = $data['descripcion'];
        $componente->proveedor = $data['proveedor'];
        $componente->cantidad = $data['cantidad'];
        $componente->save();

        return response()->json([
            'success' => true,
        ]);
    }
    public function eliminarComponenteReutilizable($id){
        $componente = ComponenteCompra::findOrFail($id);
        $componente->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    public function actualizarAnio($id){
        $dataAnio = request()->json()->all();
        $anio = Anio::findOrFail($id);
        $anio->nombre = $dataAnio['nombre'];
        $anio->save();

        return response()->json([
            'success' => true,
        ]);
    }

    public function actualizarCliente($id){
        $dataCarpeta = request()->json()->all();
        $cliente = Cliente::findOrFail($id);
        $cliente->nombre = $dataCarpeta['nombre'];
        $cliente->save();

        return response()->json([
            'success' => true,
        ]);
    }

    public function actualizarProyecto($id){
        $dataProyecto = request()->json()->all();
        $proyecto = Proyecto::findOrFail($id);
        $proyecto->nombre = $dataProyecto['nombre'];
        $proyecto->save();

        return response()->json([
            'success' => true,
        ]);
    }

    public function eliminarAnio($id){
        $anio = Anio::findOrFail($id);
        $anio->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    public function eliminarCliente($id){
        $cliente = Cliente::findOrFail($id);
        $cliente->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    public function eliminarProyecto($id){
        $proyecto = Proyecto::findOrFail($id);
        $proyecto->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    public function generarOrdenAfilado(Request $request){
        $data = json_decode($request->data, true);

        try{
            DB::beginTransaction();
            $ordenAfilado = new SolicitudAfilado();
            $ordenAfilado->componente_id = $data['componente_id'];
            $ordenAfilado->solicitante_id = $data['solicitante_id'];
            $ordenAfilado->cantidad = $data['cantidad'];
            $ordenAfilado->fecha_solicitud = $data['fecha_solicitud'];
            $ordenAfilado->fecha_deseada_entrega = $data['fecha_deseada_entrega'];
            $ordenAfilado->fecha_real_entrega = $data['fecha_real_entrega'];
            $ordenAfilado->area_solicitud = $data['area_solicitud'];            
            $ordenAfilado->numero_hr = $data['numero_hr'];
            $ordenAfilado->nombre_componente = $data['nombre_componente'];
            $ordenAfilado->cantidad = $data['cantidad'];            
            $ordenAfilado->comentarios = $data['comentarios'];  
            $ordenAfilado->caras_a_afilar = $data['caras_a_afilar'];
            $ordenAfilado->cuanto_afilar = $data['cuanto_afilar'];  
            $ordenAfilado->unidad_medida_id = $data['unidad_medida_id'];
            $ordenAfilado->save();

            if ($request->hasFile('archivo_2d')) {
                    $file2D = $request->file('archivo_2d');
                    $resultado = $this->validarArchivo($file2D); 
                    if (!$resultado['success']) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => $resultado['message'],
                        ], 200);
                    }

                $name2D = uniqid() . '_' . $file2D->getClientOriginalName();
                    Storage::disk('public')->put('ordenes_afilado/' . $name2D, \File::get($file2D));
                    $ordenAfilado->archivo_2d = $name2D;
                }
            $ordenAfilado->save();

            $anio = Anio::firstOrCreate(['nombre' => date('Y')]);
            $cliente = Cliente::firstOrCreate(['nombre' => 'ORDENES AFILADO'], ['anio_id' => $anio->id]);
            $nombreProyecto = auth()->user()->id . '.' .  auth()->user()->nombre_completo;

            $proyecto = Proyecto::firstOrCreate(
                ['nombre' => $nombreProyecto, 'cliente_id' => $cliente->id]
            );

            $nombreHerramental = $data['numero_hr'];
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
            $nombreComponente = $nombreHerramental . '-' . $data['nombre_componente'];
                $componenteExistente = Componente::where('nombre', $nombreComponente)->where('herramental_id', $herramental->id)->exists();

                if ($componenteExistente) {
                    $ordenAfilado->delete();
                    return response()->json([
                        'success' => false,
                        'message' => 'El componente ya existe, verifique el numero de componente e intentelo nuevamente',
                    ]);
                }
            $nuevoComponente = new Componente();
            $nuevoComponente->nombre = $herramental->nombre . '-' . $data['nombre_componente'];
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
            $nuevoComponente->estatus_programacion = 'inicial';
            $nuevoComponente->estatus_fabricacion = 1;
            $nuevoComponente->herramental_id = $herramental->id;
            //$nuevoComponente->material_id = $data['material_id'];

            $rutaBase = "{$herramental->proyecto_id}/{$herramental->id}/componentes/";
            Storage::disk('public')->makeDirectory($rutaBase);

            if ($ordenAfilado->archivo_2d) {
                $nuevoNombre2D = $this->generarNuevoNombre($ordenAfilado->archivo_2d);
                Storage::disk('public')->copy(
                    "ordenes_afilado/{$ordenAfilado->archivo_2d}", // Ruta correcta de origen
                    "{$rutaBase}{$nuevoNombre2D}" // Ruta de destino
                );
                $nuevoComponente->archivo_2d = $nuevoNombre2D;
            }

            $nuevoComponente->save();
            $ordenAfilado->componente_id = $nuevoComponente->id;
            $ordenAfilado->save();

            $notificacion = new Notificacion();
            $notificacion->roles = json_encode(['JEFE DE AREA']);
            $notificacion->url_base = '/enrutador';
            $notificacion->anio_id = $anio->id;
            $notificacion->cliente_id = $cliente->id;
            $notificacion->proyecto_id = $proyecto->id;
            $notificacion->herramental_id = $herramental->id;
            $notificacion->componente_id = $nuevoComponente->id;
            $notificacion->cantidad = $nuevoComponente->cantidad;
            $notificacion->descripcion = 'SE HA LIBERADO UN NUEVO COMPONENTE PARA ENRUTAMIENTO DESDE ORDENES DE AFILADO.';
            $notificacion->save();

            $users = User::role('JEFE DE AREA')->get();
            foreach ($users as $user) {
                $user->hay_notificaciones = true;
                $user->save();
            }


            DB::commit();
            return response()->json([
                'success' => true,
            ], 200);
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al generar la orden de afilado: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function editarOrdenAfilado(Request $request, $id){
        $data = json_decode($request->data, true);
        $ordenAfilado = SolicitudAfilado::findOrFail($id);
        $componente = Componente::findOrFail($ordenAfilado->componente_id);
        $herramental = Herramental::findOrFail($componente->herramental_id);
        $proyecto = Proyecto::findOrFail($herramental->proyecto_id);
        $cliente = Cliente::findOrFail($proyecto->cliente_id);
        $anio = Anio::findOrFail($cliente->anio_id);

        $ordenAfilado->fecha_deseada_entrega = $data['fecha_deseada_entrega'];
        $ordenAfilado->comentarios = $data['comentarios'];
        $ordenAfilado->save();

        $rutaBaseSolicitud = "generar-orden-afilado/";
        $rutaBaseComponente = "{$proyecto->id}/{$herramental->id}/componentes/";
        Storage::disk('public')->makeDirectory($rutaBaseComponente);

        if ($request->hasFile('archivo_2d')) {
            if ($componente->archivo_2d) {
                Storage::disk('public')->delete("{$rutaBaseComponente}{$componente->archivo_2d}");
            }
            $archivo2D = $request->file('archivo_2d');
            $nombre2D = $this->generarNuevoNombre($archivo2D->getClientOriginalName());
            $archivo2D->storeAs($rutaBaseSolicitud, $nombre2D, 'public');
            Storage::disk('public')->copy("{$rutaBaseSolicitud}{$nombre2D}", "{$rutaBaseComponente}{$nombre2D}");
            $componente->archivo_2d = $nombre2D;
            $ordenAfilado->archivo_2d = $nombre2D;
        }
        $componente->save();
        $ordenAfilado->save();

        $notificacion = new Notificacion();
        $notificacion->roles = json_encode(['JEFE DE AREA']);
        $notificacion->url_base = '/enrutador';
        $notificacion->anio_id = $anio->id;
        $notificacion->cliente_id = $cliente->id;
        $notificacion->proyecto_id = $proyecto->id;
        $notificacion->herramental_id = $herramental->id;
        $notificacion->componente_id = $componente->id;
        $notificacion->cantidad = $componente->cantidad;
        $notificacion->descripcion = 'EL DISEÑO DE UN COMPONENTE PARA AFILADO HA SIDO MODIFICADO, SE REQUIERE UN RETRABAJO / REFABRICACIÓN';
        $notificacion->save();

        $solicitud = new Solicitud();
        $solicitud->tipo = 'retrabajo';
        $solicitud->componente_id = $componente->id;
        $solicitud->comentarios = 'El diseño de un componente para afilado ha sido modificado, se requiere un retrabajo / refabricación';
        $solicitud->area_solicitante = 'DISEÑO';
        $solicitud->usuario_id = auth()->user()->id;
        $solicitud->save();

        $users = User::role('JEFE DE AREA')->get();
        foreach ($users as $user) {
            $user->hay_notificaciones = true;
            $user->save();
        }


        return response()->json([
            'success' => true,
            'ordenAfilado' => $ordenAfilado,
        ]);
    }
    public function trabajosPendientes(Request $request){
        $user = auth()->user();
        $roles = $user->getRoleNames()->toArray(); // Todos los roles del usuario
        $data = []; 

        // Mapeo de roles a queries que se deben ejecutar
        $roleQueries = [
            'ALMACENISTA' => ['compras', 'cortes', 'temples'],
            'JEFE DE AREA' => ['enrutamiento', 'solicitudes', 'pruebas_diseno'],
            'DISEÑO' => ['pruebas_diseno'],
            'PROCESOS' => ['pruebas_proceso'],
            'PROGRAMADOR' => ['programaciones'],
            'OPERADOR' => ['fabricaciones'],
            'MATRICERO' => ['ensambles'],
        ];

        // Compras
        if ($this->roleHasQuery($roles, $roleQueries, 'compras')) {
            $data['compras'] = Componente::where('es_compra', true)
                ->whereNull('fecha_real')
                ->where('cantidad', '>', 0)
                ->where(function ($query) {
                    $query->where('cancelado', false)
                        ->orWhereNull('cancelado');
                })
                ->get();
        }

        // Cortes
        if ($this->roleHasQuery($roles, $roleQueries, 'cortes')) {
            $data['cortes'] = Componente::where('es_compra', false)
                ->where('cargado', true)
                ->where('enrutado', true)
                ->where('refabricado', '!=', true)
                ->where('estatus_corte', '!=', 'finalizado')
                ->where(function ($query) {
                    $query->where('cancelado', false)
                        ->orWhereNull('cancelado');
                })
                ->get();
        }

        // Temples
        if ($this->roleHasQuery($roles, $roleQueries, 'temples')) {
            $data['temples'] = Componente::where('es_compra', false)
                ->where('cargado', true)
                ->where('enrutado', true)
                ->where('refabricado', '!=', true)
                ->where('requiere_temple', true)
                ->whereNotNull('fecha_solicitud_temple')
                ->whereNull('fecha_recibido_temple')
                ->get();
        }

        // Enrutamiento
        if ($this->roleHasQuery($roles, $roleQueries, 'enrutamiento')) {
            $data['enrutamiento'] = Componente::where('es_compra', false)
                ->where('cargado', true)
                ->where('enrutado', false)
                ->where('refabricado', '!=', true)
                ->where(function ($query) {
                    $query->where('cancelado', false)
                        ->orWhereNull('cancelado');
                })
                ->get();
        }

        // Programaciones
        if ($this->roleHasQuery($roles, $roleQueries, 'programaciones')) {
            $query = Componente::where('es_compra', false)
                ->where('cargado', true)
                ->where('enrutado', true)
                ->where('programado', false)
                ->where('refabricado', '!=', true)
                ->where(function ($query2) {
                    $query2->where('cancelado', false)
                        ->orWhereNull('cancelado');
                });
            $query->where('programador_id', $user->id);
            $data['programaciones'] = $query->get();
        }

        // Fabricaciones (si tiene máquinas asignadas)
        $maquinasAsignadas = json_decode($user->maquinas, true);

        if ($maquinasAsignadas && is_array($maquinasAsignadas) && count($maquinasAsignadas) > 0) {
            $fabricacionesConNotificacion = Fabricacion::with(['componente', 'maquina'])
                ->whereIn('maquina_id', $maquinasAsignadas)
                ->where('estatus_fabricacion', '!=', 'finalizado')
                ->where('fabricado', false)
                ->whereHas('componente', function ($query) {
                    $query->whereColumn('estatus_fabricacion', 'orden');
                    $query->where('refabricado', false);
                })
                ->get()
                ->map(function ($fab) {
                    $notificacion = Notificacion::where('componente_id', $fab->componente_id)
                        ->where('maquina_id', $fab->maquina_id)
                        ->where('fabricacion_id', $fab->id)
                        ->where('descripcion', 'like', 'COMPONENTE LIBERADO PARA FABRICACION%')
                        ->oldest()
                        ->first();

                    if (!$notificacion) {
                        return null;
                    }
                    
                    return [
                        'orden' => $fab->orden,
                        'estatus_fabricacion' => $fab->estatus_fabricacion,
                        'componente' => $fab->componente->nombre,
                        'cantidad' => $fab->componente->cantidad,
                        'prioridad' => $fab->componente->prioridad,
                        'maquina' => $fab->maquina->nombre,
                        'rutaComponente' => $fab->componente->rutaComponente,
                        'maquina_id' => $fab->maquina_id,
                        'componente_id' => $fab->componente_id,
                        'fabricacion_id' => $fab->id,
                        'fecha_liberacion' => $notificacion->created_at->format('d/m/Y h:i a'), 
                    ];
                })
                // 4. Eliminar todas las entradas que retornaron null (las que no tenían notificación)
                ->filter()
                ->values();
                
            $data['fabricaciones'] = $fabricacionesConNotificacion;

        } else {
            $data['fabricaciones'] = [];
        }

        // Ensambles
        if ($this->roleHasQuery($roles, $roleQueries, 'ensambles')) {
            $data['ensambles'] = Componente::where(function ($query) {
                $query->where(function ($q) {
                    $q->where('es_compra', true)
                    ->where('ensamblado', false)
                    ->whereNotNull('fecha_real');
                })
                ->orWhere(function ($q) {
                    $q->where('es_compra', false)
                    ->where('cargado', true)
                    ->where('enrutado', true)
                    ->where('refabricado', '!=', true)
                    ->where('programado', true)
                    ->where('ensamblado', false)
                    ->whereNotNull('fecha_terminado')
                    ->where(function ($qq) {
                        $qq->where('cancelado', false)
                            ->orWhereNull('cancelado');
                    });
                });
            })
            ->whereNotIn('id', function ($query) {
            $query->select('componente_id')
                ->from('solicitudes_externas');
            })
            ->whereNotIn('id', function ($query) {
                $query->select('componente_id')
                    ->from('solicitud_afilados');
            })
            ->get();
        }

        // Solicitudes
        if ($this->roleHasQuery($roles, $roleQueries, 'solicitudes')) {
            $data['solicitudes'] = Solicitud::where('atendida', false)->get();
        }
        
        if ($this->roleHasQuery($roles, $roleQueries, 'pruebas_diseno')) {
            $data['pruebas_diseno'] = 
                Herramental::where('estatus_ensamble', 'finalizado')
                    ->where('estatus_pruebas_diseno', '!=', 'finalizado')->get();
        }
         if ($this->roleHasQuery($roles, $roleQueries, 'pruebas_proceso')) {
            $data['pruebas_proceso'] = 
                Herramental::where('estatus_ensamble', 'finalizado')
                    ->where('estatus_pruebas_diseno', 'finalizado')
                    ->where('estatus_pruebas_proceso', '!=', 'finalizado')->get();
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    public function trabajosPendientesGeneral(){

        $data = []; 
        // Enrutamiento        
        $data['enrutamiento'] = Componente::where('es_compra', false)
            ->where('cargado', true)
            ->where('enrutado', false)
            ->where('refabricado', '!=', true)
            ->where(function ($query) {
                $query->where('cancelado', false)
                    ->orWhereNull('cancelado');
            })
            ->get();
        $data['enrutadores'] = User::role('JEFE DE AREA')->get()->pluck('nombre_completo');


        // Programaciones
        $query = Componente::where('es_compra', false)
            ->where('cargado', true)
            ->where('enrutado', true)
            ->where('programado', false)
            ->where('refabricado', '!=', true)
            ->where(function ($query2) {
                $query2->where('cancelado', false)
                    ->orWhereNull('cancelado');
            })
            ->get();

        $data['programaciones'] = $query
            ->groupBy('programador_id')
            ->map(function ($items, $programadorId) {
                $programador = User::find($programadorId);

                return [
                    'programador_id' => $programadorId,
                    'programador_nombre' => $programador ? $programador->nombre_completo : 'Desconocido',
                    'componentes' => $items
                ];
            })
            ->values();


        //Corte
        $data['cortes'] = Componente::where('es_compra', false)
            ->where('cargado', true)
            ->where('enrutado', true)
            ->where('refabricado', '!=', true)
            ->where('estatus_corte', '!=', 'finalizado')
            ->where(function ($query) {
                $query->where('cancelado', false)
                    ->orWhereNull('cancelado');
            })
            ->get();


        // Fabricaciones
        $data['fabricaciones'] = Fabricacion::query()
            ->join('componentes', 'fabricaciones.componente_id', '=', 'componentes.id')
            ->where('fabricaciones.fabricado', false)
            ->where('fabricaciones.estatus_fabricacion', '!=', 'finalizado')
            ->where('componentes.refabricado', false)
            ->whereColumn('fabricaciones.orden', '=', 'componentes.estatus_fabricacion')
            ->select('fabricaciones.*')
            ->with('componente')
            ->get()
            ->groupBy('maquina_id')
            ->map(function ($fabricacionesPorMaquina, $maquinaId) {
                $maquina = Maquina::find($maquinaId);
                $operadores = User::whereJsonContains('maquinas', $maquinaId)->get();

                $componentesConNotificacion = $fabricacionesPorMaquina->map(function ($fabricacion) use ($maquinaId) {
                    $componente = $fabricacion->componente;

                    $notificacion = Notificacion::where('componente_id', $componente->id)
                        ->where('maquina_id', $maquinaId)
                        ->where('fabricacion_id', $fabricacion->id)
                        ->where('descripcion', 'like', 'COMPONENTE LIBERADO PARA FABRICACION%')
                        ->oldest()
                        ->first();

                    if (!$notificacion) {
                        return null;
                    }

                    $componente->fecha_liberacion = $notificacion->created_at->format('d/m/Y h:i a');
                    return $componente;
                })
                ->filter()
                ->unique('id')->values();

                if ($componentesConNotificacion->isEmpty()) {
                    return null;
                }

                return [
                    'maquina_id' => $maquinaId,
                    'maquina_nombre' => $maquina ? $maquina->nombre : 'Desconocida',
                    'proceso_maquina' => $maquina ? $maquina->tipo_proceso : 'Desconocido',
                    'operadores' => $operadores->map(function ($op) {
                        return [
                            'id' => $op->id,
                            'nombre' => $op->nombre_completo,
                        ];
                    }),
                    'componentes' => $componentesConNotificacion,
                ];
            })
            ->filter()
            ->values();


        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    //Documentacion Tecnica
    public function obtenerDocumentacionTecnica($herramental_id){
        $documento = DocumentacionTecnica::where('herramental_id', $herramental_id)->get();
         return response()->json([
            'documento' => $documento,
            'success' => true,
        ], 200);
    }

    public function guardarDocumentacionTecnica(Request $request){

        if($request->hasFile('archivo')){
            $file = $request->file('archivo');
            $resultado = $this->validarArchivo($file);
            if (!$resultado['success']) {
                return response()->json(['success' => false, 'message' => $resultado['message']]);
            }

            $name = uniqid() . '_' . $file->getClientOriginalName();
            Storage::disk('public')->put("herramental/{$name}", \File::get($file));

            $documento = new DocumentacionTecnica();
            $documento->archivo = $name;
            $documento->descripcion = $request->input('descripcion');
            $documento->herramental_id = $request->input('herramental_id');
            $documento->save();

            return response()->json([
                'success' => true,
            ]);
        }
    }

    public function editarDocumentacionTecnica(Request $request, $id)
    {
        $documento = DocumentacionTecnica::findOrFail($id);

        if ($request->hasFile('archivo')) {        
            $file = $request->file('archivo');
            $resultado = $this->validarArchivo($file);

            if (!$resultado['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $resultado['message']
                ]);
            }
        
            if ($documento->archivo) {
                Storage::disk('public')->delete("herramental/{$documento->archivo}");
            }            
            $name = uniqid() . '_' . $file->getClientOriginalName();
            Storage::disk('public')->put("herramental/{$name}", \File::get($file));

            $documento->archivo = $name;
        }        
        $documento->descripcion = $request->input('descripcion');
        $documento->save();

        return response()->json([
            'success' => true,
        ]);
    }


    public function eliminarDocumentacionTecnica($id){
        $documento = DocumentacionTecnica::findOrFail($id);
        $documento->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    public function obtenerComponentesTerminados(Request $request)
    {
        $query = Componente::whereNotNull('fecha_terminado'); 
        

        if ($request->filtro === '7dias') {
            $query->where('fecha_terminado', '>=', now()->subDays(7));
        } else {
            $query->whereDate('fecha_terminado', now());
        }

        $componentes = $query->orderBy('fecha_terminado', 'desc')->get();

        return response()->json([
            'success' => true,
            'componentes' => $componentes
        ]);
    }



    /**
     * Helper para verificar si alguna de las queries de un rol debe ejecutarse
     */
    private function roleHasQuery(array $userRoles, array $roleQueries, string $queryName): bool
    {
        foreach ($userRoles as $role) {
            if (isset($roleQueries[$role]) && in_array($queryName, $roleQueries[$role])) {
                return true;
            }
        }
        return false;
    }

}

