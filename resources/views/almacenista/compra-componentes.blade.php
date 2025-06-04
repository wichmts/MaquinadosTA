@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'dashboard'
])

@section('styles')
<link rel="stylesheet" href="{{ asset('paper/css/paper-dashboard-responsivo.css') }}?v={{ time() }}">
@endsection
<style>
    .form-group input[type="text"] {
        height: 30px !important
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

        <div class="wrapper " v-cloak v-show="!cargando">
            <div class="sidebar" data-color="white" data-active-color="danger">
                
                <div class="sidebar-wrapper">
                    <ul class="nav">
                        <li>
                            <div class="nav flex-column nav-pills " id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                <a class="nav-link cursor-pointer text-right text-muted" >
                                    <i v-if="menuStep > 3"  @click="regresar(menuStep - 1)" class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/regresar.png') }}"></i>
                                </a>
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
                        </li>
                    </ul>
                </div>
            </div>
            <div class="main-panel">
                <!-- Navbar -->
                <nav class="navbar navbar-expand-xl navbar-absolute fixed-top navbar-transparent">
                    <div class="container-fluid">
                        <div class="navbar-wrapper">
                            <div class="navbar-toggle">
                                <button type="button" class="navbar-toggler">
                                    <span class="navbar-toggler-bar bar1"></span>
                                    <span class="navbar-toggler-bar bar2"></span>
                                    <span class="navbar-toggler-bar bar3"></span>
                                </button>
                            </div>
                            <p style="">
                                <span class="cursor-pointer pb-2" @click="regresar(3)"><i class="fa fa-home"></i> &nbsp;</span>
                                <span class="cursor-pointer pb-2"  v-if="ruta.proyecto" @click="regresar(4)"><i class="fa fa-angle-right"></i>   &nbsp; <span class="underline-hover">@{{ruta.proyecto}}</span>     &nbsp;</span>
                                <span class="cursor-pointer pb-2"  v-if="ruta.herramental"><i class="fa fa-angle-right"></i>   &nbsp; <span class="underline-hover">@{{ruta.herramental}}</span>      </span>
                            </p>
                        </div>
                    </div>
                </nav>
                <!-- End Navbar -->
                <div class="content">
                    <div class="row">
                        <div class="col-lg-12 mt-0" style="height: 79vh !important; overflow-y: scroll !important">
                            <div class="row">
                                <div class="col-lg-6">
                                    <h2 class="bold my-0 py-1 mb-3 text-decoration-underline" style="letter-spacing: 2px">COMPRA DE COMPONENTES</h2>
                                </div>
                                <div class="col-lg-4 form-group" v-if="selectedHerramental">
                                    <select name="" id="" class="form-control" v-model="estatusCompra" @change="fetchComponentes(selectedHerramental)">
                                        <option value="-1">TODOS LOS COMPONENTES</option>
                                        <option value="1">SIN FECHA DE PEDIDO</option>
                                        <option value="2">SIN FECHA ESTIMADA DE RECEPCION</option>
                                        <option value="3">SIN FECHA DE RECIBIDO</option>
                                    </select>
                                </div>
                                <div class="col-lg-2"  v-if="selectedHerramental" style="border-left: 1px solid  #ededed">
                                    <button class="btn btn-block mt-0" @click="guardarComponentes"><i class="fa fa-save"></i>    GUARDAR</button>
                                </div>
                            </div>
                            <div class="col-lg-12" v-if="!selectedHerramental">
                                <h5 class="text-muted my-4"> SELECCIONE UN HERRAMENTAL PARA VER SUS COMPONENTES A COMPRAR</h5>
                            </div>
                            <div class="row" v-else>
                                <div class="col-lg-12">
                                    <table class="table table-striped">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Componente</th>
                                                <th>Proveedor / Material</th>
                                                <th>Descripción</th>
                                                <th>Cant.</th>
                                                <th>Costo unit.</th>
                                                <th>Fecha de solicitud</th>
                                                <th>Fecha pedido</th>
                                                <th>Fecha estimada recepcion</th>
                                                <th>Fecha recibido</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="c in componentes">
                                                <td class="bold">
                                                    @{{c.nombre}} <br>
                                                    <span v-if="c.cancelado" class="badge badge-danger">CANCELADO</span>
                                                </td>
                                                <td><input disabled class="form-control text-center" type="text" v-model="c.proveedor"></td>
                                                <td>
                                                    <small>@{{c.descripcion}}</small>
                                                </td>
                                                <td><input disabled class="form-control text-center" type="number" step="1" v-model="c.cantidad"></td>
                                                <td><input class="form-control text-center" type="number" step="any" v-model="c.costo_unitario"></td>
                                                <td><input class="form-control text-center" type="date" :disabled="c.cancelado || c.fecha_real"  v-model="c.fecha_solicitud"></td>
                                                <td><input class="form-control text-center" type="date" :disabled="c.cancelado || c.fecha_real"  v-model="c.fecha_pedido"></td>
                                                <td><input class="form-control text-center" type="date" :disabled="c.cancelado || c.fecha_real"  v-model="c.fecha_estimada"></td>
                                                <td><input class="form-control text-center" type="date" :disabled="c.cancelado || c.fecha_real"  v-model="c.fecha_real_liberada"></td>
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
                proyecto: null,
                herramental: null,
            },
        },
        computed: {

        },
        methods:{
           regresar(step){
                switch (step) {
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
            async fetchProyectos(clienteId = -1) {
                this.cargandoMenu = true
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
            // async liberarHerramental() {
            //     let t = this;

            //     let errores = [];
            //     t.componentes.forEach((componente, index) => {  
            //         if (!componente.fecha_solicitud || !componente.fecha_pedido || !componente.fecha_estimada || !componente.fecha_real ) {
            //             errores.push(`Todos los campos son obligatorios para liberar en ${componente.nombre}.`);
            //         }
            //     });

            //     if (errores.length > 0) {
            //         swal('Errores de validación', errores.join('\n'), 'error');
            //         return;
            //     }

                
            //     t.cargando = true;
            //     let respuesta = await t.guardarComponentes(false);
            //     if(respuesta){
            //         try {
            //             const response = await axios.put(`/api/liberar-herramental-compras/${t.selectedHerramental}`);
            //             t.cargando = false;
            //             swal('Éxito', 'Componentes liberados correctamente', 'success');
            //             t.fetchComponentes(t.selectedHerramental);

            //         } catch (error) {
            //             t.cargando = false;
            //             console.error('Error al liberar el componente:', error);
            //             swal('Error', 'Ocurrió un error al liberar el herramental', 'error');
            //         }
            //     }else{
            //         swal('Error', 'Ocurrió un error al guardar la informacion de los componentes', 'error');
            //         t.cargando = false;
            //     }
            // },
           async navigateFromUrlParams() {
                const queryParams = new URLSearchParams(window.location.search);
                const proyectoId = queryParams.get('p');
                const herramentalId = queryParams.get('h');

                try {
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
            await t.fetchProyectos();
            this.navigateFromUrlParams();
        }

                
    })

    </script>



        
@endpush