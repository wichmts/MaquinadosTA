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
        width: 100%;
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
        background-color: #000000 !important;
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

    .tipoParoSeleccionado {
        background-color: #d34040 !important;
        color: white !important;
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
                        @if (auth()->user()->hasRole('JEFE DE AREA'))
                        
                        <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> MAQUINAS ASIGNADAS</a>
                        <a v-for="obj in maquinas_todas" class="nav-link cursor-pointer" @click="fetchComponentes(obj.id)" v-if="esMiMaquina(obj.id)">
                            <i class="nc-icon"><img height="20px" src="{{ asset('paper/img/icons/maquina.png') }}"></i> &nbsp;
                            <span class="underline-hover">@{{obj.nombre}}</span> 
                        </a>
                        <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> OTRAS MAQUINAS</a>
                        <a v-for="obj in maquinas_todas" class="nav-link cursor-pointer" @click="fetchComponentes(obj.id)" v-if="!esMiMaquina(obj.id)">
                            <i class="nc-icon"><img height="20px" src="{{ asset('paper/img/icons/maquina.png') }}"></i> &nbsp;
                            <span class="underline-hover">@{{obj.nombre}}</span> 
                        </a>
                        @else
                        <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> MAQUINAS ASIGNADAS </a>
                        <a class="nav-link cursor-pointer" v-for="obj in maquinas" @click="fetchComponentes(obj.id)">
                            <i class="nc-icon"><img height="20px" src="{{ asset('paper/img/icons/maquina.png') }}"></i> &nbsp;
                            <span class="underline-hover">@{{obj.nombre}}</span> 
                        </a>
                        @endif


                    </div>    
                    <div v-if="!cargandoMenu && menuStep == 2">
                        <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> COMPONENTES EN COLA DE PRODUCCION</a>
                        <a class="nav-link cursor-pointer" v-for="obj in componentes" @click="fetchComponente(obj.id)">
                            <i class="nc-icon"><img height="20px" src="{{ asset('paper/img/icons/componentes.png') }}"></i> &nbsp;
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
                            <span class="cursor-pointer pb-2"  v-if="ruta.maquina" @click="regresar(2)"><i class="fa fa-angle-right"></i>   &nbsp; <span class="underline-hover">@{{ruta.maquina}}</span>      </span>
                            <span class="cursor-pointer pb-2 bold"  v-if="ruta.componente"><i class="fa fa-angle-right"></i>   &nbsp; <span class="underline-hover">@{{ruta.componente}}</span>      </span>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-12">
                        <h2 class="bold my-0 py-1 mb-3 text-decoration-underline" style="letter-spacing: 2px">VISOR DE OPERADOR</h2>
                    </div>
                </div>
                <hr>
                <div class="col-xl-12" v-if="!selectedComponente">
                    <h5 class="text-muted my-4"> SELECCIONE UN COMPONENTE PARA SU FABRICACION</h5>
                </div>
                <div class="row mt-3 pb-3" v-else>
                    <div class="col-xl-6 form-group mb-0">
                        <span style="font-size: 22px !important; border-color: #c0d340 !important; background-color: #c0d340 !important" class="badge badge-warning badge-pill bold my-4 py-2"> <i class="fa fa-cogs" style="font-size: 16px !important" ></i> @{{componente.nombre}}</span>
                    </div>
                    <div class="col-xl-2 form-group mb-0">
                        <label class="bold">CANTIDAD</label>
                        <input type="text" class="form-control text-center" v-model="componente.cantidad" placeholder="Cantidad" disabled>
                    </div>
                    <div class="col-xl-2 form-group mb-0 mt-2">
                        <a :href="'/storage/' + componente.archivo_2d_public" target="_blank" class="btn btn-block btn-default" @click="verModalRuta()"><i class="fa fa-file"></i> Ver 2D</a>
                    </div>
                    <div class="col-xl-2 form-group mb-0 mt-2">
                        <button class="btn btn-block btn-default" @click="verModalRuta()"><i class="fa fa-eye"></i> Ver ruta</button>
                    </div>
                    <div class="col-xl-4 form-group mb-0" style="height: 120px !important">
                        <label class="bold">DESCRIPCION DEL TRABAJO</label>
                        <textarea disabled v-model="componente.descripcion_trabajo" class="mt-0 form-control text-left px-1 py-1" style="min-height: 100% !important" placeholder="Descripcion del trabajo..."></textarea>
                    </div>
                    <div class="col-xl-4 form-group mb-0" style="height: 120px !important">
                        <label class="bold">HERRAMIENTAS DE CORTE</label>
                        <textarea disabled  v-model="componente.herramientas_corte" class="mt-0 form-control text-left px-1 py-1" style="min-height: 100% !important" placeholder="Agregar herramientas de corte..."></textarea>
                    </div>
                    <div class="col-xl-4">
                        <div class="row">
                            <div class="col-xl-12 form-group">
                                <label class="bold">SELECCIONAR PROGRAMA (TXT)</label>
                                <select class="form-control" v-model="programaSeleccionado" @change="seleccionarPrograma()">
                                    <option v-for="f in componente.fabricaciones" :value="f.id"> @{{f.archivo_show}}</option>
                                </select>
                            </div>
                            <div class="col-xl-12 mt-2 form-group">
                                <label class="bold">MAQUNA</label>
                                <input type="text" :value="ruta.maquina" class="form-control" disabled>
                            </div>
                        </div>
                    </div>
                </div>
                <Transition name="fade" mode="out-in">
                    <div class="row py-3" style="border-top: 2px dashed #d6d6d6; " v-if="fabricacion && selectedComponente" :key="fabricacion.id">
                       
                        <div class="col-xl-4 mb-3">
                            <span style="font-size: 14px !important; border-color: #c0d340 !important; background-color: #c0d340 !important" class="badge badge-warning badge-pill bold py-2"> <i class="fa fa-computer" style="font-size: 13px !important" ></i> PROGRAMA SELECCIONADO: @{{fabricacion.archivo_show}}</span>
                        </div>

                         <div class="col-xl-2 mb-3"  v-if="selectedComponente">
                            <button :disabled="fabricacion.estatus_fabricacion == 'paro' || fabricacion.estatus_fabricacion == 'proceso' || fabricacion.fabricado == true || !esMiMaquina(selectedMaquina)" class="btn btn-block btn-default mt-0" @click="cambiarEstatusFabricacion('proceso')"><i class="fa fa-play-circle"></i>    INICIAR FABRIC.</button>
                        </div>
                        <div class="col-xl-2 mb-3"  v-if="selectedComponente" style="border-right: 1px solid #efefef">
                            <button :disabled="fabricacion.estatus_fabricacion == 'paro' || fabricacion.estatus_fabricacion == 'detenido' || fabricacion.estatus_fabricacion == 'inicial' || fabricacion.fabricado == true || !esMiMaquina(selectedMaquina)" class="btn btn-block btn-default mt-0" @click="cambiarEstatusFabricacion('detenido')"><i class="fa fa-stop-circle"></i>    DETENER FABRIC.</button>
                        </div>

                        <div class="col-xl-2 mb-3"  v-if="selectedComponente">
                            <button :disabled="fabricacion.estatus_fabricacion == 'paro' || fabricacion.fabricado == true || !esMiMaquina(selectedMaquina)" class="btn btn-block mt-0"  @click="guardar(false)"><i class="fa fa-save"></i> GUARDAR </button>
                        </div>
                        <div class="col-xl-2 mb-3"  v-if="selectedComponente" >
                            <button class="btn btn-success btn-block mt-0" @click="liberar()" :disabled="fabricacion.estatus_fabricacion == 'paro' || fabricacion.fabricado == true || !esMiMaquina(selectedMaquina)">
                                <i class="fa fa-check-circle"></i> 
                                <span v-if="fabricacion.fabricado != true">FINALIZAR</span>
                                <span v-else>FINALIZADA</span>
                            </button>
                        </div>
                        <div class="col-xl-9">
                            <div class="row">
                                <div class="col-xl-5 form-group" style="height: 150px !important">
                                    <label class="bold">COMENTARIOS DE COMPONENTE TERMINADO</label>
                                    <textarea :disabled="fabricacion.estatus_fabricacion == 'paro' || fabricacion.fabricado == true || !esMiMaquina(selectedMaquina)" class="mt-0 form-control text-left px-1 py-1" style="min-height: 100% !important" placeholder="Comentarios..." v-model="fabricacion.comentarios_terminado"></textarea>
                                </div>
                                <div class="col-xl-5 form-group" style="height: 150px !important">
                                    <label class="bold">REGISTRO DE MEDIDAS</label>
                                    <textarea :disabled="fabricacion.estatus_fabricacion == 'paro' || fabricacion.fabricado == true || !esMiMaquina(selectedMaquina)" class="mt-0 form-control text-left px-1 py-1" style="min-height: 100% !important" placeholder="Registro de medidas..." v-model="fabricacion.registro_medidas"></textarea>
                                </div>
                                <div class="col-xl-2 form-group">
                                    <label class="bold">FOTO DEL COMPONENTE</label>
                                    <a target="_blank" v-if="fabricacion.foto" :href="'/storage/fabricaciones/' + fabricacion.foto">
                                        <img :src="'/storage/fabricaciones/' + fabricacion.foto" style="border-radius: 10px; width: 100%; height: auto; object-fit: cover" alt="">
                                    </a>
                                    <img v-else src="{{ asset('paper/img/no-image.png') }}" style="border-radius: 10px; width: 100%; height: 150px; object-fit: cover" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3">
                            <div class="row">
                                <div class="col-xl-12">
                                    <button :disabled="fabricacion.estatus_fabricacion == 'paro' || fabricacion.fabricado == true || !esMiMaquina(selectedMaquina)" class="btn btn-block mt-0" @click="abrirCamara()"><i class="fa fa-camera"></i> <span v-if="fabricacion.foto">RETOMAR FOTO</span><span v-else>TOMAR FOTO</span></button>
                                    <input type="file" id="fileInput" accept="image/*" capture="environment" style="display: none;" @change="procesarFoto($event)">
                                </div>
                                <div class="col-xl-12">
                                    <button :disabled="fabricacion.estatus_fabricacion == 'paro' || fabricacion.fabricado == true || !esMiMaquina(selectedMaquina)" @click="abrirSolicitud('modificacion')" class="btn btn-dark btn-block mt-0"><i class="fa fa-edit"></i> SOLICITAR MODIFICACION</button>
                                </div>
                                <div class="col-xl-12">
                                    <button :disabled="fabricacion.estatus_fabricacion == 'paro' || fabricacion.fabricado == true || !esMiMaquina(selectedMaquina)" @click="abrirSolicitud('retrabajo')" class="btn btn-dark btn-block mt-0"><i class="fa fa-retweet"></i> SOLICITAR RETRABAJO</button>
                                </div>
                                <div class="col-xl-12">
                                     <button v-if="fabricacion.estatus_fabricacion != 'paro'" @click="registrarParo()" :disabled="fabricacion.estatus_fabricacion == 'paro' || fabricacion.fabricado == true || !esMiMaquina(selectedMaquina)" class="mt-1 btn btn-danger btn-block"><i class="fa fa-stop-circle"></i> Iniciar paro</button>
                                    <button  v-else @click="eliminarParo()" :disabled="fabricacion.estatus_fabricacion == 'paro' || fabricacion.fabricado == true || !esMiMaquina(selectedMaquina)" class="mt-1 btn btn-danger btn-block"><i class="fa fa-play-circle"></i> Reanudar operacion</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </div>

        <div class="modal fade" id="modalParo" tabindex="-1" aria-labelledby="modalParoLabel" aria-hidden="true">
            <div class="modal-dialog" style="min-width: 30%;">
                <div class="modal-content" >
                    <div class="modal-header">
                        <h3 class="text-danger modal-title" id="modalParoLabel">
                            <span>INICIAR PARO EN LA FABRICACION DEL COMPONENTE @{{componente.nombre}}</span>
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
                        <h3 class="bold modal-title" id="modalRutaLabel" style="letter-spacing: 1px">RUTA PARA EL COMPONENTE @{{componente.nombre}}</h3>
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
        <div class="modal fade" id="modalRetraso" tabindex="-1" aria-labelledby="modalRetrasoLabel" aria-hidden="true">
            <div class="modal-dialog" style="min-width: 35%;">
                <div class="modal-content" >
                    <div class="modal-header">
                        <h3 class="modal-title" id="modalRetrasoLabel">
                            <span>RETRASO EN EL COMPONENTE @{{componente.nombre}}</span>
                        </h3>
                        <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row px-3">
                             <div class="mt-3 py-2 col-xl-12 form-group" style="background-color: rgb(254, 195, 195); border-radius: 10px">
                                <label class="bold text-danger"><i class="fa fa-exclamation-circle"></i> Hubo un retraso en el tiempo estimado de fabricacion para este componente. Indique el motivo.</label>
                                <textarea style="border: none !important" v-model="fabricacion.motivo_retraso" class="form-control w-100 text-left px-2 py-1" placeholder="Motivo del retraso..."></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-12"><hr></div>
                            <div class="col-xl-12 text-right">
                                <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                                <button class="btn btn-secondary" v-if="!loading_button" type="button" @click="guardar(true)"><i class="fa fa-check-circle"></i> LIBERAR COMPONENTE</button>
                                <button class="btn btn-secondary" type="button" disabled v-if="loading_button"><i class="fa fa-spinner spin"></i> LIBERANDO, ESPERE ...</button>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalSolicitud" tabindex="-1" aria-labelledby="modalSolicitudLabel" aria-hidden="true">
            <div class="modal-dialog" style="min-width: 40%;">
                <div class="modal-content" >
                    <div class="modal-header">
                        <h3 class="text-dark modal-title" id="modalSolicitudLabel">
                            <span>SOLICITAR @{{solicitud.tipo.toUpperCase()}} PARA EL COMPONENTE @{{componente.nombre}}<br> @{{solicitud.tipo == 'modificacion' ? ' PROGRAMA: ' + fabricacion.archivo_show : ''}}</span>
                        </h3>
                        <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="py-0 col-xl-12">
                                <label class="bold">Comentarios <span class="text-danger">*</span></label>
                               <textarea v-model="solicitud.comentarios" class="form-control w-100 text-left px-2 py-1" placeholder="Agregar comentarios..."></textarea>
                           </div>                           
                        </div>
                        <div class="row">
                            <div class="col-xl-12 text-right">
                                <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                                <button class="btn" v-if="!loading_button" type="button" @click="enviarSolicitud()"><i class="fa fa-paper-plane"></i> ENVIAR SOLICITUD</button>
                                <button class="btn" type="button" disabled v-if="loading_button"><i class="fa fa-spinner"></i> ENVIANDO, ESPERE ...</button>
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
            solicitud: {
                tipo: '',
                comentarios: '',
                area_solicitante: 'FABRICACION'
            },
            user_id: {{auth()->user()->id}},
            componente: {
                nombre: '',
                maquinas: []
            },
            loading_button: false,
            cargando: false,            
            anios: [],         
            clientes: [],      
            proyectos: [],     
            herramentales: [], 
            componentes: [],   
            maquinas: [],   
            maquinas_todas: [],   
            cargandoMenu: true,
            menuStep: 1, 
            selectedComponente: null,
            selectedMaquina: null,
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
            archivos: [],
            hay_retraso: false,
            programaSeleccionado: null,
            fabricacion: {motivo_retraso: ''},
            fotografia: null,
            paro: {
                comentarios_paro: '',
                tipo_paro: 'Daño a la Materia Prima'
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
        watch: {
           
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
            abrirSolicitud(tipo){
                this.solicitud = {
                    tipo: tipo,
                    comentarios: '',
                    fabricacion_id:  this.fabricacion.id,
                    area_solicitante: 'FABRICACION'
                }
                $('#modalSolicitud').modal();
            },
            async enviarSolicitud(){
                let t = this;
            
                if (!t.solicitud.comentarios.trim()) {
                    swal('Campos obligatorios', 'Es necesario ingresar comentarios para continuar.', 'info');
                    return;
                }
            
                t.loading_button = true;
                
                try {
                    const response = await axios.post(`/api/solicitud/${t.componente.id}`, t.solicitud);
                    if (response.data.success) {
                        swal('Solicitud enviada', 'La solicitud ha sido enviada exitosamente.', 'success');
                        $('#modalSolicitud').modal('hide');
                    }
                } catch (error) {
                    console.error('Error al enviar la solicitud:', error);
                    swal('Error', 'Hubo un problema al intentar enviar la solicitud.', 'error');
                } finally {
                    t.loading_button = false;
                }
            },
            esMiMaquina(id){
                return this.maquinas.some(obj => obj.id == id)
            },
            async eliminarParo() {
                let t = this;
                try {
                    const response = await axios.put(`/api/eliminar-paro/${t.fabricacion.id}/fabricacion_paro`);
                    if (response.data.success) {
                        await t.fetchComponentes(t.selectedMaquina);
                        await t.fetchComponente(t.componente.id);
                        t.seleccionarPrograma();
                        swal('Operación reanudada', 'La operación ha sido reanudada exitosamente.', 'success');
                    }
                } catch (error) {
                    console.error('Error al eliminar el paro:', error);
                    swal('Error', 'Hubo un problema al intentar reanudar la operación.', 'error');
                }
            },

            async registrarParoAPI() {
                let t = this;
                if (!t.paro.tipo_paro.trim()) {
                    swal('Campos obligatorios', 'Es necesario ingresar un comentario de paro para continuar.', 'info');
                    return;
                }
                t.loading_button = true;
                try {
                    const response = await axios.post(`/api/registrar-paro/${t.componente.id}`, t.paro);
                    if (response.data.success) {
                        await t.fetchComponentes(t.selectedMaquina);
                        await t.fetchComponente(t.componente.id);
                        t.seleccionarPrograma();
                        swal(
                            'Operación detenida',
                            'El paro de operación ha sido iniciado. No olvide registrar la reanudación una vez que se retomen las actividades.',
                            'success'
                        );
                        $('#modalParo').modal('hide');
                    }
                } catch (error) {
                    console.error('Error al registrar el paro:', error);
                    swal('Error', 'Hubo un problema al intentar registrar el paro.', 'error');
                } finally {
                    t.loading_button = false;
                }
            },
            registrarParo(){
                this.paro = {
                    componente_id: this.componente.id,
                    fabricacion_id: this.fabricacion.id,
                    comentarios_paro: '',
                    tipo_paro: 'Daño a la Materia Prima',
                    tipo: 'fabricacion_paro',
                }
                $('#modalParo').modal();
            },
            seleccionarPrograma(){
                let t = this
                t.fabricacion = t.componente.fabricaciones.find(obj => obj.id == t.programaSeleccionado)
                t.fotografia = t.componente.foto;
            },  
            abrirCamara() {
                const fileInput = document.getElementById('fileInput');
                fileInput.click();
            },
            procesarFoto(event) {
                let archivo = event.target.files[0]; 
                this.fotografia = archivo;

                if (archivo) {
                    const lector = new FileReader();
                    lector.onload = (e) => {
                        console.log('Foto capturada:', e.target.result);
                    };
                    lector.readAsDataURL(archivo); // Convierte la imagen a base64 (opcional)
                }
                this.guardar(false)
            },
            async cargarRuta(){
                let t = this

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
            async fetchComponente(id, programaSeleccionado = null){
                let t = this;
                this.selectedComponente = id;
                this.componente = this.componentes.find(obj => obj.id == id)
                this.ruta.componente = this.componente.nombre;
                
                this.programaSeleccionado = programaSeleccionado ? programaSeleccionado :  this.componente.fabricaciones.length > 0 ? this.componente.fabricaciones[0].id : null;
                if(this.programaSeleccionado)
                    this.seleccionarPrograma();

                this.cargando = true;      
                try {
                    const response = await axios.get(`/api/componente/${id}`)
                    t.tasks = JSON.parse(JSON.stringify(response.data.componente.ruta));
                    t.rutaAvance = JSON.parse(JSON.stringify(t.tasks));

                    t.rutaAvance.forEach(element => {
                        element.time = []
                        let find = response.data.componente.rutaAvance.find(obj => obj.id == element.id)
                        if(find){
                            element.time = find.time
                        }
                    })
                } catch (error) {
                    console.error('Error fetching componente:', error);
                } finally {
                    this.cargando = false;
                }
            },
            async cambiarEstatusFabricacion(band){
                let t = this
                try {
                    const response = await axios.put(`/api/fabricacion/cambio-estatus/${t.programaSeleccionado}`, {estatus: band});
                    if (response.data.success) {
                        t.fabricacion.estatus_fabricacion = band;
                    }
                } catch (error) {
                    console.error('Error al cambiar el estatus de programación:', error);
                }
            },
            async verModalRuta(){
                let t = this
                await this.fetchComponente(t.selectedComponente, t.programaSeleccionado);
                await this.cargarRuta();

                $('#modalRuta').modal()

                Vue.nextTick(function(){
                    Vue.nextTick(function(){
                        $('[data-toggle="tooltip"]').tooltip('dispose');
                        $('[data-toggle="tooltip"]').tooltip()
                    })
                })
            },
            getElipsis(str){
                 if (str && str.length > 45) {
                    return str.substring(0, 44) + '...';
                }
                return str;
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
                    case 3:case 4:case 5:case 6:case 7:case 8:
                        let fabricaciones = this.componente.fabricaciones.filter(element => element.proceso_id == task.id)
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
                            maquina: null,
                            componente: null,
                        } 
                        this.selectedMaquina = null;
                        this.selectedComponente = null;
                    break;
                    case 2:
                        this.ruta.componente = null;
                        this.selectedComponente = null;
                    break;
                }
                this.menuStep = step;
            },
            async fetchMaquinas() {
                this.cargandoMenu = true
                try {
                    const response = await axios.get(`/api/maquinas?operador={{auth()->user()->id}}`);
                    this.maquinas = response.data.maquinas;

                    if ({{ auth()->user()->hasRole('JEFE DE AREA') ? 'true' : 'false' }}) {
                        const responseTodas = await axios.get(`/api/maquinas`);
                        this.maquinas_todas = responseTodas.data.maquinas;
                    }

                } catch (error) {
                    console.error('Error fetching maquinas:', error);
                } finally {
                    this.cargandoMenu = false;
                }
            },
            async fetchComponentes(maquinaId) {
                this.cargando = true
                this.selectedMaquina = maquinaId;

                this.ruta.maquina = this.maquinas.find(obj => obj.id == maquinaId)?.nombre;
                try {
                    const response = await axios.get(`/api/maquinas/${maquinaId}/componentes`);
                    this.componentes = response.data.componentes;
                    this.menuStep = 2;
                } catch (error) {
                    console.error('Error fetching componentes:', error);
                } finally {
                    this.cargando = false;
                }
            },
            async liberar(){
                
                if (!this.fabricacion.comentarios_terminado?.trim() || !this.fabricacion.registro_medidas?.trim() || !this.fabricacion.foto?.trim()) {
                    swal('Errores de validación', `Todos los campos incluyendo la foto son obligatorios para finalizar esta fabricacion.`, 'error');
                    return;
                }
                this.hay_retraso = false;
                await this.fetchComponente(this.selectedComponente, this.programaSeleccionado);
                await this.cargarRuta();
                
                let proceso = this.rutaAvance.find(obj => obj.id === this.fabricacion.tipo_proceso)
                
                if(proceso){
                    let retraso = proceso.time.find(obj => obj.type === 'delay')
                    if(retraso){
                        this.hay_retraso = true;
                        $('#modalRetraso').modal();
                        return;
                    }
                }
                this.guardar(true);
            },
            async guardar(liberarComponente){                
                let t = this

                if(this.hay_retraso && liberarComponente && !this.fabricacion.motivo_retraso?.trim()){
                     swal('Campos obligatorios', 'Debe ingresar un motivo de retraso.', 'info');
                    return false;
                }
                t.cargando = true;
                t.loading_button = true;

                try {
                    const formData = new FormData();
                    formData.append('data', JSON.stringify(t.fabricacion));
                     if (t.fotografia) {
                        formData.append('fotografia', t.fotografia);
                    }
                    const response = await axios.post(`/api/componente/${t.programaSeleccionado}/fabricacion/${liberarComponente}`, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });

                    swal('Correcto', liberarComponente ? 'Fabricacion finalizada correctamente' : 'Información guardada correctamente', 'success');
                    await t.fetchComponentes(t.selectedMaquina);

                    if(liberarComponente){
                        if(t.componentes.some(obj => obj.id == t.selectedComponente)){
                            await t.fetchComponente(t.selectedComponente);
                            t.seleccionarPrograma();
                        }else{
                            t.menuStep = 1;
                            t.selectedComponente = null;
                            t.ruta.componente = null;
                        }
                    }else{
                        await t.fetchComponente(t.selectedComponente, t.programaSeleccionado);
                        t.seleccionarPrograma();
                    }
                    t.loading_button = false;
                    $('#modalRetraso').modal('hide');
                
                } catch (error) {
                    console.log(error);
                    const mensaje = error.response?.data?.error || 'Error al guardar el componente.';
                    swal('Error', mensaje, 'error');
                    t.cargando = false;
                    t.loading_button = false;
                }
            },
            async navigateFromUrlParams() {
                const queryParams = new URLSearchParams(window.location.search);
                const maquinaId = queryParams.get('maq');
                const componenteId = queryParams.get('co');
                const fabricacionId = queryParams.get('fab');

                try {
                    if (maquinaId) {
                        await this.fetchComponentes(maquinaId);
                    }
                    if (componenteId && componenteId != 'null' && this.componentes?.some(obj => obj.id == componenteId)) {
                        await this.fetchComponente(componenteId);
                    }
                    if(fabricacionId && fabricacionId != 'null' && this.componente?.fabricaciones?.some(obj => obj.id == fabricacionId)){
                        this.programaSeleccionado = fabricacionId;
                        this.seleccionarPrograma();
                    }
                } catch (error) {
                    console.error("Error navigating from URL parameters:", error);
                }
            },
        },
        mounted: async function () {
            let t = this;
            await t.fetchMaquinas();
            this.navigateFromUrlParams();        
        }

                
    })

    </script>



        
@endpush