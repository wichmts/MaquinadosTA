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
        width: 100%;
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
        background-color: #000000 !important;
    }

    .custom-file-label:hover {
        background-color: #e7e7e7;
    }
    
     #tabla-principal {
        table-layout: fixed;
        min-width: 1200px; /* Ajusta el ancho mínimo según el contenido */
    }

     #gantt_here {
        width: 100%;
        height: 600px; /* Ajusta este valor según tus necesidades */
    }

    .btn-dark{
        background-color: #333 !important;
        color: white !important;
        border-color: #333;
    }


    input[type="number"] {
        -moz-appearance: textfield; /* Firefox */
        -webkit-appearance: none;  /* Chrome, Safari, Edge */
        appearance: none;          /* Soporte estándar */
    }

    /* Opcional: Para evitar padding adicional en algunos navegadores */
    input[type="number"]::-webkit-inner-spin-button, 
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none; 
        margin: 0; /* Opcional: Ajustar márgenes */
    }


    .table .form-check label .form-check-sign::before, .table .form-check label .form-check-sign::after {top: -10px !important}

    .gantt-chart {
        display: grid;
        grid-template-rows: auto;
        font-family: Arial, sans-serif;
    }


    .gantt-header, .gantt-row {
        display: grid;
        grid-template-columns: 150px repeat(var(--columns, 200), 1fr); /* var(--columns) es una variable CSS */
        height: 30px;
    }

    .gantt-cell {
        border: .5px solid #ddd;
        padding: 0px;
        text-align: center;
        font-size: 12px;
        width: 40px;
    }


    .task-name {
        background-color: #f0f0f0;
        text-align: center;
        font-weight: bold;
        width: 150px;
        font-size: 15px;
    }

    .gantt-bar {
        position: relative;
    }

    .normal-task {
        position: absolute;
        height: 100%;
        background-color: #4caf50;
        border-radius: 0px;
    }

    .rework-task {
        position: absolute;
        height: 100%;
        background-color: #f44336;
        border-radius: 0px;
    }

     .delay-task {
        position: absolute;
        height: 100%;
        background-color: #ff9430;
        border-radius: 0px;
    }

    .general-header {
        display: grid;
        grid-template-columns: 150px repeat(var(--columns, 200), 1fr); /* var(--columns) es una variable CSS */
        background-color: #f0f0f0;
        font-weight: bold;
        text-align: center;
    }

    .time-header {
        grid-column: span var(--columns, 200); /* Cambia este número si tienes más o menos horas */
        font-size: 14px;
        padding: 8px 0;
    }

    .limite-tiempo {
        position: absolute;
        top: 30px;
        bottom: 0;
        left: 50%; /* O la posición que desees */
        width: 0;  /* El width se debe poner a 0, ya que la línea será con borde */
        border-left: 3px dotted orange; /* Agregar borde punteado */
        z-index: 10;
    }

    .tooltip {
        max-width: none; /* Elimina el límite de ancho predeterminado */
        width: 400px; /* Asegúrate de que el tooltip se ajuste al contenido */
    }

    .maquinaSeleccionada {
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
                        <i v-if="menuStep > 1"  @click="regresar(menuStep - 1)" class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/regresar.png') }}"></i>
                    </a>
                    <div v-if="!cargandoMenu && menuStep == 1">
                        <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> AÑOS </a>
                        <a class="nav-link cursor-pointer" v-for="obj in anios" @click="fetchClientes(obj.id)">
                            <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/calendario.png') }}"></i> &nbsp;
                            <span class="underline-hover">@{{obj.nombre}}</span> 
                        </a>
                    </div>    
                    <div v-if="!cargandoMenu && menuStep == 2">
                        <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> CARPETAS </a>
                        <a class="nav-link cursor-pointer" v-for="obj in clientes" @click="fetchProyectos(obj.id)">
                            <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/carpetas.png') }}"></i> &nbsp;
                            <span class="underline-hover">@{{obj.nombre}}</span> 
                        </a>
                    </div>
                    <div v-if="!cargandoMenu && menuStep == 3">
                        <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> PROYECTOS </a>
                        <a class="nav-link cursor-pointer" v-for="obj in proyectos" @click="fetchHerramentales(obj.id)">
                            <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/carpetas.png') }}"></i> &nbsp;
                            <span class="underline-hover">@{{obj.nombre}}</span> 
                        </a>
                    </div>
                    <div v-if="!cargandoMenu && menuStep == 4">
                        <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> HERRAMENTALES </a>
                        <a class="nav-link cursor-pointer" v-for="obj in herramentales" @click="fetchComponentes(obj.id)" >
                            <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/componente.png') }}"></i> &nbsp;
                            <span class="underline-hover">@{{obj.nombre}}</span> 
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
                            <span class="cursor-pointer pb-2"  v-if="ruta.herramental" @click="regresar(5)"><i class="fa fa-angle-right"></i>   &nbsp; <span class="underline-hover">@{{ruta.herramental}}</span>      </span>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-12">
                        <h2 class="bold my-0 py-1 mb-3 text-decoration-underline" style="letter-spacing: 2px">LISTA DE COMPONENTES</h2>
                    </div>
                </div>
                <hr>
                <div class="col-xl-12" v-if="!selectedHerramental">
                    <h5 class="text-muted my-4"> SELECCIONE UN HERRAMENTAL PARA VISUALIZAR EL ESTADO DE SUS COMPONENTES</h5>
                </div>
                <div class="row mt-3" v-else>
                    <div class="col-xl-12">
                        <div class="row">
                            <div class="col-xl-12">
                                <h5 class="bold my-0 py-1 mb-3" style="letter-spacing: 1px">COMPONENTES DEL HERRAMENTAL @{{ruta.herramental}}</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-4 mb-3">
                                <input
                                    class="input-file"
                                    id="archivo2"
                                    type="file"
                                    @change="handleFileChange($event)"
                                    style="display: none;"
                                />
                                <label tabindex="0" for="archivo2" class="input-file-trigger col-12 text-center">
                                    <i class="fa fa-upload"></i> CARGAR FORMATO F71-03 ANEXO 1
                                </label>
                            </div>
                            <div class="col-xl-8 pt-2">
                                Formato cargado: <strong>@{{herramental.archivo2_show != '' ? herramental.archivo2_show : 'Sin formato cargado'}}</strong>
                            </div>
                            <div class="col-xl-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Componente</th>
                                                <th class="text-center">Cantidad</th>
                                                <th class="text-center">Estado</th>
                                                <th class="text-center" style="text-transform: none !important">Fecha de compra</th>
                                                <th class="text-center" style="text-transform: none !important">Fecha componente terminado</th>
                                                <th class="text-center" style="text-transform: none !important">Fecha ensamble</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="obj in componentes">
                                                <td class="text-center">
                                                   <span class="badge badge-pill py-2 text-dark w-50" style="font-size: 15px; background-color: #c0d340 !important"> 
                                                        <img src="/paper/img/icons/componentes.png" width="20px" height="20px" alt="">
                                                        @{{obj.nombre}}
                                                    </span>
                                                </td>
                                                <td class="text-center">@{{obj.cantidad}}</td>
                                                <td class="text-center">
                                                    <span v-if="obj.ensamblado == true" class="badge badge-success py-3 w-100">ENSAMBLADO</span>
                                                    <span v-else class="badge badge-dark py-3 w-100">SIN ENSAMBLAR</span>
                                                </td>
                                                <td>@{{obj.es_compra && obj.fecha_real ? obj.fecha_real : '-'}}</td>
                                                <td>@{{!obj.es_compra && obj.fecha_terminado ? obj.fecha_terminado : '-'}}</td>
                                                <td>@{{obj.fecha_ensamblado??'-'}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
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
            user_id: {{auth()->user()->id}},
            componente: {},
            loading_button: false,
            cargando: false,            
            //MENU IZQUIERDO 
            anios: [],         
            clientes: [],      
            proyectos: [],     
            herramentales: [], 
            componentes: [],   
            cargandoMenu: true,
            menuStep: 1, 
            selectedAnio: null,
            selectedCliente: null,
            selectedProyecto: null,
            selectedHerramental: null,
            selectedComponente: null,
            ruta:{
                anio: null,
                cliente: null,
                proyecto: null,
                herramental: null,
                componente: null,
            },
            hay_retraso: false,
            herramental: {},
        },
        watch: {
           
        },
        methods:{
            async handleFileChange(event) {
                const file = event.target.files[0];
                if (!file) return;

                const formData = new FormData();
                formData.append('archivo', file);
                
                try {
                    const response = await axios.post(`/api/herramental/${this.selectedHerramental}/formato`, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });
                    await this.fetchComponentes(this.selectedHerramental);
                    swal('Correcto', 'Formato cargado correctamente', 'success');
                } catch (error) {
                    console.error('Error uploading file:', error);
                    swal('Error', 'Error al cargar el archivo', 'error');
                }
            },
            async fetchComponente(id){
                let t = this;
                this.cargando = true;                
                this.selectedComponente = id;
                this.componente = this.componentes.find(obj => obj.id == id)
                this.ruta.componente = this.componente.nombre;
                
                try {
                    const response = await axios.get(`/api/componente/${id}`)
                    t.tasks = JSON.parse(JSON.stringify(response.data.componente.ruta));
                    t.rutaAvance = JSON.parse(JSON.stringify(t.tasks));

                    t.rutaAvance.forEach(element => {
                        element.time = []
                        let find = response.data.componente.rutaAvance.find(obj => obj.id == element.id)
                        if(find){
                            element.time = find.time
                        }
                    })
                } catch (error) {
                    console.error('Error fetching componente:', error);
                } finally {
                    this.cargando = false;
                    if(this.componente.maquinas.length > 0){
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
                    }
                }
            },
            async fetchHerramental(id){
                this.cargando = true;                
                try {
                    const response = await axios.get(`/api/herramental/${id}`)
                    this.herramental = response.data.herramental;
                } catch (error) {
                    console.error('Error fetching herramental:', error);
                } finally {
                    this.cargando = false;
                }
            },
            eliminarArchivo(maquina, index) {
                maquina.archivos.splice(index, 1);
            },
            regresar(step){
                switch (step) {
                    case 1:
                        this.ruta = {
                            anio: null,
                            cliente: null,
                            proyecto: null,
                            herramental: null,
                            componente: null,
                        } 
                        this.selectedAnio = null;
                        this.selectedCliente = null;
                        this.selectedProyecto = null;
                        this.selectedHerramental = null;
                        this.selectedComponente = null;
                    break;
                    case 2:
                        this.ruta.cliente = null;
                        this.ruta.proyecto = null;
                        this.ruta.herramental = null;
                        this.ruta.componente = null;

                        this.selectedCliente = null;
                        this.selectedProyecto = null;
                        this.selectedHerramental = null;
                        this.selectedComponente = null;

                    break;
                    case 3:
                        this.ruta.proyecto = null;
                        this.ruta.herramental = null;
                        this.ruta.componente = null;

                        this.selectedProyecto = null;
                        this.selectedHerramental = null;
                        this.selectedComponente = null;
                    break;
                    case 4:
                        this.ruta.herramental = null;
                        this.selectedHerramental = null;
                        this.ruta.componente = null;
                    break;
                    case 5:
                        this.ruta.componente = null;
                        this.selectedComponente = null;
                        this.selectedComponente = null;
                    break;
                }
                this.menuStep = step;
            },
            async fetchAnios() {
                this.cargandoMenu = true
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
                this.herramental = this.herramentales.find(obj => obj.id == herramentalId);
                this.ruta.herramental = this.herramental?.nombre;

                Vue.nextTick(function() {
                    document.querySelector("html").classList.add('js');
                    let fileInput = document.querySelector(".input-file");
                    let button = document.querySelector(".input-file-trigger");

                    button.addEventListener("keydown", function(event) {
                        if (event.keyCode == 13 || event.keyCode == 32) {
                            fileInput.focus();
                        }
                    });

                    button.addEventListener("click", function(event) {
                        fileInput.focus();
                        return false;
                    });
                });

                try {
                    const response = await axios.get(`/api/herramentales/${herramentalId}/componentes?area=ensamble`);
                    this.componentes = response.data.componentes;
                } catch (error) {
                    console.error('Error fetching componentes:', error);
                } finally {
                    this.cargando = false;
                }
            },
            async liberar(){
                
                if (!this.componente.descripcion_trabajo?.trim() || !this.componente.herramientas_corte?.trim()) {
                    swal('Errores de validación', `Todos los campos son obligatorios para liberar el componente.`, 'error');
                    return;
                }
                
                let tieneArchivo = this.componente.maquinas.some(maquina =>
                    maquina.archivos.some(a => a.archivo || a.id)
                );
                
                if (!tieneArchivo) {
                    swal('Errores de validación', 'Debe cargar al menos un archivo por máquina.', 'error');
                    return;
                }

                this.hay_retraso = false;
                await this.fetchComponente(this.selectedComponente);
                await this.cargarRuta();
                let programar = this.rutaAvance.find(obj => obj.id === 2)
                
                if(programar){
                    let retraso = programar.time.find(obj => obj.type === 'delay')
                    if(retraso){
                        this.hay_retraso = true;
                        $('#modalRetraso').modal();
                        return;
                    }
                }
                this.guardar(true);
            },
            async guardar(liberarComponente){                
                let t = this

                if(this.hay_retraso && liberarComponente && !this.componente.retraso_programacion?.trim()){
                     swal('Campos obligatorios', 'Debe ingresar un motivo de retraso.', 'info');
                    return false;
                }
                t.cargando = true;
                t.loading_button = true;

                let formData = new FormData();

                t.componente.maquinas.forEach((maquina, maquinaIndex) => {
                    maquina.archivos.forEach((archivo, archivoIndex) => {
                        if (archivo.archivo)
                            formData.append(`archivo[${maquina.maquina_id}][${archivoIndex}]`, archivo.archivo);
                        if (archivo.id) 
                            formData.append(`archivo_ids[${maquina.maquina_id}][${archivoIndex}]`, archivo.id);
                    });
                });
                formData.append('data', JSON.stringify(t.componente));

                try {
                    const response = await axios.post(`/api/componente/${t.selectedComponente}/programacion/${liberarComponente}`, formData, {
                        headers: { 'Content-Type': 'multipart/form-data' }
                    });

                    swal('Correcto', liberarComponente ? 'Componente liberado correctamente' : 'Información guardada correctamente', 'success');
                    await t.fetchComponentes(t.selectedHerramental);
                    await t.fetchComponente(t.selectedComponente);
                    t.loading_button = false;
                    $('#modalRetraso').modal('hide');
                } catch (error) {
                    console.log(error);
                    const mensaje = error.response?.data?.error || 'Error al guardar el componente.';
                    swal('Error', mensaje, 'error');
                    t.cargando = false;
                    t.loading_button = false;
                }
            },
            async navigateFromUrlParams() {
                const queryParams = new URLSearchParams(window.location.search);
                const anioId = queryParams.get('a');
                const clienteId = queryParams.get('c');
                const proyectoId = queryParams.get('p');
                const herramentalId = queryParams.get('h');
                const componenteId = queryParams.get('co');

                try {
                    if (anioId) {
                        await this.fetchClientes(anioId);
                    }
                    if (clienteId) {
                        await this.fetchProyectos(clienteId);
                    }
                    if (proyectoId) {
                        await this.fetchHerramentales(proyectoId);
                    }
                    if (herramentalId) {
                        await this.fetchComponentes(herramentalId);
                    }
                    if (componenteId && componenteId != 'null') {
                        await this.fetchComponente(componenteId);
                    }
                } catch (error) {
                    console.error("Error navigating from URL parameters:", error);
                }
            },
        },
        mounted: async function () {
            let t = this;
            await t.fetchAnios();
            this.navigateFromUrlParams();        
        }

                
    })

    </script>



        
@endpush