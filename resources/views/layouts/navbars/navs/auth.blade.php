<style>
    /* Estilos existentes... */
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
        border-right: 1px solid #f1f1f1;
    }

    .menu-link:hover{
        color: black !important;
        text-decoration: underline !important;
    }

.atendido{
        background-color: #d4edda !important;
    }

    .no-atendido{
        background-color: #f8d7da !important;
    }

    .noti:hover{
        color: black !important;
        text-decoration: underline !important;
    }

    .dropdown-item.no-atendido:hover, .dropdown-item.atendido:hover {
    color: black !important;
    text-decoration: underline !important;
    }

    .navbar11 {
        position: relative;
        top: 0;
        width: 100%;
        z-index: 1050;
        height: auto; /* Cambiado a auto para adaptarse al contenido */
        min-height: 100px;
    }
    
    .toggler-principal {
        border: 1px solid #333 !important;
        font-size: 10px !important;
    }

    
    /* NUEVOS ESTILOS PARA EL MENÚ RESPONSIVE */
    .navbar-nav.menu-principal {
        display: flex;
        flex-wrap: wrap; /* Permite que los elementos pasen a la siguiente línea */
        justify-content: flex-start;
        width: 100%;
        margin: 0;
        padding: 0;
    }
    
    .nav-item {
        white-space: nowrap; /* Evita que el texto se divida en varias líneas */
    }
    
    /* Ajustes para dispositivos móviles */
    @media (max-width: 1200px) {
        .navbar-collapse {
            max-height: 70vh;
            overflow-y: auto;
        }
        
        .menu-link {
            border-right: none;
            border-bottom: 1px solid #e2e2e2;
            padding: 10px 15px;
        }
        
        .navbar-nav.menu-principal {
            flex-direction: column;
        }
    }
    
    /* Para pantallas más grandes pero con muchos elementos */
    @media (min-width: 1201px) {
        .navbar-nav.menu-principal {
            flex-direction: row;
        }
        .navbar-brand{
            width: 250px !important;
        }
    }
</style>

