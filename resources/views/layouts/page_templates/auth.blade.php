<div class="wrapper">

    {{-- @include('layouts.navbars.admin') --}}

    
    <div class="main-panel">    
        @include('layouts.navbars.navs.auth')
        @yield('content')
        @include('layouts.footer')
    </div>
</div>
