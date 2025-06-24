@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'dashboard'
])

<style>
    .btn-group i{
        letter-spacing: 0px !important;
    }
    .btn-group .actions{
        padding-left: 10px !important;
        padding-right: 10px !important;
    }
    .loader {
        border: 16px solid hsla(0,0%,87%,.3); /* Light grey */
        border-top: 16px solid #121935;
        border-radius: 50%;
        width: 100px;
        height: 100px;
        animation: spin 2s linear infinite;
        margin: auto;
    }
    .fade-enter-active, .fade-leave-active {
      transition: opacity .2s
    }
    .fade-enter, .fade-leave-to {
      opacity: 0
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    [v-cloak] {
        display: none !important;
    }
    .no-border {
        border: none !important;
     }
     .vs__dropdown-toggle{
        height: calc(2.25rem + 2px);
     }

     .incard{
        box-shadow:none !important;
     }
    .form-group{
    }

     input[type=checkbox], input[type=radio]{
        width: 17px !important;
        height: 17px !important;
     }
     input[type="file"] {
        width: 150px; /* Ajusta el valor según lo que necesites */
        max-width: 100%; /* Para asegurarte de que no se salga del contenedor */
    }

    .custom-file-input {
        display: none;
    }

    .custom-file-label {
        display: inline-block;
        padding: 5px 10px;
        cursor: pointer;
        border: 1px solid #ccc;
        border-radius: 4px;
        background-color: #f7f7f7;
    }

    .custom-file-label:hover {
        background-color: #e7e7e7;
    }
    .grafica-wrapper {
        width: 100%;
        height: 400px; /* altura fija deseada */
        position: relative; /* necesario para que Chart.js escale bien */
    }
    .btn-error:hover{
        background-color: white !important;
    }

    .table .form-check label .form-check-sign::before, .table .form-check label .form-check-sign::after {top: -10px !important}
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
        <div class="col-lg-12 px-5" v-show="cargando">
            <div style="margin-top: 200px; max-width: 100% !important; margin-bottom: auto; text-align:center; letter-spacing: 2px">
                <h5 class="mb-5">CARGANDO...</h5>
                <div class="loader"></div>
            </div>
        </div>
        <div class="row mt-3 px-5" v-cloak v-show="!cargando">
            <div class="col-lg-6 ">
                <h2 class="bold my-0 py-1 " style="letter-spacing: 2px">TIEMPOS DE PERSONAL
                    <br>
                    <small style="font-size: 11px">PROGRAMADORES - OPERADORES - ALMACENISTAS - MATRICEROS</small>
                </h2>
            </div>
            <div class="col-lg-3 form-group">
                <label class="bold">Desde ->  Hasta</label>
                <input type="text" id="datepicker" class="form-control">
            </div>
            <div class="col-lg-3">
                <label class="bold">Filtrar por horario</label>   
                <select class="form-control" v-model="filtros.turno" @change="fetchTiempos()">
                    <option value="1">HORARIO REGULAR (8:00am - 5:30pm)</option>
                    <option value="2">HORAS EXTRA (despues de las 5:30pm)</option>
                </select>
            </div>
            <div class="col-lg-12">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="home-tab" data-toggle="tab" data-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">TIEMPOS DE PRODUCCIÓN</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-tab" data-toggle="tab" data-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">COMPONENTES ENSAMBLADOS</button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <div class="row mb-5">
                            <div class="col-lg-3 mt-3 text-center" v-for="personal in tiempos" :key="personal.id">
                                <h4 class="bold my-0 mb-2">@{{ personal.nombre }} </h4>
                                <span class="badge badge-pill badge-dark px-2 py-1 mx-1 mb-1" style="font-size: 9px" v-for="rol in personal.roles">@{{rol.nombre}}</span>
                                <canvas :id="'grafica_' + personal.id" width="200" height="200" class="grafica-canvas mt-3"></canvas>
                                <button @click="verTiemposRole(personal.roles)" class="btn-block btn btn-sm mb-0 mt-1 btn-default"><i class="fa fa-user-clock"></i> Tiempos de producción por role</button>
                                <button @click="verTiemposParo(personal.detalle_paros)" class="btn-block btn btn-sm mb-0 mt-1 btn-danger"><i class="fa fa-list"></i> Detalle de paros</button>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="row">
                            <div class="col-lg-12 text-center">
                                 <h4 class="bold">MATRICERO <br> 
                                    <small>COMPONENTES ENSAMBLADOS EN EL PERIODO: <strong> @{{totalComponentes}}</strong></small>
                                </h4>
                                 <canvas id="grafica_mat" height="100px"></canvas>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

         <div class="modal fade" id="modalTiemposRole" tabindex="-1" aria-labelledby="modalTiemposRoleLabel" aria-hidden="true">
            <div class="modal-dialog" style="min-width: 40%;">
                <div class="modal-content" >
                    <div class="modal-header">
                        <h3 class="modal-title" id="modalTiemposRoleLabel"><span>TIEMPOS DE PRODUCCIÓN POR ROLE </span></h3>
                        <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="text-transform: none !important" >Role</th>
                                            <th class="bg-success text-white" style="text-transform: none !important" >Tiempo activo</th>
                                            <th class="bg-danger text-white" style="text-transform: none !important" >Tiempo en paro</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="role in tiemposRoles" :key="role.id">
                                            <td class="bold">@{{ role.nombre }}</td>
                                            <td>@{{ formatearMinutos(role.minutos_activo) }}</td>
                                            <td>@{{ formatearMinutos(role.minutos_paro) }}</td>
                                        </tr>
                                    </tbody>

                                </table>
                            </div>           
                        </div>
                        <div class="row">
                            <div class="col-lg-12 text-right">
                                <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar </button>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
         <div class="modal fade" id="modalParos" tabindex="-1" aria-labelledby="modalParosLabel" aria-hidden="true">
            <div class="modal-dialog" style="min-width: 50%;">
                <div class="modal-content" >
                    <div class="modal-header">
                        <h3 class="modal-title" id="modalParosLabel"><span>DETALLE DE PAROS </span></h3>
                        <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="text-transform: none !important"> Fecha y Hora</th>
                                            <th style="text-transform: none !important"> Tipo de paro</th>
                                            <th style="text-transform: none !important"> Comentarios del paro</th>
                                            <th style="text-transform: none !important"> Maquina</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="paro in detalleParos" :key="paro.id">
                                            <td>@{{ paro.fecha_show }} @{{ paro.hora_show }}</td>
                                            <td>@{{ paro.tipo_paro }}</td>
                                            <td>@{{ paro.comentarios_paro }}</td>
                                            <td>@{{ paro.maquina??'NA' }}</td>
                                        </tr>
                                        <tr v-if="!detalleParos || detalleParos.length == 0">
                                            <td colspan="4">No hay paros registrados por este usuario.</td>
                                        </tr>
                                    </tbody>

                                </table>
                            </div>           
                        </div>
                        <div class="row">
                            <div class="col-lg-12 text-right">
                                <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar </button>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/litepicker/dist/nocss/litepicker.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css"/>

    <script type="text/javascript">
        Vue.component('v-select', VueSelect.VueSelect)
        var app = new Vue({
        el: '#vue-app',
        data: {
            loading_button: false,
            cargando: false,
            filtros:{
                desde: "",
                hasta: "",
                turno: 1,
            },
            tiempos: [],
            graficas: [],
            datosGrafica: [],
            totalComponentes: 0,
            tiemposRoles: [],
            detalleParos: [],
        },
        methods:{
             formatearMinutos(minutos) {
                const horas = Math.floor(minutos / 60);
                const mins = minutos % 60;
                return `${horas > 0 ? horas + ' hr ' : ''}${mins} min`;
            },
            verTiemposRole(tiempos) {
                this.tiemposRoles = tiempos;
                $('#modalTiemposRole').modal('show');
            },
            verTiemposParo(paros) {
                this.detalleParos = paros;
                $('#modalParos').modal('show');
            },
            async fetchTiempos() {
                this.cargando = true

                try {
                    const response = await axios.get(`/api/tiempos-personal`, {
                        params: {
                            desde: this.filtros.desde,
                            hasta: this.filtros.hasta,
                            turno: this.filtros.turno
                        }
                    });
                    this.tiempos = response.data.tiempos;
                    this.datosGrafica = response.data.periodo;
                    this.totalComponentes = response.data.totalComponentes;

                } catch (error) {
                    console.error('Error fetching tiempos:', error);
                } finally {
                    this.cargando = false;
                    this.graficas.forEach(g => g.destroy());

                    Vue.nextTick(() => {
                        this.tiempos.forEach((personal) => {
                            this.generarGrafica(personal);
                        });
                        this.renderGraficaMat();
                    });
                }
            },
            renderGraficaMat() {
                const labels = this.datosGrafica.map(item => item.fecha);
                const values = this.datosGrafica.map(item => item.total);

                const ctx = document.getElementById('grafica_mat').getContext('2d');

                // Destruir gráfica anterior si existe
                if (this.chart) {
                    this.chart.destroy();
                }

                this.chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                    labels: labels,
                    datasets: [{
                        label: 'Componentes ensamblados',
                        data: values,
                        backgroundColor: '#4e73df',
                        borderColor: '#4e73df',
                        borderWidth: 1
                    }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                },
                                title: {
                                    display: true,
                                    text: 'Componentes ensamblados'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                align: 'center',
                                text: 'PRODUCCIÓN DIARIA DE COMPONENTES ENSAMBLADOS'
                            },
                            legend: {
                                 display: false
                                },
                                tooltip: {
                                callbacks: {
                                    label: function(context) {
                                    return `${context.parsed.y} componentes`;
                                    }
                                }
                            }
                        }
                    }
                });
            },
            obtenerCosto(minutos, costoPorHora) {
                // Validación y conversión
                if (typeof minutos !== 'number' || minutos < 0) {
                    console.warn('Minutos inválidos');
                    return '$0.00';
                }

                // Limpiar el string de costo (por si viene con símbolos como "$" o espacios)
                if (typeof costoPorHora === 'string') {
                    costoPorHora = costoPorHora.replace(/[^0-9.,]/g, '').replace(',', '.'); // eliminar $ y convertir , a .
                }

                // Convertir a número flotante
                costoPorHora = parseFloat(costoPorHora);

                if (isNaN(costoPorHora) || costoPorHora < 0) {
                    console.warn('Costo por hora inválido');
                    return '$0.00';
                }

                // Cálculo
                const horas = Math.floor(minutos / 60);
                const minutosRestantes = minutos % 60;
                const costoTotal = (horas * costoPorHora) + ((minutosRestantes / 60) * costoPorHora);

                // Formato moneda
                return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(costoTotal);
            },
            generarGrafica(personal) {
                let datosActiva = personal.minutos_activo;
                let datosParo = personal.minutos_paro;
                let datosEsperados = Math.max(0, personal.minutos_totales - (datosActiva + datosParo));

                // Crear los labels con los costos
                let etiquetas = [
                    `Activo (${this.obtenerCosto(datosActiva, personal.costo_hora)})`,
                    `Paro (${this.obtenerCosto(datosParo, personal.costo_hora)})`,
                    `Inactivo (${this.obtenerCosto(datosEsperados, personal.costo_hora)})`
                ];

                
                let ctx = document.getElementById('grafica_' + personal.id).getContext('2d');

                let grafica = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: etiquetas,
                        datasets: [{
                            label: 'Tiempo personal',
                            data: [datosActiva, datosParo, datosEsperados],
                            backgroundColor: ['#81c784', '#e57373', '#ccd1d1'],
                            borderColor: ['#388e3c', '#c62828', '#808b96'], // Tonos más oscuros para bordes
                            borderWidth: 1,
                            hoverOffset: 10,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 20,
                                    padding: 15,
                                }
                            },
                            tooltip: {
                                backgroundColor: '#fff',
                                borderColor: '#ccc',
                                borderWidth: .5,
                                titleColor: '#333',
                                bodyColor: '#333',
                                callbacks: {
                                    label: function(tooltipItem) {
                                        let totalMinutes = tooltipItem.raw;
                                        let hours = Math.floor(totalMinutes / 60);
                                        let minutes = totalMinutes % 60;
                                        return ` ${hours} hrs. y ${minutes} min`;
                                    }
                                }
                            }
                        }
                    }
                });

                this.graficas.push(grafica);
            }
        },
        mounted: async function () {
            let t = this;
            const today = new Date();
            const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
            const lastDayOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            t.filtros.desde = firstDayOfMonth.toISOString().split('T')[0];
            t.filtros.hasta = lastDayOfMonth.toISOString().split('T')[0];

            const picker = new Litepicker({ 
                element: document.getElementById('datepicker'),
                singleMode: false,
                lang: 'es-MX',
                startDate: firstDayOfMonth,
                endDate: lastDayOfMonth,
                numberOfMonths: 1,
                tooltipText: {
                    one: 'dia',
                    other: 'dias'
                },
                format: "DD/MM/YYYY",
                setup: (picker) => {
                    picker.on('selected', (date1, date2) => {
                        t.filtros.desde = date1 ? date1.format("YYYY-MM-DD") : "";
                        t.filtros.hasta = date2 ? date2.format("YYYY-MM-DD") : "";
                        t.fetchTiempos();
                    });
                },
            });

            t.fetchTiempos();
        }

                
    })

    </script>



        
@endpush