@extends('layouts.app', [
'class' => '',
'elementActive' => 'dashboard'
])

<style>

</style>

@section('content')
<div id="vue-app" v-cloak>
    <div class="container-fluid mt-3">
        <div class="col-lg-12">
            <h2 class="bold my-0 py-1 mb-3 text-decoration-underline" style="letter-spacing: 2px">CENTRO DE HERRAMENTALES - @{{herramental.nombre}}</h2>
        </div>
        <div class="col-lg-12">
            <!-- Nav -->
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="listado-tab" data-toggle="tab" data-target="#listado" type="button" role="tab" aria-controls="listado" aria-selected="true">LISTADO DE COMPONENTES</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="documentacion-p-tab" data-toggle="tab" data-target="#documentacion-p" type="button" role="tab" aria-controls="documentacion-p" aria-selected="false">DOCUMENTACIÓN DE PRODUCCIÓN</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button @click='fetchDocumentos(selectedHerramental)' class="nav-link" id="documentacion-t-tab" data-toggle="tab" data-target="#documentacion-t" type="button" role="tab" aria-controls="documentacion-t" aria-selected="false">DOCUMENTACIÓN TÉCNICA</button>
                </li>
                <li class="nav-item ml-auto">
                    <button @click="goToVisorHerramental(herramental.rutaHerramental)"
                        class="btn btn-md btn-default"><i class="fa fa-eye"></i>
                        Ir a Visor de HR
                    </button>
                </li>
            </ul>

            <div class="tab-content pt-4" id="myTabContent">
                <!-- Listado de componentes -->
                <div class="tab-pane fade show active" id="listado" role="tabpanel" aria-labelledby="home-tab">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div v-if="!cargando">
                                <a class="nav-link" style="color:#939393 !important; letter-spacing: 2px !important">COMPONENTES</a>
                                <a class="d-flex align-items-center nav-link cursor-pointer" v-for="c in componentes" @click="">
                                    <i class="nc-icon" style="top: -3px !important">
                                        <img height="17px" src="{{ asset('paper/img/icons/componentes.png') }}">
                                    </i> &nbsp;
                                    <span class="underline-hover">
                                        @{{ c.nombre }}
                                    </span>
                                </a>
                            </div>
                        </div>


                        <div class="col-md-9">

                        </div>
                    </div>
                </div>


                <!-- Documentacion de producción -->
                <div class="tab-pane fade" id="documentacion-p" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="row mb-3">
                        <div>
                            <h1>Documentos de producción</h1>
                        </div>
                    </div>
                </div>

                <!-- Documentación técnica -->
                <div class="tab-pane fade" id="documentacion-t" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="mb-3">

                        <div class="row">
                            <div class="col-md-10">
                                <h2 class="mb-0">Documentos técnicos</h2>
                            </div>
                            <div class="col-md-2 text-right">
                                <button class="btn btn-success" @click="abrirModalNuevo()">
                                    <i class="fa fa-plus-circle"></i> Nuevo Archivo
                                </button>
                            </div>
                        </div>

                        <div class="col-md-12 table-responsive card shadow" v-cloak>
                            <table class="table align-items-center table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 30%" scope="col">Nombre</th>
                                        <th style="width: 30%" scope="col">Descripción</th>
                                        <th style="width: 30%" scope="col">Fecha creación</th>
                                        <th style="width: 10%" scope="col" class="no-sort">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <tr v-for="documento in documentos" :key="documento.id">
                                        <td class="bold">@{{ documento.archivo }}</td>
                                        <td>@{{ documento.descripcion }}</td>
                                        <td>@{{ formatFecha(documento.created_at) }}</td>
                                        <td>
                                            <div class="btn-group" style="border: 2px solid #121935; border-radius: 10px !important">
                                                <button class="btn btn-sm btn-link actions" style=""
                                                    @click="">
                                                    <i class="fa fa-folder-open"></i>
                                                </button>
                                                <button class="btn btn-sm btn-link actions"
                                                    @click="" data-toggle="tooltip" data-placement="bottom" title="Editar">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-link actions"
                                                    @click="" data-toggle="tooltip" data-placement="bottom" title="Eliminar">
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
                <!----------------->

            </div>
        </div>
    </div>

    <!-- Modal agregar -->
    <div class="modal fade" id="modalNuevo" tabindex="-1" aria-labelledby="modalNuevoLabel" aria-hidden="true">
        <div class="modal-dialog" style="min-width: 25%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="modalNuevoLabel">
                        <span>AGREGAR NUEVO ARCHIVO</span>
                    </h3>
                    <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 form-group">
                            <label class="bold" for="archivo">Archivo: <span class="text-danger">*</span></label>                            
                            <input
                                type="file"
                                id="archivo"
                                class="d-none"
                                @change="nuevoArchivo.archivo = $event.target.files[0]" />
                            
                            <label
                                for="archivo"
                                class="btn col-12 text-center"
                                style="cursor: pointer;">
                                <i class="fa fa-upload"></i> Cargar
                            </label>                            
                            <small v-if="nuevoArchivo.archivo">@{{ nuevoArchivo.archivo.name }}</small>

                        </div>
                        <div class="col-lg-12 form-group">
                            <label class="bold mt-3" for="">Descripción:</label>
                            <textarea class="form-control" v-model="nuevoArchivo.descripcion" rows="3" placeholder="Descripción del archivo"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-right">
                            <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                            <button class="btn btn-secondary" v-if="!loading_button" type="button" @click="guardarArchivo()"><i class="fa fa-save"></i> Guardar</button>
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
            loading_button: false,
            cargando: false,

            nuevoArchivo: {
                archivo: '',
                descripcion: '',
            },
            selectedHerramental: null,
            herramental: {},
            componentes: [],
            documentos: [],
        },
        methods: {
            async fetchHerramental() {
                this.cargando = true
                try {
                    const response = await axios.get(`/api/herramental/${this.selectedHerramental}`);
                    this.herramental = response.data.herramental;
                } catch (error) {
                    console.error('Error fetching herramentales:', error);
                } finally {
                    this.cargando = false;
                }
            },
            async fetchComponentes(herramentalId) {
                this.cargando = true
                this.selectedHerramental = herramentalId;
                try {
                    const response = await axios.get(`/api/avance-hr/${herramentalId}`);
                    this.componentes = response.data.componentes
                } catch (error) {
                    console.error('Error fetching componentes:', error);
                } finally {
                    this.cargando = false;
                }
            },
            /* Parte de documentos */
            async fetchDocumentos(herramentalId) {
                this.cargando = true
                try {
                    const response = await axios.get(`/api/documentacion-tecnica/${herramentalId}`);
                    this.documentos = response.data.documentacion;
                    console.log(this.documentos);
                } catch (error) {
                    console.error('Error fetching documentos:', error);
                } finally {
                    this.cargando = false;
                }
            },
            async guardarArchivo() {
                let t = this;
                t.cargando = true;

                let formData = new FormData();
                formData.append('archivo', t.nuevoArchivo.archivo);
                formData.append('descripcion', t.nuevoArchivo.descripcion || '');
                try {
                    console.log('NUEVO:', t.nuevoArchivo);/* pendiente para mañana :D */
                    const response = await axios.post(`/api/documentacion-tecnica/`, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });
                    if (response.data.success) {
                        t.nuevoArchivo = {
                            archivo: '',
                            descripcion: '',
                        };
                        t.fetchDocumentos(t.selectedHerramental);
                    } else {

                    }
                } catch (error) {
                    console.error('Error saving document:', error);
                } finally {
                    t.cargando = false;
                }
            },
            async navigateFromUrlParams() {
                const queryParams = new URLSearchParams(window.location.search);
                const herramentalId = queryParams.get('h');
                const componenteId = queryParams.get('co');

                try {
                    if (herramentalId) {
                        this.selectedHerramental = {
                            id: herramentalId
                        };
                        await this.fetchComponentes(herramentalId);

                        if (componenteId) {
                            let task = this.tasks.find(t =>
                                t.time && Array.isArray(t.time) && t.time.some(time => time.info?.id == componenteId)
                            );
                            if (task) {
                                this.componenteIdSeleccionado = componenteId;
                                //this.verInformacion(task);
                            }
                        }
                    }

                } catch (error) {
                    console.error("Error navigating from URL parameters:", error);
                }
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
            goToVisorHerramental(ruta) {
                window.location.href = '/visor-avance-hr/' + ruta;
            },
            abrirModalNuevo() {
                this.nuevoArchivo = {
                    archivo: '',
                    descripcion: '',
                }
                $('#modalNuevo').modal();
            },
        },
        mounted() {
            let t = this;
            t.navigateFromUrlParams();
            t.fetchHerramental();
        }
    });
</script>
@endpush