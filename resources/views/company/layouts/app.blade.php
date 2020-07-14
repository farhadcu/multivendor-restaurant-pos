<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<!-- begin::Head -->
<head>
    <!--begin::Base Path (base relative path for assets of this page) -->
    <base href="{{asset('/')}}">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    
    <meta name="title" content="{{ config('settings.seo_meta_title') }}"/>
    <meta name="description" content="{{ config('settings.seo_meta_description') }}">
    <meta name="author" content="KY LIFE">
    <meta name="robots" content="index, follow">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - @if (!empty(Auth::user()->company->company_name)) {{Auth::user()->company->company_name}}   @endif</title>
    @if (!empty(Auth::user()->company->favicon))
    <link rel="apple-touch-icon" href="{{ asset(FOLDER_PATH.COMPANY_PHOTO.Auth::user()->company->favicon) }}">
    <link rel="shortcut icon" href="{{ asset(FOLDER_PATH.COMPANY_PHOTO.Auth::user()->company->favicon) }}" />
    @endif
    <link href="./public/css/app.css" rel="stylesheet" type="text/css" />
	@stack('styles')
	

</head>
<!-- end::Head -->

<!-- begin::Body -->

<body>

    <!-- begin::Page loader -->
    <div id='preloader'>
        <div class='loader'>
            <div class='loader--dot'></div>
            <div class='loader--dot'></div>
            <div class='loader--dot'></div>
            <div class='loader--dot'></div>
            <div class='loader--dot'></div>
            <div class='loader--dot'></div>
            <div class='loader--text'></div>
        </div>
    </div>
    <!-- end::Page Loader -->

    <!-- begin:: Page -->

    <!-- begin:: Header Mobile -->
    <div id="kt_header_mobile" class="kt-header-mobile  kt-header-mobile--fixed " style="padding-top:10px;padding-bottom:10px;">
        <div class="kt-header-mobile__logo">
            <a href="{{url('dashboard')}}">
                @if (!empty(Auth::user()->company->logo))
                    <img alt="Logo" src="{{ asset(FOLDER_PATH.COMPANY_PHOTO.Auth::user()->company->logo) }}" style="max-width:55px;margin:2px 0;" />
                @endif
            </a>
        </div>
        <div class="kt-header-mobile__toolbar">
            <button class="kt-header-mobile__toolbar-toggler kt-header-mobile__toolbar-toggler--left"
                id="kt_aside_mobile_toggler"><span></span></button>
            {{-- <button class="kt-header-mobile__toolbar-toggler" id="kt_header_mobile_toggler"><span></span></button> --}}
            <button class="kt-header-mobile__toolbar-topbar-toggler" id="kt_header_mobile_topbar_toggler"><i
                    class="flaticon-more-1"></i></button>
        </div>
    </div>
    <!-- end:: Header Mobile -->

    <div class="kt-grid kt-grid--hor kt-grid--root">
        <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">
            <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">

                <!-- begin:: Header -->
                @include('company.include.header')
                <!-- end:: Header -->
                <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-grid--stretch">
                    <div class="kt-container kt-body  kt-grid kt-grid--ver" id="kt_body">

                        <!-- begin:: Aside -->
                        <button class="kt-aside-close " id="kt_aside_close_btn"><i class="la la-close"></i></button>
                        <div class="kt-aside  kt-aside--fixed  kt-grid__item kt-grid kt-grid--desktop kt-grid--hor-desktop"
                            id="kt_aside">

                            <!-- begin:: Aside Menu -->
                            <div class="kt-aside-menu-wrapper kt-grid__item kt-grid__item--fluid" id="kt_aside_menu_wrapper">
                                <div id="kt_aside_menu" class="kt-aside-menu " data-ktmenu-vertical="1"  data-ktmenu-scroll="1">
                                    {!! Helper::company_menu() !!}
                                </div>
                            </div>
                            <!-- end:: Aside Menu -->
                        </div>
                        <!-- end:: Aside -->
                        
                        <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
                            <!-- begin:: Content -->
                            @yield('content')
                            <!-- end:: Content -->
                        </div>
                    </div>
                </div>

                <!-- begin:: Footer -->
               @include('company.include.footer')
                <!-- end:: Footer -->
            </div>
        </div>
    </div>
    <!-- end:: Page -->

    <!-- begin::Scrolltop -->
    <div id="kt_scrolltop" class="kt-scrolltop">
        <i class="fa fa-arrow-up"></i>
    </div>
    <!-- end::Scrolltop -->

    <!-- begin::Global Config(global config for global JS sciprts) -->
    <script>
        var KTAppOptions = {
            "colors": {
                "state": {
                    "brand": "#591df1",
                    "light": "#ffffff",
                    "dark": "#282a3c",
                    "primary": "#5867dd",
                    "success": "#34bfa3",
                    "info": "#36a3f7",
                    "warning": "#ffb822",
                    "danger": "#fd397a"
                },
                "base": {
                    "label": ["#c5cbe3", "#a1a8c3", "#3d4465", "#3e4466"],
                    "shape": ["#f0f3ff", "#d9dffa", "#afb4d4", "#646c9a"]
                }
            }
        };
    </script>

    <!-- end::Global Config -->

    <!--begin:: Global Mandatory Vendors -->
    <script src="./public/js/app.js" type="text/javascript"></script>
    <script src="./public/js/all.js" type="text/javascript"></script>
    <script src="./public/js/scripts.bundle.js" type="text/javascript"></script>
    @stack('scripts') <!-- Add Script files from each blade dynamically -->
    <!--end::Global Theme Bundle -->

    <script>
    if($('#sidemenu li ul').children('.kt-menu__item--active').length !== 0){
        $('.kt-menu__item--active').parents('li').addClass('kt-menu__item--open');
    }

    /*************************************************
    ******* Start :: Store Form Data Function *******
    **************************************************/
    function store_data(table,url,method){
        $.ajax({
            url: url,
            type: "POST",
            data: $('#saveDataForm').serialize(),
            dataType: "JSON",
            beforeSend: function () {
                $('#save-btn').addClass('kt-spinner kt-spinner--md kt-spinner--light');
            },
            complete: function(){
                $('#save-btn').removeClass('kt-spinner kt-spinner--md kt-spinner--light');
            },
            success: function (data) {
                $("#saveDataForm").find('.is-invalid').removeClass('is-invalid');
                $("#saveDataForm").find('.error').remove();

                if (data.status) {
                    bootstrap_notify(data.status,data.message);
                    if(data.status == 'success'){
                        if(method == 'update'){
                            table.ajax.reload( null, false );
                        }else{
                            table.ajax.reload();
                        }
                        
                        $('#saveDataModal').modal('hide');
                    }
                } else {
                    $.each(data.errors, function (key, value) {
                        // $('#saveDataForm .form-group').find('.error_'+key).text(value); 
                        $("#saveDataForm input[name='"+key+"']").addClass('is-invalid');
                        $("#saveDataForm select#" + key).parent().addClass('is-invalid');
                        $("#saveDataForm textarea[name='" + key + "']").addClass('is-invalid');
                        $("#saveDataForm input[name='"+key+"']").after('<div id="'+key+'" class="error invalid-feedback">'+value+'</div>');
                        $("#saveDataForm select#" + key).parent().after('<div id="'+key+'" class="error invalid-feedback">'+value+'</div>');
                        $("#saveDataForm textarea[name='"+key+"']").after('<div id="'+key+'" class="error invalid-feedback">'+value+'</div>');
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }
     /*************************************************
    ******* End :: Store Form Data Function *******
    **************************************************/

    /********************************************
    ******* Start :: Delete Data Function *******
    *********************************************/
    function delete_data(table,row,id,url){
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6610f2',
            cancelButtonColor: '#fd397a',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.value) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {id:id},
                    dataType: 'json'
                })
                .done(function(response){
                        if (response.status == 'success') {
                        Swal.fire("Deleted!", response.message, "success" ).then(function () {
                            // table.ajax.reload();
                            table.row(row).remove().draw(false);
                        });
                    } else if (response.status == 'danger') {
                        Swal.fire('Error deleting!', response.message,'error');
                    }
                })
                .fail(function(){
                    swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                });
            }
        })
    }
    /********************************************
    ******* End :: Delete Data Function *******
    *********************************************/

    /********************************************************
    ******* Start :: Bulk Action Delete Data Functions *******
    *********************************************************/
    
    function bulk_action_delete(table,url,id,rows){
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6610f2',
            cancelButtonColor: '#fd397a',
            confirmButtonText: 'Yes, delete!'
        }).then((result) => {
            if (result.value) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {id:id},
                    dataType: 'json'
                })
                .done(function(response){
                        if (response.status == 'success') {
                        Swal.fire("Deleted!", response.message, "success" ).then(function () {
                            // table.ajax.reload();
                            $('.selectall').prop('checked',false);
                            table.rows(rows).remove().draw(false);
                            // table.ajax.reload();
                        });
                    } else if (response.status == 'danger') {
                        Swal.fire('Error deleting!', response.message,'error');
                    }
                })
                .fail(function(){
                    swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                });
            }
        })
    }
    /********************************************************
    ******* End :: Bulk Action Delete Data Functions *******
    *********************************************************/

    
    @if (isset($status))
    bootstrap_notify("{{$status}}","{{$message}}");
    @endif

    </script>
    <script src="./public/js/dashboard.js" type="text/javascript"></script>
</body>

<!-- end::Body -->

</html>
