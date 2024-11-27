
<style>
    .switch {
    position: relative;
    display: inline-block;
    width: 34px;
    height: 20px;
    margin-top: 5px
    }

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #1831497a;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 14px;
  width: 14px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .4s;
}

input:checked + .slider {
  background-color: #1831497a;
}

input:checked + .slider:before {
  transform: translateX(14px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 20px;
}

.slider.round:before {
  border-radius: 50%;
}
[v-cloak]{
    display: none;
}

.menu-link{
    padding-right: 20px !important;
    border-right: 1px solid #e2e2e2;
}

.menu-link:hover{
    color: black !important;
    text-decoration: underline !important;
    /* text-decoration-color: #c3d312 !important; */
}

</style>
<div  id="vue-app2" >
<nav class="navbar navbar-expand-lg navbar-light bg-light" style="background-color: #f1f1f1 !important" v-cloak>
    <div class="container-fluid">
        <a class="navbar-brand">
            <img src="{{ \App\Helpers\SystemHelper::getLogo() }}" width="130" class="pl-2 pr-4 py-0" height="auto">
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
        </button>

        <div class="collapse navbar-collapse ml-4" id="navigation" >     
            @if(auth()->user()->roles()->first()->name == 'ADMINISTRADOR')
            <ul class="navbar-nav mr-auto text-center">
                <li class="nav-item" >
                    <a class="nav-link menu-link" href="/usuario">USUARIOS</a>
                </li>
            </ul>
            @endif       
            @if(auth()->user()->roles()->first()->name == 'AUXILIAR DE DISEÑO')
            <ul class="navbar-nav mr-auto text-center">
                <li class="nav-item" >
                    <a class="nav-link menu-link" href="/carga-componentes">Carga de componentes</a>
                </li>
                <li class="nav-item" >
                    <a class="nav-link menu-link" href="">Visor de componentes externos</a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link menu-link" href="">Visor de avance</a>
                </li>
                <li class="nav-item" >
                    <a class="nav-link menu-link" href="">Orden de trabajo</a>
                </li>
                <li class="nav-item" >
                    <a class="nav-link menu-link" href="">Modificaciones de componentes</a>
                </li>
                <li class="nav-item" >
                    <a class="nav-link menu-link" href="/centro-notificaciones">Centro de notificaciones</a>
                </li>
            </ul>
            @endif
            @if(auth()->user()->roles()->first()->name == 'JEFE DE AREA')
            <ul class="navbar-nav mr-auto text-center">
                <li class="nav-item" >
                    <a class="nav-link menu-link" href="/enrutador">Enrutador</a>
                </li>
                <li class="nav-item" >
                    <a class="nav-link menu-link" href="/compra-componentes">Programador</a>
                </li>
                <li class="nav-item" >
                    <a class="nav-link menu-link" href="/compra-componentes">Operador</a>
                </li>
                <li class="nav-item" >
                    <a class="nav-link menu-link" href="/compra-componentes">Visor pruebas</a>
                </li>
                <li class="nav-item" >
                    <a class="nav-link menu-link" href="/compra-componentes">Visor Avance</a>
                </li>
                <li class="nav-item" >
                    <a class="nav-link menu-link" href="/compra-componentes">Visor Avance componente</a>
                </li>
                
                <li class="nav-item" >
                    <a class="nav-link menu-link" href="/centro-notificaciones">Centro de notificaciones</a>
                </li>
            </ul>
            @endif
            @if(auth()->user()->roles()->first()->name == 'ALMACENISTA')
            <ul class="navbar-nav mr-auto text-center">
                <li class="nav-item" >
                    <a class="nav-link menu-link" href="/compra-componentes">Compra de componentes</a>
                </li>
                <li class="nav-item" >
                    <a class="nav-link menu-link" href="/almacen-mp">AlmacÉn de MP</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="/corte">Corte</a>
                </li>
                <li class="nav-item" >
                    <a class="nav-link menu-link" href="">Temple</a>
                </li>
                <li class="nav-item" >
                    <a class="nav-link menu-link" href="/centro-notificaciones">Centro de notificaciones</a>
                </li>
            </ul>
            @endif
            <ul class="navbar-nav ml-auto text-center">
                <li class="nav-item dropdown btn-rotate">
                    <a class=" cursor-pointer nav-link dropdown-toggle cursor-pointer" style="text-transform: capitalize" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-bell"></i> <small class="bold">Ultimas notificaciones</small>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <h6 class="dropdown-header">Ultimas notificaciones</h6>
                        <h6 v-if="notificaciones.length == 0" class="dropdown-header"><small>- Sin notificaciones para mostrar -</small></h6>
                        <a v-for="n in notificaciones" @click="irNotificacion(n)" class="dropdown-item cursor-pointer" >@{{n.fecha}} @{{n.hora}} | @{{n.componente_id ? n.componente : n.herramental}} | @{{n.descripcion}}</a>
                        <a v-if="notificaciones.length > 0" class="dropdown-item text-center" href="/centro-notificaciones">Ver todas las notificaciones...</a>
                    </div>
                </li>
                <li class="nav-item btn-rotate">
                    <a class="nav-link cursor-pointer " onclick="document.getElementById('formLogOut').submit();" style="text-transform: capitalize">
                        <i class="fa fa-sign-out"></i> <small class="bold">{{ __('system.logout') }}</small>
                    </a>
                </li>
                <form class="dropdown-item" action="{{ route('logout') }}" id="formLogOut" method="POST" style="display: none;">
                    @csrf
                </form>
            </ul>
        </div>
    </div>
</nav>

</div>


@push('scripts')

    <script type="text/javascript">
        Vue.component('v-select', VueSelect.VueSelect)
        
        var app = new Vue({
        el: '#vue-app2',
        data: {
            notificaciones: [],
        },
        methods:{
            getNotificaciones(){
                axios.get(`api/ultimas-notificaciones`).then(response => {
                    this.notificaciones = response.data.notificaciones;
                })
            },
            irNotificacion(notificacion){
                window.location.href = `${notificacion.url_base}?a=${notificacion.anio_id}&c=${notificacion.cliente_id}&p=${notificacion.proyecto_id}&h=${notificacion.herramental_id}&c=${notificacion.componente_id}`
            }
        },
        mounted() {
            this.getNotificaciones();
        },

    })
 </script>


        
@endpush