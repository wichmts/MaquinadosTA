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

    .checkbox-wrapper-18 {
        display: flex;               /* Usa Flexbox para centrar */
        justify-content: center;     /* Centra horizontalmente */
        align-items: center;         /* Centra verticalmente si es necesario */
        height: 100%;                /* Asegúrate de que el contenedor tenga altura */
    }

    .checkbox-wrapper-18 .round {
        position: relative;
    }

    .checkbox-wrapper-18 .round label {
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 50%;
        cursor: pointer;
        height: 28px;
        width: 28px;
        display: block;
    }

    .checkbox-wrapper-18 .round label:after {
        border: 2px solid #fff;
        border-top: none;
        border-right: none;
        content: "";
        height: 6px;
        left: 8px;
        opacity: 0;
        position: absolute;
        top: 9px;
        transform: rotate(-45deg);
        width: 12px;
    }

    .checkbox-wrapper-18 .round input[type="checkbox"] {
        visibility: hidden;
        display: none;
        opacity: 0;
    }

    .checkbox-wrapper-18 .round input[type="checkbox"]:checked + label {
        background-color: #66bb6a;
        border-color: #66bb6a;
    }

    .checkbox-wrapper-18 .round input[type="checkbox"]:checked + label:after {
        opacity: 1;
    }


    .gantt-chart {
        display: grid;
        grid-template-rows: auto;
        font-family: Arial, sans-serif;
    }


    .gantt-header, .gantt-row {
        display: grid;
        grid-template-columns: 150px repeat(var(--columns, 200), 1fr); /* var(--columns) es una variable CSS */
        height: 40px;
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
        font-size: 15px;
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

     .delay-task {
        position: absolute;
        height: 100%;
        background-color: #ff9430;
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

    .limite-tiempo {
        position: absolute;
        top: 30px;
        bottom: 0;
        left: 50%; /* O la posición que desees */
        width: 0;  /* El width se debe poner a 0, ya que la línea será con borde */
        border-left: 3px dotted orange; /* Agregar borde punteado */
        z-index: 10;
    }

    .tooltip {
        max-width: none; /* Elimina el límite de ancho predeterminado */
        width: 400px; /* Asegúrate de que el tooltip se ajuste al contenido */
    }
     .tipoParoSeleccionado {
        background-color: #d34040 !important;
        color: white !important;
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
        <div class="col-xl-12" v-show="cargando">
            <div style="margin-top: 200px; max-width: 100% !important; margin-bottom: auto; text-align:center; letter-spacing: 2px">
                <h5 class="mb-5">CARGANDO...</h5>
                <div class="loader"></div>
            </div>
        </div>
        <div class="row" v-cloak v-show="!cargando">
            <div class="col-xl-2 pt-3" style="background-color: #f1f1f1; height: calc(100vh - 107.3px); overflow-y: scroll">
                <div class="nav flex-column nav-pills " id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link cursor-pointer text-right text-muted" >
                        <i v-if="menuStep > 1"  @click="regresar(menuStep - 1)" class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/regresar.png') }}"></i>
                    </a>
                    <div v-if="!cargandoMenu && menuStep == 1">
                        <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> AÑOS </a>
                        <a class="nav-link cursor-pointer" v-for="obj in anios" @click="fetchClientes(obj.id)">
                            <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/calendario.png') }}"></i> &nbsp;
                            <span class="underline-hover">@{{obj.nombre}}</span>  {{--<i class="fa fa-caret-right"></i>    --}}
                        </a>
                    </div>    
                    <div v-if="!cargandoMenu && menuStep == 2">
                        <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> CARPETAS </a>
                        <a class="nav-link cursor-pointer" v-for="obj in clientes" @click="fetchProyectos(obj.id)">
                            <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/carpetas.png') }}"></i> &nbsp;
                            <span class="underline-hover">@{{obj.nombre}}</span>  {{--<i class="fa fa-caret-right"></i>    --}}
                        </a>
                    </div>
                    <div v-if="!cargandoMenu && menuStep == 3">
                        <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> PROYECTOS </a>
                        <a class="nav-link cursor-pointer" v-for="obj in proyectos" @click="fetchHerramentales(obj.id)">
                            <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/carpetas.png') }}"></i> &nbsp;
                            <span class="underline-hover">@{{obj.nombre}}</span>  {{--<i class="fa fa-caret-right"></i>    --}}
                        </a>
                    </div>
                    <div v-if="!cargandoMenu && menuStep == 4">
                        <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> HERRAMENTALES </a>
                        <a class="nav-link cursor-pointer" v-for="obj in herramentales" @click="fetchComponentes(obj.id)" >
                            <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/componente.png') }}"></i> &nbsp;
                            <span class="underline-hover">@{{obj.nombre}}</span>  {{--<i class="fa fa-caret-right"></i>    --}}
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
                            <span class="cursor-pointer pb-2"  v-if="ruta.herramental"><i class="fa fa-angle-right"></i>   &nbsp; <span class="underline-hover">@{{ruta.herramental}}</span>      </span>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-12">
                        <h2 class="bold my-0 py-1 mb-3 text-decoration-underline" style="letter-spacing: 2px">VISOR DE AVANCE DE HR</h2>
                    </div>
                </div>
                <div class="col-xl-12" v-if="!selectedHerramental">
                    <h5 class="text-muted my-4"> SELECCIONE UN HERRAMENTAL PARA VER SU AVANCE</h5>
                </div>
                <div class="row" v-else>
                    <div class="col-xl-12 " v-if="tasks.length == 0">
                        <h5 class="text-muted">Este herramental aun no tiene componentes cargados...</h5>
                    </div>
                    <div class="col-xl-12" v-else>
                        <div class="gantt-chart" :style="{ '--columns': duracionTotal.length }" >
                            <div class="gantt-header general-header">
                                <div class=" time-header pb-2" :colspan="duracionTotal.length" style="letter-spacing: 1px" >TIEMPO (HORAS)</div>
                            </div>
                            <div class="gantt-header">
                                <div class="gantt-cell task-name pt-1">ACCIONES</div>
                                <div class="gantt-cell pt-1" v-for="hour in duracionTotal" :key="hour">@{{ hour }}</div>
                            </div>
                            <div class="gantt-row" v-for="task in tasks" :key="task.id" >
                                <div class="gantt-cell task-name pt-1">@{{ task.componente }}</div>
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
@endsection

@push('scripts')

    <script type="text/javascript">
        Vue.component('v-select', VueSelect.VueSelect)

        var app = new Vue({
        el: '#vue-app',
        data: {
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
            movimiento: {},
            ruta:{
                anio: null,
                cliente: null,
                proyecto: null,
                herramental: null,
            },
            tasks: [],
            procesos: [],
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
            },
            totalHoras() {
                let totalHoras = 0;
                let totalMinutos = 0;
                let maxTime = { horas: 0, minutos: 0 };

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
                let maxTime = { horas: 0, minutos: 0 };

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
            }
        },
        methods:{
            
            ajustarRutaAvance(tasks, rutaAvance) {
                let convertirAMinutos = (horas, minutos) => horas * 60 + minutos;

                let convertirAHorasYMinutos = (minutosTotales) => {
                    let horas = Math.floor(minutosTotales / 60);
                    let minutos = minutosTotales % 60;
                    return { horas, minutos };
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
                        let { horas: horasDelay, minutos: minutosDelay } = convertirAHorasYMinutos(excesoMinutos);
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
                        let { horas, minutos } = convertirAHorasYMinutos(tiempoActualEnMinutos);
                        segmento.hora_inicio = horas;
                        segmento.minuto_inicio = minutos;

                        // Avanzar el tiempo actual según la duración del segmento
                        tiempoActualEnMinutos += convertirAMinutos(segmento.horas, segmento.minutos);
                    });
                    }
                }); 
                
               return rutaAvance; // Devuelve la estructura modificada
            },
            
            getContenidoTooltip(task) {
                    let totalHoras = 0;
                    let totalMinutos = 0;

                    let normalTime = { horas: 0, minutos: 0 };
                    let reworkTime = { horas: 0, minutos: 0 };
                    let delayTime = { horas: 0, minutos: 0 };

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
            async cargarRuta(){

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
                    const response = await axios.get(`/api/avance-hr/${herramentalId}`);
                    this.tasks = response.data.tasks;
                    console.log(this.tasks);
                } catch (error) {
                    console.error('Error fetching tasks:', error);
                } finally {
                    this.cargando = false;
                }
            },
            async fetchMateriales() {
                this.cargando = true
                try {
                    const response = await axios.get(`/api/materiales`);
                    this.materiales = response.data.materiales;
                } catch (error) {
                    console.error('Error fetching materiales:', error);
                } finally {
                    this.cargando = false;
                    this.materialSelected = this.materiales[0]?.id??null;
                    this.fetchHojas();
                }
            },
            async fetchHojas(material_id) {
                this.cargando = true
                try {
                    const response = await axios.get(`/api/hojas/${material_id}`);
                    this.hojas = response.data.hojas;
                } catch (error) {
                    console.error('Error fetching hojas:', error);
                } finally {
                    this.cargando = false;
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
                        await this.fetchComponentes(herramentalId);
                    }
                } catch (error) {
                    console.error("Error navigating from URL parameters:", error);
                }
            },
            // async fetchComponente(id){
            //     this.cargando = true;
            //     try {
            //         const response = await axios.get(`/api/componente/${id}`)
            //         this.componente = response.data.componente

            //     } catch (error) {
            //         console.error('Error fetching componente:', error);
            //     } finally {
            //         this.cargando = false;
            //     }
            // }

        },
        mounted: async function () {
            let t = this;
            await t.fetchAnios();
            await t.fetchMateriales();
            this.navigateFromUrlParams();
        }

                
    })

    </script>



        
@endpush