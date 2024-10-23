@extends('layouts.app_publica', [
    'class' => 'login-page',
    'elementActive' => ''
])

<style>


</style>

@section('content')
    
@endsection

@push('scripts')

    <script type="text/javascript">

        var app = new Vue({
            el: '#vue-app',
            data: {
               
            },
            methods:{
               
            },
            mounted: function () {
                this.$nextTick(function () {
                   


                })
            }
        })

    </script>

@endpush
