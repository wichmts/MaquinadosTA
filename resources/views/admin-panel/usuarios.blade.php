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
                    <div class="col-md-6 mb-0">
                        <h3 class="bold my-0 py-1" style="letter-spacing: 2px;color: #234666 !important">USUARIOS Y VENDEDORES<br><small>USERS & SALES REPRESENTATIVES</small></h3>
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="">Filtrar por tipo de usuario:</label>
                        <select v-model="tipo_usuario" class="form-control" @change="getData">
                            <option value="-1">Todos los usuarios</option>
                            <option value="ADMINISTRADOR">Aministradores</option>
                            <option value="VENDEDOR">Vendedores</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3 col-xs-12 mt-4">
                        <button class="btn btn-normal col-md-12" @click="reiniciarModal"><i class="fa fa-circle-plus"></i> NUEVO USUARIO</button>
                    </div>

                    <div class="col-md-12 table-responsive">            
                        <table class="table align-items-center" id="tabla">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">Correo electronico</th>
                                    <th scope="col">Tipo de usuario</th>
                                    <th scope="col">Permisos del usuario</th>
                                    <th scope="col">Estatus</th>
                                    <th scope="col" class="no-sort">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(p, index) in usuarios" :key="index">
                                    <td style="width: 15%">@{{p.nombre_completo}}</td>
                                    <td style="width: 15%">@{{p.email}}</td>
                                    <td style="width: 10%">
                                        <span style="font-size: 12px" class="badge-info badge badge-pill mx-1 my-1 py-2 px-3"> @{{p.role}}</span>
                                    </td>
                                    <td style="width: 40%">
                                        <span style="font-size: 12px; background-color: #234666 !important" class="badge-dark badge badge-pill mx-1 my-1 py-2 px-3" v-for="per in p.permisos"> @{{per}}</span>
                                    </td>
                                    <td style="width: 10%">
                                        <span style="font-size: 12px" class="badge badge-success py-2 px-4" v-if="p.active"><i class="fa fa-unlock"></i> ACTIVO </span>
                                        <span style="font-size: 12px" class="badge badge-danger py-2 px-4" v-else><i class="fa fa-lock"></i> INACTIVO</span>
                                    </td>
                                    <td style="width: 10%">
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
        <div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
            <div class="modal-dialog" style="min-width: 70%;">
                <div class="modal-content" >
                    <div class="modal-header">
                        <h3 class="modal-title" id="modalUsuarioLabel">@{{usuario.id ? 'Editar usuario' : 'Crear nuevo usuario'}} </h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row ">
                            <div class="form-group col-md-3">
                                <label for="">Nombre(s) <span style="color: red">*</span></label>
                                <input placeholder="Nombre(s) del usuario..." class=" form-control" type="text" v-model="usuario.nombre"/>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="">Apellido paterno <span style="color: red">*</span></label>
                                <input placeholder="Apellidos del usuario..." class=" form-control" type="text" v-model="usuario.ap_paterno"/>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="">Apellido materno <span style="color: red">*</span></label>
                                <input placeholder="Apellidos del usuario..." class=" form-control" type="text" v-model="usuario.ap_materno"/>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="">Estatus <span style="color: red">*</span></label>
                                <select v-model="usuario.active" class="form-control">
                                    <option :value="1">Activo</option>
                                    <option :value="0">Inactivo</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="">Correo electronico <span style="color: red">*</span></label>
                                <input placeholder="nombre@mail.com" class="form-control" type="email" v-model="usuario.email"/>
                            </div>
                            <div class="form-group col-md-3">
                                <label for=""><span v-if="typeof(usuario.id) == 'undefined'">Contraseña </span><span v-else>Cambiar contraseña </span><span style="color: red">*</span></label>
                                <input class="form-control" type="text" style="font-weight: bold; letter-spacing: 1px" v-model="usuario.password"/>
                            </div>
                            <div class="form-group col-md-3">
                                <label for=""><span v-if="typeof(usuario.id) == 'undefined'">Confirmar contraseña </span><span v-else>Confirmar nueva contraseña </span>  <span style="color: red">*</span></label>
                                <input class="form-control" type="text" style="font-weight: bold; letter-spacing: 1px" v-model="usuario.password_confirmation"/>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="">Tipo de usuario: <span style="color: red">*</span></label>
                                <select class="form-control" v-model="usuario.role" @change="cambiarRole">
                                    <option value="ADMINISTRADOR">Administrador</option>
                                    <option value="VENDEDOR">Vendedor</option>
                                </select>
                            </div>
                        </div>  
                        <hr>
                        <div class="row">
                            <h5 class="col-md-12">Asignarle permisos a este usuario</h5>        
                             <div class="table-responsive col-md-12">
                                <table class="table table-condensed">
                                    <thead class="thead-light">
                                    <tr>
                                        <th style="width: 20%">Permiso</th>
                                        <th style="width: 60%">Descripcion</th>
                                        <th style="width: 20%">¿Otorgar permiso?</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(obj, index) in permisos">
                                        <td><strong>@{{obj.title}}</strong></td>
                                        <td>@{{obj.description}}</td>
                                        <td>
                                        <div class="form-group">
                                            <div class="form-check">
                                            <label class="form-check-label" style="font-size: 10px">
                                                <input type="checkbox" class="form-check-input" v-model="obj.value">
                                                <span class="form-check-sign"></span>
                                            </label>
                                            </div>
                                        </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div> 
                        <hr>
                        <div class="row">
                            <div class="col-md-8">
                                <small>Se enviara un correo electronico al usuario con la información de acceso a su cuenta, una vez dentro puede modificar sus datos basicos y contraseña.</small>
                            </div>
                            <div class="col-md-4 text-right">
                                 <button class="btn btn-secondary" v-if="!loading_button" type="button" @click="guardar"><i class="fa fa-save"></i> Guardar</button>
                                 <button class="btn btn-secondary" type="button" disabled v-else><i class="fa fa-spinner spin"></i> CARGANDO...</button>
                            </div>
                        </div>
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
            usuarios: [],
            usuario: {
                nombre: '',
                ap_paterno: '',
                ap_materno: '',
                active: 1,
                email: '',
                password: '',
                password_confirmation: '',
                role: 'ADMINISTRADOR',
                permisos: []
            },
            tipo_usuario: '-1',
            permisos: [],
            auth_role: '{{auth()->user()->roles()->first()->name}}',
        },
        methods:{ 
            cambiarRole: function(){
                let t = this
                t.permisos.map(element => {
                    if(t.usuario.role == 'VENDEDOR' && (element.title == 'Clientes' || element.title == 'Prospectos')){
                        element.value = true
                    }else
                        element.value = false

                    if(t.usuario.role == 'ADMINISTRADOR'){
                        element.value = true
                    }
                })
            },
            isEmail: function(cadena){
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailPattern.test(cadena);
            },
            eliminar: function(id){
                let t = this;
                swal({
                    title: "¿Eliminar usuario?",
                    text: "Una ves eliminado, no podra recuperar su información.",
                    icon: "warning",
                    buttons: ['Cancelar', 'Eliminar'],
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        axios.delete('/api/usuario/' + id).then(response => {
                            if(response.data.success){
                                swal('Correcto!', 'Usuario eliminado exitosamente', 'success');
                                t.getData(-1);
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
                if((t.usuario.id && t.usuario.password && t.usuario.password != '') || !t.usuario.id){
                    if(t.usuario.password != t.usuario.password_confirmation){
                        swal('Las contrasenas deben ser iguales', 'Reviselas y vuelva a intentarlo.', 'info');
                        return;
                    }
                    if(t.usuario.password.length < 6){
                        swal('Las contrasenas deben tener al menos 6 caracteres', 'Reviselas y vuelva a intentarlo.', 'info');
                        return;
                    }
                    if(t.usuario.nombre == '' || t.usuario.ap_paterno == '' || t.usuario.ap_materno == '' || t.usuario.estatus == '' || t.usuario.email == '')
                    {
                        swal('Campos obligatorios', 'Los campos marcados con asterisco son obligatorios.', 'info');
                        return;
                    }
                    if(!t.isEmail(t.usuario.email))
                    {
                        swal('Verifique el correo electronico', 'Verifique que el correo electronico que proporciono sea valido.', 'info');
                        return;
                    }
                }
                t.loading_button = true;
                t.usuario.permisos = [];
                t.permisos.map(obj => {
                    if(obj.value)
                        t.usuario.permisos.push(obj.title);
                });
                if(t.usuario.id)
                {
                    axios.put('/api/usuario/' + t.usuario.id, t.usuario).then(response => {
                        if(response.data.success){
                            $('#modalUsuario').modal('toggle');
                            swal('Correcto!', 'Usuario editado exitosamente', 'success');
                            t.getData(-1);
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
                    axios.post('/api/usuario', t.usuario).then(response => {
                        if(response.data.success){
                            $('#modalUsuario').modal('toggle');
                            swal('Correcto!', 'Usuario guardado exitosamente', 'success');
                            t.getData(-1);
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
                 t.usuario = {
                    nombre: '',
                    ap_paterno: '',
                    ap_materno: '',
                    active: 1,
                    email: '',
                    password: '',
                    password_confirmation: '',
                    role: 'ADMINISTRADOR',
                };
                t.permisos.map(obj => {
                    obj.value = true;
                });
                 Vue.nextTick(function () {
                    $('#modalUsuario').modal();
                });

            },
            editar: function(index){
                let t = this;
                t.usuario =  JSON.parse(JSON.stringify(t.usuarios[index]));
                t.permisos.map(obj => {
                    obj.value = t.usuario.permisos.includes(obj.title);
                });
                Vue.nextTick(function () {
                    $('#modalUsuario').modal();
                });
            },
            getData: function(){
                let t = this;
                t.cargando = true;
                $('#tabla').dataTable().fnDestroy();
                axios.get('/api/usuario', {params: {tipo_usuario: t.tipo_usuario}}) .then(response => {
                    t.usuarios = response.data.usuarios;
                    Vue.nextTick(function () {
                        $('[data-toggle="tooltip"]').tooltip();

                        $('#tabla').DataTable({
                            "searching": false,
                            "ordering": true,
                            "columnDefs": [{
                                orderable: false,
                                targets: "no-sort"
                            }],
                            language: {
                                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
                            }
                        });
                        t.cargando = false;
                    })
                })
                .catch(e => {
                    console.log(e);
                    t.cargando = false;    
                })
            },
            inicializarPermisos: function(){
               this.permisos = [
                    {
                        "title": "Programacion de cargas",
                        "description": "Acceso y gestión de la información relacionada con las cargas y su transportacion.",
                        "value": true
                    },
                    {
                        "title": "Clientes",
                        "description": "Acceso y gestión de la información relacionada con los clientes del sistema.",
                        "value": true
                    },
                     {
                        "title": "Prospectos",
                        "description": "Acceso y gestión de la información relacionada con los prospectos o futuros clientes asi como el seguimiento de estos.",
                        "value": true
                    },
                    {
                        "title": "Licitaciones",
                        "description": "Acceso y gestión de la información relacionada con la licitacion de cargas a transportistas externos.",
                        "value": true
                    },
                    {
                        "title": "Salidas y Destinos",
                        "description": "Acceso y gestión de la información relacionada con los puntos de salida y destino de las cargas.",
                        "value": true
                    },
                    {
                        "title": "Conductores",
                        "description": "Acceso y gestión de la información relacionada con los conductores.",
                        "value": true
                    },
                    {
                        "title": "Camiones y  Remolques",
                        "description": "Acceso y gestión de la información relacionada con los vehiculos que transportan la carga.",
                        "value": true
                    },
                    {
                        "title": "Transportistas externos",
                        "description": "Acceso y gestión de la información relacionada con los transportistas externos del sistema.",
                        "value": true
                    },
                    {
                        "title": "Usuarios y Vendedores",
                        "description": "Administración de usuarios con acceso al sistema, asignando distintos niveles de permisos y roles.",
                        "value": true
                    }
                ];

            }
        },
        mounted: function () {
            this.$nextTick(function () {
                let t = this;
                t.inicializarPermisos();
                t.getData();
            })
        }
                
    })

    </script>



        
@endpush