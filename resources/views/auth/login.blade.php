@extends('layouts.app', [
    'class' => 'login-page',
    'backgroundImagePath' => 'img/bg/fabio-mangione.jpg'
])

<style>
body {
	height: 100vh;
	overflow: hidden;
	font-family: "Poppins";
}
  .login-page {
      height: 100vh;
      margin: auto;
      align-content: center;
      background-image: url('paper/img/background-guest.jpg'); background-size: cover; background-repeat: no-repeat
  }
  .form {
    position: relative;
    z-index: 1;
    background: #FFFFFF;
    max-width: 500px;
    margin: 0 auto 100px;
    padding: 45px;
    text-align: center;
    border-radius: 10px;
    box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);
  }
  .form input {
    font-family: "Poppins", sans-serif;
    outline: 0;
    background: #f2f2f2;
    width: 100%;
    border: 0;
    border-radius: 7px;
    margin: 0 0 15px;
    padding: 15px;
    box-sizing: border-box;
    font-size: 14px;
    
  }
  .form button {
    font-family: "Poppins", sans-serif;
    text-transform: uppercase;
    outline: 0;
    background: #c3d213;
    width: 100%;
    border: 0;
    padding: 10px;
    color: black;
    border-radius: 7px;
    font-size: 18px;
    font-weight: bold;
    -webkit-transition: all 0.3 ease;
    transition: all 0.3 ease;
    cursor: pointer;
    letter-spacing: 2px;

  }
  .form button:hover,.form button:active,.form button:focus {
    background: #bdcd0d;
    color: black;
  }
  .form .message {
    margin: 15px 0 0;
    color: #b3b3b3;
    font-size: 12px;
  }
  .form .message a {
    color: #234666;
    text-decoration: none;
  }
  .form .register-form {
    display: none;
  }

</style>

@section('content')

<div class="container-fluid">
	<div class="row">
		<div class="col-xl-12" >
			<div class="login-page">
				<div class="form pt-5">
          <form class="login-form" method="post" action="{{ route('login') }}">
              @csrf
              <img src="{{ \App\Helpers\SystemHelper::getLogo() }}" class="mb-3" width="35%"><br>
              <div class="form-group">
                  <h5 class="mb-1 mt-2 bold" style="letter-spacing: 2px"><small>CODIGO DE ACCESO</small></h5>
                  <input style="font-size: 20px !important; letter-spacing: 2px" 
                        class="text-center" 
                        type="password" 
                        name="codigo_acceso" 
                        placeholder="Ingrese su código de acceso" 
                        required/>
              </div>

              @if ($errors->has('codigo_acceso'))
              <span class="invalid-feedback my-0 mb-3" style="display: block;" role="alert">
                  <strong>{{ $errors->first('codigo_acceso') }}</strong>
              </span>
              @endif
              
              <button type="submit">ENTRAR</button>
          </form>
				</div>
			</div>
		</div>
		

	</div>
</div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            demo.checkFullPageBackgroundImage();
        });
    </script>
@endpush
