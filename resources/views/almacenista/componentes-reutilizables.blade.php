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
        <div class="col-lg-12" v-show="cargando">
            <div style="margin-top: 200px; max-width: 100% !important; margin-bottom: auto; text-align:center; letter-spacing: 2px">
                <h5 class="mb-5">CARGANDO...</h5>
                <div class="loader"></div>
            </div>
        </div>
        <div class="row mt-3 px-5" v-cloak v-show="!cargando" >
            <div class="col-lg-9 d-flex align-items-start">
                <h2 class="bold my-0 py-1 " style="letter-spacing: 2px">COMPONENTES REUTILIZABLES</h2>
            </div>
            <div class="col-lg-3 d-flex align-items-end">
                <button class="btn btn-block" @click="guardarCambios"><i class="fa fa-save"></i> GUARDAR CAMBIOS</button>
            </div>
            <div class="col-lg-12 mt-3" >
                <table class="table" id="tabla">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 20%" >Componente</th>
                            <th style="width: 35%" >Descripción</th>
                            <th style="width: 20%" >Proveedor / Material</th>
                            <th style="text-transform: none !important; width: 15%">Disponibles</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="c in componentes">
                            <td class="bold">@{{c.nombre}}</td>
                            <td>@{{c.descripcion}}</td>
                            <td>@{{c.proveedor}}</td>
                            <td>
                                <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <button class="input-group-text py-0 cursor-pointer" style="background-color: #e3e3e3 !important" @click="c.cantidad_reutilizable > 0 ? c.cantidad_reutilizable-- : c.cantidad_reutilizable"> <i class="fa fa-minus"></i> &nbsp;&nbsp;</button>
                                        </div>
                                        <input type="number" v-model="c.cantidad_reutilizable" class="form-control text-center px-1 py-1" step="1">
                                        <div class="input-group-append">
                                            <button class="input-group-text py-0 cursor-pointer" style="background-color: #e3e3e3 !important" @click="c.cantidad_reutilizable++"> &nbsp;&nbsp;<i class="fa fa-plus"></i> </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
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
            cargando: false,
            componentes: [],
        },
        methods:{
            async guardarCambios(){
                this.cargando = true;
                try {
                    const response = await axios.post('/api/componentes-reutilizables', this.componentes);
                    if (response.data.success) {
                        swal('Éxito', 'Cambios guardados correctamente.', 'success');
                        this.fetchComponentes();
                    } else {
                        swal('Error', response.data.message || 'Ocurrió un error al guardar los cambios.', 'error');
                    }
                } catch (error) {
                    console.error('Error saving componentes:', error);
                    swal('Error', 'Ocurrió un error al guardar los cambios.', 'error');
                } finally {
                    this.cargando = false;
                }
            },
            async fetchComponentes() {
                this.cargando = true

                $('#tabla').DataTable().destroy();
                try {
                    const response = await axios.get(`/api/componentes-reutilizables`);
                    this.componentes = response.data.componentes;
                } catch (error) {
                    console.error('Error fetching componentes:', error);
                } finally {
                    this.cargando = false;
                    Vue.nextTick(function(){
                        $('#tabla').DataTable({
                            paging: true,
                            searching: true,
                            pageLength: 20,
                            lengthMenu: [[20, 30, 50, 100, -1], [20, 30, 50, 100, "All"]],  
                            ordering: true,
                            info: true,
                            language: {
                                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
                            }
                        });
                    });
                }
            },
        },
        mounted: async function () {
            let t = this;
            await t.fetchComponentes();
        }

                
    })

    </script>



        
@endpush