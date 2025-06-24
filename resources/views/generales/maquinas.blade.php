@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'dashboard'
])

<style>
    .no-background:hover{
        background-color: rgb(238, 238, 238) !important;
    }
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
     .nav-link:hover{
        cursor: pointer !important;
    }
    .nav-pills{
        font-size: 15px !important;
    }
    .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
        color: white !important;
        background-color: #17395d;
    }
    .underline{
        border-radius: 0px !important;
        border-bottom: 1px solid rgb(231, 231, 231);
    }

    /*GOOGLE MAPS AUTOCOMPLETE*/
    .pac-container { z-index: 10000 !important; }

    .table .form-check label .form-check-sign::before, .table .form-check label .form-check-sign::after {
      top: -5px !important;
    }

    .form-control{
         -webkit-autocomplete: off !important;
            -moz-autocomplete: off !important;
            autocomplete: off !important;
    }

    .roleSeleccionado {
        background-color: #c0d340 !important;
        color: black !important;
    }

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
        <div class="col-md-12" v-show="cargando">
            <div style="margin-top: 200px; max-width: 100% !important; margin-bottom: auto; text-align:center; letter-spacing: 2px">
                <h5 class="mb-5">CARGANDO...</h5>
                <div class="loader"></div>
            </div>
        </div>
        <div class="card shadow col-md-12" v-cloak v-show="!cargando">
            <div class="card-header border-0">
                <div class="row align-items-center">
                    <div class="col-md-10   mb-0">
                        <h3 class="bold my-0 py-1" style="letter-spacing: 1px;">ADMINISTRAR MAQUINAS DE TRABAJO</h3>
                    </div>
                    <div class="form-group col-md-2 col-xs-12 mt-4">
                        <button class="btn btn-normal col-md-12" @click="reiniciarModal"><i class="fa fa-circle-plus"></i> NUEVA MAQUINA</button>
                    </div>

                    <div class="col-md-12 table-responsive">            
                        <table class="table align-items-center" id="tabla">
                            <thead class="thead-light">
                                <tr>
                                    <th style="text-transform: none !important"  scope="col">Nombre de maquina</th>
                                    <th style="text-transform: none !important"  scope="col">Tipo de proceso</th>
                                    <th style="text-transform: none !important"  scope="col">¿Requiere programa?</th>
                                    <th style="text-transform: none !important"  scope="col" class="no-sort">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(p, index) in maquinas" :key="index">
                                    <td style="width: 30%">@{{p.nombre}}</td>
                                    <td style="width: 30%">@{{getTipoProcesoString(p.tipo_proceso)}}</td>
                                    <td style="width: 20%">@{{p.requiere_programa ? 'SI' : 'NO'}}</td>
                                    <td style="width: 20%">
                                        <div class="btn-group" style="border: 2px solid #121935; border-radius: 10px !important">
                                             <button class="btn btn-sm btn-link actions" @click="editar(index)" data-toggle="tooltip" data-placement="bottom" title="Editar información">
                                                <i class="fa fa-edit"></i>   
                                            </button>  
                                             <button class="btn btn-sm btn-link actions" @click="eliminar(p.id)" data-toggle="tooltip" data-placement="bottom" title="Eliminar">
                                                <i class="fa fa-trash"></i>   
                                            </button>              
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>  
            <div class="card-footer py-4"></div>
        </div>

        <!-- Modal nueva -->
        <div class="modal fade" id="modalMaquina" tabindex="-1" aria-labelledby="modalMaquinaLabel" aria-hidden="true">
            <div class="modal-dialog" style="min-width: 30%;">
                <div class="modal-content" >
                    <div class="modal-header">
                        <h3 class="modal-title" id="modalMaquinaLabel">@{{maquina.id ? 'Editar maquina' : 'Crear nuevo maquina'}} </h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row ">
                            <div class="form-group col-md-12">
                                <label class="bold">Nombre de la maquina<span style="color: red">*</span></label>
                                <input placeholder="Nombre de la maquina..." class=" form-control" type="text" v-model="maquina.nombre"/>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="bold">Tipo de proceso <span style="color: red">*</span></label>
                                <select v-model="maquina.tipo_proceso" class="form-control">
                                    <option v-for="p in procesos" :value="p.id"> @{{p.nombre}}</option>
                                </select>
                            </div>
                            <div class="col-lg-12 form-group">
                                <div class="checkbox-wrapper-19">
                                    <input type="checkbox" v-model="maquina.requiere_programa" /> &nbsp;&nbsp;
                                    <span style="font-size: 12px !important" class="bold">¿La máquina requiere programa?</span>
                                </div>
                            </div>
                        </div>  
                    </div>
                    <div class="modal-footer text-right">
                         <button class="btn btn-secondary" v-if="!loading_button" type="button" @click="guardar"><i class="fa fa-save"></i> Guardar</button>
                        <button class="btn btn-secondary" type="button" disabled v-else><i class="fa fa-spinner spin"></i> CARGANDO...</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
{{--  --}}

