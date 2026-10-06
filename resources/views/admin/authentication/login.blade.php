@extends('admin.authentication.master')

@section('title')Login
 | {{Config('custom.constants.solution.name')}}
@endsection

@push('css')
<style>
    /* theme CSS has no working 'hide' label, so the toggle went blank after one click */
    .login-form .show-hide span:not(.show):before { content: "hide"; }
    #websitePreview .modal-header .btn-close { position: static; margin: 0; }
</style>
@endpush

@section('content')
    <section>
	    <div class="container-fluid">
	        <div class="row">
	            <div class="col-xl-5"><img class="bg-img-cover bg-center" src="{{ asset('assets/images/login/lbg.png') }}" alt="looginpage" /></div>
	            <div class="col-xl-7 p-0">
	                <div class="login-card">
	                    <form class="theme-form login-form" method="post" action="/portal/auth">
							@csrf
							<center><img width="40%" src="{{ asset('assets/images/logo/oppah.png') }}" alt="e-Mikopo" /></center>
	                        <h6>{{ App\TIRAClient\Scripts\Classes\Utils::timeGreeting() }} !</h6>
	                        <div class="form-group">
	                            <label>Username</label>
	                            <div class="input-group">
	                                <span class="input-group-text"><i class="icon-email"></i></span>
	                                <input class="form-control" type="text" name="email" required placeholder="Enter your username ..." />
	                            </div>
								@if($errors->has('email'))
									<span class="text-danger txt-secondary"> - {{ $errors->first('email') }}</span>
								@endif
	                        </div>
	                        <div class="form-group">
	                            <label>Password</label>
	                            <div class="input-group">
	                                <span class="input-group-text"><i class="icon-lock"></i></span>
	                                <input class="form-control" type="password" name="password" required placeholder="Enter your password ..." />
	                                <div class="show-hide"><span class="show" id="togglePassword" role="button" aria-label="Show password"> </span></div>
	                            </div>
								@if($errors->has('password'))
									<span class="text-danger txt-secondary"> - {{ $errors->first('password') }}</span>
								@endif
	                        </div>

							<br />

	                        <div class="form-group">
	                            <div class="checkbox">
	                                <input id="checkbox1" type="checkbox" name="remember" />
	                                <label class="text-muted" for="checkbox1">Remember me</label>
								</div>
	                            <a class="link" href="{{ route('forget-password') }}">Forgot password?</a>
	                        </div>
	                        <div class="form-group"><button style="width:100%" class="btn btn-primary btn-block" type="submit">Sign in</button></div>
	                        <div class="form-group">
	                            <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#websitePreview">
	                                <i class="fa fa-globe"></i> View our website
	                            </button>
	                        </div>
	                        
							@if($message = Session::get('error'))
							<div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                            <i class="icon-info-alt txt-danger"></i>
								{{ $message }}
                            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                       		</div>
							@endif

							@if($message = Session::get('success'))
							<div class="alert alert-success outline alert-dismissible fade show" role="alert">
                            <i class="icon-info-alt txt-success"></i>
								{{ $message }}
                            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                       		</div>
							@endif

							<br /><br />

							<div class="login-social-title">
							<!-- <h5>&copy; {{Config('custom.constants.solution.name')}} {{Config('custom.constants.solution.version')}}</h5> -->
							<h5>&copy;{{Config('custom.constants.solution.port_uat')}}  {{Config('custom.constants.solution.name')}} {{Config('custom.constants.solution.version')}}</h5>
	                        </div>
	                        <p>How to use it ?<a class="ms-2" href="{{ route('how-to-use') }}" target="_blank">Read User Manual</a></p>
	                    </form>
	                </div>
	            </div>
	        </div>
	    </div>
	</section>

	<div class="modal fade" id="websitePreview" tabindex="-1" aria-labelledby="websitePreviewLabel" aria-hidden="true">
	    <div class="modal-dialog modal-xl modal-dialog-centered">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title" id="websitePreviewLabel">Our website</h5>
	                <a class="btn btn-sm btn-primary ms-auto me-2" href="{{ route('website') }}" target="_blank">Open full site</a>
	                <button type="button" class="btn-close ms-0" data-bs-dismiss="modal" aria-label="Close"></button>
	            </div>
	            <div class="modal-body p-0">
	                <iframe data-src="{{ route('website') }}" title="Our website" style="width:100%;height:75vh;border:0;display:block"></iframe>
	            </div>
	        </div>
	    </div>
	</div>
	<script>
	    // Show / hide password (the theme script only handles inputs named login[password]).
	    document.getElementById('togglePassword').addEventListener('click', function (e) {
	        e.stopImmediatePropagation(); // the theme's handler would flip the label back
	        var input = document.querySelector('.login-form input[name="password"]');
	        var hidden = input.type === 'password';
	        input.type = hidden ? 'text' : 'password';
	        this.classList.toggle('show', !hidden);
	        this.setAttribute('aria-label', hidden ? 'Hide password' : 'Show password');
	    });
	    document.querySelector('.login-form').addEventListener('submit', function () {
	        this.querySelector('input[name="password"]').type = 'password';
	    });

	    // Load the website only when the preview is opened.
	    document.getElementById('websitePreview').addEventListener('show.bs.modal', function () {
	        var frame = this.querySelector('iframe');
	        if (!frame.getAttribute('src')) { frame.setAttribute('src', frame.getAttribute('data-src')); }
	    });
	</script>

    @push('scripts')
    @endpush

@endsection
