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
<div class="kt-content  kt-grid__item kt-grid__item--fluid p-0" id="kt_content">
    <div class="col-xl-12">
        @include('flash')
    </div>
    <div class="kt-portlet kt-portlet--mobile">
        @if (Helper::permission('company-user-bulk-action-delete') || Helper::permission('company-user-report'))
        <div class="row py-3">
            <div class="dataTableButton text-right col-md-12 col-sm-12 px-0 d-flex justify-content-center">
                @if (Helper::permission('company-user-report'))
                <div id="colvis-btn"></div>
                <div class="dropdown dropdown-inline">
                    <button type="button" class="btn btn-sm btn-label-info btn-bold mx-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-file-download"></i> Export
                    </button>
                    <div id="btn_group" class="dropdown-menu dropdown-menu-right"></div>
                </div>
                @endif
                @if (Helper::permission('company-user-bulk-action-delete'))
                <button class="btn btn-sm btn-label-danger btn-bold" type="button" id="bulk_action_delete"><i class="kt-nav__link-icon flaticon2-trash"></i> Delete All</button>
                @endif
            </div>
        </div>
        @endif
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
                    {{-- <a href="{{route('dashboard')}}" class="btn btn-clean btn-icon-sm mr-2">
                        <i class="la la-long-arrow-left"></i>
                        Back
                    </a> --}}
                    @if (Helper::permission('company-user-add'))
                    <a href="{{route('admin.company.user.create')}}" type="button" id="showModal" class="btn btn-brand btn-icon-sm btn-sm">
                        <i class="fas fa-plus-square"></i> Add New
                    </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="kt-portlet__body">
            <!--begin: Search Form -->
            <div class="kt-form kt-form--label-right kt-margin-b-10">
                <div class="row align-items-center">
                    
                        
                    <div class="col-xl-12 order-2 order-xl-1 py-25px">
                        <form method="POST" id="form-filter" class="m-form m-form--fit m--margin-bottom-20" role="form">
                            <div class="row align-items-center">
                                <div class="col-md-6 kt-margin-b-20-tablet-and-mobile">
                                    <label>Department</label>
                                    <input type="text" class="form-control" name="department" id="department_name" placeholder="Enter department name" />
                                </div>
                                <div class="col-md-6 kt-margin-b-20-tablet-and-mobile">
                                    <div class="mt-25px">    
                                        <button id="btn-reset" class="btn btn-danger btn-sm btn-elevate btn-icon pull-right" type="button"
                                        data-skin="dark" data-toggle="kt-tooltip" data-placement="top" title="" data-original-title="Reset">
                                        <i class="fas fa-undo-alt"></i></button>

                                        <button id="btn-filter" class="btn btn-info btn-sm btn-elevate btn-icon mr-2 pull-right" type="button"
                                        data-skin="dark" data-toggle="kt-tooltip" data-placement="top" title="" data-original-title="Search">
                                        <i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!--end: Search Form -->
        </div>
        <div class="kt-portlet__body pt-0">
            <!--begin: Datatable -->

            <table class="table table-striped- table-bordered table-hover table-checkable" id="dataTable">
                <thead>
                    <tr>
                        @if (Helper::permission('company-user-bulk-action-delete'))
                        <th><label class="kt-checkbox kt-checkbox--single kt-checkbox--all kt-checkbox--solid"><input type="checkbox" class="selectall" onchange="select_all()">&nbsp;<span></span></label></th>
                        @endif
                        <th>SR</th>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Company Name</th>
                        <th>Branch Name</th>
                        <th>Role</th>
                        <th>Login At</th>
                        <th>Login IP</th>
                        @if (Helper::permission('company-user-change-status'))
                        <th>Status</th>
                        @endif
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>  
                

            <!--end: Datatable -->
        </div>


        <!-- View Modal -->
        <div class="modal fade" id="viewDataModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-brand" id="modalTitle">User Data</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
    
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-brand btn-sm">More Details</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="passChangeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-brand" id="modalTitle"></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                        <form method="POST" id="passChangeForm">
                            @csrf
                            <input type="hidden" class="form-control" name="user_id" id="update_pass_id">
                            <div class="modal-body">
                                <div class="form-group required">
                                    <label for="password" class="form-control-label">Password</label>
                                    <input type="text" class="form-control" name="password" id="password">
                                </div>
                                <div class="form-group required">
                                    <label for="password_confirmation" class="form-control-label">Password Confirm</label>
                                    <input type="text" class="form-control" name="password_confirmation" id="password_confirmation">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-brand btn-sm" id="update-pass-btn">Update Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
