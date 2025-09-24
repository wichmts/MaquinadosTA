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
                    <label class="bold">Fecha deseada de entrega <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" v-model="nuevo.fecha_deseada_entrega" :min="new Date().toISOString().split('T')[0]">
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
                <div class="col-lg-4 form-group">
                    <label class="bold">Caras a afilar <span class="text-danger">*</span></label>
                    <input :disabled="modo_edicion" type="string" step="1" class="form-control" v-model="nuevo.caras_a_afilar">
                </div>
                <div class="col-lg-2 form-group">
                    <label class="bold">Cuanto afilar <span class="text-danger">*</span></label>
                    <input :disabled="modo_edicion" type="number" step="1" class="form-control" v-model="nuevo.cuanto_afilar">
                </div>
                <div class="col-lg-2 form-group">
                    <label class="bold">
                        Unidad de medida <span class="text-danger">*</span>
                    </label>
                    <select
                        :disabled="modo_edicion"
                        class="form-control"
                        v-model="nuevo.unidad_medida_id">
                        <option disabled value="">Seleccione una medida</option>
                        <option v-for="medida in medidas" :key="medida.id" :value="medida.id">
                            @{{ medida.nombre }} (@{{medida.abreviatura }})
                        </option>
                    </select>
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
                    <button class="btn btn-default" @click="cancelarModificacion()" v-if="modo_edicion"><i class="fa fa-times-circle"></i> CANCELAR MODIFICACIÓN</button>

                    <button v-if="!cargando && modo_edicion" @click="enviarModificacion()" class="btn btn-success"><i class="fa fa-paper-plane"></i> ENVIAR MODIFICACIÓN</button>
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
                medidas: {},
                nuevo: {
                    fecha_solicitud: new Date().toISOString().slice(0, 10),
                    fecha_deseada_entrega: new Date().toISOString().slice(0, 10),
                    nombre_solicitante: '',
                    fecha_real_entrega: "",
                    area_solicitud: 'Herramentales',
                    numero_hr: '',
                    cantidad: 1,
                    unidad_medida_id: null,
                    archivo_2d: null,
                    comentarios: '',
                    solcitudante_id: null,
                    caras_a_afilar: '',
                    cuanto_afilar: '',
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
                async obtenerMedidas() {
                    try {
                        const response = await axios.get(`/api/unidad-medida`);
                        this.medidas = response.data.medidas;
                    } catch (error) {
                        console.error('Error fetching medidas:', error);
                    }
                },
                async validarFormulario(nuevo) {
                    let t = this;
                    if (t.nuevo.fecha_deseada_entrega == "" || t.nuevo.area_solicitud == "" || t.nuevo.solicitante_id == "" || t.nuevo.numero_hr == "" || t.nuevo.nombre_componente == "" || t.nuevo.cantidad == "" || t.nuevo.comentarios == "" || t.nuevo.caras_a_afilar == "" || t.nuevo.cuanto_afilar == "" || t.nuevo.unidad_medida_id == null) {
                        swal('Error', 'Debes llenar todos los campos marcados con un asterisco.', 'error');
                        return false;
                    }
                    return true;
                },
                getClaseColor(componente, fecha_real_entrega) {
                    const {
                        cargado,
                        enrutado,
                        cortado,
                        programado
                    } = componente;

                    let resultado = "badge-dark";
                    if (cargado) {
                        resultado = "badge-dark";
                    }
                    if (enrutado) {
                        resultado = "badge-dark";
                    }
                    if (cortado) {
                        resultado = "badge-dark";
                    }
                    if (programado) {
                        resultado = "badge-dark";
                    }
                    if (programado && cortado) {
                        resultado = "badge-dark";
                    }
                    if (fecha_real_entrega) {
                        resultado = "badge-success";
                    }
                    return resultado;
                },
                determinarEstatus(componente, fecha_real_entrega) {
                    const {
                        cargado,
                        enrutado,
                        cortado,
                        programado
                    } = componente;

                    let resultado = "Sin estatus";
                    if (cargado) {
                        resultado = "Enrutamiento";
                    }
                    if (enrutado) {
                        resultado = "Corte y programacion";
                    }
                    if (cortado) {
                        resultado = "Programación";
                    }
                    if (programado) {
                        resultado = "Corte";
                    }
                    if (programado && cortado) {
                        resultado = "Fabricacion";
                    }
                    if (fecha_real_entrega) {
                        resultado = "Finalizado";
                    }
                    return resultado;
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
                async fetchSolicitudes() {
                    this.cargando = true
                    try {
                        const response = await axios.get(`/api/mis-solicitudes-afilado`);
                        this.solicitudes = response.data.solicitudes;
                        console.log("Solicitudes: ", this.solicitudes)
                    } catch (error) {
                        console.error('Error fetching solicitudes:', error);
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
                    console.log(t.nuevo);
                    if (!valido) return;
                    t.cargando = true;
                    const formData = new FormData();
                    formData.append('data', JSON.stringify(t.nuevo));
                    formData.append('archivo_2d', t.archivo_2d);

                    try {
                        const response = await axios.post('/api/generar-orden-afilado', formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        });
                        if (response.data.success) {
                            swal('Éxito', 'La orden de afilado ha sido creada exitosamente.', 'success');
                            t.nuevo = {
                                fecha_solicitud: new Date().toISOString().slice(0, 10),
                                fecha_deseada_entrega: new Date().toISOString().slice(0, 10),
                                nombre_solicitante: '',
                                area_solicitud: 'Herramentales',
                                numero_hr: '',
                                cantidad: 1,
                                unidad_medida_id: null,
                                archivo_2d: null,
                                comentarios: '',
                                solicitante_id: JSON.parse('{!! json_encode(auth()->user()->id) !!}')
                            };
                            t.archivo_2d = null;

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
                        t.fetchSolicitudes();
                    }
                },
                editarSolicitud(solicitud) {
                    this.modo_edicion = true;
                    this.nuevo = JSON.parse(JSON.stringify(solicitud));
                    swal('Modo edición activo', 'Estás en modo edición, puedes modificar los archivos y algunos datos de la solicitud y enviarla nuevamente.', 'info');
                },
                async enviarModificacion() {
                    let t = this

                    let valido = await t.validarFormulario();
                    if (!valido) {
                        return
                    }

                    t.cargando = true
                    const formData = new FormData();
                    formData.append('data', JSON.stringify(t.nuevo));
                    formData.append('archivo_2d', t.archivo_2d);

                    try {
                        const response = await axios.post(`/api/editar-orden-afilado/${t.nuevo.id}`, formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        });

                        if (response.data.success) {
                            swal('Guardado', 'Tu modiciación se ha guardado correctamente y se notificará al jefe de area, puedes ver el estado del componente desde la lista de la derecha o desde el visor de avance.', 'success');
                            t.nuevo = {
                                fecha_solicitud: new Date().toISOString().slice(0, 10),
                                fecha_deseada_entrega: new Date().toISOString().slice(0, 10),
                                nombre_solicitante: '',
                                area_solicitud: 'Herramentales',
                                numero_hr: '',
                                unidad_medida_id: null,
                                cantidad: 1,
                                archivo_2d: null,
                                comentarios: '',
                                solicitante_id: JSON.parse('{!! json_encode(auth()->user()->id) !!}')
                            }
                            t.modo_edicion = false;
                            t.fetchSolicitudes();
                        } else
                            swal('Error', response.data.message, 'error');
                        t.cargando = false;

                    } catch (error) {
                        const mensaje = error.response?.data?.error || 'Error al guardar el componente.';
                        swal('Error', mensaje, 'error');
                        t.cargando = false;
                    }
                },
                cancelarModificacion() {
                    this.nuevo = {
                        fecha_solicitud: new Date().toISOString().slice(0, 10),
                        fecha_deseada_entrega: new Date().toISOString().slice(0, 10),
                        solicitante_id: JSON.parse('{!! json_encode(auth()->user()->id) !!}'),
                        fecha_real_entrega: "",
                        area_solicitud: "Herramentales",
                        numero_hr: "",
                        numero_componente: "",
                        cantidad: 1,
                        archivo_2d: null,
                        comentarios: "",
                        caras_a_afilar: '',
                        cuanto_afilar: '',
                    }
                    this.modo_edicion = false;
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
                t.fetchSolicitudes();
                t.obtenerMedidas();
            }
        });
    </script>
    @endpush