@extends('layouts.app', [
'class' => '',
'elementActive' => 'dashboard'
])

<style>
    .navBtn {
        font-weight: 600;
        font-size: 0.8571em;
        line-height: 1.35em;
        text-transform: uppercase;
        margin: 10px 1px;
        padding: 11px 22px;
        cursor: pointer;
    }
    .navBtn:hover {
        background-color: #eee;
    }
    thead th{
        text-transform: none !important;
    }
</style>

@section('content')
<div id="vue-app" v-cloak>
    <div class="container-fluid mt-3">
        <div class="col-lg-12">
            <h2 class="bold my-0 py-1 mb-3 text-decoration-underline" style="letter-spacing: 2px">VISOR GENERAL</h2> 
        </div>
        <div class="col-lg-12">
            <!-- Nav -->
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="enrutamiento-tab" data-toggle="tab" data-target="#enrutamiento" type="button" role="tab" aria-controls="enrutamiento" aria-selected="true">ENRUTAMIENTO</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="programacion-tab" data-toggle="tab" data-target="#programacion" type="button" role="tab" aria-controls="programacion" aria-selected="false">PROGRAMACIÓN</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="corte-tab" data-toggle="tab" data-target="#corte-mp" type="button" role="tab" aria-controls="corte" aria-selected="false">CORTE DE MP</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="fabricacion-tab" data-toggle="tab" data-target="#fabricacion" type="button" role="tab" aria-controls="fabricacion" aria-selected="false">FABRICACIONES POR MÁQUINA</button>
                </li>
            </ul>
            <div class="tab-content pt-4" id="myTabContent">
                <div class="tab-pane fade show active" id="enrutamiento" role="tabpanel" aria-labelledby="home-tab">
                    <div class="row mb-3">
                        <div class="table-responsive col-lg-12">
                            <table class="table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Nombre del componente</th>
                                        <th>Cantidad</th>
                                        <th>Fecha Liberacion</th>
                                        <th>Comentarios del enrutador</th>                                    
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(componente, index) in trabajosPendientes.enrutamiento">
                                        <td class="bold">@{{componente.nombre}}</td>
                                        <td>@{{componente.cantidad}}</td>
                                        <td>@{{componente.fecha_cargado}}Hrs.</td>
                                        <td>@{{componente.comentarios??'Sin comentarios'}}</td>                                    
                                    </tr>
                                    <tr v-if="trabajosPendientes?.enrutamiento?.length == 0">
                                        <td colspan="3" class="text-center">No hay trabajos de enrutamiento pendiente</td>
                                    </tr>
                                </tbody>
    
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="programacion" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="row mb-3">
                        <div class="table-responsive col-lg-12">
                            <table class="table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Nombre del programador</th>
                                        <th>Cola de trabajos activos</th>
                                        <th>Comentarios del enrutador</th>
                                        <th>Ver ruta componente</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(programador, pIndex) in trabajosPendientes.programaciones" :key="'prog-' + programador.programador_id">
                                        <td class="bold">@{{ programador.programador_nombre }}</td>
    
                                        <td>
                                            <div class="my-4" v-for="componente in programador.componentes" :key="'comp-nombre-' + componente.id">
                                                @{{ componente.nombre }}
                                            </div>
                                        </td>
    
                                        <td>
                                            <div class="my-4" v-for="componente in programador.componentes" :key="'comp-comentarios-' + componente.id">
                                                @{{ componente.comentarios ?? 'Sin comentarios' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div v-for="componente in programador.componentes" :key="'comp-ruta-' + componente.id">
                                                <button @click="goTo('visor-avance-hr', componente.rutaComponente)" class="btn btn-sm btn-default my-1"><i class="fa fa-eye">&nbsp;</i>Ver ruta componente</button>
                                            </div>
                                        </td>
                                    </tr>
    
    
                                    <tr v-if="!trabajosPendientes.programaciones || trabajosPendientes.programaciones.length === 0">
                                        <td colspan="4" class="text-center">No hay trabajos de programación pendiente</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Vista programacion -->
                </div>
                <div class="tab-pane fade" id="corte-mp" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="row mb-3">
                        
                        <div class="table-responsive col-lg-12">
                            <table class="table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Nombre del componente</th>
                                        <th>Cantidad</th>
                                        <th>Medidas de corte</th>
                                        <th>Materia prima</th>
                                        <th>Comentarios del enrutador</th>
                                        <th>Estatus del corte</th>
                                        <th>Ver ruta componente</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="cursor-pointer" v-for="(componente, index) in trabajosPendientes.cortes">
                                        <td class="bold">@{{componente.nombre}}</td>
                                        <td>
                                            <div class="col-lg-12 form-group text-left pl-1">
                                                <small class="bold">&nbsp;</small>
                                                <input class="form-control text-center" type="text" disabled v-model="componente.cantidad">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="row">
                                                <div class="col text-left form-group" v-if="componente.largo">
                                                    <small class="bold">Largo</small>
                                                    <input class="form-control text-center" type="text" disabled v-model="componente.largo">
                                                </div>
    
                                                <div class="col text-left form-group" v-if="componente.ancho">
                                                    <small class="bold">Ancho</small>
                                                    <input class="form-control text-center" type="text" disabled v-model="componente.ancho">
                                                </div>
    
                                                <div class="col text-left form-group" v-if="componente.espesor">
                                                    <small class="bold">Espesor</small>
                                                    <input class="form-control text-center" type="text" disabled v-model="componente.espesor">
                                                </div>
    
                                                <div class="col text-left form-group" v-if="componente.longitud">
                                                    <small class="bold">Longitud</small>
                                                    <input class="form-control text-center" type="text" disabled v-model="componente.longitud">
                                                </div>
    
                                                <div class="col text-left form-group" v-if="componente.diametro">
                                                    <small class="bold">Diametro</small>
                                                    <input class="form-control text-center" type="text" disabled v-model="componente.diametro">
                                                </div>
                                            </div>
                                        </td>
                                        <td>@{{componente.material_nombre}}</td>
                                        <td>@{{componente.comentarios}}</td>
                                        <td>
                                            <span v-if="componente.estatus_corte == 'paro'" class="py-2 w-100 badge badge-danger" style="font-size: 13px">EN PARO</span>
                                            <span v-if="componente.estatus_corte == 'inicial'" class="py-2 w-100 badge badge-warning" style="font-size: 13px">POR CORTAR</span>
                                            <span v-if="componente.estatus_corte == 'proceso'" class="py-2 w-100 badge badge-info" style="font-size: 13px">EN PROCESO...</span>
                                            <span v-if="componente.estatus_corte == 'pausado'" class="py-2 w-100 badge badge-dark" style="font-size: 13px">PAUSADO</span>
                                        </td>
                                        <td><button @click="goTo('visor-avance-hr', componente.rutaComponente)" class="btn btn-sm btn-default"><i class="fa fa-eye">&nbsp;</i>Ver ruta componente</button></td>
                                    </tr>
                                    <tr v-if="trabajosPendientes?.cortes?.length == 0">
                                        <td colspan="7" class="text-center">No hay trabajos de corte pendiente</td>

                                    </tr>
                                </tbody>
    
                            </table>
                        </div>
                    </div>
                    <!-- Vista corte de mp -->
                </div>
                <div class="tab-pane fade" id="fabricacion" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="row mb-3">
                        <div class="table-responsive col-lg-12">
                            <!-- Vista de Fabricaciones -->
                            <table class="table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Máquina</th>
                                        <th>Proceso Asignado</th>
                                        <th>Operadores asignados</th>
                                        <th>Cola de trabajos</th>
                                        <th>Comentarios de enrutamiento</th>
                                        <th>Fecha y hora de llegada del trabajo</th>
                                        <th>Ver ruta componente</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="cursor-pointer" v-for="(fabricaciones, index) in trabajosPendientes.fabricaciones" v-if="fabricaciones.componentes?.length > 0">
                                        <td class="bold">@{{fabricaciones.maquina_nombre}}</td>
                                        <td>@{{getTipoProcesoString(fabricaciones.proceso_maquina)}}</td>
    
                                        <td>
                                            <div v-if="fabricaciones.operadores.length > 0" v-for="operador in fabricaciones.operadores" :key="operador.id">
                                                @{{operador.nombre}}
                                            </div>
                                            <div>
                                                <span v-if="fabricaciones.operadores.length == 0">Sin operador</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="my-4" v-for="componente in fabricaciones.componentes" :key="'comp-nombre-' + componente.id">
                                                @{{ componente.nombre ?? 'Sin nombre' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="my-4" v-for="componente in fabricaciones.componentes" :key="'comp-comentarios-' + componente.id">
                                                @{{ componente.comentarios ?? 'Sin comentarios' }}
                                            </div>
                                        </td>
                                        <td>@{{fabricaciones.fecha}} Hrs.</td> <!-- PENDIENTEEEEEEE -->
                                        <td>
                                            <div class="my-1" v-for="componente in fabricaciones.componentes" :key="'comp-comentarios-' + componente.id">
                                                <button @click="goTo('visor-avance-hr', componente.rutaComponente)" class="btn btn-sm btn-default"><i class="fa fa-eye">&nbsp;</i>Ver ruta componente</button>
                                            </div>
    
                                        </td>
                                    </tr>
                                    <tr v-if="trabajosPendientes?.fabricaciones?.length == 0">
                                        <td colspan="3" class="text-center">No hay fabricaciones pendientes</td>
    
                                    </tr>
                                </tbody>
    
                            </table>
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
    const visorGeneral = new Vue({
        el: '#vue-app',
        data: {
            loading: true,
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
            },
            procesos: [
                // {id: 1, prioridad: 1, nombre: 'Cortar'},
                // {id: 2, prioridad: 2, nombre: 'Programar'},
                {
                    id: 3,
                    prioridad: 3,
                    nombre: 'Carear y/o Escuadrar'
                },
                {
                    id: 4,
                    prioridad: 4,
                    nombre: 'Maquinar'
                },
                {
                    id: 5,
                    prioridad: 5,
                    nombre: 'Tornear'
                },
                {
                    id: 6,
                    prioridad: 6,
                    nombre: 'Roscar/Rebabear'
                },
                // {id: 7, prioridad: 7, nombre: 'Templar'},
                {
                    id: 8,
                    prioridad: 8,
                    nombre: 'Rectificar'
                },
                {
                    id: 9,
                    prioridad: 9,
                    nombre: 'EDM'
                },
                {
                    id: 10,
                    prioridad: 10,
                    nombre: 'Cortar'
                },
                {
                    id: 11,
                    prioridad: 11,
                    nombre: 'Marcar'
                },
            ]
        },
        methods: {

            setActive(tab) {
                this.activeTab = tab;
            },
            goTo(ruta, query) {
                window.location.href = `/${ruta}${query}`;
            },
            async getDataInitial() {
                let t = this
                let response = await axios.get("/api/trabajos-pendientes-general");
                if (response.data.success) {
                    t.trabajosPendientes = response.data.data;
                }
                console.log(t.trabajosPendientes);
                t.loading = false;
            },
            getTipoProcesoString: function(id) {
                let t = this;
                let proceso = t.procesos.find(p => p.id == id);
                return proceso ? proceso.nombre : '';
            },
        },
        mounted() {
            let t = this;
            this.$nextTick(function() {
                t.getDataInitial();
            })
        }
    });
</script>


@endpush