<div id="vue-app2">
    <nav class="navbar navbar-expand-xl navbar-light bg-light mb-0 navbar11" style="background-color: #f1f1f1 !important" v-cloak>
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <img src="{{ \App\Helpers\SystemHelper::getLogo() }}" width="120px" class="pl-2 pr-4 py-0" height="auto">
            </a>

            <button class="navbar-toggler toggler-principal" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
                <i class="text-dark fa fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse ml-4" style="background-color: #f1f1f1 !important" id="navigation">     
                <!-- Cambiamos la clase a menu-principal para aplicar los nuevos estilos -->
                <ul class="navbar-nav mr-auto text-center menu-principal align-items-center">
                    @php
                        $addedRoutes = [];
                        function addMenuItem($route, $label, $roles, &$addedRoutes) {
                            if (!in_array($route, $addedRoutes) && auth()->user()->hasAnyRole($roles)) {
                                echo '<li class="nav-item"><a class="nav-link menu-link" href="/' . $route . '">' . $label . '</a></li>';
                                $addedRoutes[] = $route;
                            }
                        }
                    @endphp
                    
                    {{-- DIRECCION --}}
                    @php
                        addMenuItem('usuario', 'USUARIOS', ['DIRECCION'], $addedRoutes);
                        addMenuItem('maquina', 'MAQUINAS', ['DIRECCION'], $addedRoutes);
                        addMenuItem('tiempos-maquinas', 'TIEMPOS MAQUINA', ['DIRECCION'], $addedRoutes);
                        addMenuItem('tiempos-personal', 'TIEMPOS PERSONAL', ['DIRECCION'], $addedRoutes);
                        addMenuItem('visorGeneral', 'VISOR GENERAL', ['DIRECCION'], $addedRoutes);
                    @endphp

                    {{-- FINANZAS --}}
                    @php
                        addMenuItem('finanzas-py', 'FINANZAS PY', ['FINANZAS'], $addedRoutes);
                        addMenuItem('finanzas-hr', 'FINANZAS HR', ['FINANZAS'], $addedRoutes);
                        addMenuItem('costos-hora', 'COSTOS POR HORA', ['FINANZAS'], $addedRoutes);
                    @endphp

                    {{-- AUXILIAR DE DISEÑO --}}
                    @php
                        addMenuItem('carga-componentes', 'Carga de componentes', ['AUXILIAR DE DISEÑO'], $addedRoutes);
                        addMenuItem('visor-avance-hr', 'Visor de avance', ['AUXILIAR DE DISEÑO', 'DIRECCION', 'FINANZAS', 'JEFE DE AREA', 'PROYECTOS', 'MATRICERO', 'HERRAMENTALES'], $addedRoutes);
                    @endphp

                    {{-- JEFE DE AREA --}}
                    @php
                        addMenuItem('enrutador', 'Enrutador', ['JEFE DE AREA'], $addedRoutes);
                        addMenuItem('visor-programador', 'Programador', ['JEFE DE AREA', 'PROGRAMADOR'], $addedRoutes);
                        addMenuItem('visor-operador', 'Operador', ['JEFE DE AREA', 'OPERADOR'], $addedRoutes);
                        addMenuItem('visor-pruebas', 'Pruebas diseño', ['JEFE DE AREA', 'DISEÑO'], $addedRoutes);
                    @endphp

                    {{-- PROCESOS --}}
                    @php
                        addMenuItem('pruebas-proceso', 'Pruebas proceso', ['PROCESOS'], $addedRoutes);
                    @endphp

                    {{-- PROGRAMADOR --}}
                    @php
                        addMenuItem('visor-programador', 'Programador', ['PROGRAMADOR'], $addedRoutes);
                    @endphp

                    {{-- OPERADOR --}}
                    @php
                        addMenuItem('visor-operador', 'Operador', ['OPERADOR'], $addedRoutes);
                    @endphp

                    {{-- MATRICERO --}}
                    @php
                        addMenuItem('matricero', 'Matricero', ['MATRICERO'], $addedRoutes);
                        addMenuItem('matricero/lista-componentes', 'Lista de componentes', ['MATRICERO'], $addedRoutes);
                    @endphp
                    
                    {{-- HERRAMENTALES --}}
                    @php 
                        addMenuItem('herramentales', 'Herramentales', ['PROCESOS', 'HERRAMENTALES'], $addedRoutes); 
                    @endphp
                    @php 
                        addMenuItem('matricero/lista-componentes', 'Lista de componentes', ['PROCESOS', 'HERRAMENTALES'], $addedRoutes); 
                    @endphp
                    
                    {{-- ALMACENISTA --}}
                    @php
                        addMenuItem('almacen-mp', 'AlmacÉn de MP', ['ALMACENISTA'], $addedRoutes);
                        addMenuItem('compra-componentes', 'Compra de componentes', ['ALMACENISTA'], $addedRoutes);
                        addMenuItem('componentes-reutilizables', 'Componentes reutilizables', ['ALMACENISTA'], $addedRoutes);
                        addMenuItem('corte', 'Corte', ['ALMACENISTA'], $addedRoutes);
                        addMenuItem('temple', 'Temple', ['ALMACENISTA'], $addedRoutes);
                    @endphp

                    {{-- SOLICITUD EXTERNA --}}
                    @php
                        addMenuItem('orden-trabajo', 'Orden de trabajo', ['SOLICITUD EXTERNA'], $addedRoutes);
                        addMenuItem('carga-afilados', 'Carga Afilados', ['SOLICITUD EXTERNA'], $addedRoutes);
                    @endphp

                    {{-- ADMINISTRADOR DE CARPETAS --}}
                    @php
                        addMenuItem('exploradorCarpetas', 'Explorador de Carpetas', ['ADMINISTRADOR DE CARPETAS'], $addedRoutes);
                    @endphp

                    {{-- CENTRO DE NOTIFICACIONES --}}
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="/centro-notificaciones">Centro de notificaciones</a>
                    </li>
                </ul>


                <ul class="navbar-nav ml-auto text-center">
                    <li class="nav-item dropdown btn-rotate">
                        <a v-if="!hay_notificaciones" @click="toggleDropdown()" class="menu-link nav-link " style="text-transform: capitalize" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i  class="far fa-bell"></i> <small class="bold">Ultimas notificaciones&nbsp;</small>
                        </a>
                        <a v-else @click="toggleDropdown()"  class="menu-link nav-link  cursor-pointer" style="text-transform: capitalize" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-bell text-danger"></i> <small class="bold text-danger">Ultimas notificaciones&nbsp;</small>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <h6 class="dropdown-header">Ultimas notificaciones</h6>
                            <h6 v-if="notificaciones.length == 0" class="dropdown-header"><small>- Sin notificaciones para mostrar -</small></h6>
                            <a  v-for="n in notificaciones" @click="irNotificacion(n)" class="dropdown-item cursor-pointer noti" :class="n.atendida ? 'atendido' : 'no-atendido'" >@{{n.fecha}} @{{n.hora}} | @{{n.componente_id ? n.componente : n.herramental}} | @{{n.descripcion}}</a>
                            <a v-if="notificaciones.length > 0" class="dropdown-item text-center" href="/centro-notificaciones">Ver todas las notificaciones...</a>
                        </div>
                    </li>
                    <li class="nav-item btn-rotate">
                        <a class="nav-link menu-link " onclick="document.getElementById('formLogOut').submit();" style="text-transform: capitalize">
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
            hay_notificaciones: @json(auth()->user()->hay_notificaciones) ? true :  false,
        },
        methods:{
            toggleDropdown(){
                let t = this
                t.getNotificaciones();
                axios.put('/api/ver-notificaciones').then( response => {
                    t.hay_notificaciones = false;
                })
            },
            getNotificaciones(){
                axios.get(`/api/ultimas-notificaciones`).then(response => {
                    this.notificaciones = response.data.notificaciones;
                })
            },
            irNotificacion(notificacion){
                let roles = JSON.parse(notificacion.roles);

                if (roles.includes('OPERADOR')) {
                    window.location.href = `${notificacion.url_base}?maq=${notificacion.maquina_id}&co=${notificacion.componente_id}&fab=${notificacion.fabricacion_id}`;
                }else{
                    window.location.href = `${notificacion.url_base}?a=${notificacion.anio_id}&c=${notificacion.cliente_id}&p=${notificacion.proyecto_id}&h=${notificacion.herramental_id}&co=${notificacion.componente_id}`
                }                
            }
        },
        mounted() {
            this.getNotificaciones();
        },

    })
 </script>


        
@endpush