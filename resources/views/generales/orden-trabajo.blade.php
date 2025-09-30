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

   .content-2 :is(label, input, textarea, button, select) {
        font-size: 0.875rem !important;
    }

     


    .table .form-check label .form-check-sign::before, .table .form-check label .form-check-sign::after {top: -10px !important}
</style>

@section('content')
    <div class="content content-2" id="vue-app">
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
        <div class="row mt-3 px-5" v-cloak>
            <div class="col-lg-6" style="border-right: 1px solid #ededed">
                <div class="row">
                    <div class="col-xl-12 mb-4">
                        <h2 v-if="modo_edicion" class="bold my-0 py-1 text-decoration-underline" style="letter-spacing: 2px">MODIFICACIÓN A LA ORDEN DE TRABAJO</h2>
                        <h2 v-else class="bold my-0 py-1 text-decoration-underline" style="letter-spacing: 2px">NUEVA ORDEN DE TRABAJO</h2>
                    </div>
                    <div class="col-lg-6 form-group">
                        <label class="bold">Fecha de solicitud</label>
                        <input :disabled="modo_edicion" type="date" class="form-control" v-model="nuevo.fecha_solicitud" readonly>
                    </div>
                    <div class="col-lg-6 form-group">
                        <label class="bold">Fecha deseada entrega <span class="text-danger">*</span> </label>
                        <input type="date" class="form-control" v-model="nuevo.fecha_deseada_entrega" :min="new Date().toISOString().split('T')[0]">
                    </div>
                    <div class="col-lg-6 form-group">
                        <label class="bold">Área solicitante <span class="text-danger">*</span></label>
                        <select :disabled="modo_edicion" class="form-control" v-model="nuevo.area_solicitud">
                            <option v-for="obj in areas_solicitantes" :value="obj">@{{obj}}</option>
                        </select>
                    </div>
                    <div class="col-lg-6 form-group">
                        <label class="bold">Nombre del solicitante <span class="text-danger">*</span></label>
                        <v-select 
                            :options="usuarios" 
                            label="nombre_completo" 
                            v-model="nuevo.solicitante_id" 
                            disabled
                            :reduce="usuario => usuario.id">
                        </v-select>
                    </div>
                    <div class="col-lg-4 form-group">
                        <label class="bold">Número de HR <span class="text-danger">*</span></label>
                        <input :disabled="modo_edicion" type="text" class="form-control" v-model="nuevo.numero_hr">
                    </div>
                    <div class="col-lg-4 form-group">
                        <label class="bold">Número de componente <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend" style="background-color: #efefef">
                                <span class="bold input-group-text py-0">- &nbsp;&nbsp;</span>
                            </div>
                            <input :disabled="modo_edicion" type="text" class="form-control" v-model="nuevo.numero_componente">
                        </div>
                    </div>
                    <div class="col-lg-4 form-group">
                        <label class="bold">Cantidad <span class="text-danger">*</span></label>
                        <input :disabled="modo_edicion" type="number" step="1" class="form-control" v-model="nuevo.cantidad">
                    </div>
                    <div class="col-lg-4 form-group">
                        <label class="bold">Archivo 2D</label>
                        <input
                            class="input-file"
                            id="2d"
                            type="file"
                            @change="handleFileChange($event, 1)"
                            style="display: none;"
                        />
                        <label tabindex="0" for="2d" class="input-file-trigger col-12 text-center">
                            <i class="fa fa-upload"></i> @{{modo_edicion?'EDITAR':'CARGAR'}} 2D
                        </label>
                        <small>Archivo: <strong>@{{nuevo.archivo_2d ?? 'Sin cargar'}}</strong></small>
                    </div>
                    <div class="col-lg-4 form-group">
                        <label class="bold">Archivo 3D</label>
                        <input
                            class="input-file"
                            id="3d"
                            type="file"
                            @change="handleFileChange($event, 2)"
                            style="display: none;"
                        />
                        <label tabindex="0" for="3d" class="input-file-trigger col-12 text-center">
                            <i class="fa fa-upload"></i> @{{modo_edicion?'EDITAR':'CARGAR'}} 3D
                        </label>
                        <small>Archivo: <strong>@{{nuevo.archivo_3d ?? 'Sin cargar'}}</strong></small>
                    </div>
                    <div class="col-lg-4 form-group">
                        <label class="bold">Fotografía de dibujo a mano</label>
                        <input
                            class="input-file"
                            id="dibujo"
                            type="file"
                            @change="handleFileChange($event, 3)"
                            style="display: none;"
                        />
                        <label tabindex="0" for="dibujo" class="input-file-trigger col-12 text-center">
                            <i class="fa fa-upload"></i> @{{modo_edicion?'EDITAR':'CARGAR'}} DIBUJO
                        </label>
                        <small>Archivo: <strong>@{{nuevo.dibujo ?? 'Sin cargar'}}</strong></small>
                    </div>
                    <div class="col-lg-6 form-group">
                        <label class="bold">Material <span class="text-danger">*</span></label>
                        <select :disabled="modo_edicion" class="form-control" v-model="nuevo.material_id">
                            <option v-for="m in materiales" :value="m.id"> @{{m.nombre}} </option>
                        </select>
                    </div>
                    <div class="col-lg-6 form-group mt-4">
                        <div class="form-check">
                            <label class="form-check-label" style="font-size: 10px">
                                <input :disabled="modo_edicion" type="checkbox" class="form-check-input" v-model="nuevo.tratamiento_termico">
                                <span class="form-check-sign"></span> 
                                <span class="bold">¿Requiere tratamiento térmico? <span class="text-danger">*</span></span>
                            </label>
                        </div>
                    </div>
                    <div class="col-lg-12 form-group">
                        <label class="bold">Comentarios / Instrucciones <span class="text-danger">*</span></label>
                        <textarea v-model="nuevo.comentarios" class="form-control px-2 py-2 w-100 text-left" placeholder="Comentarios e instrucciones..." style="min-height: 150px !important"></textarea>
                    </div>
                    <div class="col-lg-12 text-center form-group">
                        <button class="btn btn-default" @click="cancelarModificacion" v-if="modo_edicion"><i class="fa fa-times-circle"></i> CANCELAR MODIFICACIÓN</button>

                        <button v-if="!cargando && modo_edicion" class="btn btn-success" @click="enviarModificacion"><i class="fa fa-paper-plane"></i> ENVIAR MODIFICACIÓN</button>
                        <button v-if="cargando && modo_edicion" disabled class="btn btn-success" ><i class="fa fa-spinner"></i> ENVIANDO, ESPERE...</button>
                        
                        <button v-if="!cargando && !modo_edicion" class="btn btn-success" @click="enviar"><i class="fa fa-paper-plane"></i> ENVIAR SOLICITUD</button>
                        <button v-if="cargando && !modo_edicion" disabled class="btn btn-success"><i class="fa fa-spinner"></i> ENVIANDO, ESPERE...</button>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row">
                    <div class="col-xl-12 mb-4">
                        <h2 class="bold my-0 py-1 text-decoration-underline" style="letter-spacing: 2px">MIS ORDENES DE TRABAJO</h2>
                    </div>
                    <div class="col-xl-12 table-responsive" style="max-height: 70vh; overflow-y: auto">
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
                                <tr v-for="solicitud in solicitudes" >
                                   <td class="py-1 bold">@{{solicitud.componente.nombre}}</td>
                                    <td class="py-1" >@{{solicitud.fecha_solicitud_show}}</td>
                                    <td class="py-1" >@{{solicitud.fecha_deseada_show}}</td>
                                    <td class="py-1" >@{{solicitud.fecha_real_show}}</td>
                                    <td class="py-1" >
                                        </span> <span style="font-size: 12px !important" :class="solicitud.class" class="badge badge-pill px-2 py-1 my-2">@{{solicitud.estatus}}</span>
                                    </td>
                                    <td>
                                        <a :href="'/visor-avance-hr' + solicitud.componente.rutaComponente" class="text-dark"><i class="cursor-pointer fa fa-info-circle"></i></a>
                                        <span v-if="solicitud.estatus != 'Finalizado'" class="text-muted"> | </span>
                                        <i v-if="solicitud.estatus != 'Finalizado'" @click="editarSolicitud(solicitud)" class="cursor-pointer fa fa-edit"></i> 
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
    </div>
