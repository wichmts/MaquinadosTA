@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'dashboard'
])

<style>
    .text-spacing {
        letter-spacing: 2px;
    }
    .text-none {
        text-transform: none !important;
    }
    .counter{
        font-size: 15px !important;
    }
    [v-cloak] { display: none}

    .scrollable{
        max-height: 50vh !important;
        overflow-y: scroll !important;
    }
</style>

@section('content')
    <div class="content" id="app">
        @if(session('error'))
            <div class="alert alert-primary text-dark" role="alert">
                <i class="fa fa-info-circle "></i> <strong>¡Lo sentimos! </strong> {{ session('error') }}
            </div>
        @endif
        <div class="container-fluid pb-5 px-5" v-cloak>

            <div class="row">
                <div class="col-11 col-lg-12">
                     {{-- almacenista --}}
                    <div class="row" v-if="roles.includes('ALMACENISTA')">
                        <div class="col-lg-12 pt-3 pb-4">
                            <h2 class="my-1 bold text-spacing">ALMACENISTA</h2>
                            <small class="text-muted mt-1 mb-4 text-spacing">TRABAJOS PENDIENTES</small>
                        </div>
                        <div class="col-lg-4">
                            <p class="text-spacing mb-1">CORTE DE COMPONENTES <span class="badge badge-pill counter bold" :class="{'badge-warning' : trabajosPendientes?.cortes?.length > 0, 'badge-secondary': trabajosPendientes?.cortes?.length == 0 }">@{{trabajosPendientes?.cortes?.length}}</span></p>
                            <div class="scrollable">
                                <table class="table table-hover table-striped ">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-none">Componente</th>
                                            <th class="text-none">Cantidad</th>
                                            <th class="text-none">Prioridad</th>
                                            <th class="text-none">Estatus</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="cursor-pointer" v-for="(componente, index) in trabajosPendientes.cortes" @click="goTo('corte', componente.rutaComponente)">
                                            <td class="bold">@{{componente.nombre}}</td>
                                            <td>@{{componente.cantidad}}</td>
                                            <td>
                                                <span v-if="componente.prioridad == 'I'" class="badge badge-danger badge-pill px-2 py-1"> Prioridad @{{componente.prioridad}}</span>
                                                <span v-if="componente.prioridad == 'A'" class="badge badge-danger badge-pill px-2 py-1"> Prioridad @{{componente.prioridad}}</span>
                                                <span v-if="componente.prioridad == 'B'" class="badge badge-warning badge-pill px-2 py-1"> Prioridad @{{componente.prioridad}}</span>
                                                <span v-if="componente.prioridad == 'C'" class="badge badge-info badge-pill px-2 py-1"> Prioridad @{{componente.prioridad}}</span>
                                            </td>
                                            <td>
                                                <span v-if="componente.estatus_corte == 'paro'" class="py-2 w-100 badge badge-danger">EN PARO</span>
                                                <span v-if="componente.estatus_corte == 'inicial'" class="py-2 w-100 badge badge-warning">POR CORTAR</span>
                                                <span v-if="componente.estatus_corte == 'proceso'" class="py-2 w-100 badge badge-info">EN PROCESO...</span>
                                                <span v-if="componente.estatus_corte == 'pausado'" class="py-2 w-100 badge badge-dark">PAUSADO</span>
                                            </td>
                                        </tr>
                                        <tr v-if="trabajosPendientes?.cortes?.length == 0">
                                            <td colspan="4" class="text-center">No hay trabajo pendiente</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <p class="text-spacing mb-1">COMPRA DE COMPONENTES <span class="badge badge-pill counter bold" :class="{'badge-warning' : trabajosPendientes?.compras?.length > 0, 'badge-secondary': trabajosPendientes?.compras?.length == 0 }">@{{trabajosPendientes?.compras?.length}}</span></p>
                            <div class="scrollable">

                            </div>
                            <table class="table table-hover table-striped ">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-none">Componente</th>
                                        <th class="text-none">Cantidad</th>
                                        <th class="text-none">Fecha de llegada</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="cursor-pointer" v-for="(componente, index) in trabajosPendientes.compras" @click="goTo('compra-componentes', componente.rutaComponente)">
                                        <td class="bold">@{{componente.nombre}}</td>
                                        <td>@{{componente.cantidad}}</td>
                                        <td>@{{componente.fecha_cargado}}Hrs.</td>
                                    </tr>
                                    <tr v-if="trabajosPendientes?.compras?.length == 0">
                                        <td colspan="3" class="text-center">No hay compras pendientes</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-4">
                            <p class="text-spacing mb-1">TEMPLE DE COMPONENTES <span class="badge badge-pill counter bold" :class="{'badge-warning' : trabajosPendientes?.temples?.length > 0, 'badge-secondary': trabajosPendientes?.temples?.length == 0 }">@{{trabajosPendientes?.temples?.length}}</span></p>
                            <div class="scrollable">
                                <table class="table table-hover table-striped ">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-none">Componente</th>
                                            <th class="text-none">Cantidad</th>
                                            <th class="text-none">Prioridad</th>
                                            <th class="text-none">Fecha de solicitud</th>
                                        </tr>
                                        
                                    </thead>
                                    <tbody>
                                        <tr class="cursor-pointer" v-for="(componente, index) in trabajosPendientes.temples" @click="goTo('temple', componente.rutaComponente)">
                                            <td class="bold">@{{componente.nombre}}</td>
                                            <td>@{{componente.cantidad}}</td>
                                            <td>
                                                <span v-if="componente.prioridad == 'I'" class="badge badge-danger badge-pill px-2 py-1"> Prioridad @{{componente.prioridad}}</span>
                                                <span v-if="componente.prioridad == 'A'" class="badge badge-danger badge-pill px-2 py-1"> Prioridad @{{componente.prioridad}}</span>
                                                <span v-if="componente.prioridad == 'B'" class="badge badge-warning badge-pill px-2 py-1"> Prioridad @{{componente.prioridad}}</span>
                                                <span v-if="componente.prioridad == 'C'" class="badge badge-info badge-pill px-2 py-1"> Prioridad @{{componente.prioridad}}</span>
                                            </td>
                                            <td>@{{componente.fecha_solicitud_temple}}</td>
                                        </tr>
                                        <tr v-if="trabajosPendientes?.temples?.length == 0">
                                            <td colspan="4" class="text-center">No hay temples pendientes</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{-- jefe de area --}}
                    <div class="row" v-if="roles.includes('JEFE DE AREA')">
                        <div class="col-lg-12 pt-3 pb-4">
                            <h2 class="my-1 bold text-spacing">JEFE DE AREA</h2>
                            <small class="text-muted mt-1 mb-4 text-spacing">TRABAJOS PENDIENTES</small>
                        </div>
                        <div class="col-lg-4">
                            <p class="text-spacing mb-1">ENRUTAMIENTO DE COMPONENTES <span class="badge badge-pill counter bold" :class="{'badge-warning' : trabajosPendientes?.enrutamiento?.length > 0, 'badge-secondary': trabajosPendientes?.enrutamiento?.length == 0 }">@{{trabajosPendientes?.enrutamiento?.length}}</span></p>
                            <div class="scrollable">
                                <table class="table table-hover table-striped ">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-none">Componente</th>
                                            <th class="text-none">Cantidad</th>
                                            <th class="text-none">Fecha de liberación</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="cursor-pointer" v-for="(componente, index) in trabajosPendientes.enrutamiento" @click="goTo('enrutador', componente.rutaComponente)">
                                            <td class="bold">@{{componente.nombre}}</td>
                                            <td>@{{componente.cantidad}}</td>
                                            <td>@{{componente.fecha_cargado}}Hrs.</td>
                                        </tr>
                                        <tr v-if="trabajosPendientes?.enrutamiento?.length == 0">
                                            <td colspan="3" class="text-center">No hay trabajo pendiente</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <p class="text-spacing mb-1">PRUEBAS DE DISEÑO <span class="badge badge-pill counter bold" :class="{'badge-warning' : trabajosPendientes?.pruebas_diseno?.length > 0, 'badge-secondary': trabajosPendientes?.pruebas_diseno?.length == 0 }">@{{trabajosPendientes?.pruebas_diseno?.length}}</span></p>
                            <div class="scrollable">
                                <table class="table table-hover table-striped ">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-none">Herramental</th>
                                            <th class="text-none">Fecha de ensamble</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="cursor-pointer" v-for="(herramental, index) in trabajosPendientes.pruebas_diseno" @click="goTo('visor-pruebas', herramental.rutaHerramental)">
                                            <td class="bold">@{{herramental.nombre}}</td>
                                            <td>@{{herramental.termino_ensamble}}Hrs.</td>
                                        </tr>
                                        <tr v-if="trabajosPendientes?.pruebas_diseno?.length == 0">
                                            <td colspan="2" class="text-center">No hay pruebas pendientes</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    
                        <div class="col-lg-4">
                            <p class="text-spacing mb-1">SOLICITUDES NO ATENDIDAS <span class="badge badge-pill counter bold" :class="{'badge-warning' : trabajosPendientes?.solicitudes?.length > 0, 'badge-secondary': trabajosPendientes?.solicitudes?.length == 0 }">@{{trabajosPendientes?.solicitudes?.length}}</span></p>
                            <div class="scrollable">
                                <table class="table table-hover table-striped ">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-none">Componente</th>
                                            <th class="text-none">Maquina</th>
                                            <th class="text-none">Comentarios</th>
                                            <th class="text-none">Fecha de solicitud</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="cursor-pointer" v-for="(solicitud, index) in trabajosPendientes.solicitudes" @click="goTo('enrutador', solicitud.rutaComponente)">
                                            <td class="bold">@{{solicitud.componente}}</td>
                                            <td>@{{solicitud.fabricacion?.maquina?.nombre}}</td>
                                            <td>@{{solicitud.comentarios}}Hrs.</td>
                                            <td>@{{solicitud.fecha_show}} @{{solicitud.hora_show}} </td>
                                        </tr>
                                        <tr v-if="trabajosPendientes?.solicitudes?.length == 0">
                                            <td colspan="4" class="text-center">No hay solicitudes pendientes</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-if="roles.includes('PROCESOS')">
                        <div class="col-lg-12 pt-3 pb-4">
                            <h2 class="my-1 bold text-spacing">PROCESOS</h2>
                            <small class="text-muted mt-1 mb-4 text-spacing">TRABAJOS PENDIENTES</small>
                        </div>
                        <div class="col-lg-4">
                            <p class="text-spacing mb-1">PRUEBAS DE PROCESOS <span class="badge badge-pill counter bold" :class="{'badge-warning' : trabajosPendientes?.pruebas_proceso?.length > 0, 'badge-secondary': trabajosPendientes?.pruebas_proceso?.length == 0 }">@{{trabajosPendientes?.pruebas_proceso?.length}}</span></p>
                            <div class="scrollable">
                                <table class="table table-hover table-striped ">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-none">Herramental</th>
                                            <th class="text-none">Fecha de ensamble</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="cursor-pointer" v-for="(herramental, index) in trabajosPendientes.pruebas_proceso" @click="goTo('pruebas-proceso', herramental.rutaHerramental)">
                                            <td class="bold">@{{herramental.nombre}}</td>
                                            <td>@{{herramental.termino_ensamble}}Hrs.</td>
                                        </tr>
                                        <tr v-if="trabajosPendientes?.pruebas_proceso?.length == 0">
                                            <td colspan="2" class="text-center">No hay pruebas pendientes</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-if="roles.includes('DISEÑO')">
                        <div class="col-lg-12 pt-3 pb-4">
                            <h2 class="my-1 bold text-spacing">DISEÑO</h2>
                            <small class="text-muted mt-1 mb-4 text-spacing">TRABAJOS PENDIENTES</small>
                        </div>
                        <div class="col-lg-4">
                            <p class="text-spacing mb-1">PRUEBAS DE DISEÑO <span class="badge badge-pill counter bold" :class="{'badge-warning' : trabajosPendientes?.pruebas_diseno?.length > 0, 'badge-secondary': trabajosPendientes?.pruebas_diseno?.length == 0 }">@{{trabajosPendientes?.pruebas_diseno?.length}}</span></p>
                            <div class="scrollable">
                                <table class="table table-hover table-striped ">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-none">Herramental</th>
                                            <th class="text-none">Fecha de ensamble</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="cursor-pointer" v-for="(herramental, index) in trabajosPendientes.pruebas_diseno" @click="goTo('visor-pruebas', herramental.rutaHerramental)">
                                            <td class="bold">@{{herramental.nombre}}</td>
                                            <td>@{{herramental.termino_ensamble}} Hrs.</td>
                                        </tr>
                                        <tr v-if="trabajosPendientes?.pruebas_diseno?.length == 0">
                                            <td colspan="2" class="text-center">No hay pruebas pendientes</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{-- programador --}}
                    <div class="row" v-if="roles.includes('PROGRAMADOR')">
                        <div class="col-lg-12 pt-3 pb-4">
                            <h2 class="my-1 bold text-spacing">PROGRAMADOR</h2>
                            <small class="text-muted mt-1 mb-4 text-spacing">TRABAJOS PENDIENTES</small>
                        </div>
                        <div class="col-lg-6">
                            <p class="text-spacing mb-1">PROGRAMACIÓN DE COMPONENTES <span class="badge badge-pill counter bold" :class="{'badge-warning' : trabajosPendientes?.programaciones?.length > 0, 'badge-secondary': trabajosPendientes?.programaciones?.length == 0 }">@{{trabajosPendientes?.programaciones?.length}}</span></p>
                            <div class="scrollable">
                                <table class="table table-hover table-striped ">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-none">Componente</th>
                                            <th class="text-none">Cantidad</th>
                                            <th class="text-none">Prioridad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="cursor-pointer" v-for="(componente, index) in trabajosPendientes.programaciones" @click="goTo('visor-programador', componente.rutaComponente)">
                                            <td class="bold">@{{componente.nombre}}</td>
                                            <td>@{{componente.cantidad}}</td>
                                            <td>
                                                <span v-if="componente.prioridad == 'I'" class="badge badge-danger badge-pill px-2 py-1"> Prioridad @{{componente.prioridad}}</span>
                                                <span v-if="componente.prioridad == 'A'" class="badge badge-danger badge-pill px-2 py-1"> Prioridad @{{componente.prioridad}}</span>
                                                <span v-if="componente.prioridad == 'B'" class="badge badge-warning badge-pill px-2 py-1"> Prioridad @{{componente.prioridad}}</span>
                                                <span v-if="componente.prioridad == 'C'" class="badge badge-info badge-pill px-2 py-1"> Prioridad @{{componente.prioridad}}</span>
                                            </td>
                                        </tr>
                                        <tr v-if="trabajosPendientes?.programaciones?.length == 0">
                                            <td colspan="3" class="text-center">No hay trabajo pendiente</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{-- fabricaciones --}}
                    <div class="row" v-if="roles.includes('OPERADOR')">
                        <div class="col-lg-12 pt-3 pb-4">
                            <h2 class="my-1 bold text-spacing">OPERADOR</h2>
                            <small class="text-muted mt-1 mb-4 text-spacing">TRABAJOS PENDIENTES</small>
                        </div>
                        <div class="col-lg-6">
                            <p class="text-spacing mb-1">FABRICACIÓN DE COMPONENTES <span class="badge badge-pill counter bold" :class="{'badge-warning' : trabajosPendientes?.fabricaciones?.length > 0, 'badge-secondary': trabajosPendientes?.fabricaciones?.length == 0 }">@{{trabajosPendientes?.fabricaciones?.length}}</span></p>
                            <div class="scrollable">
                                <table class="table table-hover table-striped ">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-none">Componente</th>
                                            <th class="text-none">Cantidad</th>
                                            <th class="text-none">Maquina</th>
                                            <th class="text-none">Prioridad</th>
                                            <th class="text-none">Fecha liberación</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="cursor-pointer" v-for="(fabricacion, index) in trabajosPendientes.fabricaciones" @click="goTo('visor-operador', `?maq=${fabricacion.maquina_id}&co=${fabricacion.componente_id}&fab=${fabricacion.fabricacion_id}`)">
                                            <td class="bold">@{{fabricacion.componente}}</td>
                                            <td>@{{fabricacion.cantidad}}</td>
                                            <td>@{{fabricacion.maquina}}</td>
                                            <td>
                                                <span v-if="fabricacion.prioridad == 'I'" class="badge badge-danger badge-pill px-2 py-1"> Prioridad @{{fabricacion.prioridad}}</span>
                                                <span v-if="fabricacion.prioridad == 'A'" class="badge badge-danger badge-pill px-2 py-1"> Prioridad @{{fabricacion.prioridad}}</span>
                                                <span v-if="fabricacion.prioridad == 'B'" class="badge badge-warning badge-pill px-2 py-1"> Prioridad @{{fabricacion.prioridad}}</span>
                                                <span v-if="fabricacion.prioridad == 'C'" class="badge badge-info badge-pill px-2 py-1"> Prioridad @{{fabricacion.prioridad}}</span>
                                            </td>
                                            <td>@{{fabricacion.fecha_liberacion}}</td>
                                        </tr>
                                        <tr v-if="trabajosPendientes?.fabricaciones?.length == 0">
                                            <td colspan="5" class="text-center">No hay fabricaciones pendientes</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{-- ensamble --}}
                    <div class="row" v-if="roles.includes('MATRICERO')">
                        <div class="col-lg-12 pt-3 pb-4">
                            <h2 class="my-1 bold text-spacing">MATRICERO</h2>
                            <small class="text-muted mt-1 mb-4 text-spacing">TRABAJOS PENDIENTES</small>
                        </div>
                        <div class="col-lg-6">
                            <p class="text-spacing mb-1">ENSAMBLE DE COMPONENTES <span class="badge badge-pill counter bold" :class="{'badge-warning' : trabajosPendientes?.ensambles?.length > 0, 'badge-secondary': trabajosPendientes?.ensambles?.length == 0 }">@{{trabajosPendientes?.ensambles?.length}}</span></p>
                            <div class="scrollable">
                                <table class="table table-hover table-striped ">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-none">Componente</th>
                                            <th class="text-none">Tipo</th>
                                            <th class="text-none">Cantidad</th>
                                            <th class="text-none">Fecha liberación / compra</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="cursor-pointer" v-for="(componente, index) in trabajosPendientes.ensambles" @click="goTo('matricero', componente.rutaComponente)">
                                            <td class="bold">@{{componente.nombre}}</td>
                                            <td>@{{componente.es_compra ? 'Compra' : 'Fabricación'}}</td>
                                            <td>@{{componente.cantidad}}</td>
                                            <td>@{{componente.es_compra ? componente.fecha_real : componente.fecha_terminado + 'Hrs.'}} </td>
                                        </tr>
                                        <tr v-if="trabajosPendientes?.ensambles?.length == 0">
                                            <td colspan="4" class="text-center">No hay ensambles pendientes</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-1 d-lg-none" style="background-color: #ededed">

                </div>
            </div>
        
        </div>
    </div>
@endsection

@push('scripts')

<script>
    new Vue({
        el: '#app',
        data: {
            loading: true,
            roles: @json(Auth::user()->getRoleNames()),
            user: @json(Auth::user()),
            trabajosPendientes: {
                compras: [],
                cortes: [],
                temples: [],
                enrutamiento: [],
                solicitudes: [],
                programaciones: [],
                fabricaciones: [],
                ensambles: [],
                pruebas_diseno: [],
                pruebas_proceso: [],
            }
        },
        methods:{
            goTo(ruta, query){
                window.location.href = `/${ruta}${query}`;
            },
            async getDataInitial(){
                let t = this
                let response = await axios.get("/api/trabajos-pendientes");
                if(response.data.success){
                    t.trabajosPendientes = response.data.data;
                }
                console.log(t.trabajosPendientes);
                t.loading = false;
            },
        },
        mounted() {
            let t = this;
            this.$nextTick(function () {
                t.getDataInitial();
            })
        }
    });
</script>
 
@endpush
