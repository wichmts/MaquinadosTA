@extends('layouts.app', [
'class' => '',
'elementActive' => 'dashboard'
])

@section('styles')
<link rel="stylesheet" href="{{ asset('paper/css/paper-dashboard-responsivo.css') }}?v={{ time() }}">
<link href="http://ghinda.net/css-toggle-switch/dist/toggle-switch.css" rel="stylesheet">

@endsection

@section('content')
<div class="content" id="vue-app">

    @if (session('message'))
    <div class="alert alert-success" role="alert">
        {{ session('message') }}
    </div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger" role="alert">
        {{ session('error') }}
    </div>
    @endif

    <div class="col-xl-12" v-show="cargando">
        <div style="margin-top: 200px; max-width: 100% !important; margin-bottom: auto; text-align:center; letter-spacing: 2px">
            <h5 class="mb-5">CARGANDO...</h5>
            <div class="loader"></div>
        </div>
    </div>

    <div class="wrapper " v-cloak v-show="!cargando">
        <div class="sidebar" data-color="white" data-active-color="danger">
            
            <div class="sidebar-wrapper">
                <ul class="nav">
                    <li>
                        <div class="nav flex-column nav-pills " id="v-pills-tab" role="tablist" aria-orientation="vertical" style="max-height: 85vh; overflow-y: scroll !important">
                            <div class="d-flex justify-content-end">
                                <a class="nav-link py-0 cursor-pointer text-right text-muted">
                                    <i v-if="menuStep > 1" @click="regresar(menuStep - 1)" class="nc-icon" style="top: -3px !important"><img height="17px" src="{{ asset('paper/img/icons/regresar.png') }}"></i>
                                </a>
                            </div>
                            <div v-if="!cargandoMenu && menuStep == 1">
                                <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> AÑOS </a>
                                <a class="d-flex align-items-center nav-link cursor-pointer" v-for="obj in anios" @click="fetchClientes(obj.id)">
                                    <i class="nc-icon" style="top: -3px !important"><img height="20px" src="{{ asset('paper/img/icons/calendario.png') }}"></i> &nbsp;
                                    <span class="underline-hover">@{{obj.nombre}}</span>
                                </a>
                            </div>
                            <div v-if="!cargandoMenu && menuStep == 2">
                                <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> CARPETAS </a>
                                <a class="d-flex align-items-center nav-link cursor-pointer" v-for="obj in clientes" @click="fetchProyectos(obj.id)" v-if="obj.nombre != 'ORDENES EXTERNAS' && obj.nombre != 'REFACCIONES' ">
                                    <i class="nc-icon" style="top: -3px !important"><img height="20px" src="{{ asset('paper/img/icons/carpetas.png') }}"></i> &nbsp;
                                    <span class="underline-hover">@{{obj.nombre}}</span>
                                </a>
                            </div>
                            <div v-if="!cargandoMenu && menuStep == 3">
                                <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> PROYECTOS </a>
                                <a class="d-flex align-items-center nav-link cursor-pointer" v-for="obj in proyectos" @click="fetchHerramentales(obj.id)">
                                    <i class="nc-icon" style="top: -3px !important"><img height="20px" src="{{ asset('paper/img/icons/carpetas.png') }}"></i> &nbsp;
                                    <span class="underline-hover">@{{obj.nombre}}</span>
                                </a>
                            </div>
                            <div v-if="!cargandoMenu && menuStep >= 4">
                                <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> HERRAMENTALES </a>
                                <a class="d-flex align-items-center nav-link cursor-pointer" v-for="obj in herramentales" @click="fetchPruebasHerramental(obj.id)">
                                    <i class="nc-icon" style="top: -3px !important"><img height="20px" src="{{ asset('paper/img/icons/componente.png') }}"></i> &nbsp;
                                    <span class="underline-hover">@{{obj.nombre}}</span>
                                </a>
                            </div>

                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="main-panel">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-xl navbar-absolute fixed-top navbar-transparent">
                <div class="container-fluid">
                    <div class="navbar-wrapper">
                        <div class="navbar-toggle">
                            <button type="button" class="navbar-toggler">
                                <span class="navbar-toggler-bar bar1"></span>
                                <span class="navbar-toggler-bar bar2"></span>
                                <span class="navbar-toggler-bar bar3"></span>
                            </button>
                        </div>
                        <p style="">
                            <span class="cursor-pointer pb-2" @click="regresar(1)"><i class="fa fa-home"></i> &nbsp;</span>
                            <span class="cursor-pointer pb-2" v-if="ruta.anio" @click="regresar(2)"><i class="fa fa-angle-right"></i> &nbsp; <span class="underline-hover">@{{ruta.anio}}</span> &nbsp;</span>
                            <span class="cursor-pointer pb-2" v-if="ruta.cliente" @click="regresar(3)"><i class="fa fa-angle-right"></i> &nbsp; <span class="underline-hover">@{{ruta.cliente}}</span> &nbsp;</span>
                            <span class="cursor-pointer pb-2" v-if="ruta.proyecto" @click="regresar(4)"><i class="fa fa-angle-right"></i> &nbsp; <span class="underline-hover">@{{ruta.proyecto}}</span> &nbsp;</span>
                            <span class="cursor-pointer pb-2" v-if="ruta.herramental" @click="regresar(5)"><i class="fa fa-angle-right"></i> &nbsp; <span class="underline-hover">@{{ruta.herramental}}</span> </span>
                            <span class="cursor-pointer pb-2" v-if="ruta.prueba"><i class="fa fa-angle-right"></i> &nbsp; <span class="underline-hover">@{{ruta.prueba}}</span> </span>
                        </p>
                    </div>
                </div>
            </nav>

            <div class="content" style="max-height: 80vh; overflow-y: scroll">
                <div class="row mb-2 ">
                    <div class="col-lg-6">
                        <h2 class="bold my-0 py-1 mb-3 text-decoration-underline" style="letter-spacing: 2px">VISOR DE PRUEBAS</h2> 
                    </div>
                    <div class="col-lg-3" v-if="selectedHerramental && !selectedPrueba">
                        <button :disabled="deshabilitarBotones" class="btn btn-dark btn-block" @click="iniciarNuevaPrueba()"><i class="fa fa-plus-circle"></i> Iniciar nueva prueba</button>
                    </div>
                    <div class="col-lg-3" v-if="selectedHerramental && !selectedPrueba">
                        <button :disabled="deshabilitarBotones" @click="liberarAProcesos" class="btn btn-success btn-block"><i class="fa fa-check-double"></i> Liberar a pruebas de procesos</button>
                    </div>
                     <div class="col-lg-2" v-if="selectedPrueba">
                        <button class="btn btn-default btn-block" @click="regresar(5)"><i class="fa fa-arrow-left"></i> Volver</button>
                    </div>
                    <div class="col-lg-2" v-if="selectedPrueba">
                        <button :disabled="prueba.liberada == true" class="btn btn-dark btn-block" @click="guardar(false)"><i class="fa fa-save"></i> Guardar</button>
                    </div>
                    <div class="col-lg-2" v-if="selectedPrueba && !prueba.liberada">
                        <button class="btn btn-success btn-block" @click="liberar()"><i class="fa fa-check-circle"></i> Finalizar prueba</button>
                    </div>
                    <div class="col-lg-2" v-if="selectedPrueba && prueba.liberada">
                        <button class="btn btn-success btn-block" disabled><i class="fa fa-check-circle"></i> Finalizada</button>
                    </div>
                    
                    <div class="col-12" v-if="!selectedHerramental && !selectedPrueba">
                        <h5 class="text-muted my-4"> SELECCIONE UN HERRAMENTAL PARA VER EL LISTADO DE PRUEBAS</h5>
                    </div>
                    <div class="col-12" v-if="selectedHerramental && !selectedPrueba">
                        <div class="row">
                            <div class="col-12 table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Prueba</th>
                                            <th>Descripción</th>
                                            <th>Fecha de inicio</th>
                                            <th>Fecha finalización</th>
                                            <th>Estatus</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="!pruebas || pruebas.length == 0">
                                            <td colspan="6">Este herramental aún no cuenta con ninguna prueba </td>
                                        </tr>
                                        <tr v-for="p in pruebas" v-else>
                                            <td class="bold">@{{p.nombre}}</td>
                                            <td>@{{p.descripcion}}</td>
                                            <td>@{{p.fecha_inicio_show}}</td>
                                            <td>@{{p.fecha_liberada ? p.fecha_liberada_show : '-'}}</td>
                                            <td>
                                                <span v-if="p.liberada" class="w-100 py-2 badge badge-pill badge-success"><i class="fa fa-check-circle"></i> FINALIZADA</span>
                                                <span v-else class="w-100 py-2 badge badge-pill badge-dark"><i class="fa fa-clock"></i> EN CURSO...</span>
                                            </td>
                                            <td> 
                                                <button @click="fetchPrueba(p.id)" class="btn btn-default btn-sm my-1"><i class="fa fa-edit"></i> Ir a la prueba</button> 
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-12" v-if="selectedHerramental && selectedPrueba">
                        <div class="row">
                            <div class="col-lg-12 mb-2 d-flex  align-items-center">
                                <span style="font-size: 18px !important; border-color: #c0d340 !important; background-color: #c0d340 !important" class="d-flex  px-3 py-2 badge badge-warning badge-pill bold"> <i class="fa fa-clipboard-list" style="font-size: 16px !important"></i>&nbsp;&nbsp; @{{ruta.prueba}} @{{ruta.herramental}}</span>
                            </div>
                            <div class="col-lg-8">
                                <div class="row">
                                    <div class="col-lg-6 form-group">
                                        <label class="bold">INVOLUCRADOS <span class="text-danger">*</span></label>
                                        <textarea :disabled="prueba.liberada == true" class="form-control w-100 px-1 py-1 text-left" style="min-height: 100px !important" placeholder="Lista de involucrados..." v-model="prueba.involucrados"></textarea>
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <label class="bold">DESCRIPCIÓN DE LA PRUEBA <span class="text-danger">*</span></label>
                                        <textarea :disabled="prueba.liberada == true" class="form-control w-100 px-1 py-1 text-left" style="min-height: 100px !important" placeholder="Descripcion..." v-model="prueba.descripcion"></textarea>
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <label class="bold">HERRAMIENTAS DE MEDICIÓN REQUERIDAS <span class="text-danger">*</span></label>
                                        <textarea :disabled="prueba.liberada == true" class="form-control w-100 px-1 py-1 text-left" style="min-height: 100px !important" placeholder="Herramientas de medición..." v-model="prueba.herramienta_medicion"></textarea>
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <label class="bold">HALLAZGOS / CONCLUSIONES <span class="text-danger">*</span></label>
                                        <textarea :disabled="prueba.liberada == true" class="form-control w-100 px-1 py-1 text-left" style="min-height: 100px !important" placeholder="Hallazgos / conclusiones..." v-model="prueba.hallazgos"></textarea>
                                    </div>
                                    <div class="col-lg-12 form-group" >
                                        <label class="bold">CHECKLIST PREVIO A PRUEBAS DE LIBERACIÓN</label>
                                        <div style="max-height: 300px; overflow-y: scroll; border-radius: 10px; background-color: #ededed" class="px-1 py-1">
                                            <div class="row my-1" v-for="element in prueba.checklist" :key="element.id">
                                                <div class="col-lg-8">
                                                    <span class="bold">@{{ element.id }}.- @{{ element.nombre }} </span>
                                                </div>
                                                <div class="col-lg-4">
                                                    <fieldset>
                                                        <div class="switch-toggle switch-candy">

                                                            {{-- <input :disabled="prueba.liberada == true" :id="'none-' + element.id" :name="'view-' + element.id" type="radio" class="mostrar-valor" v-model="element.valor" :value="-2">
                                                            <label :for="'none-' + element.id">-</label> --}}

                                                            <input :disabled="prueba.liberada == true" :id="'na-' + element.id" :name="'view-' + element.id" type="radio" class="mostrar-valor" v-model="element.valor" :value="-1">
                                                            <label :for="'na-' + element.id">N/A</label>
                                            
                                                            <input :disabled="prueba.liberada == true" :id="'si-' + element.id" :name="'view-' + element.id" type="radio" class="mostrar-valor" v-model="element.valor" :value="1">
                                                            <label :for="'si-' + element.id">SI</label>
                                            
                                                            <input :disabled="prueba.liberada == true" :id="'no-' + element.id" :name="'view-' + element.id" type="radio" class="mostrar-valor" v-model="element.valor" :value="0">
                                                            <label :for="'no-' + element.id">NO</label>                                                        
                                                            <a></a>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-lg-4">
                                <div class="row">
                                    <div class="col-lg-12 form-group">
                                        <label class="bold">CARGA DE ARCHIVO DIMENSIONAL <span class="text-danger">*</span></label>
                                        <input
                                            class="input-file"
                                            id="archivo2"
                                            type="file"
                                            :disabled="prueba.liberada == true"
                                            @change="handleFileChange($event)"
                                            style="display: none;"
                                        />
                                        <label tabindex="0" for="archivo2" class="input-file-trigger col-12 text-center">
                                            <i class="fa fa-upload"></i> CARGAR ARCHIVO
                                        </label>
                                        <small >Archivo: <strong>@{{prueba.archivo_dimensional_show ?? 'Sin cargar'}}</strong></small>
                                    </div>
                                    <div class="col-lg-12 form-group">
                                        <label class="bold">REGISTRO DE CIERRE DE ALTURA <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input :disabled="prueba.liberada == true" type="number" class="form-control" placeholder="Ingrese valor" aria-label="Milímetros" v-model="prueba.altura_cierre">
                                            <div class="input-group-append">
                                                <span class="input-group-text">mm</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 form-group">
                                        <label class="bold">REGISTRO DE ALTURA DE MEDIDAS DE PIEZA <span class="text-danger">*</span></label>
                                         <div class="input-group">
                                            <input :disabled="prueba.liberada == true" type="number" class="form-control" placeholder="Ingrese valor" aria-label="Milímetros" v-model="prueba.altura_medidas">
                                            <div class="input-group-append">
                                                <span class="input-group-text">mm</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 form-group">
                                        <label class="bold">PLAN DE ACCIÓN</label>
                                        <textarea :disabled="prueba.liberada == true" class="form-control w-100 px-1 py-1 text-left" style="min-height: 200px !important" placeholder="Plan de acción..." v-model="prueba.plan_accion"></textarea>
                                    </div>
                                    <div class="col-xl-12">
                                            <button :disabled="prueba.liberada == true" @click="abrirSolicitud()" class="btn btn-dark btn-block mt-0"><i class="fa fa-edit"></i> SOLICITAR MODIFICACIÓN</button>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

     <div class="modal fade" id="modalSolicitud" tabindex="-1" aria-labelledby="modalSolicitudLabel" aria-hidden="true">
            <div class="modal-dialog" style="min-width: 35%;">
                <div class="modal-content" >
                    <div class="modal-header">
                        <h3 class="text-dark modal-title" id="modalSolicitudLabel">
                            <span>SOLICITAR MODIFICACIÓN PARA EL HERRAMENTAL @{{ruta.herramental}}</span>
                        </h3>
                        <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xl-12 form-group">
                                <label class="bold">Seleccionar componentes a modificar <span style="color: red">*</span></label>
                                <ul style="height: 250px !important; overflow-y: scroll" class="dropdown-menu show w-100 position-static border mt-0">
                                    <li v-for="c in componentes" class="dropdown-item" :class="{ maquinaSeleccionada: c.seleccionado}" @click="c.seleccionado = !c.seleccionado"><i class="fa fa-check-circle" v-if="c.seleccionado"></i> @{{c.nombre}}</li>
                                </ul>
                            </div>
                            <div class="py-0 col-xl-12">
                                <label class="bold">Comentarios <span class="text-danger">*</span></label>
                            <textarea v-model="solicitud.comentarios" class="form-control w-100 text-left px-2 py-1" placeholder="Agregar comentarios..."></textarea>
                        </div>                           
                        </div>
                        <div class="row">
                            <div class="col-xl-12 text-right">
                                <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                                <button class="btn btn-dark" v-if="!loading_button" type="button" @click="enviarSolicitud()"><i class="fa fa-paper-plane"></i> ENVIAR SOLICITUD</button>
                                <button class="btn btn-dark" type="button" disabled v-if="loading_button"><i class="fa fa-spinner"></i> ENVIANDO, ESPERE ...</button>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>

