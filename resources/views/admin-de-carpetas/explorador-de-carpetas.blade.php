@extends('layouts.app', [
'class' => '',
'elementActive' => 'dashboard'
])



@section('content')
<div id="vue-app" v-show="!cargandoMenu" v-cloak>
    <div class="card shadow col-md-12" style="padding: 30px !important">
        <div class="row">
            <div class="col-md-5 mb-0">
                <h2 class="bold my-0 py-1" style="letter-spacing: 1px;">Explorador de carpetas</h2>
            </div>
            <div class="col-md-7 text-right">
                <button class="btn btn-secondary">Agregar</button>
            </div>
        </div>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-xl ">
            <div class="container-fluid">
                <div class="navbar-wrapper">
                    <p>
                        <span class="cursor-pointer pb-2" @click="regresar(1)">
                            <i class="fa fa-home"></i> &nbsp;
                        </span>

                        <span class="cursor-pointer pb-2" v-if="ruta.anio && menuStep >= 2" @click="regresar(2)">
                            <i class="fa fa-angle-right"></i> &nbsp;
                            <span class="underline-hover">@{{ruta.anio}}</span>
                        </span>

                        <span class="cursor-pointer pb-2" v-if="ruta.cliente  && menuStep >= 3">
                            <i class="fa fa-angle-right"></i> &nbsp; <span class="underline-hover">@{{ruta.cliente}}</span> &nbsp;
                        </span>
                    </p>
                </div>
            </div>
        </nav>

        <div class="col-md-12 table-responsive" v-show="!cargandoMenu">
            <table class="table align-items-center">
                <thead class="thead-light">
                    <tr>
                        <th scope="col">Nombre</th>
                        <th scope="col">Fecha creación</th>
                        <th scope="col" class="no-sort">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Años -->
                    <tr v-if="!cargandoMenu && menuStep == 1" v-for="anio in anios" :key="anio.id">
                        <td>@{{ anio.nombre }}</td>
                        <td>@{{ formatFecha(anio.created_at) }}</td>
                        <td>
                            <div class="btn-group" style="border: 2px solid #121935; border-radius: 10px !important">
                                <button class="btn btn-sm btn-link actions" style=""
                                    @click="fetchClientes(anio)">
                                    <i class="fa fa-folder-open"></i> Abrir
                                </button>
                                <button class="btn btn-sm btn-link actions"
                                    @click="editar(anio)" data-toggle="tooltip" data-placement="bottom" title="Editar">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-link actions"
                                    @click="eliminar(anio)" data-toggle="tooltip" data-placement="bottom" title="Eliminar">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Clientes -->
                    <tr v-if="!cargandoMenu && menuStep == 2" v-for="cliente in clientes" :key="cliente.id">
                        <td>@{{ cliente.nombre }}</td>
                        <td>@{{ formatFecha(cliente.created_at) }}</td>
                        <td>
                            <div class="btn-group" style="border: 2px solid #121935; border-radius: 10px !important">
                                <button class="btn btn-sm btn-link actions"
                                    @click="fetchProyectos(cliente)">
                                    <i class="fa fa-folder-open"></i> Abrir
                                </button>
                                <button class="btn btn-sm btn-link actions"
                                    @click="editar(cliente)" data-toggle="tooltip" data-placement="bottom" title="Editar">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-link actions"
                                    @click="eliminar(cliente)" data-toggle="tooltip" data-placement="bottom" title="Eliminar">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Proyecots -->
                    <tr v-if="!cargandoMenu && menuStep == 3" v-for="proyecto in proyectos" :key="proyecto.id">
                        <td>@{{ proyecto.nombre }}</td>
                        <td>@{{ formatFecha(proyecto.created_at) }}</td>
                        <td>
                            <div class="btn-group" style="border: 2px solid #121935; border-radius: 10px !important">
                                <button class="btn btn-sm btn-link actions"
                                    @click="editar(proyecto)" data-toggle="tooltip" data-placement="bottom" title="Editar">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-link actions"
                                    @click="eliminar(proyecto)" data-toggle="tooltip" data-placement="bottom" title="Eliminar">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>


        <!-- Botón regresar -->
        <div v-if="menuStep > 1" class="mt-3">
            <button class="btn btn-secondary" @click="regresar(menuStep - 1)">
                <i class="fa fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>


</div>
@endsection

@push('scripts')
<script type="text/javascript">
    const appCarpetas = new Vue({
        el: '#vue-app',
        data: {
            anios: [],
            clientes: [],
            proyectos: [],
            cargandoMenu: true,
            menuStep: 1,
            ruta: {
                anio: null,
                cliente: null,
                proyecto: null,
            },
        },
        mounted: async function() {
            await this.fetchAnios();
        },
        methods: {
            // Obtener años
            async fetchAnios() {
                this.cargandoMenu = true;
                try {
                    const response = await axios.get('/api/anios');
                    this.anios = response.data.anios;
                    this.menuStep = 1;
                } catch (error) {
                    console.error('Error fetching años:', error);
                } finally {
                    this.cargandoMenu = false;
                }
            },
            // Obtener clientes de un año
            async fetchClientes(anio) {
                this.cargandoMenu = true;
                this.ruta.anio = this.anios.find(obj => obj.id == anio.id)?.nombre;
                try {
                    const response = await axios.get(`/api/anios/${anio.id}/clientes`);
                    this.clientes = response.data.clientes;
                    this.menuStep = 2;
                } catch (error) {
                    console.error('Error fetching clientes:', error);
                } finally {
                    this.cargandoMenu = false;
                }
            },
            // Obtener proyectos de un cliente
            async fetchProyectos(cliente) {
                this.cargandoMenu = true;
                this.ruta.cliente = this.clientes.find(obj => obj.id == cliente.id)?.nombre;
                try {
                    const response = await axios.get(`/api/clientes/${cliente.id}/proyectos`);
                    this.proyectos = response.data.proyectos;
                    this.menuStep = 3;
                } catch (error) {
                    console.error('Error fetching proyectos:', error);
                } finally {
                    this.cargandoMenu = false;
                }
            },
            regresar(step) {
                switch (step) {
                    case 1:
                        this.ruta = {
                            anio: null,
                            cliente: null,
                            proyecto: null,
                        }
                        this.selectedAnio = null;
                        this.selectedCliente = null;
                        this.selectedProyecto = null;
                        break;
                    case 2:
                        this.ruta.cliente = null;
                        this.ruta.proyecto = null;

                        this.selectedCliente = null;
                        this.selectedProyecto = null;
                        break;
                    case 3:
                        this.ruta.proyecto = null;
                        this.selectedProyecto = null;
                        break;
                }
                this.menuStep = step;
            },
            formatFecha(fecha) {
                return new Date(fecha).toLocaleDateString('es-MX', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            },
            // Acciones
            editar(obj) {

            },
            eliminar(obj) {

            }
        },
    });
</script>
@endpush