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

    .componenteSeleccionado {
        background-color: #c0d340 !important;
        color: black !important;
    }

    /*  */

  .checkbox-wrapper-26 * {
    -webkit-tap-highlight-color: transparent;
    outline: none;
  }

  .checkbox-wrapper-26 input[type="checkbox"] {
    display: none;
  }

  .checkbox-wrapper-26 label {
    --size: 100px;
    --shadow: calc(var(--size) * .07) calc(var(--size) * .1);

    position: relative;
    display: block;
    width: var(--size);
    height: var(--size);
    margin: 0 auto;
    background-color: #f72414;
    border-radius: 50%;
    box-shadow: 0 var(--shadow) #ffbeb8;
    cursor: pointer;
    transition: 0.2s ease transform, 0.2s ease background-color,
      0.2s ease box-shadow;
    overflow: hidden;
    z-index: 1;
  }

  .checkbox-wrapper-26 label:before {
    content: "";
    position: absolute;
    top: 50%;
    right: 0;
    left: 0;
    width: calc(var(--size) * .7);
    height: calc(var(--size) * .7);
    margin: 0 auto;
    background-color: #fff;
    transform: translateY(-50%);
    border-radius: 50%;
    box-shadow: inset 0 var(--shadow) #ffbeb8;
    transition: 0.2s ease width, 0.2s ease height;
  }

  .checkbox-wrapper-26 label:hover:before {
    width: calc(var(--size) * .55);
    height: calc(var(--size) * .55);
    box-shadow: inset 0 var(--shadow) #ff9d96;
  }

  .checkbox-wrapper-26 label:active {
    transform: scale(0.9);
  }

  .checkbox-wrapper-26 .tick_mark {
    position: absolute;
    top: -1px;
    right: 0;
    left: calc(var(--size) * -.05);
    width: calc(var(--size) * .6);
    height: calc(var(--size) * .6);
    margin: 0 auto;
    margin-left: calc(var(--size) * .14);
    transform: rotateZ(-40deg);
  }

  .checkbox-wrapper-26 .tick_mark:before,
  .checkbox-wrapper-26 .tick_mark:after {
    content: "";
    position: absolute;
    background-color: #fff;
    border-radius: 2px;
    opacity: 0;
    transition: 0.2s ease transform, 0.2s ease opacity;
  }

  .checkbox-wrapper-26 .tick_mark:before {
    left: 0;
    bottom: 0;
    width: calc(var(--size) * .1);
    height: calc(var(--size) * .3);
    box-shadow: -2px 0 5px rgba(0, 0, 0, 0.23);
    transform: translateY(calc(var(--size) * -.68));
  }

  .checkbox-wrapper-26 .tick_mark:after {
    left: 0;
    bottom: 0;
    width: 100%;
    height: calc(var(--size) * .1);
    box-shadow: 0 3px 5px rgba(0, 0, 0, 0.23);
    transform: translateX(calc(var(--size) * .78));
  }

  .checkbox-wrapper-26 input[type="checkbox"]:checked + label {
    background-color: #07d410;
    box-shadow: 0 var(--shadow) #92ff97;
  }

  .checkbox-wrapper-26 input[type="checkbox"]:checked + label:before {
    width: 0;
    height: 0;
  }

  .checkbox-wrapper-26 input[type="checkbox"]:checked + label .tick_mark:before,
  .checkbox-wrapper-26 input[type="checkbox"]:checked + label .tick_mark:after {
    transform: translate(0);
    opacity: 1;
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

        <div class="wrapper" v-cloak v-show="!cargando">
            <div class="main-panel col-xl-12">
                <div class="row">
                    <div class="col-xl-2 pt-3" style="background-color: #f1f1f1; height: calc(100vh - 107.3px); overflow-y: scroll">
                        <div class="nav flex-column nav-pills " id="v-pills-tab" role="tablist" aria-orientation="vertical" >
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
                                <a class="nav-link cursor-pointer" v-for="obj in clientes" @click="fetchProyectos(obj.id)" v-if="obj.nombre != 'ORDENES EXTERNAS' && obj.nombre != 'REFACCIONES' && obj.nombre != 'ORDENES AFILADO' " >
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
                            <div class="col-lg-8">
                                <h2 class="bold my-0 py-1 mb-3 text-decoration-underline" style="letter-spacing: 2px">VISOR DE MATRICERO</h2>
                            </div>
                            <div class="col-lg-2" v-if="selectedComponente && herramental.estatus_ensamble != 'checklist' && herramental.estatus_ensamble != 'documento' && herramental.estatus_ensamble != 'finalizado'">
                                <button :disabled="herramental.estatus_ensamble == 'proceso'" class=" mt-1 btn btn-default btn-block" @click="cambiarEstatusHerramental(herramental.id, 'proceso')"><i class="far fa-play-circle"></i> Iniciar ensamble</button>
                            </div>
                            <div class="col-lg-2" v-if="selectedComponente && herramental.estatus_ensamble != 'checklist' && herramental.estatus_ensamble != 'documento' && herramental.estatus_ensamble != 'finalizado'">
                                <button :disabled="herramental.estatus_ensamble == 'inicial'" class=" mt-1 btn btn-default btn-block" @click="cambiarEstatusHerramental(herramental.id, 'inicial')"><i class="far fa-pause-circle"></i> Pausar ensamble</button>
                            </div>
                            {{-- <div class="col-lg-4" v-if="selectedHerramental && herramental && herramental.estatus_ensamble == 'finalizado'">
                                <button class="btn btn-success btn-block" @click="liberarHerramental()" ><i class="fa fa-check-double"></i> LIBERAR HERRAMENTAL </button>
                            </div> --}}
                        </div>
                        <hr>
                        <div class="col-xl-12" v-if="!selectedHerramental">
                            <h5 class="text-muted my-4"> SELECCIONE UN HERRAMENTAL PARA COMENZAR SU ENSAMBLE</h5>
                        </div>
                        <div class="row mt-3" v-else>
                            
                            {{-- Documento --}}
                            <div class="col-xl-12" v-show="herramental.estatus_ensamble == 'documento'">
                                <div class="row">
                                    <h5 class="bold col-xl-12" style="letter-spacing: 1px">Para liberar el herramental <strong>@{{herramental.nombre}}</strong>, es necesario completar y cargar el siguiente formato:</h5>
                                    <div class="col-xl-4">
                                        <input
                                            class="input-file"
                                            id="archivo2"
                                            type="file"
                                            @change="handleFileChange($event)"
                                            style="display: none;"
                                        />
                                        <label tabindex="0" for="archivo2" class="input-file-trigger col-12 text-center">
                                            <i class="fa fa-upload"></i> CARGAR FORMATO F71-03 ANEXO 1.1
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Checklist  --}}
                            <div class="col-lg-12" v-show="herramental.estatus_ensamble == 'checklist'">
                                <div class="row mb-3 pr-3">
                                    <div class="col-lg-9">
                                        <h3 class="bold pb-1 mb-0" style="letter-spacing: 1px">Lista de componentes para @{{herramental.nombre}}</h3>
                                        <h5 class="py-1 my-0" style="letter-spacing: 1px; opacity: .6" >Verifique todos los componentes esten ensamblados y si hubo componentes que no se utilizaron:</h5>
                                    </div>
                                    <div class="col-lg-3">
                                        <button class="btn btn-success btn-block" @click="guardarChecklist()"><i class="fa fa-save"></i> GUARDAR CAMBIOS</button>
                                    </div>
                                </div>
                                <div class="row" >
                                    <div class="col-lg-12 table-responsive" style="height: 55vh; overflow-y:scroll">
                                        <table class="table table-bordered">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th style="text-transform: none !important">¿Ensamblado?</td>
                                                    <th>Componente</td>
                                                    <th>Procedencia </td>
                                                    <th style="text-transform: none !important">Cantidad estimada</td>
                                                    <th style="text-transform: none !important">Unidades sobrantes</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(componente, index) in herramental.checklist" :key="index">
                                                    <td>@{{index + 1}}</td>
                                                    <td>
                                                        <div class="form-check" :key="index">
                                                            <label class="form-check-label">
                                                                <input class="form-check-input" type="checkbox" :id="'componente-' + index" v-model="componente.checked">
                                                                <span class="form-check-sign"></span>
                                                            </label>
                                                        </div>    
                                                    </td>
                                                    <td class="bold">@{{componente.nombre}}</td>
                                                    <td>@{{componente.es_compra ? 'COMPRAS' : 'FABRICACIÓN'}}</td>
                                                    <td>@{{componente.cantidad}}</td>
                                                    <td>
                                                        <input v-if="componente.es_compra" class="form-control text-center" style="border: none !important"  type="number" step="1" min="0" :max="componente.cantidad" v-model="componente.cantidad_sobrantes">
                                                        <span class="text-muted" v-else><small>No aplica en componentes de fabricación</small></span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{-- Ensamble --}}
                            <div class="col-xl-12" v-show="componentes.length > 0 && herramental.estatus_ensamble == 'inicial' || herramental.estatus_ensamble == 'proceso'">
                                <div class="row">
                                    <div class="col-xl-3" style="border-right: 2px dashed #d6d6d6;">  
                                         <h5 class="bold" style="letter-spacing: 1px">Seleccionar componente a ensamblar: </h5>
                                         <ul  class="dropdown-menu show w-100 position-static border mt-0" style="height: 65vh; overflow-y: scroll">
                                            <li v-for="c in componentes" class="dropdown-item" 
                                                :class="{ componenteSeleccionado: selectedComponente == c.id}" 
                                                @click.stop="fetchComponente(c.id)">
                                                <i class="fa fa-check-circle" v-if="selectedComponente == c.id"></i> 
                                                <span>@{{c.nombre}}</span> 
                                                <small>(@{{ c.ensamblado ? 'Ensamblado' : 'Sin ensamblar'}})</small>
                                            </li>
                                         </ul>
                                    </div>
                                    <div class="col-xl-9" >
                                        <div class="row">
                                            <div class="col-xl-9 form-group mt-4">
                                                <span style="font-size: 22px !important; border-color: #c0d340 !important; background-color: #c0d340 !important" class="badge badge-warning badge-pill bold mt-2 py-2"> <i class="fa fa-cogs" style="font-size: 16px !important" ></i> @{{componente.nombre}}</span>
                                            </div>
                                             <div class="col-xl-3 form-group text-center">
                                                <a v-if="componente.archivo_explosionado_public" class="text-dark cursor-pointer" :href="'/storage/' + componente.archivo_explosionado_public" target="_blank">
                                                    <img src="/paper/img/icons/file.png" height="80px"><br>
                                                    <label class="my-0 py-0 bold pt-2">VISTA EXPLOSIONADA</label>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xl-7">
                                                <div class="row">
                                                    <div class="col-xl-6 text-center form-group" >
                                                        <label class="bold ">¿COMPONENTE ENSAMBLADO?</label>
                                                        <div class="checkbox-wrapper-26 mt-4">
                                                            <input :disabled="herramental.estatus_ensamble == 'inicial'" type="checkbox" :id="'_checkbox-26' + componente.id" v-model="componente.ensamblado" @change="cambiarEstatusComponente()" :key="'checkbox-' + componente.id">
                                                            <label :for="'_checkbox-26' + componente.id">
                                                                <div class="tick_mark"></div>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 text-center form-group">
                                                        <label class="bold">FOTO DEL COMPONENTE</label>
                                                        <a target="_blank" v-if="componente.foto_matricero" :href="'/storage/fotos_matricero/' + componente.foto_matricero">
                                                            <img :src="'/storage/fotos_matricero/' + componente.foto_matricero" style="border-radius: 10px; width: 100%; height: auto; object-fit: cover" alt="">
                                                        </a>
                                                        <img v-else src="{{ asset('paper/img/no-image.png') }}" style="border-radius: 10px; width: 100%; height: 180px; object-fit: cover" alt="">
                                                        <button :disabled="componente.ensamblado == true || herramental.estatus_ensamble == 'inicial'" class="btn btn-block mb-0" @click="abrirCamara()"><i class="fa fa-camera"></i> <span v-if="componente.foto_matricero">RETOMAR FOTO</span><span v-else>TOMAR FOTO</span></button>
                                                        <input type="file" id="fileInput" accept="image/*" capture="environment" style="display: none;" @change="procesarFoto($event)">
                                                    </div>
                                                    <div class="col-xl-12" style="padding-bottom: 16px;" v-if="selectedComponente">                                       
                                                        <label class="bold">COMENTARIOS DEL ENRUTADOR</label>
                                                        <textarea 
                                                            disabled  
                                                            v-model="componente.comentarios" 
                                                            class="mt-0 form-control text-left px-1 py-1"                                     
                                                            placeholder="Sin comentarios...">
                                                        </textarea>
                                                    </div>                     
                                                    <div class="col-xl-6">
                                                        <button :disabled="componente.ensamblado == true || componente.es_compra == true || herramental.estatus_ensamble == 'inicial'" @click="abrirSolicitud('ajuste')"  class="btn btn-block btn-dark"><i class="fa fa-exclamation-circle"></i> SOLICITAR AJUSTE</button>
                                                    </div>
                                                    <div class="col-xl-6">
                                                        <button :disabled="componente.ensamblado == true || herramental.estatus_ensamble == 'inicial'" @click="abrirSolicitud('rechazo')"  class="btn btn-block btn-danger"><i class="fa fa-times-circle"></i> RECHAZAR COMPONENTE</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-5">
                                                <div class="row" style="height: 40vh">
                                                    <div class="col-xl-12">
                                                        <label class="bold">
                                                            <a v-if="componente.archivo_2d_public" :href="'/storage/' + componente.archivo_2d_public" target="_blank" class="text-dark text-decoration-none">VISTA PLANO</a>
                                                        </label>
                                                    </div>
                                                    <div class="col-xl-12 h-100" >
                                                        <object 
                                                            v-if="componente.archivo_2d_public"
                                                            :data="'/storage/' + componente.archivo_2d_public"
                                                            type="application/pdf"
                                                            style="width: 100%; height: 100%;">
                                                            <p>Tu navegador no soporta visualización de PDF. <a :href="'/storage/' + componente.archivo_2d_public">Descargar</a> en su lugar.</p>
                                                        </object>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-12 my-5 text-center" v-show="componentes.length == 0">
                                <small class="text-muted" style="letter-spacing: 1px">ESTE HERRAMENTAL AUN NO TIENE COMPONENTES PARA ENSAMBLE.</small>
                            </div>

                        </div>
                    </div>
                </div>


                {{-- MODAL SOLICITUD --}}
                <div class="modal fade" id="modalSolicitud" tabindex="-1" aria-labelledby="modalSolicitudLabel" aria-hidden="true">
                    <div class="modal-dialog" style="min-width: 40%;">
                        <div class="modal-content" >
                            <div class="modal-header">
                                <h3 class="text-dark modal-title" id="modalSolicitudLabel">
                                    <span>@{{solicitud.tipo == 'ajuste' ? 'SOLICITAR AJUSTE PARA EL COMPONENTE ' + componente.nombre : 'RECHAZAR EL COMPONENTE ' + componente.nombre}}</span>
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
                                        <button class="btn" :class="{'btn-danger' : solicitud.tipo == 'rechazo'}" v-if="!loading_button" type="button" @click="enviarSolicitud()"><i class="fa fa-paper-plane"></i> @{{solicitud.tipo == 'rechazo' ? 'RECHAZAR COMPONENTE' : 'SOLICITAR AJUSTE'}}</button>
                                        <button class="btn" :class="{'btn-danger' : solicitud.tipo == 'rechazo'}" type="button" disabled v-if="loading_button"><i class="fa fa-spinner"></i> ENVIANDO, ESPERE ...</button>
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


    <script type="text/javascript">
        Vue.component('v-select', VueSelect.VueSelect)

        var app = new Vue({
        el: '#vue-app',
        data: {
            solicitud: {motivo: '', tipo: 'ajuste'},
            user_id: {{auth()->user()->id}},
            componente: {},
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
            hay_retraso: false,
            herramental: {},
            solicitud: {
                tipo: '',
                comentarios: '',
                area_solicitante: 'ENSAMBLE'
            },
        },
        methods:{
            cambiarEstatusHerramental(herramentalId, nuevoEstatus){
                let t = this
                axios.put(`api/ensamble/cambio-estatus/${herramentalId}/${t.componente.id}`, {estatus: nuevoEstatus} ).then(response => {
                    if(response.data.success){
                        t.herramental.estatus_ensamble = nuevoEstatus;
                    }
                })
            },
            async guardarChecklist(){
                let t = this;
                
                let invalid = t.herramental.checklist.some(element => {
                    if (!element.es_compra) return false;

                    let sobrantes = parseInt(element.cantidad_sobrantes);
                    let estimados = parseInt(element.cantidad);

                    return (
                        element.cantidad_sobrantes === '' ||
                        isNaN(sobrantes) ||
                        sobrantes < 0 ||
                        sobrantes > estimados
                    );
                });
                if (invalid) {
                    swal(
                        'Verifica la información',
                        'Los campos de sobrantes no pueden estar vacíos, negativos o exceder los estimados.',
                        'error'
                    );
                    return;
                }
                
                this.loading_button = true;
                try {
                    const response = await axios.post(`/api/herramental/${this.selectedHerramental}/checklist`, t.herramental.checklist);
                    swal('Correcto', 'Checklist guardado correctamente', 'success');
                    await this.fetchHerramental(this.selectedHerramental);
                    this.fetchComponentes(this.selectedHerramental);

                } catch (error) {
                    console.error('Error guardando checklist:', error);
                    swal('Error', 'Error al guardar el checklist', 'error');
                } finally {
                    this.loading_button = false;
                }
            },
            // async liberarHerramental(){
            //     let t = this;
            
            //     let unassembledComponents = this.componentes.filter(c => !c.ensamblado);
            //     if (unassembledComponents.length > 0) {
            //         swal('Error', 'No se puede liberar el herramental. Todos los componentes deben estar ensamblados.', 'error');
            //         return;
            //     }

            //     swal({
            //         title: "¿Está seguro?",
            //         text: "¿Desea liberar el herramental para comenzar con las pruebas?",
            //         icon: "info",
            //         buttons: ['Cancelar', 'Si, liberar'],
            //         dangerMode: false,
            //     }).then((willChange) => {
            //         if (willChange) {
            //             t.liberar();
            //         }
            //     });
            // },
            // async liberar(){
            //     let t = this;
            //     axios.put(`/api/liberar-herramental-ensamble/${t.selectedHerramental}`)
            //     .then(response => {
            //         if(response.data.success){
            //             swal('Herramental liberado', 'El herramental ha sido liberado exitosamente.', 'success');
            //             t.selectedHerramental = null;
            //             t.fetchHerramentales(t.selectedProyecto);
            //         }
            //     })
            //     .catch(error => {
            //         console.error('Error liberando herramental:', error);
            //         swal('Error', 'Hubo un problema al intentar liberar el herramental.', 'error');
            //     });
            // },
            abrirSolicitud(tipo){
                this.solicitud = {
                    tipo: tipo,
                    comentarios: '',
                    area_solicitante: 'ENSAMBLE'
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
            abrirCamara() {
                const fileInput = document.getElementById('fileInput');
                fileInput.click();
            },
            async procesarFoto(event) {
                let archivo = event.target.files[0]; 
                this.fotografia = archivo;

                if (archivo) {
                    const lector = new FileReader();
                    lector.onload = (e) => {
                        console.log('Foto capturada:', e.target.result);
                    };
                    lector.readAsDataURL(archivo);
                }
                await this.guardarFoto()
            },
            async guardarFoto(){
                let t = this;
                this.loading_button = true;
                try {
                    const formData = new FormData();
                    formData.append('foto', this.fotografia);
                    const response = await axios.post(`/api/herramental/${this.selectedComponente}/foto`, formData);
                    if(response.data.success === false){
                        swal('No se pudo guardar la foto', response.data.message, 'error');
                    }
                    await this.fetchComponente(this.selectedComponente);
                } catch (error) {
                    console.error('Error guardando foto:', error);
                    swal('Error', 'Error al guardar la foto', 'error');
                } finally {
                    this.loading_button = false;
                }
            },
            async cambiarEstatusComponente(){
                let t = this;
                
                if(this.componente.es_compra && !this.componente.fecha_real){
                    swal('Acción no disponible', 'No se puede ensamblar un componente que aún se encuentra en proceso de compra.', 'warning');
                    this.$nextTick(() => {
                        this.$set(this.componente, 'ensamblado', false);
                    });
                    return;
                }
                if(!this.componente.es_compra && !this.componente.fecha_terminado){
                    swal('Acción no disponible', 'No se puede ensamblar un componente que aún esta en fabricación.', 'warning');
                    this.$nextTick(() => {
                        this.$set(this.componente, 'ensamblado', false);
                    });
                    return;
                }
                if (!this.componente.foto_matricero) {
                    swal('Acción no disponible', 'Debe cargar una foto del componente antes de cambiar el estado de componente.', 'warning');
                    this.$nextTick(() => {
                        this.$set(this.componente, 'ensamblado', false);
                    });
                    return;
                }

                swal({
                    title: "¿Está seguro?",
                    text: "¿Desea cambiar el estado del componente?",
                    icon: "warning",
                    buttons: ['Cancelar', 'Si, cambiar estado'],
                    dangerMode: true,
                }).then((willChange) => {
                    if (willChange) {
                        t.cambiarEstado();
                    } else {
                        this.$nextTick(() => {
                            this.$set(t.componente, 'ensamblado', !t.componente.ensamblado);
                        });
                    }
                });
            },
            async cambiarEstado(){
                let t = this;
                try {
                    const response = await axios.post(`/api/herramental/${this.selectedComponente}/estatus-ensamblado`, { ensamblado: t.componente.ensamblado });
                    await this.fetchComponentes(this.selectedHerramental);
                    await this.fetchComponente(this.selectedComponente);
                } catch (error) {
                    console.error('Error cambiando estatus:', error);
                    swal('Error', 'Error al cambiar el estatus', 'error');
                    this.$nextTick(() => {
                        this.$set(t.componente, 'ensamblado', !t.componente.ensamblado);
                    });
                }
            },
            async handleFileChange(event) {
                const file = event.target.files[0];
                if (!file) return;

                const formData = new FormData();
                formData.append('archivo', file);
                try {
                    const response = await axios.post(`/api/herramental/${this.selectedHerramental}/formato`, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });
                    if(response.data.success === false){
                        swal('Error al cargar el formato', response.data.message, 'error');
                    }else{
                        swal('Herramental liberado', 'El herramental se liberó correctamente.', 'success').then(() => {
                            window.location.href = '/matricero';
                        });
                    }
                } catch (error) {
                    console.error('Error uploading file:', error);
                    swal('Error', 'Error al cargar el archivo', 'error');
                }
            },
            async fetchComponente(id){
                let t = this;
                this.cargando = true;                
                this.selectedComponente = id;
                try {
                    const response = await axios.get(`/api/componente/${id}`)
                    this.componente = response.data.componente;
                    this.ruta.componente = this.componente.nombre;
                } catch (error) {
                    console.error('Error fetching componente:', error);
                } finally {
                    this.cargando = false;
                }
            },
            async fetchHerramental(id){
                this.cargando = true;                
                try {
                    const response = await axios.get(`/api/herramental/${id}`)
                    this.herramental = response.data.herramental;
                } catch (error) {
                    console.error('Error fetching herramental:', error);
                } finally {
                    this.cargando = false;
                }
            },
            eliminarArchivo(maquina, index) {
                maquina.archivos.splice(index, 1);
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
                try {
                    const response = await axios.get('/api/anios');
                    this.anios = response.data.anios;
                } catch (error) {
                    console.error('Error fetching años:', error);
                } finally {
                    this.cargandoMenu = false;
                    
                }
            },
            async fetchMaquinas() {
                this.cargando = true
                try {
                    const response = await axios.get('/api/maquinas');
                    this.maquinas = response.data.maquinas;
                } catch (error) {
                    console.error('Error fetching maquinas:', error);
                } finally {
                    this.cargando = false;
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
                    const response = await axios.get(`/api/proyectos/${proyectoId}/herramentales?area=ensamble`);
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
                this.selectedComponente = null;
                this.ruta.componente = null;

                
                await this.fetchHerramental(herramentalId);
                // this.herramental = this.herramentales.find(obj => obj.id == herramentalId);
                this.ruta.herramental = this.herramental?.nombre;

                if(this.herramental.estatus_ensamble == 'documento'){
                    Vue.nextTick(function() {
                        document.querySelector("html").classList.add('js');
                        let fileInput = document.querySelector(".input-file");
                        let button = document.querySelector(".input-file-trigger");

                        button.addEventListener("keydown", function(event) {
                            if (event.keyCode == 13 || event.keyCode == 32) {
                                fileInput.focus();
                            }
                        });

                        button.addEventListener("click", function(event) {
                            fileInput.focus();
                            return false;
                        });
                    });
                }
                
                try {
                    const response = await axios.get(`/api/herramentales/${herramentalId}/componentes?area=ensamble`);
                    this.componentes = response.data.componentes;
                    let checklist = this.componentes.map(obj => {
                        return {
                            id: obj.id,
                            cantidad: obj.cantidad,
                            nombre: obj.nombre,
                            es_compra: obj.es_compra,
                            checked: obj.matricero_id?false:true,
                            cantidad_sobrantes: 0,
                        };
                    });

                    // seleccionar el primer componente al cargar la vista de ensamble:
                    if((this.herramental.estatus_ensamble == 'inicial' || this.herramental.estatus_ensamble == 'proceso') && this.componentes.length > 0){
                        this.fetchComponente(this.componentes[0].id);
                    }

                    if (this.herramental.checklist && this.herramental.checklist.length > 0) {
                        checklist = checklist.map(item => {
                            const existingItem = this.herramental.checklist.find(obj => obj.id === item.id);
                            return {
                                ...item,
                                checked: existingItem ? existingItem.checked : false
                            };
                        });
                    }
                    this.herramental.checklist = checklist;

                } catch (error) {
                    console.error('Error fetching componentes:', error);
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
            this.navigateFromUrlParams();        
        }       
    })

    </script>



        
@endpush