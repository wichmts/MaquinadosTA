@extends('layouts.app', [
'class' => '',
'elementActive' => 'dashboard'
])

@section('styles')
<link rel="stylesheet" href="{{ asset('paper/css/paper-dashboard-responsivo.css') }}?v={{ time() }}">
@endsection


@section('content')
<div id="vue-app">

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

    <div class="wrapper " v-cloak v-show="!cargando">
        <div class="sidebar" data-color="white" data-active-color="danger">
            <div class="sidebar-wrapper">
                <ul class="nav">
                    <li>
                        <div class="nav flex-column nav-pills " id="v-pills-tab" role="tablist" aria-orientation="vertical">
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
                                <a class="d-flex align-items-center nav-link cursor-pointer" v-for="obj in proyectos" @click="fetchHerramentales(obj.id)">
                                    <i class="nc-icon" style="top: -3px !important"><img height="17px" src="{{ asset('paper/img/icons/carpetas.png') }}"></i> &nbsp;
                                    <span class="underline-hover">@{{obj.nombre}}</span>
                                </a>
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
                                    <span class="underline-hover" :style=" obj.cancelado == true ? 'text-decoration: line-through' : ''">
                                        @{{obj.nombre}} 
                                    </span>&nbsp;&nbsp;
                                    <small 
                                        v-if="tienePendientes(obj)" 
                                        :key="'componente' + obj.id" 
                                        class="cursor-info text-danger fa fa-info-circle" 
                                        data-toggle="tooltip" 
                                        data-placement="bottom" 
                                        :title="getContenidoTooltipComponente(obj)" >
                                    </small>
                                    <small 
                                        v-if="!tienePendientes(obj) && obj.enrutado == true" 
                                        :key="'componente-listo-' + obj.id" 
                                        class="cursor-info text-success fa fa-check-circle" 
                                        data-toggle="tooltip" 
                                        data-placement="bottom" 
                                        title="Componente liberado" >
                                    </small>
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
                <div class="row mb-2">
                    <div class="col-xl-6 col-lg-4">
                        <h2 class="bold my-0 py-1 mb-3 text-decoration-underline" style="letter-spacing: 2px"> ENRUTADOR</h2>
                    </div>
                    <div class="col-xl-3 col-lg-4 text-right" v-if="selectedComponente && componente.cancelado != true">
                        <button class="btn btn-block" :disabled="componente.enrutado == true" @click="guardar(false)"><i class="fa fa-save"></i> GUARDAR</button>
                    </div>
                    <div class="col-xl-3 col-lg-4 text-right" v-if="selectedComponente && componente.cancelado != true">
                        <button class="btn btn-success btn-block" :disabled="componente.enrutado == true" @click="guardar(true)"><i class="fa fa-check-double"></i>
                             @{{componente.enrutado == true ? 'LIBERADO' : 'LIBERAR'}}
                        </button>
                    </div>
                    <div class="col-xl-6" v-if="selectedComponente && componente.cancelado == true">
                        <button class="btn btn-block btn-danger" disabled><i class="fa fa-exclamation-circle"></i> ESTE COMPONENTE HA SIDO CANCELADO</button>
                    </div>
                </div>

                <div class="col-12" v-if="!selectedComponente">
                    <h5 class="text-muted my-4"> SELECCIONE UN COMPONENTE PARA VER SU ENRUTAMIENTO</h5>
                </div>
                <div v-else>
                    <div class="row mb-3">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" v-for="c in componente.refabricaciones">
                                <a :class="{active: c.version == componente.version}" @click="fetchComponente(c.id)" class="bold nav-link cursor-pointer">@{{componente.nombre}}.v@{{c.version}}</a>
                            </li>
                        </ul>
                    </div>
                    <div class="row">
                        <div class="col-xl-7">
                            <div class="row">
                                <div class="col-lg-2 px-2 my-3 d-flex justify-content-center align-items-center">
                                    <span style="font-size: 18px !important; border-color: #c0d340 !important; background-color: #c0d340 !important" class="d-flex justify-content-center w-100 badge badge-warning badge-pill bold"> <i class="fa fa-cogs" style="font-size: 16px !important"></i> @{{componente.nombre}}</span>
                                </div>
                                <div class="col-lg-3 d-flex justify-content-around">
                                    <div class="p-1">
                                        <a class="text-dark" :href="'/storage/' + componente.archivo_2d_public" target="_blank">
                                            <h5 class="my-0 py-0 bold">2D</h5>
                                            <img src="/paper/img/icons/file.png" width="100%">
                                        </a>
                                    </div>
                                    <div class="p-1">
                                        <a class="text-dark" :href="'/storage/' + componente.archivo_3d_public" target="_blank">
                                            <h5 class="my-0 py-0 bold">3D</h5>
                                            <img src="/paper/img/icons/file.png" width="100%">
                                        </a>
                                    </div>
                                    <div class="p-1">
                                        <a class="text-dark" :href="'/storage/' + componente.archivo_explosionado_public" target="_blank">
                                            <h5 class="my-0 py-0 bold">EXPL.</h5>
                                            <img src="/paper/img/icons/file.png" width="100%">
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-7 d-flex justify-content-around">
                                    <div class="form-group px-1">
                                        <label class="bold">Cantidad</label>
                                        <input type="number" step="any" class="form-control text-center" readonly :value="componente.cantidad">
                                    </div>
                                    <div class="form-group px-1">
                                        <label class="bold">Prioridad</label>
                                        <select class="form-control" v-model="componente.prioridad" :disabled="componente.enrutado == true || componente.cancelado == true">
                                            <option :value="null" disabled>Asignar...</option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="C">C</option>
                                        </select>
                                    </div>
                                    <div class="form-group px-1">
                                        <label class="bold">Programador</label>
                                        <select class="form-control" v-model="componente.programador_id" :disabled="componente.enrutado == true || componente.cancelado == true">
                                            <option :value="null" disabled>Seleccionar programador...</option>
                                            <option v-for="p in programadores" :value="p.id">@{{p.nombre_completo}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col" style="overflow-x:scroll">
                                    <div class="gantt-chart" :style="{ '--columns': duracionTotal.length }">
                                        <div class="gantt-header general-header">
                                            <div class=" time-header pb-2" :colspan="duracionTotal.length" style="letter-spacing: 1px">TIEMPO TEÓRICO EN HORAS</div>
                                        </div>
                                        <div class="gantt-header">
                                            <div class="gantt-cell task-name pt-1">ACCIONES</div>
                                            <div class="gantt-cell pt-1" v-for="hour in duracionTotal" :key="hour">
                                                @{{ hour }}
                                            </div>
                                        </div>
                                        <div class="gantt-row" v-for="task in tasks" :key="task.id">
                                            <div class="gantt-cell task-name pt-1">@{{ task.name }}</div>
                                            <div class="gantt-cell gantt-bar" v-for="hour in duracionTotal" :key="hour">
                                                <div
                                                    v-for="segment in task.time"
                                                    :data-tooltip="getContenidoTooltip(task)"
                                                    :key="segment.inicio"
                                                    v-if="isTaskInHour(segment, hour)"
                                                    :class="segment.type === 'normal' ? 'normal-task' : segment.type === 'rework' ? 'rework-task' : 'delay-task'"
                                                    :style="getTaskStyle(segment, hour)"
                                                    class="gantt-bar-segment"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-3">
                                <div class="col" style="overflow-x:scroll">
                                    <div class="gantt-chart" :style="{ '--columns': duracionTotal.length }">
                                        <div class="gantt-header general-header">
                                            <div class=" time-header pb-2" :colspan="duracionTotal.length" style="letter-spacing: 1px">TIEMPO REAL EN HORAS</div>
                                        </div>
                                        <div class="gantt-header">
                                            <div class="gantt-cell task-name pt-1">ACCIONES</div>
                                            <div class="gantt-cell pt-1" v-for="hour in duracionTotal" :key="hour">@{{ hour }}</div>
                                        </div>
                                        <div class="gantt-row" v-for="task in rutaAvance" :key="task.id">
                                            <div class="gantt-cell task-name pt-1">@{{ task.name }}</div>
                                            <div class="gantt-cell gantt-bar" v-for="hour in duracionTotal" :key="hour">
                                                <div
                                                    v-for="segment in task.time"
                                                    :data-tooltip="getContenidoTooltip(task)"
                                                    :key="segment.inicio"
                                                    v-if="isTaskInHour(segment, hour)"
                                                    class="gantt-bar-segment"
                                                    :class="segment.type === 'normal' ? 'normal-task' : segment.type === 'rework' ? 'rework-task' : 'delay-task'"
                                                    :style="getTaskStyle(segment, hour)">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="limite-tiempo" :style="{ left: `${165 + (40 * totalHoras) + ((40 / 60 ) * totalMinutos) }px !important` }"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-5">
                            <div class="row">
                                <div class="col-12 table-responsive">
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
                                            <tr v-for="p in procesos" :key="p.id" v-if="p.id > 0 && p.id < 6">
                                                <td class="py-1">
                                                    <div class="form-group">
                                                        <div class="form-check">
                                                            <label class="form-check-label" style="font-size: 10px">
                                                                <input type="checkbox" class="form-check-input" @change="toggleTask(p)" v-model="p.incluir" :disabled="componente.enrutado == true || componente.cancelado == true">
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
                                                                    <button :disabled="componente.enrutado == true || componente.cancelado == true" class="input-group-text py-0 cursor-pointer" style="background-color: #e3e3e3 !important" @click="p.horas > 0 ? p.horas-- : p.horas"> <i class="fa fa-minus"></i> &nbsp;&nbsp;</button>
                                                                </div>
                                                                <input type="number" v-model="p.horas" class="form-control text-center px-1 py-1" step="1" @change="calcularInicio()">
                                                                <div class="input-group-append">
                                                                    <button :disabled="componente.enrutado == true || componente.cancelado == true" class="input-group-text py-0 cursor-pointer" style="background-color: #e3e3e3 !important" @click="p.horas++"> &nbsp;&nbsp;<i class="fa fa-plus"></i> </button>
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
                                                                    <button :disabled="componente.enrutado == true || componente.cancelado == true" class="input-group-text py-0 cursor-pointer" style="background-color: #e3e3e3 !important" @click="p.minutos > 0 ? p.minutos-- : p.minutos"> <i class="fa fa-minus"></i> &nbsp;&nbsp;</button>
                                                                </div>
                                                                <input type="number" v-model="p.minutos" class="form-control text-center px-1 py-1" step="1" @change="calcularInicio()">
                                                                <div class="input-group-append">
                                                                    <button :disabled="componente.enrutado == true || componente.cancelado == true" class="input-group-text py-0 cursor-pointer" style="background-color: #e3e3e3 !important" @click="p.minutos < 60 ? p.minutos++ : p.minutos "> &nbsp;&nbsp;<i class="fa fa-plus"></i> </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="form-group">
                                                        <div class="form-check">
                                                            <label class="form-check-label" style="font-size: 10px">
                                                                <input type="checkbox" class="form-check-input" v-model="componente.requiere_temple" :disabled="componente.enrutado == true || componente.cancelado == true">
                                                                <span class="form-check-sign"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td> Temple </td>
                                                <td> - </td>
                                                <td> - </td>
                                            </tr>
                                            <tr v-for="p in procesos" :key="p.id" v-if="p.id > 6 && p.id < 10">
                                                <td class="py-1">
                                                    <div class="form-group">
                                                        <div class="form-check">
                                                            <label class="form-check-label" style="font-size: 10px">
                                                                <input type="checkbox" class="form-check-input" @change="toggleTask(p)" v-model="p.incluir" :disabled="componente.enrutado == true || componente.cancelado == true">
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
                                                                    <button :disabled="componente.enrutado == true || componente.cancelado == true" class="input-group-text py-0 cursor-pointer" style="background-color: #e3e3e3 !important" @click="p.horas > 0 ? p.horas-- : p.horas"> <i class="fa fa-minus"></i> &nbsp;&nbsp;</button>
                                                                </div>
                                                                <input type="number" v-model="p.horas" class="form-control text-center px-1 py-1" step="1" @change="calcularInicio()">
                                                                <div class="input-group-append">
                                                                    <button :disabled="componente.enrutado == true || componente.cancelado == true" class="input-group-text py-0 cursor-pointer" style="background-color: #e3e3e3 !important" @click="p.horas++"> &nbsp;&nbsp;<i class="fa fa-plus"></i> </button>
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
                                                                    <button :disabled="componente.enrutado == true || componente.cancelado == true" class="input-group-text py-0 cursor-pointer" style="background-color: #e3e3e3 !important" @click="p.minutos > 0 ? p.minutos-- : p.minutos"> <i class="fa fa-minus"></i> &nbsp;&nbsp;</button>
                                                                </div>
                                                                <input type="number" v-model="p.minutos" class="form-control text-center px-1 py-1" step="1" @change="calcularInicio()">
                                                                <div class="input-group-append">
                                                                    <button :disabled="componente.enrutado == true || componente.cancelado == true" class="input-group-text py-0 cursor-pointer" style="background-color: #e3e3e3 !important" @click="p.minutos < 60 ? p.minutos++ : p.minutos "> &nbsp;&nbsp;<i class="fa fa-plus"></i> </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-12 text-center mt-4">
                                    <h5 class="badge badge-dark badge-pill px-3 py-2 w-100" style="background-color: #c0d340 !important; color: black !important"> Tiempo estimado: @{{totalHoras}} horas y @{{totalMinutos}} minutos. </h5>
                                </div>
                            </div>
                            <div class="row" v-if="!componente.refabricado">
                                <div class="col-lg-6 py-0 px-1">
                                    <button :disabled="!componente.enrutado || componente.cancelado" class="px-1 btn btn-block btn-dark my-1" @click="abrirModalSolicitud('retrabajo')"><i class="fa fa-retweet"></i> RETRABAJO</button>
                                </div>
                                <div class="col-lg-6 py-0 px-1">
                                    <button :disabled="!componente.enrutado || componente.cancelado" class="px-1 btn btn-block btn-dark my-1" @click="abrirModalSolicitud('modificacion')"><i class="fa fa-edit"></i> MODIFICACIÓN</button>
                                </div>
                                <div class="col-lg-6 py-0 px-1">
                                    <button :disabled="!componente.enrutado || componente.cancelado" class="px-1 btn btn-block btn-dark my-1" @click="generarRefabricacion()"><i class="fa fa-recycle"></i> REFABRICACIÓN</button>
                                </div>
                                <div class="col-lg-6 py-0 px-1">
                                    <button :disabled="componente.cancelado"  v-if="componente.refaccion == true" @click="esRefaccion(false)" class="px-1 btn btn-block btn-success my-1"><i class="fa fa-puzzle-piece"></i> REFACCIÓN - Si</button>
                                    <button :disabled="componente.cancelado" v-else @click="esRefaccion(true)" class="px-1 btn btn-block btn-dark my-1"><i class="fa fa-puzzle-piece"></i> REFACCIÓN - No</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-12 px-1" v-if="componente && componente.esComponenteExterno">
                                    <button @click="verSolicitudExterna(componente.id)" class="btn btn-block my-0 btn-default"><i class="fa fa-info-circle"></i> VER ORDEN DE TRABAJO </button>
                                </div>
                                <div class="col-lg-6 px-1">
                                    <button @click="mostrarLineaDeTiempo" class="btn btn-block btn-default"><i class="fa fa-calendar"></i> LINEA DEL TIEMPO </button>
                                </div>
                                <div class="col-lg-6 px-1">
                                    <button @click="fetchSolicitudes" class="btn btn-block btn-default"><i class="fa fa-clipboard-list"></i> SOLICITUDES</button>
                                </div>
                            </div>
                        </div>
                    </div>
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
                    <div class="row">
                        <div class="col-xl-12 text-right">
                            <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalSolicitudes" tabindex="-1" aria-labelledby="modalSolicitudesLabel" aria-hidden="true">
        <div class="modal-dialog" style="min-width: 80%;">
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
                                        <th>¿Atendida?</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="solicitudes.length == 0">
                                        <td colspan="7"> No hay ninguna solicitud de retrabajo, modificacion o ajuste pendiente para este componente </td>
                                    </tr>
                                    <tr v-for="(l, index) in solicitudes" :key="'linea- ' + index + '-' + l.created_at">
                                        <td>@{{ l.fecha }}</td>
                                        <td>@{{ l.hora }}</td>
                                        <td class="bold">@{{ l.tipo.toUpperCase() }}</td>
                                        <td>@{{ l.programa }}</td>
                                        <td><span v-html="l.comentarios"></span></td>
                                        <td>@{{ l.usuario.nombre_completo }} <br><small>@{{l.area_solicitante}}</small></td>
                                        <td>
                                            <input class="cursor-pointer" type="checkbox" v-model="l.atendida" @change="solicitudAtendida(l)">
                                        </td>
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

    <div class="modal fade" id="modalRetrabajo" tabindex="-1" aria-labelledby="modalRetrabajoLabel" aria-hidden="true">
        <div class="modal-dialog" style="min-width: 40%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="bold modal-title" id="modalRetrabajoLabel">
                        AGREGAR TIEMPO DE RETRABAJO PARA @{{componente.nombre}}
                    </h3>
                    <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close" @click="fetchComponente(componente.id)">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xl-12">

                        </div>
                        <div class="col-xl-12 table-responsive">
                            <table class="table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Accion</th>
                                        <th>Horas</th>
                                        <th>Minutos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="p in solicitud.procesos" :key="'sol-' + p.id">
                                        <td class="py-1">@{{p.nombre}}</td>
                                        <td class="py-1">
                                            <div class="row">
                                                <div class="col-xl-12">
                                                    <div class="input-group mb-0">
                                                        <div class="input-group-prepend">
                                                            <button class="input-group-text py-0 cursor-pointer" style="background-color: #e3e3e3 !important" @click="p.horas > 0 ? p.horas-- : p.horas; actualizarRetrabajos()"> <i class="fa fa-minus"></i> &nbsp;&nbsp;</button>
                                                        </div>
                                                        <input type="number" v-model="p.horas" class="form-control text-center px-1 py-1" step="1" @change="actualizarRetrabajos()">
                                                        <div class="input-group-append">
                                                            <button class="input-group-text py-0 cursor-pointer" style="background-color: #e3e3e3 !important" @click="p.horas++; actualizarRetrabajos()"> &nbsp;&nbsp;<i class="fa fa-plus"></i> </button>
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
                                                            <button class="input-group-text py-0 cursor-pointer" style="background-color: #e3e3e3 !important" @click="p.minutos > 0 ? p.minutos-- : p.minuto; actualizarRetrabajos()"> <i class="fa fa-minus"></i> &nbsp;&nbsp;</button>
                                                        </div>
                                                        <input type="number" v-model="p.minutos" class="form-control text-center px-1 py-1" step="1" @change="actualizarRetrabajos()">
                                                        <div class="input-group-append">
                                                            <button class="input-group-text py-0 cursor-pointer" style="background-color: #e3e3e3 !important" @click="p.minutos < 60 ? p.minutos++ : p.minutos; actualizarRetrabajos()"> &nbsp;&nbsp;<i class="fa fa-plus"></i> </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-xl-12 form-group mt-3">
                            <label class="bold">Descripción del problema detectado: <span class="text-danger">*</span> </label>
                            <textarea v-model="componente.notificacion_texto" class="form-control w-100 text-left px-1 py-1" style="height: 120px" placeholder="Describe de problema para que el programador pueda realizar los ajustes requeridos (esto se agregara a la linea de tiempo del componente)..."></textarea>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-xl-12 text-right">
                            <button @click="fetchComponente(componente.id)" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                            <button class="btn" @click="enviarRetrabajo()"><i class="fa fa-save"></i> APLICAR CAMBIOS</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalModificacion" tabindex="-1" aria-labelledby="modalModificacionLabel" aria-hidden="true">
        <div class="modal-dialog" style="min-width: 40%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="bold modal-title" id="modalModificacionLabel">
                        SOLICITAR MODIFICACIÓN AL @{{!componente.esComponenteExterno?'AUXILIAR DE DISEÑO':'SOLICITANTE DEL COMPONENTE EXTERNO'}}
                    </h3>
                    <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xl-12 form-group">
                            <label class="bold">Descripción del problema detectado: <span class="text-danger">*</span> </label>
                            <textarea v-model="componente.notificacion_texto" class="form-control w-100 text-left px-1 py-1" style="height: 120px" placeholder="Describe de problema para que se puedan realizar los ajustes requeridos al diseño..."></textarea>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-xl-12 text-right">
                            <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                            <button class="btn" @click="enviarModificacion()"><i class="fa fa-save"></i> APLICAR CAMBIOS</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

     <div class="modal fade" id="modalSolicitudExterna" tabindex="-1" aria-labelledby="modalSolicitudExternaLabel" aria-hidden="true">
        <div class="modal-dialog" style="min-width: 40%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="bold modal-title" id="modalSolicitudExternaLabel">
                        INFORMACIÓN DE LA ORDEN DE TRABAJO EXTERNA
                    </h3>
                    <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xl-12 text-center">
                            <p><strong>Fecha de solicitud </strong><br>@{{solicitudExterna.fecha_solicitud_show}}</p>
                            <p><strong>Fecha deseada entrega </strong><br> @{{solicitudExterna.fecha_deseada_show}}</p>
                            <p><strong>Nombre del solicitante </strong><br> @{{solicitudExterna.solicitante.nombre_completo}}</p>
                            <p><strong>Area de solicitud </strong><br> @{{solicitudExterna.area_solicitud}}</p>
                            <p><strong>Comentarios e intrucciones </strong><br> @{{solicitudExterna.comentarios}}</p>
                            <p><strong>¿Requiere tratamiento térmico? </strong><br> @{{solicitudExterna.tratamiento_termico ? 'Si' : 'No'}}</p>
                            <p><strong>Archivos</strong></p>
                            <a :href="'/api/download/ordenes_trabajo/' + solicitudExterna.archivo_2d" v-if="solicitudExterna.archivo_2d" class="my-0 btn btn-default btn-sm"><i class="fa fa-download"></i> Archivo 2D</a>
                            <a :href="'/api/download/ordenes_trabajo/' + solicitudExterna.archivo_3d" v-if="solicitudExterna.archivo_3d" class="my-0 btn btn-default btn-sm"><i class="fa fa-download"></i> Archivo 3D</a>
                            <a :href="'/api/download/ordenes_trabajo/' + solicitudExterna.dibujo" v-if="solicitudExterna.dibujo" class="my-0 btn btn-default btn-sm"><i class="fa fa-download"></i> Dibujo a mano</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12 text-center mt-4">
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
            solicitud: {
                tipo: '',
                reasignar: 'corte'
            },
            solicitudes: [],
            maquinas: [],
            componente: {
                hay_retrabajo: false,
                notificacion_texto: ''
            },
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
            ruta: {
                anio: null,
                cliente: null,
                proyecto: null,
                herramental: null,
                componente: null,
            },
            procesos: [{
                    id: 1,
                    prioridad: 1,
                    nombre: 'Cortar',
                    horas: 0,
                    minutos: 0,
                    incluir: false
                },
                {
                    id: 2,
                    prioridad: 2,
                    nombre: 'Programar',
                    horas: 0,
                    minutos: 0,
                    incluir: false
                },
                 {
                    id: 3,
                    prioridad: 3,
                    nombre: 'Carear',
                    horas: 0,
                    minutos: 0,
                    incluir: false
                },
                {
                    id: 4,
                    prioridad: 4,
                    nombre: 'Maquinar',
                    horas: 0,
                    minutos: 0,
                    incluir: false
                },
                {
                    id: 5,
                    prioridad: 5,
                    nombre: 'Tornear',
                    horas: 0,
                    minutos: 0,
                    incluir: false
                },
                {
                    id: 6,
                    prioridad: 6,
                    nombre: 'Roscar/Rebabear',
                    horas: 0,
                    minutos: 0,
                    incluir: false
                },
                // {id: 7, prioridad: 7, nombre: 'Templar', horas: 0, minutos: 0, incluir: false},
                {
                    id: 8,
                    prioridad: 8,
                    nombre: 'Rectificar',
                    horas: 0,
                    minutos: 0,
                    incluir: false
                },
                {
                    id: 9,
                    prioridad: 9,
                    nombre: 'EDM',
                    horas: 0,
                    minutos: 0,
                    incluir: false
                }
            ],
            tasks: [],
            rutaAvance: [],
            programadores: [],
            verVersiones: false,
            lineaTiempo: [],
            solicitudExterna: {
                solicitante: {nombre_completo: ''},
                archivo_2d: null,
                archivo_3d: null,
                dibujo: null
            }
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
                let maxTime = {
                    horas: 0,
                    minutos: 0
                };

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
                let maxTime = {
                    horas: 0,
                    minutos: 0
                };

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
        methods: {
            async solicitudAtendida(solicitud){
                try {
                    const response = await axios.put(`/api/solicitud/${solicitud.id}/atendida`, { atendida: solicitud.atendida });
                    if (response.data.success) {
                        swal('Correcto', 'La solicitud ha sido actualizada correctamente.', 'success');
                    } else {
                        swal('Error', 'Ocurrió un error al actualizar la solicitud.', 'error');
                    }
                } catch (error) {
                    console.error('Error updating solicitud:', error);
                    swal('Error', 'Ocurrió un error al actualizar la solicitud.', 'error');
                }
            },
            async enviarRefaccion(band) {
                let t = this
                t.cargando = true;
                try {
                    const response = await axios.put(`/api/componente/${t.componente.id}/refaccion/${band}`);
                    if (response.data.success) {
                        t.cargando = false;
                        swal('Correcto', 'Se ha actualizado el componente correctamente.', 'success');
                        await t.fetchComponentes(t.selectedHerramental);
                        await t.fetchComponente(t.selectedComponente);
                    }else{
                        t.cargando = false;
                        swal('Error', 'Ocurrió un error al intentar actualizar el componente.', 'error');
                    }
                } catch (error) {
                    t.cargando = false;
                    swal('Error', 'Ocurrió un error al intentar actualizar el componente.', 'error');
                }
            },
            esRefaccion(band){
                if(band){ 
                    swal({
                        title: "¿Está seguro de marcar este componente como refacción?",
                        text: "",
                        icon: "info",
                        buttons: {
                            cancel: {
                                text: "Cancelar",
                                value: null,
                                visible: true,
                            },
                            confirm: {
                                text: "Aceptar",
                                value: true,
                                visible: true,
                            },
                        },
                        dangerMode: false,
                    }).then((willMark) => {
                        if (willMark) {
                            this.enviarRefaccion(true);
                        }
                    });
                }else{ //no es refaccion
                    swal({
                        title: "¿Está seguro de eliminar este componente como refacción?",
                        text: "",
                        icon: "info",
                        buttons: {
                            cancel: {
                                text: "Cancelar",
                                value: null,
                                visible: true,
                            },
                            confirm: {
                                text: "Aceptar",
                                value: true,
                                visible: true,
                            },
                        },
                        dangerMode: false,
                    }).then((willMark) => {
                        if (willMark) {
                            this.enviarRefaccion(false);
                        }
                    });
                }
            },
            async verSolicitudExterna(){
                let t = this
                this.cargando = true;

                try {
                    const response = await axios.get(`/api/solicitud-externa/${t.componente.id}`);
                    t.solicitudExterna = response.data.solicitud;

                } catch (error) {
                    console.error('Error fetching solicitudes:', error);
                } finally {
                    this.cargando = false;
                    $('#modalSolicitudExterna').modal();
                }
            },
            async generarRefabricacion() {
                let t = this
                const acepto = await this.aceptarRefabricacion();
                if (!acepto) {
                    return;
                }
                switch (acepto) {
                    case "refabricacion":
                        try {
                            t.cargando = true
                            const response = await axios.put(`/api/refabricacion-componente/${t.componente.id}`);
                            if (response) {
                                await this.fetchComponentes(this.selectedHerramental);
                                await this.fetchComponente(response.data.id);
                                t.cargando = false
                                swal('Refabricacion generada correctamente', `Se ha generado una nueva version para el componente ${t.componente.nombre}, ahora puedes liberarlo.`, 'success');
                            }
                        } catch (error) {
                            swal('Error', 'Ocurrió un error al intentar generar la refabricación.', 'error');
                            t.cargando = false
                        }
                        break;

                    default:
                        break;
                }
            },
            aceptarRefabricacion() {
                return swal({
                    title: `¿Está seguro de que desea realizar una refabricación del componente ${this.componente.nombre}?`,
                    text: "Esto generará una nueva versión del componente con la misma ruta y diseño. Posteriormente, será liberado para corte y programación.",
                    icon: "info",
                    buttons: {
                        cancel: {
                            text: "Cancelar",
                            value: null,
                            visible: true,
                        },
                        refabricacion: {
                            text: "Si, refabricar",
                            value: "refabricacion",
                            className: "btn-dark",
                        },
                    },
                });
            },
            async enviarRetrabajo() {
                let t = this
                t.cargando = true;

                t.componente.ruta = JSON.parse(JSON.stringify(t.tasks));
                t.componente.hay_retrabajo = true;

                try {
                    const response = await axios.put(`/api/componente/${t.selectedComponente}/enrutamiento/${false}`, t.componente);
                    $('#modalRetrabajo').modal('hide');
                    swal('Correcto', 'Se ha asignado el tiempo de retrabajo correctamente y el componente ha sido reasignado a programacion.', 'success');
                    await t.fetchComponentes(t.selectedHerramental);
                    await t.fetchComponente(t.selectedComponente);
                } catch (error) {
                    t.cargando = false
                    return false;
                }
            },
            async enviarModificacion() {
                let t = this
                t.cargando = true;
                t.componente.hay_modificacion = true;
                try {
                    const response = await axios.put(`/api/componente/${t.selectedComponente}/enrutamiento/${false}`, t.componente);

                    $('#modalModificacion').modal('hide');

                    if(t.componente.esComponenteExterno){
                        swal('Correcto', 'Se ha notificado correctamente al solicitante del componente externo sobre una modificación en el componente.', 'success');
                    }else{
                        swal('Correcto', 'Se ha notificacado correctamente al auxiliar de diseno sobre una modificación en el componente.', 'success');
                    }
                    await t.fetchComponentes(t.selectedHerramental);
                    await t.fetchComponente(t.selectedComponente);

                } catch (error) {
                    t.cargando = false
                    return false;
                }
            },
            abrirModalSolicitud(tipo) {
                let t = this;
                t.componente.notificacion_texto = '';

                switch (tipo) {
                    case 'retrabajo':
                        this.solicitud = {
                            tipo: tipo,
                            reasignar: 'corte',
                            procesos: [{
                                id: 1,
                                prioridad: 1,
                                nombre: 'Cortar',
                                horas: 0,
                                minutos: 0,
                            },
                            {
                                id: 2,
                                prioridad: 2,
                                nombre: 'Programar',
                                horas: 0,
                                minutos: 0,
                            },
                            {
                                id: 3,
                                prioridad: 3,
                                nombre: 'Carear',
                                horas: 0,
                                minutos: 0,
                            },
                            {
                                id: 4,
                                prioridad: 4,
                                nombre: 'Maquinar',
                                horas: 0,
                                minutos: 0,
                            },
                            {
                                id: 5,
                                prioridad: 5,
                                nombre: 'Tornear',
                                horas: 0,
                                minutos: 0,
                            },
                            {
                                id: 6,
                                prioridad: 6,
                                nombre: 'Roscar/Rebabear',
                                horas: 0,
                                minutos: 0,
                            },
                            // {id: 7, prioridad: 7, nombre: 'Templar', horas: 0, minutos: 0, incluir: false},
                            {
                                id: 8,
                                prioridad: 8,
                                nombre: 'Rectificar',
                                horas: 0,
                                minutos: 0,
                            },
                            {
                                id: 9,
                                prioridad: 9,
                                nombre: 'EDM',
                                horas: 0,
                                minutos: 0,
                            }
                            ],
                        };
                        this.solicitud.procesos = this.solicitud.procesos.filter(proceso => {
                            return this.tasks.some(task => task.id === proceso.id);
                        });

                        this.solicitud.procesos.forEach(proceso => {
                            let task = this.tasks.find(task => task.id === proceso.id);
                            if (task) {
                                let retrabajo = task.time.find(time => time.type === "rework");
                                if (retrabajo) {
                                    proceso.horas = retrabajo.horas;
                                    proceso.minutos = retrabajo.minutos;
                                }
                            }
                        });
                        $('#modalRetrabajo').modal({
                            backdrop: 'static',
                            keyboard: false // Opcional: evita cerrar el modal con la tecla Esc
                        });
                        break;
                    case 'modificacion':
                        $('#modalModificacion').modal({
                            backdrop: 'static',
                            keyboard: false
                        });
                        break;
                }

            },
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
            async fetchSolicitudes() {
                let t = this;
                this.cargando = true;

                try {
                    const response = await axios.get(`/api/solicitud/${t.componente.id}`);
                    let solicitudes = response.data.solicitudes;

                    this.solicitudes = solicitudes.map(s => {
                        return {
                            id: s.id, 
                            atendida: s.atendida,
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
            getMaquina(id) {
                let maquina = this.maquinas.find(m => m.id === id);
                return maquina ? maquina.nombre : '-';
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
            ajustarRutaAvance(tasks, rutaAvance) {
                let convertirAMinutos = (horas, minutos) => horas * 60 + minutos;

                let convertirAHorasYMinutos = (minutosTotales) => {
                    let horas = Math.floor(minutosTotales / 60);
                    let minutos = minutosTotales % 60;
                    return {
                        horas,
                        minutos
                    };
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
                            let {
                                horas: horasDelay,
                                minutos: minutosDelay
                            } = convertirAHorasYMinutos(excesoMinutos);

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
                            let {
                                horas,
                                minutos
                            } = convertirAHorasYMinutos(tiempoActualEnMinutos);
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

                let normalTime = {
                    horas: 0,
                    minutos: 0
                };
                let reworkTime = {
                    horas: 0,
                    minutos: 0
                };
                let delayTime = {
                    horas: 0,
                    minutos: 0
                };

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
            getMotivoRetraso(task) {
                switch (task.id) {
                    case 1:
                        return this.componente.retraso_corte ? `(${this.componente.retraso_corte})` : '';
                        break
                    case 2:
                        return this.componente.retraso_programacion ? `(${this.componente.retraso_programacion})` : '';
                        break
                    case 3:
                    case 4:
                    case 5:
                    case 6:
                    case 7:
                    case 8:
                        let fabricaciones = this.componente.fabricaciones.filter(element => element.proceso_id == task.id)
                        let motivosRetraso = fabricaciones.map(f => f.motivo_retraso).filter(motivo => motivo).join(', ')
                        return motivosRetraso ? `(${motivosRetraso})` : '';
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
                        time: [{
                            hora_inicio: null,
                            minuto_inicio: null,
                            horas: proceso.horas,
                            minutos: proceso.minutos,
                            type: "normal"
                        }]
                    });
                } else {
                    this.tasks.splice(index, 1);
                }
                this.calcularInicio();
            },
            actualizarRetrabajos() {
                let t = this;
                t.solicitud.procesos.forEach(proceso => {
                    let task = this.tasks.find(task => task.id === proceso.id);
                    if (task) {
                        let retrabajo = task.time.find(time => time.type === "rework");
                        if (retrabajo) {
                            retrabajo.horas = parseInt(proceso.horas);
                            retrabajo.minutos = parseInt(proceso.minutos);

                            if (proceso.horas === 0 && proceso.minutos === 0) {
                                task.time = task.time.filter(time => time.type !== "rework");
                            }
                        } else {
                            if (proceso.horas !== 0 || proceso.minutos !== 0) {
                                task.time.push({
                                    hora_inicio: null,
                                    minuto_inicio: null,
                                    horas: proceso.horas,
                                    minutos: proceso.minutos,
                                    type: "rework"
                                });
                            }
                        }
                    }
                });

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
                    if (find) {
                        element.time = find.time
                    }
                })

                const tareasFijas = this.tasks.filter(task => task.id === 1 || task.id === 2);
                const otrasTareas = this.tasks.filter(task => task.id !== 1 && task.id !== 2);

                tareasFijas.forEach(task => {
                    let proceso = t.procesos.find(p => p.id === task.id);
                    task.time.forEach((segmento, index) => {
                        if (index == 0) {
                            segmento.hora_inicio = 1;
                            segmento.minuto_inicio = 0;
                            segmento.horas = parseInt(proceso.horas);
                            segmento.minutos = parseInt(proceso.minutos);
                        } else {
                            if (task.id == 1) {
                                segmento.hora_inicio = acumuladorHoras1;
                                segmento.minuto_inicio = acumuladorMinutos1;
                            } else {
                                segmento.hora_inicio = acumuladorHoras2;
                                segmento.minuto_inicio = acumuladorMinutos2;
                            }
                        }
                        if (task.id == 1) {
                            acumuladorHoras1 += segmento.horas;
                            acumuladorMinutos1 += segmento.minutos;
                        } else {
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

                        if (index == 0) {
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

                Vue.nextTick(function() {
                    tippy('[data-tippy-root]').forEach(t => t.destroy());
                    tippy('.gantt-bar-segment', {
                        content(reference) {
                            return reference.getAttribute('data-tooltip')
                        },
                        allowHTML: true,
                        theme: 'light-border',
                    });
                })

                // FALTA ACTUALIZAR EL DE AVANCE CUANDO CAMBIE EL ORIGINAL
                // Vue.nextTick(function(){
                //     t.rutaAvance = t.ajustarRutaAvance(t.tasks, t.rutaAvance);
                //     t.calcularInicioAvance();
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

                        if (task.id == 1) {
                            segmento.hora_inicio = acumuladorHoras1;
                            segmento.minuto_inicio = acumuladorMinutos1;
                        } else {
                            segmento.hora_inicio = acumuladorHoras2;
                            segmento.minuto_inicio = acumuladorMinutos2;
                        }

                        if (task.id == 1) {
                            acumuladorHoras1 += segmento.horas;
                            acumuladorMinutos1 += segmento.minutos;
                        } else {
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
            regresar(step) {
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
            async fetchMaquinas() {
                try {
                    const response = await axios.get('/api/maquinas');
                    this.maquinas = response.data.maquinas;
                } catch (error) {
                    console.error('Error fetching maquinas:', error);
                }
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
                    Vue.nextTick(function() {
                        $('[data-toggle="tooltip"]').tooltip();
                    })
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
                    if (find) {
                        element.time = find.time
                    }
                })

                t.procesos =  [{
                    id: 1,
                    prioridad: 1,
                    nombre: 'Cortar',
                    horas: 0,
                    minutos: 0,
                    incluir: false
                },
                {
                    id: 2,
                    prioridad: 2,
                    nombre: 'Programar',
                    horas: 0,
                    minutos: 0,
                    incluir: false
                },
                 {
                    id: 3,
                    prioridad: 3,
                    nombre: 'Carear',
                    horas: 0,
                    minutos: 0,
                    incluir: false
                },
                {
                    id: 4,
                    prioridad: 4,
                    nombre: 'Maquinar',
                    horas: 0,
                    minutos: 0,
                    incluir: false
                },
                {
                    id: 5,
                    prioridad: 5,
                    nombre: 'Tornear',
                    horas: 0,
                    minutos: 0,
                    incluir: false
                },
                {
                    id: 6,
                    prioridad: 6,
                    nombre: 'Roscar/Rebabear',
                    horas: 0,
                    minutos: 0,
                    incluir: false
                },
                // {id: 7, prioridad: 7, nombre: 'Templar', horas: 0, minutos: 0, incluir: false},
                {
                    id: 8,
                    prioridad: 8,
                    nombre: 'Rectificar',
                    horas: 0,
                    minutos: 0,
                    incluir: false
                },
                {
                    id: 9,
                    prioridad: 9,
                    nombre: 'EDM',
                    horas: 0,
                    minutos: 0,
                    incluir: false
                }
                ];

                t.tasks.forEach(task => {
                    let proceso = t.procesos.find(obj => obj.id === task.id);

                    if (proceso) {
                        proceso.horas = task.time[0]?.horas ?? 0;
                        proceso.minutos = task.time[0]?.minutos ?? 0;
                        proceso.incluir = true;
                    }
                });

                Vue.nextTick(function() {
                    t.rutaAvance = t.ajustarRutaAvance(t.tasks, rutaAvanceAux);
                    t.calcularInicioAvance();

                    tippy('[data-tippy-root]').forEach(t => t.destroy());
                    Vue.nextTick(function() {
                        tippy('.gantt-bar-segment', {
                            content(reference) {
                                return reference.getAttribute('data-tooltip')
                            },
                            allowHTML: true,
                            theme: 'light-border',
                        });
                    })
                })

            },
            async guardar(liberarComponente) {
                let t = this

                if (liberarComponente) {
                    if (!t.componente.prioridad || !t.componente.programador_id) {
                        swal('Errores de validación', `Todos los campos son obligatorios para liberar.`, 'error');
                        return;
                    }
                    if (t.tasks.length == 0) {
                        swal('Errores de validación', `El componente debe incluir al menos una accion para poder ser liberado a programacion.`, 'error');
                        return;
                    }
                }
                t.cargando = true;
                t.componente.ruta = JSON.parse(JSON.stringify(t.tasks));

                try {
                    const response = await axios.put(`/api/componente/${t.selectedComponente}/enrutamiento/${liberarComponente}`, t.componente);
                    if (!liberarComponente)
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
                    if (!componente.fecha_solicitud || !componente.fecha_pedido || !componente.fecha_estimada || !componente.fecha_real) {
                        errores.push(`Todos los campos son obligatorios para liberar en ${componente.nombre}.`);
                    }
                });

                if (errores.length > 0) {
                    swal('Errores de validación', errores.join('\n'), 'error');
                    return;
                }


                t.cargando = true;
                let respuesta = await t.guardarComponentes(false);
                if (respuesta) {
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
                } else {
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
            tienePendientes(componente) {
                return componente.bAjuste || componente.bRetrabajo || 
                    componente.bModificacion || componente.bRefabricacion || 
                    componente.bRechazo;
            },
            getContenidoTooltipComponente(componente) {
                let pendientes = [];
                if (componente.bAjuste) pendientes.push("Ajuste");
                if (componente.bRetrabajo) pendientes.push("Retrabajo");
                if (componente.bModificacion) pendientes.push("Modificación");
                if (componente.bRefabricacion) pendientes.push("Refabricación");
                if (componente.bRechazo) pendientes.push("Rechazo");

                return pendientes.length 
                    ? `Pendiente: ${pendientes.join(', ')}`
                    : "No hay pendientes";
            }
        },
        mounted: async function() {
            let t = this;
            await t.fetchAnios();
            await t.fetchProgramadores();
            await t.fetchMaquinas();
            this.navigateFromUrlParams();
        }


    })
</script>
@endpush