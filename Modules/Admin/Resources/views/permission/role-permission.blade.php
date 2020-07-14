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

        <div class="kt-portlet__body pt-0">
            <div class="col-md-12" id="store-role-permission">
                @if (Helper::permission('role-permission'))
                <form method="post" id="saveDataForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 mb-50px required">
                            <label>Role</label>
                            <select  class="form-control selectpicker" name="role_id" id="role_id" onchange="get_role_permission(this.value)" data-live-search="true" data-live-search-placeholder="Search" title="Choose one of the following role">
                                @if (!empty($roles))
                                    @foreach ($roles as $role)
                                    <option value="{{$role->id}}">{{$role->role}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-12" style="position:relative;">
                            <ul id="permission" class="text-left"></ul>

                            <div class="col-md-12 text-center content-loading"><img class="loading-image" src="./public/svg/table-loading.svg" /></div>
                        </div>
                        <div class="col-md-12 btn-section pt-4" style="display:none;">
                            <button type="button" class="btn btn-brand btn-sm" id="save-btn">Save</button>
                        </div>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')

<script  type="text/javascript">

 /** BEGIN:: DATA ADD/UPDATE AJAX CODE **/
 $(document).on('click','#save-btn',function () {
    var url = "{{route('admin.role.permission.store')}}";
    var role_id = $("#role_id option:selected").val();

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
            bootstrap_notify(data.status,data.message);
            if(data.status == 'success'){
                get_role_permission(role_id);
            } 
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
});
/** END:: DATA ADD/UPDATE AJAX CODE **/




function get_role_permission(role_id){
    var url     = "{{route('admin.role.permission.get')}}";
    var _token  = "{{csrf_token()}}";
    if(role_id){
        $.ajax({
            url: url,
            type: "POST",
            data: {role_id:role_id,_token:_token},
            beforeSend: function () {
                $('.content-loading').show();
            },
            complete: function(){
                $('.content-loading').hide();
            },
            success: function (data) {
                if (data) {
                    $('#permission').html(data);                  
                    $('#permission').treed();
                    $('.btn-section').show();
                    $('input[type=checkbox]').click(function(){
                        $(this).next().find('input[type=checkbox]').prop('checked',this.checked);
                        $(this).parents('ul').prev('input[type=checkbox]').prop('checked',function(){
                            return $(this).next().find(':checked').length;
                        });
                    });

                } else {
                    $('#permission').html('');
                    $('.btn-section').hide();
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }else{
        bootstrap_notify(error = "error",message = "Please select role");
    }
    
}


</script>
@endpush
