@extends('layouts.app', [
'class' => '',
'elementActive' => 'dashboard'
])

<style>

</style>

@section('content')
<div id="vue-app" v-cloak>
    <div class="container-fluid mt-3">
        <div class="col-lg-12">
            <h2 class="bold my-0 py-1 mb-3 text-decoration-underline" style="letter-spacing: 2px">CENTRO DE HERRAMENTALES</h2>
        </div>
        <div class="col-lg-12">
            <!-- Nav -->
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="listado-tab" data-toggle="tab" data-target="#listado" type="button" role="tab" aria-controls="listado" aria-selected="true">LISTADO DE COMPONENTES</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="documentacion-p-tab" data-toggle="tab" data-target="#documentacion-p" type="button" role="tab" aria-controls="documentacion-p" aria-selected="false">DOCUMENTACIÓN DE PRODUCCIÓN</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="documentacion-t-tab" data-toggle="tab" data-target="#documentacion-t" type="button" role="tab" aria-controls="documentacion-t" aria-selected="false">DOCUMENTACIÓN TÉCNICA</button>
                </li>

                <div class="col-lg-2 d-flex ml-auto justify-content-end">
                    <div class="d-flex">
                        <div >
                            <button class="btn btn-md btn-default my-1">Ir a Visor de HR</button>
                        </div>
                    </div>                    
                </div>
            </ul>

            <div class="tab-content pt-4" id="myTabContent">
                <!-- Listado de componentes -->
                <div class="tab-pane fade show active" id="listado" role="tabpanel" aria-labelledby="home-tab">
                    <div class="row mb-3">
                        <div>
                            <h1>Listado de componentes</h1>
                        </div>
                    </div>
                </div>

                <!-- Documentacion de producción -->
                <div class="tab-pane fade" id="documentacion-p" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="row mb-3">
                        <div>
                            <h1>Documentos de producción</h1>
                        </div>
                    </div>
                </div>
                
                <!-- Documentación técnica -->
                <div class="tab-pane fade" id="documentacion-t" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="row mb-3">
                        <div >
                            <h1>Documentos tecnicos</h1>
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
        data: {},
        methods: {

        },
        mounted() {}
    });
</script>
@endpush