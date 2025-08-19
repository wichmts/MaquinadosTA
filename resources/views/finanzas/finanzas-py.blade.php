@extends('layouts.app', [
'class' => '',
'elementActive' => 'dashboard'
])

@section('styles')
<link rel="stylesheet" href="{{ asset('paper/css/paper-dashboard-responsivo.css') }}?v={{ time() }}">
<link href="http://ghinda.net/css-toggle-switch/dist/toggle-switch.css" rel="stylesheet">

@endsection

<style>

</style>

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
                                <a class="d-flex align-items-center nav-link cursor-pointer" v-for="obj in clientes" @click="fetchProyectos(obj.id)" v-if="obj.nombre != 'ORDENES EXTERNAS' && obj.nombre != 'REFACCIONES'">
                                    <i class="nc-icon" style="top: -3px !important"><img height="20px" src="{{ asset('paper/img/icons/carpetas.png') }}"></i> &nbsp;
                                    <span class="underline-hover">@{{obj.nombre}}</span>
                                </a>
                            </div>
                            <div v-if="!cargandoMenu && menuStep == 3">
                                <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> PROYECTOS </a>
                                <a class="d-flex align-items-center nav-link cursor-pointer" v-for="obj in proyectos" @click="seleccionarProyecto(obj.id)">
                                    <i class="nc-icon" style="top: -3px !important"><img height="20px" src="{{ asset('paper/img/icons/carpetas.png') }}"></i> &nbsp;
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
                            <span class="cursor-pointer pb-2" v-if="ruta.proyecto"><i class="fa fa-angle-right"></i> &nbsp; <span class="underline-hover">@{{ruta.proyecto}}</span> &nbsp;</span>
                        </p>
                    </div>
                </div>
            </nav>

            <div class="content" style="max-height: 80vh; overflow-y: scroll">
                <div class="row mb-2 ">
                    <div class="col-12">
                        <h2 class="bold my-0 py-1 mb-3 text-decoration-underline" style="letter-spacing: 2px">REPORTE DE FINANZAS (PY)</h2> 
                    </div>
                    <div class="col-12" v-show="!selectedProyecto">
                        <h5 class="text-muted my-4"> SELECCIONE UN PROYECTO PARA VER LOS REPORTES DISPONIBLES</h5>
                    </div>
                    <div class="col-12 text-center pt-5" v-show="selectedProyecto && loading">
                        <h5 class="mt-5 bold" style="letter-spacing: 1px">CARGANDO INFORMACIÓN, ESPERE UN MOMENTO...</h5>
                    </div>
                    <div class="col-lg-12" v-show="selectedProyecto && !loading">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="tiempos-produccion-tab" data-toggle="tab" data-target="#tiempos-produccion" type="button" role="tab" aria-controls="tiempos-produccion" aria-selected="true">TIEMPOS DE PRODUCCIÓN</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="materia-prima-tab" data-toggle="tab" data-target="#materia-prima" type="button" role="tab" aria-controls="materia-prima" aria-selected="false">MATERIA PRIMA</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="comprados-tab" data-toggle="tab" data-target="#comprados" type="button" role="tab" aria-controls="comprados" aria-selected="false">COMPONENTES COMPRADOS</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="tiempos-produccion" role="tabpanel" aria-labelledby="tiempos-produccion-tab">
                                <div class="row mt-4 px-4">
                                    <div class="col-lg-3">
                                        <div class="row">
                                            <div class="col-12" >
                                                <div class="card" style="box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);">
                                                    <div class="card-header text-center">
                                                        <h3 style="letter-spacing: 1px" class="bold pb-0 mb-1"><small>HORAS DE MAQUINADO</small></h3><hr class="my-0 py-0" style="height: 10px !important">
                                                    </div>
                                                    <div class="card-footer text-center py-2" >
                                                        <h4 style="letter-spacing: 1px" class="bold my-0 py-0"><small>Horas máquina <br> </small> @{{tiempos.maquinado_horas}} Horas y @{{tiempos.maquinado_minutos}} Minutos</h4>
                                                        <h4 style="letter-spacing: 1px" class="bold my-0 py-0"><small>Costo producción <br> </small> @{{tiempos.costoTotalMaquinado | currency}} </h4>
                                                        <button class="btn btn-sm btn-default mt-2 py-1" @click="verDetalleCostos"><i class="fa fa-info-circle"></i> Ver detalle</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12" >
                                                <div class="card" style="box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);">
                                                    <div class="card-header text-center">
                                                        <h5 style="letter-spacing: 1px" class="bold pb-0 my-1"><small>PAROS / RETRABAJOS / MODIFICACIONES</small></h5><hr class="my-0 py-0" style="height: 10px !important">
                                                    </div>
                                                    <div class="card-footer text-center py-2" >
                                                        <h4 style="letter-spacing: 1px" class="bold my-0 py-0"><small>Retrabajos <br> </small> @{{tiempos.retrabajo_horas}} Horas y @{{tiempos.retrabajo_minutos}} Minutos</h4>
                                                        <h4 style="letter-spacing: 1px" class="bold my-0 py-0"><small>Paros <br> </small> @{{tiempos.paro_horas}} Horas y @{{tiempos.paro_minutos}} Minutos</h4>
                                                        <h4 style="letter-spacing: 1px" class="bold my-0 py-0"><small>Modificaciones <br> </small> @{{tiempos.modificacion_horas}} Horas y  @{{tiempos.modificacion_minutos}} Minutos</h4>
                                                        <button class="btn btn-sm btn-default mt-2 py-1" @click="verDetalleRetrasos"><i class="fa fa-info-circle"></i> Ver detalle</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12" >
                                                <div class="card" style="box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);">
                                                    <div class="card-header text-center">
                                                        <h3 style="letter-spacing: 1px" class="bold pb-0 my-1"><small>TIEMPO DE PRUEBAS</small></h3><hr class="my-0 py-0" style="height: 10px !important">
                                                    </div>
                                                    <div class="card-footer text-center py-2" >
                                                        <h4 style="letter-spacing: 1px" class="bold my-0 py-0"><small>Pruebas de diseño <br> </small> @{{tiempos.diseno_horas}} Horas y  @{{tiempos.diseno_minutos}} Minutos</h4>
                                                        <h4 style="letter-spacing: 1px" class="bold my-0 py-0"><small>Pruebas de proceso <br> </small>  @{{tiempos.proceso_horas}} Horas y  @{{tiempos.proceso_minutos}} Minutos</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-9" >
                                        <div class="col-12 text-center mt-2">
                                            <h4 class="my-0 py-2 mb-0 text-center" style="letter-spacing: 1px">RESUMEN DE TIEMPOS DE PRODUCCIÓN Y PROCESOS PARA @{{ruta.proyecto}} </h4> 
                                        </div>
                                        <canvas id="graficaTiempos"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="materia-prima" role="tabpanel" aria-labelledby="materia-prima-tab">
                                <div class="row mt-4 px-4">
                                    <div class="col-lg-3">
                                        <div class="row">
                                            <div class="col-12" v-for="mp in tiempos.reporte_materia_prima" :key="mp.hoja_descripcion">
                                                <div class="card" style="box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);">
                                                    <div class="card-header text-center">
                                                        <h3 style="letter-spacing: 1px" class="bold pb-0 mb-1">@{{mp.material}} <br> <small>@{{mp.hoja_descripcion}}</small></h3><hr class="my-0 py-0" style="height: 10px !important">
                                                    </div>
                                                    <div class="card-footer text-center py-2" >
                                                        <h4 style="letter-spacing: 1px" class="bold my-0 py-0"><small>Material utilizado: <br> </small> @{{mp.peso_total}} Kg</h4>
                                                        <h4 style="letter-spacing: 1px" class="bold my-0 py-0"><small>Precio / Kg: <br> </small> @{{mp.precio_kilo | currency}}</h4>
                                                        <h4 style="letter-spacing: 1px" class="bold my-0 py-0"><small>Costo materia prima: <br> </small> @{{mp.costo_total | currency}}</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-9 text-center" >
                                        <div class="col-12 text-center mt-2">
                                            <h4 class="my-0 py-2 mb-0 text-center" style="letter-spacing: 1px">DISTRIBUCIÓN DE COSTOS DE MATERIA PRIMA PARA @{{ruta.proyecto}} </h4> 
                                        </div>
                                        <canvas id="graficaMP"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="comprados" role="tabpanel" aria-labelledby="comprados-tab">
                                <div class="row mt-4 px-4">
                                    <div class="col-lg-3">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="card" style="box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);">
                                                    <div class="card-header text-center">
                                                        <h3 style="letter-spacing: 1px" class="bold pb-0 mb-1">COMPONENTES</h3><hr class="my-0 py-0" style="height: 10px !important">
                                                    </div>
                                                    <div class="card-footer text-center py-2" >
                                                        <h4 style="letter-spacing: 1px" class="bold my-0 py-0"><small>Total componentes: <br> </small> @{{tiempos.total_componentes_comprados}}</h4>
                                                        <h4 style="letter-spacing: 1px" class="bold my-0 py-0"><small>Comprados: <br> </small> @{{tiempos.total_componentes_pagados}}</h4>
                                                        <h4 style="letter-spacing: 1px" class="bold my-0 py-0"><small>Reutilizados:<br> </small> @{{tiempos.total_componentes_reutilizados }}</h4>
                                                    </div>
                                                </div>
                                                <div class="card" style="box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);">
                                                    <div class="card-header text-center">
                                                        <h3 style="letter-spacing: 1px" class="bold pb-0 mb-1">COSTOS</h3><hr class="my-0 py-0" style="height: 10px !important">
                                                    </div>
                                                    <div class="card-footer text-center py-2 mb-2" >
                                                        <h4 style="letter-spacing: 1px" class="bold my-0 py-0"><small>Total: <br> </small> @{{tiempos.total_costo_compras| currency}}</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-9 text-center" >
                                        <div class="col-12 text-center mt-2">
                                            <h4 class="my-0 py-2 mb-0 text-center" style="letter-spacing: 1px">RESUMEN DE COSTOS DE COMPONENTES COMPRADOS PARA @{{ruta.proyecto}} </h4> 
                                        </div>
                                        <canvas id="graficaCompras"></canvas>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="modalRetrasos" tabindex="-1" role="dialog" aria-labelledby="modalRetrasosLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title bold" id="modalRetrasosLabel">DETALLE DE RETRABAJOS, PAROS Y MODIFICACIONES</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <table class="table">
                                <thead class="thead-light">
                                    <tr >
                                        <th class="py-1" style="background-color: #f1c40f !important; color: white !important; letter-spacing: 1px" colspan="5" >RETRABAJOS</th>
                                    </tr>
                                    <tr>
                                        <th>Componente</th>
                                        <th>Fecha</th>
                                        <th>Area solicitante</th>
                                        <th>Detalle</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(retrabajo, index) in detalleRetrasos.retrabajos" :key="'retrabajo-det-' + index">
                                        <td>@{{retrabajo.componente}} <small v-if="retrabajo.version > 1">v@{{retrabajo.version}}</small></td>
                                        <td>@{{retrabajo.created_at}} Hrs.</td>
                                        <td>@{{retrabajo.area_solicitante}}</td>
                                        <td><a :href="'/visor-avance-hr' + retrabajo.rutaVisor" target="_blank" class="btn btn-sm btn-default"><i class="fa fa-eye"></i> Visor de avance HR </a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="col-lg-12">
                            <table class="table">
                                <thead class="thead-light">
                                    <tr >
                                        <th class="py-1" style="background-color: #e74c3c !important; color: white !important; letter-spacing: 1px" colspan="5" >PAROS</th>
                                    </tr>
                                    <tr>
                                        <th>Componente</th>
                                        <th>Fecha</th>
                                        <th>Tipo de paro</th>
                                        <th>Detalle</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(paro, index) in detalleRetrasos.paros" :key="'paro-det-' + index">
                                        <td>@{{paro.componente}} <small v-if="paro.version > 1">v@{{paro.version}}</small></td>
                                        <td>@{{paro.fecha}} @{{paro.hora}} Hrs.</td>
                                        <td>@{{paro.tipo_paro}}</td>
                                        <td><a :href="'/visor-avance-hr' + paro.rutaVisor" target="_blank" class="btn btn-sm btn-default"><i class="fa fa-eye"></i> Visor de avance HR </a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="col-lg-12">
                            <table class="table">
                                <thead class="thead-light">
                                    <tr >
                                        <th class="py-1" style="background-color: #3498db !important; color: white !important; letter-spacing: 1px" colspan="5" >MODIFICACIONES</th>
                                    </tr>
                                    <tr>
                                        <th>Componente</th>
                                        <th>Fecha</th>
                                        <th>Area solicitante</th>
                                        <th>Detalle</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(modificacion, index) in detalleRetrasos.modificaciones" :key="'paro-det-' + index">
                                        <td>@{{modificacion.componente}} <small v-if="modificacion.version > 1">v@{{modificacion.version}}</small></td>
                                        <td>@{{modificacion.created_at}} Hrs.</td>
                                        <td>@{{modificacion.area_solicitante}}</td>
                                        <td><a :href="'/visor-avance-hr' + modificacion.rutaVisor" target="_blank" class="btn btn-sm btn-default"><i class="fa fa-eye"></i> Visor de avance HR</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalCostos" tabindex="-1" role="dialog" aria-labelledby="modalCostosLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title bold" id="modalCostosLabel">DETALLE DE COSTOS DE FABRICACIÓN POR MAQUINA </h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <table class="table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Maquina</th>
                                        <th>Horas maquina</th>
                                        <th>Costo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(maquina, index) in tiempos.costosMaquinas" :key="'costos-maq-det-' + index">
                                        <td>@{{maquina.nombre}}</td>
                                        <td>@{{maquina.tiempo_horas}} Horas y @{{maquina.tiempo_minutos}} Minutos</td>
                                        <td>@{{maquina.costo | currency}}</td>
                                    </tr>
                                    <tr class="border-top: 1px solid #333">
                                        <td class="bold" style="letter-spacing: 2px">TOTALES</td>
                                        <td class="bold">@{{tiempos.maquinado_horas}} Horas y @{{tiempos.maquinado_minutos}} Minutos</td>
                                        <td class="bold">@{{tiempos.costoTotalMaquinado | currency}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
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
            loading: false,
            cargando: false,
            anios: [],
            clientes: [],
            proyectos: [],
            cargandoMenu: true,
            menuStep: 1,
            selectedAnio: null,
            selectedCliente: null,
            selectedProyecto: null,
            ruta: {
                anio: null,
                cliente: null,
                proyecto: null,
            },
            tiempos: {
                maquinado_horas: 0,
                maquinados_minutos: 0,
                costosMaquinas: [],
                costoTotalMaquinado: 0,
            },
            chartInstance: null,
            chartInstance2: null,
            chartInstance3: null,
            detalleRetrasos: {
                retrabajos: [],
                paros: [],
                modificaciones: [],
            },
        },
        watch: {

        },
        computed: {
           
        },
        methods: {
            goToHerramental(ruta){
                window.location.href = '/visor-avance-hr/' + ruta;
            },
            verDetalleRetrasos() {
                this.loading = true;
                axios.get('/api/proyectos/' + this.selectedProyecto + '/retrasos')
                .then(response => {
                    this.detalleRetrasos = response.data.retrasos;
                    this.loading = false;
                    $('#modalRetrasos').modal('show');
                })
                .catch(error => {
                    console.error('Error fetching retrasos:', error);
                    this.loading = false;
                });
            },
            verDetalleCostos() {
                $('#modalCostos').modal('show');
            },
            regresar(step) {
                switch (step) {
                    case 1:
                        this.ruta = {
                            anio: null,
                            cliente: null,
                            proyecto: null,
                        }
                        this.selectedAnio = null;
                        this.selectedCliente = null;
                        this.selectedProyecto = null;
                        break;
                    case 2:
                        this.ruta.cliente = null;
                        this.ruta.proyecto = null;
                        this.selectedCliente = null;
                        this.selectedProyecto = null;
                        break;
                    case 3:
                        this.ruta.proyecto = null;
                        this.selectedProyecto = null;
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
            async seleccionarProyecto(proyectoId) {
                this.selectedProyecto = proyectoId;
                this.ruta.proyecto = this.proyectos.find(obj => obj.id == proyectoId)?.nombre;
                this.loading = true;
                try {
                    const response = await axios.get(`/api/proyectos/${proyectoId}/finanzas`);
                    this.tiempos = response.data.tiempos;
                    this.loading = false;
                    Vue.nextTick(() => {
                        this.renderGrafica();
                        this.renderGrafica2();
                        this.renderGrafica3();
                    });
                } catch (error) {
                    console.error('Error fetching proyectos:', error);
                    this.loading = false;
                    swal({
                        title: "Error",
                        text: "Ha ocurrido un error al obtener los tiempos del proyecto, intentelo más tarde.",
                        icon: "error",
                        button: "Aceptar",
                    });
                } finally {
                    this.loading = false;
                }
            },
            renderGrafica2() {
                if (this.chartInstance2) {
                    this.chartInstance2.destroy(); // Evitar duplicados si se vuelve a cargar
                }

                const ctx = document.getElementById("graficaMP").getContext("2d");

                const labels = this.tiempos.reporte_materia_prima.map(r => r.material + ' ' + r.hoja_descripcion);
                const data = this.tiempos.reporte_materia_prima.map(r => r.costo_total);

                const backgroundColors = [
                    "#4e73df",
                    "#f6c23e",
                    "#e74a3b",
                    "#36b9cc",
                    "#1cc88a",
                    "#858796",
                    "#4e73df",
                    "#f6c23e",
                    "#e74a3b",
                    "#36b9cc",
                    "#1cc88a",
                    "#858796",
                    "#4e73df",
                    "#f6c23e",
                    "#e74a3b",
                    "#36b9cc",
                    "#1cc88a",
                    "#858796",
                    "#4e73df",
                    "#f6c23e",
                    "#e74a3b",
                    "#36b9cc",
                    "#1cc88a",
                    "#858796",
                    "#4e73df",
                    "#f6c23e",
                    "#e74a3b",
                    "#36b9cc",
                    "#1cc88a",
                    "#858796",
                    "#4e73df",
                    "#f6c23e",
                    "#e74a3b",
                    "#36b9cc",
                    "#1cc88a",
                    "#858796",
                ];

                this.chartInstance2 = new Chart(ctx, {
                    type: "bar",
                    data: {
                        labels,
                        datasets: [{
                            label: "Costo por hoja ($)",
                            data,
                            backgroundColor: backgroundColors.slice(0, labels.length)
                        }]
                    },
                    options: {
                        indexAxis: "y", // barra horizontal
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: "COSTO TOTAL DE MATERIA PRIMA: $" + this.tiempos.total_materia_prima.toFixed(2),
                            },
                           tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.dataset.label || '';
                                        const value = context.parsed.x; // x porque es gráfico horizontal
                                        return `$${value.toFixed(2)}`;
                                    }
                                }
                            },

                            legend: {
                                display: false // sin leyenda, ya que cada barra tiene su propio label
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: "Costo ($)"
                                }
                            },
                            y: {
                                ticks: {
                                    autoSkip: false,
                                    maxRotation: 0
                                }
                            }
                        }
                    }
                });
            },
            renderGrafica() {
                if (this.chartInstance) {
                    this.chartInstance.destroy();
                }

                const tiempos = this.tiempos;

                const labels = [
                    "Horas de Maquinado",
                    "Retrabajos",
                    "Paros",
                    "Modificaciones",
                    // las pruebas individuales se agregarán abajo
                ];

                const data = [
                    (tiempos.maquinado_horas * 60) + tiempos.maquinado_minutos,
                    (tiempos.retrabajo_horas * 60) + tiempos.retrabajo_minutos,
                    (tiempos.paro_horas * 60) + tiempos.paro_minutos,
                    (tiempos.modificacion_horas * 60) + tiempos.modificacion_minutos,
                ];

                const backgroundColors = [
                    "#4e73df", // Maquinado
                    "#f6c23e", // Retrabajos
                    "#e74a3b", // Paros
                    "#36b9cc"  // Modificaciones
                ];

                // Agregar pruebas de diseño
                if (Array.isArray(tiempos.pruebasDiseno)) {
                    tiempos.pruebasDiseno.forEach((prueba, index) => {
                        labels.push(`P. Diseño - ${prueba.nombre}`);
                        data.push((prueba.horas * 60) + prueba.minutos);
                        backgroundColors.push("#1cc88a"); // Verde para diseño
                    });
                }

                // Agregar pruebas de proceso
                if (Array.isArray(tiempos.pruebasProceso)) {
                    tiempos.pruebasProceso.forEach((prueba, index) => {
                        labels.push(`P. Proceso - ${prueba.nombre}`);
                        data.push((prueba.horas * 60) + prueba.minutos);
                        backgroundColors.push("#858796"); // Gris para proceso
                    });
                }

                const ctx = document.getElementById("graficaTiempos").getContext("2d");

                this.chartInstance = new Chart(ctx, {
                    type: "bar",
                    data: {
                        labels,
                        datasets: [{
                            label: "Tiempo (minutos)",
                            data,
                            backgroundColor: backgroundColors
                        }]
                    },
                    options: {
                        indexAxis: "y",
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (context) => {
                                        const totalMin = context.raw;
                                        const hrs = Math.floor(totalMin / 60);
                                        const min = totalMin % 60;
                                        return `${context.label}: ${hrs}h ${min}m`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: "Tiempo total en minutos"
                                }
                            }
                        }
                    }
                });
            },
            renderGrafica3() {
                if (this.chartInstance3) {
                    this.chartInstance3.destroy(); // evitar duplicados si se vuelve a cargar
                }

                const tiempos = this.tiempos;

                const labels = [
                    "Total componentes",
                    "Componentes comprados",
                    "Componentes reutilizados"
                ];

                const data = [
                    tiempos.total_componentes_comprados || 0,
                    tiempos.total_componentes_pagados || 0,
                    tiempos.total_componentes_reutilizados || 0
                ];

                const ctx = document.getElementById("graficaCompras").getContext("2d");

                this.chartInstance3 = new Chart(ctx, {
                    type: "bar",
                    data: {
                        labels,
                        datasets: [{
                            label: "Cantidad de componentes",
                            data,
                            backgroundColor: [
                                "#4e73df",
                                "#1cc88a",
                                "#f6c23e"
                            ]
                        }]
                    },
                    options: {
                        indexAxis: 'y', // Esto hace la gráfica horizontal
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (context) => {
                                        const value = context.raw || 0;
                                        return `${value} componente(s)`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: "Componentes"
                                },
                                beginAtZero: true, 
                                stepSize: 1 // Espacio entre las barras
                            }
                        }
                    }
                });
            },
            async navigateFromUrlParams() {
                const queryParams = new URLSearchParams(window.location.search);
                const anioId = queryParams.get('a');
                const clienteId = queryParams.get('c');
                const proyectoId = queryParams.get('p');

                try {
                    if (anioId) {
                        await this.fetchClientes(anioId);
                    }
                    if (clienteId) {
                        await this.fetchProyectos(clienteId);
                    }
                    if (proyectoId) {
                        await this.seleccionarProyecto(proyectoId);
                    }
                } catch (error) {
                    console.error("Error navigating from URL parameters:", error);
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