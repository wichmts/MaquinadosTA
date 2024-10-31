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
        <div class="row mt-3 px-2" v-cloak v-show="!cargando" >
            <div class="col-xl-6 d-flex align-items-end">
                <h2 class="bold my-0 py-1 text-decoration-underline" style="letter-spacing: 2px">ALMACÉN DE MATERIA PRIMA</h2>
            </div>
            <div class="col-xl-2 d-flex align-items-end">
                <div class="w-100">
                    <label class="bold" style="letter-spacing: 1px">Filtrar por MP</label>
                    <select class="form-control" v-model="materialSelected" @change="fetchHojas">
                        <option v-for="m in materiales" :value="m.id">@{{m.nombre}}</option>
                    </select>
                </div>
            </div>
            <div class="col-xl-2 d-flex align-items-end">
                <div class="w-100">
                    <label class="bold" style="letter-spacing: 1px">Filtrar por estatus</label>
                    <select class="form-control" v-model="estatusSelected" @change="fetchHojas">
                        <option value="activo">ACTIVAS</option>
                        <option value="inactivo">INACTIVAS</option>
                    </select>
                </div>
            </div>
            <div class="col-xl-2 d-flex align-items-end">
                <button class="btn btn-block mb-0" @click="abrirModalNuevo"><i class="fa fa-plus-circle"></i> AÑADIR HOJA</button>
            </div>
            <div class="col-xl-12 mt-3" >
                <table class="table table-bordered" id="tabla">
                    <thead class="thead-light">
                        <tr>
                            <th  class="py-1" rowspan="2" style="width: 5% ; border: 1px solid #b6b6b6 !important">Consecutivo</th>
                            <th  class="py-1" rowspan="2" style="width: 5% ; border: 1px solid #b6b6b6 !important">Calidad</th>
                            <th  class="py-1" rowspan="2" style="width: 5% ; border: 1px solid #b6b6b6 !important">Espesor</th>    
                            <th  class="py-1 bg-success text-white" colspan="3" style="width: 13% ; border: 1px solid #b6b6b6 !important" >Entrada</th>    
                            <th  class="py-1 bg-warning" colspan="3" style="width: 13% ; border: 1px solid #b6b6b6 !important; text-transform: none !important">Saldo actual</th>    
                            <th  class="py-1" rowspan="2" style="width: 7% ; border: 1px solid #b6b6b6 !important">Precio / Kilo</th>
                            <th  class="py-1" rowspan="2" style="width: 7% ; border: 1px solid #b6b6b6 !important">Fecha Entrada</th>
                            <th  class="py-1" rowspan="2" style="width: 12% ; border: 1px solid #b6b6b6 !important">Ultimo movimiento</th>
                            <th  class="py-1" rowspan="2" style="width: 10% ; border: 1px solid #b6b6b6 !important">Factura</th>
                            <th  class="py-1" rowspan="2" style="width: 20% ; border: 1px solid #b6b6b6 !important">Acciones</th>
                        </tr>
                        <tr>
                            <th class="py-1" style="border: 1px solid #b6b6b6 !important">Largo</th>
                            <th class="py-1" style="border: 1px solid #b6b6b6 !important">Ancho</th>
                            <th class="py-1" style="border: 1px solid #b6b6b6 !important">Peso</th>
                            <th class="py-1" style="border: 1px solid #b6b6b6 !important">Ancho</th>
                            <th class="py-1" style="border: 1px solid #b6b6b6 !important">Largo</th>
                            <th class="py-1" style="border: 1px solid #b6b6b6 !important">Peso</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="h in hojas">
                            <td>@{{h.consecutivo}}</td>
                            <td>@{{h.calidad}}</td>
                            <td>@{{h.espesor}}</td>
                            <td>@{{h.largo_entrada}}</td>
                            <td>@{{h.ancho_entrada}}</td>
                            <td>@{{h.peso_entrada}}</td>
                            <td>@{{h.largo_saldo}}</td>
                            <td>@{{h.ancho_saldo}}</td>
                            <td>@{{h.peso_saldo}}</td>
                            <td>@{{h.precio_kilo | currency}}</td>
                            <td>@{{h.fecha_entrada}}</td>
                            <td>@{{h.ultimo_movimiento ?? '-' }}</td>
                            <td>
                                <a :href=`/api/download/facturas/${h.factura}` target="_blank">@{{h.factura}}</a>
                            </td>
                            <td>
                                <button class="my-1 btn btn-sm btn-default" @click="verMovimientosHoja(h.id)"><i class="fa fa-list-ul cursor-pointer"></i> ver movimientos</button>
                                <button v-if="h.estatus" class="my-1 btn btn-sm btn-danger" @click="estatusHoja(h.id, false)"><i class="fa fa-ban cursor-pointer"></i> Dar de baja</button>
                                <button v-else class="my-1 btn btn-sm btn-warning text-dark" @click="estatusHoja(h.id, true)"><i class="fa fa-level-up-alt cursor-pointer"></i> Reactivar hoja</button>
                            </td>
                        </tr>
                        <tr v-if="hojas.length == 0">
                            <td colspan="14">No ha ingresado nignuna hoja para esta materia prima</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal fade" id="modalNuevo" tabindex="-1" aria-labelledby="modalNuevoLabel" aria-hidden="true">
            <div class="modal-dialog" style="min-width: 40%;">
                <div class="modal-content" >
                    <div class="modal-header">
                        <h3 class="modal-title" id="modalNuevoLabel">
                            <span>AÑADIR HOJA</span>
                        </h3>
                        <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                             <div class="col-xl-12 form-group">
                                <label class="bold">Materia prima</label>
                                <select class="form-control" v-model="nuevo.material_id">
                                    <option v-for="m in materiales" :value="m.id">@{{m.nombre}}</option>
                                </select>
                            </div>
                            <div class="col-xl-4 form-group">
                                <label class="bold">Calidad <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" v-model="nuevo.calidad" placeholder="Calidad de la hoja...">
                            </div>
                            <div class="col-xl-4 form-group">
                                <label class="bold">Espesor <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" v-model="nuevo.espesor" placeholder="Espesor de la hoja...">
                            </div>
                            <div class="col-xl-4 form-group">
                                <label class="bold">Fecha de entrada<span class="text-danger">*</span></label>
                                <input type="date" step="any" class="form-control" v-model="nuevo.fecha_entrada" >
                            </div>
                            <div class="col-xl-3 form-group">
                                <label class="bold">Ancho <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" v-model="nuevo.ancho_entrada" placeholder="Ancho de la hoja...">
                            </div>
                            <div class="col-xl-3 form-group">
                                <label class="bold">Largo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" v-model="nuevo.largo_entrada" placeholder="Largo de la hoja...">
                            </div>
                            <div class="col-xl-3 form-group">
                                <label class="bold">Peso <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" v-model="nuevo.peso_entrada" placeholder="Peso de la hoja...">
                            </div>
                            <div class="col-xl-3 form-group">
                                <label class="bold">Precio / Kilo <span class="text-danger">*</span></label>
                                <input type="number" step="any" class="form-control" v-model="nuevo.precio_kilo" >
                            </div>
                            <div class="col-xl-12 form-group mt-3">
                                <input class="input-file" id="factura" type="file" name="file" @change="seleccionaArchivo($event)" v-show="false">
                                <label tabindex="0" for="factura" class="input-file-trigger col-12 text-center"><i class="fa fa-upload"></i> Subir factura  </label>
                                <small>@{{nuevo.factura}}</small>
                            </div>
                            <div class="col-xl-12" style="letter-spacing: 1px">
                                <small>El registro quedara guardado por el usuario: <strong>{{auth()->user()->nombre_completo}}</strong></small>
                            </div> 
                        </div>
                        <div class="row">
                        <div class="col-xl-12 text-right">
                                <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                                <button @click="guardarHoja" class="btn btn-secondary" v-if="!loading_button" type="button"><i class="fa fa-save"></i> Guardar</button>
                                <button class="btn btn-secondary" type="button" disabled v-if="loading_button"><i class="fa fa-spinner spin"></i> Guardando...</button>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalMovimientos" tabindex="-1" aria-labelledby="modalMovimientosLabel" aria-hidden="true">
            <div class="modal-dialog" style="min-width: 50%;">
                <div class="modal-content" >
                    <div class="modal-header">
                        <h3 class="modal-title" id="modalMovimientosLabel">
                            <span>
                                HISTORIAL DE MOVIMIENTOS PARA LA HOJA
                            </span>
                        </h3>
                        <button v-if="!loading_button" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">


                        <div class="row">
                            <div class="col-xl-12">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="py-1" style="border: 1px solid #b6b6b6 !important" rowspan="2">Fecha</th>
                                            <th class="py-1" style="border: 1px solid #b6b6b6 !important; text-transform: none !important; " colspan="3">Restante despues del movimiento</th>
                                            <th class="py-1" style="border: 1px solid #b6b6b6 !important" rowspan="2">HR</th>
                                            <th class="py-1" style="border: 1px solid #b6b6b6 !important" rowspan="2">PY</th>
                                        </tr>
                                        <tr>
                                            <th class="py-1" style="border: 1px solid #b6b6b6 !important">Ancho</th>
                                            <th class="py-1" style="border: 1px solid #b6b6b6 !important">Largo</th>
                                            <th class="py-1" style="border: 1px solid #b6b6b6 !important">Peso</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="m in movimientos">
                                            <td>@{{m.fecha}} @{{m.hora}}</td>
                                            <td>@{{m.ancho}}</td>
                                            <td>@{{m.largo}}</td>
                                            <td>@{{m.peso}}</td>
                                            <td>@{{m.py}}</td>
                                            <td>@{{m.hr}}</td>
                                        </tr>
                                        <tr v-if="movimientos.length == 0">
                                            <td colspan="6">No se han realizado movimientos para esta hoja</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-xl-12 text-right">
                                <button class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
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
            materiales: [],
            hojas: [],
            movimientos: [],
            nuevo: {},
            materialSelected: -1,
            estatusSelected: 'activo',
        },
        methods:{
            estatusHoja(id, estatus){
                let t = this
                if(estatus){
                    swal({
                        title: "¿Esta seguro de reactivar esta hoja?",
                        text: "Aparecera nuevamente como una hoja activa para las otras areas",
                        icon: "warning",
                        buttons: ['Cancelar', 'Si, reactivar'],
                        dangerMode: false,
                    })
                    .then((willDelete) => {
                        if (willDelete) {
                            t.cargando = true;
                            axios.delete(`/api/hoja/${id}/${estatus}`).then(response => {
                                if(response.data.success){
                                    swal('Correcto', 'Hoja reactivada correctamente, podra seguir visualizandola en hojas activas.', 'success');
                                    t.fetchHojas();
                                    t.cargando = false;
                                }else{
                                    t.cargando = false;
                                }
                            })
                        }
                    });
                }else{
                    swal({
                        title: "¿Esta seguro de dar de baja esta hoja?",
                        text: "No aparecera como una hoja activa para las otras areas",
                        icon: "warning",
                        buttons: ['Cancelar', 'Si, dar de baja'],
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) {
                            t.cargando = true;
                            axios.delete(`/api/hoja/${id}/${estatus}`).then(response => {
                                if(response.data.success){
                                    swal('Correcto', 'Hoja dada de baja correctamente, podra seguir visualizandola en hojas inactivas.', 'success');
                                    t.fetchHojas();
                                    t.cargando = false;
                                }else{
                                    t.cargando = false;
                                }
                            })
                        }
                    });
                }



            },
            verMovimientosHoja(id){
                let t = this;
                t.cargando = true
                axios.get(`api/movimientos-hoja/${id}`).then(response => {
                    if(response.data.success){
                        t.movimientos = response.data.movimientos;
                        t.cargando = false;
                        Vue.nextTick(function(){
                            $('#modalMovimientos').modal();
                        })
                    }else{
                        swal('Lo sentimos!', response.data.message, 'info');
                        t.cargando = false;
                        console.log(e);
                    }
                })    
            },
            guardarHoja(){
                let t = this;
                let formData = new FormData();
                let file1 = document.querySelector('#factura');

                if(!t.nuevo.calidad || !t.nuevo.espesor || !t.nuevo.largo_entrada || !t.nuevo.ancho_entrada || !t.nuevo.peso_entrada || !t.nuevo.precio_kilo || !t.nuevo.fecha_entrada || !t.nuevo.material_id ){
                    swal('Campos obligatorios', 'Todos los campos son obligatorios', 'info');
                    return;
                }
                
                if (!file1.files.length) {
                    swal('Factura obligatoria', 'Por favor seleccione un archivo antes de guardar.', 'info');
                    return;
                }
                
                formData.append("factura", file1.files[0]);
                formData.append("data", JSON.stringify(t.nuevo));

                axios.post(`/api/hoja`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(response => {
                    if(response.data.success){
                        swal({
                            title: 'Hoja guardarda correctamente',
                            icon: 'success',
                            allowOutsideClick: false
                        }).then(() => {
                            t.fetchHojas();
                            $('#modalNuevo').modal('toggle');
                        });
                    }else{
                        swal('Lo sentimos!', response.data.message, 'info');
                        t.cargando = false;
                        console.log(e);
                    }
                }).catch(e => {
                    swal('Lo sentimos!', 'Intentelo de nuevo mas tarde', 'info');
                    t.cargando = false;
                    console.log(e);
                });
            },
            seleccionaArchivo: function(e){
                let t = this;
                var files = e.target.files || e.dataTransfer.files;

                if (!files.length)
                    return;
                
                t.nuevo.factura = '';
                t.nuevo.factura += files[0].name;
            },
            abrirModalNuevo(){
                this.nuevo = {
                    calidad: '',
                    espesor: '',
                    largo_entrada: '',
                    ancho_entrada: '',
                    peso_entrada: '',
                    largo_saldo: '',
                    ancho_saldo: '',
                    peso_saldo: '',
                    precio_kilo: 0,
                    fecha_entrada: new Date().toISOString().split('T')[0] ,
                    factura: '',
                    material_id: this.materialSelected,
                }
                $('#modalNuevo').modal();
                
                Vue.nextTick(function(){
                    document.querySelector("html").classList.add('js');
                    let fileInput  = document.querySelector( ".input-file" )
                    let button     = document.querySelector( ".input-file-trigger" )
                    
                    button.addEventListener( "keydown", function( event ) {
                        if ( event.keyCode == 13 || event.keyCode == 32 ) {
                            fileInput.focus();
                        }
                    });

                    button.addEventListener( "click", function( event ) {
                        fileInput.focus();
                        return false;
                    });
                })
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
            async fetchHojas() {
                this.cargando = true
                try {
                    const response = await axios.get(`/api/hojas/${this.materialSelected}?estatus=${this.estatusSelected}`);
                    this.hojas = response.data.hojas;
                    Vue.nextTick(function(){
                        // $('#tabla').DataTable({
                        //     paging: true,
                        //     searching: true,
                        //     pageLength: 20,
                        //     lengthMenu: [[20, 30, 50, 100, -1], [20, 30, 50, 100, "All"]],  
                        //     ordering: true,
                        //     info: true,
                        //     language: {
                        //         url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
                        //     }
                        // });
                    });
                } catch (error) {
                    console.error('Error fetching hojas:', error);
                } finally {
                    this.cargando = false;
                }
            },  
        },
        mounted: async function () {
            let t = this;
            await t.fetchMateriales();
        }

                
    })

    </script>



        
@endpush