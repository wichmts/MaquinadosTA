@extends('layouts.app', [
'class' => '',
'elementActive' => 'dashboard'
])



@section('content')
<div id="vue-app" v-show="!cargandoMenu" v-cloak>
    <div class="col-md-12" style="padding: 30px !important">
        <div class="row">
            <div class="col-md-5 mb-0">
                <h2 class="bold my-0 py-1" style="letter-spacing: 1px;">Explorador de carpetas</h2>
            </div>
            <div class="col-md-7 text-right">
                <button class="btn btn-secondary cursor-pointer" v-if="menuStep == 1" @click="abrirModalNuevo('año', 'Año')">
                    <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/plus.png') }}"></i> &nbsp;
                    <span class="underline-hover">Nuevo año...</span>
                </button>

                <button class="btn btn-secondary cursor-pointer" v-if="menuStep == 2" @click="abrirModalNuevo('carpeta', 'Nombre de la carpeta')">
                    <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/plus.png') }}"></i> &nbsp;
                    <span class="underline-hover">Nueva carpeta...</span>
                </button>

                <button class="btn btn-secondary cursor-pointer" v-if="menuStep == 3" @click="abrirModalNuevo('proyecto', 'Nombre del Proyecto')">
                    <i class="nc-icon"><img height="17px" src="{{ asset('paper/img/icons/plus.png') }}"></i> &nbsp;
                    <span class="underline-hover">Nuevo proyecto...</span>
                </button>
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

        <div class="col-md-12 table-responsive card shadow" v-show="!cargandoMenu" v-cloak>
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
                                    @click="fetchClientes(anio.id)">
                                    <i class="fa fa-folder-open"></i> Abrir
                                </button>
                                <button class="btn btn-sm btn-link actions"
                                    @click="abrirModalEditar('año', 'Nuevo año', anio.id)" data-toggle="tooltip" data-placement="bottom" title="Editar">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-link actions"
                                    @click="guardarEliminado(anio.id, 'año')" data-toggle="tooltip" data-placement="bottom" title="Eliminar">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Clientes / carpetas -->
                    <tr v-if="!cargandoMenu && menuStep == 2" v-for="cliente in clientes" :key="cliente.id">
                        <td>@{{ cliente.nombre }}</td>
                        <td>@{{ formatFecha(cliente.created_at) }}</td>
                        <td>
                            <div class="btn-group" style="border: 2px solid #121935; border-radius: 10px !important">
                                <button class="btn btn-sm btn-link actions"
                                    @click="fetchProyectos(cliente.id)">
                                    <i class="fa fa-folder-open"></i> Abrir
                                </button>
                                <button class="btn btn-sm btn-link actions"
                                    @click="abrirModalEditar('carpeta', 'Nuevo nobre de la carpeta', cliente.id)" data-toggle="tooltip" data-placement="bottom" title="Editar">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-link actions"
                                    @click="guardarEliminado(cliente.id, 'carpeta')" data-toggle="tooltip" data-placement="bottom" title="Eliminar">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Proyectos -->
                    <tr v-if="!cargandoMenu && menuStep == 3" v-for="proyecto in proyectos" :key="proyecto.id">
                        <td>@{{ proyecto.nombre }}</td>
                        <td>@{{ formatFecha(proyecto.created_at) }}</td>
                        <td>
                            <div class="btn-group" style="border: 2px solid #121935; border-radius: 10px !important">
                                <button class="btn btn-sm btn-link actions"
                                    @click="abrirModalEditar('proyecto', 'Nuevo nombre del proyecto', proyecto.id)" data-toggle="tooltip" data-placement="bottom" title="Editar">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-link actions"
                                    @click="guardarEliminado(proyecto.id, 'proyecto')" data-toggle="tooltip" data-placement="bottom" title="Eliminar">
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

    <!-- Modal de agregar  -->
    <div class="modal fade" id="modalNuevo" tabindex="-1" aria-labelledby="modalNuevoLabel" aria-hidden="true">
        <div class="modal-dialog" style="min-width: 25%;">
            <div class="modal-content">
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
                        <div class="col-lg-12 form-group">
                            <label class="bold" for="">@{{nuevo.text}}: <span class="text-danger">*</span></label>
                            <input v-model="nuevo.nombre" type="text" class="form-control" :placeholder="nuevo.text + '...'">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-right">
                            <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                            <button class="btn btn-secondary" v-if="!loading_button" type="button" @click="guardarNuevo('carpeta')"><i class="fa fa-save"></i> Guardar</button>
                            <button class="btn btn-secondary" type="button" disabled v-if="loading_button"><i class="fa fa-spinner spin"></i> Guardando...</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de editar -->
    <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
        <div class="modal-dialog" style="min-width: 25%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="modalEditarLabel">
                        <span>EDITAR @{{objEditar.tipo.toUpperCase()}}</span>
                    </h3>
                    <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 form-group">
                            <label class="bold" for="">@{{objEditar.text}}: <span class="text-danger">*</span></label>
                            <input v-model="objEditar.nombre" type="text" class="form-control" :placeholder="objEditar.text + '...'">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-right">
                            <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                            <button class="btn btn-secondary" v-if="!loading_button" type="button" @click="guardarEditado('carpeta')"><i class="fa fa-save"></i> Guardar</button>
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
    const appCarpetas = new Vue({
        el: '#vue-app',
        data: {
            anios: [],
            clientes: [],
            proyectos: [],
            cargandoMenu: true,
            menuStep: 1,
            loading_button: false,
            selectedAnio: null,
            selectedCliente: null,
            selectedProyecto: null,
            nuevo: {
                tipo: '',
                text: '',
                nombre: ''
            },
            objEditar: {
                tipo: '',
                text: '',
                nombre: '',
                id: '',
            },
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
            async fetchClientes(anioId) {
                this.cargandoMenu = true;
                this.selectedAnio = anioId;
                this.ruta.anio = this.anios.find(obj => obj.id == anioId)?.nombre;
                try {
                    const response = await axios.get(`/api/anios/${anioId}/clientes`);
                    this.clientes = response.data.clientes.filter(c =>
                        !['ORDENES EXTERNAS', 'REFACCIONES'].includes(c.nombre)
                    );
                    this.menuStep = 2;
                } catch (error) {
                    console.error('Error fetching clientes:', error);
                } finally {
                    this.cargandoMenu = false;
                }
            },
            async fetchProyectos(clienteId) {
                this.cargandoMenu = true;
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
            }, //no se si sea asi
            async editar(obj) {
                await axios.put(`/api/anios/${obj}/edit`);
            },
            async eliminar(obj) {
                await axios.delete(`/api/anios/${obj}`);
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
                                console.log(t.anios.nombre);
                                await t.fetchClientes(responseAnios.data.id);
                                Vue.nextTick(() => {
                                    t.selectedAnio = responseAnios.data.id;
                                    t.menuStep = 2;
                                    t.ruta.anio = t.anios.find(obj => obj.id == t.selectedAnio)?.nombre;
                                    $('#modalNuevo').modal('toggle');
                                });
                            }
                            break;

                        case 'carpeta':
                            if (t.nuevo.nombre === 'REFACCIONES' || t.nuevo.nombre === 'ORDENES EXTERNAS') {
                                swal('Error!', 'No se puede agregar esta carpeta/cliente', 'error');
                                break;
                            }
                            const responseCarpeta = await axios.post(`/api/clientes/${t.selectedAnio}`, t.nuevo);
                            if (responseCarpeta.data.success) {
                                await t.fetchClientes(t.selectedAnio);
                                t.selectedCliente = responseCarpeta.data.id;
                                t.ruta.cliente = t.clientes.find(obj => obj.id == t.selectedCliente)?.nombre;
                                t.menuStep = 3;
                                await t.fetchProyectos(responseCarpeta.data.id);
                                $('#modalNuevo').modal('toggle');
                            }
                            break;

                        case 'proyecto':
                            const responseProyecto = await axios.post(`/api/proyectos/${t.selectedCliente}`, t.nuevo);
                            if (responseProyecto.data.success) {
                                await t.fetchProyectos(t.selectedCliente);
                                t.selectedProyecto = responseProyecto.data.id;
                                t.ruta.proyecto = t.proyectos.find(obj => obj.id == t.selectedProyecto)?.nombre;
                                $('#modalNuevo').modal('toggle');
                            }
                            break;
                    }
                } catch (error) {
                    console.error(error);
                    swal('Error!', 'Ocurrió un problema al agregar. Intenta nuevamente.', 'error');
                } finally {
                    t.loading_button = false;
                }
            },
            async guardarEditado() {
                let t = this;
                try {
                    t.loading_button = true;

                    switch (t.objEditar.tipo) {
                        case 'año':
                            const responseAnios = await axios.put(`/api/anios/` + t.objEditar.id, t.objEditar);
                            if (responseAnios.data.success) {
                                console.log("se editó correctamente :D");
                                $('#modalEditar').modal('toggle');
                            }
                            await t.fetchAnios();
                            break;

                        case 'carpeta':
                            const responseClientes = await axios.put(`/api/clientes/` + t.objEditar.id, t.objEditar);
                            if (responseClientes.data.success) {
                                console.log("se editó correctamente :D");
                                $('#modalEditar').modal('toggle');
                            }
                            let idAnio = t.anios.find(obj => obj.nombre == t.ruta.anio)?.id;
                            await t.fetchClientes(idAnio);
                            break;

                        case 'proyecto':
                            const responseProyectos = await axios.put(`/api/proyectos/` + t.objEditar.id, t.objEditar);
                            if (responseProyectos.data.success) {
                                console.log("se editó correctamente :D");
                                $('#modalEditar').modal('toggle');
                            }
                            let idClliente = t.clientes.find(obj => obj.nombre == t.ruta.cliente)?.id;
                            await t.fetchProyectos(idClliente);
                            break;
                    }
                } catch {
                    console.error(error);
                    swal('Error!', 'Ocurrió un problema al editar. Intenta nuevamente.', 'error');
                } finally {
                    t.loading_button = false;
                }

            },
            async guardarEliminado(id, tipo) {
                let t = this;
                swal({
                        title: `¿Eliminar ${tipo}?`,
                        text: "Una vez eliminado, no podra recuperarlo.",
                        icon: "warning",
                        buttons: ['Cancelar', 'Eliminar'],
                        dangerMode: true,
                    })
                    .then(async (willDelete) => {
                        if (willDelete) {
                            try {
                                switch (tipo) {
                                    case 'año':
                                        const responseAnios = await axios.delete('/api/anios/' + id);
                                        if (responseAnios.data.success) {
                                            console.log("Se eliminó correctamente :D");
                                            swal('Correcto!', 'Año eliminado exitosamente', 'success');
                                        }
                                        await t.fetchAnios();
                                        break;

                                    case 'carpeta':
                                        const responseClientes = await axios.delete(`/api/clientes/` + id);
                                        if (responseClientes.data.success) {
                                            console.log("Se eliminó correctamente :D");
                                            swal('Correcto!', 'Carpeta eliminada exitosamente', 'success');
                                        }
                                        let idAnio = t.anios.find(obj => obj.nombre == t.ruta.anio)?.id;
                                        await t.fetchClientes(idAnio);
                                        break;

                                    case 'proyecto':
                                        const responseProyectos = await axios.delete(`/api/proyectos/` + id);
                                        if (responseProyectos.data.success) {
                                            console.log("Se eliminó correctamente :D");
                                            swal('Correcto!', 'Proyecto eliminado exitosamente', 'success');
                                        }
                                        let idClliente = t.clientes.find(obj => obj.nombre == t.ruta.cliente)?.id;
                                        await t.fetchProyectos(idClliente);
                                        break;
                                }
                            } catch (error) {
                                console.error(error);
                                swal('Error!', 'Ocurrió un problema al eliminar. Intenta nuevamente.', 'error');
                            }
                        }
                    });
            },

            abrirModalNuevo(tipo, text) {
                this.nuevo = {
                    tipo: tipo,
                    text: text,
                    nombre: '',
                }
                $('#modalNuevo').modal();
            },
            abrirModalEditar(tipo, text, id) {
                this.objEditar = {
                    tipo: tipo,
                    text: text,
                    nombre: '',
                    id: id,
                }
                $('#modalEditar').modal();
            },
        },
    });
</script>
@endpush