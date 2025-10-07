@extends('layouts.app', [
'class' => '',
'elementActive' => 'dashboard'
])

@section('styles')
<link rel="stylesheet" href="{{ asset('paper/css/paper-dashboard-responsivo.css') }}?v={{ time() }}">


@endsection

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
                        <div class="col-md-2 border-right">
                            <div v-if="!cargando">
                                <a class="nav-link" style="color:#939393 !important; letter-spacing: 2px !important">COMPONENTES</a>
                                <a class="d-flex align-items-center nav-link cursor-pointer" v-for="c in componentes" :key="c.id" @click="seleccionarComponente(c)">
                                    <i class="nc-icon" style="top: -3px !important">
                                        <img height="17px" src="{{ asset('paper/img/icons/componentes.png') }}">
                                    </i> &nbsp;
                                    <span class="underline-hover">
                                        @{{ c.nombre }}
                                    </span>
                                </a>
                            </div>
                        </div>


                        <div class="col-md-10">
                            <div v-if="cargando" class="text-center">
                                <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
                                <span class="sr-only">Cargando...</span>
                            </div>

                            <div v-else>
                                <div v-if="componenteSeleccionado.id">
                                    <h3 class="bold text-center mb-4">@{{ componenteSeleccionado.nombre }}</h3>
                                    <div class="table-responsive shadow">
                                        <table class="table align-items-center table-bordered">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="width: 20%">Tipo</th>
                                                    <th style="width: 60%" scope="col">Nombre del Archivo</th>
                                                    <th style="width: 20%" scope="col">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Plano</td>
                                                    <td>@{{ componenteSeleccionado.archivo_2d_show ? componenteSeleccionado.archivo_2d_show : 'Sin archivo'}}</td>
                                                    <td>
                                                        <div class="btn-group" style="border: 2px solid #121935; border-radius: 10px !important">
                                                            <a class="btn btn-sm btn-link actions text-dark"
                                                                :href="'/storage/' + componenteSeleccionado.archivo_2d_public"
                                                                :disabled="!componenteSeleccionado.archivo_2d_public"
                                                                target="_blank"><i class="fa fa-external-link"></i>
                                                            </a>
                                                        </div>
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <td>Vista 3D</td>
                                                    <td>@{{componenteSeleccionado.archivo_3d_show ? componenteSeleccionado.archivo_3d_show : 'Sin archivo'}}</td>
                                                    <td>
                                                        <div class="btn-group" style="border: 2px solid #121935; border-radius: 10px !important">
                                                            <a class="btn btn-sm btn-link actions text-dark"
                                                                :href="'/storage/' + componenteSeleccionado.archivo_3d_public"
                                                                :disabled="!componenteSeleccionado.archivo_3d_public"
                                                                target="_blank">
                                                                <i class="fa fa-external-link"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Explosionado</td>
                                                    <td>@{{ componenteSeleccionado.archivo_explosionado_show ? componenteSeleccionado.archivo_explosionado_show : 'Sin archivo' }}</td>
                                                    <td>
                                                        <div class="btn-group" style="border: 2px solid #121935; border-radius: 10px !important">
                                                            <a class="btn btn-sm btn-link actions text-dark"
                                                                :href="'/storage/' + componenteSeleccionado.archivo_explosionado_public"
                                                                :disabled="!componenteSeleccionado.archivo_explosionado_show"
                                                                target="_blank">
                                                                <i class="fa fa-external-link"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Fotografía de ensamble</td>
                                                    <td>@{{ componenteSeleccionado.foto_matricero ? componenteSeleccionado.foto_matricero : 'Sin archivo' }}</td>
                                                    <td>
                                                        <div class="btn-group" style="border: 2px solid #121935; border-radius: 10px !important">
                                                            <a class="btn btn-sm btn-link actions text-dark"
                                                                :href="'/storage/fotos_matricero/' + componenteSeleccionado.fotoMatricero"
                                                                :disabled="!componenteSeleccionado.foto_matricero"
                                                                target="_blank">

                                                                <i class="fa fa-external-link"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Parte de lo de fabricaciones  -->
                                    <div v-if="fabricacionesComponente.length != 0" class="table-responsive shadow mt-3">
                                        <table class="table align-items-center table-bordered">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="width: 10%">Fabricacion</th>
                                                    <th style="width: 40%" scope="col">Nombre de Archivos</th>
                                                    <th style="width: 40%" scope="col">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="f in fabricacionesComponente" :key="f.id">
                                                    <td>@{{ f.maquina_nombre }}</td>
                                                    <td>
                                                        <div class="pb-4">
                                                            <strong>Archivo:</strong>
                                                            <span>@{{ f.archivo_show ? f.archivo_show : 'Sin archivo' }}</span>
                                                        </div>
                                                        <div class="border-top pt-4">
                                                            <strong>Foto:</strong>
                                                            <span>@{{ f.foto ? f.foto : 'Sin foto' }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="pb-3 pt-2">
                                                            <div class="btn-group" style="border: 2px solid #121935; border-radius: 10px !important">
                                                                <a class="btn btn-sm btn-link actions text-dark"
                                                                    :href="'/api/download/programas/' + f.archivo"
                                                                    :disabled="!f.archivo"
                                                                    target="_blank">
                                                                    <i class="fa fa-download"></i> Descargar
                                                                </a>
                                                            </div>
                                                        </div>

                                                        <div class="pt-3 border-top">
                                                            <a target="_blank" v-if="f.foto" :href="'/storage/fabricaciones/' + f.foto">
                                                                <img :src="'/storage/fabricaciones/' + f.foto" style="border-radius: 10px; width: 10px; height: 20%; object-fit: cover" alt="">
                                                            </a>
                                                            <img v-else src="{{ asset('paper/img/no-image.png') }}" style="border-radius: 10px; width: 10%; height: 20%; object-fit:cover" alt="">
                                                        </div>


                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div v-else class="text-center">
                                        <h4 class="bold text-center mb-4">No hay fabricaciones para este componente</h4>
                                    </div>
                                </div>
                                <div v-else class="text-center">
                                    <h3 class="bold text-center mb-4">Seleccione un componente para ver sus archivos</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documentacion de producción -->
                <div class="tab-pane fade" id="documentacion-p" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="mb-3">
                        <div class="col-md-10 mb-3">
                            <h2 class="mb-0">Documentos de Producción</h2>
                        </div>

                        <div class="col-md-12 table-responsive card shadow" v-cloak>
                            <table class="table align-items-center table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 30%" scope="col">Tipo</th>
                                        <th style="width: 30%" scope="col">Nombre</th>
                                        <th style="width: 10%" scope="col" class="no-sort">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>F71-03 ANEXO 1</td>
                                        <td>@{{ herramental.archivo_show ? herramental.archivo_show : 'Sin archivo'}}</td>
                                        <td>
                                            <div class="btn-group" style="border: 2px solid #121935; border-radius: 10px !important">
                                                <a class="btn btn-sm btn-link actions text-dark"
                                                    :href="'/storage/' + herramental.archivo_public"
                                                    :disabled="!herramental.archivo"
                                                    target="_blank"><i class="fa fa-external-link"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>F71-03 ANEXO 1.1</td>
                                        <td>@{{ herramental.archivo2_show ? herramental.archivo2_show : 'Sin archivo'}}</td>
                                        <td>
                                            <div class="btn-group" style="border: 2px solid #121935; border-radius: 10px !important">
                                                <a class="btn btn-sm btn-link actions text-dark"
                                                    :href="'/storage/' + herramental.archivo2_public"
                                                    :disabled="!herramental.archivo2"
                                                    target="_blank"><i class="fa fa-external-link"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>ARCHIVO EXPLOSIONADO</td>
                                        <td>@{{ herramental.archivo_explosionado_show ? herramental.archivo_explosionado_show : 'Sin archivo'}}</td>
                                        <td>
                                            <div class="btn-group" style="border: 2px solid #121935; border-radius: 10px !important">
                                                <a class="btn btn-sm btn-link actions text-dark"
                                                    :href="'/storage/' + herramental.archivo_explosionado_public"
                                                    :disabled="!herramental.archivo_explosionado"
                                                    target="_blank"><i class="fa fa-external-link"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Prebas Diseño -->
                            <div v-if='pruebasDiseño.length != 0' class="table-responsive shadow mt-3">
                                <h4>Pruebas de diseño</h4>
                                <table class="table align-items-center table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 10%">Prueba de Diseño</th>
                                            <th style="width: 40%" scope="col">Tipo</th>
                                            <th style="width: 40%" scope="col">Nombre de Archivos</th>
                                            <th style="width: 40%" scope="col">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="p in pruebasDiseño" :key="f.id">
                                            <td>@{{ p.nombre }}</td>
                                            <td>ARCHIVO DIMENSIONAL</td>
                                            <td>
                                                @{{ p.archivo_dimensional ? p.archivo_dimensional : 'Sin archivo' }}
                                            </td>
                                            <td>
                                                <div class="btn-group" style="border: 2px solid #121935; border-radius: 10px !important">
                                                    <a class="btn btn-sm btn-link actions text-dark"
                                                        :href="'/api/download/pruebas-diseno/' + p.archivo_dimensional"
                                                        :disabled="!p.archivo_dimensional"
                                                        target="_blank">
                                                        <i class="fa fa-download"></i> Descargar
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-else class="text-center">
                                <h4 class="bold text-center mb-4">No hay pruebas de diseño del herramental</h4>
                            </div>

                            <!-- Prebas Proceso -->
                            <div v-if='pruebasProceso.length != 0' class="table-responsive shadow mt-3">
                                <h4>Pruebas de proceso</h4>
                                <table class="table align-items-center table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 10%">Prueba de Proceso</th>
                                            <th style="width: 40%" scope="col">Tipo</th>
                                            <th style="width: 40%" scope="col">Nombre de Archivos</th>
                                            <th style="width: 40%" scope="col">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="p in pruebasProceso" :key="f.id">
                                            <td>@{{ p.nombre }}</td>
                                            <td>FORMATO F71-03 ANEXO 2</td>
                                            <td>
                                                @{{ p.archivo ? p.archivo : 'Sin archivo' }}
                                            </td>
                                            <td>
                                                <div class="btn-group" style="border: 2px solid #121935; border-radius: 10px !important">
                                                    <a class="btn btn-sm btn-link actions text-dark"
                                                        :href="'/api/download/pruebas-proceso/' + p.archivo"
                                                        :disabled="!p.archivo"
                                                        target="_blank">
                                                        <i class="fa fa-download"></i> Descargar
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-else class="text-center">
                                <h4 class="bold text-center mb-4">No hay pruebas de proceso del herramental</h4>
                            </div>
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
                                <button class="btn btn-success" @click="abrirModal('agregar')">
                                    <i class="fa fa-plus-circle"></i> NUEVO DOCUMENTO
                                </button>
                            </div>
                        </div>

                        <div v-if='documentos.length != 0' class="col-md-12 table-responsive card shadow" v-cloak>
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
                                        <td class="bold">@{{ documento.archivo_show }}</td>
                                        <td>@{{ documento.descripcion }}</td>
                                        <td>@{{ formatFecha(documento.created_at) }}</td>
                                        <td>
                                            <div class="btn-group" style="border: 2px solid #121935; border-radius: 10px !important">
                                                <a class="btn btn-sm btn-link actions text-dark"
                                                    :href="'/storage/' + documento.archivo_public"
                                                    target="_blank">
                                                    <i class="fa fa-external-link"></i>
                                                </a>

                                                <button class="btn btn-sm btn-link actions"
                                                    @click="abrirModal('editar', documento.id, documento.archivo_show , documento.descripcion)" data-toggle="tooltip" data-placement="bottom" title="Editar">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-link actions"
                                                    @click="eliminarArchivo(documento.id)" data-toggle="tooltip" data-placement="bottom" title="Eliminar">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-center">
                            <h4 class="bold text-center mb-4">No hay documentos técnicos del herramental</h4>
                        </div>

                    </div>
                </div>
                <!----------------->
            </div>
        </div>
    </div>

    <!-- Modal agregar/editar -->
    <div class="modal fade" id="modalArchivo" tabindex="-1" aria-labelledby="modalArchivoLabel" aria-hidden="true">
        <div class="modal-dialog" style="min-width: 25%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="modalArchivoLabel">
                        <span>@{{modalEdicion ? 'EDITAR' : 'AGREGAR'}} DOCUMENTO</span>
                    </h3>
                    <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 form-group">
                            <label class="bold mt-3" for="">Descripción:</label>
                            <textarea class="form-control" v-model="nuevoArchivo.descripcion" rows="3" placeholder="Descripción del documento"></textarea>
                        </div>
                        <div class="col-lg-12 form-group">
                            <label class="bold" for="archivo">Documento: <span class="text-danger">*</span></label>
                            <input
                                type="file"
                                id="archivo"
                                class="d-none"
                                @change="nuevoArchivo.archivo = $event.target.files[0]" />

                            <label
                                for="archivo"
                                class="input-file-trigger col-12 text-center"
                                style="cursor: pointer;">
                                <i class="fa fa-upload"></i> CARGAR DOCUMENTO
                            </label>
                            
                            <small v-if="nuevoArchivo.archivo">
                                @{{ typeof nuevoArchivo.archivo === 'string' ? nuevoArchivo.archivo : (nuevoArchivo.archivo.name || '') }}
                            </small>

                        </div>

                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-right">
                            <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                            <button class="btn btn-dark" v-if="!loading_button" type="button" @click="modalEdicion ? editarArchivo(nuevoArchivo.id) : guardarArchivo()"><i class="fa fa-save"></i> Guardar</button>
                            <button class="btn btn-dark !important" type="button" disabled v-if="loading_button"><i class="fa fa-spinner spin"></i> Guardando...</button>
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
            modalEdicion: false,

            nuevoArchivo: {
                id: null,
                archivo: '',
                descripcion: '',
                idHerramental: null,
            },
            selectedHerramental: null,
            herramental: {},
            componentes: [],
            pruebasDiseño: [],
            pruebasProceso: [],
            componenteSeleccionado: {},
            documentos: [],
            fabricacionesComponente: [],
        },
        methods: {
            async fetchHerramental() {
                this.cargando = true
                try {
                    const response = await axios.get(`/api/herramental/${this.selectedHerramental}`);
                    this.herramental = response.data.herramental;
                    console.log(this.herramental);
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
                    this.componentes = response.data.componentes;
                } catch (error) {
                    console.error('Error fetching componentes:', error);
                } finally {
                    this.cargando = false;
                }
            },
            async fetchPruebasDiseño(herramentalId) {
                let t = this;
                t.cargando = true;
                try {
                    const response = await axios.get(`/api/herramental/${herramentalId}/pruebas-diseno`);
                    t.pruebasDiseño = response.data.pruebas;
                } catch (error) {
                    console.error('Error fetching pruebas de diseño:', error);
                } finally {
                    t.cargando = false;
                }
            },
            async fetchPruebasProceso(herramentalId) {
                let t = this;
                t.cargando = true;
                try {
                    const response = await axios.get(`/api/herramental/${herramentalId}/pruebas-proceso`);
                    t.pruebasProceso = response.data.pruebas;
                } catch (error) {
                    console.error('Error fetching pruebas de proceso:', error);
                } finally {
                    t.cargando = false;
                }
            },
            /* Parte de documentos */
            async fetchDocumentos(herramentalId) {
                this.cargando = true
                try {
                    const response = await axios.get(`/api/documentacion-tecnica/${herramentalId}`);
                    this.documentos = response.data.documento;

                    Vue.nextTick(function() {
                    document.querySelector("html").classList.add('js');                    
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
                formData.append('archivo', t.nuevoArchivo.archivo); // este es el archivo en si, no el nombre 
                formData.append('descripcion', t.nuevoArchivo.descripcion || '');
                formData.append('herramental_id', t.selectedHerramental);
                try {
                    const response = await axios.post(`/api/documentacion-tecnica/${t.selectedHerramental}`, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });
                    if (response.data.success) {
                        swal('Éxito', 'El archivo se ha subido exitosamente.', 'success');
                        t.nuevoArchivo = {
                            id: null,
                            archivo: '',
                            descripcion: '',
                            idHerramental: null,
                        };
                        t.fetchDocumentos(t.selectedHerramental);
                    } else {

                    }
                } catch (error) {
                    console.error('Error al guardar documento:', error);
                    swal('Error', 'Error al guardar documento', 'error');
                } finally {
                    t.cargando = false;
                    $('#modalArchivo').modal('hide');
                }
            },
            async eliminarArchivo(documentoId) {
                let t = this;
                t.cargando = true;
                swal({
                    title: `¿Eliminar documento?`,
                    text: "Una vez eliminado, no podra recuperarlo.",
                    icon: "warning",
                    buttons: ['Cancelar', 'Eliminar'],
                }).then(async (willDelete) => {
                    if (willDelete) {
                        try {
                            const response = await axios.delete(`/api/documentacion-tecnica/${documentoId}`);
                            if (response.data.success) {
                                swal('Éxito', 'El archivo se ha eliminado exitosamente.', 'success');
                                t.fetchDocumentos(t.selectedHerramental);
                            }
                        } catch (error) {
                            console.error('Error al eliminar documento:', error);
                            swal('Error', 'Ocurrió un error al eliminar el archivo.', 'error');
                        } finally {
                            t.cargando = false;
                        }
                    } else {
                        t.cargando = false;
                    }
                });
            },
            async editarArchivo(documentoId) {

                let t = this
                t.cargando = true;

                let formData = new FormData();
                if (t.nuevoArchivo.archivo instanceof File) {
                    formData.append('archivo', t.nuevoArchivo.archivo);
                }
                formData.append('descripcion', t.nuevoArchivo.descripcion || '');


                try {
                    const response = await axios.post(`/api/documentacion-tecnica/editar/${documentoId}`, formData);
                    if (response.data.success) {
                        swal('Éxito', 'El archivo se ha editado exitosamente.', 'success');
                        t.fetchDocumentos(t.selectedHerramental);
                        t.nuevoArchivo = {
                            id: null,
                            archivo: '',
                            descripcion: '',
                            idHerramental: null,
                        };
                    }
                } catch (error) {
                    console.error('Error al editar documento:', error);
                    swal('Error', 'Ocurrió un error al eliminar el archivo', 'error');
                } finally {
                    t.cargando = false;
                    $('#modalArchivo').modal('hide');
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
                        await this.fetchPruebasDiseño(herramentalId);
                        await this.fetchPruebasProceso(herramentalId);

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
            seleccionarComponente(c) {
                this.componenteSeleccionado = c;
                this.fabricacionesComponente = c.fabricaciones;
            },
            abrirModal(modo, id, archivo, descripcion) {
                let t = this;
                if (modo == 'agregar') {
                    t.modalEdicion = false;
                    t.nuevoArchivo = {
                        id: null,
                        archivo: '',
                        descripcion: '',
                        idHerramental: null,
                    };
                } else if (modo == 'editar') {
                    t.modalEdicion = true;
                    t.nuevoArchivo = {
                        id: id,
                        archivo: archivo,
                        descripcion: descripcion,
                    };
                }
                $('#modalArchivo').modal();
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