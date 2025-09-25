@extends('layouts.app', [
'class' => '',
'elementActive' => 'dashboard'
])

<style></style>

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
    <div class="col-md-12" v-show="cargando">
        <div style="margin-top: 200px; max-width: 100% !important; margin-bottom: auto; text-align:center; letter-spacing: 2px">
            <h5 class="mb-5">CARGANDO...</h5>
            <div class="loader"></div>
        </div>
    </div>
    <div class="card shadow col-md-12" v-cloak v-show="!cargando">
        <div class="card-header border-0">
            <div class="row align-items-center">
                <div class="col-md-10   mb-0">
                    <h3 class="bold my-0 py-1" style="letter-spacing: 1px;">ADMINISTRAR MEDIDAS</h3>
                </div>
                <div class="form-group col-md-2 col-xs-12 mt-4">
                    <button class="btn btn-normal col-md-12" @click="abrirModal('agregar')"><i class="fa fa-circle-plus"></i> NUEVA MEDIDA</button>
                </div>

                <div class="col-md-12 table-responsive">
                    <table class="table align-items-center" id="tabla">
                        <thead class="thead-light">
                            <tr>
                                <th style="text-transform: none !important" scope="col">Nombre de medida</th>
                                <th style="text-transform: none !important" scope="col">Abreviatura</th>
                                <th style="text-transform: none !important" scope="col" class="no-sort">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="m in medidas" :key="m.id">
                                <td style="width: 30%">@{{m.nombre}}</td>
                                <td style="width: 20%">@{{m.abreviatura}}</td>
                                <td style="width: 20%">
                                    <div class="btn-group" style="border: 2px solid #121935; border-radius: 10px !important">
                                        <button class="btn btn-sm btn-link actions" @click="abrirModal('editar', m.id , m.nombre, m.abreviatura)" data-toggle="tooltip" data-placement="bottom" title="Editar información">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-link actions" @click="eliminarMedida(m.id)" data-toggle="tooltip" data-placement="bottom" title="Eliminar">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer py-4"></div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalMedida" tabindex="-1" aria-labelledby="modalMedidaLabel" aria-hidden="true">
        <div class="modal-dialog" style="min-width: 30%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="modalMedidaLabel">@{{modalEdicion ? "Editar Medida" : "Crear Medida"}} </h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row ">
                        <div class="form-group col-md-12">
                            <label class="bold">Nombre de la medida<span style="color: red">*</span></label>
                            <input placeholder="Nombre de la medida..." class=" form-control" type="text" v-model="medida.nombre" />
                        </div>
                        <div class="form-group col-md-12">
                            <label class="bold">Abreviatura <span style="color: red">*</span></label>
                            <input placeholder="Abreaviatura de la medida..." class=" form-control" type="text" v-model="medida.abreviatura" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer text-right">
                    <button class="btn btn-secondary" v-if="!loading_button && !modalEdicion" type="button" @click="nuevaMedida()"><i class="fa fa-save"></i>Guardar</button>

                    <button class="btn btn-secondary" v-if="!loading_button && modalEdicion" type="button" @click="editarMedida(medida.id)"><i class="fa fa-save"></i>Actualizar</button>

                    <button class="btn btn-secondary" type="button" disabled v-if="loading_button && modalEdicion"><i class="fa fa-spinner spin"></i> CARGANDO...</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

{{-- --}}

@push('scripts')
<script>
    var app = new Vue({
        el: '#vue-app',
        data: {
            loading_button: false,
            modalEdicion: false,
            cargando: true,
            medidas: [],
            medida: {
                id:'',
                nombre: '',
                abreviatura: '',
            },
        },
        methods: {
            async fetchMedidas() {
                let t = this;
                t.cargando = true;
                try {
                    const response = await axios.get('/api/unidad-medida');
                    this.medidas = response.data.medidas;
                } catch (e) {
                    console.error('Error fetching medidas:', e);
                } finally {
                    t.cargando = false;
                }
            },
            async nuevaMedida() {
                let t = this;
                t.cargando = true;
                t.loading_button = true;
                if (t.medida.nombre == '' || t.medida.abreviatura == '') {
                    swal('Campos obligatorios', 'Los campos marcados con asterisco son obligatorios.', 'info');
                    return;
                }
                try {
                    const response = await axios.post('/api/nueva-unidad-medida', t.medida);
                    if (response.data.success) {
                        swal('Éxito', 'La medida ha sido creada exitosamente.', 'success');
                        $('#modalMedida').modal('hide');
                        t.fetchMedidas();
                        t.medida = {
                            id:'',
                            nombre: '',
                            abreviatura: ''
                        };
                    } else {
                        console.error('Error agregando medida:', response.data.message);
                    }
                } catch (e) {
                    console.error('Error agregando medida:', e);
                } finally {
                    t.cargando = false;
                    t.loading_button = false;
                }
            },
            async editarMedida(medidaId) {
                let t = this;      
                t.cargando = true;    
                t.loading_button = true;                           
                try {
                    const response = await axios.put(`/api/editar-unidad-medida/${medidaId}`, t.medida);                
                    if(response.data.success){
                        swal('Éxito', 'La medida ha sido editada exitosamente.', 'success');
                        $('#modalMedida').modal('hide');
                        t.fetchMedidas();
                        t.medida = {
                            id:'',
                            nombre: '',
                            abreviatura: ''
                        };
                    }else {
                        console.error('Error editando medida:', response.data.message);
                    }
                } catch (e) {
                    console.error('Error agregando medida:', e);
                } finally {
                    t.cargando = false;
                    t.loading_button = false;
                }
            },
            async eliminarMedida(medidaId) {
                let t = this;
                swal({
                    title: "¿Eliminar medida?",
                    text: "Una vez eliminada, no podrás recuperar esta medida.",
                    icon: "warning",
                    buttons: ["Cancelar", "Eliminar"],
                    dangerMode: true,
                }).then(async (willDelete) => {
                    if (willDelete) {
                        try {
                            const response = await axios.delete(`/api/eliminar-unidad-medida/${medidaId}`);
                            if (response.data.success) {
                                swal('Éxito', 'La medida ha sido eliminada exitosamente.', 'success');
                                t.fetchMedidas();
                            } else {
                                swal('Error', response.data.message || 'No se pudo eliminar la medida.', 'error');
                            }
                        } catch (e) {
                            console.error('Error eliminando medida:', e);
                            swal('Error', 'Ocurrió un error al eliminar la medida.', 'error');
                        }
                    }
                });
            },
            abrirModal(modo, id,  nombre, abreviatura) {
                let t = this;            
                if (modo == 'agregar') {                    
                    t.modalEdicion = false;
                    t.medida = {
                        id:'',
                        nombre: '',
                        abreviatura: '',
                    };
                } else if (modo == 'editar') {                    
                    t.modalEdicion = true;
                    t.medida.id = id;
                    t.medida.nombre = nombre;
                    t.medida.abreviatura = abreviatura;                    
                }
                $('#modalMedida').modal();

            },
        },
        mounted() {
            let t = this;
            t.fetchMedidas();
        }
    })
</script>
@endpush