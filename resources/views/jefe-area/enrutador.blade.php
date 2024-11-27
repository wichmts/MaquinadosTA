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
    
     #tabla-principal {
        table-layout: fixed;
        min-width: 1200px; /* Ajusta el ancho mínimo según el contenido */
    }

     #gantt_here {
        width: 100%;
        height: 600px; /* Ajusta este valor según tus necesidades */
    }

    .btn-dark{
        background-color: #333 !important;
        color: white !important;
        border-color: #333;
    }


    input[type="number"] {
        -moz-appearance: textfield; /* Firefox */
        -webkit-appearance: none;  /* Chrome, Safari, Edge */
        appearance: none;          /* Soporte estándar */
    }

    /* Opcional: Para evitar padding adicional en algunos navegadores */
    input[type="number"]::-webkit-inner-spin-button, 
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none; 
        margin: 0; /* Opcional: Ajustar márgenes */
    }


    .table .form-check label .form-check-sign::before, .table .form-check label .form-check-sign::after {top: -10px !important}

    .gantt-chart {
        display: grid;
        grid-template-rows: auto;
        font-family: Arial, sans-serif;
    }

    .gantt-header, .gantt-row {
        display: grid;
        grid-template-columns: 150px repeat(var(--columns, 200), 1fr); /* var(--columns) es una variable CSS */
        height: 25px;
    }

    .gantt-cell {
        border: .5px solid #ddd;
        padding: 0px;
        text-align: center;
        font-size: 12px;
        width: 40px;
    }

    .task-name {
        background-color: #f0f0f0;
        text-align: center;
        font-weight: bold;
        width: 150px;
    }

    .gantt-bar {
        position: relative;
    }

    .normal-task {
        position: absolute;
        height: 100%;
        background-color: #4caf50;
        border-radius: 0px;
    }

    .rework-task {
        position: absolute;
        height: 100%;
        background-color: #f44336;
        border-radius: 0px;
    }
    .general-header {
        display: grid;
        grid-template-columns: 150px repeat(var(--columns, 200), 1fr); /* var(--columns) es una variable CSS */
        background-color: #f0f0f0;
        font-weight: bold;
        text-align: center;
    }

    .time-header {
        grid-column: span var(--columns, 200); /* Cambia este número si tienes más o menos horas */
        font-size: 14px;
        padding: 8px 0;
    }


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
        <div class="row" v-cloak v-show="!cargando">
            <div class="col-xl-2 pt-3" style="background-color: #f1f1f1; height: calc(100vh - 107.3px)">
                <div class="nav flex-column nav-pills " id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link cursor-pointer text-right text-muted" >
                        <i v-if="menuStep > 1"  @click="regresar(menuStep - 1)" class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/regresar.png') }}"></i>
                    </a>
                    <div v-if="!cargandoMenu && menuStep == 1">
                        <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> AÑOS </a>
                        <a class="nav-link cursor-pointer" v-for="obj in anios" @click="fetchClientes(obj.id)">
                            <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/calendario.png') }}"></i> &nbsp;
                            <span class="underline-hover">@{{obj.nombre}}</span> 
                        </a>
                    </div>    
                    <div v-if="!cargandoMenu && menuStep == 2">
                        <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> CARPETAS </a>
                        <a class="nav-link cursor-pointer" v-for="obj in clientes" @click="fetchProyectos(obj.id)">
                            <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/carpetas.png') }}"></i> &nbsp;
                            <span class="underline-hover">@{{obj.nombre}}</span> 
                        </a>
                    </div>
                    <div v-if="!cargandoMenu && menuStep == 3">
                        <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> PROYECTOS </a>
                        <a class="nav-link cursor-pointer" v-for="obj in proyectos" @click="fetchHerramentales(obj.id)">
                            <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/carpetas.png') }}"></i> &nbsp;
                            <span class="underline-hover">@{{obj.nombre}}</span> 
                        </a>
                    </div>
                    <div v-if="!cargandoMenu && menuStep == 4">
                        <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> HERRAMENTALES </a>
                        <a class="nav-link cursor-pointer" v-for="obj in herramentales" @click="fetchComponentes(obj.id)" >
                            <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/componente.png') }}"></i> &nbsp;
                            <span class="underline-hover">@{{obj.nombre}}</span> 
                        </a>
                    </div>
                    <div v-if="!cargandoMenu && menuStep == 5">
                        <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> COMPONENTES </a>
                        <a class="nav-link cursor-pointer" v-for="obj in componentes" @click="fetchComponente(obj.id)">
                            <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/componentes.png') }}"></i> &nbsp;
                            <span class="underline-hover">@{{obj.nombre}}</span> 
                        </a>
                    </div>
                    
                </div>            
            </div>
            <div class="col-xl-10 mt-3">
                <div class="row">
                    <div class="mb-2 col-xl-12" style="border-bottom: 1px solid #ededed">
                        <p style="">
                            <span class="cursor-pointer pb-2" @click="regresar(1)"><i class="fa fa-home"></i> &nbsp;</span>
                            <span class="cursor-pointer pb-2"  v-if="ruta.anio" @click="regresar(2)"><i class="fa fa-angle-right"></i>   &nbsp; <span class="underline-hover">@{{ruta.anio}}</span>    &nbsp;</span>
                            <span class="cursor-pointer pb-2"  v-if="ruta.cliente" @click="regresar(3)"><i class="fa fa-angle-right"></i>   &nbsp; <span class="underline-hover">@{{ruta.cliente}}</span>     &nbsp;</span>
                            <span class="cursor-pointer pb-2"  v-if="ruta.proyecto" @click="regresar(4)"><i class="fa fa-angle-right"></i>   &nbsp; <span class="underline-hover">@{{ruta.proyecto}}</span>     &nbsp;</span>
                            <span class="cursor-pointer pb-2"  v-if="ruta.herramental" @click="regresar(5)"><i class="fa fa-angle-right"></i>   &nbsp; <span class="underline-hover">@{{ruta.herramental}}</span>      </span>
                            <span class="cursor-pointer pb-2 bold"  v-if="ruta.componente"><i class="fa fa-angle-right"></i>   &nbsp; <span class="underline-hover">@{{ruta.componente}}</span>      </span>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-10">
                        <h2 class="bold my-0 py-1 mb-3 text-decoration-underline" style="letter-spacing: 2px">ENRUTADOR</h2>
                    </div>
                    <div class="col-xl-2"  v-if="selectedHerramental" style="border-left: 1px solid  #ededed">
                        <button class="btn btn-block mt-0" @click="guardarComponentes"><i class="fa fa-save"></i>    GUARDAR</button>
                    </div>
                </div>
                <div class="col-xl-12" v-if="!selectedComponente">
                    <h5 class="text-muted my-4"> SELECCIONE UN COMPONENTE PARA VER SU ENRUTAMIENTO</h5>
                </div>
                <div class="row" v-else>
                    <div class="col-xl-8">
                        <div class="row">
                            <div class="col-xl-6">
                                <span style="font-size: 23px !important; border-color: #c0d340 !important; background-color: #c0d340 !important" class="badge badge-warning badge-pill bold my-4 "> <i class="fa fa-cogs" style="font-size: 20px !important" ></i> @{{componente.nombre}}</span>
                            </div>
                            <div class="col-xl-2">
                                <a class="text-dark" :href="'/storage/' + componente.archivo_2d_public" target="_blank">
                                    <h3 class="my-0 py-0 bold">2D</h3>
                                </a>
                                <div class="preview">
                                    <iframe style="border: none; border-radius: 5px" :src="'/storage/' + componente.archivo_2d_public" width="90%" height="50px"></iframe>
                                </div>
                            </div>
                            <div class="col-xl-2">
                                <a class="text-dark" :href="'/storage/' + componente.archivo_3d_public" target="_blank">
                                    <h3 class="my-0 py-0 bold">3D</h3>
                                </a>
                                <div class="preview">
                                    <iframe style="border: none; border-radius: 5px" :src="'/storage/' + componente.archivo_3d_public" width="90%" height="50px"></iframe>
                                </div>
                            </div>
                            <div class="col-xl-2">
                                <a class="text-dark" :href="'/storage/' + componente.archivo_explosionado_public" target="_blank">
                                    <h3 class="my-0 py-0 bold">EXPL.</h3>
                                </a>
                                <div class="preview">
                                    <iframe style="border: none; border-radius: 5px" :src="'/storage/' + componente.archivo_explosionado_public" width="90%" height="50px"></iframe>
                                </div>
                            </div>
                            <div class="col-xl-3 form-group">
                                <label class="bold">Cantidad</label>
                                <input type="number" step="any" class="form-control text-center" readonly :value="componente.cantidad">
                            </div>
                            <div class="col-xl-4 form-group">
                                <label class="bold">Asignar prioridad</label>
                                <select class="form-control" v-model="componente.prioridad">
                                    <option :value="null" disabled>Seleccionar prioridad...</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                </select>
                            </div>
                            <div class="col-xl-5 form-group">
                                <label class="bold">Programador</label>
                                <select class="form-control" v-model="componente.programdor">
                                    <option :value="null" disabled>Seleccionar programador...</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-xl-12">
                            <div class="row">
                                <div class="col-xl-12 px-0" style="overflow-x:scroll">
                                <div class="gantt-chart" :style="{ '--columns': duracionTotal.length }" >
                                        <div class="gantt-header general-header">
                                            <div class=" time-header" :colspan="duracionTotal.length">Tiempo teórico en horas</div>
                                        </div>
                                        <div class="gantt-header">
                                            <div class="gantt-cell task-name pt-1">ACCIONES</div>
                                            <div class="gantt-cell pt-1" v-for="hour in duracionTotal" :key="hour">
                                                @{{ hour }}
                                            </div>
                                        </div>
                                        <div class="gantt-row" v-for="task in tasks" :key="task.id" >
                                            <div class="gantt-cell task-name pt-1">@{{ task.name }}</div>
                                            <div class="gantt-cell gantt-bar" v-for="hour in duracionTotal" :key="hour">
                                                <div
                                                v-for="segment in task.time"
                                                :key="segment.inicio"
                                                v-if="isTaskInHour(segment, hour)"
                                                :class="segment.type === 'normal' ? 'normal-task' : 'rework-task'"
                                                :style="getTaskStyle(segment, hour)"
                                                ></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="row">
                            <div class="col-xl-12">
                                <table class="table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Incluir</th> 
                                            <th>Accion</th> 
                                            <th>Horas</th> 
                                            <th>Minutos</th> 
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="p in procesos" :key="p.id">
                                            <td class="py-1">
                                                 <div class="form-group">
                                                    <div class="form-check">
                                                    <label class="form-check-label" style="font-size: 10px">
                                                        <input type="checkbox" class="form-check-input" @change="toggleTask(p)">
                                                        <span class="form-check-sign"></span>
                                                    </label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-1">@{{p.nombre}}</td>
                                            <td class="py-1">
                                                <div class="row">
                                                    <div class="col-xl-12">
                                                        <div class="input-group mb-0">
                                                             <div class="input-group-prepend">
                                                                 <span class="input-group-text py-0 cursor-pointer" style="background-color: #e3e3e3 !important"  @click="p.horas > 0 ? p.horas-- : p.horas"> <i class="fa fa-minus"></i> &nbsp;&nbsp;</span>
                                                             </div>
                                                             <input type="number" v-model="p.horas" class="form-control text-center px-1 py-1" step="1" @change="calcularInicio()">
                                                             <div class="input-group-append">
                                                                 <span class="input-group-text py-0 cursor-pointer" style="background-color: #e3e3e3 !important" @click="p.horas++"> &nbsp;&nbsp;<i class="fa fa-plus"></i> </span>
                                                             </div>
                                                         </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-1">
                                                <div class="row">
                                                    <div class="col-xl-12">
                                                        <div class="input-group mb-0">
                                                             <div class="input-group-prepend">
                                                                 <span class="input-group-text py-0 cursor-pointer" style="background-color: #e3e3e3 !important"  @click="p.minutos > 0 ? p.minutos-- : p.minutos"> <i class="fa fa-minus"></i> &nbsp;&nbsp;</span>
                                                             </div>
                                                             <input type="number" v-model="p.minutos" class="form-control text-center px-1 py-1" step="1" @change="calcularInicio()">
                                                             <div class="input-group-append">
                                                                 <span class="input-group-text py-0 cursor-pointer" style="background-color: #e3e3e3 !important" @click="p.minutos < 60 ? p.minutos++ : p.minutos "> &nbsp;&nbsp;<i class="fa fa-plus"></i> </span>
                                                             </div>
                                                         </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-6 py-0">
                                <button class="btn btn-block btn-dark my-1" @click="agregarRetrabajo(1)">RE TRABAJO</button>
                            </div>
                            <div class="col-xl-6 py-0">
                                <button class="btn btn-block btn-dark my-1" @click="agregarRetrabajo(2)" >MODIFICACIÓN</button>
                            </div>
                            <div class="col-xl-6 py-0">
                                <button class="btn btn-block btn-dark my-1">RE FABRICACIÓN</button>
                            </div>
                            <div class="col-xl-6 py-0">
                                <button class="btn btn-block btn-dark my-1">REFACCIÓN</button>
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
            componente: {},
            estatusCompra: -1,
            loading_button: false,
            cargando: false,            
            //MENU IZQUIERDO 
            anios: [],         
            clientes: [],      
            proyectos: [],     
            herramentales: [], 
            componentes: [],   
            cargandoMenu: true,
            menuStep: 1, 
            selectedAnio: null,
            selectedCliente: null,
            selectedProyecto: null,
            selectedHerramental: null,
            selectedComponente: null,
            ruta:{
                anio: null,
                cliente: null,
                proyecto: null,
                herramental: null,
                componente: null,
            },
            procesos: [
                {id: 1, prioridad: 1, nombre: 'Cortar', horas: 0, minutos: 0},
                {id: 2, prioridad: 2, nombre: 'Programar', horas: 0, minutos: 0},
                {id: 3, prioridad: 3, nombre: 'Maquinar', horas: 0, minutos: 0},
                {id: 4, prioridad: 4, nombre: 'Tornear', horas: 0, minutos: 0},
                {id: 5, prioridad: 5, nombre: 'Roscar', horas: 0, minutos: 0},
                {id: 6, prioridad: 6, nombre: 'Templar', horas: 0, minutos: 0},
                {id: 7, prioridad: 7, nombre: 'Rectificar', horas: 0, minutos: 0},
                {id: 8, prioridad: 8, nombre: 'EDM', horas: 0, minutos: 0}
           ],
            tasks: []
        },
        watch: {
            procesos: {
                handler() {
                    this.procesos.forEach(proceso => {
                        if (proceso.horas < 0) {
                            proceso.horas = 0;
                        }
                        
                        if (proceso.minutos < 0) {
                            proceso.minutos = 0;
                        } else if (proceso.minutos >= 60) {
                            proceso.minutos = 59;
                        }
                    });
                    this.calcularInicio(); 
                },
                deep: true
            }
        },
        computed: {
            duracionTotal() {
                let maxHour = 0;
                this.tasks.forEach(task => {
                    task.time.forEach(segment => {
                    const endHour = segment.hora_inicio + segment.horas + segment.minutos / 60;
                    if (endHour > maxHour) maxHour = Math.ceil(endHour);
                    });
                });
                return maxHour;
            }
        },
        methods:{
             toggleTask(proceso) {
                const index = this.tasks.findIndex(task => task.id === proceso.id);

                if (index === -1) {
                    this.tasks.push({
                        id: proceso.id,
                        name: proceso.nombre,
                        time: [
                            {
                                hora_inicio: null,
                                minuto_inicio: null,
                                horas: proceso.horas,
                                minutos: proceso.minutos,
                                type: "normal"
                            }
                        ]
                    });
                } else {
                    this.tasks.splice(index, 1);
                }
                Vue.nextTick(function(){

                })

                this.calcularInicio();
            },
            agregarRetrabajo(taskId) {
                const task = this.tasks.find(task => task.id === taskId);
                if (task) {
                    task.time.push({
                        hora_inicio: null,
                        minuto_inicio: null,
                        horas: 1,
                        minutos: 0,
                        type: "rework"
                    });
                }
                this.calcularInicio();
            },
            calcularInicio() {
                let t = this
                let acumuladorHoras = 1;
                let acumuladorMinutos = 0;

                this.tasks.sort((a, b) => {
                    const prioridadA = this.procesos.find(p => p.id === a.id).prioridad;
                    const prioridadB = this.procesos.find(p => p.id === b.id).prioridad;
                    return prioridadA - prioridadB;
                });

                this.tasks.forEach(task => {
                    let proceso = t.procesos.find(p => p.id === task.id);                    
                    task.time.forEach((segmento, index) => {
                        if (index === 0) {
                            segmento.hora_inicio = acumuladorHoras;
                            segmento.minuto_inicio = acumuladorMinutos;
                            
                            segmento.horas = parseInt(proceso.horas);
                            segmento.minutos = parseInt(proceso.minutos);

                        } else {
                            if (acumuladorMinutos >= 60) {
                                acumuladorHoras += Math.floor(acumuladorMinutos / 60);
                                acumuladorMinutos = acumuladorMinutos % 60;
                            }
                            segmento.hora_inicio = acumuladorHoras;
                            segmento.minuto_inicio = acumuladorMinutos;
                        }
                        
                        acumuladorHoras += segmento.horas;
                        acumuladorMinutos += segmento.minutos;
                        
                        if (acumuladorMinutos >= 60) {
                            acumuladorHoras += Math.floor(acumuladorMinutos / 60);
                            acumuladorMinutos = acumuladorMinutos % 60;
                        }
                        console.log('HORAS: ',acumuladorHoras);
                    });
                });
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
            regresar(step){
                switch (step) {
                    case 1:
                        this.ruta = {
                            anio: null,
                            cliente: null,
                            proyecto: null,
                            herramental: null,
                        } 
                        this.selectedAnio = null;
                        this.selectedCliente = null;
                        this.selectedProyecto = null;
                        this.selectedHerramental = null;
                    break;
                    case 2:
                        this.ruta.cliente = null;
                        this.ruta.proyecto = null;
                        this.ruta.herramental = null;

                        this.selectedCliente = null;
                        this.selectedProyecto = null;
                        this.selectedHerramental = null;
                    break;
                    case 3:
                        this.ruta.proyecto = null;
                        this.ruta.herramental = null;

                        this.selectedProyecto = null;
                        this.selectedHerramental = null;
                    break;
                    case 4:
                        this.ruta.herramental = null;
                        this.selectedHerramental = null;
                    break;
                    case 5:
                        this.ruta.componente = null;
                        this.selectedComponente = null;
                    break;
                }
                this.menuStep = step;
            },
            async fetchAnios() {
                this.cargandoMenu = true
                axios.get('/api/anios')
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
            async fetchComponentes(herramentalId) {
                this.cargando = true
                this.selectedHerramental = herramentalId;
                this.ruta.herramental = this.herramentales.find(obj => obj.id == herramentalId)?.nombre;

                try {
                    const response = await axios.get(`/api/herramentales/${herramentalId}/componentes?area=corte`);
                    this.componentes = response.data.componentes;
                    this.menuStep = 5;
                } catch (error) {
                    console.error('Error fetching componentes:', error);
                } finally {
                    this.cargando = false;
                }
            },
            async fetchComponente(componenteId) {
                let t = this;
                t.selectedComponente = componenteId;
                t.ruta.componente = t.componentes.find(obj => obj.id == componenteId)?.nombre;
                t.componente = t.componentes.find(obj => obj.id == componenteId);

                Vue.nextTick(function () {

                });
            },
            async guardarComponentes(mostrarAlerta = true){
                let t = this
                t.cargando = true;
                let formData = new FormData();
                formData.append('data', JSON.stringify(t.componentes));
                try {
                    const response = await axios.post(`/api/componente/${t.selectedHerramental}/compras`, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });
                    if(mostrarAlerta){
                        swal('Correcto', 'Informacion guardada correctamente', 'success');
                        t.fetchComponentes(t.selectedHerramental);
                    }else{
                        return true;
                    }
                } catch (error) {
                    t.cargando = false
                    return false;
                }  
            },
            async liberarHerramental() {
                let t = this;

                let errores = [];
                t.componentes.forEach((componente, index) => {  
                    if (!componente.fecha_solicitud || !componente.fecha_pedido || !componente.fecha_estimada || !componente.fecha_real ) {
                        errores.push(`Todos los campos son obligatorios para liberar en ${componente.nombre}.`);
                    }
                });

                if (errores.length > 0) {
                    swal('Errores de validación', errores.join('\n'), 'error');
                    return;
                }

                
                t.cargando = true;
                let respuesta = await t.guardarComponentes(false);
                if(respuesta){
                    try {
                        const response = await axios.put(`/api/liberar-herramental-compras/${t.selectedHerramental}`);
                        t.cargando = false;
                        swal('Éxito', 'Componentes liberados correctamente', 'success');
                        t.fetchComponentes(t.selectedHerramental);

                    } catch (error) {
                        t.cargando = false;
                        console.error('Error al liberar el componente:', error);
                        swal('Error', 'Ocurrió un error al liberar el herramental', 'error');
                    }
                }else{
                    swal('Error', 'Ocurrió un error al guardar la informacion de los componentes', 'error');
                    t.cargando = false;
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
                    if (componenteId) {
                        await this.fetchComponente(componenteId);
                    }
                } catch (error) {
                    console.error("Error navigating from URL parameters:", error);
                }
            },
            
        },
        mounted: async function () {
            let t = this;
            await t.fetchAnios();
            this.navigateFromUrlParams();        
        }

                
    })

    </script>



        
@endpush