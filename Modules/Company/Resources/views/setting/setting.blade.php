@extends('company.layouts.app')

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
    <form id="saveDataForm" method="POST" enctype="multipart/form-data">
        @csrf
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
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        @if (Helper::permission('setting-manage'))
                        <button type="submit" id="save-btn" class="btn btn-brand btn-icon-sm btn-sm">
                            <i class="fas fa-refresh"></i> Update
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="row user">
                    <div class="col-md-3">
                        <div class="tile p-0">
                            <ul class="nav flex-column nav-tabs user-tabs" id="setting-tab">
                                @if (Helper::permission('setting-manage'))
                                <li class="nav-item"><a class="nav-link active" href="#general" data-toggle="tab">General</a></li>
                                <li class="nav-item"><a class="nav-link" href="#site-logo" data-toggle="tab">Site Logo</a></li>
                                <li class="nav-item"><a class="nav-link" href="#invoice" data-toggle="tab">Invoice</a></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="tab-content">
                            @if (Helper::permission('setting-manage'))
                            <div class="tab-pane active" id="general">
                                @include('company::setting.include.general')
                            </div>
                            <div class="tab-pane fade" id="site-logo">
                                @include('company::setting.include.logo')
                            </div>
                            <div class="tab-pane fade" id="invoice">
                                @include('company::setting.include.invoice')
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
loadFile = function(event, id) {
    var output = document.getElementById(id);
    output.src = URL.createObjectURL(event.target.files[0]);
};
$(document).ready(function(){
    $('#saveDataForm').on('submit', function(event){
        event.preventDefault();
        $.ajax({
            url: "{{route('setting.update')}}",
            type: "POST",
            data: new FormData(this),
            dataType: "JSON",
            contentType:false,
            cache:false,
            processData:false,
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
                } else {
                    $.each(data.errors, function (key, value) {
                        $("#saveDataForm input[name='"+key+"']").addClass('is-invalid');
                        $("#saveDataForm select#" + key).parent().addClass('is-invalid');
                        $("#saveDataForm textarea[name='" + key + "']").parent().addClass('is-invalid');
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
    });
});

</script>
@endpush
