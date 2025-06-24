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
                <div class="row align-items-start">
                    <div class="col-md-6 table-responsive">            
                        <div class="row">
                            <div class="col-md-8">
                                <h3 class=" mt-2" style="letter-spacing: 2px">COSTOS POR PUESTO DE TRABAJO</h3>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-normal col-md-12" @click="reiniciarModal"><i class="fa fa-circle-plus"></i> NUEVO PUESTO</button>
                            </div>
                            <div class="col-md-12" style="max-height: 70vh; overflow-y:auto !important">
                                 <table class="table align-items-center" id="tabla">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="text-transform: none !important"  scope="col">Nombre de puesto</th>
                                            <th style="text-transform: none !important"  scope="col">Costo por hora</th>
                                            <th style="text-transform: none !important"  scope="col" class="no-sort">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(p, index) in puestos" :key="index">
                                            <td style="width: 40%" class="bold">@{{p.nombre}}</td>
                                            <td style="width: 30%">@{{p.pago_hora | currency }}</td>
                                            <td style="width: 30%">
                                                <div class="btn-group" style="border: 2px solid #121935; border-radius: 10px !important">
                                                    <button class="btn btn-sm btn-link actions" @click="editar(p.id)" data-toggle="tooltip" data-placement="bottom" title="Editar información">
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
                     <div class="col-md-6 table-responsive">            
                        <div class="row">
                            <div class="col-md-12 mb-1 mt-2">
                                <h3 class="" style="letter-spacing: 2px">COSTOS POR MAQUINA</h3>
                            </div>
                            <div class="col-md-12" style="max-height: 70vh; overflow-y:auto !important">
                                <table class="table align-items-center" id="tabla">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="text-transform: none !important"  scope="col">Maquina</th>
                                            <th style="text-transform: none !important"  scope="col">Costo por hora</th>
                                            <th style="text-transform: none !important"  scope="col" class="no-sort">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(p, index) in maquinas" :key="index">
                                            <td style="width: 40%" class="bold">@{{p.nombre}}</td>
                                            <td style="width: 30%">@{{p.pago_hora | currency }}</td>
                                            <td style="width: 30%">
                                                <div class="btn-group" style="border: 2px solid #121935; border-radius: 10px !important">
                                                     <button class="btn btn-sm btn-link actions" @click="editarMaquina(p.id)" data-toggle="tooltip" data-placement="bottom" title="Editar información">
                                                        <i class="fa fa-edit"></i>   
                                                    </button>              
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>  
            <div class="card-footer py-4"></div>
        </div>

        <!-- Modal nueva -->
        <div class="modal fade" id="modalPuesto" tabindex="-1" aria-labelledby="modalPuestoLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content" >
                    <div class="modal-header">
                        <h3 class="modal-title" id="modalPuestoLabel">@{{puesto.id ? 'Editar puesto' : 'Crear nuevo puesto'}} </h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row ">
                            <div class="form-group col-md-12">
                                <label class="bold">Nombre de la puesto <span style="color: red">*</span></label>
                                <input placeholder="Nombre de la puesto..." class=" form-control" type="text" v-model="puesto.nombre"/>
                            </div>
                           <div class="form-group col-md-12">
                                <label class="bold">Costo por hora <span style="color: red">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="number" step="any" placeholder="Costo por hora..." class="form-control" v-model="puesto.pago_hora" />
                                    <div class="input-group-append">
                                        <span class="input-group-text"> &nbsp;MXN</span>
                                    </div>
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
        <div class="modal fade" id="modalMaquina" tabindex="-1" aria-labelledby="modalMaquinaLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content" >
                    <div class="modal-header">
                        <h3 class="modal-title" id="modalMaquinaLabel">Editar costo maquina </h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row ">
                            <div class="form-group col-md-12">
                                <label class="bold">Nombre de la maquina <span style="color: red">*</span></label>
                                <input placeholder="Nombre de la maquina..." class=" form-control" type="text" v-model="maquina.nombre" disabled />
                            </div>
                           <div class="form-group col-md-12">
                                <label class="bold">Costo por hora <span style="color: red">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="number" step="any" placeholder="Costo por hora..." class="form-control" v-model="maquina.pago_hora" />
                                    <div class="input-group-append">
                                        <span class="input-group-text"> &nbsp;MXN</span>
                                    </div>
                                </div>
                            </div>
                        </div>  
                    </div>
                    <div class="modal-footer text-right">
                         <button class="btn btn-secondary" v-if="!loading_button" type="button" @click="guardarMaquina"><i class="fa fa-save"></i> Guardar</button>
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
            puestos: [],
            maquinas: [],
            puesto: {
                nombre: '',
                pago_hora: 0,   
            },
            maquina: {
                nombre: '',
                pago_hora: 0,   
            },
        },
        methods:{ 
            eliminar: function(id){
                let t = this;
                swal({
                    title: "¿Eliminar puesto?",
                    text: "Una ves eliminado, no podra recuperar su información.",
                    icon: "warning",
                    buttons: ['Cancelar', 'Eliminar'],
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        axios.delete('/api/puesto/' + id).then(response => {
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
                if (!t.puesto.nombre.trim() || t.puesto.pago_hora === '' || isNaN(t.puesto.pago_hora) || parseFloat(t.puesto.pago_hora) <= 0) {
                    swal(
                        'Campos obligatorios',
                        'Los campos marcados con asterisco son obligatorios y el pago por hora debe ser mayor a cero.',
                        'info'
                    );
                    return;
                }
                t.loading_button = true;
                if(t.puesto.id)
                {
                    axios.put('/api/puesto/' + t.puesto.id, t.puesto).then(response => {
                        if(response.data.success){
                            $('#modalPuesto').modal('toggle');
                            swal('Correcto!', 'El puesto se ha editado correctamente.', 'success');
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
                    axios.post('/api/puesto', t.puesto).then(response => {
                        if(response.data.success){
                            $('#modalPuesto').modal('toggle');
                            swal('Correcto!', 'El puesto se ha guardado correctamente.', 'success');
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
            guardarMaquina: function(){
                let t = this;
                if (! t.maquina.pago_hora === '' || isNaN(t.maquina.pago_hora) || parseFloat(t.maquina.pago_hora) <= 0) {
                    swal(
                        'Campos obligatorios',
                        'Los campos marcados con asterisco son obligatorios y el pago por hora debe ser mayor a cero.',
                        'info'
                    );
                    return;
                }
                t.loading_button = true;

                axios.put('/api/maquina/costos/' + t.maquina.id, t.maquina).then(response => {
                    if(response.data.success){
                        $('#modalMaquina').modal('toggle');
                        swal('Correcto!', 'La maquina se ha guardado correctamente.', 'success');
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
            },
            reiniciarModal: function(){
                let t = this;
                 t.puesto = {
                    nombre: '',
                    pago_hora: 0,
                };
                 Vue.nextTick(function () {
                    $('#modalPuesto').modal();
                });

            },
            editar: function(id){
                let t = this;
                t.puesto =  JSON.parse(JSON.stringify(t.puestos.find(p => p.id == id)));
                Vue.nextTick(function () {
                    $('#modalPuesto').modal();
                });
            },
            editarMaquina: function(id){
                let t = this;
                t.maquina =  JSON.parse(JSON.stringify(t.maquinas.find(p => p.id == id)));
                Vue.nextTick(function () {
                    $('#modalMaquina').modal();
                });
            },
            getData: function(){
                let t = this;
                t.cargando = true;

                axios.get('api/puestos').then(response => {
                    t.puestos = response.data.puestos;
                }).catch(e => {
                    console.log(e);
                });

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