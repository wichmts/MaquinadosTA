@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'dashboard'
])


@section('styles')
<link rel="stylesheet" href="{{ asset('paper/css/paper-dashboard-responsivo.css') }}?v={{ time() }}">
@endsection
<style>
    .normal-task {
        position: absolute;
        height: 100%;
        background-color: #4caf50;
        border-radius: 0px;
    }

    .rework-task {
        position: absolute;
        height: 100%;
        background-color: black;
        border-radius: 0px;
    }

    .delay-task {
        position: absolute;
        height: 100%;
        background-color: #ff9430;
        border-radius: 0px;
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

    .limite-tiempo2 {
        position: absolute;
        top: 30px;
        bottom: 0;
        left: 50%; /* O la posición que desees */
        width: 0;  /* El width se debe poner a 0, ya que la línea será con borde */
        border-left: 3px dotted rgb(0, 128, 255); /* Agregar borde punteado */
        z-index: 10;
    }

  


    .gantt-chart2 {
        display: grid;
        grid-template-rows: auto;
        font-family: Arial, sans-serif;
    }


    .gantt-header2, .gantt-row2 {
        display: grid;
        grid-template-columns: 200px repeat(var(--columns, 200), 1fr); /* var(--columns) es una variable CSS */
        height: 30px;
    }
    .gantt-cell2 {
        border: .5px solid #ddd;
        padding: 0px;
        text-align: center;
        font-size: 12px;
        width: 40px;
    }
    .task-name2 {
        background-color: #f0f0f0;
        text-align: center;
        font-weight: bold;
        width: 200px;
        font-size: 15px;
    }

    .normal-task2 {
        position: absolute;
        height: 100%;
        background-color: #4caf50;
        border-radius: 0px;
    }

    .rework-task2 {
        position: absolute;
        height: 100%;
        background-color: #f44336;
        border-radius: 0px;
    }

     .delay-task2 {
        position: absolute;
        height: 100%;
        background-color: #ff9430;
        border-radius: 0px;
    }

    .general-header2 {
        display: grid;
        grid-template-columns: 200px repeat(var(--columns, 200), 1fr); /* var(--columns) es una variable CSS */
        background-color: #f0f0f0;
        font-weight: bold;
        text-align: center;
    }

    .time-header2 {
        grid-column: span var(--columns, 200); /* Cambia este número si tienes más o menos horas */
        font-size: 14px;
        padding: 8px 0;
    }
    .tooltip {
        max-width: none; /* Elimina el límite de ancho predeterminado */
        width: 400px; /* Asegúrate de que el tooltip se ajuste al contenido */
    }
     .tipoParoSeleccionado {
        background-color: #d34040 !important;
        color: white !important;
    }

      .gallery-img {
        width: 100%;
        height: 150px; /* Tamaño fijo para todas las imágenes */
        object-fit: cover; /* Recorta y ajusta la imagen sin deformarla */
        border-radius: 5px;
    }

    .gallery-card {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%; /* Hace que todas las tarjetas tengan la misma altura */
    }

    .gallery-card .card-body {
        text-align: center;
        padding: 5px;
    }
    .square2 {
        width: 25%;
        height: 12px;
        margin-top: 7px;
    }
    .square {
        width: 15px;
        height: 10px;
    }

    .dotted-line-anterior {
        border-right: 4px dotted orange;
        height: 15px;
        display: inline-block;
    }
    .dotted-line-favor {
        border-right: 4px dotted rgb(0, 136, 255);
        height: 15px;
        display: inline-block;
    }
    .dotted-line-limite {
        border-right: 4px dotted rgb(230, 214, 43);
        height: 15px;
        display: inline-block;
    }
    .dotted-line-atrazo {
        border-right: 4px dotted rgb(255, 58, 58);
        height: 15px;
        display: inline-block;
    }
    
    .table .form-check label .form-check-sign::before, .table .form-check label .form-check-sign::after {top: -10px !important}
</style>

@section('content')
<div id="vue-app">

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
                                <template v-for="obj in proyectos">
                                    @if(auth()->user()->hasRole('JEFE DE AREA'))
                                    <a class="nav-link cursor-pointer"  @click="fetchHerramentales(obj.id)">
                                        <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/carpetas.png') }}"></i> &nbsp;
                                        <span class="underline-hover">@{{obj.nombre}}</span> 
                                    </a>
                                    @else
                                    <a class="nav-link cursor-pointer"  @click="fetchHerramentales(obj.id)"  v-if="(ruta.cliente !== 'REFACCIONES' && ruta.cliente !== 'ORDENES EXTERNAS') || esMiCarpeta(obj.nombre)">
                                        <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/carpetas.png') }}"></i> &nbsp;
                                        <span class="underline-hover">@{{obj.nombre}}</span> 
                                    </a>
                                    @endif
                                </template>
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
                                <a class="d-flex align-items-center nav-link cursor-pointer" v-for="obj in componentes" v-if="!obj.refabricado" @click="fetchComponente(obj.id)">
                                    <i class="nc-icon" style="top: -3px !important"><img height="17px" src="{{ asset('paper/img/icons/componentes.png') }}"></i> &nbsp;
                                    <span class="underline-hover">
                                        @{{obj.nombre}} 
                                    </span>
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
            <div class="content" style="height: 75vh; overflow-y: scroll !important">
                <div class="row mb-2" v-cloak v-show="!cargando">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-6">
                                <h2 class="bold my-0 py-1 mb-3 text-decoration-underline" style="letter-spacing: 2px">VISOR DE AVANCE @{{ruta.herramental}}</h2> 
                                <div v-if="selectedHerramental" class="d-flex gap-1 align-items-center">
                                    <div class="square bg-danger"></div> <span class="bold" style="letter-spacing: 1px"> &nbsp; Retrabajos &nbsp;&nbsp;&nbsp; </span>
                                    <div class="square bg-warning"></div> <span class="bold" style="letter-spacing: 1px"> &nbsp; Retrasos &nbsp;&nbsp;&nbsp; </span>
                                    <div class="square bg-dark"></div> <span class="bold" style="letter-spacing: 1px"> &nbsp; Refabricaciones &nbsp;&nbsp;&nbsp;  </span>
                                    <div class="square bg-info"></div> <span class="bold" style="letter-spacing: 1px"> &nbsp; Refacción </span>
                                    
                                </div>
                            </div>
                            <div class="col-lg-2 px-2 py-1" v-if="selectedHerramental">
                                <span class="bold" style="letter-spacing: 1px; font-size: 13px"> &nbsp; <div class="dotted-line-favor"></div> Tiempo a favor </span> <br>
                                <span class="bold" style="letter-spacing: 1px; font-size: 13px"> &nbsp; <div class="dotted-line-atrazo"></div> Atrazo </span> <br>
                                <span class="bold" style="letter-spacing: 1px; font-size: 13px"> &nbsp; <div class="dotted-line-limite"></div> Tiempo limite </span> <br>
                                <span class="bold" style="letter-spacing: 1px; font-size: 13px"> &nbsp; <div class="dotted-line-anterior"></div> Limite anterior </span> 
                            </div>
                            <div class="col-lg-2 text-right">
                                <span v-if="selectedHerramental" class="bold">Fecha de creación:</span>  <br>
                                <span v-if="selectedHerramental" class="bold">Fecha límite:</span> <br>
                                <span v-if="selectedHerramental" class="bold"> Fecha de finalización:</span> 
                            </div>
                            <div class="col-lg-2">
                                <span  v-if="selectedHerramental">@{{herramental.fecha_creacion}}</span> <br>
                                <span  v-if="selectedHerramental">@{{herramental.fecha_limite_show??' Sin fecha'}}</span> <br>
                                <span  v-if="selectedHerramental">@{{herramental.fecha_finalizado}}</span>
                                @if(auth()->user()->hasRole('PROYECTOS'))
                                <button v-if="selectedHerramental" @click="agregarFechaLimite()" class=" btn-block btn btn-sm mt-2 mb-1"><i class="fa fa-calendar-day"></i> Agregar fecha limite </button>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-12" v-if="!selectedHerramental">
                            <h5 class="text-muted my-4"> SELECCIONE UN HERRAMENTAL PARA VER SU AVANCE</h5>
                        </div>
                        <div class="row" v-else>
                            <div class="col-lg-12 " v-if="tasks.length == 0">
                                <h5 class="text-muted">Este herramental aun no tiene componentes cargados...</h5>
                            </div>
                            <div class="col-lg-12" v-else style="overflow-x:scroll">
                            <div class="gantt-chart3" >
                                <div class="gantt-header3 general-header">
                                    <div class="time-header pb-2" >TIEMPO (DIAS)</div>
                                </div>
                                <div class="gantt-header3">
                                    <div class="gantt-cell3 task-name3 pt-2">ACCIONES</div>
                                    <div class="gantt-cell3 pt-2" v-for="day in duracionTotal" :key="day">
                                        <span class="bold">@{{ day }}</span>
                                    </div>
                                </div>
                                <div class="gantt-row3 cursor-pointer" v-for="task in tasks" :key="task.id" @click="verInformacion(task)">
                                    <div class="gantt-cell3 task-name3 pt-2">
                                        @{{ task.componente }} 
                                        <div class="d-flex gap-1 align-items-center">
                                            <div v-if="task.componente_id > 0 && task.tieneRetrabajos" class="square2 bg-danger"></div>
                                            <div v-if="task.componente_id > 0 && task.tieneRetrasos" class="square2 bg-warning"></div>
                                            <div v-if="task.componente_id > 0 && task.tieneRefabricaciones" class="square2 bg-dark"></div>
                                            <div v-if="task.componente_id > 0 && task.esRefaccion" class="square2 bg-info"></div>
                                        </div>
                                    </div>
                                    <div class="gantt-cell3 gantt-bar3" v-for="day in duracionTotal" :key="day" >
                                        <div
                                            v-for="segment in task.time"
                                            class=""
                                            :key="segment.dia_inicio"
                                            :class="segment.type === 'normal' ? 'normal-task' : segment.type === 'rework' ? 'rework-task' : 'delay-task'"
                                            :style="getTaskStyle(segment, day)">
                                        </div>
                                    </div>
                                </div>
                                <div v-for="(pos, index) in otrasFechasPosiciones" :key="'limite-otra-' + index" class="limite-tiempo-anterior" :style="{ left: `${pos}px` }"></div>
                                <div v-if="herramental.fecha_limite" class="limite-tiempo-limite" :style="{ left: `${215 + (80 * (totalDias)) }px` }"></div>
                                <div :class="{'limite-tiempo-favor': proyectoAFavor,  'limite-tiempo-atrazo': !proyectoAFavor}"  :style="{ left: `${posicionFinRealProyecto}px` }"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="modalComponente" tabindex="-1" aria-labelledby="modalComponenteLabel" aria-hidden="true">
                    <div class="modal-dialog" style="min-width: 60%;">
                        <div class="modal-content" >
                            <div class="modal-header">
                                <h3 class="modal-title" id="modalComponenteLabel">
                                    <span class="bold">INFORMACIÓN DE @{{infoComponentes.componente}}</span>
                                </h3>
                                <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <div class="modal-body">
                                <div class="col-lg-12 accordion" id="accordionComponentes" v-if="infoComponentes.time && infoComponentes.time.length > 0">
                                    <div class="card" v-for="(component, index) in infoComponentes.time.slice().reverse()" :key="component.version" style="border-radius: 20px !important">
                                        <!-- Cabecera del acordeón -->
                                        <div class="card-header cursor-pointer" :id="'heading-' + component.version" :class="{'bg-success text-white': component.type == 'normal', 'bg-dark text-white': component.type == 'rework'}" data-toggle="collapse"  :data-target="'#collapse-' + component.version"  :aria-expanded="componenteIdSeleccionado ? component.info?.id == componenteIdSeleccionado : index === 0"  :aria-controls="'collapse-' + component.version">
                                            <h5 class="bold"> Version @{{ component.version }} &nbsp;&nbsp;  <small>(@{{component.dia_inicio}} Hrs. - @{{component.dia_termino}} Hrs.)</small></h5>
                                        </div>
                                        <!-- Contenido colapsable -->
                                        <div :id="'collapse-' + component.version" class="collapse" :class="{ show: componenteIdSeleccionado ? component.info?.id == componenteIdSeleccionado : index === 0 }" :aria-labelledby="'heading-' + component.version" data-parent="#accordionComponentes">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-lg-9" style="border-right: 1px solid #ededed">
                                                        <div class="mb-2">
                                                            <span><strong>Tipo de componente: </strong> @{{component.info.es_compra ? 'COMPRA' : 'FABRICACIÓN'}}</span><br>
                                                            <span><strong>Cantidad: </strong> @{{component.info.cantidad}}&nbsp;&nbsp;</span>
                                                            <span v-if="!component.info.es_compra"><strong>Material: </strong> @{{component.info.material_nombre}} @{{component.info.material_id == 6 ? '(' + component.info.otro_material + ')' : ''}}</span>
                                                            <span v-if="component.info.es_compra"><strong>Proveedor / Material: </strong> @{{component.info.proveedor}}&nbsp;&nbsp; <br>            </span>
                                                            <span v-if="component.info.es_compra"><strong>Descripción: </strong> @{{component.info.descripcion}}&nbsp;&nbsp;</span>

                                                            <span v-if="component.info.material_id == 2 || component.info.material_id == 2 || component.info.material_id == 5 || component.info.material_id == 4"><strong>Largo: </strong> @{{component.info.largo}}&nbsp;&nbsp;</span>
                                                            <span v-if="component.info.material_id == 1 || component.info.material_id == 6 || component.info.material_id == 2 || component.info.material_id == 5 || component.info.material_id == 4"><strong>Ancho: </strong> @{{component.info.ancho}}&nbsp;&nbsp;</span>
                                                            <span v-if="component.info.material_id == 1 || component.info.material_id == 6 || component.info.material_id == 2 || component.info.material_id == 5 "><strong>Espesor: </strong> @{{component.info.espesor}}&nbsp;&nbsp;</span>
                                                            <span v-if="component.info.material_id == 3"><strong>Longitud: </strong> @{{component.info.longitud}}&nbsp;&nbsp;</span>
                                                            <span v-if="component.info.material_id == 3"><strong>Diametro: </strong> @{{component.info.diametro}}&nbsp;&nbsp;</span>
                                                        </div>
                                                        <div class="mb-2" v-if="!component.info.es_compra">
                                                            <span><strong>Fecha de Carga:</strong> @{{ component.info.fecha_cargado }}</span> <br>
                                                            <span><strong>Fecha Términado:</strong> @{{ component.info.fecha_terminado ?? ' Sin finalizar' }}</span> <br>
                                                            <span><strong>Fecha Ensamblado:</strong> @{{ component.info.requiere_ensamble ? (component.info.fecha_ensamblado ?? 'Sin ensamblar') : 'No requiere ensamble' }}</span> <br>
                                                            <span><strong>Ultimo estatus:</strong> </span> <span class="badge badge-dark badge-pill px-2 py-1 my-2">@{{determinarEstatus(component.info)}}</span> <br>
                                                            <div v-if="component.info.requiere_temple">
                                                                <span class="bold">Detalles de temple: <br></span>
                                                                <div class="ml-2">
                                                                    <small><strong>Fecha solicitud:</strong> @{{ component.info.fecha_solicitud_temple }}</small> <br>
                                                                    <small><strong>Fecha envio:</strong> @{{ component.info.fecha_envio_temple ?? '-' }}</small> <br>
                                                                    <small><strong>Fecha estimada:</strong> @{{ component.info.fecha_estimada_temple ?? '-' }}</small><br>
                                                                    <small><strong>Fecha recibido:</strong> @{{ component.info.fecha_recibido_temple ?? '-' }}</small><br>
                                                                </div>
                                                            </div>
        
                                                        </div>
                                                        <div v-else>
                                                            <span><strong>Fecha solicitud:</strong> @{{ component.info.fecha_solicitud }}</span> <br>
                                                            <span><strong>Fecha pedido:</strong> @{{ component.info.fecha_pedido ?? '-' }}</span> <br>
                                                            <span><strong>Fecha estimada:</strong> @{{ component.info.fecha_estimada ?? '-' }}</span> <br>
                                                            <span><strong>Fecha compra real:</strong> @{{ component.info.fecha_real ?? '-' }}</span> <br>
                                                        </div>
                
                                                        <div class="mb-2 row">
                                                            
                                                            <div class="col">
                                                                <a class="btn btn-block btn-sm btn-default" :href="'/storage/' + component.info.archivo_2d_public" target="_blank">
                                                                    <i class="fa fa-download"></i> Vista 2D
                                                                </a>
                                                            </div>
                                                            <div class="col">
                                                                <a class="btn btn-block btn-sm btn-default" :href="'/storage/' + component.info.archivo_3d_public" target="_blank">
                                                                    <i class="fa fa-download"></i> Vista 3D
                                                                </a>
                                                            </div>
                                                            <div class="col">
                                                                <a class="btn btn-block btn-sm btn-default" :href="'/storage/' + component.info.archivo_explosionado_public" target="_blank">
                                                                    <i class="fa fa-download"></i> Explosionado
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <div class="text-center">
                                                            <button @click="verModalRuta(component.info.id)" v-if="!component.info.es_compra" class="btn btn-block btn-sm btn-dark mx-2"><i class="fa fa-eye"></i> Visor componente</button>
                                                            <button @click="verCotas(component.info)" v-if="!component.info.es_compra" class="btn btn-block btn-sm btn-dark mx-2"><i class="fa fa-ruler-combined"></i> Cotas críticas</button>
                                                            <button @click="verFotografias(component.info)" v-if="!component.info.es_compra" class="btn btn-block btn-sm btn-dark mx-2"><i class="fa fa-images"></i> Galeria fabricacíon</button>
                                                            <button @click="mostrarLineaDeTiempo(component.info.id)" v-if="!component.info.es_compra" class="btn btn-block btn-sm btn-dark mx-2"><i class="fa fa-calendar-alt"></i> Linea de tiempo</button>
                                                            <button @click="fetchSolicitudes(component.info.id)" v-if="!component.info.es_compra" class="btn btn-block btn-sm btn-dark mx-2"><i class="fa fa-list"></i> Solicitudes</button>
                                                            @if(auth()->user()->hasRole('SOLICITUD EXTERNA'))
                                                                <button @click="solicitarRefaccion(component.info.id)" v-if="!component.info.es_compra && !component.info.esComponenteExterno" class="btn btn-block btn-sm btn-info mx-2"><i class="fa fa-puzzle-piece"></i> Solicitar Refacción</button>
                                                            @endif                                                        
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> 
                            <div class="modal-footer my-0 py-1">
                                <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="modalEnsamble" tabindex="-1" aria-labelledby="modalEnsambleLabel" aria-hidden="true">
                    <div class="modal-dialog" style="min-width: 70%;">
                        <div class="modal-content" >
                            <div class="modal-header">
                                <h3 class="modal-title" id="modalEnsambleLabel">
                                    <span class="bold">INFORMACIÓN DE ENSAMBLE</span>
                                </h3>
                                <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <div class="modal-body">
                                <div class="col-lg-12">
                                    <div class="mb-2">
                                        <span><strong>Estatus de ensamble:</strong> </span> <span class="badge badge-dark badge-pill px-2 py-1 my-2">@{{herramental.estatus_ensamble.toUpperCase()}}</span> <br>
                                        <span><strong>Fecha de inicio ensamble: </strong> @{{herramental.inicio_ensamble ?? 'Sin iniciar'}}</span><br>
                                        <span><strong>Fecha de fin ensamble: </strong> @{{herramental.termino_ensamble ?? 'Sin terminar'}}</span><br>
                                    </div>
                                    <div class="mb-2 row">
                                        <div class="col-lg-4">
                                            <a class="btn btn-block btn-default" :href="'/api/download/' + herramental.archivo2">
                                                <i class="fa fa-download"></i> FORMATO F71-03 ANEXO 1.1
                                            </a>
                                        </div>
                                        <div class="col-lg-4">
                                            <button class="btn btn-block btn-dark" @click="verFotografiasEnsamble"><i class="fa fa-camera"></i> Ver fotos</button>
                                        </div>
                                    </div>
                                    <div class="mb-2 row">
                                        <div class="col-lg-12">
                                            <h5 class="bold">RECHAZOS Y AJUSTES </h5>
                                        </div>
                                        <div class="col-lg-12">
                                            <table class="table">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th class="py-1">Fecha</th>
                                                        <th class="py-1">Componente</th>
                                                        <th class="py-1">Tipo</th>
                                                        <th class="py-1">Descripcion</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <template v-if="solicitudesEnsamble && solicitudesEnsamble.length > 0">
                                                        <tr v-for="sol in solicitudesEnsamble" :key="sol.id">
                                                            <td>@{{sol.fecha_show}}</td>
                                                            <td>@{{sol.componente}}</td>
                                                            <td>@{{sol.tipo.toUpperCase()}}</td>
                                                            <td>@{{sol.comentarios}}</td>
                                                        </tr>
                                                    </template>
                                                    <tr v-else>
                                                        <td colspan="4">No hay rechazos ni ajustes para este herramental</td>
                                                    </tr>
        
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>  
                                </div>
                            </div> 
                            <div class="modal-footer my-0 py-1">
                                <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="modalPruebasDiseño" tabindex="-1" aria-labelledby="modalPruebasDiseñoLabel" aria-hidden="true">
                    <div class="modal-dialog" style="min-width: 35%;">
                        <div class="modal-content" >
                            <div class="modal-header">
                                <h3 class="modal-title" id="modalPruebasDiseñoLabel">
                                    <span class="bold">INFORMACIÓN DE LA PRUEBA DISEÑO (@{{prueba.nombre}})</span>
                                </h3>
                                <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <div class="modal-body">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-12 text-center">
                                            <div class="mb-2">
                                                <span><strong>Estatus de la prueba:</strong> <br> </span> 
                                                    <span v-if="prueba.liberada == true" class="badge badge-success badge-pill px-2 py-1 my-2">LIBERADA</span>
                                                    <span v-else class="badge badge-dark badge-pill px-2 py-1 my-2">NO LIBERADA</span>
                                                <br>
                                                <span><strong>Fecha de inicio: </strong> <br> @{{ prueba.fecha_inicio_show }}</span><br>
                                                <span><strong>Fecha de liberación: </strong> <br> @{{ prueba.fecha_liberada_show??'Sin liberar' }}</span><br>
                                                <span><strong>Involucrados en la prueba: </strong> <br> @{{ prueba.involucrados }}</span><br>
                                                <span><strong>Descripcion de la prueba: </strong> <br> @{{prueba.descripcion}}</span><br>
                                                <span><strong>Hallazgos: </strong> <br> @{{prueba.hallazgos}}</span><br>
                                                <span><strong>Plan de accion: </strong> <br> @{{prueba.plan_accion}}</span><br>
                                            </div>
                                            <div class="mb-2 row">
                                                <div class="col-lg-12 text-center">
                                                    <a class="btn btn-sm btn-default" :href="'/api/download/pruebas-diseno/' + prueba.archivo_dimensional">
                                                        <i class="fa fa-download"></i> ARCHIVO DIMENSIONAL
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                            
                                </div>
                            </div> 
                            <div class="modal-footer my-0 py-1">
                                <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="modalPruebasProceso" tabindex="-1" aria-labelledby="modalPruebasProcesoLabel" aria-hidden="true">
                    <div class="modal-dialog" style="min-width: 35%;">
                        <div class="modal-content" >
                            <div class="modal-header">
                                <h3 class="modal-title" id="modalPruebasProcesoLabel">
                                    <span class="bold">INFORMACIÓN DE LA PRUEBA PROCESO (@{{prueba.nombre}})</span>
                                </h3>
                                <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <div class="modal-body">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-12 text-center" >
                                            <div class="mb-2">
                                                <span><strong>Estatus de la prueba:</strong> <br> </span> 
                                                    <span v-if="prueba.liberada == true" class="badge badge-success badge-pill px-2 py-1 my-2">LIBERADA</span>
                                                    <span v-else class="badge badge-dark badge-pill px-2 py-1 my-2">NO LIBERADA</span>
                                                <br>
                                                <span><strong>Fecha de inicio: </strong> <br> @{{ prueba.fecha_inicio_show }}</span><br>
                                                <span><strong>Fecha de liberación: </strong> <br> @{{ prueba.fecha_liberada_show??'Sin liberar' }}</span><br>
                                                <span><strong>Descripcion de la prueba: </strong> <br> @{{prueba.descripcion}}</span><br>
                                                <span><strong>Comentarios: </strong> <br> @{{prueba.comentarios}}</span><br>
                                                <span><strong>Plan de accion: </strong> <br> @{{prueba.plan_accion}}</span><br>
                                            </div>
                                            <div class="mb-2 row">
                                                <div class="col-lg-12 text-center">
                                                    <a class="btn btn-sm btn-default" :href="'/api/download/pruebas-proceso/' + prueba.archivo">
                                                        <i class="fa fa-download"></i> FORMATO F71-03 ANEXO 2
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 text-center">
                                            <h5 class="bold">Fotografia del herramental</h5>
                                            <div class="card">
                                                <a class="px-0 mx-0" :href="'/storage/pruebas-proceso/' + prueba.foto" v-if="prueba.foto" data-lightbox="prueba-proceso">
                                                    <img :src="'/storage/pruebas-proceso/' + prueba.foto" class="gallery-img" alt="Foto de fabricación">
                                                </a>
                                                <img v-else src="/paper/img/no-image.png" class="gallery-img" alt="Sin imagen">
                                            </div>
                                        </div>
                                    </div>
                                            
                                </div>
                            </div> 
                            <div class="modal-footer my-0 py-1">
                                <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="modalSolicitudes" tabindex="-1" aria-labelledby="modalSolicitudesLabel" aria-hidden="true">
                    <div class="modal-dialog" style="min-width: 70%;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="bold modal-title" id="modalSolicitudesLabel">
                                    SOLICITUDES ASOCIADAS AL COMPONENTE @{{componente.nombre}}
                                </h3>
                                <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-lg-12 table-responsive table-stripped" style="max-height: 75vh !important; overflow-y: scroll !important">
                                        <table class="table">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Hora</th>
                                                    <th>Tipo</th>
                                                    <th>Programa</th>
                                                    <th>Comentarios</th>
                                                    <th>Solicita</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-if="solicitudes.length == 0">
                                                    <td colspan="6"> No hay ninguna solicitud de retrabajo, modificacion o ajuste pendiente para este componente </td>
                                                </tr>
                                                <tr v-for="(l, index) in solicitudes" :key="'linea- ' + index + '-' + l.created_at">
                                                    <td>@{{ l.fecha }}</td>
                                                    <td>@{{ l.hora }}</td>
                                                    <td class="bold">@{{ l.tipo.toUpperCase() }}</td>
                                                    <td>@{{ l.programa }}</td>
                                                    <td><span v-html="l.comentarios"></span></td>
                                                    <td>@{{ l.usuario.nombre_completo }} <br><small>@{{l.area_solicitante}}</small></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer my-0 py-1">
                                <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="modalLineaTiempo" tabindex="-1" aria-labelledby="modalLineaTiempoLabel" aria-hidden="true">
                    <div class="modal-dialog" style="min-width: 70%;">
                        <div class="modal-content">
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
                                    <div class="col-lg-12 table-responsive table-stripped" style="height: 75vh !important; overflow-y: scroll !important">
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
                                                    <td style="width: 15% !important">@{{ l.fecha }}</td>
                                                    <td style="width: 10% !important">@{{ l.hora }}</td>
                                                    <td style="width: 40% !important"><span v-html="l.descripcion"></span></td>
                                                    <td style="width: 15% !important">@{{ l.maquina }}</td>
                                                    <td style="width: 20% !important">@{{ l.area }} <br><small>@{{l.encargado}}</small></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer my-0 py-1">
                                <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="modalFotografias" tabindex="-1" aria-labelledby="modalFotografiasLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" style="min-width: 70%; min-height: 70%">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title font-weight-bold" id="modalFotografiasLabel">
                                    Galería de fabricación para @{{ componente.nombre }}
                                </h3>
                                <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
        
                            <div class="modal-body">
                                <div class="container">
                                    <div v-if="componente.fabricaciones.length">
                                        <div class="row">
                                            <div class="col-md-4 mb-3" v-for="f in componente.fabricaciones" :key="f.id">
                                                <div class="card gallery-card">
                                                    <a class="px-0 mx-0" :href="'/storage/fabricaciones/' + f.foto" v-if="f.foto" data-lightbox="fabricacion" :data-title="getMaquina(f.maquina_id) + ' (' + f.updated_at.substring(0,10) + ')'">
                                                        <img :src="'/storage/fabricaciones/' + f.foto" class="gallery-img" alt="Foto de fabricación">
                                                    </a>
                                                    <img v-else src="/paper/img/no-image.png" class="gallery-img" alt="Sin imagen">
                                                    <div class="card-body">
                                                        <p class="card-text mb-0"><strong>@{{ getMaquina(f.maquina_id) }}</strong></p>
                                                        <small class="text-muted">@{{ f.updated_at.substring(0,10) }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="!componente.fabricaciones.length && !componente.foto_matricero && (!componente.pruebas || !componente.pruebas.length)" class="text-center text-muted">
                                        <p>No hay imágenes disponibles para este componente.</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 text-right">
                                        <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="modalCotas" tabindex="-1" aria-labelledby="modalCotasLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" style="max-width: 40%">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title font-weight-bold" id="modalCotasLabel">
                                    Cotas críticas para el componente @{{ componente.nombre }}
                                </h3>
                                <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
        
                            <div class="modal-body">
                                <div class="container">
                                    <div v-if="componente.cuotas_criticas && componente.cuotas_criticas.length > 0">
                                        <div class="row">
                                            <div class="col-lg-12 form-group" v-for="(cota, index) in componente.cuotas_criticas">
                                                <label class="bold">Cota @{{index + 1}} - @{{cota.valor}}</label>
                                                <input type="text" disabled class="form-control" :value="cota.valor_real" />
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="text-center text-muted">
                                        <p>No hay medidas registradas aún para este componente.</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 text-right">
                                        <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="modalFotografiasEnsamble" tabindex="-1" aria-labelledby="modalFotografiasEnsambleLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" style="min-width: 70%;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title font-weight-bold" id="modalFotografiasEnsambleLabel">
                                    Galeria de ensamble para @{{ herramental.nombre }}
                                </h3>
                                <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
        
                            <div class="modal-body">
                                <div class="container">
                                    <div v-if="componentes.length">
                                        <div class="row">
                                            <div class="col-md-4 mb-3" v-for="componente in componentes" :key="componente.id" v-if="componente.foto_matricero">
                                                <div class="card gallery-card">
                                                    <a class="px-0 mx-0" :href="'/storage/fotos_matricero/' + componente.foto_matricero" v-if="componente.foto_matricero" data-lightbox="fabricacion" :data-title="componente.nombre + ' (' + componente.fecha_ensamblado??'Sin ensamblar' + ')'">
                                                        <img :src="'/storage/fotos_matricero/' + componente.foto_matricero" class="gallery-img" alt="Foto de ensamble">
                                                    </a>
                                                    <img v-else src="/paper/img/no-image.png" class="gallery-img" alt="Sin imagen">
                                                    <div class="card-body">
                                                        <p class="card-text mb-0"><strong>@{{ componente.nombre }} v@{{componente.version}}</strong></p>
                                                        <small class="text-muted">@{{ componente.fecha_ensamblado??'Sin ensamblar' }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 text-right">
                                        <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="modalRuta" tabindex="-1" aria-labelledby="modalRutaLabel" aria-hidden="true" >
                    <div class="modal-dialog"  style="min-width: 80%;">
                        <div class="modal-content" >
                            <div class="modal-header">
                                <h3 class="my-0 py-0 bold">RUTA PARA EL COMPONENTE @{{componente.nombre}}</h3>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row d-flex align-items-center">
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="col-lg-12" style="overflow-x:scroll">
                                                <div class="gantt-chart" :style="{ '--columns': duracionTotal2.length }" >
                                                    <div class="gantt-header2 general-header2">
                                                        <div class=" time-header2 pb-2" :colspan="duracionTotal2.length" style="letter-spacing: 1px" >TIEMPO TEÓRICO EN HORAS</div>
                                                    </div>
                                                    <div class="gantt-header2">
                                                        <div class="gantt-cell2 task-name2 pt-1">ACCIONES</div>
                                                        <div class="gantt-cell2 pt-1" v-for="hour in duracionTotal2" :key="hour">
                                                            @{{ hour }}
                                                        </div>
                                                    </div>
                                                    <div class="gantt-row2" v-for="task in tasks2" :key="task.uuid" >
                                                        <div class="gantt-cell2 task-name2 pt-1">@{{ task.name }}</div>
                                                        <div class="gantt-cell2 gantt-bar" v-for="hour in duracionTotal2" :key="hour">
                                                            <div
                                                            v-for="segment in task.time"
                                                            data-toggle="tooltip" data-html="true" :title="getContenidoTooltip(task)"
                                                            :key="segment.inicio"
                                                            v-if="isTaskInHour(segment, hour)"
                                                            :class="segment.type === 'normal' ? 'normal-task2' : segment.type === 'rework' ? 'rework-task2' : 'delay-task2'"
                                                            :style="getTaskStyle2(segment, hour)"
                                                            ></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-lg-12" style="overflow-x:scroll">
                                                <div class="gantt-chart" :style="{ '--columns': duracionTotal2.length }" >
                                                    <div class="gantt-header2 general-header2">
                                                        <div class=" time-header2 pb-2" :colspan="duracionTotal2.length" style="letter-spacing: 1px" >TIEMPO REAL EN HORAS</div>
                                                    </div>
                                                    <div class="gantt-header2">
                                                        <div class="gantt-cell2 task-name2 pt-1">ACCIONES</div>
                                                        <div class="gantt-cell2 pt-1" v-for="hour in duracionTotal2" :key="hour">@{{ hour }}</div>
                                                    </div>
                                                    <div class="gantt-row2" v-for="task in rutaAvance" :key="task.uuid" >
                                                        <div class="gantt-cell2 task-name2 pt-1">@{{ task.name }}</div>
                                                        <div class="gantt-cell2 gantt-bar" v-for="hour in duracionTotal2" :key="hour">
                                                            <div
                                                                v-for="segment in task.time"
                                                                data-toggle="tooltip" data-html="true" :title="getContenidoTooltip(task)"
                                                                :key="segment.inicio"
                                                                v-if="isTaskInHour(segment, hour)"
                                                                :class="segment.type === 'normal' ? 'normal-task2' : segment.type === 'rework' ? 'rework-task2' : 'delay-task2'"
                                                                :style="getTaskStyle2(segment, hour)">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="limite-tiempo" :style="{ left: `${215 + (40 * totalHoras) + ((40 / 60 ) * totalMinutos) }px` }"></div>
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
                <div class="modal fade" id="modalSolicitarRefaccion" tabindex="-1" aria-labelledby="modalSolicitarRefaccionLabel" aria-hidden="true">
                    <div class="modal-dialog" style="min-width: 30%;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="bold modal-title" id="modalSolicitarRefaccionLabel">
                                    NUEVA SOLICITUD DE REFACCIÓN
                                </h3>
                                <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-lg-12 form-group">
                                        <label class="bold">Solicitada por<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" value="{{auth()->user()->nombre_completo}}" disabled>
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <label class="bold">Fecha deseada de entrega <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" v-model="nuevaRefaccion.fecha_deseada_entrega" required>
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <label class="bold">Cantidad <span class="text-danger">*</span></label>
                                        <input type="number" step="1" class="form-control" v-model="nuevaRefaccion.cantidad" required>
                                    </div>
                                    <div class="col-lg-12 form-group">
                                        <label class="bold">Comentarios </label>
                                        <textarea class="form-control my-1 px-1 text-left" placeholder="Comentarios para el enrutador..." v-model="nuevaRefaccion.comentarios" rows="3"></textarea>
                                    </div>
                                    <div class="col-lg-12">
                                        <small>Todas las solicitudes de refacciones se pueden visualizar y dar seguimiento desde el menu <strong>ORDEN DE TRABAJO.</strong></small>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer my-0 py-1">
                                <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
                                <button v-if="!loading_button" class="btn" @click="generarSolicitudRefaccion()"><i class="fa fa-paper-plane"></i> Enviar solicitud</button>
                                <button v-if="loading_button" class="btn" disabled><i class="fa fa-spinner"></i> Enviando...</button>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')

    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>


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
            componente: {
                fabricaciones: [],
                pruebas: [],
            },
            infoComponentes: [
                {
                    time: [
                        { es_compra: false }
                    ],
                }
            ],
            seleccionado: null,
            solicitudes: [],
            lineaTiempo: [],
            maquinas: [],
            tasks2: [],
            rutaAvance: [],
            herramental: {estatus_ensamble: 'Sin ensamblar'},
            pruebasProceso: [],
            pruebasDiseno: [],
            prueba: {},
            solicitudesEnsamble: [],
            componenteIdSeleccionado: null,
            cuotas_criticas: [],
            nuevaRefaccion: {
                componente_id: null,
                fecha_deseada_entrega: new Date().toISOString().split('T')[0],
                solicitante_id: {{ auth()->user()->id }},
                area_solicitud: 'Herramentales',
                comentarios: '',
                desde: 'refacciones',
                cantidad: 1, 
            },
        },
        mounted: async function () {
            let t = this;
            await t.fetchAnios();
            await t.fetchMaquinas();
            await t.fetchMateriales();
            this.navigateFromUrlParams();
        },
        computed: {
            proyectoAFavor() {
                if (!this.herramental?.fecha_limite) return true; // No hay límite, se considera a favor

                let [year, month, day] = this.herramental.fecha_limite.split('-').map(Number);
                let fechaLimite = new Date(year, month - 1, day, 23, 59, 59);
                // let fechaLimite = new Date(this.herramental.fecha_limite);
                console.log('limite:', fechaLimite)
                
                let fechaFinReal = null;
                this.tasks.forEach(task => {
                    task.time.forEach(segment => {
                        let fechaFin = new Date(segment.dia_termino);
                        console.log('fin:', fechaFin)
                        if (!fechaFinReal || fechaFin > fechaFinReal) {
                            fechaFinReal = fechaFin;
                        }
                    });
                });

                if (!fechaFinReal) return true;

                // Si la fecha real del proyecto es menor o igual al límite, está a favor
                return fechaFinReal <= fechaLimite;
            },
            posicionFinRealProyecto() {
                if (!this.tasks?.length || !this.duracionTotal?.length) return 0;

                // Buscar la fecha y hora más tardía
                let fechaHoraFinMax = null;

                this.tasks.forEach(task => {
                    task.time.forEach(segment => {
                        const fechaFin = new Date(segment.dia_termino);
                        if (!fechaHoraFinMax || fechaFin > fechaHoraFinMax) {
                            fechaHoraFinMax = fechaFin;
                        }
                    });
                });

                if (!fechaHoraFinMax) return 0;

                const fechaStr = fechaHoraFinMax.toLocaleDateString('sv-SE'); // 'YYYY-MM-DD'

                const indexDia = this.duracionTotal.findIndex(d => d === fechaStr);
                if (indexDia === -1) return 0;

                // Calcular la posición relativa dentro del día (en pixeles)
                const hora = fechaHoraFinMax.getHours();
                const minutos = fechaHoraFinMax.getMinutes();
                const porcentajeDia = (hora + minutos / 60) / 24; // 0 a 1
                const pixelPorDia = 80;

                const leftPx = 215 + (pixelPorDia * indexDia) + (pixelPorDia * porcentajeDia);

                return leftPx;
            },
            otrasFechasPosiciones() {
                if (!this.duracionTotal || !this.herramental?.otras_fechas) return [];

                return this.herramental.otras_fechas.map(fecha => {
                    const fechaDate = new Date(fecha);
                    for (let i = 0; i < this.duracionTotal.length; i++) {
                        const fechaActual = new Date(this.duracionTotal[i]);
                        if (fechaActual.toDateString() === fechaDate.toDateString()) {
                            return 215 + (80 * (i + 1)); // misma fórmula que usas arriba
                        }
                    }
                    return null; // si no se encontró
                }).filter(pos => pos !== null);
            },
            duracionTotal() {
                const fechasInicio = this.tasks.flatMap(task => 
                    task.time.map(t => t.dia_inicio.split(' ')[0]) // Solo la fecha en formato YYYY-MM-DD
                );

                const fechasFin = this.tasks.flatMap(task => 
                    task.time.map(t => t.dia_termino.split(' ')[0]) // Solo la fecha en formato YYYY-MM-DD
                );

                const minFecha = new Date(Math.min(...fechasInicio.map(fecha => new Date(fecha).getTime())));
                let maxFecha = new Date(Math.max(...fechasFin.map(fecha => new Date(fecha).getTime())));

                if (this.herramental?.fecha_limite) {
                    const fechaLimite = new Date(this.herramental.fecha_limite);
                    maxFecha = fechaLimite > maxFecha ? fechaLimite : maxFecha;
                    maxFecha.setDate(maxFecha.getDate() + 1);
                }

                const diasTotales = [];
                for (let d = new Date(minFecha); d <= maxFecha; d.setDate(d.getDate() + 1)) {
                    diasTotales.push(new Date(d).toISOString().split('T')[0]); // YYYY-MM-DD
                }

                return diasTotales;
            },
            duracionTotal2() {
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
                calcularMaxHora(this.tasks2);

                // Calcular para rutaAvance
                calcularMaxHora(this.rutaAvance);

                return maxHour;
            },
            totalHoras() {
                let totalHoras = 0;
                let totalMinutos = 0;
                let maxTime = { horas: 0, minutos: 0 };

                this.tasks2.forEach(task => {
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

                this.tasks2.forEach(task => {
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
            totalDias() {
                if (!this.duracionTotal || !this.herramental?.fecha_limite) return 0;

                const fechaLimite = new Date(this.herramental.fecha_limite);

                for (let i = 0; i < this.duracionTotal.length; i++) {
                    const fechaActual = new Date(this.duracionTotal[i]);

                    if (fechaActual.toDateString() === fechaLimite.toDateString()) {
                        return i + 1; // +1 si quieres contar días en forma humana (no índice)
                    }
                }
                return 0; // Si no se encontró la fecha
            }
        },
        methods:{
            async generarSolicitudRefaccion(){
                let t = this;
                
                let errores = [];
                if (!t.nuevaRefaccion.fecha_deseada_entrega) {
                    errores.push("Debe ingresar una fecha deseada de entrega.");
                }
                
                if (
                    t.nuevaRefaccion.cantidad === null ||
                    t.nuevaRefaccion.cantidad === undefined ||
                    isNaN(t.nuevaRefaccion.cantidad) ||
                    Number(t.nuevaRefaccion.cantidad) <= 0
                ) {
                    errores.push("La cantidad debe ser un número mayor a cero.");
                }
                
                if (errores.length > 0) {
                    swal("¡Lo sentimos!", errores.join("\n"), "info");
                    return;
                }

                t.loading_button = true;
                try {
                    const response = await axios.post('/api/solicitud-refaccion/' + t.nuevaRefaccion.componente_id, t.nuevaRefaccion);
                    if (response.data.success) {
                        await swal("Correcto", "Solicitud de refacción enviada correctamente, puede darle seguimiento desde el menu ORDEN DE TRABAJO", "success");
                        $('#modalSolicitarRefaccion').modal('hide');
                    } else {
                        await swal("Error", "No se pudo enviar la solicitud. Inténtelo de nuevo más tarde.", "error");
                    }
                } catch (error) {
                    console.error('Error al enviar solicitud de refacción:', error);
                    await swal("Error", "Ocurrió un error al enviar la solicitud. Inténtelo de nuevo más tarde.", "error");
                } finally {
                    t.loading_button = false;
                }
            },
            solicitarRefaccion(id){
                this.nuevaRefaccion = {
                    componente_id: id,
                    fecha_deseada_entrega: new Date().toISOString().split('T')[0],
                    solicitante_id: {{ auth()->user()->id }},
                    area_solicitud: 'Herramentales',
                    comentarios: '',
                    desde: 'refacciones',
                    cantidad: 1, 
                };
                $('#modalSolicitarRefaccion').modal();
            },
            verCotas(componente){
                this.componente = {...componente};
                $('#modalCotas').modal();
            },
            async agregarFechaLimite() {
                let t = this;

                try {
                    const value = await swal({
                        title: "¿Asignar una fecha límite para el herramental?",
                        text: "Debe seleccionar una fecha límite para continuar.",
                        icon: 'info',
                        content: {
                            element: "input",
                            attributes: {
                                type: "date",
                                id: "fechaLimite",
                                class: "swal-input",
                                required: true
                            }
                        },
                        buttons: {
                            cancel: "Cancelar",
                            confirm: {
                                text: "Asignar",
                                closeModal: false
                            }
                        }
                    });
                    
                    if(value == null)
                        return 

                    if (value == '') {
                        await swal("Error", "Debe ingresar una fecha límite.", "error");
                        return;
                    }
                    let fechaLimite = document.getElementById("fechaLimite").value;
                    
                    t.cargando = true;
                    await axios.put(`/api/herramental/${t.selectedHerramental}/fecha-limite`, { fechaLimite });
                    
                    await swal("Correcto", "Fecha límite establecida correctamente.", "success");
                    await this.fetchClientes(t.selectedAnio);
                    await this.fetchProyectos(t.selectedCliente);
                    await this.fetchHerramentales(t.selectedProyecto);
                    await this.fetchComponentes(t.selectedHerramental);

                } catch (error) {
                    await swal("Error", "Inténtelo de nuevo más tarde.", "error");
                } finally {
                    t.cargando = false;
                }
            },
            esMiCarpeta(nombreCarpeta) {
                let userId = {{ auth()->user()->id }};
                let roles = @json(auth()->user()->roles->pluck('name'));
                return nombreCarpeta.startsWith(userId + '.') || roles.some(role => ['DIRECCION', 'FINANZAS', 'HERRAMENTALES', 'JEFE DE AREA'].includes(role));
            },
            verFotografias(componente){
                this.componente = {...componente};
                $('#modalFotografias').modal();
            },
            verFotografiasEnsamble(){
                $('#modalFotografiasEnsamble').modal();
            },
            async fetchSolicitudes(id) {
                let t = this;

                this.cargando = true;
                try {
                    const response = await axios.get(`/api/solicitud/${id}`);
                    let solicitudes = response.data.solicitudes;

                    this.solicitudes = solicitudes.map(s => {
                        return {
                            fecha: s.fecha_show,
                            hora: s.hora_show,
                            tipo: s.tipo,
                            programa: s.fabricacion_id ? s.fabricacion.archivo_show : 'N/A',
                            comentarios: s.comentarios,
                            area_solicitante: s.area_solicitante,
                            usuario: s.usuario
                        };
                    });
                } catch (error) {
                    console.error('Error fetching solicitudes:', error);
                } finally {
                    this.cargando = false;
                    $('#modalSolicitudes').modal();
                }
            },
            async mostrarLineaDeTiempo(id) {
                let t = this;
                this.cargando = true;

                try {
                    const response = await axios.get(`/api/linea-tiempo/${id}`);
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
                            maquina: this.getMaquina(n.maquina_id),
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
                        descripcion = tipo === 1 ?
                            "<strong>SE INICIA PARO EN EL PROCESO DE CORTE</strong>" :
                            "<strong>SE FINALIZA PARO EN EL PROCESO DE CORTE</strong>";
                        break;

                    case "fabricacion_paro":
                        descripcion = tipo === 1 ?
                            "<strong>SE INICIA PARO EN EL PROCESO DE FABRICACION</strong>" :
                            "<strong>SE FINALIZA PARO EN EL PROCESO DE FABRICACION</strong>";
                        break;
                    case "corte":
                        descripcion = tipo === 1 ?
                            "<strong>INICIA EL PROCESO DE CORTE</strong>" :
                            "<strong>FINALIZA EL PROCESO DE CORTE</strong>";
                        break;
                    case "fabricacion":
                        descripcion = tipo === 1 ?
                            "<strong>INICIA EL PROCESO DE FABRICACION</strong>" :
                            "<strong>FINALIZA EL PROCESO DE FABRICACION</strong>";
                        break;
                    case "programacion":
                        descripcion = tipo === 1 ?
                            "<strong>INICIA PROGRAMACION </strong>" :
                            "<strong>FINALIZA PROGRAMACION </strong>";
                        break;
                    case "ensamble":
                        descripcion = tipo === 1 ?
                            "<strong>INICIA EL PROCESO DE ENSAMBLE </strong>" :
                            "<strong>FINALIZA EL PROCESO DE ENSAMBLE </strong>";
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
                if (tipo == 'seguimiento') {
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
                        case "ensamble":
                            area = 'ENSAMBLE'
                            break;
                        default:
                            area = "AREA DESCONOCIDA"; // Por si hay otras acciones no previstas
                    }
                } else {
                    let accion2 = JSON.parse(accion)
                    accion2.forEach(obj => {
                        area += obj + ' ,'
                    });
                    if (area.endsWith(' ,')) {
                        area = area.slice(0, -2); // Elimina la coma y el espacio al final
                    }
                }
                return area;
            },
            getMaquina(id) {
                let maquina = this.maquinas.find(m => m.id === id);
                return maquina ? maquina.nombre : '-';
            },
            determinarEstatus(componente) {
                const { cargado, enrutado, cortado, programado, ensamblado, fecha_terminado, esComponenteExterno, requiere_ensamble } = componente;

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
                    resultado = "En Fabricación";
                }
                if (!esComponenteExterno && fecha_terminado) {
                    resultado = "En Ensamble";
                }
                if (ensamblado) {
                    resultado = "Ensamblado";
                }
                if((esComponenteExterno || !requiere_ensamble) && fecha_terminado){
                    resultado = "Terminado";
                }
                return resultado;
            },   
            async fetchSolicitudesEnsamble(){
                let t = this;
                t.cargando = true;
                try {
                    let response = await axios.get(`/api/solicitud-ensamble/${t.selectedHerramental}`);
                    return response.data.solicitudes
                } catch (error) {
                    console.error('Error fetching solicitudes:', error);
                } finally {
                    this.cargando = false;
                }
            },      
            async verInformacion(task){
                let t = this;
                t.infoComponentes = task;

                if(t.infoComponentes.componente_id == -1){ //ensamble
                    t.solicitudesEnsamble = await t.fetchSolicitudesEnsamble();
                    Vue.nextTick(function(){
                        $('#modalEnsamble').modal('show');
                        return;
                    })
                }
                if(t.infoComponentes.componente_id == -2){ //pruebas diseño
                    t.prueba = t.pruebasDiseno.find(p => p.id == t.infoComponentes.prueba_id);
                    $('#modalPruebasDiseño').modal('show');
                    return;
                }
                if(t.infoComponentes.componente_id == -3){ // pruebas de procesos
                    t.prueba = t.pruebasProceso.find(p => p.id == t.infoComponentes.prueba_id);
                    $('#modalPruebasProceso').modal('show');
                    return;
                }
                if(t.infoComponentes.componente_id > 0){
                    t.componente = t.componentes.find(c => c.id == t.infoComponentes.componente_id);
                    $('#modalComponente').modal('show');
                    $('#modalComponente').on('hidden.bs.modal', function () {
                        t.componenteIdSeleccionado = null;
                    });
                }
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
                    this.pruebasProceso = response.data.pruebasProceso
                    this.pruebasDiseno = response.data.pruebasDiseno
                    this.herramental = response.data.herramental
                    this.componentes = response.data.componentes
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
            async fetchMaquinas() {
                try {
                    const response = await axios.get('/api/maquinas');
                    this.maquinas = response.data.maquinas;
                } catch (error) {
                    console.error('Error fetching maquinas:', error);
                }
            },
            async fetchComponente(id){
                this.cargando = true;
                try {
                    const response = await axios.get(`/api/componente/${id}`)
                    this.componente = response.data.componente

                } catch (error) {
                    console.error('Error fetching componente:', error);
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

                        if (componenteId) {
                            let task = this.tasks.find(t => 
                                t.time && Array.isArray(t.time) && t.time.some(time => time.info?.id == componenteId)
                            );
                           if (task) {
                                this.componenteIdSeleccionado = componenteId;
                                this.verInformacion(task);
                            }
                        }
                    }

                } catch (error) {
                    console.error("Error navigating from URL parameters:", error);
                }
            },
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
                    let tareaTeorica = tasks.find((t) => t.uuid === tareaAvance.uuid);

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
            calcularInicioAvance() {
                let t = this;
                let acumuladorHoras1 = 1; 
                let acumuladorMinutos1 = 0;
                let acumuladorHoras2 = 1; 
                let acumuladorMinutos2 = 0;

               
                const tareasFijas = this.rutaAvance.filter(task => task.id === 1 || task.id === 2);
                const otrasTareas = this.rutaAvance.filter(task => task.id !== 1 && task.id !== 2);
                tareasFijas.forEach(task => {
                    let proceso = t.procesos.find(p => p.uuid === task.uuid);
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
                    let proceso = t.procesos.find(p => p.uuid === task.uuid);
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
                    case 3:case 4:case 5:case 6:case 8:case 9:case 11:
                        let fabricaciones = this.componente.fabricaciones.filter(element => element.proceso_uuid === task.uuid)
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
            getTaskStyle2(segment, hour) {
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
                let t = this

                t.tasks2 = JSON.parse(JSON.stringify(t.componente.ruta));
                t.rutaAvance = JSON.parse(JSON.stringify(t.tasks2));
                t.rutaAvance.forEach(element => {
                    element.time = []
                    let find = t.componente.rutaAvance.find(obj => obj.uuid == element.uuid)
                    if(find){
                        element.time = find.time
                    }
                })
                
                t.procesos = [];
                t.tasks2.forEach((element) => {
                    t.procesos.push({
                        uuid: element.uuid,
                        id: element.id,
                        nombre: element.name,
                        horas: element.time[0]?.horas ?? 0,
                        minutos: element.time[0]?.minutos ?? 0,                        
                    })
                })

                Vue.nextTick(function(){
                    t.rutaAvance = t.ajustarRutaAvance(t.tasks2, t.rutaAvance);
                    t.calcularInicioAvance();
                    return true;
                })
            },
            async verModalRuta(id){
                let t = this
                await this.fetchComponente(id);
                await this.cargarRuta();
                
                $('#modalRuta').modal()
                Vue.nextTick(function(){
                    Vue.nextTick(function(){
                        $('[data-toggle="tooltip"]').tooltip('dispose');
                        $('[data-toggle="tooltip"]').tooltip()
                    })
                })
            },
        }                
    })

    </script>



        
@endpush