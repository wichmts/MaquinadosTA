@extends('layouts.app', [
    'class' => 'register-page',
    'backgroundImagePath' => 'img/bg/jan-sendereks.jpg'
])


@section('content')
    <style>
        .selectable{
            cursor: pointer; 
        }
        .selectable:hover{
            cursor: pointer;
            background-color:  #17395d;
            color: white 
        }

        .no-selectable{
             background-color: #e5e8e8 ; 
        }

        [v-cloak] {
            display: none !important;
        }
        .fade-enter-active,
        .fade-leave-active {
            transition: opacity .5s ease;
        }
        .fade-enter, .fade-leave-to {
            opacity: 0
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
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .text-sm{
            font-size: 11px !important;
        }
        .title-table{
            font-size: 12px !important;
        }
         @media only screen and (min-width: 600px) {
        .text-sm{
            font-size: 13px !important;
        }   
        .title-table{
           font-size: 13px !important;
        }
    }
    </style>
    <div class="homepage">
            <div class="content"  id="vue-app">
        <div class="container ">
            <div class="row">
                <div class="col-md-6 d-none d-sm-block">
                    <div class="info-area info-horizontal mt-5">
                        <div class="icon icon-primary">
                            <i class="text-white fa fa-user-md"></i>
                        </div>
                        <div class="description">
                            <h5 class="info-title">Gestiona tu salud desde tu celular..</h5>
                            <p class="description">
                                Programa tus citas, accede a tus historiales médicos, realiza seguimientos de tratamientos y mucho más, desde la comodidad desde donde estes.
                            </p>
                        </div>
                    </div>
                    <div class="info-area info-horizontal mt-5">
                        <div class="icon icon-primary">
                            <i class="fa fa-hand-holding-usd text-white"></i>
                        </div>
                        <div class="description">
                            <h5 class="info-title">Tu tiempo y bienestar son valiosos.</h5>
                            <p class="description">
                                Hemos creado una experiencia única que te permite acceder a médicos especialistas de manera rápida y sencilla desde donde estes.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 mr-auto" v-cloak>
                    <div  class="card card-signup text-center" >
                        <div class="card-header" v-if="step != 5">
                            <h4 class="card-title p-0 m-0">@{{(step == 1 || step == 0) ? 'Crear cuenta como paciente' : '¡Registro exitoso!'}}</h4>
                            <h5 v-if="step == 1" style="font-size: 15px !important; color: grey" class="card-title p-0 m-0">{{ __('Registrate y forma parte de RHPlus.') }}</h5>
                        </div>
                        <Transition name="fade" mode="out-in">
                        <div v-if="step == 0" key="step0">
                            <div class="card-body px-3">                                
                                <div style="margin-bottom: 15%;margin-top: 5%; text-align:center; letter-spacing: 1px">
                                    <h5 class="mb-5 d-inline d-sm-none" style="font-size: 15px">Espere un momento...</h5>
                                    <h5 csuss="mb-5 d-none d-sm-inline" style="font-size: 20px">Espere un momento...</h5>
                                    <div class="mt-3 loader"></div>
                                </div>                              
                            </div>
                        </div>
                        <div v-if="step == 1" key="step1">
                            <div class="card-body px-3">                                
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="nc-icon nc-single-02"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" v-model="nuevo.nombre" placeholder="Nombre(s)" required autofocus>
                                </div>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="nc-icon nc-single-02"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" v-model="nuevo.ap_paterno" placeholder="Apellido paterno" required>
                                </div>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="nc-icon nc-single-02"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" v-model="nuevo.ap_materno" placeholder="Apellido materno" required>
                                </div>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="nc-icon nc-mobile"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" v-model="nuevo.celular" placeholder="Celular (10 digitos)" required @input="isPhone" >
                                </div>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="nc-icon nc-email-85"></i>
                                        </span>
                                    </div>
                                    <input type="email" class="form-control" v-model="nuevo.email" placeholder="Email" required autocomplete="off" name="23232322">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="nc-icon nc-key-25"></i>
                                        </span>
                                    </div>
                                    <input type="password" class="form-control" minlength="6" v-model="nuevo.password" placeholder="Contraseña (Min. 6 caracteres)" required autocomplete="off" name="2323232222">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="nc-icon nc-key-25"></i>
                                        </span>
                                    </div>
                                    <input type="password" class="form-control" minlength="6" v-model="nuevo.password_confirmation" placeholder="Confirmar contraseña (Min. 6 caracteres)" required autocomplete="off" name="2323232222">
                                </div>
                                
                                <div class="form-check text-left">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="checkbox" v-model="terms">
                                        <span class="form-check-sign"></span>
                                        {{ __('Estoy de acuerdo con los ') }}
                                        <a href="/terminos-y-condiciones" target="_blank">{{ __('Términos y Condiciones') }}</a>. Acepto haber leido y entendido la <a href="/terminos-y-condiciones" target="_blank">{{ __('Política de privacidad') }}</a>
                                    </label>
                                </div>

                                <div class="card-footer py-1">
                                    <button  class="btn btn-round" @click="enviar()" >Crear cuenta</button>
                                </div>                                
                            </div>
                        </div>
                        <div v-if="step == 2" key="step2">
                            <div class="card-body px-3">                                
                                <img src="/paper/img/icons/success.png" width="25%" class="d-inline d-sm-none pb-3">
                                <img src="/paper/img/icons/success.png" width="15%" class="pb-3 d-none d-sm-inline">
                                <h5 class="text-center mb-3 d-block d-sm-none" style="text-align:justify; font-size: 15px"><strong>Revisa tu correo electrónico</strong></h5>
                                <h5 class="text-center mb-3 d-none d-sm-block" style="letter-spacing: 1px;"><strong>Revisa tu correo electrónico</strong></h5>
                                <p>Tu registro ha sido exitoso, Por favor revisa tu buzón de correo electrónico y sigue las instrucciones enviadas, el correo fue enviado a: <br><br> <strong>@{{user.email}}</strong>.</p>
                                <hr>
                                <div class="card-footer mt-1">
                                    <div style="font-size: 9px; color:grey" class="d-block d-sm-none">Gracias por elegir RHPlus <a href="/login" style="text-decoration: underline; font-weight: bold">inicia sesión</a> en nuestro sitio web.</div>
                                    <div style="font-size: 13px; color:grey" class="d-none d-sm-block">Gracias por elegir RHPlus <a href="/login" style="text-decoration: underline; font-weight: bold">inicia sesión</a> en nuestro sitio web.</div>
                                </div>
                            </div>
                        </div>
                        </Transition>
                    </div>
                </div>
            </div>
        </div>
    </div> 
    </div>
     @endsection
     
     @push('scripts')
     <script>
        
    </script>

     <script>
        var app = new Vue({
            el: '#vue-app',
            data: {
                folio: '',
                terms: false,
                step: 1,
                nuevo: {
                    nombre: '',
                    ap_paterno: '',
                    ap_materno: '',
                    celular: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                },
                user: {
                    email: ''
                }
            },
            methods:{
                isEmail: function(){
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    return emailPattern.test(this.nuevo.email);
                },
                isPhone: function()  {
                    this.nuevo.celular = this.nuevo.celular.replace(/\D/g, '').slice(0, 10);
                },
                delay: function(time) {
                    return new Promise(resolve => setTimeout(resolve, time));
                },
                enviar: function(){
                    let t = this;
                     if(!t.isEmail()){
                        swal('Verifique su correo electronico', 'Verifique que el correo electronico que proporciono sea valido.', 'info');
                        return;
                    }
                    if(t.nuevo.nombre == '' || t.nuevo.ap_paterno == '' || t.nuevo.ap_materno == '' || t.nuevo.celular == '' || t.nuevo.email == '')
                    {
                        swal('Campos obligatorios', 'Todos los campos son oblgatorios, favor de rellenarlos antes de continuar.', 'info');
                        return;
                    }
                    if(!t.terms){
                        swal('Terminos y Condiciones', 'Para continuar es necesario que acepte los terminos y condiciones y la politica de privacidad.', 'info');
                        return;
                    }
                    if(t.nuevo.password.length <= 5){
                        swal('Verifique su contraseña', 'La contraseña debe contener al menos 6 caracteres.', 'info');
                        return;
                    }
                    if(t.nuevo.password != t.nuevo.password_confirmation){
                        swal('Verifique su contraseña', 'La contraseñas deben coincidir.', 'info');
                        return;
                    }
                    t.step = 0;
                    axios.post('/api/usuario/registro', t.nuevo).then(response => {
                        if(response.data.success){
                            t.user = response.data.user
                            t.step = 2
                        }else{
                            swal(response.data.title, response.data.message, 'error');
                            t.step = 1;
                        }
                    }).catch(e => {
                        console.log(e);
                    });
                },
            },
            mounted: function () {
                this.$nextTick(function () {  
                    let t = this;
                    t.step = 1;
                })
            }
        });
    </script>

@endpush
