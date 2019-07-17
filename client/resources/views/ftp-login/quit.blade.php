@extends('layout.layout')
@section('header')
	<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
	<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="/fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
	<link rel="stylesheet" type="text/css" href="/css/util.css">
	<link rel="stylesheet" type="text/css" href="/css/main.css" />
@endsection
@section('content')
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100 p-l-85 p-r-85 p-t-55 p-b-55">
				<form action="/disconnect" method="POST" class="login100-form validate-form flex-sb flex-w">

					<input type="hidden" name="_token" value="{{ csrf_token() }}">

					<div class="container-login100-form-btn">
                        <button class="login100-form-btn abort-form">
                            Go back
                        </button>
                    </div>

					<div class="container-login100-form-btn">
						<button class="login100-form-btn">
							Disconnect
						</button>
					</div>

				</form>
			</div>
		</div>
	</div>
@endsection

@section('footer')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.15.0/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
	<script src="/js/main.js"></script>
@endsection


