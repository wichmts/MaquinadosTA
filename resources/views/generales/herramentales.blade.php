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
        <div class="row mt-3 px-4" v-cloak v-show="!cargando" >
            <div class="col-xl-12 d-flex align-items-end">
                <h2 class="bold my-0 py-1 text-decoration-underline" style="letter-spacing: 2px">LISTADO DE HERRAMENTALES</h2>
            </div>
            <div class="col-xl-12 mt-3" >
                <table class="table table-hover" id="tabla">
                    <thead class="thead-light">
                        <tr>
                            <th>Año</th>
                            <th>Carpeta</th>
                            <th>Proyecto</th>
                            <th>Herramental</th>
                            <th>Fecha creación</th>
                            <th>Fecha finalización</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(n, index) in herramentales" :key="index" class="cursor-pointer" @click="goToHerramental(n.rutaHerramental)">
                            <td>@{{n.anio}}</td>
                            <td>@{{n.cliente}}</td>
                            <td>@{{n.proyecto}}</td>
                            <td>@{{n.nombre}}</td>
                            <td>@{{n.fecha_creacion }} </td>
                            <td>@{{n.fecha_finalizado }} </td>
                        </tr>
                    </tbody>
                </table>
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
            herramentales: [],
        },
        methods:{
            goToHerramental(ruta){
                window.location.href = '/visor-avance-hr/' + ruta;
            },
            async fetchherramentales() {
                $('#tabla').dataTable().fnDestroy();
                this.cargando = true
                try {
                    const response = await axios.get(`/api/herramentales`);
                    this.herramentales = response.data.herramentales;
                } catch (error) {
                    console.error('Error fetching herramentales:', error);
                } finally {
                    this.cargando = false;
                    Vue.nextTick(function(){
                        $('#tabla').DataTable({
                            "searching": true,
                            "ordering": true,
                            "order": [[0, 'desc']], 
                            "columnDefs": [{
                                orderable: false,
                                targets: "no-sort"
                            }],
                            language: {
                                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
                            }
                        });
                    })
                }
            },  
        },
        mounted: async function () {
            let t = this;
            await t.fetchherramentales();
        }                
    })
    </script>        
@endpush