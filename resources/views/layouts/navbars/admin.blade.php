<style media="screen">
  .sidebar-mini-icon{
      font-size: 17px !important;
      padding-top: 4px
  }



</style>
  {{-- <div class="sidebar" data-color="white" data-active-color="danger" style="border-radius: 12px; box-shadow: 3px 10px 10px 4px rgba(0, 0, 0, 0.15);">
      <div class="logo py-3">
          <a href="/" class="simple-text logo-normal" style="text-align:center; opacity:1 !important">
            <img src="{{ \App\Helpers\SystemHelper::getLogo() }}" width="60%" height="auto">
          </a>
      </div>
      <div class="sidebar-wrapper">
        @if(auth()->user()->roles()->first()->name == 'ADMINISTRADOR')
          <ul class="nav">
            <li class="mt-3">
              <a style="font-size: 17px; cursor: default">
                <p><span> {{ __('system.sales') }} </span>
                </p>
              </a>
              <hr style="background-color: white !important; opacity: .5; margin-left: 20px; margin-right: 20px; margin-top: 0px; margin-bottom: 0px; ">
              <div class="" id="catalogo">
                <ul class="nav mt-3" style="margin-left: 11px !important">
                  <li id="prospecto">
                    <a href="/prospecto">
                      <i class="nc-icon"><img width="90%" src="{{ asset('paper/img/icons/prospecto.png') }}"></i>
                      <p  class="" >{{ __('system.prospects') }}</p>
                    </a>
                  </li>
                </ul>
              </div>
            </li>

            <li class="mt-3">
              <a style="font-size: 17px; cursor: default">
                <p><span> {{ __('system.operations') }}</span>
                </p>
              </a>
              <hr style="background-color: white !important; opacity: .5; margin-left: 20px; margin-right: 20px; margin-top: 0px; margin-bottom: 0px; ">
              <div class="" id="catalogo">
                <ul class="nav mt-3" style="margin-left: 11px !important">
                  <li id="solicitud-servicio">
                    <a href="/solicitud-servicio">
                      <i class="nc-icon"><img width="90%" src="{{ asset('paper/img/icons/solicitud-de-cotizacion.png') }}"></i>
                      <p  class="" >{{ __('system.quotations') }}</p>
                    </a>
                  </li>
                  <li id="embarque">
                    <a href="/embarque">
                      <i class="nc-icon"><img width="90%" src="{{ asset('paper/img/icons/investigacion.png') }}"></i>
                      <p  class="" >{{ __('system.shipments') }}  </p>
                    </a>
                  </li>
                  <li id="cliente">
                    <a href="/cliente">
                      <i class="nc-icon"><img width="90%" src="{{ asset('paper/img/icons/clasificacion.png') }}"></i>
                      <p  class="" >{{ __('system.customers') }} </p>
                    </a>
                  </li>
                  <li id="proveedor">
                    <a href="/proveedor">
                      <i class="nc-icon"><img width="90%" src="{{ asset('paper/img/icons/empresa.png') }}"></i>
                      <p  class="" >{{ __('system.suppliers') }}</p>
                    </a>
                  </li>      
                </ul>
              </div>
            </li>

            <li class="mt-3">
              <a style="font-size: 17px; cursor: default">
                <p><span> Multi-Channel Logistics </span></p>
              </a>
              <hr style="background-color: white !important; opacity: .5; margin-left: 20px; margin-right: 20px; margin-top: 0px; margin-bottom: 0px; ">
              <div class="" id="catalogo">
                <ul class="nav mt-3" style="margin-left: 11px !important">
                  <li id="vehiculo">
                    <a href="/vehiculo">
                    <i class="nc-icon"><img width="90%" src="{{ asset('paper/img/icons/remolque.png') }}"></i>
                    <p  class="" >{{ __('system.company_vehicles') }} </p>
                  </a>
                  </li>
                  <li id="conductor">
                    <a href="/conductor">
                      <i class="nc-icon"><img width="90%" src="{{ asset('paper/img/icons/conductor.png') }}"></i>
                      <p  class="" >{{ __('system.drivers') }}</p>
                    </a>
                  </li>
                  <li id="configuracion">
                    <a href="/configuracion">
                      <i class="nc-icon"><img width="90%" src="{{ asset('paper/img/icons/configuraciones.png') }}"></i>
                      <p  class="" >{{ __('system.settings') }}</p>
                    </a>
                  </li>
                  <li id="usuario">
                    <a href="/usuario">
                      <i class="nc-icon"><img width="27px" src="{{ asset('paper/img/icons/iniciar-sesion.png') }}"></i>
                      <p  class="" >{{ __('system.users_and_sales_representatives') }}</p>
                    </a>
                  </li>              
                </ul>
              </div>
            </li>
          </ul>
        @endif
        @if(auth()->user()->roles()->first()->name == 'AUXILIAR DE DISEÑO')
            
        <div class="" id="catalogo">

          <ul class="nav mt-3" style="margin-left: 11px !important">

            <li id="carpeta">
              <a href="/carpeta">
                <i class="nc-icon"><img width="90%" src="{{ asset('paper/img/icons/calendario.png') }}"></i>
                <p  class="bold"> 2024 &nbsp;&nbsp;► </p>
              </a>
            </li>

            <li id="carpeta">
              <a href="/carpeta">
                <i class="nc-icon"><img width="90%" src="{{ asset('paper/img/icons/carpetas.png') }}"></i>
                <p  class="bold"> Cliente 001 &nbsp;&nbsp;► </p>
              </a>
            </li>

            <li id="componente">
              <a href="/componente">
                <i class="nc-icon"><img width="90%" src="{{ asset('paper/img/icons/componente.png') }}"></i>
                <p  class="bold"> HR100-01 &nbsp;&nbsp;► </p>
              </a>
            </li>

          </ul>
        </div>
        @endif

      </div>
  </div> --}}


<script>
document.addEventListener("DOMContentLoaded", function() {
    var urlActual = window.location.href;
    var match = urlActual.match(/\/\/[^\/]+\/([^\/]+)/);
    var palabraClave = match ? match[1] : null;

    if (palabraClave) {
        var elemento = document.getElementById(palabraClave);
        if (elemento) {
            elemento.style.backgroundColor = "rgb(1100 33 33)";
            elemento.style.marginRight = "11px";
            elemento.style.marginLeft = "-10px";
            elemento.style.paddingLeft = "10px";
            elemento.style.borderRadius = "0px 30px 30px 0px";
        } else {
            console.log("No se encontró ningún elemento con el ID:", palabraClave);
        }
    } else {
        console.log("No se encontró ninguna palabra clave en la URL.");
    }
});


</script>
