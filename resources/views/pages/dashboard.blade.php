@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'dashboard'
])


<style>
    

</style>


@section('content')
    <div class="content" id="app">
        @if(session('error'))
            <div class="alert alert-primary text-dark" role="alert">
                <i class="fa fa-info-circle "></i> <strong>¡Lo sentimos! </strong> {{ session('error') }}
            </div>
        @endif
        <div class="row">
            
        </div>
    </div>
@endsection

@push('scripts')

<script>
// Vue.js App
        new Vue({
            el: '#app',
            mounted() {

            }
        });
</script>
 
@endpush
