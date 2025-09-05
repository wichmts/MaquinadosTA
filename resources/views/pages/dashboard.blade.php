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
</style>

@section('content')
    <div class="content" id="app">
        @if(session('error'))
            <div class="alert alert-primary text-dark" role="alert">
                <i class="fa fa-info-circle "></i> <strong>¡Lo sentimos! </strong> {{ session('error') }}
            </div>
        @endif
        <div class="container-fluid" v-cloak>
            
            {{-- almacenista --}}
            <div class="row" v-if="roles.includes('ALMACENISTA')">
                <div class="col-xl-12 pt-3 pb-4">
                    <h2 class="my-1 bold text-spacing">ALMACENISTA</h2>
                    <small class="text-muted mt-1 mb-4 text-spacing">TRABAJOS PENDIENTES</small>
                </div>
                <div class="col-xl-4">
                    <p class="text-spacing mb-1">CORTE DE COMPONENTES <span class="badge badge-pill badge-warning counter bold">@{{trabajosPendientes?.cortes?.length}}</span></p>
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-none">Componente</th>
                                <th class="text-none">Cantidad</th>
                                <th class="text-none">Prioridad</th>
                                <th class="text-none">Estatus</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="cursor-pointer" v-for="(componente, index) in trabajosPendientes.cortes" @click="goToRuta('corte', componente.rutaComponente)">
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
                        </tbody>
                    </table>
                </div>
                <div class="col-xl-4">
                    <p class="text-spacing mb-1">COMPRA DE COMPONENTES <span class="badge badge-pill badge-warning counter bold">@{{trabajosPendientes?.compras?.length}}</span></p>
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-none">Componente</th>
                                <th class="text-none">Cantidad</th>
                                <th class="text-none">Fecha de llegada</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="cursor-pointer" v-for="(componente, index) in trabajosPendientes.compras" @click="goToRuta('compra-componentes', componente.rutaComponente)">
                                <td class="bold">@{{componente.nombre}}</td>
                                <td>@{{componente.cantidad}}</td>
                                <td>@{{componente.fecha_cargado}}Hrs.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                 <div class="col-xl-4">
                    <p class="text-spacing mb-1">TEMPLE DE COMPONENTES <span class="badge badge-pill badge-warning counter bold">@{{trabajosPendientes?.temples?.length}}</span></p>
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-none">Componente</th>
                                <th class="text-none">Cantidad</th>
                                <th class="text-none">Prioridad</th>
                                <th class="text-none">Fecha de solicitud</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="cursor-pointer" v-for="(componente, index) in trabajosPendientes.temples" @click="goToRuta('temple', componente.rutaComponente)">
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
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- jefe de area --}}
            <div class="row" v-if="roles.includes('JEFE DE AREA')">
                <div class="col-xl-12 pt-3 pb-4">
                    <h2 class="my-1 bold text-spacing">JEFE DE AREA</h2>
                    <small class="text-muted mt-1 mb-4 text-spacing">TRABAJOS PENDIENTES</small>
                </div>
                <div class="col-xl-4">
                    <p class="text-spacing mb-1">ENRUTAMIENTO DE COMPONENTES <span class="badge badge-pill badge-warning counter bold">@{{trabajosPendientes?.enrutamiento?.length}}</span></p>
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-none">Componente</th>
                                <th class="text-none">Cantidad</th>
                                <th class="text-none">Fecha de liberación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="cursor-pointer" v-for="(componente, index) in trabajosPendientes.enrutamiento" @click="goToRuta('enrutador', componente.rutaComponente)">
                                <td class="bold">@{{componente.nombre}}</td>
                                <td>@{{componente.cantidad}}</td>
                                <td>@{{componente.fecha_cargado}}Hrs.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-xl-5">
                    <p class="text-spacing mb-1">SOLICITUDES NO ATENDIDAS <span class="badge badge-pill badge-warning counter bold">@{{trabajosPendientes?.solicitudes?.length}}</span></p>
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-none">Componente</th>
                                <th class="text-none">Maquina</th>
                                <th class="text-none">Comentarios</th>
                                <th class="text-none">Fecha de solicitud</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="cursor-pointer" v-for="(solicitud, index) in trabajosPendientes.solicitudes" @click="goToRuta('enrutador', solicitud.rutaComponente)">
                                <td class="bold">@{{solicitud.componente}}</td>
                                <td>@{{solicitud.fabricacion?.maquina?.nombre}}</td>
                                <td>@{{solicitud.comentarios}}Hrs.</td>
                                <td>@{{solicitud.fecha_show}} @{{solicitud.hora_show}} </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
            {{-- programador --}}
             <div class="row" v-if="roles.includes('PROGRAMADOR')">
                <div class="col-xl-12 pt-3 pb-4">
                    <h2 class="my-1 bold text-spacing">PROGRAMADOR</h2>
                    <small class="text-muted mt-1 mb-4 text-spacing">TRABAJOS PENDIENTES</small>
                </div>
                <div class="col-xl-6">
                    <p class="text-spacing mb-1">PRROGRAMACIÓN DE COMPONENTES <span class="badge badge-pill badge-warning counter bold">@{{trabajosPendientes?.programaciones?.length}}</span></p>
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-none">Componente</th>
                                <th class="text-none">Cantidad</th>
                                <th class="text-none">Prioridad</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="cursor-pointer" v-for="(componente, index) in trabajosPendientes.programaciones" @click="goToRuta('visor-programador', componente.rutaComponente)">
                                <td class="bold">@{{componente.nombre}}</td>
                                <td>@{{componente.cantidad}}</td>
                                <td>
                                    <span v-if="componente.prioridad == 'I'" class="badge badge-danger badge-pill px-2 py-1"> Prioridad @{{componente.prioridad}}</span>
                                    <span v-if="componente.prioridad == 'A'" class="badge badge-danger badge-pill px-2 py-1"> Prioridad @{{componente.prioridad}}</span>
                                    <span v-if="componente.prioridad == 'B'" class="badge badge-warning badge-pill px-2 py-1"> Prioridad @{{componente.prioridad}}</span>
                                    <span v-if="componente.prioridad == 'C'" class="badge badge-info badge-pill px-2 py-1"> Prioridad @{{componente.prioridad}}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
             {{-- fabricaciones --}}
             <div class="row" v-if="roles.includes('OPERADOR')">
                <div class="col-xl-12 pt-3 pb-4">
                    <h2 class="my-1 bold text-spacing">OPERADOR</h2>
                    <small class="text-muted mt-1 mb-4 text-spacing">TRABAJOS PENDIENTES</small>
                </div>
                <div class="col-xl-6">
                    <p class="text-spacing mb-1">FABRICACIÓN DE COMPONENTES <span class="badge badge-pill badge-warning counter bold">@{{trabajosPendientes?.fabricaciones?.length}}</span></p>
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-none">Componente</th>
                                <th class="text-none">Cantidad</th>
                                <th class="text-none">Maquina</th>
                                <th class="text-none">Prioridad</th>
                                <th class="text-none">Estatus fabricación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="cursor-pointer" v-for="(fabricacion, index) in trabajosPendientes.fabricaciones" @click="goToRuta('visor-operador', `?maq=${fabricacion.maquina_id}&co=${fabricacion.componente_id}&fab=${fabricacion.fabricacion_id}`)">
                                <td class="bold">@{{fabricacion.componente}}</td>
                                <td>@{{fabricacion.cantidad}}</td>
                                <td>@{{fabricacion.maquina}}</td>
                                <td>
                                    <span v-if="fabricacion.prioridad == 'I'" class="badge badge-danger badge-pill px-2 py-1"> Prioridad @{{fabricacion.prioridad}}</span>
                                    <span v-if="fabricacion.prioridad == 'A'" class="badge badge-danger badge-pill px-2 py-1"> Prioridad @{{fabricacion.prioridad}}</span>
                                    <span v-if="fabricacion.prioridad == 'B'" class="badge badge-warning badge-pill px-2 py-1"> Prioridad @{{fabricacion.prioridad}}</span>
                                    <span v-if="fabricacion.prioridad == 'C'" class="badge badge-info badge-pill px-2 py-1"> Prioridad @{{fabricacion.prioridad}}</span>
                                </td>
                                <td>
                                    <span class="badge px-3 py-2 badge-dark">@{{fabricacion.estatus_fabricacion.toUpperCase()}}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
            allPosibleRoles: [
                'ALMACENISTA',
                'JEFE DE AREA',
                'PROGRAMADOR',
                'OPERADOR',
                'MATRICERO'
            ],
            trabajosPendientes: {
                compras: [],
                cortes: [],
                temples: [],
                enrutamiento: [],
                solicitudes: [],
                programaciones: [],
                fabricaciones: [],
                ensambles: []
            }
        },
        methods:{
            goToRuta(ruta, query){
                window.location.href = `/${ruta}${query}`;
            },
            async getDataInitial(){
                let t = this
                let response = await axios.get("/api/trabajos-pendientes");
                if(response.data.success){
                    t.trabajosPendientes = response.data.data;
                }
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