</div>
@endsection

@push('scripts')
<script type="text/javascript">
    Vue.component('v-select', VueSelect.VueSelect)

    var app = new Vue({
        el: '#vue-app',
        data: {
            user_id: {{auth()->user()->id}},
            componente: {
                nombre: '',
                maquinas: []
            },
            loading_button: false,
            cargando: false,
            //MENU IZQUIERDO 
            anios: [],
            clientes: [],
            proyectos: [],
            herramentales: [],
            componentes: [],
            pruebas: [],
            cargandoMenu: true,
            menuStep: 1,
            selectedAnio: null,
            selectedCliente: null,
            selectedProyecto: null,
            selectedHerramental: null,
            selectedPrueba: null,
            ruta: {
                anio: null,
                cliente: null,
                proyecto: null,
                herramental: null,
                prueba: null
            },
            archivos: [],
            solicitud: {},
        },
        watch: {

        },
        computed: {
            deshabilitarBotones(){
                if(this.selectedHerramental){
                    let herramental = this.herramentales.find(h => h.id == this.selectedHerramental);
                    return herramental.estatus_pruebas_diseno == 'finalizado' || this.pruebas.some(c => c.liberada == false) || herramental.estatus_ensamble != 'finalizado';
                }
                return true;
            }
        },
        methods: {
            async liberarAProcesos(){
                let t = this;

                if(t.pruebas.some(c => c.liberada == false)){
                    swal('Error', 'No se puede liberar a pruebas de procesos si hay pruebas pendientes de liberar.', 'error');
                    return;
                }

                swal({
                    title: "¿Está seguro?",
                    text: "¿Desea liberar este herramental a pruebas de procesos?",
                    icon: "info",
                    buttons: ["Cancelar", "Sí, estoy seguro"],
                    dangerMode: false,
                }).then((willCreate) => {
                    if (willCreate) {
                        axios.put(`/api/liberar-herramental-pruebas-diseno/${t.selectedHerramental}`).then(response => {
                            if(response.data.success){
                                swal('Correcto', 'Herramental liberado a pruebas de proceso correctamente', 'success');
                                Vue.nextTick(function(){
                                    t.fetchHerramentales(t.selectedProyecto)
                                })
                            }
                        })
                    }
                });
            },
            async abrirSolicitud(){
                let t = this;
                t.solicitud = {
                    tipo: 'modificacion',
                    prueba_id: t.selectedPrueba,
                    comentarios: '',
                    area_solicitante: 'PRUEBAS DE DISEÑO',
                    componentes: []
                }
                try {
                    const response = await axios.get(`/api/herramentales/${t.selectedHerramental}/componentes?area=pruebas`);
                    t.componentes = response.data.componentes;
                    Vue.nextTick(function(){
                        t.componentes = t.componentes.map(c => ({
                            ...c,
                            seleccionado: c.seleccionado ?? false, // Asegura que tenga la propiedad
                        }));
                    })
                } catch (error) {
                    console.error('Error fetching componentes:', error);
                } finally {
                    t.cargando = false;
                    $('#modalSolicitud').modal();
                }
            },
            async enviarSolicitud(){
                let t = this;
            
                if (!t.solicitud.comentarios.trim()) {
                    swal('Campos obligatorios', 'Es necesario ingresar comentarios para continuar.', 'info');
                    return;
                }
                t.solicitud.componentes = t.componentes.filter(c => c.seleccionado).map(c => c.id);
                if (t.solicitud.componentes.length == 0) {
                    swal('Campos obligatorios', 'Es necesario seleccionar al menos un componente para continuar.', 'info');
                    return;
                }
                t.loading_button = true;
                try {
                    const response = await axios.post(`/api/solicitud-herramental/${t.selectedHerramental}`, t.solicitud);
                    if (response.data.success) {
                        await t.fetchHerramentales(t.selectedProyecto);
                        this.regresar(this.menuStep - 1);
                        swal('Solicitud enviada', 'La solicitud ha sido enviada exitosamente.', 'success');
                        $('#modalSolicitud').modal('hide');
                        t.loading_button = false;
                    }
                } catch (error) {
                    console.error('Error al enviar la solicitud:', error);
                    swal('Error', 'Hubo un problema al intentar enviar la solicitud.', 'error');
                } finally {
                    t.loading_button = false;
                }
            },
            getElipsis(str) {
                if (str && str.length > 45) {
                    return str.substring(0, 44) + '...';
                }
                return str;
            },
            regresar(step) {
                switch (step) {
                    case 1:
                        this.ruta = {
                            anio: null,
                            cliente: null,
                            proyecto: null,
                            herramental: null,
                            prueba: null,
                        }
                        this.selectedAnio = null;
                        this.selectedCliente = null;
                        this.selectedProyecto = null;
                        this.selectedHerramental = null;
                        this.selectedPrueba = null;
                        break;
                    case 2:
                        this.ruta.cliente = null;
                        this.ruta.proyecto = null;
                        this.ruta.herramental = null;
                        this.ruta.prueba = null;

                        this.selectedCliente = null;
                        this.selectedProyecto = null;
                        this.selectedHerramental = null;
                        this.selectedPrueba = null;
                        break;
                    case 3:
                        this.ruta.proyecto = null;
                        this.ruta.herramental = null;
                        this.ruta.prueba = null;

                        this.selectedProyecto = null;
                        this.selectedHerramental = null;
                        this.selectedPrueba = null;

                        break;
                    case 4:
                        this.ruta.herramental = null;
                        this.selectedHerramental = null;

                        this.ruta.prueba = null;
                        this.selectedPrueba = null;
                        break;
                    case 5:
                        this.ruta.prueba = null;
                        this.selectedPrueba = null;
                    break;
                }
                this.menuStep = step;
            },
            async fetchAnios() {
                this.cargandoMenu = true
                try {
                    const response = await axios.get('/api/anios');
                    this.anios = response.data.anios;
                } catch (error) {
                    console.error('Error fetching años:', error);
                } finally {
                    this.cargandoMenu = false;

                }
            },
            async fetchClientes(anioId) {
                this.cargandoMenu = true
                this.selectedAnio = anioId;
                this.ruta.anio = this.anios.find(obj => obj.id == anioId)?.nombre;

                try {
                    const response = await axios.get(`/api/anios/${anioId}/clientes`);
                    this.clientes = response.data.clientes;
                    this.menuStep = 2;
                } catch (error) {
                    console.error('Error fetching clientes:', error);
                } finally {
                    this.cargandoMenu = false;
                }
            },
            async fetchProyectos(clienteId) {
                this.cargandoMenu = true
                this.selectedCliente = clienteId;
                this.ruta.cliente = this.clientes.find(obj => obj.id == clienteId)?.nombre;

                try {
                    const response = await axios.get(`/api/clientes/${clienteId}/proyectos`);
                    this.proyectos = response.data.proyectos;
                    this.menuStep = 3;
                } catch (error) {
                    console.error('Error fetching proyectos:', error);
                } finally {
                    this.cargandoMenu = false;
                }
            },
            async fetchHerramentales(proyectoId) {
                this.cargandoMenu = true
                this.selectedProyecto = proyectoId;
                this.ruta.proyecto = this.proyectos.find(obj => obj.id == proyectoId)?.nombre;

                try {
                    const response = await axios.get(`/api/proyectos/${proyectoId}/herramentales`);
                    this.herramentales = response.data.herramentales;
                    this.menuStep = 4;
                } catch (error) {
                    console.error('Error fetching herramentales:', error);
                } finally {
                    this.cargandoMenu = false;
                }
            },
            async fetchPruebasHerramental(herramentalId, pruebaId = null) {
                this.cargando = true
                this.selectedHerramental = herramentalId;
                this.ruta.herramental = this.herramentales.find(obj => obj.id == herramentalId)?.nombre;
                this.menuStep = 5

                try {
                    const response = await axios.get(`/api/herramental/${herramentalId}/pruebas-diseno`);
                    this.pruebas = response.data.pruebas;

                    if(pruebaId)
                        this.fetchPrueba(pruebaId)

                } catch (error) {
                    console.error('Error fetching pruebas:', error);
                } finally {
                    this.cargando = false;
                }
            },
            async liberar() {

                if (!this.prueba.involucrados?.trim() || !this.prueba.descripcion?.trim() || !this.prueba.herramienta_medicion?.trim() || !this.prueba.hallazgos?.trim()|| !this.prueba.plan_accion?.trim()|| !this.prueba.altura_cierre?.trim() || !this.prueba.altura_medidas?.trim()){
                    swal('Errores de validación', `Todos los campos son obligatorios para liberar el componente.`, 'error');
                    return;
                }
        
                if(!this.prueba.archivo_dimensional){
                    swal('Errores de validación', `Debe cargar el archivo dimensional para liberar esta prueba`, 'error');
                    return;
                }

                this.guardar(true);
            },
            async fetchPrueba(id) {
                let t = this;
                this.selectedPrueba = id;
                this.prueba = this.pruebas.find(obj => obj.id == id)
                if(!this.prueba.checklist){
                    this.inicializarChecklist();
                }
                this.ruta.prueba = this.prueba.nombre;

                Vue.nextTick(function() {
                    document.querySelector("html").classList.add('js');
                    let fileInput = document.querySelector(".input-file");
                    let button = document.querySelector(".input-file-trigger");

                    button.addEventListener("keydown", function(event) {
                        if (event.keyCode == 13 || event.keyCode == 32) {
                            fileInput.focus();
                        }
                    });

                    button.addEventListener("click", function(event) {
                        fileInput.focus();
                        return false;
                    });
                });
            },
            async guardar(liberarHerramental) {
                let t = this
                t.cargando = true;
                t.loading_button = true;

                let formData = new FormData();
                formData.append('data', JSON.stringify(t.prueba));
                try {
                    const response = await axios.post(`/api/prueba-diseno/${t.selectedPrueba}/${liberarHerramental}`, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });

                    swal('Correcto', liberarHerramental ? 'Herramental liberado correctamente' : 'Información guardada correctamente', 'success');
                    await t.fetchPruebasHerramental(t.selectedHerramental, t.selectedPrueba);

                    t.loading_button = false;

                } catch (error) {
                    console.log(error);
                    const mensaje = error.response?.data?.error || 'Error al guardar el componente.';
                    swal('Error', mensaje, 'error');
                    t.cargando = false;
                    t.loading_button = false;
                }
            },
            async navigateFromUrlParams() {
                const queryParams = new URLSearchParams(window.location.search);
                const anioId = queryParams.get('a');
                const clienteId = queryParams.get('c');
                const proyectoId = queryParams.get('p');
                const herramentalId = queryParams.get('h');

                try {
                    if (anioId) {
                        await this.fetchClientes(anioId);
                    }
                    if (clienteId) {
                        await this.fetchProyectos(clienteId);
                    }
                    if (proyectoId) {
                        await this.fetchHerramentales(proyectoId);
                    }
                    if (herramentalId) {
                        await this.fetchPruebasHerramental(herramentalId);
                    }
                } catch (error) {
                    console.error("Error navigating from URL parameters:", error);
                }
            },
            iniciarNuevaPrueba(){
                let t = this
                swal({
                    title: "¿Está seguro?",
                    text: "¿Desea iniciar una nueva prueba?",
                    icon: "info",
                    buttons: ["Cancelar", "Sí, estoy seguro"],
                    dangerMode: false,
                }).then((willCreate) => {
                    if (willCreate) {
                        axios.post(`/api/prueba-diseno/${t.selectedHerramental}`).then(response => {
                            if(response.data.success){
                                Vue.nextTick(function(){
                                    t.fetchPruebasHerramental(t.selectedHerramental, response.data.id)
                                })
                            }
                        })
                    }
                });
            },
            inicializarChecklist(){
                this.prueba.checklist = [
                    { id: 1, nombre: "Es congruente con el diseño a simple vista", valor: -1},
                    { id: 2, nombre: "Las dimensiones exteriores coinciden con la prensa asignada", valor: -1},
                    { id: 3, nombre: "Está pintado con el color definido por el cliente o T/A", valor: -1},
                    { id: 4, nombre: "Está identificado con el nombre de la parte aplicable", valor: -1},
                    { id: 5, nombre: "Está identificado con el número de parte aplicable", valor: -1},
                    { id: 6, nombre: "Está identificado el número de operación", valor: -1},
                    { id: 7, nombre: "Está identificado con el peso (Kg)", valor: -1},
                    { id: 8, nombre: "Espesor de materia prima", valor: -1},
                    { id: 9, nombre: "Ancho de Materia Prima", valor: -1},
                    { id: 10, nombre: "Paso", valor: -1},
                    { id: 11, nombre: "Altura de Cierre", valor: -1},
                    { id: 12, nombre: "Carrera requerida", valor: -1},
                    { id: 13, nombre: "Tonelaje requerido para desarrollo", valor: -1},
                    { id: 14, nombre: "Si utiliza cojin neumático", valor: -1},
                    { id: 15, nombre: "Especificar el tipo de lubricante", valor: -1},
                    { id: 16, nombre: "Tiene el nombre del fabricante", valor: -1},
                    { id: 17, nombre: "Tiene forma de sujetarse con tornillos y tuercas 'T'", valor: -1},
                    { id: 18, nombre: "Se requieren hacer perforaciones para sujeción", valor: -1},
                    { id: 19, nombre: "Cuenta con guías para la lámina", valor: -1},
                    { id: 20, nombre: "Minuta", valor: -1},
                    { id: 21, nombre: "Hay concentricidad en cada Matriz-Punzón, checar con Pza muestra, recortes de rebaba o CAD", valor: -1},
                    { id: 22, nombre: "Los pilotos guía son suficientes", valor: -1},
                    { id: 23, nombre: "Cuenta con dispositivos para sensar el paso", valor: -1},
                    { id: 24, nombre: "Cuenta con acabado superficial aceptable", valor: -1},
                    { id: 25, nombre: "El alimentador es capaz de alimentar p/ la lamina requerida", valor: -1},
                    { id: 26, nombre: "Es confiable la sujeción del alimentador", valor: -1},
                    { id: 27, nombre: "El alimentador cuenta con un programa específico para el producto", valor: -1},
                    { id: 28, nombre: "Garantiza la alineación", valor: -1},
                    { id: 29, nombre: "Garantiza entrada en prensa", valor: -1},
                    { id: 30, nombre: "Facilita la instalación del alimentador", valor: -1},
                    { id: 31, nombre: "Recepción de planos e componentes a último nivel", valor: -1},
                    { id: 32, nombre: "Frecuencia de mantenimiento recomendada por el cliente", valor: -1},
                    { id: 33, nombre: "Formato proporcionado por cliente para mantenimiento preventivo", valor: -1},
                    { id: 34, nombre: "Número de piezas de vida del herramental", valor: -1},
                    { id: 35, nombre: "Están los Uretanos y/o resortes correctamente calculados, se checara en el momento que este trabajando el troquel. Hacer prueba antes de liberación.", valor: -1},
                    { id: 36, nombre: "Están respetados los radios y desahogos para prevenir la concentración de esfuerzos, se checará en el momento que este trabajando el troquel. Hacer prueba antes de liberación.", valor: -1},
                    { id: 37, nombre: "Este Herramental se encuentra dado de alta en el registro de Inventario F7515-02", valor: -1},
                    { id: 38, nombre: "Este Herramental está en el programa de Mantto Prev F7515-05 y en Sistema Touch", valor: -1},
                    { id: 39, nombre: "Este Herramental cuenta con su registro de Mantto Prev F7515-01 y en Sistema Touch", valor: -1},
                    { id: 40, nombre: "Este Herramental cuenta con su registro de Mantto Prev F7515-06 y en Sistema Touch", valor: -1},
                    { id: 41, nombre: "Los resortes expuestos al operador están protegidos con guardas de seguridad.", valor: -1},
                    { id: 42, nombre: "Pernos de registro cuentan con roscas de extracción", valor: -1}
                ];
            },
            async handleFileChange(event) {
                let t =  this
                const file = event.target.files[0];
                if (!file) return;

                const formData = new FormData();
                formData.append('data', JSON.stringify(t.prueba));
                formData.append('archivo_dimensional', file);

                try {
                    const response = await axios.post(`/api/prueba-diseno/${t.selectedPrueba}/false`, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });
                    if(response.data.success === false){
                        swal('Error', response.data.message, 'error');
                    }
                    await t.fetchPruebasHerramental(t.selectedHerramental, t.selectedPrueba);
                    t.loading_button = false;
                } catch (error) {
                    console.log(error);
                    const mensaje = error.response?.data?.error || 'Error al guardar el componente.';
                    swal('Error', mensaje, 'error');
                    t.cargando = false;
                    t.loading_button = false;
                }
            },
        },
        mounted: async function() {
            let t = this;
            await t.fetchAnios();
            this.navigateFromUrlParams();
        }


    })
</script>




@endpush