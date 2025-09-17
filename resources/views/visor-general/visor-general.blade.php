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

    .container-general {
        margin: 30px;
    }
</style>

@section('content')
<div id="vue-app" v-cloak>
    <div class="container-general">
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
            <div class="tab-content" id="myTabContent">

                <div class="tab-pane fade show active" id="enrutamiento" role="tabpanel" aria-labelledby="home-tab">
                    <!-- Vista enrutamiento -->
                    <div class="table-responsive card shadow">
                        <table class="table align-items-center">
                            <thead>
                                <tr>
                                    <th>Nombre del componente</th>
                                    <th>Cantidad</th>
                                    <th>Fecha Liberacion</th>
                                    <th>Comentarios del enrutador</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="cursor-pointer" v-for="(componente, index) in trabajosPendientes.enrutamiento" @click="goTo('enrutador', componente.rutaComponente)">
                                    <td class="bold">@{{componente.nombre}}</td>
                                    <td>@{{componente.cantidad}}</td>
                                    <td>@{{componente.fecha_cargado}}Hrs.</td>
                                    <td>@{{componente.comentarios}}</td>
                                </tr>
                                <tr v-if="trabajosPendientes?.enrutamiento?.length == 0">
                                    <td colspan="3" class="text-center">No hay trabajo pendiente</td>
                                </tr>
                            </tbody>

                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="programacion" role="tabpanel" aria-labelledby="profile-tab">
                    <!-- Vista programacion -->
                    <div class="table-responsive card shadow">
                        <table class="table align-items-center">
                            <thead>
                                <tr>
                                    <th>Nombre del programador</th>
                                    <th>Cola de trabajos activos</th>
                                    <th>Comentarios del enrutador</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(programador, pIndex) in trabajosPendientes.programaciones" :key="'prog-' + programador.programador_id">
                                    <td class="bold">@{{ programador.programador_nombre }}</td>

                                    <td>
                                        <div v-for="componente in programador.componentes" :key="'comp-nombre-' + componente.id">
                                            @{{ componente.nombre }}
                                        </div>
                                    </td>

                                    <td>
                                        <div v-for="componente in programador.componentes" :key="'comp-comentarios-' + componente.id">
                                            @{{ componente.comentarios ?? 'Sin comentarios' }}
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
                <div class="tab-pane fade" id="corte-mp" role="tabpanel" aria-labelledby="profile-tab">
                    <!-- Vista corte de mp -->
                    <div class="table-responsive card shadow">
                        <table class="table align-items-center">
                            <thead>
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
                                <tr class="cursor-pointer" v-for="(componente, index) in trabajosPendientes.cortes" @click="goTo('enrutador', componente.rutaComponente)">
                                    <td class="bold">@{{componente.nombre}}</td>
                                    <td>
                                        <div class="col-lg-12 form-group text-left pl-1">
                                            <small class="bold">&nbsp;</small>
                                            <input class="form-control text-center" type="text" disabled v-model="componente.cantidad">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="row">
                                            <div class="col-lg-4 text-left form-group pl-1" v-if="componente.largo">
                                                <small class="bold">Largo</small>
                                                <input class="form-control text-center" type="text" disabled v-model="componente.largo">
                                            </div>

                                            <div class="col-lg-4 text-left form-group pl-1" v-if="componente.ancho">
                                                <small class="bold">Ancho</small>
                                                <input class="form-control text-center" type="text" disabled v-model="componente.ancho">
                                            </div>

                                            <div class="col-lg-4 text-left form-group pl-1" v-if="componente.espesor">
                                                <small class="bold">Espesor</small>
                                                <input class="form-control text-center" type="text" disabled v-model="componente.espesor">
                                            </div>

                                            <div class="col-lg-4 text-left form-group pl-1" v-if="componente.longitud">
                                                <small class="bold">Longitud</small>
                                                <input class="form-control text-center" type="text" disabled v-model="componente.longitud">
                                            </div>

                                            <div class="col-lg-4 text-left form-group pl-1" v-if="componente.diametro">
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
                                    <td><button class="btn btn-sm btn-link actions"><i class="fa fa-eye">&nbsp;</i>Ver ruta componente</button></td>
                                </tr>
                                <tr v-if="trabajosPendientes?.cortes?.length == 0">
                                    <td colspan="3" class="text-center">No hay trabajos de programación pendiente</td>

                                </tr>
                            </tbody>

                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="fabricacion" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="table-responsive card shadow">
                        <table class="table align-items-center">
                            <thead>
                                <tr>
                                    <th>Máquina</th>
                                    <th>Proceso Asignado</th>
                                    <th>Operadores asignados</th>
                                    <th>Cola de trabajos</th>
                                    <th>Comentarios de enrutamiento</th>
                                    <th>Fecha y hora de llegada del trabajo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="cursor-pointer" v-for="(fabricaciones, index) in trabajosPendientes.fabricaciones" @click="goTo('fabriaciones', componente.rutaComponente)">
                                    <td class="bold">@{{fabricaciones.maquina_nombre}}</td>
                                    <td>@{{fabricaciones.material_nombre}}</td>
                                    <td>
                                        <div v-for="operador in fabricaciones.operadores" :key="operador.id">
                                            @{{operador.nombre ? operador.nombre : 'Sin operador asignado'}}
                                        </div>
                                        </td>
                                    <td>@{{fabricaciones.componente.nombre}}</td>
                                    <td>@{{fabricaciones.componente.comentarios}}</td>
                                </tr>
                                <tr v-if="trabajosPendientes?.cortes?.length == 0">
                                    <td colspan="3" class="text-center">No hay trabajos de programación pendiente</td>

                                </tr>
                            </tbody>

                        </table>
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
            }
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