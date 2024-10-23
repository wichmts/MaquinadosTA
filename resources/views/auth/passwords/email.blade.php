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
      background-image: url('/paper/img/background-guest.jpg'); background-size: cover; background-repeat: no-repeat
  }
  .form {
    position: relative;
    z-index: 1;
    background: #FFFFFF;
    max-width: 500px;
    margin: 0 auto 100px;
    padding: 45px;
    text-align: center;
    border-radius: 15px;
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
    background: #234666;
    width: 100%;
    border: 0;
    padding: 10px;
    color: #FFFFFF;
    border-radius: 7px;
    font-size: 14px;
    -webkit-transition: all 0.3 ease;
    transition: all 0.3 ease;
    cursor: pointer;
  }
  .form button:hover,.form button:active,.form button:focus {
    background: #0e2941;
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
				<div class="form pt-3">
					<form class="login-form" method="post" action="{{ route('password.email') }}">
						@csrf
						<img src="{{ \App\Helpers\SystemHelper::getLogo() }}" class="mb-3" width="60%"><br>
            <div class="form-group ">
              <h5 class="mb-1" style="letter-spacing: 2px"><small>Correo electrónico</small> </h5>
              <input class=" text-center" type="text" name="email" placeholder="nombre@mail.com" required />
            </div>
            @if (session('status'))
						<div>
							<span class="text-success my-0 mb-3" style="display: block" role="alert">
								<strong>{{ session('status') }}</strong>
							</span>
						</div>
					  @endif
						@if ($errors->has('email'))
						<span class="invalid-feedback my-0 mb-3" style="display: block;" role="alert">
							<strong>{{ $errors->first('email') }}</strong>
						</span>
					@endif
						<button type="submit">Enviar link al correo electrónico</button>
						<p class="message">¿Recordaste tu contrasena? <a href="/login">Inicia sesión aqui</a></p>
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