var table;
$(document).ready(function () {
   
    /** BEGIN:: DATATABLE SERVER SIDE CODE **/
    var table = $('#dataTable').DataTable({

        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.
        "responsive": true, //make able resposive in mobile devices
        "bInfo": true, //to show the total number of data showing
        "bFilter": false, //for datatable default search box
        "lengthMenu": [
            [5, 10, 15, 25, 50, 100, -1],
            [5, 10, 15, 25, 50, 100, "All"]
        ],
        "pageLength": 25,
        "language": {
            processing: '<img class="loading-image" src="./public/svg/table-loading.svg" />',
            emptyTable: '<strong class="text-danger">No Data Found</strong>',
            infoEmpty: '',
            zeroRecords: '<strong class="text-danger">No Data Found</strong>',
        },

        // Load data for the table's content from an Ajax source//
        "ajax": {
            "url": "{{route('admin.company.user.list')}}",
            "type": "POST",
            "data": function (data) {
                data.name           = $('#form-filter #name').val();
                data.username       = $('#form-filter #username').val();
                data.email          = $('#form-filter #email').val();
                data.mobile         = $('#form-filter #mobile').val();
                data.office_id      = $('#form-filter #office_id').val();
                data.department     = $('#form-filter #department').val();
                data.designation    = $('#form-filter #designation').val();
                data.employee_id    = $('#form-filter #employee_id').val();
                data.profile_status = $('#form-filter #profile_status').val();
                data._token = "{{csrf_token()}}";
            }
        },

        //Set column definition initialisation properties.
        "columnDefs": [
            {
                "targets": [0,10], //first column / numbering column
                "orderable": false, //set not orderable
            }, 
            {
                "targets": [0,10],
                "className": "text-center",
            }
        ],


    });
    /** END:: DATATABLE SERVER SIDE CODE **/

    /** BEGIN:: DATATABLE SEARCH FORM BUTTON TRIGGER CODE **/
    $('#btn-filter').click(function () {
        table.ajax.reload();
    });

    $('#btn-reset').click(function () {
        $('#form-filter')[0].reset();
        $('#form-filter .selectpicker').selectpicker('refresh');
        table.ajax.reload();
    });
    /** END:: DATATABLE SEARCH FORM BUTTON TRIGGER CODE **/

    /** END:: DATATABLE BUTTONS **/
    new $.fn.dataTable.Buttons(table, {
    name:"export",   
    buttons: [     
            {
                extend: 'print',
                title: "{{ucwords($sub_title)}}",
                orientation: 'portrait',//'landscape', //portrait
                pageSize: 'A4', //A3 , A5 , A6 , legal , letter
                exportOptions: {
                   
                    @if(Helper::permission('company-user-bulk-action-delete'))
                    columns: [1,2,3,4,5,6,7,8]
                    @else
                    columns: [0,1,2,3,4,5,6,7]
                    @endif

                },
                customize: function(win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).find('table').addClass('display').css('font-size', '9px');
                    $(win.document.body).find('h1').css('text-align','center');
                }
            },
            {
                extend: 'copy'
            },
            {
                extend: 'excel',
                title: "{{ucwords($sub_title)}}",
                filename: 'company-user-report',
                exportOptions: {
                    @if(Helper::permission('company-user-bulk-action'))
                    columns: [1,2,3,4,5,6,7,8]
                    @else
                    columns: [0,1,2,3,4,5,6,7]
                    @endif

                }
            },
            {
                extend: 'csv',
                title: "{{ucwords($sub_title)}}",
                filename: 'company-user-report',
                exportOptions: {
                    @if(Helper::permission('company-user-bulk-action'))
                    columns: [1,2,3,4,5,6,7,8]
                    @else
                    columns: [0,1,2,3,4,5,6,7]
                    @endif

                }
            },
            {
                extend: 'pdf',
                title: "{{ucwords($sub_title)}}",
                filename: 'company-user-report',
                orientation: 'portrait', //landscape
                pageSize: 'A4', //A3 , A5 , A6 , legal , letter
                exportOptions: {
                    @if(Helper::permission('company-user-bulk-action'))
                    columns: [1,2,3,4,5,6,7,8]
                    @else
                    columns: [0,1,2,3,4,5,6,7]
                    @endif

                },
                customize: function ( doc ) {
                    doc.content[1].table.widths = [
                    '5%',
                    '30%',
                    '15%',
                    '10%',
                    '10%',
                    '10%',
                    '10%',
                    '10%',
                    ];
						//Remove the title created by datatTables
						doc.content.splice(0,1);
						//Create a date string that we use in the footer. Format is dd-mm-yyyy
						var now = new Date();
						var jsDate = now.getDate()+'.'+(now.getMonth()+1)+'.'+now.getFullYear();
						// Logo converted to base64
						// var logo = getBase64FromImageUrl('https://datatables.net/media/images/logo.png');
						// The above call should work, but not when called from codepen.io
						// So we use a online converter and paste the string in.
						// Done on http://codebeautify.org/image-to-base64-converter
						// It's a LONG string scroll down to see the rest of the code !!!
						var logo = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAACCCAIAAAA/nfqcAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA4tpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBN0RDQkZGMDAyODAxMUU5ODlEMTlCNEE0ODJBREVGQiIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBN0RDQkZFRjAyODAxMUU5ODlEMTlCNEE0ODJBREVGQiIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M2IFdpbmRvd3MiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0idXVpZDpmYWY1YmRkNS1iYTNkLTExZGEtYWQzMS1kMzNkNzUxODJmMWIiIHN0UmVmOmRvY3VtZW50SUQ9IkM0RjBBMkM5QkM5QzdGMkYzMjQ5QzJBRUFFOTNGRDA5Ii8+IDxkYzpjcmVhdG9yPiA8cmRmOlNlcT4gPHJkZjpsaT5Vc2VyPC9yZGY6bGk+IDwv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';
						// A documentation reference can be found at
						// https://github.com/bpampuch/pdfmake#getting-started
						// Set page margins [left,top,right,bottom] or [horizontal,vertical]
						// or one number for equal spread
						// It's important to create enough space at the top for a header !!!
						doc.pageMargins = [20,60,20,30];
						// Set the font size fot the entire document
						doc.defaultStyle.fontSize = 7;
						// Set the fontsize for the table header
						doc.styles.tableHeader.fontSize = 7;
						// Create a header object with 3 columns
						// Left side: Logo
						// Middle: brandname
						// Right side: A document title
						doc['header']=(function() {
							return {
								columns: [
									{
										image: logo,
										width: 20,
									},
									{
										alignment: 'right',
										// italics: true,
										text: 'User Report',
										fontSize: 12,
										margin: [20,0],
                                        width:300,
									},
									{
										alignment: 'right',
										fontSize: 12,
                                        width: 200,
										text: ['Date: ', { text: jsDate.toString() }]
									}
								],
								margin: 20
							}
						});
						// Create a footer object with 2 columns
						// Left side: report creation date
						// Right side: current page and total pages
						doc['footer']=(function(page, pages) {
							return {
								columns: [
									{
										alignment: 'left',
										text: ['Created on: ', { text: jsDate.toString() }]
									},
									{
										alignment: 'right',
										text: ['page ', { text: page.toString() },	' of ',	{ text: pages.toString() }]
									}
								],
								margin: 20,
                                
							}
						});
						// Change dataTable layout (Table styling)
						// To use predefined layouts uncomment the line below and comment the custom lines below
						// doc.content[0].layout = 'lightHorizontalLines'; // noBorders , headerLineOnly
						var objLayout = {};
						objLayout['hLineWidth'] = function(i) { return .5; };
						objLayout['vLineWidth'] = function(i) { return .5; };
						objLayout['hLineColor'] = function(i) { return '#aaa'; };
						objLayout['vLineColor'] = function(i) { return '#aaa'; };
						objLayout['paddingLeft'] = function(i) { return 4; };
						objLayout['paddingRight'] = function(i) { return 4; };
						doc.content[0].layout = objLayout;
                },
            },
            

           
        ]
    }).container().appendTo($('#btn_group'));
   
    new $.fn.dataTable.Buttons( table, {
        name: 'visiable',
        buttons:[
            {
               extend: 'colvis',
               name: 'colvis',
               text: 'Column',
               className: 'btn btn-sm btn-label-brand btn-bold'
            },
        ],
        
    });

    table.buttons('visiable',null).containers().appendTo($('.dataTableButton #colvis-btn'));
    /** END:: DATATABLE BUTTONS **/

    $(document).on('click','.change_password',function(){
        $('#passChangeForm')[0].reset(); //reset form
        var id = $(this).data('id');
        $('#update_pass_id').val(id);//empty id input field
        
        $(".error").each(function () {
            $(this).empty(); //remove error text
        });
        $("#passChangeForm").find('.is-invalid').removeClass('is-invalid'); //remover red border color

        $('#passChangeModal').modal({
            keyboard: false,
            backdrop: 'static', //make modal static
        });
        $('.modal-title').html('<i class="fas fa-key"></i> <span>Change Password</span>'); //set modal title
        // $('#save-btn').text('Update Password'); //set save button text
    });

    /** BEGIN:: DATA ADD/UPDATE AJAX CODE **/
    
    $(document).on('click','#update-pass-btn',function () {

        $.ajax({
            url: "{{route('admin.company.user.password.update')}}",
            type: "POST",
            data: $('#passChangeForm').serialize(),
            dataType: "JSON",
            beforeSend: function () {
                $('#update-pass-btn').addClass('kt-spinner kt-spinner--md kt-spinner--light');
            },
            complete: function(){
                $('#update-pass-btn').removeClass('kt-spinner kt-spinner--md kt-spinner--light');
            },
            success: function (data) {
                $("#passChangeForm").find('.is-invalid').removeClass('is-invalid');
                $("#passChangeForm").find('.error').remove();

                if (data.status) {
                    bootstrap_notify(data.status,data.message);
                    table.ajax.reload( null, false );
                    $('#passChangeModal').modal('hide');
                    
                } else {
                    $.each(data.errors, function (key, value) {
                        $('#passChangeForm .form-group').find('.error_'+key).text(value); 
                        $("#passChangeForm input[name='"+key+"']").addClass('is-invalid');
                        $("#passChangeForm input[name='"+key+"']").after('<div id="'+key+'" class="error invalid-feedback">'+value+'</div>');
                        
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });
    /** END:: DATA ADD/UPDATE AJAX CODE **/


    //BEGIN: FETCHING DATA AJAX CODE
    $(document).on('click','.view_data',function () {
        var id     = $(this).data('id');
        var _token = "{{csrf_token()}}";
        $.ajax({
            url: "{{route('admin.company.user.view')}}",
            type: "POST",
            data:{id:id,_token:_token},
            success: function (data) {
                // $('#viewDataModal').modal('show');
                $('#viewDataModal .modal-body').html('');
                $('#viewDataModal').modal({
                    keyboard: false,
                    backdrop: 'static', //make modal static
                });
                $('#viewDataModal .modal-body').html(data);
                $('#viewDataModal .modal-title').html('<i class="fas fa-eye"></i> <span>View User Data</span>');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });
    //END: FETCHING DATA AJAX CODE

    //BEGIN: DELETE ROLE DATA CODE
    $(document).on('click','.delete_data',function () {
        var row = table.row( $(this).parents('tr') );
        var id  = $(this).data('id');
        var url = "{{route('admin.company.user.delete')}}";
        delete_data(table,row,id,url);
    });
    //BEGIN: DELETE ROLE DATA CODE

    //BEGIN: SELECT ALL CHECKBOX CHECKED IF ANY ROW SELECTED CODE
    $(document).on('change','.select_data',function(){
        var total = $('.select_data').length;
        var number = $('.select_data:checked').length;
        if($(this).is(':checked'))
        {
            $(this).closest('tr').addClass('bg-danger');
            $(this).closest('tr').children('td').addClass('text-white');
        }
        else
        {
            $(this).closest('tr').removeClass('bg-danger');
            $(this).closest('tr').children('td').removeClass('text-white');
        }
        if(total == number){
            $('.selectall').prop('checked',true);
        }else{
            $('.selectall').prop('checked',false);
        }
    });
    //END: SELECT ALL CHECKBOX CHECKED IF ANY ROW SELECTED CODE


    //START: BULK ACTION DELETE AJAX CODE
    $(document).on("click",'#bulk_action_delete',function(e) {
        var id = [];
        var rows;
        $('.select_data:checked').each(function(i){
            id.push($(this).val());
            rows = table.rows( $('.select_data:checked').parents('tr') );
        });
        console.log(id);
        if(id.length === 0) //tell us if the array is empty
        {
            Swal.fire({
                type: 'error',
                title: 'Error',
                text: 'Please checked at least one row of table!',
            })
        }
        else{
            var url = "{{route('admin.company.user.bulkaction')}}";
            bulk_action_delete(table,url,id,rows);
        }
    });
    //END: BULK ACTION DELETE AJAX CODE

    //BEGIN:: STATUS CHANGE AJAX CODE
    $(document).on("change",".change_status", function() {
        if ($(this).is(":checked")) {
            status = 1;
            $(this).parents('.kt-switch').removeClass('kt-switch--danger').addClass('kt-switch--brand');
        } else {
            status = 2;
            $(this).parents('.kt-switch').removeClass('kt-switch--brand').addClass('kt-switch--danger');
        }
        id         = $(this).data("id");
        var _token = "{{csrf_token()}}";
        if(status && id){
            $.ajax({
                url: "{{route('admin.company.user.changestatus')}}",
                type: "POST",
                data: {id:id,status:status,_token:_token},
                dataType: "JSON",
                success: function (data) {
                    if (data.status) {
                        bootstrap_notify(data.status,data.message);
                        table.ajax.reload( null, false );     
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }
    });
    //END:: STATUS CHANGE AJAX CODE
}); 

</script>
@endpush