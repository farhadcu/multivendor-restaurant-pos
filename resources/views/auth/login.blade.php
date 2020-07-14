<!doctype html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>
    <base href="{{asset('/')}}" />
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="title" content="{{ config('settings.seo_meta_title') }}" />
    <meta name="description" content="{{ config('settings.seo_meta_description') }}">
    <meta name="author" content="">
    <meta name="robots" content="index, follow">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - {{ config('settings.site_title') }}</title>
    
    @if (config('settings.site_favicon') != null)
    <link rel="apple-touch-icon" href="{{ asset(FOLDER_PATH.LOGO.config('settings.site_favicon')) }}">
    <link rel="shortcut icon" href="{{ asset(FOLDER_PATH.LOGO.config('settings.site_favicon')) }}" />
    @endif
    <!--begin::Page Custom Styles(used by this page) -->
    <link rel="stylesheet" type="text/css" href="./public/css/app.css">
    <link href="./public/css/login-6.css" rel="stylesheet" type="text/css" />

    <!--end::Page Custom Styles -->

</head>

<!-- end::Head -->

<!-- begin::Body -->

<body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--fixed kt-subheader--enabled kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-page--loading">

    <!-- begin:: Page -->
    <div class="kt-grid kt-grid--ver kt-grid--root">
        <div class="kt-grid kt-grid--hor kt-grid--root  kt-login kt-login--v6 kt-login--signin" id="kt_login">
            <div
                class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--desktop kt-grid--ver-desktop kt-grid--hor-tablet-and-mobile">
                <div
                    class="kt-grid__item  kt-grid__item--order-tablet-and-mobile-2  kt-grid kt-grid--hor kt-login__aside">
                    <div class="kt-login__wrapper">
                        <div class="kt-login__container">
                            <div class="kt-login__body">
                                <div class="kt-login__logo">
                                    <a href="javascript:void(0);">
                                        @if (config('settings.site_logo') != null)
                                        <img alt="Logo" src="{{ asset(FOLDER_PATH.LOGO.config('settings.site_logo')) }}" style="max-width:125px;" />
                                        @endif
                                    </a>
                                </div>
                                <div class="kt-login__signin">
                                    <div class="kt-login__head">
                                        <h3 class="kt-login__title">Login To Dashboard</h3>
                                    </div>
                                    <div class="kt-login__form">
                                        <form class="kt-form" action="{{ route($route) }}" method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <input class="form-control @error('email') is-invalid @enderror" type="email" placeholder="Email" name="email"
                                                    autocomplete="off">
                                                @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <input class="form-control @error('password') is-invalid @enderror" type="password"
                                                    placeholder="Password" name="password">
                                                    @error('password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="kt-login__extra">
                                                <label class="kt-checkbox">
                                                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}> Remember me
                                                    <span></span>
                                                </label>
                                                @if (Route::has('password.request'))
                                                <a href="{{ route('password.request') }}" id="kt_login_forgot">Forget Password ?</a>
                                                @endif
                                            </div>
                                            <div class="kt-login__actions">
                                                <button type="submit" id="kt_login_signin_submit"
                                                    class="btn btn-brand btn-pill btn-elevate">Login</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="kt-grid__item kt-grid__item--fluid kt-grid__item--center kt-grid kt-grid--ver kt-login__content"
                    style="background-image: url(./public/img/bg-4.jpg);">
                    <div class="kt-login__section">
                        <div class="kt-login__block">
                            <h3 class="kt-login__title">Join Our Community</h3>
                            <div class="kt-login__desc">
                                Lorem ipsum dolor sit amet, coectetuer adipiscing
                                <br>elit sed diam nonummy et nibh euismod
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- end:: Page -->

    <!-- begin::Global Config(global config for global JS sciprts) -->
    <script>
        var KTAppOptions = {
            "colors": {
                "state": {
                    "brand": "#5d78ff",
                    "dark": "#282a3c",
                    "light": "#ffffff",
                    "primary": "#5867dd",
                    "success": "#34bfa3",
                    "info": "#36a3f7",
                    "warning": "#ffb822",
                    "danger": "#fd3995"
                },
                "base": {
                    "label": ["#c5cbe3", "#a1a8c3", "#3d4465", "#3e4466"],
                    "shape": ["#f0f3ff", "#d9dffa", "#afb4d4", "#646c9a"]
                }
            }
        };

    </script>

    <!-- end::Global Config -->

    <script src="./public/js/app.js" type="text/javascript"></script>
    <script src="./public/js/all.js" type="text/javascript"></script>
</body>

<!-- end::Body -->

</html>
