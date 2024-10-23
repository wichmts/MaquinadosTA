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
/*        border-radius: 6px !important;*/
        box-shadow:none !important;
/*        border: .5px solid #a9a9a9 !important;*/
     }

     .form-group{
        /* margin-bottom: 5px !important; */
     }

     input[type=checkbox], input[type=radio]{
        width: 17px !important;
        height: 17px !important;
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
        <div class="card shadow col-xl-12" v-cloak v-show="!cargando">
            <div class="card-header border-0">
                <h3 class="bold my-0 py-1" style="letter-spacing: 1px;color: #183149 !important">Completa la información de tu empresa</h3>
                <span>Aquí podrás administrar la información general de la empresa, así como configurar los datos de facturación correspondientes.</small>
                <hr>
            </div>
            <div class="card-body pt-0">
                <div class="row align-items-middle">
                    <div class="col-xl-8" style="border-right: 1px solid rgba(0,0,0,.1)">
                        <div class="row">
                            <div class="col-xl-12">
                                <nav>
                                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                        <button class="nav-link active" id="nav-generales-tab" data-toggle="tab" data-target="#nav-generales" type="button" role="tab" aria-controls="nav-generales" aria-selected="true">Información facturación</button>
                                        {{-- <button class="nav-link" id="nav-facturacion-tab" data-toggle="tab" data-target="#nav-facturacion" type="button" role="tab" aria-controls="nav-facturacion" aria-selected="false">Otras configuraciones</button> --}}
                                    </div>
                                </nav>
                                <div class="tab-content" id="nav-tabContent">
                                    <div class="tab-pane fade show active" id="nav-generales" role="tabpanel" aria-labelledby="nav-generales-tab">
                                        <div class="row">
                                            <div class="col-xl-12 my-2">
                                                <h4 class="bold my-0 py-1" style="letter-spacing: 1px;color: #183149 !important">Información de facturación</h5>
                                            </div>

                                            <div class="col-xl-8 form-group">
                                                <label class="bold">Razon social</label>
                                                <input type="text" class="form-control" v-model="configuracion.razon_social">
                                            </div>
                                            <div class="col-xl-4 form-group">
                                                <label class="bold">RFC <span class="text-danger">*</span></label>
                                                <input v-model="configuracion.rfc" type="text" class="form-control" @input="validarInput">
                                                <small v-if="rfc_valido" class="bold text-success">RFC válido</small>
                                                <small v-else class="bold text-danger">RFC inválido</small>
                                            </div>
                                            <div class="form-group col-xl-6">
                                                <label class="bold">Estado <span class="text-danger">*</span></label>
                                                <v-select placeholder="Seleccione un estado..." :options="estados" v-model="configuracion.estado" id="estado" label="name" :reduce="estado => estado.name" @input="getCiudades()"></v-select>
                                            </div>
                                            <div class="form-group col-xl-6">
                                                <label class="bold">Ciudad <span class="text-danger">*</span></label>
                                                <v-select placeholder="Seleccione una ciudad..." :options="ciudades" v-model="configuracion.ciudad" id="ciudad" label="name" :reduce="ciudad => ciudad.name"></v-select>
                                            </div>
                                            <div class="col-xl-4 form-group">
                                                <label class="bold">Colonia <span class="text-danger">*</span></label>
                                                <input class="form-control" type="text" v-model="configuracion.colonia" id="colonia" placeholder="Colonia o localidad">
                                            </div>
                                            <div class="col-xl-5 form-group">
                                                <label class="bold">Calle y numero <span class="text-danger">*</span></label>
                                                <input class="form-control" type="text" v-model="configuracion.calle" id="calle" placeholder="Calle y numero del configuracion">
                                            </div>
                                            <div class="col-xl-3 form-group">
                                                <label class="bold">Código postal <span class="text-danger">*</span></label>
                                                <input class="form-control" type="text" v-model="configuracion.codigo_postal" id="codigo_postal" placeholder="código postal">
                                            </div>
                                            <div class="col-xl-12 py-3 text-center">
                                                <button class="btn col-xl-4" @click="guardarGenerales()"> <i class="fa fa-save"></i> GUARDAR DATOS GENERALES</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-xl-4 text-center">
                        <img src="{{ \App\Helpers\SystemHelper::getLogo() }}" style="border-radius: 10px !important" width="90%">
                         <div class="col-xl-12 mt-3 form-group">
                            <input accept="image/*"  class="input-file" id="logo" type="file" name="file" @change="seleccionaArchivo($event)" v-show="false">
                            <label tabindex="0" for="logo" class="input-file-trigger col-12 text-center"><i class="fa fa-edit"></i> Cambiar logo empresa</label>
                            <p class="file-return">@{{logo}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
{{--  --}}

@push('scripts')

    <script type="text/javascript">
        Vue.component('v-select', VueSelect.VueSelect)

        var app = new Vue({
        el: '#vue-app',
        data: {
            configuracion: {
                razon_social: '',
                rfc: '',
                estado: '',
                ciudad: '',
                colonia: '',
                calle: '',
                codigo_postal: '',
            },
            cargando: true,
            rfc_valido: false,
            logo: '',
            ciudades: [],
            estados: [],
        },
        computed: {

        },
        methods:{
            seleccionaArchivo: function(e){
                let t = this;
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;


                let formData = new FormData();
                let file1 = document.querySelector('#logo');

                if(file1.files[0] == undefined){
                    swal('Campos obligatorios', 'Aun no ha subido una imagen.', 'info');
                    return;
                }
                formData.append("logo", file1.files[0]);

                t.cargando = true

                axios.post(`/api/configuracion/logo`, formData, {
                    headers: {
                    'Content-Type': 'multipart/form-data'
                    }
                }).then(response => {
                    if(response.data.success){
                        swal('Correcto!', 'Logo de la empresa actualizado correcamente', 'success');
                        t.cargando = false
                        location.reload();
                    }else{
                        swal('Lo sentimos!', response.data.message, 'info');
                        t.cargando = false
                    }
                }).catch(e => {
                    swal('Lo sentimos!', 'Intentelo de nuevo mas tarde', 'info');
                    t.cargando = false;
                    console.log(e);
                });

            },
            getPeriodicidad: function(valor) {
                return this.periodicidades[valor] || 'Valor no válido';
            },
            getRegimenFiscal: function(valor){
                return this.regimenes[valor] || 'Valor no válido';
            },
            rfcValido: function(rfc, aceptarGenerico = true) {
                const re       = /^([A-ZÑ&]{3,4}) ?(?:- ?)?(\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])) ?(?:- ?)?([A-Z\d]{2})([A\d])$/;
                var   validado = rfc.match(re);

                if (!validado)  //Coincide con el formato general del regex?
                    return false;

                //Separar el dígito verificador del resto del RFC
                const digitoVerificador = validado.pop(),
                    rfcSinDigito      = validado.slice(1).join(''),
                    len               = rfcSinDigito.length,

                //Obtener el digito esperado
                    diccionario       = "0123456789ABCDEFGHIJKLMN&OPQRSTUVWXYZ Ñ",
                    indice            = len + 1;
                var   suma,
                    digitoEsperado;

                if (len == 12) suma = 0
                else suma = 481; //Ajuste para persona moral

                for(var i=0; i<len; i++)
                    suma += diccionario.indexOf(rfcSinDigito.charAt(i)) * (indice - i);
                digitoEsperado = 11 - suma % 11;
                if (digitoEsperado == 11) digitoEsperado = 0;
                else if (digitoEsperado == 10) digitoEsperado = "A";

                if ((digitoVerificador != digitoEsperado)
                && (!aceptarGenerico || rfcSinDigito + digitoVerificador != "XAXX010101000"))
                    return false;
                else if (!aceptarGenerico && rfcSinDigito + digitoVerificador == "XEXX010101000")
                    return false;
                return rfcSinDigito + digitoVerificador;
            },
            validarInput: function() {
                let t = this
                t.rfc_valido = false
                t.configuracion.rfc = t.configuracion.rfc.toUpperCase();

                var rfc = t.configuracion.rfc.trim()
                
                var rfcCorrecto = t.rfcValido(rfc);  
                
                if (rfcCorrecto)
                    t.rfc_valido = true
                else
                    t.rfc_valido = false
            }, 
            guardarGenerales: function(){
                let t = this
                axios.post('/api/configuracion', t.configuracion).then(response => {
                    if(response.data.success){
                        swal('Se han guardado sus cambios', 'Su informacion ha sido guardada correctamente', 'success');
                    }
                })
            },
            getData: function(){
                let t = this;
                t.cargando = true;

                axios.get(`/api/states/Mexico`).then(response => {
                    t.estados = response.data.estados
                })
                axios.get(`/api/configuracion`).then(response => {
                    t.configuracion = response.data.configuracion
                    
                    if(!t.configuracion)
                    t.configuracion = {
                        razon_social: '',
                        rfc: '',
                        estado: '',
                        ciudad: '',
                        colonia: '',
                        calle: '',
                        codigo_postal: '',
                    }
                    if(t.configuracion.rfc && t.configuracion.rfc != '')
                        t.validarInput();
                    t.cargando = false
                });
            },
            getCiudades:function(index){
                let t = this;
                t.configuracion.ciudad = '';
                
                if(!t.configuracion.estado){
                    t.ciudades = [];
                    return;
                }
                axios.get('/api/cities/' + t.configuracion.estado).then(function(response){
                    t.ciudades = response.data.ciudades;
                }).catch(errors => {
                    console.log(errors);
                });
            },
        },
        mounted: function () {
            this.$nextTick(function () {
                let t = this;
                document.querySelector("html").classList.add('js');
                var fileInput  = document.querySelector( ".input-file" ),
                button     = document.querySelector( ".input-file-trigger" ),
                the_return = document.querySelector(".file-return");

                button.addEventListener( "keydown", function( event ) {
                    if ( event.keyCode == 13 || event.keyCode == 32 ) {
                        fileInput.focus();
                    }
                });
                button.addEventListener( "click", function( event ) {
                    fileInput.focus();
                    return false;
                });
                t.getData();

            })
        }
                
    })

    </script>



        
@endpush