@push('scripts')

    <script type="text/javascript">
        var app = new Vue({
        el: '#vue-app',
        data: {
            loading_button: false,
            cargando: true,
            maquinas: [],
            maquina: {
                nombre: '',
                tipo_proceso: '',   
                requiere_programa: true,            
            },
            procesos:  [
                    // {id: 1, prioridad: 1, nombre: 'Cortar'},
                    // {id: 2, prioridad: 2, nombre: 'Programar'},
                    {id: 3, prioridad: 3, nombre: 'Carear'},
                    {id: 4, prioridad: 4, nombre: 'Maquinar'},
                    {id: 5, prioridad: 5, nombre: 'Tornear'},
                    {id: 6, prioridad: 6, nombre: 'Roscar/Rebabear'},
                    // {id: 7, prioridad: 7, nombre: 'Templar'},
                    {id: 8, prioridad: 8, nombre: 'Rectificar'},
                    {id: 9, prioridad: 9, nombre: 'EDM'},
                    {id: 10, prioridad: 10, nombre: 'Cortar'},
                    {id: 11, prioridad: 11, nombre: 'Marcar'},
                ]
        },
        methods:{ 
            getTipoProcesoString: function(id){
                let t = this;
                let proceso = t.procesos.find(p => p.id == id);
                return proceso ? proceso.nombre : '';
            },  
            eliminar: function(id){
                let t = this;
                swal({
                    title: "¿Eliminar maquina?",
                    text: "Una ves eliminado, no podra recuperar su información.",
                    icon: "warning",
                    buttons: ['Cancelar', 'Eliminar'],
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        axios.delete('/api/maquina/' + id).then(response => {
                            if(response.data.success){
                                swal('Correcto!', response.data.message, 'success');
                                t.getData();
                            }else{
                                swal('Lo sentimos!', response.data.message, 'error');
                            }
                    }).catch(e => {
                        console.log(e);
                    });
                    }
                });
            },
            guardar: function(){
                let t = this;
                if(t.maquina.nombre == '' || t.maquina.tipo_proceso == '')
                {
                    swal('Campos obligatorios', 'Los campos marcados con asterisco son obligatorios.', 'info');
                    return;
                }
                t.loading_button = true;

                if(t.maquina.id)
                {
                    axios.put('/api/maquina/' + t.maquina.id, t.maquina).then(response => {
                        if(response.data.success){
                            $('#modalMaquina').modal('toggle');
                            swal('Correcto!', response.data.message, 'success');
                            t.getData();
                            t.loading_button = false;

                        }else{
                            swal('Lo sentimos!', response.data.message, 'error');
                            t.loading_button = false;
                        }
                    }).catch(e => {
                        console.log(e);
                        t.loading_button = false;

                    });
                }else{
                    axios.post('/api/maquina', t.maquina).then(response => {
                        if(response.data.success){
                            $('#modalMaquina').modal('toggle');
                            swal('Correcto!', response.data.message, 'success');
                            t.getData();
                            t.loading_button = false;

                        }else{
                            swal('Lo sentimos!', response.data.message, 'error');
                            t.loading_button = false;
                        }
                    }).catch(e => {
                        console.log(e);
                        t.loading_button = false;
                    });
                }         
            },
            reiniciarModal: function(){
                let t = this;
                 t.maquina = {
                    nombre: '',
                    tipo_proceso: '',
                    requiere_programa: true,            
                };
                 Vue.nextTick(function () {
                    $('#modalMaquina').modal();
                });

            },
            editar: function(index){
                let t = this;
                t.maquina =  JSON.parse(JSON.stringify(t.maquinas[index]));
                Vue.nextTick(function () {
                    $('#modalMaquina').modal();
                });
            },
            getData: function(){
                let t = this;
                t.cargando = true;
                $('#tabla').dataTable().fnDestroy();

                axios.get('api/maquinas').then(response => {
                    t.maquinas = response.data.maquinas;
                    t.cargando = false;
                }).catch(e => {
                    console.log(e);
                });
            }
        },
        mounted: function () {
            this.$nextTick(function () {
                let t = this;
                t.getData();
            })
        }
                
    })

    </script>



        
@endpush