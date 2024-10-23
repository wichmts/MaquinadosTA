{{-- <nav class="navbar navbar-expand-lg navbar-transparent" style="background-color: white !important; padding-top: 10px; padding-bottom: 10px"> --}}
{{-- <nav id="barra-navegacion" class="px-5 navbar navbar-expand-lg fixed-top navbar-transparent" >
   <div class="container-fluid">
    <div class="navbar-wrapper" style="max-width: 170px">
        <div class="navbar-toggle">
            <button type="button" class="navbar-toggler">
                <span class="navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
            </button>
        </div>
        <a class="navbar-brand " href="/" >
            <img src="/paper/img/logo-color.png" width="80%">
        </a>
    </div>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-bar navbar-kebab"></span>
        <span class="navbar-toggler-bar navbar-kebab"></span>
        <span class="navbar-toggler-bar navbar-kebab"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navigation">
        <ul class="navbar-nav ml-auto">
            @if(!auth()->check())
            <li class="nav-item">
                <a href="{{ route('login') }}" class=" login-boton">
                    <i class="fa fa-sign-in-alt"></i> Iniciar sesión
                </a>
            </li>
            @else
            <li class="nav-item">
                <a href="{{ route('home') }}" class=" login-boton">
                     <i class="far fa-user"></i> Ir a mi cuenta
                </a>
            </li>
            @endif
        </ul>
    </div>
</div> --}}

</nav>

