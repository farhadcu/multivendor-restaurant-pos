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

    <title>Forgotten Password - {{ config('settings.site_title') }}</title>
    <link rel="apple-touch-icon" href="./public/images/ico/apple-icon-120.png">
    @if (config('settings.site_favicon') != null)
    <link rel="shortcut icon" href="{{ asset(FOLDER_PATH.LOGO.config('settings.site_favicon')) }}" />
    @endif
    <!--begin::Page Custom Styles(used by this page) -->
    <link rel="stylesheet" type="text/css" href="./public/css/app.css">
    <link href="./public/css/login-6.css" rel="stylesheet" type="text/css" />

    <!--end::Page Custom Styles -->

</head>

<body
    class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--fixed kt-subheader--enabled kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-page--loading">

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
                                        <img alt="Logo" src="{{ asset(FOLDER_PATH.LOGO.config('settings.site_logo')) }}"
                                            style="max-width:125px;" />
                                        @endif
                                    </a>
                                </div>
                                <div class="kt-login__signin">
                                    <div class="kt-login__head">
                                        <h3 class="kt-login__title">Forgotten Password</h3>
                                    </div>
                                    <div class="kt-login__form">
                                        @if (session('status'))
                                        <div class="alert alert-success" role="alert">
                                            {{ session('status') }}
                                        </div>
                                        @endif

                                        <form method="POST" action="{{ route('password.email') }}">
                                            @csrf

                                            <div class="form-group">
                                                    <input id="email" type="email"
                                                        class="form-control @error('email') is-invalid @enderror"
                                                        name="email" value="{{ old('email') }}" required
                                                        autocomplete="email" autofocus placeholder="{{ __('E-Mail Address') }}">

                                                    @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                            </div>

                                            <div class="form-group mt-5">
                                                <div class="col-md-8 offset-md-2">
                                                    <button type="submit" class="btn btn-brand btn-pill btn-elevate">
                                                        {{ __('Send Password Reset Link') }}
                                                    </button>
                                                </div>
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

</html>