@endsection

@push('scripts')

    <script type="text/javascript">
        Vue.component('v-select', VueSelect.VueSelect)

        var app = new Vue({
        el: '#vue-app',
        data: {
            modo_edicion: false,
            loading_button: false,
            cargando: false,
            materiales: [],
            usuarios: [],
            nuevo: {
                fecha_solicitud: new Date().toISOString().slice(0, 10),
                fecha_deseada_entrega: new Date().toISOString().slice(0, 10),
                nombre_solicitante: "",
                fecha_real_entrega: "",
                area_solicitud: "Herramentales",
                numero_hr: "",
                numero_componente: "",
                cantidad: 1,
                archivo_2d: null,
                archivo_3d: null,
                dibujo: null,
                material_id: 1,
                tratamiento_termico: false,
                comentarios: "",
                desde: "auxiliar_diseno",
                solicitante_id: {{auth()->user()->id}}
            },
            roles_usuario: JSON.parse('{!! json_encode(auth()->user()->roles->pluck("name")) !!}'),
            solicitudes: [],
            archivo_2d: null,
            archivo_3d: null,
            dibujo: null,
            areas_solicitantes: [],
        },
        methods:{
            obtenerAreasSolicitud(){
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
                    if(t.areas_solicitantes.length > 0){
                        t.nuevo.area_solicitud = t.areas_solicitantes[0];
                    }
                });
            },
            async validarFormulario(nuevo){
                let t = this;
                if(t.nuevo.fecha_deseada_entrega == "" || t.nuevo.area_solicitud == "" || t.nuevo.solicitante_id == "" || t.nuevo.numero_hr == "" || t.nuevo.numero_componente == "" || t.nuevo.cantidad == "" || t.nuevo.comentarios == ""){
                    swal('Error', 'Debes llenar todos los campos marcados con un asterisco.', 'error');
                    return false;
                }
               if ((!t.archivo_2d && !t.archivo_3d && !t.dibujo) && (!t.nuevo.archivo_2d && !t.nuevo.archivo_3d && !t.nuevo.dibujo)) {
                    swal('Error', 'Debes cargar al menos uno de los archivos: 2D, 3D o el dibujo a mano.', 'error');
                    return false;
                }
                return true;
            },
            cancelarModificacion(){
                this.nuevo = {
                    fecha_solicitud: new Date().toISOString().slice(0, 10),
                    fecha_deseada_entrega: new Date().toISOString().slice(0, 10),
                    solicitante_id: {{auth()->user()->id}},
                    fecha_real_entrega: "",
                    area_solicitud: "Herramentales",
                    numero_hr: "",
                    numero_componente: "",
                    cantidad: 1,
                    archivo_2d: null,
                    archivo_3d: null,
                    dibujo: null,
                    material_id: 1,
                    tratamiento_termico: false,
                    comentarios: "",
                    desde: "auxiliar_diseno",
                }
                this.modo_edicion = false;
            },
            async enviarModificacion(){
                let t =  this
                
                let valido = await t.validarFormulario();
                if(!valido){
                    return
                }
                
                t.cargando = true
                const formData = new FormData();
                formData.append('data', JSON.stringify(t.nuevo));
                formData.append('archivo_2d', t.archivo_2d);
                formData.append('archivo_3d', t.archivo_3d);
                formData.append('dibujo', t.dibujo);

                try {
                    const response = await axios.post(`/api/orden-trabajo/${t.nuevo.id}`, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });

                    if (response.data.success) {
                        swal('Guardado', 'Tu modificación se ha guardado correctamente y se notificará al jefe de area, puedes ver el estado del componente desde la lista de la derecha o desde el visor de avance.', 'success');
                        t.nuevo = {
                            fecha_solicitud: new Date().toISOString().slice(0, 10),
                            fecha_deseada_entrega: new Date().toISOString().slice(0, 10),
                            solicitante_id: {{auth()->user()->id}},
                            fecha_real_entrega: "",
                            area_solicitud: "Herramentales",
                            numero_hr: "",
                            numero_componente: "",
                            cantidad: 1,
                            archivo_2d: null,
                            archivo_3d: null,
                            dibujo: null,
                            material_id: 1,
                            tratamiento_termico: false,
                            comentarios: "",
                            desde: "auxiliar_diseno",
                        }
                        t.modo_edicion =  false;
                        t.obtenerAreasSolicitud();
                        t.fetchSolicitudes();
                    }else
                        swal('Error', response.data.message, 'error');
                    t.cargando = false;

                } catch (error) {
                    const mensaje = error.response?.data?.error || 'Error al guardar el componente.';
                    swal('Error', mensaje, 'error');
                    t.cargando = false;
                }
            },
            editarSolicitud(solicitud) {
                this.modo_edicion = true;
                this.nuevo = JSON.parse(JSON.stringify(solicitud));
                swal('Modo edición activo', 'Estás en modo edición, puedes modificar los archivos y algunos datos de la solicitud y enviarla nuevamente.', 'info');
            },
            getClaseColor(componente, fecha_real_entrega) {
                const { cargado, enrutado, cortado, programado } = componente;

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
                const { cargado, enrutado, cortado, programado } = componente;

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
                    resultado = "Fabricación";
                }
                if (fecha_real_entrega) {
                    resultado = "Finalizado";
                }
                return resultado;
            },         
            async handleFileChange(event, tipo) {
                const file = event.target.files[0];
                if (!file) return;

                if (tipo == 1) {
                    this.nuevo.archivo_2d = file.name;
                    this.archivo_2d = file;
                }
                if (tipo == 2) {
                    this.nuevo.archivo_3d = file.name;
                    this.archivo_3d = file;
                }
                if (tipo == 3) {
                    this.nuevo.dibujo = file.name;
                    this.dibujo = file;
                }
            },
            async enviar() {
                let t =  this
                
                let valido = await t.validarFormulario();
                if(!valido){
                    return
                }
                
                t.cargando = true
                const formData = new FormData();
                formData.append('data', JSON.stringify(t.nuevo));

                formData.append('archivo_2d', t.archivo_2d);
                formData.append('archivo_3d', t.archivo_3d);
                formData.append('dibujo', t.dibujo);

                try {
                    const response = await axios.post(`/api/orden-trabajo`, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });

                    if (response.data.success) {
                        swal('Guardado', 'Tu solicitud se ha generado correctamente, puedes ver el estado del componente desde la lista de la derecha o desde el visor de avance.', 'success');
                        t.nuevo = {
                            fecha_solicitud: new Date().toISOString().slice(0, 10),
                            fecha_deseada_entrega: new Date().toISOString().slice(0, 10),
                            solicitante_id: {{auth()->user()->id}},
                            fecha_real_entrega: "",
                            area_solicitud: "Herramentales",
                            numero_hr: "",
                            numero_componente: "",
                            cantidad: 1,
                            archivo_2d: null,
                            archivo_3d: null,
                            dibujo: null,
                            material_id: 1,
                            tratamiento_termico: false,
                            comentarios: "",
                            desde: "auxiliar_diseno",
                        }
                        t.obtenerAreasSolicitud();
                        t.fetchSolicitudes();
                    }else
                        swal('Error', response.data.message, 'error');
                    t.cargando = false;

                } catch (error) {
                    const mensaje = error.response?.data?.error || 'Error al guardar el componente.';
                    swal('Error', mensaje, 'error');
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
                }
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
                    const response = await axios.get(`/api/mis-solicitudes-externas`);
                    this.solicitudes = response.data.solicitudes;
                    this.solicitudes.forEach(element => {
                        element.estatus = this.determinarEstatus(element.componente, element.fecha_real_entrega);
                        element.class = this.getClaseColor(element.componente, element.fecha_real_entrega);
                    });
                    this.solicitudes.sort((a, b) => {
                        const aEsFinalizado = a.estatus === 'Finalizado';
                        const bEsFinalizado = b.estatus === 'Finalizado';

                        if (aEsFinalizado && !bEsFinalizado) {
                            return 1; // 'a' (Finalizado) va después que 'b'
                        }
                        if (!aEsFinalizado && bEsFinalizado) {
                            return -1; // 'a' (No finalizado) va antes que 'b'
                        }
                        return b.fecha_solicitud.localeCompare(a.fecha_solicitud);
                    });

                } catch (error) {
                    console.error('Error fetching solicitudes:', error);
                } finally {
                    this.cargando = false;
                }
            },
        },
        mounted: async function () {
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
            await t.fetchMateriales();
            await t.fetchUsuarios();
            await t.fetchSolicitudes();
        }

                
    })

    </script>



        
@endpush