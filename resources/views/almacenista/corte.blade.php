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
                    <div class="col-xl-9">
                        <h2 class="bold my-0 py-1 mb-3 text-decoration-underline" style="letter-spacing: 2px">CORTE DE MP</h2>
                    </div>
                    <div class="col-xl-3 form-group" v-if="selectedHerramental">
                        <select class="form-control" @change="fetchComponentes(selectedHerramental)" v-model="estatusCorte">
                            <option value="-1">TODOS LOS COMPONENTES</option>
                            <option value="inicial">POR CORTAR</option>
                            <option value="proceso">EN PROCESO</option>
                            <option value="pausado">PAUSADO</option>
                            <option value="finalizado">FINALIZADO</option>
                            <option value="paro">EN PARO</option>
                        </select>
                    </div>
                </div>
                <div class="col-xl-12" v-if="!selectedHerramental">
                    <h5 class="text-muted my-4"> SELECCIONE UN HERRAMENTAL PARA VER LOS CORTES A REALIZAR</h5>
                </div>
                <div class="row" v-else>
                    <div class="col-xl-12 " v-if="componentes.length == 0">
                        <h5 class="text-muted">Este herramental aun no tiene componentes cargados...</h5>
                    </div>
                    <div class="col-xl-12" style="overflow-x: auto !important;" v-if="componentes.length > 0">
                        <table class="table table-sm" id="tabla-principal">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 10%"> Componente </th>
                                    <th style="width: 7%"> Cantidad </th>
                                    <th style="width: 6%"> Largo </th>
                                    <th style="width: 6%"> Ancho </th>
                                    <th style="width: 5%"> Alto </th>
                                    <th style="width: 10%"> Material </th>
                                    <th style="width: 10%"> Estatus </th>
                                    <th style="width: 23%"> Corte </th>
                                    <th style="width: 22%"> Acciones </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="c in componentes">
                                    <td class="bold">
                                        @{{c.nombre}} <br>
                                        <span v-if="c.cancelado" class="badge badge-danger">CANCELADO</span>
                                    </td>
                                    <td><input readonly class="form-control text-center" type="number" step="1" v-model="c.cantidad"></td>
                                    <td><input readonly class="form-control text-center" type="text"  v-model="c.largo"></td>
                                    <td><input readonly class="form-control text-center" type="text"  v-model="c.ancho"></td>
                                    <td><input readonly class="form-control text-center" type="text"  v-model="c.alto"></td>
                                    <td><input readonly class="form-control text-center" type="text"  v-model="c.material_nombre"></td>
                                    <td>
                                        <span v-if="c.estatus_corte == 'paro'" class="py-2 w-100 badge badge-danger" style="font-size: 13px">EN PARO</span>
                                        <span v-if="c.estatus_corte == 'inicial'" class="py-2 w-100 badge badge-warning" style="font-size: 13px">POR CORTAR</span>
                                        <span v-if="c.estatus_corte == 'proceso'" class="py-2 w-100 badge badge-info" style="font-size: 13px">EN PROCESO...</span>
                                        <span v-if="c.estatus_corte == 'pausado'" class="py-2 w-100 badge badge-dark" style="font-size: 13px">PAUSADO</span>
                                        <span v-if="c.estatus_corte == 'finalizado'" class="py-2 w-100 badge badge-success" style="font-size: 13px">FINALIZADO</span>
                                    </td>
                                    <td>
                                        <button :disabled="c.estatus_corte == 'paro' || c.estatus_corte == 'finalizado' || c.estatus_corte == 'proceso'" class=" mt-1 btn btn-default btn-sm" @click="cambiarEstatusCorte(c.id, 'proceso')"><i class="far fa-play-circle"></i> Iniciar</button>
                                        <button :disabled="c.estatus_corte == 'paro' || c.estatus_corte == 'inicial' || c.estatus_corte == 'finalizado' || c.estatus_corte == 'pausado'" class=" mt-1 btn btn-default btn-sm" @click="cambiarEstatusCorte(c.id, 'pausado')"><i class="far fa-pause-circle"></i> Pausar</button>
                                        <button :disabled="c.estatus_corte == 'paro' || c.estatus_corte == 'inicial' || c.estatus_corte == 'finalizado' " class=" mt-1 btn btn-default btn-sm" @click="finalizarCorte(c.id)"><i class="far fa-check-circle"></i> Finalizar</button>
                                    </td>
                                    <td>
                                        <button @click="verModalRuta(c.id)" class="mt-1 btn btn-default btn-sm"><i class="fa fa-eye"></i> Ver ruta </button>
                                        <button v-if="c.estatus_corte != 'paro'" @click="registrarParo(c.id)" :disabled="c.estatus_corte == 'finalizado'" class="mt-1 btn btn-danger btn-sm"><i class="fa fa-stop-circle"></i> Iniciar paro</button>
                                        <button  v-else @click="eliminarParo(c.id)" :disabled="c.estatus_corte == 'finalizado'" class="mt-1 btn btn-danger btn-sm"><i class="fa fa-play-circle"></i> Reanudar operacion</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalFinalizarCorte" tabindex="-1" aria-labelledby="modalFinalizarCorteLabel" aria-hidden="true">
            <div class="modal-dialog" style="min-width: 35%;">
                <div class="modal-content" >
                    <div class="modal-header">
                        <h3 class="modal-title" id="modalFinalizarCorteLabel">
                            <span>FINALIZAR CORTE @{{componente.nombre}}</span>
                        </h3>
                        <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xl-12 form-group">
                                <label class="bold" >Seleccione MP</label>
                                <select class="form-control" v-model="movimiento.material_id" @change="fetchHojas(movimiento.material_id)">
                                    <option v-for="m in materiales" :value="m.id"> @{{m.nombre}} </option>
                                </select>
                            </div>
                            <div class="col-xl-12 form-group">
                                <label class="bold" >Seleccione hoja</label>
                                <select class="form-control" v-model="movimiento.hoja_id">
                                    <option :value="null" disabled>Seleccione la hoja de donde realizo el corte...</option>
                                    <option v-for="h in hojas" :value="h.id">Consecutivo @{{h.consecutivo}}.- Espesor: @{{h.espesor}}, Actuales: @{{h.ancho_saldo}} x @{{h.largo_saldo}}, Peso: @{{h.peso_saldo}} </option>
                                </select>
                            </div>
                            <div class="col-xl-4 form-group">
                                <label class="bold" >Largo restante de la hoja</label>
                                <input type="text" class="form-control" v-model="movimiento.largo">
                            </div>
                            <div class="col-xl-4 form-group">
                                <label class="bold" >Ancho restante de la hoja</label>
                                <input type="text" class="form-control" v-model="movimiento.ancho">
                            </div>
                            <div class="col-xl-4 form-group">
                                <label class="bold" >Peso restante de la hoja</label>
                                <input type="text" class="form-control" v-model="movimiento.peso">
                            </div>
                        </div>
                        <div class="row px-3" v-if="hay_retraso">
                             <div class="mt-3 py-2 col-xl-12 form-group" style="background-color: rgb(254, 195, 195); border-radius: 10px">
                                <label class="bold text-danger"><i class="fa fa-exclamation-circle"></i> Hubo un retraso en el tiempo estimado de corte para este componente. Indique el motivo.</label>
                                <textarea style="border: none !important" v-model="movimiento.motivo_retraso" class="form-control w-100 text-left px-2 py-1" placeholder="Motivo del retraso..."></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-12"><hr></div>
                            <div class="col-xl-12 text-right">
                                <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                                <button class="btn btn-secondary" v-if="!loading_button" type="button" @click="finalizarCorteAPI()"><i class="fa fa-check-circle"></i> FINALIZAR CORTE</button>
                                <button class="btn btn-secondary" type="button" disabled v-if="loading_button"><i class="fa fa-spinner spin"></i> FINALIZANDO, ESPERE ...</button>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>

         <div class="modal fade" id="modalParo" tabindex="-1" aria-labelledby="modalParoLabel" aria-hidden="true">
            <div class="modal-dialog" style="min-width: 30%;">
                <div class="modal-content" >
                    <div class="modal-header">
                        <h3 class="text-danger modal-title" id="modalParoLabel">
                            <span>INICIO DE PARO EN EL COMPONENTE @{{componente.nombre}}</span>
                        </h3>
                        <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xl-12 form-group">
                                <label class="bold">Seleccionar motivo <span style="color: red">*</span></label>
                                <ul style="height: 300px !important; overflow-y: scroll" class="dropdown-menu show w-100 position-static border mt-0">
                                    <li v-for="p in paros" class="dropdown-item" :class="{ tipoParoSeleccionado: paro.tipo_paro == p}" @click="paro.tipo_paro = p"><i class="fa fa-check-circle" v-if="paro.tipo_paro == p"></i> @{{p}}</li>
                                </ul>
                            </div>
                            <div class="py-0 col-xl-12">
                                <label class="bold">Comentarios de paro</label>
                               <textarea v-model="paro.comentarios_paro" class="form-control w-100 text-left px-2 py-1" placeholder="Comentarios de paro..."></textarea>
                           </div>                                   
                        </div>
                        <div class="row">
                            <div class="col-xl-12 text-right">
                                <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                                <button class="btn btn-danger" v-if="!loading_button" type="button" @click="registrarParoAPI()"><i class="fa fa-stop-circle"></i> INICAR PARO</button>
                                <button class="btn btn-danger" type="button" disabled v-if="loading_button"><i class="fa fa-spinner spin"></i> PROCESANDO, ESPERE ...</button>
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
                                <div class="row mt-3">
                                    <div class="col-xl-12" style="overflow-x:scroll">
                                        <div class="gantt-chart" :style="{ '--columns': duracionTotal.length }" >
                                            <div class="gantt-header general-header">
                                                <div class=" time-header pb-2" :colspan="duracionTotal.length" style="letter-spacing: 1px" >TIEMPO REAL EN HORAS</div>
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

    <script type="text/javascript">
        Vue.component('v-select', VueSelect.VueSelect)

        var app = new Vue({
        el: '#vue-app',
        data: {
            loading_button: false,
            cargando: false,
            componente: {nombre: ''},
            materiales: [],
            hojas: [],
            
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
            estatusCorte: '-1',
            rutaAvance: [],
            tasks: [],
            procesos: [],
            hay_retraso: false,
            paro: {
                comentarios_paro: '',
                tipo_paro: 'Daño a la Materia Prima',
            },
            paros: [
                "Daño a la Materia Prima",
                "Error de Compensación",
                "Cambio del Componente a producir",
                "Cambio de Programa",
                "Falta Herramienta de Corte",
                "Falla en Herramienta",
                "Origen Incorrecto",
                "Falta de Material",
                "Falta de Lubricante",
                "Mantenimiento",
                "Falla Mecánica",
                "Falla del Programa",
                "CFE",
                "Protección Civil",
                "Cambio de Insertos",
                "Cambio de Herramienta",
                "Error de Programación",    
            ]
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
            eliminarParo(id){
                let t = this;
                 axios.put(`/api/eliminar-paro/${id}/corte_paro`).then(response => {
                    if(response.data.success){
                        t.fetchComponentes(t.selectedHerramental);
                        swal('Operación reanudada', 'La operación ha sido reanudada exitosamente.', 'success');
                    }
                })
            },  
            registrarParoAPI(){
                let t = this
                if(!t.paro.tipo_paro.trim()){
                    swal('Campos obligatorios', 'Es necesario ingresar un motivo de paro para continuar.', 'info');
                    return
                }
                t.loading_button = true;
                axios.post(`/api/registrar-paro/${t.componente.id}`, t.paro).then(response => {
                    if(response.data.success){
                        t.fetchComponentes(t.selectedHerramental);
                        swal('Operación detenida', 'El paro de operación ha sido iniciado. No olvide registrar la reanudación una vez que se retomen las actividades.', 'success');
                        $('#modalParo').modal('hide');
                    }
                    t.loading_button = false;
                })
            },
            registrarParo(id){
                this.componente = this.componentes.find(obj => obj.id == id);
                this.paro = {
                    componente_id: id,
                    comentarios_paro: '',
                    tipo_paro: 'Daño a la Materia Prima',
                    tipo: 'corte_paro',
                }
                $('#modalParo').modal();
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
                let t = this

                t.tasks = JSON.parse(JSON.stringify(t.componente.ruta));
                t.rutaAvance = JSON.parse(JSON.stringify(t.tasks));
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
    
                t.tasks.forEach(task => {
                    let proceso = t.procesos.find(obj => obj.id === task.id);
                    if (proceso) {
                        proceso.horas = task.time[0]?.horas ?? 0;  
                        proceso.minutos = task.time[0]?.minutos ?? 0;
                        proceso.incluir = true;
                    }
                });

                Vue.nextTick(function(){
                    t.rutaAvance = t.ajustarRutaAvance(t.tasks, t.rutaAvance);
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
            cambiarEstatusCorte(id, nuevoEstatus){
                let t = this
                axios.put(`api/corte/cambio-estatus/${id}`, {estatus: nuevoEstatus} ).then(response => {
                    if(response.data.success){
                        t.fetchComponentes(t.selectedHerramental)    
                    }
                })
            },
            finalizarCorteAPI(){
                let t = this
                
                if (!t.movimiento.hoja_id) {
                    swal('Campos obligatorios', 'El campo hoja no puede estar vacío.', 'info');
                    return false;
                }
                 if (this.hay_retraso && !t.movimiento.motivo_retraso.trim()) {
                    swal('Campos obligatorios', 'Debe ingresar un motivo de retraso.', 'info');
                    return false;
                }
                t.cargando = true;
                axios.put(`api/corte/finalizar/${t.componente.id}`, {movimiento: t.movimiento} ).then(response => {
                    if(response.data.success){
                        t.fetchComponentes(t.selectedHerramental)    
                        $('#modalFinalizarCorte').modal('toggle');
                        t.cargando = false;
                    }
                })
            },
            async finalizarCorte(id){
                let t = this

                await this.fetchComponente(id);                
                await this.fetchHojas(t.componente.material_id)
                await this.cargarRuta();

                this.hay_retraso = false;
                let cortar = this.rutaAvance.find(obj => obj.id === 1)
                if(cortar){
                    let retraso = cortar.time.find(obj => obj.type === 'delay')
                    if(retraso){
                        this.hay_retraso = true;
                    }
                }

                t.movimiento = {
                    material_id: t.componente.material_id, 
                    hoja_id: null, 
                    largo: 0,
                    ancho: 0,
                    peso: 0,
                    motivo_retraso: '',
                }
                $('#modalFinalizarCorte').modal()
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
                    const response = await axios.get(`/api/herramentales/${herramentalId}/componentes?area=corte&estatusCorte=${this.estatusCorte}`);
                    this.componentes = response.data.componentes;

                } catch (error) {
                    console.error('Error fetching componentes:', error);
                } finally {
                    this.cargando = false;
                }
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
            }

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