@extends('admin.layouts.app')

@section('title')
    {{ucwords($page_title)}}
@endsection

@section('content')
<!-- begin:: Subheader -->
<div class="kt-subheader   kt-grid__item" id="kt_subheader">
    <div class="kt-subheader__main">
        {{-- <h3 class="kt-subheader__title"> Dashboard </h3> --}}
        <span class="kt-subheader__separator kt-hidden"></span>
        <div class="kt-subheader__breadcrumbs">
            <a href="{{route('admin.dashboard')}}" class="kt-subheader__breadcrumbs-link"><i class="m-nav__link-icon la la-home"></i>  Dashboard </a>
            <a class="kt-subheader__breadcrumbs-link"><i class="la la-angle-double-right"></i>  Software Settings </a>
            <a class="kt-subheader__breadcrumbs-link"><i class="la la-angle-double-right"></i>  {{ucwords($sub_title)}} </a>

            <!-- <span class="kt-subheader__breadcrumbs-link kt-subheader__breadcrumbs-link--active">Active link</span> -->
        </div>
    </div>
</div>
<!-- end:: Subheader -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="col-xl-12">
        @include('flash')
    </div>
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
            <div class="kt-portlet__head-label">
                <span class="kt-portlet__head-icon">
                    <i class="{{$page_icon}} text-brand"></i>
                </span>
                <h3 class="kt-portlet__head-title text-brand">
                    {{ucwords($sub_title)}}
                </h3>
            </div>
        </div>
        <div class="kt-portlet__body">
            <div class="row user">
                <div class="col-md-3">
                    <div class="tile p-0">
                        <ul class="nav flex-column nav-tabs user-tabs" id="setting-tab">
                            @if (Helper::permission('setting-general'))
                            <li class="nav-item"><a class="nav-link active" href="#general" data-toggle="tab">General</a></li>
                            <li class="nav-item"><a class="nav-link" href="#site-logo" data-toggle="tab">Site Logo</a></li>
                            <li class="nav-item"><a class="nav-link" href="#footer-seo" data-toggle="tab">Footer &amp; SEO</a></li>
                            <li class="nav-item"><a class="nav-link" href="#social-links" data-toggle="tab">Social Links</a></li>
                            @endif
                            @if (Helper::permission('setting-smtp'))
                            <li class="nav-item"><a class="nav-link" href="#mail" data-toggle="tab">SMTP</a></li>
                            @endif
                            @if (Helper::permission('setting-sms'))
                            <li class="nav-item"><a class="nav-link" href="#sms" data-toggle="tab">SMS</a></li>
                            @endif
                            @if (Helper::permission('setting-api'))
                            <li class="nav-item"><a class="nav-link" href="#payments" data-toggle="tab">Payments</a></li>
                            <li class="nav-item"><a class="nav-link" href="#social-media" data-toggle="tab">Social Media</a></li>
                            @endif
                        </ul>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="tab-content">
                        @if (Helper::permission('setting-general'))
                        <div class="tab-pane active" id="general">
                            @include('admin::setting.include.general')
                        </div>
                        <div class="tab-pane fade" id="site-logo">
                            @include('admin::setting.include.logo')
                        </div>
                        <div class="tab-pane fade" id="footer-seo">
                            @include('admin::setting.include.footer-seo')
                        </div>
                        <div class="tab-pane fade" id="social-links">
                            @include('admin::setting.include.social-links')
                        </div>
                        @endif
                        @if (Helper::permission('setting-smtp'))
                        <div class="tab-pane fade" id="mail">
                            @include('admin::setting.include.mail')
                        </div>
                        @endif
                        @if (Helper::permission('setting-sms'))
                        <div class="tab-pane fade" id="sms">
                            @include('admin::setting.include.sms')
                        </div>
                        @endif
                        @if (Helper::permission('setting-api'))
                        <div class="tab-pane fade" id="payments">
                            @include('admin::setting.include.payments')
                        </div>
                        <div class="tab-pane fade" id="social-media">
                            @include('admin::setting.include.social-media')
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
    <script>
        loadFile = function(event, id) {
            var output = document.getElementById(id);
            output.src = URL.createObjectURL(event.target.files[0]);
        };
        function update_data(form) {
            if(form == 'mail'){
                var update_form = $('#mail-form').serialize();
                var url = "{{route('admin.setting.mail')}}";
                var button = '#update-mail';
            }else if(form == 'sms'){
                var update_form = $('#sms-form').serialize();
                var url = "{{route('admin.setting.sms')}}";
                var button = '#update-sms';
            }

            $.ajax({
                url: url,
                type: "POST",
                data: update_form,
                dataType: "JSON",
                beforeSend: function () {
                    $(button).addClass('kt-spinner kt-spinner--md kt-spinner--light');
                },
                complete: function(){
                    $(button).removeClass('kt-spinner kt-spinner--md kt-spinner--light');
                },
                success: function (data) {
                    if (data.status) {
                        bootstrap_notify(data.status,data.message);
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }
        // $(document).ready(function () {
        //     $(document).on('click','#update-mail',function () {
        //         alert();
                
        //     });
        // });
    </script>
@endpush
