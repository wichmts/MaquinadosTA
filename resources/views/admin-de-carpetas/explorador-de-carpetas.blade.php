@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'dashboard'
])

@section('styles')
<link rel="stylesheet" href="{{ asset('paper/css/paper-dashboard-responsivo.css') }}?v={{ time() }}">
@endsection

<style>

</style>

@section('content')


<div class="container" id="vue-app">
    <div class="wrapper " v-cloak v-show="!cargandoMenu">
        <h2>Sistema de carpetas</h2>
        <ul>
            <!-- Niveles dinámicos -->
            <li v-for="anio in anios" :key="anio.id">
                <div @click="toggleAnio(anio)" style="cursor: pointer;">
                    <strong>@{{ anio.nombre }}</strong>
                    <span v-if="anio.open">[-]</span>
                    <span v-else>[+]</span>
                </div>

                <ul v-show="anio.open">
                    <li v-for="cliente in anio.clientes" :key="cliente.id">
                        <div @click="toggleCliente(anio, cliente)" style="cursor: pointer; margin-left: 15px;">
                            @{{ cliente.nombre }}
                            <span v-if="cliente.open">[-]</span>
                            <span v-else>[+]</span>
                        </div>

                        <ul v-show="cliente.open">
                            <li v-for="proyecto in cliente.proyectos" :key="proyecto.id" style="margin-left: 30px;">
                                @{{ proyecto.nombre }}
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>
    </div>

</div>
@endsection

@push('scripts')
<script type="text/javascript">
    Vue.component('v-select', VueSelect.VueSelect);

    const appCarpetas = new Vue({
        el: '#vue-app',
        data: {
            anios: [],
            cargandoMenu: false,
        },
        mounted: async function() {
            this.fetchAnios();
        },
        methods: {
            // Traer años
            async fetchAnios() {
                this.cargandoMenu = true;
                try {
                    const response = await axios.get('/api/anios');
                    this.anios = response.data.anios.map(a => ({
                        ...a,
                        open: false,
                        clientes: []
                    }));
                } catch (error) {
                    console.error('Error fetching años:', error);
                } finally {
                    this.cargandoMenu = false;
                }
            },

            // Expand/collapse año y cargar clientes
            async toggleAnio(anio) {
                anio.open = !anio.open;
                if (anio.open && anio.clientes.length === 0) {
                    await this.fetchClientes(anio);
                }
            },

            async fetchClientes(anio) {
                try {
                    const response = await axios.get(`/api/anios/${anio.id}/clientes`);
                    anio.clientes = response.data.clientes.map(c => ({
                        ...c,
                        open: false,
                        proyectos: []
                    }));
                } catch (error) {
                    console.error('Error fetching clientes:', error);
                }
            },

            // Expand/collapse cliente y cargar proyectos
            async toggleCliente(anio, cliente) {
                cliente.open = !cliente.open;
                if (cliente.open && cliente.proyectos.length === 0) {
                    await this.fetchProyectos(cliente);
                }
            },

            async fetchProyectos(cliente) {
                try {
                    const response = await axios.get(`/api/clientes/${cliente.id}/proyectos`);
                    cliente.proyectos = response.data.proyectos;
                } catch (error) {
                    console.error('Error fetching proyectos:', error);
                }
            },
        },
    });
</script>
@endpush