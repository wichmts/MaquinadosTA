<div>
    {{-- @include('layouts.navbars.admin') --}}

    @include('layouts.navbars.navs.auth')
    @yield('content')
    @include('layouts.footer')
</div>