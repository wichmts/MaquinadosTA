@extends('layouts.app', [
'class' => '',
'elementActive' => 'dashboard'
])

<style>
    .btn-group i {
        letter-spacing: 0px !important;
    }

    .btn-group .actions {
        padding-left: 10px !important;
        padding-right: 10px !important;
    }

    .loader {
        border: 16px solid hsla(0, 0%, 87%, .3);
        /* Light grey */
        border-top: 16px solid #121935;
        border-radius: 50%;
        width: 100px;
        height: 100px;
        animation: spin 2s linear infinite;
        margin: auto;
    }

    .fade-enter-active,
    .fade-leave-active {
        transition: opacity .2s
    }

    .fade-enter,
    .fade-leave-to {
        opacity: 0
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    [v-cloak] {
        display: none !important;
    }

    .no-border {
        border: none !important;
    }

    .vs__dropdown-toggle {
        height: calc(2.25rem + 2px);
    }

    .incard {
        box-shadow: none !important;
    }

    .form-group {}

    input[type=checkbox],
    input[type=radio] {
        width: 17px !important;
        height: 17px !important;
    }

    input[type="file"] {
        width: 150px;
        /* Ajusta el valor según lo que necesites */
        max-width: 100%;
        /* Para asegurarte de que no se salga del contenedor */
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

    .content-2 :is(label, input, textarea, button, select) {
        font-size: 0.875rem !important;
    }

    .table .form-check label .form-check-sign::before,
    .table .form-check label .form-check-sign::after {
        top: -10px !important
    }
</style>

@section('content')
<div class="content content-2" id="vue-app">
    <div class="row mt-3 px-5" v-cloak>
        <!-- Parte izq -->
        <div class="col-lg-6" style="border-right: 1px solid #ededed">
            <h2 class="bold my-0 py-1 text-decoration-underline" style="letter-spacing: 2px">
                NUEVA ORDEN DE AFILADO
            </h2>

            <div class="row">
                <div class="col-lg-6 form-group">
                    <label class="bold">Fecha de solicitud</label>
                    <input :disabled="modo_edicion" type="date" class="form-control" v-model="nuevo.fecha_solicitud" readonly>
                </div>
                <div class="col-lg-6 form-group">
                    <label class="bold">Fecha entrega solicitada <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" v-model="nuevo.fecha_entrega_solicitada" :min="new Date().toISOString().split('T')[0]">
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 form-group">
                    <label class="bold">Área solicitante <span class="text-danger">*</span></label>
                    <select :disabled="modo_edicion" class="form-control" v-model="nuevo.area_solicitud">
                        <option v-for="obj in areas_solicitantes" :value="obj">@{{obj}}</option>
                    </select>
                </div>
                <div class="col-lg-6 form-group">
                    <label class="bold">Nombre del solicitante <span class="text-danger">*</span></label>
                    <v-select
                        :options="this.usuarios"
                        label="nombre_completo"
                        v-model="nuevo.solicitante_id"
                        disabled
                        :reduce="usuario => usuario.id">
                    </v-select>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4 form-group">
                    <label class="bold">Número de HR <span class="text-danger">*</span></label>
                    <input :disabled="modo_edicion" type="text" class="form-control" v-model="nuevo.numero_hr">
                </div>
                <div class="col-lg-4 form-group">
                    <label class="bold">Nombre de componente <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend" style="background-color: #efefef">
                            <span class="bold input-group-text py-0">- &nbsp;&nbsp;</span>
                        </div>
                        <input :disabled="modo_edicion" type="text" class="form-control" v-model="nuevo.nombre_componente">
                    </div>
                </div>
                <div class="col-lg-4 form-group">
                    <label class="bold">Cantidad <span class="text-danger">*</span></label>
                    <input :disabled="modo_edicion" type="number" step="1" class="form-control" v-model="nuevo.cantidad">
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4 form-group">
                    <label class="bold">Archivo 2D</label>
                    <input
                        class="input-file"
                        id="2d"
                        type="file"
                        @change="handleFileChange($event)"
                        style="display: none;" />
                    <label tabindex="0" for="2d" class="input-file-trigger col-12 text-center">
                        <i class="fa fa-upload"></i> @{{modo_edicion?'EDITAR':'CARGAR'}} 2D
                    </label>
                    <small>Archivo: <strong>@{{nuevo.archivo_2d ?? 'Sin cargar'}}</strong></small>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 form-group">
                    <label class="bold">Comentarios / Instrucciones <span class="text-danger">*</span></label>
                    <textarea v-model="nuevo.comentarios" class="form-control px-2 py-2 w-100 text-left" placeholder="Comentarios e instrucciones..." style="min-height: 150px !important"></textarea>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 text-center form-group">
                    <button class="btn btn-default" v-if="modo_edicion"><i class="fa fa-times-circle"></i> CANCELAR MODIFICACIÓN</button>

                    <button v-if="!cargando && modo_edicion" class="btn btn-success"><i class="fa fa-paper-plane"></i> ENVIAR MODIFICACIÓN</button>
                    <button v-if="cargando && modo_edicion" disabled class="btn btn-success"><i class="fa fa-spinner"></i> ENVIANDO, ESPERE...</button>

                    <button v-if="!cargando && !modo_edicion" @click="enviar()" class="btn btn-success"><i class="fa fa-paper-plane"></i> ENVIAR SOLICITUD</button>
                    <button v-if="cargando && !modo_edicion" disabled class="btn btn-success"><i class="fa fa-spinner"></i> ENVIANDO, ESPERE...</button>
                </div>
            </div>
        </div>

        <!-- La parte de derecha -->
        <div class="col-lg-6">
            <div class="row">
                <div class="col-xl-12 mb-4">
                    <h2 class="bold my-0 py-1 text-decoration-underline" style="letter-spacing: 2px">MIS ORDENES DE AFILADO</h2>
                </div>
                <div class="col-xl-12 table-responsive">
                    <table class="table">
                        <thead class="thead-light">
                            <tr>
                                <th style="text-transform: none !important">Componente</th>
                                <th style="text-transform: none !important">Fecha solicitud</th>
                                <th style="text-transform: none !important">Fecha deseada entrega</th>
                                <th style="text-transform: none !important">Fecha entrega</th>
                                <th style="text-transform: none !important">Estatus</th>
                                <th style="text-transform: none !important"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="solicitud in solicitudes">
                                <td class="py-1 bold">@{{solicitud.componente.nombre}}</td>
                                <td class="py-1">@{{solicitud.fecha_solicitud_show}}</td>
                                <td class="py-1">@{{solicitud.fecha_deseada_show}}</td>
                                <td class="py-1">@{{solicitud.fecha_real_show}}</td>
                                <td class="py-1">
                                    </span> <span style="font-size: 12px !important" :class="getClaseColor(solicitud.componente, solicitud.fecha_real_entrega)" class="badge  badge-pill px-2 py-1 my-2">@{{determinarEstatus(solicitud.componente, solicitud.fecha_real_entrega)}}</span>
                                </td>
                                <td>
                                    <a :href="'/visor-avance-hr' + solicitud.componente.rutaComponente" class="text-dark"><i class="cursor-pointer fa fa-info-circle"></i></a>
                                    <span v-if="determinarEstatus(solicitud.componente, solicitud.fecha_real_entrega) != 'Finalizado'" class="text-muted"> | </span>
                                    <i v-if="determinarEstatus(solicitud.componente, solicitud.fecha_real_entrega) != 'Finalizado'" @click="editarSolicitud(solicitud)" class="cursor-pointer fa fa-edit"></i>
                                </td>
                            </tr>
                            <tr v-if="solicitudes.length == 0">
                                <td colspan="6" class="text-center">No tiene solicitudes registradas todavia</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endsection

    @push('scripts')
    <script type="text/javascript">
        Vue.component('v-select', VueSelect.VueSelect);

        var app = new Vue({
            el: '#vue-app',
            data: {
                loading_button: false,
                modo_edicion: false,
                cargando: false,
                materiales: [],
                usuarios: [],
                nuevo: {
                    fecha_solicitud: new Date().toISOString().slice(0, 10),
                    fecha_entrega_solicitada: new Date().toISOString().slice(0, 10),
                    nombre_solicitante: '',
                    area_solicitud: 'Herramentales',
                    numero_hr: '',
                    cantidad: 1,
                    archivo_2d: null,
                    comentarios: '',
                    solcitudante_id: null,

                    solicitante_id: JSON.parse('{!! json_encode(auth()->user()->id) !!}')

                },
                roles_usuario: JSON.parse('{!! json_encode(auth()->user()->roles->pluck("name")) !!}'),
                solicitudes: [],
                archivo_2d: null,
                areas_solicitantes: [],

            },
            methods: {
                obtenerAreasSolicitud() {
                    let t = this;
                    let disponibles = [
                        'DISEÑO',
                        'DIRECCION',
                        'HERRAMENTALES',
                        'INFRAESTRUCTURA',
                        'MANTENIMIENTO',
                        'METROLOGIA',
                        'PRODUCCION',
                        'PROYECTOS'
                    ];
                    t.areas_solicitantes = disponibles.filter(area => this.roles_usuario.includes(area));
                    Vue.nextTick(() => {
                        if (t.areas_solicitantes.length > 0) {
                            t.nuevo.area_solicitud = t.areas_solicitantes[0];
                        }
                    });
                },
                async validarFormulario(nuevo) {
                    let t = this;
                    if (t.nuevo.fecha_entrega_solicitada == "" || t.nuevo.area_solicitud == "" || t.nuevo.solicitante_id == "" || t.nuevo.numero_hr == "" || t.nuevo.nombre_componente == "" || t.nuevo.cantidad == "" || t.nuevo.comentarios == "") {
                        swal('Error', 'Debes llenar todos los campos marcados con un asterisco.', 'error');
                        return false;
                    }
                    if ((!t.archivo_2d && !t.archivo_3d && !t.dibujo) && (!t.nuevo.archivo_2d && !t.nuevo.archivo_3d && !t.nuevo.dibujo)) {
                        swal('Error', 'Debes cargar al menos uno de los archivos: 2D, 3D o el dibujo a mano.', 'error');
                        return false;
                    }
                    return true;
                },
                async fetchUsuarios() {
                    this.cargando = true
                    try {
                        const response = await axios.get(`/api/usuario?tipo_usuario=-1`);
                        this.usuarios = response.data.usuarios;
                    } catch (error) {
                        console.error('Error fetching usuarios:', error);
                    } finally {
                        this.cargando = false;
                    }
                },
                async handleFileChange(event) {
                    let t = this;
                    const file = event.target.files[0];
                    if (file) {
                        t.archivo_2d = file;
                        t.nuevo.archivo_2d = file.name;
                    }
                },
                async enviar() {
                    let t = this;
                    let valido = await t.validarFormulario(t.nuevo);
                    if (!valido) return;
                    t.cargando = true;
                    const formData = new FormData();
                    formData.append('data', JSON.stringify(t.nuevo));
                    console.log(t.nuevo);
                    formData.append('archivo_2d', t.archivo_2d);

                    try {
                        const response = await axios.post('/api/generar-orden-afilado', formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        });
                        console.log(response);
                        if (response.data.success)  {
                            swal('Éxito', 'La orden de afilado ha sido creada exitosamente.', 'success');
                            t.nuevo = {
                                fecha_solicitud: new Date().toISOString().slice(0, 10),
                                fecha_entrega_solicitada: new Date().toISOString().slice(0, 10),
                                nombre_solicitante: '',
                                area_solicitud: 'Herramentales',
                                numero_hr: '',
                                cantidad: 1,
                                archivo_2d: null,
                                comentarios: '',
                                solicitante_id: JSON.parse('{!! json_encode(auth()->user()->id) !!}')
                            };
                            t.archivo_2d = null;
                            t.obtenerAreasSolicitud();
                            t.fetchUsuarios();
                        } else {
                            swal('Error', response.data.message, 'error');
                            t.cargando = false;
                        }
                    } catch (error) {
                        const mensaje = error.response?.data?.error || 'Error al guardar el componente.';
                        swal('Error', mensaje, 'error');
                        t.cargando = false;
                    } finally {
                        t.cargando = false;
                    }
                },

            },
            mounted() {
                let t = this;
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

                t.obtenerAreasSolicitud();
                t.fetchUsuarios();
            }
        });
    </script>
    @endpush