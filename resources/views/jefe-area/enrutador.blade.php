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
        height: 30px;
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

    /* Transición personalizada */
.slide-fade-enter-active, .slide-fade-leave-active {
    transition: all 0.3s ease;
}
.slide-fade-enter, .slide-fade-leave-to {
    opacity: 0;
    transform: translateY(-10px);
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
            <div class="col-xl-2 pt-3" style="background-color: #f1f1f1; height: calc(100vh - 107.3px); overflow-y: scroll">
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
                    <div class="col-xl-8">
                        <h2 class="bold my-0 py-1 mb-3 text-decoration-underline" style="letter-spacing: 2px"> ENRUTADOR</h2>
                    </div>
                    <div class="col-xl-2"  v-if="selectedComponente" >
                        <button class="btn btn-block mt-0" :disabled="componente.enrutado == true" @click="guardar(false)"><i class="fa fa-save"></i> GUARDAR</button>
                    </div>
                     <div class="col-xl-2"  v-if="selectedComponente" >
                        <button class="btn btn-success btn-block mt-0" :disabled="componente.enrutado == true" @click="guardar(true)"><i class="fa fa-check-double"></i> @{{componente.enrutado == true ? 'LIBERADO' : 'LIBERAR'}}</button>
                    </div>
                </div>
                <div class="col-xl-12" v-if="!selectedComponente">
                    <h5 class="text-muted my-4"> SELECCIONE UN COMPONENTE PARA VER SU ENRUTAMIENTO</h5>
                </div>
                <div class="row" v-else>
                    <div class="col-xl-7">
                        <div class="row">
                            <div class="col-xl-2">
                                <span style="font-size: 18px !important; border-color: #c0d340 !important; background-color: #c0d340 !important" class="badge badge-warning badge-pill bold my-4 py-2"> <i class="fa fa-cogs" style="font-size: 16px !important" ></i> @{{componente.nombre}}</span>
                            </div>
                            <div class="col-xl-1">
                                <a class="text-dark" :href="'/storage/' + componente.archivo_2d_public" target="_blank">
                                    <h5 class="my-0 py-0 bold">2D</h5>
                                    <img src="/paper/img/icons/file.png" width="100%">
                                </a>
                            </div>
                            <div class="col-xl-1">
                                <a class="text-dark" :href="'/storage/' + componente.archivo_3d_public" target="_blank">
                                    <h5 class="my-0 py-0 bold">3D</h5>
                                    <img src="/paper/img/icons/file.png" width="100%">
                                </a>
                            </div>
                            <div class="col-xl-1">
                                <a class="text-dark" :href="'/storage/' + componente.archivo_explosionado_public" target="_blank">
                                    <h5 class="my-0 py-0 bold">EXPL.</h5>
                                    <img src="/paper/img/icons/file.png" width="100%">
                                </a>
                            </div>
                            <div class="col-xl-2 form-group">
                                <label class="bold">Cantidad</label>
                                <input type="number" step="any" class="form-control text-center" readonly :value="componente.cantidad">
                            </div>
                            <div class="col-xl-2 form-group">
                                <label class="bold">Prioridad</label>
                                <select class="form-control" v-model="componente.prioridad" :disabled="componente.enrutado == true">
                                    <option :value="null" disabled>Asignar...</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                </select>
                            </div>
                            <div class="col-xl-3 form-group">
                                <label class="bold">Programador</label>
                                <select class="form-control" v-model="componente.programador_id" :disabled="componente.enrutado == true">
                                    <option :value="null" disabled>Seleccionar programador...</option>
                                    <option v-for="p in programadores" :value="p.id"  >@{{p.nombre_completo}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-xl-12">
                            <div class="row">
                                <div class="col-xl-12 px-0" style="overflow-x:scroll">
                                    <div class="gantt-chart" :style="{ '--columns': duracionTotal.length }" >
                                        <div class="gantt-header general-header">
                                            <div class=" time-header pb-2" :colspan="duracionTotal.length" style="letter-spacing: 1px" >TIEMPO TEÓRICO EN HORAS</div>
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
                                                    data-toggle="tooltip" data-html="true" :title="getContenidoTooltip(task)"
                                                    :key="segment.inicio"
                                                    v-if="isTaskInHour(segment, hour)"
                                                    :class="segment.type === 'normal' ? 'normal-task' : segment.type === 'rework' ? 'rework-task' : 'delay-task'"
                                                    :style="getTaskStyle(segment, hour)"
                                                ></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-12 mt-3">
                            <div class="row">
                                <div class="col-xl-12 px-0" style="overflow-x:scroll">
                                    <div class="gantt-chart" :style="{ '--columns': duracionTotal.length }" >
                                        <div class="gantt-header general-header">
                                            <div class=" time-header pb-2" :colspan="duracionTotal.length" style="letter-spacing: 1px" >VISTA DE AVANCE DEL COMPONENTE</div>
                                        </div>
                                        <div class="gantt-header">
                                            <div class="gantt-cell task-name pt-1">ACCIONES</div>
                                            <div class="gantt-cell pt-1" v-for="hour in duracionTotal" :key="hour">@{{ hour }}</div>
                                        </div>
                                        <div class="gantt-row" v-for="task in rutaAvance" :key="task.id" >
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
                                        <div class="limite-tiempo" :style="{ left: `${150 + (40 * totalHoras) + ((40 / 60 ) * totalMinutos) }px` }"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-5">
                        <div class="row">
                            <div class="col-xl-12 table-responsive">
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
                                                        <input type="checkbox" class="form-check-input" @change="toggleTask(p)" v-model="p.incluir" :disabled="componente.enrutado == true">
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
                                                                 <button :disabled="componente.enrutado == true" class="input-group-text py-0 cursor-pointer" style="background-color: #e3e3e3 !important"  @click="p.horas > 0 ? p.horas-- : p.horas"> <i class="fa fa-minus"></i> &nbsp;&nbsp;</button>
                                                             </div>
                                                             <input type="number" v-model="p.horas" class="form-control text-center px-1 py-1" step="1" @change="calcularInicio()">
                                                             <div class="input-group-append">
                                                                 <button :disabled="componente.enrutado == true" class="input-group-text py-0 cursor-pointer" style="background-color: #e3e3e3 !important" @click="p.horas++"> &nbsp;&nbsp;<i class="fa fa-plus"></i> </button>
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
                                                                 <button :disabled="componente.enrutado == true" class="input-group-text py-0 cursor-pointer" style="background-color: #e3e3e3 !important"  @click="p.minutos > 0 ? p.minutos-- : p.minutos"> <i class="fa fa-minus"></i> &nbsp;&nbsp;</button>
                                                             </div>
                                                             <input type="number" v-model="p.minutos" class="form-control text-center px-1 py-1" step="1" @change="calcularInicio()">
                                                             <div class="input-group-append">
                                                                 <button :disabled="componente.enrutado == true" class="input-group-text py-0 cursor-pointer" style="background-color: #e3e3e3 !important" @click="p.minutos < 60 ? p.minutos++ : p.minutos "> &nbsp;&nbsp;<i class="fa fa-plus"></i> </button>
                                                             </div>
                                                         </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-xl-12 text-center mt-3">
                                <h5 class="badge badge-dark badge-pill px-3 py-2" style="background-color: #c0d340 !important; color: black !important"> Tiempo estimado:  @{{totalHoras}} horas y @{{totalMinutos}} minutos. </h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-3 py-0 px-1">
                                <button class="px-1 btn btn-block btn-dark my-1">RE TRABAJO</button>
                            </div>
                            <div class="col-xl-3 py-0 px-1">
                                <button class="px-1 btn btn-block btn-dark my-1" >MODIFICACIÓN</button>
                            </div>
                            <div class="col-xl-3 py-0 px-1">
                                <button class="px-1 btn btn-block btn-dark my-1">RE FABRICACIÓN</button>
                            </div>
                            <div class="col-xl-3 py-0 px-1">
                                <button class="px-1 btn btn-block btn-dark my-1">REFACCIÓN</button>
                            </div>
                        </div>
                         <div class="row mt-3">
                            <div class="col-xl-12">
                                <button @click="mostrarLineaDeTiempo" class="btn btn-block btn-default"><i class="fa fa-calendar"></i> Ver linea de tiempo (@{{componente.nombre}}) </button>
                            </div>
                        </div>
                        {{-- <div class="row">
                            <div class="col-xl-12">
                                <h5 class="bold cursor-pointer" style="letter-spacing: 1px" @click="verVersiones = !verVersiones">
                                    <i class="fa fa-history"></i> Historial de versiones 
                                    <i v-if="verVersiones" class="fa fa-caret-down"></i>
                                    <i v-if="!verVersiones" class="fa fa-caret-right"></i>
                                </h5>
                            </div>
                            <transition name="slide-fade">
                                <div class="col-xl-12 table-responsive" v-if="verVersiones">
                                    <table class="table">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="font-size: 14px">Version</th>
                                                <th style="font-size: 14px">Fecha</th>
                                                <th style="font-size: 14px">Tipo</th>
                                                <th style="font-size: 14px">Motivo</th>
                                                <th style="font-size: 14px">Ver</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="font-size: 11px !important">.1</td>
                                                <td style="font-size: 11px !important">28/11/2024 14:56 Hrs</td>
                                                <td style="font-size: 11px !important" class="bold">REFABRICACION</td>
                                                <td style="font-size: 11px !important">
                                                    Se rompio una pieza fundamental durante el ensamblado y requiere volver a iniciar el trabajo completo.
                                                </td>
                                                <td style="font-size: 11px !important">
                                                    <button class="btn btn-sm btn-default">
                                                        <i class="fa fa-eye"></i> Ver
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 11px !important">.2</td>
                                                <td style="font-size: 11px !important">10/12/2024 10:31 Hrs</td>
                                                <td style="font-size: 11px !important" class="bold">MODIFICACION</td>
                                                <td style="font-size: 11px !important">
                                                    Se rompio una pieza fundamental durante el ensamblado y requiere volver a iniciar el trabajo completo.
                                                </td>
                                                <td style="font-size: 11px !important">
                                                    <button class="btn btn-sm btn-default">
                                                        <i class="fa fa-eye"></i> Ver
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </transition>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="modalLineaTiempo" tabindex="-1" aria-labelledby="modalLineaTiempoLabel" aria-hidden="true">
            <div class="modal-dialog" style="min-width: 70%;">
                <div class="modal-content" >
                    <div class="modal-header">
                        <h3 class="bold modal-title" id="modalLineaTiempoLabel">
                            LINEA DE TIEMPO PARA EL COMPONENTE @{{componente.nombre}}
                        </h3>
                        <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xl-12 table-responsive table-stripped"  style="height: 75vh !important; overflow-y: scroll !important">
                                <table class="table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Hora</th>
                                            <th>Descripción</th>
                                            <th>Maquina</th>
                                            <th>Encargado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(l, index) in lineaTiempo" :key="'linea- ' + index + '-' + l.created_at">
                                            <td>@{{ l.fecha }}</td>
                                            <td>@{{ l.hora }}</td>
                                            <td><span v-html="l.descripcion"></span></td>
                                            <td>@{{ l.maquina }}</td>
                                            <td>@{{ l.area }} <br><small>@{{l.encargado}}</small></td>
                                        </tr>    
                                    </tbody>
                                </table>
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
                {id: 1, prioridad: 1, nombre: 'Cortar', horas: 0, minutos: 0, incluir: false},
                {id: 2, prioridad: 2, nombre: 'Programar', horas: 0, minutos: 0, incluir: false},
                {id: 3, prioridad: 3, nombre: 'Maquinar', horas: 0, minutos: 0, incluir: false},
                {id: 4, prioridad: 4, nombre: 'Tornear', horas: 0, minutos: 0, incluir: false},
                {id: 5, prioridad: 5, nombre: 'Roscar/Rebabear', horas: 0, minutos: 0, incluir: false},
                {id: 6, prioridad: 6, nombre: 'Templar', horas: 0, minutos: 0, incluir: false},
                {id: 7, prioridad: 7, nombre: 'Rectificar', horas: 0, minutos: 0, incluir: false},
                {id: 8, prioridad: 8, nombre: 'EDM', horas: 0, minutos: 0, incluir: false}
            ],
            tasks: [],
            rutaAvance: [],
            programadores: [],
            verVersiones: false,
            lineaTiempo: [],
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
            async mostrarLineaDeTiempo() {
                let t = this;
                this.cargando = true;

                try {
                    const response = await axios.get(`/api/linea-tiempo/${t.componente.id}`);
                    let notificaciones = response.data.notificaciones;
                    let seguimiento = response.data.seguimiento;

                    let lineaTiempo = [];

                    notificaciones.forEach(n => {
                        lineaTiempo.push({
                            fecha: n.fecha,
                            hora: n.hora,
                            tipo: "NOTIFICACION",
                            area: this.generarArea(n.roles, 'notificacion'),
                            descripcion: `<strong>${n.descripcion}</strong>`,
                            created_at: n.created_at,
                            encargado: '',
                            maquina: '-',
                        });
                    });

                    seguimiento.forEach(s => {
                        lineaTiempo.push({
                            fecha: s.fecha_show,
                            hora: s.hora_show,
                            tipo: "SEGUIMIENTO",
                            area: this.generarArea(s.accion),
                            descripcion: this.generarDescripcion(s.accion, s.tipo, s.tipo_paro, s.comentarios_paro),
                            created_at: s.created_at,
                            encargado: s.usuario,
                            maquina: s.maquina
                        });
                    });

                    // Ordenar por created_at
                    this.lineaTiempo = lineaTiempo.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                } catch (error) {
                    console.error('Error fetching linea:', error);
                } finally {
                    this.cargando = false;
                    $('#modalLineaTiempo').modal();

                }
            },
            generarDescripcion(accion, tipo, tipo_paro, motivo) {
                let descripcion = "";
                
                switch (accion) {
                    case "corte_paro":
                        descripcion = tipo === 1 
                            ? "<strong>SE INICIA PARO EN EL PROCESO DE CORTE</strong>" 
                            : "<strong>SE FINALIZA PARO EN EL PROCESO DE CORTE</strong>";
                        break;

                    case "fabricacion_paro":
                        descripcion = tipo === 1 
                            ? "<strong>SE INICIA PARO EN EL PROCESO DE FABRICACION</strong>" 
                            : "<strong>SE FINALIZA PARO EN EL PROCESO DE FABRICACION</strong>";
                        break;
                    case "corte":
                        descripcion = tipo === 1 
                            ? "<strong>INICIA EL PROCESO DE CORTE</strong>" 
                            : "<strong>FINALIZA EL PROCESO DE CORTE</strong>";
                    break;
                    case "fabricacion":
                        descripcion = tipo === 1 
                            ? "<strong>INICIA EL PROCESO DE FABRICACION</strong>" 
                            : "<strong>FINALIZA EL PROCESO DE FABRICACION</strong>";
                    break;
                    case "programacion":
                        descripcion = tipo === 1 
                            ? "<strong>INICIA PROGRAMACION </strong>" 
                            : "<strong>FINALIZA PROGRAMACION </strong>";
                    break;
                    default:
                        descripcion = "ACCION DESCONOCIDA"; // Por si hay otras acciones no previstas
                }   
                if (tipo_paro) {
                    descripcion += `<br><small>MOTIVO: ${tipo_paro}</small>`;
                }
                if (motivo) {
                    descripcion += `<br><small>COMENTARIOS: ${motivo}</small>`;
                }

                return descripcion;
            },
            generarArea(accion, tipo = 'seguimiento') {
                let area = "";
                if(tipo == 'seguimiento'){
                    switch (accion) {
                        case "corte_paro":
                        case "corte":
                            area = 'ALMACENISTA'
                        break;
                        case "programacion":
                            area = 'PROGRAMADOR'
                        break;
                        case "fabricacion_paro":
                        case "fabricacion":
                            area = 'OPERADOR'
                        break;
                        default:
                            area = "AREA DESCONOCIDA"; // Por si hay otras acciones no previstas
                    }
                }else{
                    let accion2 = JSON.parse(accion)
                    accion2.forEach(obj=> {
                        area+= obj + ' ,'
                    });
                    if (area.endsWith(' ,')) {
                        area = area.slice(0, -2);  // Elimina la coma y el espacio al final
                    }
                }
                return area;
            },
            ajustarRutaAvance(tasks, rutaAvance) {
                let convertirAMinutos = (horas, minutos) => horas * 60 + minutos;

                let convertirAHorasYMinutos = (minutosTotales) => {
                    let horas = Math.floor(minutosTotales / 60);
                    let minutos = minutosTotales % 60;
                    return { horas, minutos };
                };

                let tiempoActualEnMinutos = 60;
                rutaAvance.forEach((tareaAvance) => {
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
            getMotivoRetraso(task){
                switch(task.id){
                    case 1: 
                        return this.componente.retraso_corte ? `(${this.componente.retraso_corte})` : '';
                    break
                    case 2: 
                        return this.componente.retraso_programacion ? `(${this.componente.retraso_programacion})` : '';
                    break
                }
            },
            toggleTask(proceso) {
                let t = this

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
            agregarRetraso(taskId) {
                const task = this.tasks.find(task => task.id === taskId);
                if (task) {
                    task.time.push({
                        hora_inicio: null,
                        minuto_inicio: null,
                        horas: 1,
                        minutos: 0,
                        type: "delay"
                    });
                }
                this.calcularInicio();
            },
            calcularInicio() {
                let t = this;
                let acumuladorHoras1 = 1; 
                let acumuladorMinutos1 = 0;
                let acumuladorHoras2 = 1; 
                let acumuladorMinutos2 = 0;

                this.tasks.sort((a, b) => {
                    const prioridadA = this.procesos.find(p => p.id === a.id).prioridad;
                    const prioridadB = this.procesos.find(p => p.id === b.id).prioridad;
                    return prioridadA - prioridadB;
                });

                // Actualizo las tareas avance con los checkbox de las tareas
                t.rutaAvance = JSON.parse(JSON.stringify(t.tasks));
                t.rutaAvance.forEach(element => {
                    element.time = []
                    let find = t.componente.rutaAvance.find(obj => obj.id == element.id)
                    if(find){
                        element.time = find.time
                    }
                })

                const tareasFijas = this.tasks.filter(task => task.id === 1 || task.id === 2);
                const otrasTareas = this.tasks.filter(task => task.id !== 1 && task.id !== 2);

                tareasFijas.forEach(task => {
                    let proceso = t.procesos.find(p => p.id === task.id);
                    task.time.forEach((segmento, index) => {
                        if(index == 0){
                            segmento.hora_inicio = 1;
                            segmento.minuto_inicio = 0;
                            segmento.horas = parseInt(proceso.horas);
                            segmento.minutos = parseInt(proceso.minutos);
                        }else{
                            if(task.id == 1){
                                segmento.hora_inicio = acumuladorHoras1;
                                segmento.minuto_inicio = acumuladorMinutos1;
                            }else{
                                segmento.hora_inicio = acumuladorHoras2;
                                segmento.minuto_inicio = acumuladorMinutos2;
                            }
                        }
                        if(task.id == 1){
                            acumuladorHoras1 += segmento.horas;
                            acumuladorMinutos1 += segmento.minutos;
                        }else{
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

                        if(index == 0){
                            segmento.horas = parseInt(proceso.horas);
                            segmento.minutos = parseInt(proceso.minutos);
                        }

                        acumuladorHoras += segmento.horas;
                        acumuladorMinutos += segmento.minutos;

                        if (acumuladorMinutos >= 60) {
                            acumuladorHoras += Math.floor(acumuladorMinutos / 60);
                            acumuladorMinutos = acumuladorMinutos % 60;
                        }
                    });
                });

                // FALTA ACTUALIZAR EL DE AVANCE CUANDO CAMBIE EL ORIGINAL
                
                // Vue.nextTick(function(){
                //     t.rutaAvance = t.ajustarRutaAvance(t.tasks, t.rutaAvance);
                //     t.calcularInicioAvance();
                //     Vue.nextTick(function(){
                //         $('[data-toggle="tooltip"]').tooltip()
                //     })
                // })

            },
            calcularInicioAvance() {
                let t = this;
                let acumuladorHoras1 = 1; 
                let acumuladorMinutos1 = 0;
                let acumuladorHoras2 = 1; 
                let acumuladorMinutos2 = 0;

                this.rutaAvance.sort((a, b) => {
                    let prioridadA = this.procesos.find(p => p.id === a.id).prioridad;
                    let prioridadB = this.procesos.find(p => p.id === b.id).prioridad;
                    return prioridadA - prioridadB;
                });

                let tareasFijas = this.rutaAvance.filter(task => task.id === 1 || task.id === 2);
                let otrasTareas = this.rutaAvance.filter(task => task.id !== 1 && task.id !== 2);

                tareasFijas.forEach(task => {
                    // let proceso = t.procesos.find(p => p.id === task.id);
                    task.time.forEach((segmento, index) => {
                      
                            if(task.id == 1){
                                segmento.hora_inicio = acumuladorHoras1;
                                segmento.minuto_inicio = acumuladorMinutos1;
                            }else{
                                segmento.hora_inicio = acumuladorHoras2;
                                segmento.minuto_inicio = acumuladorMinutos2;
                            }
                        
                        if(task.id == 1){
                            acumuladorHoras1 += segmento.horas;
                            acumuladorMinutos1 += segmento.minutos;
                        }else{
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
                    // let proceso = t.procesos.find(p => p.id === task.id);
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
                // axios.get('/api/anios')
                try {
                    const response = await axios.get('/api/anios');
                    this.anios = response.data.anios;
                } catch (error) {
                    console.error('Error fetching años:', error);
                } finally {
                    this.cargandoMenu = false;
                    
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
                    const response = await axios.get(`/api/herramentales/${herramentalId}/componentes?area=enrutador`);
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
                t.rutaAvance = [];
                t.tasks = [];

                t.selectedComponente = componenteId;
                t.ruta.componente = t.componentes.find(obj => obj.id == componenteId)?.nombre;
                t.componente = t.componentes.find(obj => obj.id == componenteId);
                t.tasks = JSON.parse(JSON.stringify(t.componente.ruta));
                
                
                let rutaAvanceAux = JSON.parse(JSON.stringify(t.tasks));
                rutaAvanceAux.forEach(element => {
                    element.time = []
                    let find = t.componente.rutaAvance.find(obj => obj.id == element.id)
                    if(find){
                        element.time = find.time
                    }
                })

                t.procesos = [
                    {id: 1, prioridad: 1, nombre: 'Cortar', horas: 0, minutos: 0, incluir: false},
                    {id: 2, prioridad: 2, nombre: 'Programar', horas: 0, minutos: 0, incluir: false},
                    {id: 3, prioridad: 3, nombre: 'Maquinar', horas: 0, minutos: 0, incluir: false},
                    {id: 4, prioridad: 4, nombre: 'Tornear', horas: 0, minutos: 0, incluir: false},
                    {id: 5, prioridad: 5, nombre: 'Roscar/Rebabear', horas: 0, minutos: 0, incluir: false},
                    {id: 6, prioridad: 6, nombre: 'Templar', horas: 0, minutos: 0, incluir: false},
                    {id: 7, prioridad: 7, nombre: 'Rectificar', horas: 0, minutos: 0, incluir: false},
                    {id: 8, prioridad: 8, nombre: 'EDM', horas: 0, minutos: 0, incluir: false}
                ]

                t.tasks.forEach(task => {
                    let proceso = t.procesos.find(obj => obj.id === task.id);
                    
                    if (proceso) {
                        proceso.horas = task.time[0]?.horas ?? 0;  
                        proceso.minutos = task.time[0]?.minutos ?? 0;
                        proceso.incluir = true;
                    }
                });

                Vue.nextTick(function(){
                    t.rutaAvance = t.ajustarRutaAvance(t.tasks, rutaAvanceAux);
                    t.calcularInicioAvance();
                    
                    Vue.nextTick(function(){
                        $('[data-toggle="tooltip"]').tooltip()
                    })
                })

            },
            async guardar(liberarComponente){
                let t = this
                
                
                if(liberarComponente){
                    if(!t.componente.prioridad || !t.componente.programador_id){
                        swal('Errores de validación', `Todos los campos son obligatorios para liberar.`, 'error');
                        return;
                    }
                    if(t.tasks.length == 0){
                        swal('Errores de validación', `El componente debe incluir al menos una accion para poder ser liberado a programacion.`, 'error');
                        return;
                    }
                }
                t.cargando = true;
                t.componente.ruta = JSON.parse(JSON.stringify(t.tasks));
                try {
                    const response = await axios.put(`/api/componente/${t.selectedComponente}/enrutamiento/${liberarComponente}`, t.componente);
                    if(!liberarComponente)
                        swal('Correcto', 'Informacion guardada correctamente', 'success');
                    else
                        swal('Correcto', 'Componente liberado correctamente', 'success');

                    await t.fetchComponentes(t.selectedHerramental);
                    await t.fetchComponente(t.selectedComponente);

                } catch (error) {
                    t.cargando = false
                    return false;
                }  
            },
            async liberarComponente() {
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
                    if (componenteId && componenteId != 'null') {
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
            await t.fetchProgramadores();
            this.navigateFromUrlParams();        
        }

                
    })

    </script>



        
@endpush