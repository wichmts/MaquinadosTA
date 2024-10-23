@extends('layouts.app', [
    'class' => 'register-page',
    'backgroundImagePath' => 'img/bg/jan-sendereks.jpg'
])


@section('content')
    <style>
        .register-page {
            background-color: #004da7 !important;
        }
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
        @media (min-width: 1200px) {
            .container {
            max-width: 1280px !important;
            }
        }
    </style>
    <div class="homepage">
            <div class="content"  id="vue-app">
        <div class="container" style="max-width: ">
            <div class="row">
                <div class="col-md-6 d-none d-sm-block">
                    <div class="info-area info-horizontal">
                        <div>
                            <h4 class="mb-2 description font-weight-bold">Plan Básico
                                <span style="font-weight: normal"> - 699.00 MXN / mes</span><br><small style="font-weight: normal"> antes: <span style="text-decoration: line-through;"> 999.00 MXN</span></small> 
                            </h4> 
                        </div>
                        <div class="ml-2">
                            <h5 class="my-0 description">◦ Agenda Online</h5>
                            <h5 class="my-0 description">◦ Expediente electrónico</h5>
                            <h5 class="my-0 description">◦ Receta online</h5>
                            <h5 class="my-0 description">◦ Recordatorios Whatsapp</h5>
                            <h5 class="my-0 description">◦ Presencia en directorio nacional de especialistas</h5>
                        </div>
                    </div>
                    <div class="info-area info-horizontal mt-3">
                        <div>
                            <h4 class="mb-2 description font-weight-bold">Plan Intermedio
                                <span style="font-weight: normal"> - 899.00 MXN / mes</span><br><small style="font-weight: normal"> antes: <span style="text-decoration: line-through;"> 1199.00 MXN</span></small> 
                            </h4> 
                        </div>
                        <div class="ml-2">
                            <h5 class="my-0 description font-weight-bold">Todos los beneficios del plan básico más:</h5>
                            <h5 class="my-0 description">◦ Facturación</h5>
                            <h5 class="my-0 description">◦ Pago en linea a tus pacientes</h5>
                        </div>
                    </div>
                    <div class="info-area info-horizontal mt-3">
                        <div>
                            <h4 class="mb-2 description font-weight-bold">Plan Plus
                                <span style="font-weight: normal"> - 1199.00 MXN / mes</span><br><small style="font-weight: normal"> antes: <span style="text-decoration: line-through;"> 1499.00 MXN</span></small> 
                            </h4> 
                        </div>
                        <div class="ml-2">
                            <h5 class="my-0 description font-weight-bold">Todos los beneficios del plan intermedio más:</h5>
                            <h5 class="my-0 description">◦ Pago directo a tu equipo de trabajo y proveedores</h5>
                            <h5 class="my-0 description">◦ Terminal bancaria SIN COMISIÓN</h5>
                        </div>
                    </div>
                    <div class="my-4">
                        <p class="description text-justify">Para <strong>Plan Plus</strong> un asesor se comunicará contigo para brindarte un seguimiento personalizado en tu registro. Es importante destacar que el pago correspondiente se efectuará directamente con RHPlus una vez que hayas completado exitosamente todo el proceso de registro. </p>
                    </div>
                </div>
                <div class="col-md-6 mr-auto" v-cloak>
                    <div  class="card card-signup text-center" >
                        <div class="card-header" v-if="step != 5">
                            <h4 class="card-title p-0 m-0">{{ __('Registrarse como especialista ') }}</h4>
                            <h5 style="font-size: 15px !important; color: grey" class="card-title p-0 m-0">{{ __('Registrate y forma parte de RHPlus.') }}</h5>
                        </div>
                        <Transition name="fade" mode="out-in">
                            <div v-if="step == 0" key="step0">
                                <div class="card-body px-3">                                
                                    <div style="border-bottom: 15%;margin-top: 5%; text-align:center; letter-spacing: 1px">
                                        <h5 class="mb-5 d-inline d-sm-none" style="font-size: 15px">Espere un momento...</h5>
                                        <h5 csuss="mb-5 d-none d-sm-inline" style="font-size: 20px">Espere un momento...</h5>
                                        <div class="my-3 loader"></div>
                                    </div>                              
                                </div>
                            </div>
                            <div v-if="step == 1" key="step1">
                                <div class="card-body px-3">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="nc-icon nc-tap-01"></i>
                                            </span>
                                        </div>
                                        <select class="form-control" v-model="nuevo.suscripcion">
                                            <option v-for="p in planes" :value="p.value">@{{p.nombre}}</option>
                                        </select>
                                    </div>                             
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="nc-icon nc-single-02"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control" v-model="nuevo.nombre" placeholder="Nombre(s)" required autofocus>
                                    </div>
                                    <div class="row">
                                        <div class="input-group col-md-6">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="nc-icon nc-single-02"></i>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control" v-model="nuevo.ap_paterno" placeholder="Apellido paterno" required>
                                        </div>
                                        <div class="input-group col-md-6">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="nc-icon nc-single-02"></i>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control" v-model="nuevo.ap_materno" placeholder="Apellido materno" required>
                                        </div>
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
                                    
                                    <div class="input-group text-left" >
                                        <select id='selEstado' style='width: 100%' v-select2 v-model="nuevo.estado_id" @change="getCiudades">
                                            <option v-for="e in estados" :value='e.id'>@{{e.name}}</option> 
                                        </select>
                                    </div>
                                    <div class="input-group text-left" >
                                        <select id='selCiudad' style='width: 100%' v-select2 v-model="nuevo.ciudad_id">
                                            <option v-for="e in ciudades" :value='e.id'>@{{e.name}}</option> 
                                        </select>
                                    </div>
                                    <div class="input-group text-left" >
                                        <select id='selEspecialidad' style='width: 100%' v-select2 v-model="nuevo.especialidad_id">
                                            <option v-for="e in especialidades" :value='e.id'>@{{e.nombre}}</option> 
                                        </select>
                                    </div>                                
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="nc-icon nc-badge"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control" v-model="nuevo.cedula" placeholder="Cédula profesional" required >
                                    </div>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="nc-icon nc-badge"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control" v-model="nuevo.cedula_especialidad" placeholder="Cédula de especialidad" required >
                                    </div>
                                    <div class="">
                                        <span>¿Tienes un código de referencia? <span style="cursor: pointer; color:#17395d; text-decoration: underline" @click="mostrarReferencia = !mostrarReferencia">Ingrésalo aquí.</span></span>
                                    </div>
                                    <div class="row mt-2" v-show="mostrarReferencia">
                                        <div class="input-group col-md-9 my-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fa fa-link " style="font-size: .8rem !important;"></i>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control"  v-model="nuevo.referencia" placeholder="No. de referencia (opcional)">
                                        </div>
                                        <div class="col-md-3 my-md-0 pl-md-0">
                                            <button class="btn btn-normal my-0 btn-sm mt-1 w-100"  @click="validarReferencia" :disabled="nuevo.referencia == '' || nuevo.referencia.length != 9"><i class="fa fa-check-circle"></i> validar</button>
                                        </div>
                                    </div>
                                    <div class="form-check text-left">
                                        <label class="form-check-label">
                                            <input class="form-check-input" type="checkbox" v-model="terms">
                                            <span class="form-check-sign"></span>
                                            {{ __('Estoy de acuerdo con los ') }}
                                            <a href="/terminos-y-condiciones" target="_blank">{{ __('Términos y Condiciones') }}</a>.
                                        </label>
                                    </div>

                                    <div class="card-footer py-1">
                                        <button type="submit" class="btn btn-round col-xl-4" @click="cambiarStep(4)" >Continuar  </button>
                                    </div>                                
                                </div>
                            </div>
                        {{-- PAGO --}}
                            <div v-if="step == 2" key="step2">
                                <div class="card-body px-3 py-0"> 
                                    <form action="#" method="POST" id="payment-form">
                                        <input type="hidden" name="token_id" id="token_id">
                                        <input type="hidden" name="use_card_points" id="use_card_points" value="false">
                                        <div class="my-3">
                                            <h4 class="font-weight-bold py-3 my-1" style="background-color: #e9e9e9">Tarjeta de crédito o débito</h4>
                                            <div class="row my-3">
                                                <div class="col-xl-4" style="border-right: 1px solid #e9e9e9">
                                                    <div class="credit"><h5 class="my-2">Tarjetas de crédito</h5></div>
                                                    <img src="/paper/img/openpay/cards1.png" alt="">
                                                </div>
                                                <div class="col-xl-8">
                                                    <div class="debit"><h5 class="my-2">Tarjetas de débito</h5></div>
                                                    <img src="/paper/img/openpay/cards2.png" alt="">
                                                </div>
                                            </div>
                                            <div class="row text-left mt-3">
                                                <div class="form-group col-xl-6">
                                                    <h5 class="my-2">Nombre del titular</h5>
                                                    <input type="text" placeholder="Como aparece en la tarjeta" autocomplete="off" data-openpay-card="holder_name" class="form-control">
                                                </div>
                                                <div class="form-group col-xl-6">
                                                    <h5 class="my-2">Número de tarjeta</h5>
                                                    <input type="text" autocomplete="off" data-openpay-card="card_number" class="form-control" maxlength="16" style="font-weight: bold; letter-spacing:2px">
                                                </div>
                                            </div>
                                            <div class="row text-left mt-3">
                                                <div class="form-group col-xl-6">
                                                    <h5 class="my-2">Fecha de expiración</h5>
                                                    <div class="row">
                                                        <div class="col-xl-6">
                                                            <select type="text" data-openpay-card="expiration_month" class="form-control">
                                                                <option value="" disabled selected>Mes</option>
                                                                <option value="01">01</option>
                                                                <option value="02">02</option>
                                                                <option value="03">03</option>
                                                                <option value="04">04</option>
                                                                <option value="05">05</option>
                                                                <option value="06">06</option>
                                                                <option value="07">07</option>
                                                                <option value="08">08</option>
                                                                <option value="09">09</option>
                                                                <option value="10">10</option>
                                                                <option value="11">11</option>
                                                                <option value="12">12</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-xl-6">
                                                            <select type="text" data-openpay-card="expiration_year" class="form-control">
                                                                <option value="" disabled selected>Año</option>
                                                                <option v-for="year in futureYears" :key="year.value" :value="year.value">@{{ year.label }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group col-xl-6">
                                                    <h5 class="my-2">Código de seguridad</h5>
                                                    <div class="row">
                                                        <div class="col-xl-6">
                                                            <input type="text" placeholder="Max 4 dígitos" autocomplete="off" data-openpay-card="cvv2" class="form-control" maxlength="4">
                                                        </div>
                                                        <div class="col-xl-6 pt-2">
                                                            <img src="/paper/img/openpay/cvv.png" alt="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-5">
                                                <div class="col-xl-4" style="border-right: 1px solid #e9e9e9">
                                                    <small>Transacciones realizadas vía:</small><br>
                                                    <img src="/paper/img/openpay/openpay.png" alt="">
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="row">
                                                        <div class="col-xl-3 text-right">
                                                            <img src="/paper/img/openpay/security.png" alt="">
                                                        </div>
                                                        <div class="col-xl-9 text-left">
                                                            <small>Tus pagos se realizan de forma segura con encriptación de 256 bits</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-12 mt-3 text-center">
                                                    <button style="button" class="btn btn-round col-xl-4" @click="pagar"> PAGAR</button><br>
                                                    <small class="text-danger font-weight-bold" v-if="error_card">Error: @{{error_card}}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div v-if="step == 4" key="step4">
                                <div class="card-body px-3">                                
                                    <h5 class="text-center mb-3" style="text-align:justify; font-size: 15px">Confirma tu información</h5>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="table table-striped">
                                                <tbody>
                                                    <tr>
                                                        <th style="width: 35%" class="title-table">Plan:</th>
                                                        <td style="width: 65%"><span class="text-sm">@{{getPlan(nuevo.suscripcion)}}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <th style="width: 35%" class="title-table">Nombre:</th>
                                                        <td style="width: 65%"><span class="text-sm">@{{nuevo.nombre}} @{{nuevo.ap_paterno}} @{{nuevo.ap_materno}}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <th style="width: 35%" class="title-table">Celular:</th>
                                                        <td style="width: 65%"><span class="text-sm">@{{nuevo.celular}}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <th style="width: 35%" class="title-table">Email:</th>
                                                        <td style="width: 65%"><span class="text-sm">@{{nuevo.email}}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <th style="width: 35%" class="title-table">Estado:</th>
                                                        <td style="width: 65%"><span class="text-sm">@{{getEstado(nuevo.estado_id)}}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <th style="width: 35%" class="title-table">Ciudad:</th>
                                                        <td style="width: 65%"><span class="text-sm">@{{getCiudad(nuevo.ciudad_id)}}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <th style="width: 35%" class="title-table">Especialidad:</th>
                                                        <td style="width: 65%"><span class="text-sm">@{{getEspecialidad(nuevo.especialidad_id)}}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <th style="width: 35%" class="title-table">Cédula:</th>
                                                        <td style="width: 65%"><span class="text-sm">@{{nuevo.cedula}}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <th style="width: 35%" class="title-table">Cédula de especialidad:</th>
                                                        <td style="width: 65%"><span class="text-sm">@{{nuevo.cedula_especialidad}}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2" style="opacity: .7">Estás a un paso de unirte a RHPlus. ¡Bienvenido a bordo! <span v-if="nuevo.suscripcion == 3"><br> </span> </td>
                                                    </tr>
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>
                                    <div class="card-footer ml-auto text-center my-1 py-0">
                                        <button  class="btn btn-round col-xl-4" @click="(nuevo.suscripcion == 3) ? enviar() : cambiarStep(2)" > @{{nuevo.suscripcion == 3 ? 'Enviar Información' : 'Continuar al pago'}} </button>
                                    </div>  
                                </div>
                            </div>
                            <div v-if="step == 5" key="step5">
                                <div class="card-body px-3">                                
                                    <img src="/paper/img/icons/success.png" width="25%" class="d-inline d-sm-none pb-3">
                                    <img src="/paper/img/icons/success.png" width="15%" class="pb-3 d-none d-sm-inline">
                                    <h5 class="text-center mb-3 d-block d-sm-none" style="text-align:justify; font-size: 15px">¡Su solicitud ha sido enviada con exito!</h5>
                                    <h5 class="text-center mb-3 d-none d-sm-block" style="letter-spacing: 1px;">¡Su solicitud ha sido enviada con exito!</h5>
                                    <div style="font-size: 18px; color:#333"><strong>
                                        Folio: @{{folio.toString().padStart(7, '0')}}</strong>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <table class="table table-striped">
                                                <tbody>
                                                    <tr>
                                                        <th style="width: 35%" class="title-table">Nombre:</th>
                                                        <td style="width: 65%"><span class="text-sm">@{{nuevo.nombre}} @{{nuevo.ap_paterno}} @{{nuevo.ap_materno}}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <th style="width: 35%" class="title-table">Celular:</th>
                                                        <td style="width: 65%"><span class="text-sm">@{{nuevo.celular}}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <th style="width: 35%" class="title-table">Email:</th>
                                                        <td style="width: 65%"><span class="text-sm">@{{nuevo.email}}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <th style="width: 35%" class="title-table">Estado:</th>
                                                        <td style="width: 65%"><span class="text-sm">@{{getEstado(nuevo.estado_id)}}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <th style="width: 35%" class="title-table">Ciudad:</th>
                                                        <td style="width: 65%"><span class="text-sm">@{{getCiudad(nuevo.ciudad_id)}}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <th style="width: 35%" class="title-table">Especialidad:</th>
                                                        <td style="width: 65%"><span class="text-sm">@{{getEspecialidad(nuevo.especialidad_id)}}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <th style="width: 35%" class="title-table">Cédula:</th>
                                                        <td style="width: 65%"><span class="text-sm">@{{nuevo.cedula}}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <th style="width: 35%" class="title-table">Cédula de especialidad:</th>
                                                        <td style="width: 65%"><span class="text-sm">@{{nuevo.cedula_especialidad}}</span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>  
                                <div class="card-footer mt-1">
                                    <div style="font-size: 9px; color:grey" class="d-block d-sm-none">Gracias por elegir RHPlus <a href="/login" style="text-decoration: underline; font-weight: bold">inicia sesión</a> en nuestro sitio web para dar seguimiento a tu tramite.</div>
                                    <div style="font-size: 13px; color:grey" class="d-none d-sm-block">Gracias por elegir RHPlus <a href="/login" style="text-decoration: underline; font-weight: bold">inicia sesión</a> en nuestro sitio web para dar seguimiento a tu tramite.</div>
                                </div>
                            </div>
                            <div v-if="step == 6" key="step6">
                                <div class="card-body px-3">                                
                                    <img src="/paper/img/icons/success.png" width="25%" class="d-inline d-sm-none pb-3">
                                    <img src="/paper/img/icons/success.png" width="15%" class="pb-3 d-none d-sm-inline">
                                    <h5 class="text-center mb-3 d-block d-sm-none" style="text-align:justify; font-size: 15px">¡Tu registro se ha completado con exito!</h5>
                                    <h5 class="text-center mb-3 d-none d-sm-block" style="letter-spacing: 1px;">¡Tu registro se ha completado con exito!</h5>
                                </div>  
                                <div class="card-footer mt-1">
                                    <div style="font-size: 9px; color:grey" class="d-block d-sm-none">Gracias por elegir RHPlus <a href="/login" style="text-decoration: underline; font-weight: bold">inicia sesión</a> en nuestro sitio web para disfrutar de todo lo que tenemos para ofrecerte.</div>
                                    <div style="font-size: 13px; color:grey" class="d-none d-sm-block">Gracias por elegir RHPlus <a href="/login" style="text-decoration: underline; font-weight: bold">inicia sesión</a> en nuestro sitio web para disfrutar de todo lo que tenemos para ofrecerte.</div>
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
        var app = new Vue({
            el: '#vue-app',
            data: {
                error_card: null,
                mostrarReferencia: false,
                folio: '',
                terms: false,
                step: 1,
                especialidades: [],
                estados: [],
                ciudades: [],
                brokers: [],
                nuevo: {
                    nombre: '',
                    ap_paterno: '',
                    ap_materno: '',
                    celular: '',
                    email: '',
                    cedula: '',
                    cedula_especialidad: '',
                    especialidad_id: null,
                    estado_id: null,
                    broker_id: 1,
                    fecha: '', 
                    hora: '',
                    referencia: '',
                    suscripcion: 1,
                    token_id: null,
                    device_session_id: null,
                },
                horario:{
                    tipo_citas: 1,
                    tipo_horario: 1,
                    min_entrecitas: 20,
                    confirmacion_cita: 1,
                    envio_confirmacion: 4,
                    detalles:[
                        {
                            activo: true,
                            dia: 1,
                            dia_show: 'Lunes',
                            hora_apertura: '07:00',
                            hora_cierre: '14:00',
                            hora_apertura2: '16:00',
                            hora_cierre2: '20:00'
                        },
                        {
                            activo: true,
                            dia: 2,
                            dia_show: 'Martes',
                            hora_apertura: '07:00',
                            hora_cierre: '14:00',
                            hora_apertura2: '16:00',
                            hora_cierre2: '20:00'
                        },
                        {
                            activo: true,
                            dia: 3,
                            dia_show: 'Miercoles',
                            hora_apertura: '07:00',
                            hora_cierre: '14:00',
                            hora_apertura2: '16:00',
                            hora_cierre2: '20:00'
                        },
                        {
                            activo: true,
                            dia: 4,
                            dia_show: 'Jueves',
                            hora_apertura: '07:00',
                            hora_cierre: '14:00',
                            hora_apertura2: '16:00',
                            hora_cierre2: '20:00'
                        },
                        {
                            activo: true,
                            dia: 5,
                            dia_show: 'Viernes',
                            hora_apertura: '07:00',
                            hora_cierre: '14:00',
                            hora_apertura2: '16:00',
                            hora_cierre2: '20:00'
                        },
                        {
                            activo: false,
                            dia: 6,
                            dia_show: 'Sabado',
                            hora_apertura: '07:00',
                            hora_cierre: '14:00',
                            hora_apertura2: '16:00',
                            hora_cierre2: '20:00'
                        },
                        {
                            activo: false,
                            dia: 7,
                            dia_show: 'Domingo',
                            hora_apertura: '07:00',
                            hora_cierre: '14:00',
                            hora_apertura2: '16:00',
                            hora_cierre2: '20:00'
                        },
                    ]
                },
                errors: [
                    { codigo: '3001', descripcion: 'La tarjeta fue rechazada.'},
	                { codigo: '3002', descripcion: 'La tarjeta ha expirado.'},
	                { codigo: '3003', descripcion: 'La tarjeta no tiene fondos suficientes.'},
	                { codigo: '3004', descripcion: 'La tarjeta ha sido identificada como una tarjeta robada.'},
 	                { codigo: '3005', descripcion: 'La tarjeta ha sido rechazada por el sistema antifraudes. '}
                ],
                planes: [
                    {
                        nombre: 'Plan Básico - 699.00 MXN / mes',
                        openpay_id: "{{env('PLAN_BASICO')}}",
                        value: 1
                    },
                    {
                        nombre: 'Plan Intermedio - 899.00 MXN / mes',
                        openpay_id: "{{env('PLAN_INTERMEDIO')}}",
                        value: 2
                    },
                    {
                        nombre: 'Plan Plus - 1199.00 MXN / mes',
                        value: 3
                    },
                ]
            },
            computed: {
                futureYears() {
                    const currentYear = new Date().getFullYear();
                    const futureYearRange = 10;
                    const years = [];

                    for (let i = 0; i < futureYearRange; i++) {
                            const year = currentYear + i;
                            years.push({
                            label: year.toString(),
                            value: year.toString().slice(-2),
                        });
                    }
                    return years;
                },
            },
            methods:{
                getError: function(code){
                    let t = this;
                    let error = t.errors.find(obj => obj.codigo == code);
                    if(error)
                        return error.descripcion;
                    return code; 

                },
                isEmail: function(){
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    return emailPattern.test(this.nuevo.email);
                },
                isPhone: function()  {
                    this.nuevo.celular = this.nuevo.celular.replace(/\D/g, '').slice(0, 10);
                },
                validarReferencia: function(){
                    let t = this;
                    t.cargandoVerificacion = false;
                    axios.get('/api/validar-referencia/' + t.nuevo.referencia).then(response => {
                        if(response.data.success){
                            swal({
                                title: 'Validacion correcta',
                                text: `Oficina: ${response.data.broker}\nAgente: ${response.data.user}`,
                                icon: 'success',
                            })
                        }else{
                            swal({
                                title: 'Datos no encontrados',
                                text: `Verifique el numero de referencia e intentelo nuevamente.`,
                                icon: 'error',
                            })
                        }
                    });
                },
                getEspecialidad(id){
                    return this.especialidades.find(e => e.id == id).nombre;
                },
                getEstado(id){
                    return this.estados.find(e => e.id == id).name;
                },
                getCiudad(id){
                    return this.ciudades.find(e => e.id == id).name;
                },
                getPlan(value){
                    return this.planes.find(obj => obj.value == value).nombre
                },
                delay: function(time) {
                    return new Promise(resolve => setTimeout(resolve, time));
                },
                enviarSuscripcion: function(){
                     let t = this;
                    t.step = 0;
                    axios.post('/api/suscripcion', t.nuevo).then(response => {
                        if(response.data.success){
                            axios.post('/api/configurar/citas/' + response.data.user_id, t.horario).then(response1 => {
                                if(response1.data.success){
                                    t.step = 6;
                                }
                            }).catch(e => {
                                console.log(e);
                            });
                        }else{
                            swal('Lo sentimos', t.getError(response.data.message), 'error');
                            t.cambiarStep(1);
                        }
                    }).catch(e => {
                        console.log(e);
                        swal('Lo sentimos!', t.getError(e.data.message), 'error');
                        t.cambiarStep(1);
                    });
                },
                enviar: function(){
                    // CREAR LA SOLICITUD DEL MEDICO 
                    let t = this;
                    t.step = 0;
                    axios.post('/api/solicitud', t.nuevo).then(response => {
                        if(response.data.success){
                            t.folio = response.data.folio;
                            axios.post('/api/configurar/citas/' + response.data.user_id, t.horario).then(response1 => {
                                if(response1.data.success){
                                    t.step = 5;
                                }
                            }).catch(e => {
                                console.log(e);
                            });
                        }else{
                            swal(response.data.title, response.data.message, 'error');
                            t.getData();
                        }
                    }).catch(e => {
                        console.log(e);
                    });
                },
                cambiarStep:function(step){
                    let t = this;
                    switch(step){
                        case 2:
                            if(t.nuevo.suscripcion == 3)
                                t.cambiarStep(4);
                            else
                                t.step = step; 
                        break;
                        case 4:
                            if(!t.isEmail()){
                                swal('Verifique su correo electronico', 'Verifique que el correo electronico que proporciono sea valido.', 'info');
                                return;
                            }
                            if(t.nuevo.nombre == '' || t.nuevo.ap_paterno == '' || t.nuevo.ap_materno == '' || t.nuevo.celular == '' || t.nuevo.email == '' || t.nuevo.cedula == '' || t.nuevo.cedula_especialidad == '' || t.nuevo.estado_id == null || t.nuevo.especialidad_id == null)
                            {
                                swal('Campos obligatorios', 'Todos los campos son oblgatorios, favor de rellenarlos antes de continuar.', 'info');
                                return;
                            }
                            if(!t.terms){
                                swal('Terminos y Condiciones', 'Para continuar es necesario que acepte los terminos y condiciones.', 'info');
                                return;
                            }
                            if(t.nuevo.password.length <= 5){
                                swal('Verifique la contraseña', 'La contraseña debe contener al menos 6 caracteres.', 'info');
                                return;
                            }
                            t.step = step;
                        break;
                        default:
                            t.step = step;
                            Vue.nextTick(() => {
                                $('#selEspecialidad').select2({
                                    placeholder: "Especialidad"
                                });     
                                $('#selEstado').select2({
                                    placeholder: "Estado"
                                });   
                                $('#selCiudad').select2({
                                    placeholder: "Ciudad"
                                });           
                                $('#selOficina').select2({
                                    placeholder: "Oficina (opcional)"
                                });   
                            });
                        break;
                    }
                },
                getBrokers: function(){
                    let t =this;
                    axios.get('/api/web/brokers/' + t.nuevo.estado_id).then(function (response) {
                        t.brokers = response.data.brokers;
                         Vue.nextTick(() => {
                            $('#selOficina').select2({
                                placeholder: "Oficina (opcional)"
                            });    
                        });
                    }).catch(errors => {
                        console.log(errors);
                    });
                },
                getCiudades:function(){
                    let t = this;
                    axios.get('/api/cities/' + t.nuevo.estado_id).then(function(response){
                        t.ciudades = response.data.ciudades;
                    }).catch(errors => {
                        console.log(errors);
                    });
                },
                getData: function(){
                    let t = this;
                    t.step = 1;
                    let urls = [
                        '/api/especialidad',
                        '/api/states/Mexico'
                    ];
                    let requests = urls.map((url) => axios.get(url));
                    axios.all(requests).then(axios.spread((...responses) => {
                        t.especialidades = responses[0].data.especialidades;
                        t.estados = responses[1].data.estados;
                        Vue.nextTick(() => {
                            $('#selEspecialidad').select2({
                                placeholder: "Especialidad"
                            });     
                            $('#selEstado').select2({
                                placeholder: "Estado"
                            });   
                             $('#selCiudad').select2({
                                placeholder: "Ciudad"
                            });           
                             $('#selOficina').select2({
                                placeholder: "Oficina (opcional)"
                            });   
                            t.step = 1
                        });
                    })).catch(errors => {
                        console.log('Errors:', errors);
                    });
                },
                pagar: function(event){
                    let t = this;
                    event.preventDefault();
                    t.step = 0;
                    OpenPay.token.extractFormAndCreate('payment-form', t.tarjetaValida, t.tarjetaDeclinada);    
                },
                tarjetaValida: function(response){
                    let t = this;
                    t.nuevo.token_id = response.data.id;
                    t.enviarSuscripcion()

                },
                tarjetaDeclinada: function(response){
                    let t = this;
                    var descripcion = response.data.description != undefined ? response.data.description : response.message;
                    t.error_card = descripcion
                    t.cambiarStep(2);
                }
            },
            mounted: function () {
                this.$nextTick(function () {  
                    let t = this;
                    const urlParams = new URLSearchParams(window.location.search);
                    this.nuevo.suscripcion = urlParams.get('plan_id') ? urlParams.get('plan_id') : 1 ;
                    OpenPay.setId("{{env('OPENPAY_ID')}}");
                    OpenPay.setApiKey("{{env('OPENPAY_PK')}}");
                    OpenPay.setSandboxMode("{{env('OPENPAY_SANDBOX_MODE')}}");
                    t.nuevo.device_session_id = OpenPay.deviceData.setup("payment-form", "deviceIdHiddenFieldName");
                    console.log('DEVICE_ID',t.nuevo.device_session_id)

                    t.getData();    
                })
            }
        });
    </script>

@endpush
