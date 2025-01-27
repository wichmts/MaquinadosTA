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
        width: 80px;
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
                    <div class="col-xl-12" v-else style="overflow-x:scroll">
                       <div class="gantt-chart" >
                            <div class="gantt-header general-header">
                                <div class="time-header pb-2" >TIEMPO (DIAS)</div>
                            </div>
                            <div class="gantt-header">
                                <div class="gantt-cell task-name pt-2">ACCIONES</div>
                                <div class="gantt-cell pt-2" v-for="day in duracionTotal" :key="day">
                                    <span class="bold">@{{ day }}</span>
                                </div>
                            </div>
                            <div class="gantt-row cursor-pointer" v-for="task in tasks" :key="task.id" @click="verInformacion(task)">
                                <div class="gantt-cell task-name pt-2">@{{ task.componente }}</div>
                                <div class="gantt-cell gantt-bar" v-for="day in duracionTotal" :key="day" >
                                    <div
                                        v-for="segment in task.time"
                                        class=""
                                        :key="segment.dia_inicio"
                                        :class="segment.type === 'normal' ? 'normal-task' : segment.type === 'rework' ? 'rework-task' : 'delay-task'"
                                        :style="getTaskStyle(segment, day)">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="modalNuevo" tabindex="-1" aria-labelledby="modalNuevoLabel" aria-hidden="true">
            <div class="modal-dialog" style="min-width: 50%;">
                <div class="modal-content" >
                    <div class="modal-header">
                        <h3 class="modal-title" id="modalNuevoLabel">
                            <span class="bold">INFORMACIÓN DE @{{infoComponentes.componente}}</span>
                        </h3>
                        <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                   <div class="modal-body">
                        <div class="col-xl-12 accordion" id="accordionComponentes" v-if="infoComponentes.time && infoComponentes.time.length > 0">
                            <div class="card" v-for="(componente, index) in infoComponentes.time.slice().reverse()" :key="componente.version">
                                <!-- Cabecera del acordeón -->
                                <div class="card-header cursor-pointer" :id="'heading-' + componente.version" :class="{'bg-success text-white': componente.type == 'normal', 'bg-danger text-white': componente.type == 'rework'}" data-toggle="collapse"  :data-target="'#collapse-' + componente.version"  :aria-expanded="index === 0"  :aria-controls="'collapse-' + componente.version">
                                    <h5 class="bold"> Version @{{ componente.version }} &nbsp;&nbsp;  <small>(@{{componente.dia_inicio}} Hrs. - @{{componente.dia_termino}} Hrs.)</small></h5>
                                </div>

                                <!-- Contenido colapsable -->
                                <div :id="'collapse-' + componente.version" class="collapse" :class="{ show: index === 0 }" :aria-labelledby="'heading-' + componente.version" data-parent="#accordionComponentes">
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <h5 class="bold">INFORMACION GENERAL DEL COMPONENTE: </h5>
                                            <span><strong>Tipo de componente: </strong> @{{componente.info.es_compra ? 'COMPRA' : 'FABRICACION'}}</span><br>
                                            <span><strong>Cantidad: </strong> @{{componente.info.cantidad}}</span>&nbsp;&nbsp;
                                            <span><strong>Alto: </strong> @{{componente.info.alto}}</span>&nbsp;&nbsp;
                                            <span><strong>Largo: </strong> @{{componente.info.largo}}</span>&nbsp;&nbsp;
                                            <span><strong>Ancho: </strong> @{{componente.info.ancho}}</span>&nbsp;&nbsp;
                                            <span><strong>Material: </strong> @{{componente.info.material_nombre}}</span>&nbsp;&nbsp;
                                        </div>
                                        <div class="mb-2" v-if="!componente.info.es_compra">
                                            <span><strong>Fecha de Carga:</strong> @{{ componente.info.fecha_cargado }}</span> <br>
                                            <span><strong>Fecha Términado:</strong> @{{ componente.info.fecha_terminado ?? ' Sin finalizar' }}</span> <br>
                                            <span><strong>Fecha Ensamblado:</strong> @{{ componente.info.fecha_ensamblado ?? 'Sin ensamblar' }}</span> <br>
                                            <span><strong>Ultimo estatus:</strong> </span> <span class="badge badge-dark badge-pill px-2 py-2 my-2">@{{determinarEstatus(componente.info)}}</span> <br>
                                        </div>
                                        <div v-else>
                                            <span><strong>Fecha solicitud:</strong> @{{ componente.info.fecha_solicitud }}</span> <br>
                                            <span><strong>Fecha pedido:</strong> @{{ componente.info.fecha_pedido ?? '-' }}</span> <br>
                                            <span><strong>Fecha estimada:</strong> @{{ componente.info.fecha_estimada ?? '-' }}</span> <br>
                                            <span><strong>Fecha compra real:</strong> @{{ componente.info.fecha_real ?? '-' }}</span> <br>
                                        </div>

                                        <div class="mb-2">
                                            <a class="btn btn-sm btn-default" :href="'/download/' + componente.info.archivo_2d_public">
                                                <i class="fa fa-download"></i> Descargar Vista 2D
                                            </a>
                                            <a class="btn btn-sm btn-default" :href="'/download/' + componente.info.archivo_3d_public">
                                                <i class="fa fa-download"></i> Descargar Vista 3D
                                            </a>
                                            <a class="btn btn-sm btn-default" :href="'/download/' + componente.info.archivo_explosionado_public">
                                                <i class="fa fa-download"></i> Descargar Vista Explosionado
                                            </a>
                                        </div>

                                        <!-- Botones de acciones -->
                                        <div class="text-center">
                                            <button v-if="!componente.info.es_compra" class="btn btn-dark mx-2"><i class="fa fa-list"></i> Ver solicitudes</button>
                                            <button v-if="!componente.info.es_compra" class="btn btn-dark mx-2"><i class="fa fa-calendar"></i> Ver linea de tiempo</button>
                                            <button v-if="!componente.info.es_compra" class="btn btn-dark mx-2"><i class="fa fa-camera"></i> Ver fotos</button>
                                            <button v-if="!componente.info.es_compra" class="btn btn-dark mx-2"><i class="fa fa-eye"></i> Visor componente</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-12 text-right">
                                <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
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
            componente: {},
            infoComponentes: [],
            seleccionado: null,
        },
        mounted: async function () {
            let t = this;
            await t.fetchAnios();
            await t.fetchMateriales();
            this.navigateFromUrlParams();
        },
        computed: {
            duracionTotal() {
                // Extraemos las fechas de inicio y término de las tareas
                const fechasInicio = this.tasks.flatMap(task => 
                    task.time.map(t => t.dia_inicio.split(' ')[0]) // Solo la fecha en formato YYYY-MM-DD
                );

                const fechasFin = this.tasks.flatMap(task => 
                    task.time.map(t => t.dia_termino.split(' ')[0]) // Solo la fecha en formato YYYY-MM-DD
                );

                // Convertimos las fechas a objetos Date
                const minFecha = new Date(Math.min(...fechasInicio.map(fecha => new Date(fecha).getTime())));
                const maxFecha = new Date(Math.max(...fechasFin.map(fecha => new Date(fecha).getTime())));

                // Creamos un array de días entre la fecha mínima y máxima
                const diasTotales = [];
                for (let d = new Date(minFecha); d <= maxFecha; d.setDate(d.getDate() + 1)) {
                    diasTotales.push(new Date(d).toISOString().split('T')[0]); // YYYY-MM-DD
                }

                return diasTotales;
            }

        },
        methods:{
            determinarEstatus(componente) {
                const { cargado, enrutado, cortado, programado, ensamblado } = componente;

                let resultado = "Sin estatus";
                if (cargado) {
                    resultado = "En enrutamiento";
                }
                if (enrutado) {
                    resultado = "En corte y programacion";
                }
                if (cortado) {
                    resultado = "En programacion";
                }
                if (programado) {
                    resultado = "En corte";
                }
                if (programado && cortado) {
                    resultado = "En Fabricacion";
                }
                if (ensamblado) {
                    resultado = "Ensamblado";
                }
                return resultado;
            },         
            async verInformacion(task){
                let t = this;
                t.infoComponentes = task;

                $('#modalNuevo').modal('show');
            },  
            getTaskStyle(segment, day) {
                const startDate = new Date(segment.dia_inicio);
                const endDate = new Date(segment.dia_termino);


                // Asegurarse de que la fecha sea interpretada correctamente en la zona horaria local
                const [year, month, date] = day.split('-'); // Separar año, mes, día
                const currentDate = new Date(year, month - 1, date); // Mes es 0-indexado
                currentDate.setHours(0, 0, 0, 0);


                // Rest of the logic remains the same
                if (currentDate < new Date(startDate.toDateString()) || currentDate > new Date(endDate.toDateString())) {
                    return { display: 'none' };
                }

                const taskStartHour = startDate.getHours() + startDate.getMinutes() / 60;
                const taskEndHour = endDate.getHours() + endDate.getMinutes() / 60;

                const totalDayHours = 24;

                const isStartDay = currentDate.toDateString() === startDate.toDateString();
                const isEndDay = currentDate.toDateString() === endDate.toDateString();

                const startPercentage = isStartDay ? (taskStartHour / totalDayHours) * 100 : 0;
                const endPercentage = isEndDay ? (taskEndHour / totalDayHours) * 100 : 100;

                const width = Math.max(0, endPercentage - startPercentage);

                return {
                    left: `${startPercentage}%`,
                    width: `${width}%`,
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
              

        }                
    })

    </script>



        
@endpush