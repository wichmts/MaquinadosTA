@extends('layouts.app', [
    'class' => 'login-page',
    'backgroundImagePath' => 'img/bg/fabio-mangione.jpg'
])

<style>
    tr, th, td {
        padding: 5px !important;
    }
    [v-cloak] {
        display: none;
    }
</style>

@section('content')
  <div class="homepage">
        <div class="content" id="vue-app">
        <div class="container" v-cloak>
            <div class="col-lg-8 col-md-8 ml-auto mr-auto">
                <div class="card">
                    <div class="card-header text-center">
                        <img src="/paper/img/icons/success.png" width="70px" height="70px">
                        <h3 class="mt-1 mb-0 pb-0" style="letter-spacing: 2px; font-weight: bold">¡Tu cuenta ha sido verificada con exito!</h3>
                        <hr>
                    </div>
                    <div class="card-body py-0">
                        <div class="row">
                            <div class="col-xl-12" >
                                <h5>
                                    Ahora estás listo para comenzar a utilizar todos los servicios y características que ofrecemos. <br>
                                    Para iniciar sesión en tu cuenta, simplemente haz clic en el enlace de abajo <br>
                                    Una vez que inicies sesión, podrás:
                                </h5>

                                <ul>
                                    <li><h5>Programar tus citas médicas con facilidad.</h5></li>
                                    <li><h5>Acceder a tu historial de citas y recordatorios. </h5></li>
                                    <li><h5>Comunicarte con médicos y profesionales de la salud de manera segura. </h5></li>
                                    <li><h5>Obtener acceso a información de salud personalizada.   </h5></li>
                                </ul>

                                <h5>
                                    Gracias por confiar en nosotros para tu atención médica. Si tienes alguna pregunta o necesitas asistencia, no dudes en contactarnos a través de nuestro sitio web.<br>
                                    Estamos aquí para ayudarte en cada paso de tu viaje hacia una mejor salud. ¡Esperamos verte pronto en RHPlus!
                                </h5>
                            </div>                            
                        </div>

                    </div>
                    <hr>
                    <div class="card-footer text-center pt-0">
                        <div class="col-md-12 text-center">
                           <a class="btn" href="/login" style="letter-spacing:2px"><strong>Iniciar sesión</strong></a>
                        </div>
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
                
            },
            methods:{
                
            },
            mounted: function () {
                this.$nextTick(function () {  

                })
            }
        });

        $(document).ready(function() {
            demo.checkFullPageBackgroundImage();
        });
    </script>

    
@endpush
