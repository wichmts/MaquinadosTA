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

    .checkbox-wrapper-18 {
        display: flex;               /* Usa Flexbox para centrar */
        justify-content: center;     /* Centra horizontalmente */
        align-items: center;         /* Centra verticalmente si es necesario */
        height: 100%;                /* Asegúrate de que el contenedor tenga altura */
    }

    .checkbox-wrapper-18 .round {
        position: relative;
    }

    .checkbox-wrapper-18 .round label {
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 50%;
        cursor: pointer;
        height: 28px;
        width: 28px;
        display: block;
    }

    .checkbox-wrapper-18 .round label:after {
        border: 2px solid #fff;
        border-top: none;
        border-right: none;
        content: "";
        height: 6px;
        left: 8px;
        opacity: 0;
        position: absolute;
        top: 9px;
        transform: rotate(-45deg);
        width: 12px;
    }

    .checkbox-wrapper-18 .round input[type="checkbox"] {
        visibility: hidden;
        display: none;
        opacity: 0;
    }

    .checkbox-wrapper-18 .round input[type="checkbox"]:checked + label {
        background-color: #66bb6a;
        border-color: #66bb6a;
    }

    .checkbox-wrapper-18 .round input[type="checkbox"]:checked + label:after {
        opacity: 1;
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
            <div class="col-xl-2 pt-3" style="background-color: #f1f1f1; height: calc(100vh - 107.3px)">
                <div class="nav flex-column nav-pills " id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link cursor-pointer text-right text-muted" >
                        <i v-if="menuStep > 1"  @click="regresar(menuStep - 1)" class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/regresar.png') }}"></i>
                    </a>
                    <div v-if="!cargandoMenu && menuStep == 1">
                        <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> AÑOS </a>
                        <a class="nav-link cursor-pointer" v-for="obj in anios" @click="fetchClientes(obj.id)">
                            <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/calendario.png') }}"></i> &nbsp;
                            <span class="underline-hover">@{{obj.nombre}}</span>  {{--<i class="fa fa-caret-right"></i>    --}}
                        </a>
                    </div>    
                    <div v-if="!cargandoMenu && menuStep == 2">
                        <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> CARPETAS </a>
                        <a class="nav-link cursor-pointer" v-for="obj in clientes" @click="fetchProyectos(obj.id)">
                            <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/carpetas.png') }}"></i> &nbsp;
                            <span class="underline-hover">@{{obj.nombre}}</span>  {{--<i class="fa fa-caret-right"></i>    --}}
                        </a>
                    </div>
                    <div v-if="!cargandoMenu && menuStep == 3">
                        <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> PROYECTOS </a>
                        <a class="nav-link cursor-pointer" v-for="obj in proyectos" @click="fetchHerramentales(obj.id)">
                            <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/carpetas.png') }}"></i> &nbsp;
                            <span class="underline-hover">@{{obj.nombre}}</span>  {{--<i class="fa fa-caret-right"></i>    --}}
                        </a>
                    </div>
                    <div v-if="!cargandoMenu && menuStep == 4">
                        <a class="nav-link" style="color:#939393 !important; letter-sapcing: 2px !important"> HERRAMENTALES </a>
                        <a class="nav-link cursor-pointer" v-for="obj in herramentales" @click="fetchComponentes(obj.id)" >
                            <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/componente.png') }}"></i> &nbsp;
                            <span class="underline-hover">@{{obj.nombre}}</span>  {{--<i class="fa fa-caret-right"></i>    --}}
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
                        <h2 class="bold my-0 py-1 mb-3 text-decoration-underline" style="letter-spacing: 2px">CORTE DE MP</h2>
                    </div>
                    <div class="col-xl-3 form-group" v-if="selectedHerramental">
                        <select class="form-control" @change="fetchComponentes(selectedHerramental)" v-model="estatusCorte">
                            <option value="-1">TODOS LOS COMPONENTES</option>
                            <option value="inicial">POR CORTAR</option>
                            <option value="proceso">EN PROCESO</option>
                            <option value="pausado">PAUSADO</option>
                            <option value="finalizado">FINALIZADO</option>
                        </select>
                    </div>
                    <div class="col-xl-2"  v-if="selectedHerramental">
                        <div class="row">
                            <div class="col">
                                <button class="btn btn-block btn-success mt-0" @click="liberarHerramental"><i class="fa fa-check-double"></i> LIBERAR</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12" v-if="!selectedHerramental">
                    <h5 class="text-muted my-4"> SELECCIONE UN HERRAMENTAL PARA VER LOS CORTES A REALIZAR</h5>
                </div>
                <div class="row" v-else>
                    <div class="col-xl-12" style="overflow-x: auto !important;">
                        <table class="table table-sm" id="tabla-principal">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 10%"> Componente </th>
                                    <th style="width: 8%"> Cantidad </th>
                                    <th style="width: 8%"> Largo </th>
                                    <th style="width: 8%"> Ancho </th>
                                    <th style="width: 8%"> Alto </th>
                                    <th style="width: 10%"> Material </th>
                                    <th style="width: 12%"> Estatus </th>
                                    <th style="width: 35%"> Acciones </th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="c in componentes">
                                    <td class="bold">
                                        @{{c.nombre}} <br>
                                        <span v-if="c.cancelado" class="badge badge-danger">CANCELADO</span>
                                    </td>
                                    <td><input readonly class="form-control text-center" type="number" step="1" v-model="c.cantidad"></td>
                                    <td><input readonly class="form-control text-center" type="text"  v-model="c.largo"></td>
                                    <td><input readonly class="form-control text-center" type="text"  v-model="c.ancho"></td>
                                    <td><input readonly class="form-control text-center" type="text"  v-model="c.alto"></td>
                                    <td><input readonly class="form-control text-center" type="text"  v-model="c.material_nombre"></td>
                                    <td>
                                        <span v-if="c.estatus_corte == 'inicial'" class="py-2 w-100 badge badge-warning" style="font-size: 13px">POR CORTAR</span>
                                        <span v-if="c.estatus_corte == 'proceso'" class="py-2 w-100 badge badge-info" style="font-size: 13px">EN PROCESO...</span>
                                        <span v-if="c.estatus_corte == 'pausado'" class="py-2 w-100 badge badge-dark" style="font-size: 13px">PAUSADO</span>
                                        <span v-if="c.estatus_corte == 'finalizado'" class="py-2 w-100 badge badge-success" style="font-size: 13px">FINALIZADO</span>
                                    </td>
                                    <td>
                                        <button :disabled="c.estatus_corte == 'finalizado' || c.estatus_corte == 'proceso'" class=" mt-1 btn btn-default btn-sm" @click="cambiarEstatusCorte(c.id, 'proceso')"><i class="far fa-play-circle"></i> Iniciar corte</button>
                                        <button :disabled="c.estatus_corte == 'inicial' || c.estatus_corte == 'finalizado' || c.estatus_corte == 'pausado'" class=" mt-1 btn btn-default btn-sm" @click="cambiarEstatusCorte(c.id, 'pausado')"><i class="far fa-pause-circle"></i> Pausar corte</button>
                                        <button :disabled="c.estatus_corte == 'inicial' || c.estatus_corte == 'finalizado' " class=" mt-1 btn btn-default btn-sm" @click="finalizarCorte(c.id)"><i class="far fa-stop-circle"></i> Finalizar corte</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalFinalizarCorte" tabindex="-1" aria-labelledby="modalFinalizarCorteLabel" aria-hidden="true">
            <div class="modal-dialog" style="min-width: 35%;">
                <div class="modal-content" >
                    <div class="modal-header">
                        <h3 class="modal-title" id="modalFinalizarCorteLabel">
                            <span>FINALIZAR CORTE @{{componente.nombre}}</span>
                        </h3>
                        <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xl-12 form-group">
                                <label class="bold" >Seleccione MP</label>
                                <select class="form-control" v-model="movimiento.material_id" @change="fetchHojas(movimiento.material_id)">
                                    <option v-for="m in materiales" :value="m.id"> @{{m.nombre}} </option>
                                </select>
                            </div>
                            <div class="col-xl-12 form-group">
                                <label class="bold" >Seleccione hoja</label>
                                <select class="form-control" v-model="movimiento.hoja_id">
                                    <option :value="null" disabled>Seleccione la hoja de donde realizo el corte...</option>
                                    <option v-for="h in hojas" :value="h.id">Consecutivo @{{h.consecutivo}}.- Espesor: @{{h.espesor}}, Actuales: @{{h.ancho_saldo}} x @{{h.largo_saldo}}, Peso: @{{h.peso_saldo}} </option>
                                </select>
                            </div>
                            <div class="col-xl-4 form-group">
                                <label class="bold" >Largo restante de la hoja</label>
                                <input type="text" class="form-control" v-model="movimiento.largo">
                            </div>
                            <div class="col-xl-4 form-group">
                                <label class="bold" >Ancho restante de la hoja</label>
                                <input type="text" class="form-control" v-model="movimiento.ancho">
                            </div>
                            <div class="col-xl-4 form-group">
                                <label class="bold" >Peso restante de la hoja</label>
                                <input type="text" class="form-control" v-model="movimiento.peso">
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-xl-12 text-right">
                                <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                                <button class="btn btn-secondary" v-if="!loading_button" type="button" @click="finalizarCorteAPI()"><i class="fa fa-check-circle"></i> FINALIZAR CORTE</button>
                                <button class="btn btn-secondary" type="button" disabled v-if="loading_button"><i class="fa fa-spinner spin"></i> FINALIZANDO, ESPERE ...</button>
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
            loading_button: false,
            cargando: false,
            componente: {},
            materiales: [],
            hojas: [],
            
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
            movimiento: {},
            ruta:{
                anio: null,
                cliente: null,
                proyecto: null,
                herramental: null,
            },
            estatusCorte: '-1',
        },
        computed: {
          
        },
        methods:{
            cambiarEstatusCorte(id, nuevoEstatus){
                let t = this
                axios.put(`api/corte/cambio-estatus/${id}`, {estatus: nuevoEstatus} ).then(response => {
                    if(response.data.success){
                        t.fetchComponentes(t.selectedHerramental)    
                    }
                })
            },
            finalizarCorteAPI(){
                let t = this
                axios.put(`api/corte/finalizar/${t.componente.id}`, {movimiento: t.movimiento} ).then(response => {
                    if(response.data.success){
                        t.fetchComponentes(t.selectedHerramental)    
                        $('#modalFinalizarCorte').modal('toggle');
                    }
                })
            },
            async finalizarCorte(id){
                let t = this
                t.componente = t.componentes.find(element => element.id == id)
                await t.fetchHojas(t.componente.material_id);

                t.movimiento = {
                    material_id: t.componente.material_id, 
                    hoja_id: null, 
                    largo: 0,
                    ancho: 0,
                    peso: 0,
                }
                $('#modalFinalizarCorte').modal()
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
                    const response = await axios.get(`/api/herramentales/${herramentalId}/componentes?area=corte&estatusCorte=${this.estatusCorte}`);
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
            async fetchMateriales() {
                this.cargando = true
                try {
                    const response = await axios.get(`/api/materiales`);
                    this.materiales = response.data.materiales;
                } catch (error) {
                    console.error('Error fetching materiales:', error);
                } finally {
                    this.cargando = false;
                    this.materialSelected = this.materiales[0]?.id??null;
                    this.fetchHojas();
                }
            },
            async fetchHojas(material_id) {
                this.cargando = true
                try {
                    const response = await axios.get(`/api/hojas/${material_id}`);
                    this.hojas = response.data.hojas;
                } catch (error) {
                    console.error('Error fetching hojas:', error);
                } finally {
                    this.cargando = false;
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
            await t.fetchMateriales();
            this.navigateFromUrlParams();
        }

                
    })

    </script>



        
@endpush