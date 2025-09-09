@extends('layouts.app', [
'class' => '',
'elementActive' => 'dashboard'
])

<style>
    .navBtn {
        font-weight: 600;
        font-size: 0.8571em ;
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
        <!-- Nav -->
        <nav class="nav nav-pills flex-column flex-sm-row">
            <a class="flex-sm-fill text-sm-center nav-link navBtn" :class="{ btn: activeTab === 'enrutamiento'}" @click="setActive('enrutamiento')">Enrutamiento</a>
            <a class="flex-sm-fill text-sm-center nav-link navBtn" :class="{ btn: activeTab === 'programación'}" @click="setActive('programación')">Programación</a>
            <a class="flex-sm-fill text-sm-center nav-link navBtn" :class="{ btn: activeTab === 'corte'}" @click="setActive('corte')">Corte de MP</a>
            <a class="flex-sm-fill text-sm-center nav-link navBtn" :class="{ btn: activeTab === 'fabricaciones'}" @click="setActive('fabricaciones')">Fabricaciones por Máquina</a>
        </nav>
        <!-- Vistas -->

        <!-- Vista enrutamiento -->
        <div class="table-responsive card shadow" v-show="activeTab === 'enrutamiento'">
            <table class="table align-items-center">
                <thead>
                    <tr>
                        <th>Nombre del componente</th>
                        <th>Cola de trabajos activos</th>
                        <th>Comentarios del enrutador</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- traer enrutamiento -->
                </tbody>

            </table>
        </div>

        <!-- Vista programacion -->
        <div class="table-responsive card shadow" v-show="activeTab === 'programación'">
            <table class="table align-items-center">
                <thead>
                    <tr>
                        <th>Nombre del componente</th>
                        <th>Cola de trabajos activos</th>
                        <th>Comentarios del enrutador</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- traer programacion -->
                </tbody>

            </table>
        </div>

        <!-- Vista corte de mp -->
        <div class="table-responsive card shadow" v-show="activeTab === 'corte'">
            <table class="table align-items-center">
                <thead>
                    <tr>
                        <th>Medidas de corte</th>
                        <th>Materia prima</th>
                        <th>Comentarios del enrutador</th>
                        <th>Estatus del corte</th>
                        <th><button class="btn btn-sm btn-link actions"><i class="fa fa-eye">&nbsp;</i>Ver ruta componente</button></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- traer corte mp -->
                </tbody>

            </table>
        </div>

        <!-- Vista fabricacion por máquina -->
        <div class="table-responsive card shadow" v-show="activeTab === 'fabricaciones'">
            <table class="table align-items-center">
                <thead>
                    <tr>
                        <th>Máquina</th>
                        <th>Proceso Asignado</th>
                        <th>Operadores asignados</th>
                        <th>Cola de trabajos</th>
                        <th>Comentarios de enrutamiento</th>
                        <th>Fecha y hora de llegada del componente a la estacion</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- traer fabricacion por maquina -->
                </tbody>

            </table>
        </div>

    </div>
</div>

@endsection

@push('scripts')

<script type="text/javascript">
    const visorGeneral = new Vue({
        el: '#vue-app',
        data: {
            activeTab: 'enrutamiento'
        },
        methods: {

            setActive(tab) {
                this.activeTab = tab;
            }
        }
    });
</script>


@endpush