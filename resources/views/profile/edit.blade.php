@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'profile'
])

@section('content')
  <style media="screen">
    .card .card-body{
      padding: 0px !important
    }
    .activate{
        background-color: #6cbac3 !important;
    }
    .activate:hover{
        background-color: #6cbac3 !important;
    }
    .activate:active{
        background-color: #6cbac3 !important;
    }
    .activate:focus{
        background-color: #6cbac3 !important;
    }
    .card-body{
        min-height: 180px !important;
    }
    .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
        border-color: #dee2e6 #dee2e6 #fff;
        font-weight: bold !important;
    }

    [v-cloak] { display: none; }
  </style>
    <div class="content" id="app">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif
        @if (session('password_status'))
            <div class="alert alert-success" role="alert">
                {{ session('password_status') }}
            </div>
        @endif
        <div class="row" v-cloak>
            <div class="col-12 col-xl-10 offset-xl-1">
                <div class="card card-user">
                    <div class="image">
                        <img src="{{ asset('/paper/img/back.png
                        ') }}" alt="...">
                    </div>
                    <div class="card-header">
                        <div class="author">
                            <img style="background-color: white; object-fit: cover" src="/paper/img/profilepic.png" alt="logo" class=" border-gray avatar" >
                            <h3 class="my-1" style="color: #003e5f;  letter-spacing:1px" >{{auth()->user()->nombre_completo}}</h3>
                            <h5 class="badge badge-primary my-1" style="font-weight: normal; font-size: 17px; letter-spacing:2px">@{{role}}</h5>                             
                            <h5 class="mt-1 mb-4" style="color: black !important; font-weight: normal; font-size: 15px">{{auth()->user()->email}}</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 px-4">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="general-tab" data-toggle="tab" data-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true"><i class="fa fa-user-circle"></i> Información General</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="seguridad-tab" data-toggle="tab" data-target="#seguridad" type="button" role="tab" aria-controls="seguridad" aria-selected="false"><i class="fa fa-key"></i> Seguridad</button>
                                    </li>
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="row my-3">
                                                <div class="col-xl-8 offset-xl-2 form-group">
                                                    <label class="bold">Nombre(s)</label>
                                                    <input type="text" name="nombre" class="form-control"  value="{{ auth()->user()->nombre }}" required>
                                                    @if ($errors->has('nombre'))
                                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                                            <strong>{{ $errors->first('nombre') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="col-xl-8 offset-xl-2 form-group">
                                                    <label class="bold">Apellido paterno</label>
                                                    <input type="text" name="ap_paterno" class="form-control"  value="{{ auth()->user()->ap_paterno }}" required>
                                                    @if ($errors->has('ap_paterno'))
                                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                                            <strong>{{ $errors->first('ap_paterno') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="col-xl-8 offset-xl-2 form-group">
                                                    <label class="bold">Apellido materno</label>
                                                    <input type="text" name="ap_materno" class="form-control"  value="{{ auth()->user()->ap_materno }}" required>
                                                    @if ($errors->has('ap_materno'))
                                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                                            <strong>{{ $errors->first('ap_materno') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="col-xl-8 offset-xl-2 form-group">
                                                    <label class="bold">Celular</label>
                                                    <input type="text" name="celular" class="form-control"  value="{{ auth()->user()->celular }}">
                                                    @if ($errors->has('celular'))
                                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                                            <strong>{{ $errors->first('celular') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="col-xl-8 offset-xl-2 form-group">
                                                    <label class="bold">Correo electronico</label>
                                                    <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}" required>
                                                    @if ($errors->has('email'))
                                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                                            <strong>{{ $errors->first('email') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="col-xl-12 text-center">
                                                    <button type="submit" class="btn btn-secondary btn-round"><i class="fa fa-save"></i> {{ __('Guardar cambios') }}</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane fade show" id="seguridad" role="tabpanel" aria-labelledby="seguridad-tab">
                                         <form action="{{ route('profile.password') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="row my-3">
                                                <div class="col-xl-8 offset-xl-2 form-group">
                                                    <label class="bold">Contraseña actual</label>
                                                    <input type="password" name="old_password" class="form-control" required>
                                                     @if ($errors->has('old_password'))
                                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                                            <strong>{{ $errors->first('old_password') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="col-xl-8 offset-xl-2 form-group">
                                                    <label class="bold">Nueva contraseña</label>
                                                    <input type="password" name="password" class="form-control"  required>
                                                    @if ($errors->has('password'))
                                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                                            <strong>{{ $errors->first('password') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="col-xl-8 offset-xl-2 form-group">
                                                    <label class="bold">Confirmar contraseña nueva</label>
                                                    <input type="password" name="password_confirmation" class="form-control"  required>
                                                    @if ($errors->has('password_confirmation'))
                                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="col-xl-12 text-center">
                                                    <button type="submit" class="btn btn-secondary btn-round"><i class="fa fa-save"></i> {{ __('Guardar cambios') }}</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('vuescripts')
  <script type="text/javascript">
  var app = new Vue({
    el: '#app',
    data: {
        role: '{{auth()->user()->roles()->first()->name}}',
        user: {},
        activado: true,
        cargando: true,
        medico: {
            imagen_perfil: '',
            imagen_portada: '',
        },
        suscripcion: {
            plan: 1, 
            estatus: 'cancelled', 
            proximo_cobro: '-', 
        }
    },
    methods:{

    },
    mounted: function () {
      let t = this;
      t.cargando = true;
      this.$nextTick(function () {
        axios.get('/api/user')
            .then(response => {
                t.user = response.data;
                t.cargando = false;
            })
            .catch(e => {
                console.log(e);
                t.cargando = false;    
            })
      });
    }
  })
  </script>
@endsection