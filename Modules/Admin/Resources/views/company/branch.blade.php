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
        @if (Helper::permission('branch-bulk-action-delete') || Helper::permission('company-module-report'))
        <div class="row py-3">
            <div class="dataTableButton text-right col-md-12 col-sm-12 px-0 d-flex justify-content-center">
                
                @if (Helper::permission('company-module-report'))
                <div id="colvis-btn"></div>
                <div class="dropdown dropdown-inline">
                    <button type="button" class="btn btn-sm btn-label-info btn-bold mx-1" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-file-download"></i> Export
                    </button>
                    <div id="btn_group" class="dropdown-menu dropdown-menu-right"></div>
                </div>
                @endif
                @if (Helper::permission('branch-bulk-action-delete'))
                <button class="btn btn-sm btn-label-danger btn-bold" type="button" id="bulk_action_delete"><i
                        class="kt-nav__link-icon flaticon2-trash"></i> Delete All</button>
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
                    @if (Helper::permission('company-module-add'))
                    <button type="button" id="showModal" onclick="show_modal(modal_title='Add New Branch',btn_text='Save')" class="btn btn-brand btn-icon-sm btn-sm">
                        <i class="fas fa-plus-square"></i> Add New
                    </button>
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
                                <div class="col-md-4 kt-margin-b-20-tablet-and-mobile">
                                    <label>Branch Name</label>
                                    <input type="text" class="form-control" name="branch_name" id="branch_name" placeholder="Enter branch name" />
                                </div>
                                <div class="col-md-4 kt-margin-b-20-tablet-and-mobile">
                                    <label>Company</label>
                                    <select  class="form-control selectpicker" name="companyID" id="companyID" data-live-search="true" data-live-search-placeholder="Search" title="Select Please">
                                        <option value="">Select Please</option>
                                        @if (!empty($data['companies']))
                                            @foreach ($data['companies'] as $company)
                                                <option value="{{$company->id}}">{{$company->company_name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-4 kt-margin-b-20-tablet-and-mobile">
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
                        @if (Helper::permission('branch-bulk-action-delete'))
                        <th><label class="kt-checkbox kt-checkbox--single kt-checkbox--all kt-checkbox--solid"><input type="checkbox" class="selectall" onchange="select_all()">&nbsp;<span></span></label></th>
                        @endif
                        <th>SR</th>
                        <th>Company Name</th>
                        <th>Branch Name</th>
                        <th>Branch Email</th>
                        <th>Branch Mobile</th>
                        <th>Branch Phone</th>
                        @if (Helper::permission('branch-change-status'))
                        <th>Status</th>
                        @endif
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>  
            <!--end: Datatable -->
        </div>

        <div class="modal fade" id="saveDataModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-brand" id="modalTitle"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form method="POST" id="saveDataForm">
                        @csrf
                        <input type="hidden" class="form-control" name="branch_id" id="update_id">
                        <div class="modal-body">
                            <div class="form-group row ">
                                <div class="col-lg-6 mb-2 required">
                                    <label for="company_id" class="form-control-label">Company</label>
                                    <select  class="form-control selectpicker" name="company_id" id="company_id" data-live-search="true" data-live-search-placeholder="Search" title="Select Please">
                                        <option value="">Select Please</option>
                                        @if (!empty($data['companies']))
                                            @foreach ($data['companies'] as $company)
                                                <option value="{{$company->id}}">{{$company->company_name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-lg-6 mb-2 required">
                                    <label for="branch_name" class="form-control-label">Branch Name</label>
                                    <input type="text" class="form-control" name="branch_name" id="branch_name"  placeholder="Enter branch name" onkeyup="url_generator(this.value, output_id='module_link')">
                                </div>
                                <div class="col-lg-6 mb-2">
                                    <label for="branch_email" class="form-control-label">Branch Email</label>
                                    <input type="text" class="form-control" name="branch_email" id="branch_email">
                                </div>
                                <div class="col-lg-6 mb-2 required">
                                    <label for="branch_mobile" class="form-control-label">Branch Mobile No.</label>
                                    <input type="text" class="form-control" name="branch_mobile" id="branch_mobile">
                                </div>
                                <div class="col-lg-6 mb-2">
                                    <label for="branch_phone" class="form-control-label">Branch Phone No.</label>
                                    <input type="text" class="form-control" name="branch_phone" id="branch_phone">
                                </div>
                                <div class="col-lg-6 mb-2">
                                    <label for="branch_address" class="form-control-label">Branch Address</label>
                                    <textarea class="form-control" name="branch_address" id="branch_address"></textarea>
                                </div>
                            </div>        
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-brand btn-sm" id="save-btn"></button>
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
            "url": "{{route('admin.company.branch.list')}}",
            "type": "POST",
            "data": function (data) {
                data.branch_name    = $('#form-filter #branch_name').val();
                data.companyID      = $('#form-filter #companyID').val();
                data._token         = "{{csrf_token()}}";
            }
        },

        //Set column definition initialisation properties.
        "columnDefs": [
            {
                @if (Helper::permission('branch-bulk-action-delete') && Helper::permission('branch-change-status'))
                "targets": [0,8], //first column / numbering column
                @elseif (!Helper::permission('branch-bulk-action-delete') && !Helper::permission('branch-change-status'))
                "targets": [0,7],
                @elseif (Helper::permission('branch-bulk-action-delete') && !Helper::permission('branch-change-status'))
                "targets": [0,7],
                @else
                "targets": [6],
                @endif
                "orderable": false, //set not orderable
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
                orientation: 'portrait', //landscape
                pageSize: 'A4', //A3 , A5 , A6 , legal , letter
                exportOptions: {
                    @if (Helper::permission('branch-bulk-action-delete') && Helper::permission('branch-change-status'))
                    columns: [1,2,3,4,5,6]
                    @elseif (!Helper::permission('branch-bulk-action-delete') && !Helper::permission('branch-change-status'))
                    columns: [0,1,2,3,4,5]
                    @elseif (Helper::permission('branch-bulk-action-delete') && !Helper::permission('branch-change-status'))
                    columns: [1,2,3,4,5,6]
                    @else
                    columns: [0,1,2,3,4,5]
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
                filename: 'company-module-report',
                exportOptions: {
                    @if (Helper::permission('branch-bulk-action-delete') && Helper::permission('branch-change-status'))
                    columns: [1,2,3,4,5,6]
                    @elseif (!Helper::permission('branch-bulk-action-delete') && !Helper::permission('branch-change-status'))
                    columns: [0,1,2,3,4,5]
                    @elseif (Helper::permission('branch-bulk-action-delete') && !Helper::permission('branch-change-status'))
                    columns: [1,2,3,4,5,6]
                    @else
                    columns: [0,1,2,3,4,5]
                    @endif
                }
            },
            {
                extend: 'csv',
                title: "{{ucwords($sub_title)}}",
                filename: 'company-module-report',
                exportOptions: {
                    @if (Helper::permission('branch-bulk-action-delete') && Helper::permission('branch-change-status'))
                    columns: [1,2,3,4,5,6]
                    @elseif (!Helper::permission('branch-bulk-action-delete') && !Helper::permission('branch-change-status'))
                    columns: [0,1,2,3,4,5]
                    @elseif (Helper::permission('branch-bulk-action-delete') && !Helper::permission('branch-change-status'))
                    columns: [1,2,3,4,5,6]
                    @else
                    columns: [0,1,2,3,4,5]
                    @endif
                }
            },
            {
                extend: 'pdf',
                title: "{{ucwords($sub_title)}}",
                filename: 'company-module-report',
                orientation: 'portrait', //landscape
                pageSize: 'A4', //A3 , A5 , A6 , legal , letter
                exportOptions: {
                    @if (Helper::permission('branch-bulk-action-delete') && Helper::permission('branch-change-status'))
                    columns: [1,2,3,4,5,6]
                    @elseif (!Helper::permission('branch-bulk-action-delete') && !Helper::permission('branch-change-status'))
                    columns: [0,1,2,3,4,5]
                    @elseif (Helper::permission('branch-bulk-action-delete') && !Helper::permission('branch-change-status'))
                    columns: [1,2,3,4,5,6]
                    @else
                    columns: [0,1,2,3,4,5]
                    @endif
                },
                customize: function ( doc ) {
                    doc.content[1].table.widths = [
                    '10%',
                    '15%',
                    '15%',
                    '30%',
                    '15%',
                    '15%',
                    ];
						//Remove the title created by datatTables
						doc.content.splice(0,1);
						//Create a date string that we use in the footer. Format is dd-mm-yyyy
						var now = new Date();
						var jsDate = now.getDate()+'.'+(now.getMonth()+1)+'.'+now.getFullYear();

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
                                        alignment: 'center',
                                        // italics: true,
                                        text: ['\n{{ config("settings.site_title") }}','\n{{ucwords($page_title)}} Report'],
                                        fontSize: 8,
                                        margin: [20,0],
                                        width:600,
                                    },
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
										text: ['Created on: ','{{date("j-M-Y")}}']
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
						var objLayout             = {};
						objLayout['hLineWidth']   = function(i) { return .5; };
						objLayout['vLineWidth']   = function(i) { return .5; };
						objLayout['hLineColor']   = function(i) { return '#aaa'; };
						objLayout['vLineColor']   = function(i) { return '#aaa'; };
						objLayout['paddingLeft']  = function(i) { return 4; };
						objLayout['paddingRight'] = function(i) { return 4; };
						doc.content[0].layout     = objLayout;
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

    /** BEGIN:: DATA ADD/UPDATE AJAX CODE **/
    $(document).on('click','#save-btn',function () {
        
        var id  = $('#update_id').val();
        if(id){
            var method = 'update';
            var url = "{{route('admin.company.branch.update')}}";
        }else{
            var method = 'add';
            var url = "{{route('admin.company.branch.store')}}";
        }
        store_data(table,url,method);
    });
    /** END:: DATA ADD/UPDATE AJAX CODE **/

    //BEGIN: FETCHING DATA AJAX CODE
    $(document).on('click','.edit_data',function () {
        var id     = $(this).data('id');
        var _token = "{{csrf_token()}}";
        $('#saveDataForm')[0].reset(); // reset form on show modals
        $(".error").each(function () {
            $(this).empty();//remove error text
        });
        $("#saveDataForm").find('.is-invalid').removeClass('is-invalid');//remover red border color
        $('.selectpicker').selectpicker('refresh');
        $.ajax({
            url: "{{route('admin.company.branch.edit')}}",
            type: "POST",
            data:{id:id,_token:_token},
            dataType: "JSON",
            success: function (data) {
                $('#saveDataForm #update_id').val(data.branch.id);
                $('#saveDataForm #branch_name').val(data.branch.branch_name);
                $('#saveDataForm #branch_slug').val(data.branch.branch_slug);
                $('#saveDataForm #branch_email').val(data.branch.branch_email);
                $('#saveDataForm #branch_mobile').val(data.branch.branch_mobile);
                $('#saveDataForm #branch_phone').val(data.branch.branch_phone);
                $('#saveDataForm #branch_address').val(data.branch.branch_address);
                $('#saveDataForm select[name="company_id"]').val(data.branch.company_id);
                $('#saveDataForm .selectpicker').selectpicker('refresh');
                $('#saveDataModal').modal({
                    keyboard: false,
                    backdrop: 'static', //make modal static
                });
                $('.modal-title').html('<i class="fas fa-edit"></i> <span>Edit Branch</span>');
                $('#save-btn').text('Update');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });
    //END: FETCHING DATA AJAX CODE

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
                url: "{{route('admin.company.branch.change-status')}}",
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

    //BEGIN: DELETE ROLE DATA CODE
    $(document).on('click','.delete_data',function () {
        var row = table.row( $(this).parents('tr') );
        var id  = $(this).data('id');
        var url = "{{route('admin.company.branch.delete')}}";
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
        if(id.length === 0) //tell us if the array is empty
        {
            Swal.fire({
                type: 'error',
                title: 'Error',
                text: 'Please checked at least one row of table!',
            })
        }
        else{
            var url = "{{route('admin.company.branch.bulkaction')}}";
            bulk_action_delete(table,url,id,rows);
        }
    });
    //END: BULK ACTION DELETE AJAX CODE
}); 

</script>
@endpush