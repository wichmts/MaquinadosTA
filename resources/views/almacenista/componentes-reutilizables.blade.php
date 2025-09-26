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
        box-shadow:none !important;
     }
    .form-group{
    }

     input[type=checkbox], input[type=radio]{
        width: 17px !important;
        height: 17px !important;
     }
     input[type="file"] {
        width: 150px; /* Ajusta el valor según lo que necesites */
        max-width: 100%; /* Para asegurarte de que no se salga del contenedor */
    }

    .custom-file-input {
        display: none;
    }

    .custom-file-label {
        display: inline-block;
        padding: 5px 10px;
        cursor: pointer;
        border: 1px solid #ccc;
        border-radius: 4px;
        background-color: #f7f7f7;
    }

    .custom-file-label:hover {
        background-color: #e7e7e7;
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
        <div class="col-lg-12" v-show="cargando">
            <div style="margin-top: 200px; max-width: 100% !important; margin-bottom: auto; text-align:center; letter-spacing: 2px">
                <h5 class="mb-5">CARGANDO...</h5>
                <div class="loader"></div>
            </div>
        </div>
        <div class="row mt-3 px-5" v-cloak v-show="!cargando" >
            <div class="col-lg-8 d-flex align-items-start">
                <h2 class="bold my-0 py-1 " style="letter-spacing: 2px">COMPONENTES REUTILIZABLES</h2>
            </div>
            <div class="col-lg-2 d-flex align-items-end">
                <button class="btn btn-block btn-success" @click="ingresarComponente"><i class="fa fa-plus-circle"></i> INGRESAR COMPONENTE</button>
            </div>
            <div class="col-lg-2 d-flex align-items-end">
                <button class="btn btn-block" @click="guardarCambios"><i class="fa fa-save"></i> GUARDAR CAMBIOS</button>
            </div>
            <div class="col-lg-12 mt-3" >
                <table class="table" id="tabla">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 15%" >Fecha ingreso</th>
                            <th style="width: 15%" >Componente</th>
                            <th style="width: 30%" >Descripción</th>
                            <th style="width: 20%" >Proveedor / Material</th>
                            <th style="text-transform: none !important; width: 15%">Disponibles</th>
                            <th style="text-transform: none !important; width: 10%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="c in componentes">
                            <td>@{{c.created_at_show}}</td>
                            <td class="bold">@{{c.nombre}}</td>
                            <td>@{{c.descripcion}}</td>
                            <td>@{{c.proveedor}}</td>
                            <td>
                                <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <button class="input-group-text py-0 cursor-pointer" style="background-color: #e3e3e3 !important" @click="c.cantidad > 0 ? c.cantidad-- : c.cantidad"> <i class="fa fa-minus"></i> &nbsp;&nbsp;</button>
                                        </div>
                                        <input type="number" v-model="c.cantidad" class="form-control text-center px-1 py-1" step="1">
                                        <div class="input-group-append">
                                            <button class="input-group-text py-0 cursor-pointer" style="background-color: #e3e3e3 !important" @click="c.cantidad++"> &nbsp;&nbsp;<i class="fa fa-plus"></i> </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-danger" @click="eliminarComponente(c.id)"><i class="fa fa-times-circle"></i> Dar de baja</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- modal nuevo componente --}}
            <div class="modal fade" id="modalNuevo" tabindex="-1" role="dialog" aria-labelledby="modalNuevoLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title bold" id="modalNuevoLabel">INGRESAR NUEVO COMPONENTE</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="bold" for="nombre">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" v-model="nuevo.nombre" placeholder="Nombre del componente">
                            </div>
                            <div class="form-group">
                                <label class="bold" for="descripcion">Descripción</label>
                                <textarea class="form-control text-left px-1 py-1" v-model="nuevo.descripcion" rows="3" placeholder="Descripción del componente"></textarea>
                            </div>
                            <div class="form-group">
                                <label class="bold" for="proveedor">Proveedor / Material</label>
                                <input type="text" class="form-control" v-model="nuevo.proveedor" placeholder="Proveedor o material">
                            </div>
                            <div class="form-group">
                                <label class="bold" for="cantidad">Cantidad Inicial <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" v-model.number="nuevo.cantidad" min="0">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
                            <button type="button" class="btn" @click="guardarNuevoComponente"> <i class="fa fa-save"></i> Guardar componente</button>
                        </div>
                    </div>
                </div>
            </div>


        </div>

    </div>
@endsection

@push('scripts')

    <script type="text/javascript">
        Vue.component('v-select', VueSelect.VueSelect)

        var app = new Vue({
        el: '#vue-app',
        data: {
            cargando: false,
            componentes: [],
            nuevo: {nombre:'', descripcion:'', proveedor:'', cantidad: 0},
        },
        methods:{
            async eliminarComponente(id){
                let t = this
                swal({
                    title: '¿Estás seguro?',
                    text: "Esta acción eliminará el componente seleccionado del inventario.",
                    icon: 'warning',
                    buttons: {
                        cancel: {
                            text: "Cancelar",
                            value: null,
                            visible: true,
                            className: "",
                            closeModal: true,
                        },
                        confirm: {
                            text: "Eliminar",
                            value: true,
                            visible: true,
                            className: "btn-danger",
                            closeModal: true
                        }
                    },
                    dangerMode: true,
                }).then(async (willDelete) => {
                    if (willDelete) {
                        t.cargando = true;
                        try {
                            const response = await axios.delete(`/api/componentes-reutilizables/${id}`);
                            if (response.data.success) {
                                swal('Éxito', 'Componente eliminado correctamente.', 'success');
                                t.fetchComponentes();
                            } else {
                                swal('Error', response.data.message || 'Ocurrió un error al eliminar el componente.', 'error');
                            }
                        } catch (error) {
                            console.error('Error deleting componente:', error);
                            swal('Error', 'Ocurrió un error al eliminar el componente.', 'error');
                        } finally {
                            t.cargando = false;
                        }
                    }
                });
            },
            async guardarNuevoComponente(){
                let t = this
                if (!t.nuevo.nombre || t.nuevo.nombre.trim() === '') {
                    swal('Error', 'El nombre del componente es obligatorio.', 'error');
                    return;
                }
                if (t.nuevo.cantidad === null || t.nuevo.cantidad < 0) {
                    swal('Error', 'La cantidad inicial debe ser un número mayor o igual a cero.', 'error');
                    return;
                }
                t.cargando = true;
                try {
                    const response = await axios.post('/api/componentes-reutilizables', this.nuevo);
                    if (response.data.success) {
                        swal('Éxito', 'Componente guardado correctamente.', 'success');
                    } else {
                        swal('Error', response.data.message || 'Ocurrió un error al guardar los cambios.', 'error');
                    }
                } catch (error) {
                    console.error('Error saving componentes:', error);
                    swal('Error', 'Ocurrió un error al guardar los cambios.', 'error');
                } finally {
                    this.fetchComponentes();
                    $('#modalNuevo').modal('hide');
                    this.cargando = false;
                }

            },
            ingresarComponente(){
                let t = this
                t.nuevo = {nombre:'', descripcion:'', proveedor:'', cantidad: 0};
                $('#modalNuevo').modal('show');
            },
            async guardarCambios(){
                this.cargando = true;
                try {
                    const response = await axios.put('/api/componentes-reutilizables', this.componentes);
                    if (response.data.success) {
                        swal('Éxito', 'Cambios guardados correctamente.', 'success');
                        this.fetchComponentes();
                    } else {
                        swal('Error', response.data.message || 'Ocurrió un error al guardar los cambios.', 'error');
                    }
                } catch (error) {
                    console.error('Error saving componentes:', error);
                    swal('Error', 'Ocurrió un error al guardar los cambios.', 'error');
                } finally {
                    this.cargando = false;
                }
            },
            async fetchComponentes() {
                this.cargando = true

                $('#tabla').DataTable().destroy();
                try {
                    const response = await axios.get(`/api/componentes-reutilizables`);
                    this.componentes = response.data.componentes;
                } catch (error) {
                    console.error('Error fetching componentes:', error);
                } finally {
                    this.cargando = false;
                    Vue.nextTick(function(){
                        $('#tabla').DataTable({
                            paging: true,
                            searching: true,
                            pageLength: 20,
                            lengthMenu: [[20, 30, 50, 100, -1], [20, 30, 50, 100, "All"]],  
                            ordering: true,
                            info: true,
                            language: {
                                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
                            }
                        });
                    });
                }
            },
        },
        mounted: async function () {
            let t = this;
            await t.fetchComponentes();
        }

                
    })

    </script>



        
@endpush