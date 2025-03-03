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
        width: 200px; /* Ajusta el valor según lo que necesites */
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
        grid-template-columns: 200px repeat(var(--columns, 200), 1fr); /* var(--columns) es una variable CSS */
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
        width: 200px;
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
        grid-template-columns: 200px repeat(var(--columns, 200), 1fr); /* var(--columns) es una variable CSS */
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
                        <template v-for="obj in proyectos">
                            @if(auth()->user()->hasRole('JEFE DE AREA'))
                            <a class="nav-link cursor-pointer"  @click="fetchHerramentales(obj.id)">
                                <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/carpetas.png') }}"></i> &nbsp;
                                <span class="underline-hover">@{{obj.nombre}}</span> 
                            </a>
                            @else
                             <a class="nav-link cursor-pointer"  @click="fetchHerramentales(obj.id)" v-if="esMiCarpeta(obj.nombre)">
                                <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/carpetas.png') }}"></i> &nbsp;
                                <span class="underline-hover">@{{obj.nombre}}</span> 
                            </a>
                            @endif
                        </template>
                    </div>
                    <div v-if="!cargandoMenu && menuStep == 4">
                        <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> HERRAMENTALES </a>
                        <a class="nav-link cursor-pointer" v-for="obj in herramentales" @click="fetchComponentes(obj.id)" >
                            <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/componente.png') }}"></i> &nbsp;
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
                        <div class="col-xl-12 accordion" id="accordionComponentes" v-if="infoComponentes.time && infoComponentes.time.length > 0">
                            <div class="card" v-for="(component, index) in infoComponentes.time.slice().reverse()" :key="component.version" style="border-radius: 20px !important">
                                <!-- Cabecera del acordeón -->
                                <div class="card-header cursor-pointer" :id="'heading-' + component.version" :class="{'bg-success text-white': component.type == 'normal', 'bg-danger text-white': component.type == 'rework'}" data-toggle="collapse"  :data-target="'#collapse-' + component.version"  :aria-expanded="index === 0"  :aria-controls="'collapse-' + component.version">
                                    <h5 class="bold"> Version @{{ component.version }} &nbsp;&nbsp;  <small>(@{{component.dia_inicio}} Hrs. - @{{component.dia_termino}} Hrs.)</small></h5>
                                </div>

                                <!-- Contenido colapsable -->
                                <div :id="'collapse-' + component.version" class="collapse" :class="{ show: index === 0 }" :aria-labelledby="'heading-' + component.version" data-parent="#accordionComponentes">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-xl-9" style="border-right: 1px solid #ededed">
                                                <div class="mb-2">
                                                    <span><strong>Tipo de componente: </strong> @{{component.info.es_compra ? 'COMPRA' : 'FABRICACIÓN'}}</span><br>
                                                    <span><strong>Cantidad: </strong> @{{component.info.cantidad}}</span>&nbsp;&nbsp;
                                                    <span><strong>Alto: </strong> @{{component.info.alto}}</span>&nbsp;&nbsp;
                                                    <span><strong>Largo: </strong> @{{component.info.largo}}</span>&nbsp;&nbsp;
                                                    <span><strong>Ancho: </strong> @{{component.info.ancho}}</span>&nbsp;&nbsp;
                                                    <span><strong>Material: </strong> @{{component.info.material_nombre}}</span>&nbsp;&nbsp;
                                                </div>
                                                <div class="mb-2" v-if="!component.info.es_compra">
                                                    <span><strong>Fecha de Carga:</strong> @{{ component.info.fecha_cargado }}</span> <br>
                                                    <span><strong>Fecha Términado:</strong> @{{ component.info.fecha_terminado ?? ' Sin finalizar' }}</span> <br>
                                                    <span><strong>Fecha Ensamblado:</strong> @{{ component.info.fecha_ensamblado ?? 'Sin ensamblar' }}</span> <br>
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
                                                        <a class="btn btn-block btn-sm btn-default" :href="'/download/' + component.info.archivo_2d_public">
                                                            <i class="fa fa-download"></i> Vista 2D
                                                        </a>
                                                    </div>
                                                    <div class="col">
                                                        <a class="btn btn-block btn-sm btn-default" :href="'/download/' + component.info.archivo_3d_public">
                                                            <i class="fa fa-download"></i> Vista 3D
                                                        </a>
                                                    </div>
                                                    <div class="col">
                                                        <a class="btn btn-block btn-sm btn-default" :href="'/download/' + component.info.archivo_explosionado_public">
                                                            <i class="fa fa-download"></i> Explosionado
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-3">
                                                <div class="text-center">
                                                    <button @click="fetchSolicitudes(component.info.id)" v-if="!component.info.es_compra" class="btn btn-block btn-dark mx-2"><i class="fa fa-list"></i> Ver solicitudes</button>
                                                    <button @click="mostrarLineaDeTiempo(component.info.id)" v-if="!component.info.es_compra" class="btn btn-block btn-dark mx-2"><i class="fa fa-calendar"></i> Ver linea de tiempo</button>
                                                    <button @click="verFotografias(component.info)" v-if="!component.info.es_compra" class="btn btn-block btn-dark mx-2"><i class="fa fa-camera"></i> Ver fotos</button>
                                                    <button @click="verModalRuta(component.info.id)" v-if="!component.info.es_compra" class="btn btn-block btn-dark mx-2"><i class="fa fa-eye"></i> Visor componente</button>
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
            <div class="modal-dialog" style="min-width: 50%;">
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
                        <div class="col-xl-12">
                            <div class="row">
                                <div class="col-xl-9" style="border-right: 1px solid #ededed">
                                    <div class="mb-2">
                                        <span><strong>Estatus de ensamble:</strong> </span> <span class="badge badge-dark badge-pill px-2 py-1 my-2">@{{herramental.estatus_ensamble.toUpperCase()}}</span> <br>
                                        <span><strong>Fecha de inicio ensamble: </strong> @{{herramental.inicio_ensamble ?? 'Sin iniciar'}}</span><br>
                                        <span><strong>Fecha de fin ensamble: </strong> @{{herramental.termino_ensamble ?? 'Sin terminar'}}</span><br>
                                    </div>
                                    <div class="mb-2 row">
                                        <div class="col-lg-6">
                                            <a class="btn btn-block btn-sm btn-default" :href="'/download/' + herramental.archivo2">
                                                <i class="fa fa-download"></i> FORMATO F71-03 ANEXO 1.1
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3">
                                    <div class="text-center">
                                        <button class="btn btn-block btn-dark mx-2" @click="verFotografiasEnsamble"><i class="fa fa-camera"></i> Ver fotos</button>
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
                        <div class="col-xl-12">
                            <div class="row">
                                <div class="col-xl-12 text-center">
                                    <div class="mb-2">
                                        <span><strong>Estatus de la prueba:</strong> <br> </span> 
                                            <span v-if="prueba.liberada == true" class="badge badge-success badge-pill px-2 py-1 my-2">LIBERADA</span>
                                            <span v-else class="badge badge-dark badge-pill px-2 py-1 my-2">NO LIBERADA</span>
                                         <br>
                                        <span><strong>Fecha de inicio: </strong> <br> @{{ prueba.fecha_inicio }}</span><br>
                                        <span><strong>Fecha de liberación: </strong> <br> @{{ prueba.fecha_liberada??'Sin liberar' }}</span><br>
                                        <span><strong>Involucrados en la prueba: </strong> <br> @{{ prueba.involucrados }}</span><br>
                                        <span><strong>Descripcion de la prueba: </strong> <br> @{{prueba.descripcion}}</span><br>
                                        <span><strong>Hallazgos: </strong> <br> @{{prueba.hallazgos}}</span><br>
                                        <span><strong>Plan de accion: </strong> <br> @{{prueba.plan_accion}}</span><br>
                                    </div>
                                    <div class="mb-2 row">
                                        <div class="col-lg-12 text-center">
                                            <a class="btn btn-sm btn-default" :href="'/download/pruebas-diseno/' + prueba.archivo_dimensional">
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
                        <div class="col-xl-12">
                            <div class="row">
                                <div class="col-xl-12 text-center" >
                                    <div class="mb-2">
                                        <span><strong>Estatus de la prueba:</strong> <br> </span> 
                                            <span v-if="prueba.liberada == true" class="badge badge-success badge-pill px-2 py-1 my-2">LIBERADA</span>
                                            <span v-else class="badge badge-dark badge-pill px-2 py-1 my-2">NO LIBERADA</span>
                                         <br>
                                        <span><strong>Fecha de inicio: </strong> <br> @{{ prueba.fecha_inicio }}</span><br>
                                        <span><strong>Fecha de liberación: </strong> <br> @{{ prueba.fecha_liberada??'Sin liberar' }}</span><br>
                                        <span><strong>Descripcion de la prueba: </strong> <br> @{{prueba.descripcion}}</span><br>
                                        <span><strong>Comentarios: </strong> <br> @{{prueba.comentarios}}</span><br>
                                        <span><strong>Plan de accion: </strong> <br> @{{prueba.plan_accion}}</span><br>
                                    </div>
                                    <div class="mb-2 row">
                                        <div class="col-lg-12 text-center">
                                            <a class="btn btn-sm btn-default" :href="'/download/pruebas-proceso/' + prueba.archivo">
                                                <i class="fa fa-download"></i> FORMATO F71-03 ANEXO 2
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-12 text-center">
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
                            <div class="col-xl-12 table-responsive table-stripped" style="max-height: 75vh !important; overflow-y: scroll !important">
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
                            <div class="col-xl-12 table-responsive table-stripped" style="height: 75vh !important; overflow-y: scroll !important">
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
                            Galería de Imágenes @{{ componente.nombre }}
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
                            <div class="col-xl-12 text-right">
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
                            Fotografias de ensamblado para @{{ herramental.nombre }}
                        </h3>
                        <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="container">
                            <div v-if="componentes.length">
                                <div class="row">
                                    <div class="col-md-4 mb-3" v-for="componente in componentes" :key="componente.id">
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
                            <div class="col-xl-12 text-right">
                                <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalRuta" tabindex="-1" aria-labelledby="modalRutaLabel" aria-hidden="true" >
            <div class="modal-dialog"  style="min-width: 60%;">
                <div class="modal-content" >
                    <div class="modal-header">
                        <h3 class="my-0 py-0 bold">RUTA PARA EL COMPONENTE @{{componente.nombre}}</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                         <div class="row d-flex align-items-center">
                            <div class="col-xl-12">
                                <div class="row">
                                    <div class="col-xl-12" style="overflow-x:scroll">
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
                                            <div class="gantt-row2" v-for="task in tasks2" :key="task.id" >
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
                                    <div class="col-xl-12" style="overflow-x:scroll">
                                        <div class="gantt-chart" :style="{ '--columns': duracionTotal2.length }" >
                                            <div class="gantt-header2 general-header2">
                                                <div class=" time-header2 pb-2" :colspan="duracionTotal2.length" style="letter-spacing: 1px" >TIEMPO REAL EN HORAS</div>
                                            </div>
                                            <div class="gantt-header2">
                                                <div class="gantt-cell2 task-name2 pt-1">ACCIONES</div>
                                                <div class="gantt-cell2 pt-1" v-for="hour in duracionTotal2" :key="hour">@{{ hour }}</div>
                                            </div>
                                            <div class="gantt-row2" v-for="task in rutaAvance" :key="task.id" >
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
        },
        mounted: async function () {
            let t = this;
            await t.fetchAnios();
            await t.fetchMaquinas();
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
            }
        },
        methods:{
             esMiCarpeta(nombreCarpeta) {
                let userId = {{auth()->user()->id}};
                return nombreCarpeta.startsWith(userId + '.');
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
                const { cargado, enrutado, cortado, programado, ensamblado, fecha_terminado, esComponenteExterno } = componente;

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
                if(esComponenteExterno && fecha_terminado){
                    resultado = "Terminado";
                }
                if (ensamblado) {
                    resultado = "Ensamblado";
                }
                return resultado;
            },         
            async verInformacion(task){
                let t = this;
                t.infoComponentes = task;

                if(t.infoComponentes.componente_id == -1){ //ensamble
                    $('#modalEnsamble').modal('show');
                    return;
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
                $('#modalComponente').modal('show');
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
                ];
    
                t.tasks2.forEach(task => {
                    let proceso = t.procesos.find(obj => obj.id === task.id);
                    if (proceso) {
                        proceso.horas = task.time[0]?.horas ?? 0;  
                        proceso.minutos = task.time[0]?.minutos ?? 0;
                        proceso.incluir = true;
                    }
                });

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