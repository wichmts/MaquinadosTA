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
                    <div class="col-md-8   mb-0">
                        <h3 class="bold my-0 py-1" style="letter-spacing: 1px;">USUARIOS Y PERMISOS</h3>
                    </div>
                    <div class="col-md-2 form-group">
                        <label for="">Filtrar por tipo de usuario:</label>
                        <select v-model="tipo_usuario" class="form-control" @change="getData">
                            <option value="-1">Todos los usuarios</option>
                            <option v-for="r in roles" :value="r">@{{r}}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2 col-xs-12 mt-4">
                        <button class="btn btn-normal col-md-12" @click="reiniciarModal"><i class="fa fa-circle-plus"></i> NUEVO USUARIO</button>
                    </div>

                    <div class="col-md-12 table-responsive">            
                        <table class="table align-items-center" id="tabla">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">Correo electronico</th>
                                    <th scope="col">Roles del usuario</th>
                                    <th scope="col">Estatus</th>
                                    <th scope="col" class="no-sort">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(p, index) in usuarios" :key="index">
                                    <td style="width: 15%">@{{p.nombre_completo}}</td>
                                    <td style="width: 15%">@{{p.email}}</td>
                                    <td style="width: 10%">
                                        <span v-for="r in p.roles" style="font-size: 12px" class="badge-dark badge badge-pill mx-1 my-1 py-2 px-3"> @{{r}}</span>
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
                            <div class="form-group col-md-4">
                                <label class="bold">Nombre(s) <span style="color: red">*</span></label>
                                <input placeholder="Nombre(s) del usuario..." class=" form-control" type="text" v-model="usuario.nombre"/>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="bold">Apellido paterno </label>
                                <input placeholder="Apellidos paterno ..." class=" form-control" type="text" v-model="usuario.ap_paterno"/>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="bold">Apellido materno </label>
                                <input placeholder="Apellidos materno ..." class=" form-control" type="text" v-model="usuario.ap_materno"/>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="bold">Estatus <span style="color: red">*</span></label>
                                <select v-model="usuario.active" class="form-control">
                                    <option :value="1">Activo</option>
                                    <option :value="0">Inactivo</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="bold">Correo electronico</label>
                                <input placeholder="nombre@mail.com" class="form-control" type="email" v-model="usuario.email"/>
                            </div>
                            <div class="form-group col-xl-4">
                                <label class="bold text-danger" style="letter-spacing: 1px">CODIGO DE ACCESO *</label>
                                <input class="form-control text-center" placeholder="-----" type="text" style="font-weight: bold; letter-spacing: 2px" v-model="usuario.codigo_acceso"/>
                            </div>
                            <div class="col-xl-4 form-group mt-2">
                                <label class="bold">Seleccionar puesto del usuario</label>
                                <ul style="height: 300px !important; overflow-y: scroll" class="dropdown-menu show w-100 position-static border mt-0">
                                    <li v-for="r in puestos" class="dropdown-item" :class="{ roleSeleccionado: usuario.puesto_id == r.id}" @click="seleccionarPuesto(r.id)"><i class="fa fa-check-circle" v-if="usuario.puesto_id == r.id"></i> @{{r.nombre}}</li>
                                </ul>
                            </div>
                            <div class="col-xl-4 form-group mt-2">
                                <label class="bold">Seleccionar role(s) del usuario <span style="color: red">*</span></label>
                                <ul style="height: 300px !important; overflow-y: scroll" class="dropdown-menu show w-100 position-static border mt-0">
                                    <li v-for="r in roles" class="dropdown-item" :class="{ roleSeleccionado: existeRole(r)}" @click="incluirRole(r)"><i class="fa fa-check-circle" v-if="existeRole(r)"></i> @{{r}}</li>
                                </ul>
                            </div>
                            <div class="col-xl-4 form-group mt-2" v-if="existeRole('OPERADOR')">
                                <label class="bold">Asignar MAQUINAS al operador <span style="color: red">*</span></label>
                                <ul style="height: 300px !important; overflow-y: scroll" class="dropdown-menu show w-100 position-static border mt-0">
                                    <li v-for="m in maquinas" class="dropdown-item" :class="{ roleSeleccionado: existeMaquina(m.id)}" @click="incluirMaquina(m.id)"><i class="fa fa-check-circle" v-if="existeMaquina(m.id)"></i> @{{m.nombre}}</li>
                                </ul>
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
            usuarios: [],
            usuario: {
                nombre: '',
                ap_paterno: '',
                ap_materno: '',
                active: 1,
                puesto_id: null,
                email: '',
                codigo_acceso: '',
                roles: [],
                permisos: [],
                maquinas: [],
            },
            tipo_usuario: '-1',
            permisos: [],
            maquinas: [],
            puestos: [],
            roles: [
                'ALMACENISTA',
                'AUXILIAR DE DISEÑO',
                'DISEÑO',
                'DIRECCION',
                'FINANZAS',
                'HERRAMENTALES',
                'INFRAESTRUCTURA',
                'JEFE DE AREA',
                'MANTENIMIENTO',
                'MATRICERO',
                'METROLOGIA',
                'OPERADOR',
                'PROCESOS',
                'PRODUCCION',
                'PROYECTOS',
                'PROGRAMADOR',
                'SOLICITUD EXTERNA'
            ]

        },
        methods:{ 
            seleccionarPuesto(id){
                this.usuario.puesto_id = id;
            },
            incluirRole(role) {
                let indiceRole = this.usuario.roles.findIndex((m) => m === role);

                if (indiceRole !== -1){
                    this.usuario.roles.splice(indiceRole, 1); 
                    if(role == 'OPERADOR')
                        this.usuario.maquinas = [];
                }
                else
                    this.usuario.roles.push(role);
            },
            existeRole(role){
                return this.usuario.roles?.some(obj => obj == role);
            },
            incluirMaquina(id) {
                let indiceMaquina = this.usuario.maquinas.findIndex((m) => m === id);

                if (indiceMaquina !== -1)
                    this.usuario.maquinas.splice(indiceMaquina, 1); 
                else
                    this.usuario.maquinas.push(id);
            },
            existeMaquina(id){
                return this.usuario.maquinas?.some(obj => obj == id);
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
               if (!t.usuario.nombre || t.usuario.nombre.trim() === '') {
                    swal('Campo obligatorio', 'El nombre es obligatorio.', 'info');
                    return;
                }

                if (!t.usuario.codigo_acceso || t.usuario.codigo_acceso.trim() === '') {
                    swal('Campo obligatorio', 'El código de acceso es obligatorio.', 'info');
                    return;
                }


                if(t.usuario.roles.length == 0){
                    swal('Role obligatorio', 'El usuario debe tener al menos un role asignado.', 'info');
                    return;
                }

                if (t.usuario.email && t.usuario.email.trim() !== '') {
                    if (!t.isEmail(t.usuario.email)) {
                        swal('Verifique el correo electrónico', 'Verifique que el correo electrónico que proporcionó sea válido.', 'info');
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
                    puesto_id: null,
                    email: '',
                    password: '',
                    password_confirmation: '',
                    roles: [],
                    maquinas: [],
                };
                 Vue.nextTick(function () {
                    $('#modalUsuario').modal();
                });

            },
            editar: function(index){
                let t = this;
                t.usuario =  JSON.parse(JSON.stringify(t.usuarios[index]));
                Vue.nextTick(function () {
                    $('#modalUsuario').modal();
                });
            },
            getData: function(){
                let t = this;
                t.cargando = true;
                $('#tabla').dataTable().fnDestroy();

                axios.get('api/maquinas').then(response => {
                    t.maquinas = response.data.maquinas.filter(maq => maq.tipo_proceso != 10)
                }).catch(e => {
                    console.log(e);
                });

                 axios.get('api/puestos').then(response => {
                    t.puestos = response.data.puestos;
                }).catch(e => {
                    console.log(e);
                });


                axios.get('/api/usuario', {params: {tipo_usuario: t.tipo_usuario}}) .then(response => {
                    t.usuarios = response.data.usuarios;
                    Vue.nextTick(function () {
                        $('[data-toggle="tooltip"]').tooltip();

                        $('#tabla').DataTable({
                            "searching": false,
                            "ordering": true,
                            "pageLength": 25, 
                            "order": [[2, "asc"]], // Ordenar por la segunda columna (índice 1) en orden ascendente
                            "lengthMenu": [[25, 50, 100], [25, 50, 100]], // Eliminar la opción de 10
                            "columnDefs": [{
                                orderable: false,
                                targets: "no-sort"
                            }],
                            "language": {
                                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
                            },
                            "pagingType": "simple_numbers" // Paginación más amplia (usa 'full_numbers' si prefieres)
                        });

                        t.cargando = false;
                    })
                })
                .catch(e => {
                    console.log(e);
                    t.cargando = false;    
                })
            },
           
        },
        mounted: function () {
            this.$nextTick(function () {
                let t = this;
                // t.inicializarPermisos();
                t.getData();
            })
        }
                
    })

    </script>



        
@endpush