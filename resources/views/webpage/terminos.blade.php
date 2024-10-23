@extends('layouts.app', [
    'class' => 'login-page',
    'backgroundImagePath' => 'img/bg/fabio-mangione.jpg'
])

@section('content')
    <div class="homepage">
        <div class="content">
        <div class="container">
            <div class="col-lg-12 ml-auto mr-auto" style="margin-top: 120px">
                <div class="card card-login">
                    <div class="card-header text-center">
                        <h3>Términos y Condiciones</h3>
                    </div>
                    <div class="card-body" style="height: 40vh; overflow-y: scroll;">
                        <p>
                            
                        </p>
                    </div>
                    <div class="text-center card-footer">
                        <button class="btn" id="cerrarPestana" style="letter-spacing: 2px"> ACEPTAR </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('scripts')

    <script>
        $(document).ready(function() {
            document.getElementById("cerrarPestana").addEventListener("click", cerrarPestana);
            
            demo.checkFullPageBackgroundImage();
            
        });
         function cerrarPestana() {
            window.close();
        }

    </script>
@endpush
