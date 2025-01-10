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
                            <span class="cursor-pointer pb-2"  v-if="ruta.herramental"><i class="fa fa-angle-right"></i>   &nbsp; <span class="underline-hover">@{{ruta.herramental}}</span>      </span>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-7">
                        <h2 class="bold my-0 py-1 mb-3 text-decoration-underline" style="letter-spacing: 2px">COMPRA DE COMPONENTES</h2>
                    </div>
                    <div class="col-xl-3 form-group" v-if="selectedHerramental">
                        <select name="" id="" class="form-control" v-model="estatusCompra" @change="fetchComponentes(selectedHerramental)">
                            <option value="-1">TODOS LOS COMPONENTES</option>
                            <option value="1">SIN FECHA DE PEDIDO</option>
                            <option value="2">SIN FECHA ESTIMADA DE RECEPCION</option>
                            <option value="3">SIN FECHA DE RECIBIDO</option>
                        </select>
                    </div>
                    <div class="col-xl-2"  v-if="selectedHerramental" style="border-left: 1px solid  #ededed">
                        <button class="btn btn-block mt-0" @click="guardarComponentes"><i class="fa fa-save"></i>    GUARDAR</button>
                    </div>
                </div>
                <div class="col-xl-12" v-if="!selectedHerramental">
                    <h5 class="text-muted my-4"> SELECCIONE UN HERRAMENTAL PARA VER SUS COMPONENTES A COMPRAR</h5>
                </div>
                <div class="row" v-else>
                    <div class="col-xl-12">
                        <table class="table">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 25%" >Componente</th>
                                    <th style="width: 15%" >Cantidad</th>
                                    <th style="width: 15%" style="text-transform: normal !important">Fecha de solicitud</th>
                                    <th style="width: 15%" style="text-transform: normal !important">Fecha pedido</th>
                                    <th style="width: 15%" style="text-transform: normal !important">Fecha estimada recepcion</th>
                                    <th style="width: 15%" style="text-transform: normal !important">Fecha recibido</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="c in componentes">
                                    <td class="bold">
                                        @{{c.nombre}} <br>
                                        <span v-if="c.cancelado" class="badge badge-danger">CANCELADO</span>
                                    </td>
                                    <td><input disabled class="form-control text-center" type="number" step="1" v-model="c.cantidad"></td>
                                    <td><input class="form-control text-center" type="date"  v-model="c.fecha_solicitud"></td>
                                    <td><input class="form-control text-center" type="date"  v-model="c.fecha_pedido"></td>
                                    <td><input class="form-control text-center" type="date"  v-model="c.fecha_estimada"></td>
                                    <td><input class="form-control text-center" type="date"  v-model="c.fecha_real"></td>
                                </tr>
                            </tbody>
                        </table>
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
            estatusCompra: -1,
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
            ruta:{
                anio: null,
                cliente: null,
                proyecto: null,
                herramental: null,
            },
        },
        computed: {

        },
        methods:{
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
                    const response = await axios.get(`/api/herramentales/${herramentalId}/componentes?area=compras&estatusCompra=${this.estatusCompra}`);
                    this.componentes = response.data.componentes;
                } catch (error) {
                    console.error('Error fetching componentes:', error);
                } finally {
                    this.cargando = false;
                }
            },
            async guardarComponentes(mostrarAlerta = true){
                let t = this
                t.cargando = true;
                let formData = new FormData();
                formData.append('data', JSON.stringify(t.componentes));
                try {
                    const response = await axios.post(`/api/componente/${t.selectedHerramental}/compras`, formData, {
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
                    t.cargando = false
                    return false;
                }  
            },
            async liberarHerramental() {
                let t = this;

                let errores = [];
                t.componentes.forEach((componente, index) => {  
                    if (!componente.fecha_solicitud || !componente.fecha_pedido || !componente.fecha_estimada || !componente.fecha_real ) {
                        errores.push(`Todos los campos son obligatorios para liberar en ${componente.nombre}.`);
                    }
                });

                if (errores.length > 0) {
                    swal('Errores de validación', errores.join('\n'), 'error');
                    return;
                }

                
                t.cargando = true;
                let respuesta = await t.guardarComponentes(false);
                if(respuesta){
                    try {
                        const response = await axios.put(`/api/liberar-herramental-compras/${t.selectedHerramental}`);
                        t.cargando = false;
                        swal('Éxito', 'Componentes liberados correctamente', 'success');
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
            async navigateFromUrlParams() {
                const queryParams = new URLSearchParams(window.location.search);
                const anioId = queryParams.get('a');
                const clienteId = queryParams.get('c');
                const proyectoId = queryParams.get('p');
                const herramentalId = queryParams.get('h');

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