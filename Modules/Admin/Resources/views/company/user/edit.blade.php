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
            <a class="kt-subheader__breadcrumbs-link"><i class="la la-angle-double-right"></i>  Manage Company </a>
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
        <form method="post" id="saveDataForm">
            @csrf
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
                    
                        @if (Helper::permission('company-user-add'))
                        <button type="button" id="save-btn" class="btn btn-brand btn-icon-sm btn-sm mr-2">
                            <i class="fas fa-redo-alt"></i> Update
                        </button>
                        @endif
                        <a href="{{route('admin.company.user')}}" class="btn btn-danger btn-icon-sm btn-sm">
                            <i class="la la-long-arrow-left"></i>
                            Cancel
                        </a>
                    </div>
                </div>
            </div>

            <div class="kt-portlet__body pt-0">
                <div class="col-md-12" id="store-role-permission">
                    <input type="hidden" name="user_id" value="{{$data['user']['id']}}">
                    <fieldset>
                        <legend>User Details</legend>
                        <div class="form-group row">
                            <div class="col-md-3 required mb-2">
                                <label for="company_id" class="form-control-label">Company</label>
                                <select  class="form-control selectpicker" name="company_id" id="company_id" onchange="get_role_branch(this.value)" data-live-search="true" data-live-search-placeholder="Search" title="Select Please">
                                    <option value="">Select Please</option>
                                    @if (!empty($data['companies']))
                                        @foreach ($data['companies'] as $company)
                                            <option @if ($data['user']['company_id'] == $company->id)
                                                selected
                                            @endif value="{{$company->id}}">{{$company->company_name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="branch_id">Branch</label>
                                <select  class="form-control selectpicker" name="branch_id" id="branch_id" data-live-search="true" data-live-search-placeholder="Search" title="Select Please">
                                    <option value=""></option>
                                    @if (!empty($data['branches']))
                                        @foreach ($data['branches'] as $branch)
                                        <option @if($branch->id == $data['user']['branch_id'])  selected @endif value="{{$branch->id}}">{{$branch->branch_name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3 required mb-2">
                                <label for="role_id">Role</label>
                                <select  class="form-control selectpicker" name="role_id" id="role_id" data-live-search="true" data-live-search-placeholder="Search" title="Select Please">
                                    <option value=""></option>
                                    @if (!empty($data['roles']))
                                        @foreach ($data['roles'] as $role)
                                        <option @if($role->id == $data['user']['role_id'])  selected @endif value="{{$role->id}}">{{$role->role}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3 required mb-2">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name" id="name" value="{{$data['user']['name']}}">
                            </div>
                            
                            <div class="col-md-3 required mb-2">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" name="email" id="email" value="{{$data['user']['email']}}">
                            </div>
                            <div class="col-md-3 required mb-2">
                                <label for="mobile">Mobile No.</label>
                                <input type="text" class="form-control" name="mobile" id="mobile" value="{{$data['user']['mobile']}}">
                            </div>
                            <div class="col-md-3 required mb-2">
                                <label for="gender" class="form-control-label">Gender</label>
                                <select  class="form-control selectpicker" name="gender" id="gender" data-live-search="true" data-live-search-placeholder="Search" title="Select Please">
                                    <option @if ($data['user']['gender'] == 1) selected @endif value="1">Male</option>
                                    <option @if ($data['user']['gender'] == 2) selected @endif value="2">Female</option>
                                </select>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset id="password_section">
                        <legend>User Permission</legend>
                        <div class="row">
                            <div class="col-md-12" style="position:relative;">
                                <ul id="permission" class="text-left">
                                    {!! $data['permission'] !!}
                                </ul>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')

<script  type="text/javascript">
$(document).ready(function(){
    $('#permission').treed();
    $('input[type=checkbox]').click(function(){
        $(this).next().find('input[type=checkbox]').prop('checked',this.checked);
        $(this).parents('ul').prev('input[type=checkbox]').prop('checked',function(){
            return $(this).next().find(':checked').length;
        });
    });
});

 /** BEGIN:: DATA ADD/UPDATE AJAX CODE **/
 $(document).on('click','#save-btn',function () {
    var url = "{{route('admin.company.user.update')}}";
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
                    window.location.replace("{{route('admin.company.user')}}");
                }
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
/** END:: DATA ADD/UPDATE AJAX CODE **/

function get_role_branch(company_id){
    var url     = "{{route('admin.company.user.get-role-branch')}}";
    var _token  = "{{csrf_token()}}";
    if(company_id){
        $.ajax({
            url: url,
            type: "POST",
            data: {company_id:company_id,_token:_token},
            beforeSend: function () {
                $('#save-btn').addClass('kt-spinner kt-spinner--md kt-spinner--light');
            },
            complete: function(){
                $('#save-btn').removeClass('kt-spinner kt-spinner--md kt-spinner--light');
            },
            success: function (data) {
                if (data) {
                    if(data.role){
                        $('#role_id').html(data.role);
                    }
                    if(data.branch){
                        $('#branch_id').html(data.branch);
                    }
                    $('.selectpicker').selectpicker('refresh');
                } else {
                    $('#branch_id').html('');
                    $('#role_id').html('');
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }else{
        bootstrap_notify(error = "error",message = "Please select company");
    }
    
}
</script>
@endpush
