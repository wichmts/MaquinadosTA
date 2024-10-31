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
        <div class="row mt-3 px-2" v-cloak v-show="!cargando" >
            <div class="col-xl-12 d-flex align-items-end">
                <h2 class="bold my-0 py-1 text-decoration-underline" style="letter-spacing: 2px">CENTRO DE NOTIFICACIONES</h2>
            </div>
            <div class="col-xl-12 mt-3" >
                <table class="table table-hover" id="tabla">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 10%">Fecha</th>
                            <th style="width: 10%">Año</th>
                            <th style="width: 10%">Carpeta</th>
                            <th style="width: 10%">Proyecto</th>
                            <th style="width: 10%">Herramental</th>
                            <th style="width: 10%">Componente</th>
                            <th style="width: 50%">Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(n, index) in notificaciones" :key="index" class="cursor-pointer" @click="irNotificacion(n)">
                            <td>@{{n.fecha}} @{{n.hora}} </td>
                            <td>@{{n.anio}}</td>
                            <td>@{{n.cliente}}</td>
                            <td>@{{n.proyecto}}</td>
                            <td>@{{n.herramental}}</td>
                            <td>@{{n.componente}}</td>
                            <td>@{{n.descripcion}}</td>
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
            notificaciones: [],
        },
        methods:{
            irNotificacion(notificacion){
                window.location.href = `${notificacion.url_base}?a=${notificacion.anio_id}&c=${notificacion.cliente_id}&p=${notificacion.proyecto_id}&h=${notificacion.herramental_id}&c=${notificacion.componente_id}`
            },
            async fetchNotificaciones() {
                $('#tabla').dataTable().fnDestroy();
                this.cargando = true

                try {
                    const response = await axios.get(`/api/notificaciones`);
                    this.notificaciones = response.data.notificaciones;
                } catch (error) {
                    console.error('Error fetching notificaciones:', error);
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
            await t.fetchNotificaciones();
        }                
    })
    </script>        
@endpush