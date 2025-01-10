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
    #tabla-principal {
        table-layout: fixed;
        min-width: 1200px; /* Ajusta el ancho mínimo según el contenido */
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
        <div class="row" v-cloak v-show="!cargando">
            <div class="col-xl-2 pt-3" style="background-color: #f1f1f1; height: calc(100vh - 107.3px); overflow-y: scroll">
                <div class="nav flex-column nav-pills " id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link cursor-pointer text-right text-muted" >
                        <i class="nc-icon" v-if="menuStep > 1" @click="regresar(menuStep - 1)" ><img height="17px" src="{{ asset('paper/img/icons/regresar.png') }}"></i>
                    </a>
                    <div v-if="!cargandoMenu && menuStep == 1">
                        <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> AÑOS </a>
                        <a class="nav-link cursor-pointer" @click="abrirModalNuevo('año', 'Año')">
                            <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/plus.png') }}"></i> &nbsp;
                            <span class="underline-hover">Nuevo año...</span>
                        </a>
                        <a class="nav-link cursor-pointer" v-for="obj in anios" @click="fetchClientes(obj.id)">
                            <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/calendario.png') }}"></i> &nbsp;
                            <span class="underline-hover">@{{obj.nombre}}</span> &nbsp;&nbsp; {{--<i class="fa fa-caret-right"></i>    --}}
                        </a>
                    </div>    
                    <div v-if="!cargandoMenu && menuStep == 2">
                        <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> CARPETAS </a>
                        <a class="nav-link cursor-pointer" @click="abrirModalNuevo('carpeta', 'Nombre de la carpeta')">
                            <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/plus.png') }}"></i> &nbsp;
                            <span class="underline-hover">Nueva carpeta...</span>
                        </a>
                        <a class="nav-link cursor-pointer" v-for="obj in clientes" @click="fetchProyectos(obj.id)">
                            <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/carpetas.png') }}"></i> &nbsp;
                            <span class="underline-hover">@{{obj.nombre}}</span> &nbsp;&nbsp; {{--<i class="fa fa-caret-right"></i>    --}}
                        </a>
                    </div>
                    <div v-if="!cargandoMenu && menuStep == 3">
                        <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> PROYECTOS </a>
                        <a class="nav-link cursor-pointer" @click="abrirModalNuevo('proyecto', 'Nombre del Proyecto')">
                            <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/plus.png') }}"></i> &nbsp;
                            <span class="underline-hover">Nuevo proyecto...</span>
                        </a>
                        <a class="nav-link cursor-pointer" v-for="obj in proyectos" @click="fetchHerramentales(obj.id)">
                            <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/carpetas.png') }}"></i> &nbsp;
                            <span class="underline-hover">@{{obj.nombre}}</span> &nbsp;&nbsp; {{--<i class="fa fa-caret-right"></i>    --}}
                        </a>
                    </div>
                    <div v-if="!cargandoMenu && menuStep == 4">
                        <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> HERRAMENTALES </a>
                        <a class="nav-link cursor-pointer" @click="nuevoHerramental()">
                            <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/plus.png') }}"></i> &nbsp;
                            <span class="underline-hover">Nuevo herramental...</span>
                        </a>
                        <a class="nav-link cursor-pointer" v-for="obj in herramentales" @click="fetchComponentes(obj.id)" >
                            <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/componente.png') }}"></i> &nbsp;
                            <span class="underline-hover">@{{obj.nombre}}</span> &nbsp;&nbsp; {{--<i class="fa fa-caret-right"></i>    --}}
                        </a>
                    </div>
                    
                </div>            
            </div>
            <div class="col-xl-10 mt-3">
                <div class="row">
                    <div class="mb-2 col-xl-12" style="border-bottom: 1px solid #ededed">
                        <p style="">
                            <span class="cursor-pointer pb-2" @click="regresar(1)"><i class="fa fa-home"></i> &nbsp;</span>
                            <span class="cursor-pointer pb-2"  v-if="ruta.anio" @click="regresar(2)"><i class="fa fa-angle-right"></i>   &nbsp; <span class="underline-hover">@{{ruta.anio}}</span>    &nbsp;</span>
                            <span class="cursor-pointer pb-2"  v-if="ruta.cliente" @click="regresar(3)"><i class="fa fa-angle-right"></i>   &nbsp; <span class="underline-hover">@{{ruta.cliente}}</span>     &nbsp;</span>
                            <span class="cursor-pointer pb-2"  v-if="ruta.proyecto" @click="regresar(4)"><i class="fa fa-angle-right"></i>   &nbsp; <span class="underline-hover">@{{ruta.proyecto}}</span>     &nbsp;</span>
                            <span class="cursor-pointer pb-2"  v-if="ruta.herramental"><i class="fa fa-angle-right"></i>   &nbsp; <span class="underline-hover">@{{ruta.herramental}}</span>      </span>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6">
                        <h2 class="bold my-0 py-1 mb-3 text-decoration-underline" style="letter-spacing: 2px">CARGA DE COMPONENTES</h2>
                    </div>
                    <div class="col-xl-6" v-if="selectedHerramental">
                        <div class="row">
                            <div class="col">
                                <button class="btn btn-block mt-0" @click="agregarComponente" ><i class="fa fa-plus-circle"></i> AGREGAR COMPONENTE</button>
                            </div>
                            <div class="col" style="border-left: 1px solid  #ededed">
                                <button class="btn btn-block mt-0" @click="guardarComponente"><i class="fa fa-save"></i>    GUARDAR</button>
                            </div>
                            <div class="col">
                                <button class="btn btn-block btn-success mt-0" @click="liberarHerramental"><i class="fa fa-check-double"></i> LIBERAR TODOS </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12" v-if="!selectedHerramental">
                    <h5 class="text-muted my-4" > SELECCIONE UN HERRAMENTAL PARA AGREGAR SUS COMPONENTES</h5>
                </div>
                <div class="row" v-else>
                    <div class="col-xl-12">
                        <div class="table-responsive" style="overflow-x: auto !important;">
                            <table class="table table-sm" id="tabla-principal">
                                <thead class="thead thead-light">
                                    <tr>
                                        <th>Componente</th>
                                        <th>Vista 2D</th>
                                        <th>Vista 3D</th>
                                        <th>Vista explosionada</th>
                                        <th>Compra / Fabricacion</th>
                                        <th>Cantidad</th>
                                        <th>Largo</th>
                                        <th>Ancho</th>
                                        <th>Alto</th>
                                        <th>Material</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(c, index) in componentes" :key="index">
                                        <td>
                                            <strong> @{{ c.nombre }}</strong>
                                        </td>
                                        <td>
                                            <input class="input-file" :id="'2d-' + index" type="file" name="file" @change="handleFileChange($event, index, 'vista2D')" style="display: none;">
                                            <label tabindex="0" :for="'2d-' + index" class="input-file-trigger col-12 text-center"><i class="fa fa-upload"></i> Cargar</label>
                                            <small>@{{ getElipsis(c.archivo_2d) }}</small>
                                        </td>
                                        <td>
                                            <input class="input-file" :id="'3d-' + index" type="file" name="file" @change="handleFileChange($event, index, 'vista3D')" style="display: none;">
                                            <label tabindex="0" :for="'3d-' + index" class="input-file-trigger col-12 text-center"><i class="fa fa-upload"></i> Cargar</label>
                                            <small>@{{ getElipsis(c.archivo_3d) }}</small>
                                        </td>
                                        <td>
                                            <input class="input-file" :id="'explosionada-' + index" type="file" name="file" @change="handleFileChange($event, index, 'vistaExplosionada')" style="display: none;">
                                            <label tabindex="0" :for="'explosionada-' + index" class="input-file-trigger col-12 text-center"><i class="fa fa-upload"></i> Cargar</label>
                                            <small>@{{ getElipsis(c.archivo_explosionado) }}</small>
                                        </td>
                                        <td class="text-left">
                                            <div class="form-group text-left">
                                                <div class="form-check">
                                                    <input class="form-check-input" style="margin-left: 0px !important" type="radio" :id="'es_compra_si-' + index" :value="1" v-model="c.es_compra" :disabled="c.cargado == 1">
                                                    <label class="bold form-check-label" :for="'es_compra_si-' + index">
                                                        Compra
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" style="margin-left: 0px !important" type="radio" :id="'es_compra_no-' + index" :value="0" v-model="c.es_compra" :disabled="c.cargado == 1">
                                                    <label class="bold form-check-label" :for="'es_compra_no-' + index">
                                                        Fabricación
                                                    </label>
                                                </div>
                                            </div>
                                        </td>
                                        <td><input type="number" step="any" min="0" class="form-control" v-model="c.cantidad" :disabled="c.cargado == 1"></td>
                                        <td><input type="text" class="form-control" v-model="c.largo" :disabled="c.cargado == 1"></td>
                                        <td><input type="text" class="form-control" v-model="c.ancho" :disabled="c.cargado == 1"></td>
                                        <td><input type="text" class="form-control" v-model="c.alto" :disabled="c.cargado == 1"></td>
                                        <td>
                                            <select :disabled="c.es_compra == 1 || c.cargado == 1" class="form-control" v-model="c.material_id">
                                                <option :value="null">Material...</option>
                                                <option v-for="m in materiales" :value="m.id">@{{ m.nombre }}</option>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="row">
                                                <div class="col my-1">
                                                    <button v-if="!c.cargado" @click="liberarComponente(c)" class=" my-1 btn-block btn btn-sm btn-success"><i class="fa fa-check"></i> Liberar</button>
                                                    <button v-if="c.cargado && !c.cancelado" disabled class=" my-1 btn-block btn btn-sm btn-success"><i class="fa fa-check-double"></i> Liberado</button>
                                                    <button v-if="c.cargado && !c.cancelado" @click="preCancelarComponente(c)" class=" my-1 btn-block btn btn-sm btn-danger"><i class="fa fa-ban"></i> Cancelar</button>
                                                    <button v-if="c.cargado && c.cancelado" class=" my-1 btn-block btn btn-sm btn-danger" disabled><i class="fa fa-ban"></i> Cancelado</button>
                                                </div>
                                                <div class="col my-1">
                                                    <button v-if="!c.cargado" @click="eliminarComponente(index)" class="btn-block btn btn-sm btn-danger"><i class="fa fa-times-circle"></i> Eliminar</button>
                                                </div>
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

        <div class="modal fade" id="modalNuevo" tabindex="-1" aria-labelledby="modalNuevoLabel" aria-hidden="true">
            <div class="modal-dialog" style="min-width: 25%;">
                <div class="modal-content" >
                    <div class="modal-header">
                        <h3 class="modal-title" id="modalNuevoLabel">
                            <span>AGREGAR @{{nuevo.tipo.toUpperCase()}}</span>
                        </h3>
                        <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xl-12 form-group">
                                <label class="bold" for="">@{{nuevo.text}}: <span class="text-danger">*</span></label>
                                <input v-model="nuevo.nombre" type="text" class="form-control" :placeholder="nuevo.text + '...'">
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-xl-12 text-right">
                                <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                                <button class="btn btn-secondary" v-if="!loading_button" type="button" @click="guardarNuevo('carpeta')"><i class="fa fa-save"></i> Guardar</button>
                                <button class="btn btn-secondary" type="button" disabled v-if="loading_button"><i class="fa fa-spinner spin"></i> Guardando...</button>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalHerramental" tabindex="-1" aria-labelledby="modalHerramentalLabel" aria-hidden="true">
            <div class="modal-dialog" style="min-width: 25%;">
                <div class="modal-content" >
                    <div class="modal-header">
                        <h3 class="modal-title" id="modalHerramentalLabel">
                            <span>CARGAR FORMATO F71-03 ANEXO 1</span>
                        </h3>
                        <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xl-12 form-group">
                                <input class="input-file" id="archivo" type="file" name="file" @change="seleccionaArchivo($event)" v-show="false">
                                <label tabindex="0" for="archivo" class="input-file-trigger col-12 text-center"><i class="fa fa-upload"></i> Subir formato  </label>
                                <small>@{{archivo}}</small>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-xl-12 text-right">
                                <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                                <button class="btn btn-secondary" v-if="!loading_button" type="button" @click="guardarHerramental()"><i class="fa fa-save"></i> Guardar</button>
                                <button class="btn btn-secondary" type="button" disabled v-if="loading_button"><i class="fa fa-spinner spin"></i> Guardando...</button>
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
        data: {
            errores: [],
            loading_button: false,
            cargando: false,
            anios: [],         
            clientes: [],      
            proyectos: [],     
            herramentales: [], 
            componentes: [],   
            materiales: [],
            selectedAnio: null,
            selectedCliente: null,
            selectedProyecto: null,
            selectedHerramental: null,
            menuStep: 1, 
            cargandoMenu: true,
            nuevo:{
                tipo: '',
                text: '',
                nombre: ''
            },
            ruta:{
                anio: null,
                cliente: null,
                proyecto: null,
                herramental: null,
            },
            archivo: '',
            componentes:[],
            files: []
        },
        watch: {
            componentes: {
                handler(newComponentes) {
                    newComponentes.forEach((componente) => {
                        if (componente.es_compra === 1) {
                            // componente.largo = '';
                            // componente.ancho = '';
                            // componente.alto = '';
                            componente.material_id = null;
                        }
                    });
                },
                deep: true // Necesario para observar cambios dentro de los objetos en el arreglo
            }
        },
        computed: {

        },
        methods:{
            getElipsis(str){
                 if (str && str.length > 10) {
                    return str.substring(0, 15) + '...';
                }
                return str;
            },
            async guardarComponente(mostrarAlerta = true){
                let t = this
                t.cargando = true;
                let formData = new FormData();

                t.files.forEach((fileObj, index) => {
                    if (fileObj.vista2D) {
                        formData.append(`files[${index}][vista2D]`, fileObj.vista2D);
                    }
                    if (fileObj.vista3D) {
                        formData.append(`files[${index}][vista3D]`, fileObj.vista3D);
                    }
                    if (fileObj.vistaExplosionada) {
                        formData.append(`files[${index}][vistaExplosionada]`, fileObj.vistaExplosionada);
                    }
                });

                formData.append('data', JSON.stringify(t.componentes));

                try {
                    const response = await axios.post(`/api/componente/${t.selectedHerramental}`, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });
                    if(mostrarAlerta){
                        swal('Correcto', 'Informacion guardada correctamente', 'success');
                        t.fetchComponentes(t.selectedHerramental);
                    }else{
                        return true;
                    }
                } catch (error) {
                    console.error('Error al subir archivos:', error);
                    t.cargando = false
                    return false;
                }  
            },
            handleFileChange(event, index, fileType) {
                let t = this;
                let file = event.target.files[0];
                
                if (!this.files[index]) {
                        this.files[index] = {
                            vista2D: null,
                            vista3D: null,
                            vistaExplosionada: null
                        };
                }

                this.files[index][fileType] = file;

                switch(fileType){
                    case 'vista2D':
                        t.componentes[index].archivo_2d = file.name;
                    break;
                    case 'vista3D':
                        t.componentes[index].archivo_3d = file.name;
                    break;
                    case 'vistaExplosionada':
                        t.componentes[index].archivo_explosionado = file.name;
                    break;
                }
            },
            agregarComponente(){
                this.componentes.push({
                    nombre: `${this.ruta.herramental}-${(this.componentes.length + 1).toString().padStart(2, '0')}`,
                    es_compra: 1,
                    cantidad: 1,
                    largo: '',
                    ancho: '',
                    alto: '',
                    material_id: null,
                    archivo_2d: '',
                    archivo_3d: '',
                    archivo_explosionado: '',
                    cargado: false,
                })

                document.querySelector("html").classList.add('js');
                let fileInput  = document.querySelector( ".input-file" )
                let button     = document.querySelector( ".input-file-trigger" )
                
                button.addEventListener( "keydown", function( event ) {
                    if ( event.keyCode == 13 || event.keyCode == 32 ) {
                        fileInput.focus();
                    }
                });

                button.addEventListener( "click", function( event ) {
                    fileInput.focus();
                    return false;
                });
            },
            seleccionaArchivo: function(e){
                let t = this;
                var files = e.target.files || e.dataTransfer.files;

                if (!files.length)
                    return;
                
                t.archivo = '';
                t.archivo += files[0].name;
            },
            nuevoHerramental(){
                $('#modalHerramental').modal();
                this.archivo = '';

                Vue.nextTick(function(){    
                    $('#archivo').val(null);
                    document.querySelector("html").classList.add('js');
                    let fileInput  = document.querySelector( ".input-file" )
                    let button     = document.querySelector( ".input-file-trigger" )
                    
                    button.addEventListener( "keydown", function( event ) {
                        if ( event.keyCode == 13 || event.keyCode == 32 ) {
                            fileInput.focus();
                        }
                    });

                    button.addEventListener( "click", function( event ) {
                        fileInput.focus();
                        return false;
                    });
                })
            },
            guardarHerramental(){
                let t = this
                let formData = new FormData();
                let file1 = document.querySelector('#archivo');

                if (!file1.files.length) {
                    swal('Formato obligatorio', 'Por favor seleccione un archivo antes de guardar.', 'info');
                    return;
                }

                formData.append("archivo", file1.files[0]);
                axios.post(`/api/herramental/${t.selectedProyecto}`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(response => {
                    if(response.data.success){
                        t.fetchHerramentales(t.selectedProyecto);
                        $('#modalHerramental').modal('toggle');
                    }else{
                        swal('Lo sentimos!', response.data.message, 'info');
                        t.cargando = false;
                        console.log(e);
                    }
                }).catch(e => {
                    swal('Lo sentimos!', 'Intentelo de nuevo mas tarde', 'info');
                    t.cargando = false;
                    console.log(e);
                });
            },
            regresar(step){
                switch (step) {
                    case 1:
                        this.ruta = {
                            anio: null,
                            cliente: null,
                            proyecto: null,
                            herramental: null,
                        } 
                        this.selectedAnio = null;
                        this.selectedCliente = null;
                        this.selectedProyecto = null;
                        this.selectedHerramental = null;
                    break;
                    case 2:
                        this.ruta.cliente = null;
                        this.ruta.proyecto = null;
                        this.ruta.herramental = null;

                        this.selectedCliente = null;
                        this.selectedProyecto = null;
                        this.selectedHerramental = null;
                    break;
                    case 3:
                        this.ruta.proyecto = null;
                        this.ruta.herramental = null;

                        this.selectedProyecto = null;
                        this.selectedHerramental = null;
                    break;
                    case 4:
                        this.ruta.herramental = null;
                        this.selectedHerramental = null;
                    break;
                }
                this.menuStep = step;

            },
            async guardarNuevo() {
                let t = this;
                try {
                    t.loading_button = true;
                    
                    switch (t.nuevo.tipo) {
                        case 'año':
                            const responseAnios = await axios.post('/api/anios', t.nuevo);
                            if (responseAnios.data.success) {
                                await t.fetchAnios();
                                await t.fetchClientes(responseAnios.data.id);
                                $('#modalNuevo').modal('toggle');
                            }
                            break;

                        case 'carpeta':
                            const responseCarpeta = await axios.post(`/api/clientes/${t.selectedAnio}`, t.nuevo);
                            if (responseCarpeta.data.success) {
                                await t.fetchClientes(t.selectedAnio);
                                await t.fetchProyectos(responseCarpeta.data.id);
                                $('#modalNuevo').modal('toggle');
                            }
                            break;

                        case 'proyecto':
                            const responseProyecto = await axios.post(`/api/proyectos/${t.selectedCliente}`, t.nuevo);
                            if (responseProyecto.data.success) {
                                await t.fetchProyectos(t.selectedCliente);
                                await t.fetchHerramentales(responseProyecto.data.id);
                                $('#modalNuevo').modal('toggle');
                            }
                            break;
                    }
                } catch (error) {
                    console.error(error);
                } finally {
                    t.loading_button = false;
                }
            },
            abrirModalNuevo(tipo, text){
                this.nuevo = {
                    tipo: tipo,
                    text: text,
                    nombre: '',
                }
                $('#modalNuevo').modal();
            },
            async fetchAnios() {
                this.cargandoMenu = true
                axios.get('/api/anios')
                try {
                    const response = await axios.get('/api/anios');
                    this.anios = response.data.anios;
                } catch (error) {
                    console.error('Error fetching años:', error);
                } finally {
                    this.cargandoMenu = false;
                }
            },
            async fetchClientes(anioId) {
                this.cargandoMenu = true
                this.selectedAnio = anioId;
                this.ruta.anio = this.anios.find(obj => obj.id == anioId)?.nombre;
                
                 try {
                    const response = await axios.get(`/api/anios/${anioId}/clientes`);
                    this.clientes = response.data.clientes;
                    this.menuStep = 2;
                } catch (error) {
                    console.error('Error fetching clientes:', error);
                } finally {
                    this.cargandoMenu = false;
                }
            },
            async fetchProyectos(clienteId) {
                this.cargandoMenu = true
                this.selectedCliente = clienteId;
                this.ruta.cliente = this.clientes.find(obj => obj.id == clienteId)?.nombre;

               try {
                    const response = await axios.get(`/api/clientes/${clienteId}/proyectos`);
                    this.proyectos = response.data.proyectos;
                    this.menuStep = 3;
                } catch (error) {
                    console.error('Error fetching proyectos:', error);
                } finally {
                    this.cargandoMenu = false;
                }
            },
            async fetchHerramentales(proyectoId) {
                this.cargandoMenu = true
                this.selectedProyecto = proyectoId;
                this.ruta.proyecto = this.proyectos.find(obj => obj.id == proyectoId)?.nombre;

                try {
                    const response = await axios.get(`/api/proyectos/${proyectoId}/herramentales`);
                    this.herramentales = response.data.herramentales;
                    this.menuStep = 4;
                } catch (error) {
                    console.error('Error fetching herramentales:', error);
                } finally {
                    this.cargandoMenu = false;
                }
            },
            async fetchComponentes(herramentalId) {
                this.cargando = true
                this.selectedHerramental = herramentalId;
                this.ruta.herramental = this.herramentales.find(obj => obj.id == herramentalId)?.nombre;

                try {
                    const response = await axios.get(`/api/herramentales/${herramentalId}/componentes?area=cargar-componentes`);
                    this.componentes = response.data.componentes;
                    document.querySelector("html").classList.add('js');
                    let fileInput  = document.querySelector( ".input-file" )
                    let button     = document.querySelector( ".input-file-trigger" )

                    button.addEventListener( "keydown", function( event ) {
                        if ( event.keyCode == 13 || event.keyCode == 32 ) {
                            fileInput.focus();
                        }
                    });

                    button.addEventListener( "click", function( event ) {
                        fileInput.focus();
                        return false;
                    });
                } catch (error) {
                    console.error('Error fetching componentes:', error);
                } finally {
                    this.cargando = false;
                }
            },
            async fetchMateriales() {
                this.cargando = true

                try {
                    const response = await axios.get(`/api/materiales`);
                    this.materiales = response.data.materiales;
                } catch (error) {
                    console.error('Error fetching materiales:', error);
                } finally {
                    this.cargando = false;
                }
            },
            eliminarComponente(index){
                this.componentes.splice(index, 1);
                this.files.splice(index, 1);
                this.componentes.forEach((element, index) => {
                    element.nombre = `${this.ruta.herramental}-${(index + 1).toString().padStart(2, '0')}`;
                })
            },
            componenteValido(componente){
                let t = this;
                let errores = [];

                if (!componente.cantidad || parseInt(componente.cantidad) <= 0 ) {
                    errores.push(`La cantidad en ${componente.nombre} es obligatoria y tiene que ser mayor a 0.`);
                }
                if (componente.es_compra == 0 && !componente.largo) {
                    errores.push(`El largo en ${componente.nombre} es obligatorio.`);
                }
                if (componente.es_compra == 0 && !componente.ancho) {
                    errores.push(`El ancho en ${componente.nombre} es obligatorio.`);
                }
                if (componente.es_compra == 0 && !componente.alto) {
                    errores.push(`El alto en ${componente.nombre} es obligatorio.`);
                }
                if (componente.es_compra == 0 && !componente.material_id) {
                    errores.push(`El material es obligatorio en ${componente.nombre} cuando no es compra.`);
                }
                if (!componente.archivo_2d || !componente.archivo_3d || !componente.archivo_explosionado) {
                    errores.push(`Todos los archivos son obligatorios en ${componente.nombre}.`);
                }

                if(errores.length > 0){
                    t.errores.push(...errores);
                    return false;
                }
                else
                    return true;
            },
            preCancelarComponente(c){
                let t = this
                swal({
                    title: "¿Esta seguro de cancelar este componente?",
                    text: "Se cancelar para todas las areas",
                       icon: "warning",
                       buttons: ['Cancelar', 'Si, cancelar'],
                       dangerMode: true,
                   })
                   .then((willDelete) => {
                       if (willDelete) {
                            t.cancelarComponente(c);                         
                       }
                   });
            },
            async cancelarComponente(componente){
                let t = this
                try {
                    const response = await axios.delete(`/api/cancelar-componente-cargar/${componente.id}`);
                    t.cargando = false;
                    swal('Éxito', 'Componente cancelado correctamente', 'success');
                    t.fetchComponentes(t.selectedHerramental);

                } catch (error) {
                    t.cargando = false;
                    console.error('Error al cancelar el componente:', error);
                    swal('Error', 'Ocurrió un error al liberar el herramental', 'error');
                }
               
            },
            async liberarComponente(componente){
                let t = this
                t.errores = [];
                let valido = t.componenteValido(componente)
                if (!valido) {
                    swal('Errores de validación', t.errores.join('\n'), 'error');
                    return;
                }

                let respuesta = await t.guardarComponente(false);
                
                if(respuesta){
                    try {
                        const response = await axios.put(`/api/liberar-componente-cargar/${t.selectedHerramental}`, {componente: componente.nombre});
                        t.cargando = false;
                        swal('Éxito', 'Componente liberado correctamente', 'success');
                        t.fetchComponentes(t.selectedHerramental);

                    } catch (error) {
                        t.cargando = false;
                        console.error('Error al liberar el componente:', error);
                        swal('Error', 'Ocurrió un error al liberar el herramental', 'error');
                    }
                }else{
                    swal('Error', 'Ocurrió un error al guardar la informacion de los componentes', 'error');
                    t.cargando = false;
                }

            },
            async liberarHerramental() {
                let t = this;

                t.errores = [];
                let valido = true;
                t.componentes.forEach((componente, index) => {  
                    valido = valido && t.componenteValido(componente);
                });
                if (!valido) {
                    swal('Errores de validación', t.errores.join('\n'), 'error');
                    return;
                }
                t.cargando = true;
                let respuesta = await t.guardarComponente(false);

                if(respuesta){
                    try {
                        const response = await axios.put(`/api/liberar-herramental-cargar/${t.selectedHerramental}`);
                        t.cargando = false;
                        if(response.data.success){
                            swal('Éxito', 'Componentes del herramental liberados correctamente', 'success');
                            t.fetchComponentes(t.selectedHerramental);
                        }else{
                            swal('Lo sentimos', response.data.message, 'error');
                        }
                    } catch (error) {
                        t.cargando = false;
                        console.error('Error al liberar los componentes:', error);
                        swal('Error', 'Ocurrió un error al liberar el herramental', 'error');
                    }
                }else{
                    swal('Error', 'Ocurrió un error al guardar la informacion de los componentes', 'error');
                    t.cargando = false;
                }
            }
        },
        mounted: async function () {
            let t = this;
            await t.fetchAnios();
            await t.fetchMateriales();
        }

                
    })

    </script>



        
@endpush