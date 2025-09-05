@extends('layouts.app', [
'class' => '',
'elementActive' => 'dashboard'
])

@section('styles')
<link rel="stylesheet" href="{{ asset('paper/css/paper-dashboard-responsivo.css') }}?v={{ time() }}">
@endsection

@section('content')
<div id="vue-app">

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
                                    <i class="nc-icon" style="top: -3px !important"><img height="17px" src="{{ asset('paper/img/icons/calendario.png') }}"></i> &nbsp;
                                    <span class="underline-hover">@{{obj.nombre}}</span>
                                </a>
                            </div>
                            <div v-if="!cargandoMenu && menuStep == 2">
                                <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> CARPETAS </a>
                                <a class="d-flex align-items-center nav-link cursor-pointer" v-for="obj in clientes" @click="fetchProyectos(obj.id)">
                                    <i class="nc-icon" style="top: -3px !important"><img height="17px" src="{{ asset('paper/img/icons/carpetas.png') }}"></i> &nbsp;
                                    <span class="underline-hover">@{{obj.nombre}}</span>
                                </a>
                            </div>
                            <div v-if="!cargandoMenu && menuStep == 3">
                                <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> PROYECTOS </a>
                                <a class="d-flex align-items-center nav-link cursor-pointer" v-for="obj in proyectos" @click="fetchHerramentales(obj.id)">
                                    <i class="nc-icon" style="top: -3px !important"><img height="17px" src="{{ asset('paper/img/icons/carpetas.png') }}"></i> &nbsp;
                                    <span class="underline-hover">@{{obj.nombre}}</span>
                                </a>
                            </div>
                            <div v-if="!cargandoMenu && menuStep == 4">
                                <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> HERRAMENTALES </a>
                                <a class="d-flex align-items-center nav-link cursor-pointer" v-for="obj in herramentales" @click="fetchComponentes(obj.id)">
                                    <i class="nc-icon" style="top: -3px !important"><img height="17px" src="{{ asset('paper/img/icons/componente.png') }}"></i> &nbsp;
                                    <span class="underline-hover">@{{obj.nombre}}</span>
                                </a>
                            </div>
                            <div v-if="!cargandoMenu && menuStep == 5">
                                <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> COMPONENTES </a>
                                <a class="d-flex align-items-center nav-link cursor-pointer" v-for="obj in componentes" @click="fetchComponente(obj.id)">
                                    <i class="nc-icon" style="top: -3px !important"><img height="17px" src="{{ asset('paper/img/icons/componentes.png') }}"></i> &nbsp;
                                    <span class="underline-hover">@{{obj.nombre}}</span> &nbsp;&nbsp;
                                    <small 
                                        v-if="obj.programado == true" 
                                        :key="'componente-listo-' + obj.id" 
                                        class="cursor-info text-success fa fa-check-circle" 
                                        data-toggle="tooltip" 
                                        data-placement="bottom" 
                                        title="Componente liberado" >
                                    </small>
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
                            <span class="cursor-pointer pb-2 bold" v-if="ruta.componente"><i class="fa fa-angle-right"></i> &nbsp; <span class="underline-hover">@{{ruta.componente}}</span> </span>
                        </p>
                    </div>
                </div>
            </nav>
            <!-- End Navbar -->
            <div class="content">
                <!-- BOTONES -->
                <div class="row mb-2 ">
                    <div class="col-lg-3">
                        <h2 class="bold my-0 py-1 mb-3 text-decoration-underline" style="letter-spacing: 2px">VISOR DE PROGRAMADOR</h2>
                    </div>

                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-xl-6" v-if="selectedComponente">
                                        <button :disabled="componente.estatus_programacion == 'proceso' || componente.programado == true || componente.programador_id != user_id" class="btn btn-block btn-default" @click="cambiarEstatusProgramacion('proceso')"><i class="fa fa-play-circle"></i> INICIAR PROGRAM.</button>
                                    </div>
                                    <div class="col-xl-6" v-if="selectedComponente">
                                        <button :disabled="componente.estatus_programacion == 'detenido' || componente.estatus_programacion == 'inicial' || componente.programado == true || componente.programador_id != user_id" class="btn btn-block btn-default" @click="cambiarEstatusProgramacion('detenido')"><i class="fa fa-stop-circle"></i> DETENER PROGRAM.</button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6" style="border-left: 1px solid  #ededed">
                                <div class="row">
                                    <div class="col-xl-6" v-if="selectedComponente">
                                        <button class="btn btn-block" :disabled="!puedeEditarse()" @click="guardar(false)"><i class="fa fa-save"></i> GUARDAR</button>
                                    </div>
                                    <div class="col-xl-6" v-if="selectedComponente">
                                        <button class="btn btn-success btn-block" @click="liberar()" :disabled="componente.estatus_programacion == 'inicial' || componente.estatus_programacion == 'detenido' || componente.programado == true || componente.programador_id != user_id">
                                            <i class="fa fa-check-double"></i>
                                            <span v-if="componente.programado == true">LIBERADO</span>
                                            <span v-else>LIBERAR</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END BOTONES -->

                <!-- LABEL -->
                <div class="col-12" v-if="!selectedComponente">
                    <h5 class="text-muted my-4"> SELECCIONE UN COMPONENTE PARA VER SU PROGRAMACION</h5>
                </div>
                <!-- END LABEL -->
                <!-- COMPONENTE -->
                <div class="row" v-else>

                    <!-- BLOQUE  -->
                    <div class="col-xl-8">

                        <div class="row">
                            <div class="col-lg-4 px-2 my-3 d-flex justify-content-center align-items-center">
                                <span style="font-size: 16px !important; border-color: #c0d340 !important; background-color: #c0d340 !important" class="d-flex justify-content-center w-100 badge badge-warning badge-pill bold py-3"> <i class="fa fa-cogs" style="font-size: 15px !important"></i> @{{componente.nombre}}</span>
                            </div>
                            <div class="col-lg-4 d-flex justify-content-around">
                                <div class="d-flex align-items-center">
                                    <a class="text-dark" :href="'/storage/' + componente.archivo_2d_public" target="_blank">
                                        <img src="/paper/img/icons/file.png" height="80px">
                                        <h5 class="my-0 py-0 bold pt-2">2D</h5>
                                    </a>
                                </div>
                                <div class="d-flex align-items-center">
                                    <a class="text-dark" :href="'/storage/' + componente.archivo_3d_public" target="_blank">
                                        <img src="/paper/img/icons/file.png" height="80px">
                                        <h5 class="my-0 py-0 bold pt-2">3D</h5>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-4 d-flex align-items-center">
                                <button class="btn btn-block btn-default" @click="verModalRuta()"><i class="fa fa-eye"></i> Ver ruta</button>
                            </div>
                        </div>

                        <div class="row mb-1 mt-2">
                            <div class="col-lg-4 form-group mt-3">
                                <label class="bold">DESCRIPCION DEL TRABAJO:</label>
                                <textarea :disabled="!puedeEditarse()" v-model="componente.descripcion_trabajo" class="form-control text-left px-1 py-1" style="min-height: 200px !important" placeholder="Descripcion del trabajo..."></textarea>
                            </div>
                            <div class="col-lg-4 form-group mt-3">
                                <label class="bold">HERRAMIENTAS DE CORTE:</label>
                                <textarea :disabled="!puedeEditarse()" v-model="componente.herramientas_corte" class="form-control text-left px-1 py-1" style="min-height: 200px !important" placeholder="Agregar herramientas de corte..."></textarea>
                            </div>
                            <div class="col-lg-4 form-group mt-3" >
                                <label class="bold">SELECCIONAR MAQUINA(S):</label>
                                <ul
                                    style="height: 200px !important; overflow-y: scroll"
                                    :class="[
                                        'dropdown-menu',
                                        'show',
                                        'w-100',
                                        'position-static',
                                        'border',
                                        { 'disabled-ul': !puedeEditarse() }
                                    ]">
                                    <li
                                        v-for="m in maquinas"
                                        :class="[
                                            'dropdown-item',
                                            { 'disabled-item': !puedeEditarse() },
                                            { 'maquinaSeleccionada': existeMaquina(m.id) }
                                        ]"
                                        @click="puedeEditarse() && incluirMaquina(m.id)">
                                        <i class="fa fa-check-circle" v-if="existeMaquina(m.id)"></i>
                                        @{{ m.nombre }}
                                    </li>
                                </ul>

                            </div>
                        </div>
                        <div>
                            <label class="bold">COMENTARIOS:</label>
                            <textarea disabled  v-model="componente.comentarios" class="form-control text-left px-1 py-1" style="min-height: 100px !important" placeholder="Comentarios..."></textarea>
                        </div>

                    </div>

                    <!-- BLOQUE ARCHIVOS -->
                    <div class="col-xl-4">
                        <div class="row">
                            <div class="col-12">
                                <h3 class="bold mb-2" style="letter-spacing: 1px; ">PROGRAMAS POR MAQUINA</h3>
                            </div>
                        </div>
                        <div class="text-center pt-5" v-if="componente.maquinas.length == 0">
                            <span class="text-muted">Es necesario seleccionar una o más <strong>máquinas</strong> para poder cargar los programas</span>
                        </div>
                        <div class="row" v-for="(m, ind) in componente.maquinas">
                            <div class="col-xl-6">
                                <h5 class="bold" style="letter-spacing: 1px"><i class="fa fa-computer"></i> @{{m.nombre}}</h5>
                            </div>
                            <div class="col-xl-6 text-right" v-if="componente.programado != true && componente.programador_id == user_id  && m.requiere_programa">
                                <small class="cursor-pointer" style="text-decoration: underline" @click="agregarArchivo(m)"><i class="fa fa-plus-circle"></i> Agregar programa</small>
                            </div>
                            <div class="col-xl-12">
                                <div class="row mb-2" v-for="(a, index) in m.archivos" :key="index + '-' + m.maquina_id">
                                    <div class="col-xl-12 mb-2" v-if="!m.requiere_programa">
                                        <small> Esta maquina no requiere programa </small>
                                    </div>
                                    <div class="col-xl-10 text-center mr-0 pr-0" v-if="m.requiere_programa">
                                        <input
                                            :disabled="!puedeEditarse()"
                                            class="input-file"
                                            :id="'archivo-' + m.maquina_id + '-' + index"
                                            type="file"
                                            :name="'file['+ m.maquina_id +'][' + index + ']'"
                                            @change="handleFileChange($event, ind, index)"
                                            style="display: none;"
                                            />
                                        <label
                                            tabindex="0"
                                            :for="'archivo-' + m.maquina_id + '-' + index"
                                            class="input-file-trigger col-12 text-center">
                                            <i class="fa fa-upload"></i> Subir programa (.txt)
                                        </label>
                                        <small style="font-style: italic" v-if="!a.id">
                                            @{{ a.nombre ? getElipsis(a.nombre) : "Archivo no seleccionado" }}
                                        </small>
                                        <small v-else>
                                            <a :href="'/api/download/programas/' + a.nombre">@{{getElipsis(a.nombre)}}</a>
                                        </small>
                                    </div>
                                    <div class="col-xl-2 text-center ml-0 pl-0" v-if="componente.programado != true && componente.programador_id == user_id  && m.requiere_programa">
                                        <button class="btn btn-block btn-link my-0" @click="eliminarArchivo(m, index)" :disabled="!puedeEditarse()">
                                            <i class="fa fa-times-circle text-danger" style="font-size: 20px !important"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END BLOQUE ARCHIVOS -->
                </div>
            </div>
            <!-- END BLOQUE -->

        </div>
    </div>


    <div class="modal fade" id="modalRuta" tabindex="-1" aria-labelledby="modalRutaLabel" aria-hidden="true">
        <div class="modal-dialog" style="min-width: 60%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="bold modal-title" id="modalRutaLabel" style="letter-spacing: 1px">RUTA PARA EL COMPONENTE @{{componente.nombre}}</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row d-flex align-items-center">
                        <div class="col-xl-12">
                            <div class="row">
                                <div class="col-xl-12" style="overflow-x:scroll">
                                    <div class="gantt-chart" :style="{ '--columns': duracionTotal.length }">
                                        <div class="gantt-header general-header">
                                            <div class=" time-header pb-2" :colspan="duracionTotal.length" style="letter-spacing: 1px">TIEMPO TEÓRICO EN HORAS</div>
                                        </div>
                                        <div class="gantt-header">
                                            <div class="gantt-cell task-name pt-1">ACCIONES</div>
                                            <div class="gantt-cell pt-1" v-for="hour in duracionTotal" :key="hour">
                                                @{{ hour }}
                                            </div>
                                        </div>
                                        <div class="gantt-row" v-for="task in tasks" :key="task.id">
                                            <div class="gantt-cell task-name pt-1">@{{ task.name }}</div>
                                            <div class="gantt-cell gantt-bar" v-for="hour in duracionTotal" :key="hour">
                                                <div
                                                    v-for="segment in task.time"
                                                    data-toggle="tooltip" data-html="true" :title="getContenidoTooltip(task)"
                                                    :key="segment.inicio"
                                                    v-if="isTaskInHour(segment, hour)"
                                                    :class="segment.type === 'normal' ? 'normal-task' : segment.type === 'rework' ? 'rework-task' : 'delay-task'"
                                                    :style="getTaskStyle(segment, hour)"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-xl-12" style="overflow-x:scroll">
                                    <div class="gantt-chart" :style="{ '--columns': duracionTotal.length }">
                                        <div class="gantt-header general-header">
                                            <div class=" time-header pb-2" :colspan="duracionTotal.length" style="letter-spacing: 1px">TIEMPO REAL EN HORAS</div>
                                        </div>
                                        <div class="gantt-header">
                                            <div class="gantt-cell task-name pt-1">ACCIONES</div>
                                            <div class="gantt-cell pt-1" v-for="hour in duracionTotal" :key="hour">@{{ hour }}</div>
                                        </div>
                                        <div class="gantt-row" v-for="task in rutaAvance" :key="task.id">
                                            <div class="gantt-cell task-name pt-1">@{{ task.name }}</div>
                                            <div class="gantt-cell gantt-bar" v-for="hour in duracionTotal" :key="hour">
                                                <div
                                                    v-for="segment in task.time"
                                                    data-toggle="tooltip" data-html="true" :title="getContenidoTooltip(task)"
                                                    :key="segment.inicio"
                                                    v-if="isTaskInHour(segment, hour)"
                                                    :class="segment.type === 'normal' ? 'normal-task' : segment.type === 'rework' ? 'rework-task' : 'delay-task'"
                                                    :style="getTaskStyle(segment, hour)">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="limite-tiempo" :style="{ left: `${165 + (40 * totalHoras) + ((40 / 60 ) * totalMinutos) }px` }"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalRetraso" tabindex="-1" aria-labelledby="modalRetrasoLabel" aria-hidden="true">
        <div class="modal-dialog" style="min-width: 35%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="modalRetrasoLabel">
                        <span>RETRASO EN EL COMPONENTE @{{componente.nombre}}</span>
                    </h3>
                    <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row px-3">
                        <div class="mt-3 py-2 col-xl-12 form-group" style="background-color: rgb(254, 195, 195); border-radius: 10px">
                            <label class="bold text-danger"><i class="fa fa-exclamation-circle"></i> Hubo un retraso en el tiempo estimado de programacion para este componente. Indique el motivo.</label>
                            <textarea style="border: none !important" v-model="componente.retraso_programacion" class="form-control w-100 text-left px-2 py-1" placeholder="Motivo del retraso..."></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12">
                            <hr>
                        </div>
                        <div class="col-xl-12 text-right">
                            <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                            <button class="btn btn-secondary" v-if="!loading_button" type="button" @click="guardar(true)"><i class="fa fa-check-circle"></i> LIBERAR COMPONENTE</button>
                            <button class="btn btn-secondary" type="button" disabled v-if="loading_button"><i class="fa fa-spinner spin"></i> LIBERANDO, ESPERE ...</button>
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
            user_id: {{ auth()->user()->id }},
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
            maquinas: [],
            cargandoMenu: true,
            menuStep: 1,
            selectedAnio: null,
            selectedCliente: null,
            selectedProyecto: null,
            selectedHerramental: null,
            selectedComponente: null,
            ruta: {
                anio: null,
                cliente: null,
                proyecto: null,
                herramental: null,
                componente: null,
            },
            procesos: [{
                    id: 1,
                    prioridad: 1,
                    nombre: 'Cortar',
                    horas: 0,
                    minutos: 0,
                    incluir: false
                },
                {
                    id: 2,
                    prioridad: 2,
                    nombre: 'Programar',
                    horas: 0,
                    minutos: 0,
                    incluir: false
                },
                {
                    id: 3,
                    prioridad: 3,
                    nombre: 'Carear',
                    horas: 0,
                    minutos: 0,
                    incluir: false
                },
                {
                    id: 4,
                    prioridad: 4,
                    nombre: 'Maquinar',
                    horas: 0,
                    minutos: 0,
                    incluir: false
                },
                {
                    id: 5,
                    prioridad: 5,
                    nombre: 'Tornear',
                    horas: 0,
                    minutos: 0,
                    incluir: false
                },
                {
                    id: 6,
                    prioridad: 6,
                    nombre: 'Roscar/Rebabear',
                    horas: 0,
                    minutos: 0,
                    incluir: false
                },
                // {id: 7, prioridad: 7, nombre: 'Templar', horas: 0, minutos: 0, incluir: false},
                {
                    id: 8,
                    prioridad: 8,
                    nombre: 'Rectificar',
                    horas: 0,
                    minutos: 0,
                    incluir: false
                },
                {
                    id: 9,
                    prioridad: 9,
                    nombre: 'EDM',
                    horas: 0,
                    minutos: 0,
                    incluir: false
                },
                {
                    id: 11,
                    prioridad: 11,
                    nombre: 'Marcar',
                    horas: 0,
                    minutos: 0,
                    incluir: false
                }
            ],
            procesosValidos: [3, 4, 5, 6, 8, 9, 11],
            tasks: [],
            rutaAvance: [],
            archivos: [],
            hay_retraso: false,
        },
        watch: {

        },
        computed: {
            duracionTotal() {
                let maxHour = 0;

                const calcularMaxHora = (array) => {
                    array.forEach(task => {
                        task.time.forEach(segment => {
                            const endHour = segment.hora_inicio + segment.horas + segment.minutos / 60;
                            if (endHour > maxHour) maxHour = Math.ceil(endHour);
                        });
                    });
                };

                // Calcular para tasks
                calcularMaxHora(this.tasks);

                // Calcular para rutaAvance
                calcularMaxHora(this.rutaAvance);

                return maxHour;
            },
            totalHoras() {
                let totalHoras = 0;
                let totalMinutos = 0;
                let maxTime = {
                    horas: 0,
                    minutos: 0
                };

                this.tasks.forEach(task => {
                    let taskTotalHoras = 0;
                    let taskTotalMinutos = 0;

                    // Sumar el tiempo de cada segmento de la tarea
                    task.time.forEach(segmento => {
                        taskTotalHoras += segmento.horas;
                        taskTotalMinutos += segmento.minutos;
                    });

                    // Normalizar minutos en horas
                    taskTotalHoras += Math.floor(taskTotalMinutos / 60);
                    taskTotalMinutos = taskTotalMinutos % 60;

                    // Si la tarea es 1 o 2 (comienza en la misma hora), tomamos la tarea con mayor tiempo total
                    if (task.id === 1 || task.id === 2) {
                        if (taskTotalHoras > maxTime.horas || (taskTotalHoras === maxTime.horas && taskTotalMinutos > maxTime.minutos)) {
                            maxTime.horas = taskTotalHoras;
                            maxTime.minutos = taskTotalMinutos;
                        }
                    } else {
                        totalHoras += taskTotalHoras;
                        totalMinutos += taskTotalMinutos;
                    }
                });

                // Normalizar los minutos
                totalHoras += Math.floor(totalMinutos / 60);
                totalMinutos = totalMinutos % 60;

                // Asegurar que las tareas 1 y 2 empiezan a la misma hora, y agregamos su duración total
                totalHoras += maxTime.horas;
                totalMinutos += maxTime.minutos;

                // Normalizar al final
                if (totalMinutos >= 60) {
                    totalHoras += Math.floor(totalMinutos / 60);
                    totalMinutos = totalMinutos % 60;
                }

                return totalHoras;
            },
            totalMinutos() {
                let totalMinutos = 0;
                let maxTime = {
                    horas: 0,
                    minutos: 0
                };

                this.tasks.forEach(task => {
                    let taskTotalHoras = 0;
                    let taskTotalMinutos = 0;

                    // Sumar el tiempo de cada segmento de la tarea
                    task.time.forEach(segmento => {
                        taskTotalHoras += segmento.horas;
                        taskTotalMinutos += segmento.minutos;
                    });

                    // Normalizar minutos en horas
                    taskTotalHoras += Math.floor(taskTotalMinutos / 60);
                    taskTotalMinutos = taskTotalMinutos % 60;

                    // Si la tarea es 1 o 2 (comienza en la misma hora), tomamos la tarea con mayor tiempo total
                    if (task.id === 1 || task.id === 2) {
                        if (taskTotalHoras > maxTime.horas || (taskTotalHoras === maxTime.horas && taskTotalMinutos > maxTime.minutos)) {
                            maxTime.horas = taskTotalHoras;
                            maxTime.minutos = taskTotalMinutos;
                        }
                    } else {
                        totalMinutos += taskTotalMinutos;
                    }
                });

                // Agregar la duración total de las tareas 1 y 2
                totalMinutos += maxTime.minutos;

                // Normalizar minutos
                if (totalMinutos >= 60) {
                    totalMinutos = totalMinutos % 60;
                }

                return totalMinutos;
            },
        },
        methods: {
            puedeEditarse(){
                if(this.componente.programador_id != this.user_id) return false;
                if(this.componente.programado == true || this.componente.estatus_programacion == 'inicial' || this.componente.estatus_programacion == 'detenido') return false;
                return true;
            },
            // cincoMinutosPasaron() {
            //     let fecha_actual = "{{ now()->toDateTimeString() }}";
            //     console.log(fecha_actual);
            //     console.log(this.componente.fecha_programado)

            //     if (!this.componente.fecha_programado || !fecha_actual) {
            //         return true;
            //     }

            //    // Parsear ambas fechas
            //     const fechaProgramado = new Date(this.componente.fecha_programado.replace(' ', 'T'));
            //     const fechaActual = new Date(fecha_actual.replace(' ', 'T'));

            //     const diferenciaMs = fechaActual - fechaProgramado;

            //     return diferenciaMs > (5 * 60 * 1000);
            // },
            incluirMaquina(maquina_id) {
                let t = this;
                let indiceMaquina = this.componente.maquinas.findIndex(
                    (m) => m.maquina_id === maquina_id
                );

                // Si la máquina ya existe, elimínala del arreglo
                if (indiceMaquina !== -1) {
                    this.componente.maquinas.splice(indiceMaquina, 1); // Elimina la máquina
                } else {
                    if(this.maquinas.find(obj => obj.id === maquina_id)?.requiere_programa != 1){
                        this.componente.maquinas.push({
                            maquina_id: maquina_id,
                            nombre: this.maquinas.find(obj => obj.id === maquina_id)?.nombre,
                            requiere_programa: this.maquinas.find(obj => obj.id === maquina_id)?.requiere_programa,
                            archivos: [{
                                nombre: 'No requiere',
                                archivo: new File(
                                    ['Esta maquina no requiere programación'], // contenido del archivo
                                    'no requiere programa.txt',                         // nombre del archivo
                                    { type: 'text/plain' }                     // tipo MIME
                                    )
                            }]
                        });
                    }else{
                        this.componente.maquinas.push({
                            maquina_id: maquina_id,
                            nombre: this.maquinas.find(obj => obj.id === maquina_id)?.nombre,
                            requiere_programa: this.maquinas.find(obj => obj.id === maquina_id)?.requiere_programa,
                            archivos: [{
                                nombre: '',
                                archivo: null
                            }]
                        });
                    }

                    Vue.nextTick(function() {
                        if(t.componente.maquinas.length > 0 && t.componente.maquinas.some(m => m.requiere_programa == 1)) {
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
                        }
                    });
                }
            },
            existeMaquina(maquina_id) {
                return this.componente.maquinas?.some(obj => obj.maquina_id == maquina_id);
            },
            async cargarRuta() {
                let t = this

                t.procesos = [{
                        id: 1,
                        prioridad: 1,
                        nombre: 'Cortar',
                        horas: 0,
                        minutos: 0,
                        incluir: false
                    },
                    {
                        id: 2,
                        prioridad: 2,
                        nombre: 'Programar',
                        horas: 0,
                        minutos: 0,
                        incluir: false
                    },
                    {
                        id: 3,
                        prioridad: 3,
                        nombre: 'Carear',
                        horas: 0,
                        minutos: 0,
                        incluir: false
                    },
                    {
                        id: 4,
                        prioridad: 5,
                        nombre: 'Maquinar',
                        horas: 0,
                        minutos: 0,
                        incluir: false
                    },
                    {
                        id: 5,
                        prioridad: 5,
                        nombre: 'Tornear',
                        horas: 0,
                        minutos: 0,
                        incluir: false
                    },
                    {
                        id: 6,
                        prioridad: 6,
                        nombre: 'Roscar/Rebabear',
                        horas: 0,
                        minutos: 0,
                        incluir: false
                    },
                    {id: 7, prioridad: 7, nombre: 'Templar', horas: 0, minutos: 0, incluir: false},
                    {
                        id: 8,
                        prioridad: 8,
                        nombre: 'Rectificar',
                        horas: 0,
                        minutos: 0,
                        incluir: false
                    },
                    {
                        id: 9,
                        prioridad: 9,
                        nombre: 'EDM',
                        horas: 0,
                        minutos: 0,
                        incluir: false
                    },
                     {
                        id: 11,
                        prioridad: 11,
                        nombre: 'Marcar',
                        horas: 0,
                        minutos: 0,
                        incluir: false
                    }
                ];

                t.tasks.forEach(task => {
                    let proceso = t.procesos.find(obj => obj.id === task.id);
                    if (proceso) {
                        proceso.horas = task.time[0]?.horas ?? 0;
                        proceso.minutos = task.time[0]?.minutos ?? 0;
                        proceso.incluir = true;
                    }
                });

                Vue.nextTick(function() {
                    t.rutaAvance = t.ajustarRutaAvance(t.tasks, t.rutaAvance);
                    t.calcularInicioAvance();
                    return true;
                })
            },
            async fetchComponente(id) {
                let t = this;
                this.cargando = true;
                this.selectedComponente = id;
                this.componente = this.componentes.find(obj => obj.id == id)
                this.ruta.componente = this.componente.nombre;

                try {
                    const response = await axios.get(`/api/componente/${id}`)
                    t.tasks = JSON.parse(JSON.stringify(response.data.componente.ruta));
                    t.rutaAvance = JSON.parse(JSON.stringify(t.tasks));

                    t.rutaAvance.forEach(element => {
                        element.time = []
                        let find = response.data.componente.rutaAvance.find(obj => obj.id == element.id)
                        if (find) {
                            element.time = find.time
                        }
                    })
                } catch (error) {
                    console.error('Error fetching componente:', error);
                } finally {
                    this.cargando = false;
                    if (this.componente.maquinas.length > 0 && this.componente.maquinas.some(m => m.requiere_programa == 1)) {
                        document.querySelector("html").classList.add('js');
                        let fileInput = document.querySelector(".input-file")
                        let button = document.querySelector(".input-file-trigger")

                        button.addEventListener("keydown", function(event) {
                            if (event.keyCode == 13 || event.keyCode == 32) {
                                fileInput.focus();
                            }
                        });

                        button.addEventListener("click", function(event) {
                            fileInput.focus();
                            return false;
                        });
                    }
                }
            },
            async cambiarEstatusProgramacion(band) {
                let t = this
                try {
                    const response = await axios.put(`api/programacion/cambio-estatus/${t.selectedComponente}`, {
                        estatus: band
                    });
                    if (response.data.success) {
                        t.componente.estatus_programacion = band;
                    }
                } catch (error) {
                    console.error('Error al cambiar el estatus de programación:', error);
                }
            },
            async verModalRuta() {
                let t = this
                await this.fetchComponente(t.selectedComponente);
                await this.cargarRuta();

                $('#modalRuta').modal()
                Vue.nextTick(function() {
                    Vue.nextTick(function() {
                        $('[data-toggle="tooltip"]').tooltip('dispose');
                        $('[data-toggle="tooltip"]').tooltip()
                    })
                })
            },
            getElipsis(str) {
                if (str && str.length > 45) {
                    return str.substring(0, 44) + '...';
                }
                return str;
            },
            handleFileChange(event, index1, index2) {
                const file = event.target.files[0];
                if (file) {
                    this.$set(this.componente.maquinas[index1].archivos[index2], "nombre", file.name);
                    this.$set(this.componente.maquinas[index1].archivos[index2], "archivo", file);
                }
            },
            agregarArchivo(maquina) {
                maquina.archivos.push({
                    nombre: '',
                    archivo: null
                });
                Vue.nextTick(function() {
                    document.querySelector("html").classList.add('js');
                    let fileInput = document.querySelector(".input-file")
                    let button = document.querySelector(".input-file-trigger")

                    button.addEventListener("keydown", function(event) {
                        if (event.keyCode == 13 || event.keyCode == 32) {
                            fileInput.focus();
                        }
                    });

                    button.addEventListener("click", function(event) {
                        fileInput.focus();
                        return false;
                    });
                })
            },
            eliminarArchivo(maquina, index) {
                maquina.archivos.splice(index, 1);
                if(maquina.archivos.length == 0){
                    this.componente.maquinas.splice(this.componente.maquinas.findIndex(m => m.maquina_id == maquina.maquina_id), 1);
                }
            },
            ajustarRutaAvance(tasks, rutaAvance) {
                let convertirAMinutos = (horas, minutos) => horas * 60 + minutos;

                let convertirAHorasYMinutos = (minutosTotales) => {
                    let horas = Math.floor(minutosTotales / 60);
                    let minutos = minutosTotales % 60;
                    return {
                        horas,
                        minutos
                    };
                };

                // Variables para rastrear el tiempo actual
                let tiempoActualEnMinutos = 60; // Empieza desde el inicio del proyecto

                // Recorremos las tareas de rutaAvance
                rutaAvance.forEach((tareaAvance) => {
                    // Buscar la tarea correspondiente en tasks
                    let tareaTeorica = tasks.find((t) => t.id === tareaAvance.id);

                    if (tareaTeorica) {
                        // Calcular tiempo total en tasks
                        let tiempoTotalTasks = tareaTeorica.time.reduce(
                            (total, tiempo) => total + convertirAMinutos(tiempo.horas, tiempo.minutos),
                            0
                        );

                        let tiempoTotalRutaAvance = tareaAvance.time.reduce(
                            (total, tiempo) => {
                                // Solo suma si el tipo no es 'delay'
                                if (tiempo.type !== 'delay') {
                                    return total + convertirAMinutos(tiempo.horas, tiempo.minutos);
                                }
                                return total; // Devuelve el total sin cambios si type === 'delay'
                            },
                            0 // Valor inicial del acumulador
                        );

                        // Si el tiempo de rutaAvance excede al de tasks
                        if (tiempoTotalRutaAvance > tiempoTotalTasks) {
                            let excesoMinutos = tiempoTotalRutaAvance - tiempoTotalTasks;

                            // Ajustar tiempo normal para que coincida con tasks
                            let tiempoNormal = tareaAvance.time.find((t) => t.type === "normal");
                            if (tiempoNormal) {
                                tiempoNormal.horas = Math.floor(tiempoTotalTasks / 60);
                                tiempoNormal.minutos = tiempoTotalTasks % 60;
                            }

                            // Agregar el tiempo de delay
                            let {
                                horas: horasDelay,
                                minutos: minutosDelay
                            } = convertirAHorasYMinutos(excesoMinutos);
                            tareaAvance.time.push({
                                hora_inicio: 0, // Este se ajustará después
                                minuto_inicio: 0,
                                horas: horasDelay,
                                minutos: minutosDelay,
                                type: "delay",
                            });
                        }

                        // Ajustar hora_inicio y minuto_inicio de cada segmento
                        tareaAvance.time.forEach((segmento) => {
                            let {
                                horas,
                                minutos
                            } = convertirAHorasYMinutos(tiempoActualEnMinutos);
                            segmento.hora_inicio = horas;
                            segmento.minuto_inicio = minutos;

                            // Avanzar el tiempo actual según la duración del segmento
                            tiempoActualEnMinutos += convertirAMinutos(segmento.horas, segmento.minutos);
                        });
                    }
                });

                return rutaAvance; // Devuelve la estructura modificada
            },
            calcularInicioAvance() {
                let t = this;
                let acumuladorHoras1 = 1;
                let acumuladorMinutos1 = 0;
                let acumuladorHoras2 = 1;
                let acumuladorMinutos2 = 0;

                this.rutaAvance.sort((a, b) => {
                    const prioridadA = this.procesos.find(p => p.id === a.id).prioridad;
                    const prioridadB = this.procesos.find(p => p.id === b.id).prioridad;
                    return prioridadA - prioridadB;
                });

                const tareasFijas = this.rutaAvance.filter(task => task.id === 1 || task.id === 2);
                const otrasTareas = this.rutaAvance.filter(task => task.id !== 1 && task.id !== 2);

                tareasFijas.forEach(task => {
                    let proceso = t.procesos.find(p => p.id === task.id);

                    task.time.forEach((segmento, index) => {

                        if (task.id == 1) {
                            segmento.hora_inicio = acumuladorHoras1;
                            segmento.minuto_inicio = acumuladorMinutos1;
                        } else {
                            segmento.hora_inicio = acumuladorHoras2;
                            segmento.minuto_inicio = acumuladorMinutos2;
                        }

                        if (task.id == 1) {
                            acumuladorHoras1 += segmento.horas;
                            acumuladorMinutos1 += segmento.minutos;
                        } else {
                            acumuladorHoras2 += segmento.horas;
                            acumuladorMinutos2 += segmento.minutos;
                        }
                    });
                });

                let maxHoras = 1;
                let maxMinutos = 0;
                tareasFijas.forEach(task => {
                    let totalHoras = 1;
                    let totalMinutos = 0;

                    task.time.forEach(segmento => {
                        totalHoras += segmento.horas;
                        totalMinutos += segmento.minutos;
                    });

                    totalHoras += Math.floor(totalMinutos / 60);
                    totalMinutos = totalMinutos % 60;

                    if (totalHoras > maxHoras || (totalHoras === maxHoras && totalMinutos > maxMinutos)) {
                        maxHoras = totalHoras;
                        maxMinutos = totalMinutos;
                    }
                });
                if (maxMinutos >= 60) {
                    maxHoras += Math.floor(maxMinutos / 60);
                    maxMinutos = maxMinutos % 60;
                }

                let acumuladorHoras = maxHoras;
                let acumuladorMinutos = maxMinutos;
                otrasTareas.forEach(task => {
                    let proceso = t.procesos.find(p => p.id === task.id);
                    task.time.forEach((segmento, index) => {
                        segmento.hora_inicio = acumuladorHoras;
                        segmento.minuto_inicio = acumuladorMinutos;

                        acumuladorHoras += segmento.horas;
                        acumuladorMinutos += segmento.minutos;

                        if (acumuladorMinutos >= 60) {
                            acumuladorHoras += Math.floor(acumuladorMinutos / 60);
                            acumuladorMinutos = acumuladorMinutos % 60;
                        }
                    });
                });
            },
            getContenidoTooltip(task) {
                let totalHoras = 0;
                let totalMinutos = 0;

                let normalTime = {
                    horas: 0,
                    minutos: 0
                };
                let reworkTime = {
                    horas: 0,
                    minutos: 0
                };
                let delayTime = {
                    horas: 0,
                    minutos: 0
                };

                task.time.forEach(t => {
                    if (t.type === 'normal') {
                        normalTime.horas += t.horas;
                        normalTime.minutos += t.minutos;
                    } else if (t.type === 'rework') {
                        reworkTime.horas += t.horas;
                        reworkTime.minutos += t.minutos;
                    } else if (t.type === 'delay') {
                        delayTime.horas += t.horas;
                        delayTime.minutos += t.minutos;
                    }
                });

                normalTime.horas += Math.floor(normalTime.minutos / 60);
                normalTime.minutos = normalTime.minutos % 60;

                reworkTime.horas += Math.floor(reworkTime.minutos / 60);
                reworkTime.minutos = reworkTime.minutos % 60;

                delayTime.horas += Math.floor(delayTime.minutos / 60);
                delayTime.minutos = delayTime.minutos % 60;

                totalHoras = normalTime.horas + reworkTime.horas + delayTime.horas;
                totalMinutos = normalTime.minutos + reworkTime.minutos + delayTime.minutos;

                totalHoras += Math.floor(totalMinutos / 60);
                totalMinutos = totalMinutos % 60;

                let tooltipContent = `
                        <div class="text-left">
                            <strong>${task.name}</strong><br>
                            <strong>Total:</strong> ${totalHoras} horas y ${totalMinutos} min <br><br>
                    `;
                if (normalTime.horas > 0 || normalTime.minutos > 0)
                    tooltipContent += `<strong>${task.name}:</strong> ${normalTime.horas} horas y ${normalTime.minutos} min <br>`;

                if (reworkTime.horas > 0 || reworkTime.minutos > 0)
                    tooltipContent += `<strong>Retrabajos:</strong> ${reworkTime.horas} horas y ${reworkTime.minutos} min <br>`;

                if (delayTime.horas > 0 || delayTime.minutos > 0)
                    tooltipContent += `<strong>Retrasos:</strong> ${delayTime.horas} horas y ${delayTime.minutos} min <br> ${this.getMotivoRetraso(task)}`;

                tooltipContent += `</div>`;
                return tooltipContent;
            },
            getMotivoRetraso(task) {
                switch (task.id) {
                    case 1:
                        return this.componente.retraso_corte ? `(${this.componente.retraso_corte})` : '';
                        break
                    case 2:
                        return this.componente.retraso_programacion ? `(${this.componente.retraso_programacion})` : '';
                        break
                    case 3:
                    case 4:
                    case 5:
                    case 6:
                    case 7:
                    case 8:
                        let fabricaciones = this.componente.fabricaciones.filter(element => element.proceso_id == task.id)
                        let motivosRetraso = fabricaciones.map(f => f.motivo_retraso).filter(motivo => motivo).join(', ')
                        return motivosRetraso ? `(${motivosRetraso})` : '';
                        break
                }
            },
            isTaskInHour(segment, hour) {
                const start = segment.hora_inicio + segment.minuto_inicio / 60; // Hora de inicio en formato decimal
                const end = start + segment.horas + segment.minutos / 60; // Hora de fin en formato decimal
                return start < hour + 1 && end > hour;
            },
            getTaskStyle(segment, hour) {
                const start = segment.hora_inicio + segment.minuto_inicio / 60; // Hora de inicio con minutos
                const end = start + segment.horas + segment.minutos / 60; // Hora de fin con duración

                // Calcula el porcentaje de la barra que corresponde a esta hora
                const startPercentage = Math.max(0, (Math.max(start, hour) - hour) * 100); // Posición de inicio en porcentaje
                const endPercentage = Math.min(100, (Math.min(end, hour + 1) - hour) * 100); // Posición de fin en porcentaje

                // Asegúrate de que el ancho de la barra no sea negativo (por ejemplo, si la tarea comienza antes de la hora)
                const width = Math.max(0, endPercentage - startPercentage);

                return {
                    left: `${startPercentage}%`, // Posición inicial de la barra
                    width: `${width}%` // Ancho de la barra
                };
            },
            regresar(step) {
                switch (step) {
                    case 1:
                        this.ruta = {
                            anio: null,
                            cliente: null,
                            proyecto: null,
                            herramental: null,
                            componente: null,
                        }
                        this.selectedAnio = null;
                        this.selectedCliente = null;
                        this.selectedProyecto = null;
                        this.selectedHerramental = null;
                        this.selectedComponente = null;
                        break;
                    case 2:
                        this.ruta.cliente = null;
                        this.ruta.proyecto = null;
                        this.ruta.herramental = null;
                        this.ruta.componente = null;

                        this.selectedCliente = null;
                        this.selectedProyecto = null;
                        this.selectedHerramental = null;
                        this.selectedComponente = null;

                        break;
                    case 3:
                        this.ruta.proyecto = null;
                        this.ruta.herramental = null;
                        this.ruta.componente = null;

                        this.selectedProyecto = null;
                        this.selectedHerramental = null;
                        this.selectedComponente = null;
                        break;
                    case 4:
                        this.ruta.herramental = null;
                        this.selectedHerramental = null;
                        this.ruta.componente = null;
                        break;
                    case 5:
                        this.ruta.componente = null;
                        this.selectedComponente = null;
                        this.selectedComponente = null;
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
            async fetchMaquinas() {
                this.cargando = true
                try {
                    const response = await axios.get('/api/maquinas');
                    this.maquinas = response.data.maquinas.filter(maq => maq.tipo_proceso != 10);
                } catch (error) {
                    console.error('Error fetching maquinas:', error);
                } finally {
                    this.cargando = false;
                }
            },
            async fetchProgramadores() {
                try {
                    const response = await axios.get('/api/programadores');
                    this.programadores = response.data.programadores;
                } catch (error) {
                    console.error('Error fetching programs:', error);
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
            async fetchComponentes(herramentalId) {
                this.cargando = true
                this.selectedHerramental = herramentalId;
                this.ruta.herramental = this.herramentales.find(obj => obj.id == herramentalId)?.nombre;

                try {
                    const response = await axios.get(`/api/herramentales/${herramentalId}/componentes?area=programador`);
                    this.componentes = response.data.componentes;
                    this.menuStep = 5;
                } catch (error) {
                    console.error('Error fetching componentes:', error);
                } finally {
                    this.cargando = false;
                    Vue.nextTick(function() {
                        $('[data-toggle="tooltip"]').tooltip();
                    })
                }
            },
            async liberar() {
                let t = this
                if (!this.componente.descripcion_trabajo?.trim() || !this.componente.herramientas_corte?.trim()) {
                    swal('Errores de validación', `Todos los campos son obligatorios para liberar el componente.`, 'error');
                    return;
                }
                
                //verificar procesos
                let maquinasCargadas = t.componente.maquinas.map(m => m.maquina_id);
                let procesosRequierenMaquina = t.tasks
                    .map(task => task.id)
                    .filter(id => t.procesosValidos.includes(id));

                let procesosCubiertos = t.maquinas
                    .filter(m => maquinasCargadas.includes(m.id))
                    .map(m => parseInt(m.tipo_proceso));

                for (let procesoId of procesosRequierenMaquina) {
                    if (!procesosCubiertos.includes(procesoId)) {
                        let proceso = t.tasks.find(t => t.id === procesoId);
                        swal(
                            'Errores de validación',
                            `Debe asignar una máquina que cubra el proceso "${proceso?.name ?? 'ID ' + procesoId}".`,
                            'error'
                        );
                        return;
                    }
                }

                let todasTienenArchivo = this.componente.maquinas.every(maquina =>
                    Array.isArray(maquina.archivos) &&
                    maquina.archivos.some(archivo =>
                        archivo.archivo instanceof File ||                             // archivo nuevo (File)
                        (typeof archivo.archivo === 'string' && archivo.archivo.trim() !== '') || // string no vacía
                        archivo.id ||                                                  // archivo ya guardado
                        (archivo.nombre && archivo.nombre.trim() !== '')              // nombre de archivo existente
                    )
                );

                if (!todasTienenArchivo) {
                    swal('Errores de validación', 'Cada máquina debe tener al menos un archivo cargado.', 'error');
                    return;
                }

                this.hay_retraso = false;
                await this.fetchComponente(this.selectedComponente);
                await this.cargarRuta();
                let programar = this.rutaAvance.find(obj => obj.id === 2)

                if (programar) {
                    let retraso = programar.time.find(obj => obj.type === 'delay')
                    if (retraso) {
                        this.hay_retraso = true;
                        $('#modalRetraso').modal();
                        return;
                    }
                }
                this.guardar(true);
            },
            async guardar(liberarComponente) {
                let t = this

                if (this.hay_retraso && liberarComponente && !this.componente.retraso_programacion?.trim()) {
                    swal('Campos obligatorios', 'Debe ingresar un motivo de retraso.', 'info');
                    return false;
                }
                t.cargando = true;
                t.loading_button = true;

                let formData = new FormData();

                t.componente.maquinas.forEach((maquina, maquinaIndex) => {
                    maquina.archivos.forEach((archivo, archivoIndex) => {
                        if (archivo.archivo)
                            formData.append(`archivo[${maquina.maquina_id}][${archivoIndex}]`, archivo.archivo);
                        if (archivo.id)
                            formData.append(`archivo_ids[${maquina.maquina_id}][${archivoIndex}]`, archivo.id);
                    });
                });
                formData.append('data', JSON.stringify(t.componente));

                try {
                    const response = await axios.post(`/api/componente/${t.selectedComponente}/programacion/${liberarComponente}`, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });
                    if(response.data.success === false) {
                        swal('Lo sentimos!', response.data.message, 'error');
                        t.cargando = false;
                        t.loading_button = false;
                    }else{
                        swal('Correcto', liberarComponente ? 'Componente liberado correctamente' : 'Información guardada correctamente', 'success');
                        t.loading_button = false;
                    }
                    await t.fetchComponentes(t.selectedHerramental);
                    await t.fetchComponente(t.selectedComponente);
                    $('#modalRetraso').modal('hide');

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
                const componenteId = queryParams.get('co');

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
                        await this.fetchComponentes(herramentalId);
                    }
                    if (componenteId && componenteId != 'null') {
                        await this.fetchComponente(componenteId);
                    }
                } catch (error) {
                    console.error("Error navigating from URL parameters:", error);
                }
            },
        },
        mounted: async function() {
            let t = this;
            await t.fetchMaquinas();
            await t.fetchAnios();
            await t.fetchProgramadores();
            this.navigateFromUrlParams();
        }


    })
</script>




@endpush