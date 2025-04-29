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
                        <div class="nav flex-column nav-pills " id="v-pills-tab" role="tablist" aria-orientation="vertical">
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
                                <a class="d-flex align-items-center nav-link cursor-pointer" v-for="obj in clientes" @click="fetchProyectos(obj.id)" v-if="obj.nombre != 'ORDENES EXTERNAS'">
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
                        <h2 class="bold my-0 py-1 mb-3 text-decoration-underline" style="letter-spacing: 2px">REPORTE DE FINANZAS </h2> 
                    </div>
                    
                    <div class="col-12" v-if="!selectedProyecto">
                        <h5 class="text-muted my-4"> SELECCIONE UN PROYECTO PARA VER LOS REPORTES DISPONIBLES</h5>
                    </div>
                    <div class="col-3" v-if="selectedProyecto && !loading">
                        <div class="row">
                            <div class="col-12" >
                                <div class="card" style="box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);">
                                    <div class="card-header text-center">
                                        <h3 style="letter-spacing: 1px" class="bold pb-0 mb-1"><small>HORAS DE MAQUINADO</small></h3><hr class="my-0 py-0">
                                    </div>
                                    <div class="card-footer text-center py-2" >
                                        <h4 style="letter-spacing: 1px" class="bold my-0 py-0"><small>Horas máquina <br> </small> @{{tiempos.maquinado_horas}} Horas y @{{tiempos.maquinado_minutos}} Minutos</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12" >
                                <div class="card" style="box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);">
                                    <div class="card-header text-center">
                                        <h5 style="letter-spacing: 1px" class="bold pb-0 my-1"><small>PAROS / RETRABAJOS / MODIFICACIONES</small></h5><hr class="my-0 py-0">
                                    </div>
                                    <div class="card-footer text-center py-2" >
                                        <h4 style="letter-spacing: 1px" class="bold my-0 py-0"><small>Retrabajos <br> </small> @{{tiempos.retrabajo_horas}} Horas y @{{tiempos.retrabajo_minutos}} Minutos</h4>
                                        <h4 style="letter-spacing: 1px" class="bold my-0 py-0"><small>Paros <br> </small> @{{tiempos.paro_horas}} Horas y @{{tiempos.paro_minutos}} Minutos</h4>
                                        <h4 style="letter-spacing: 1px" class="bold my-0 py-0"><small>Modificaciones <br> </small> @{{tiempos.modificacion_horas}} Horas y  @{{tiempos.modificacion_minutos}} Minutos</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12" >
                                <div class="card" style="box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);">
                                    <div class="card-header text-center">
                                        <h3 style="letter-spacing: 1px" class="bold pb-0 my-1"><small>TIEMPO DE PRUEBAS</small></h3><hr class="my-0 py-0">
                                    </div>
                                    <div class="card-footer text-center py-2" >
                                        <h4 style="letter-spacing: 1px" class="bold my-0 py-0"><small>Pruebas de diseño <br> </small> @{{tiempos.diseno_horas}} Horas y  @{{tiempos.diseno_minutos}} Minutos</h4>
                                        <h4 style="letter-spacing: 1px" class="bold my-0 py-0"><small>Pruebas de proceso <br> </small>  @{{tiempos.proceso_horas}} Horas y  @{{tiempos.proceso_minutos}} Minutos</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-9" v-if="!loading && tiempos">
                        <div class="col-12 text-center mt-2">
                            <h4 v-if="selectedProyecto" class="my-0 py-2 mb-0 text-center" style="letter-spacing: 1px">RESUMEN DE TIEMPOS DE PRODUCCIÓN Y PROCESOS DEL PROYECTO @{{ruta.proyecto}} </h4> 
                        </div>
                        <canvas id="graficaTiempos"></canvas>
                    </div>
                    <div class="col-12 text-center pt-5" v-if="selectedProyecto && loading">
                        <h5 class="mt-5 bold" style="letter-spacing: 1px">CARGANDO INFORMACIÓN, ESPERE UN MOMENTO...</h5>
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
            loading: false,
            cargando: false,
            //MENU IZQUIERDO 
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
            },
        },
        watch: {

        },
        computed: {
           
        },
        methods: {
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
            renderGrafica() {
                if (this.chartInstance) {
                    this.chartInstance.destroy(); // evitar duplicados si se vuelve a cargar
                }

                const labels = [
                    "Horas de Maquinado",
                    "Retrabajos",
                    "Paros",
                    "Modificaciones",
                    "Pruebas de Diseño",
                    "Pruebas de Proceso"
                ];

                const tiempos = this.tiempos;
                const data = [
                    (tiempos.maquinado_horas * 60) + tiempos.maquinado_minutos,
                    (tiempos.retrabajo_horas * 60) + tiempos.retrabajo_minutos,
                    (tiempos.paro_horas * 60) + tiempos.paro_minutos,
                    (tiempos.modificacion_horas * 60) + tiempos.modificacion_minutos,
                    (tiempos.diseno_horas * 60) + tiempos.diseno_minutos,
                    (tiempos.proceso_horas * 60) + tiempos.proceso_minutos
                ];

                const ctx = document.getElementById("graficaTiempos").getContext("2d");

                this.chartInstance = new Chart(ctx, {
                    type: "bar",
                    data: {
                        labels,
                        datasets: [{
                            label: "Tiempo (minutos)",
                            data,
                            backgroundColor: [
                                "#4e73df",
                                "#f6c23e",
                                "#e74a3b",
                                "#36b9cc",
                                "#1cc88a",
                                "#858796"
                            ]
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
                                        return `${hrs}h ${min}m`;
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