@extends('company.layouts.app')

@section('title')
    {{ucwords($page_title)}}
@endsection

@push('styles')
<link rel="stylesheet" href="./public/css/bootstrap-datepicker.min.css" />
@endpush

@section('content')
<!-- begin:: Subheader -->
<div class="kt-subheader   kt-grid__item" id="kt_subheader">
    <div class="kt-subheader__main">
        {{-- <h3 class="kt-subheader__title"> Dashboard </h3> --}}
        <span class="kt-subheader__separator kt-hidden"></span>
        <div class="kt-subheader__breadcrumbs">
            <a href="{{route('dashboard')}}" class="kt-subheader__breadcrumbs-link"><i class="m-nav__link-icon la la-home"></i>  Dashboard </a>
            <a class="kt-subheader__breadcrumbs-link"><i class="la la-angle-double-right"></i>  Accounts </a>
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
        @if (Helper::permission('purchase-bulk-action-delete') || Helper::permission('purchase-report'))
        <div class="row py-3">
            <div class="dataTableButton text-right col-md-12 col-sm-12 px-0 d-flex justify-content-center">
               
                @if (Helper::permission('purchase-report'))
                <div id="colvis-btn"></div>
                <div class="dropdown dropdown-inline">
                    <button type="button" class="btn btn-sm btn-label-info btn-bold mx-1" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-file-download"></i> Export
                    </button>
                    <div id="btn_group" class="dropdown-menu dropdown-menu-right"></div>
                </div>
                @endif
                @if (Helper::permission('purchase-bulk-action-delete'))
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
                    @if (Helper::permission('purchase-add'))
                    <a type="button" href="{{route('pos')}}" class="btn btn-brand btn-icon-sm btn-sm">
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
                                <div class="form-group col-md-4 kt-margin-b-20-tablet-and-mobile">
                                    <label for="from_date">From Date</label>
                                    <input type="text" class="form-control date" name="from_date" id="from_date" placeholder="Enter frmo date" />
                                </div>
                                <div class="form-group col-md-4 kt-margin-b-20-tablet-and-mobile">
                                    <label for="to_date">To Date</label>
                                    <input type="text" class="form-control date" name="to_date" id="to_date" placeholder="Enter to date" />
                                </div>
                                <div class="form-group col-md-4 kt-margin-b-20-tablet-and-mobile">
                                    <label for="customer_id">Customer</label>
                                    <select class="form-control selectpicker" name="customer_id" id="customer_id"  data-live-search="true" data-live-search-placeholder="Search" title="Select Please">
                                        <option value="">Select Please</option>
                                        @if (!empty($customers))
                                            @foreach($customers as $customer)
                                                    <option value="{{$customer->id}}">{{$customer->name.' - '.$customer->mobile.' ('.$customer->email.')'}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group col-md-4 kt-margin-b-20-tablet-and-mobile">
                                    <label for="table_no">Table</label>
                                    <select class="form-control selectpicker" name="table_no" id="table_no"  data-live-search="true" data-live-search-placeholder="Search" title="Select Please">
                                        <option value="">Select Please</option>
                                        @if (!empty($tables))
                                            @foreach($tables as $table)
                                                    <option value="{{$table->id}}">{{$table->table_no}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group col-md-4 kt-margin-b-20-tablet-and-mobile">
                                    <label for="status">Order Status</label>
                                    <select class="form-control selectpicker" name="status" id="status"  data-live-search="true" data-live-search-placeholder="Search" title="Select Please">
                                        <option value="">Select Please</option>
                                        @foreach (ORDER_STATUS as $key => $value)
                                            <option value="{{$key}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4 kt-margin-b-20-tablet-and-mobile">
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
                        @if (Helper::permission('purchase-bulk-action-delete'))
                        <th><label class="kt-checkbox kt-checkbox--single kt-checkbox--all kt-checkbox--solid"><input type="checkbox" class="selectall" onchange="select_all()">&nbsp;<span></span></label></th>
                        @endif
                        <th>SR</th>
                        <th>Table</th>
                        <th>Customer</th>
                        <th>Total Item</th>
                        <th>Total Qty</th>
                        <th class="text-right">Amount</th>
                        <th class="text-right">Dicount</th>
                        <th class="text-right">Adjustment</th>
                        <th class="text-right">Vat</th>
                        <th class="text-right">Grand Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>  
            <!--end: Datatable -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="./public/js/bootstrap-datepicker.min.js"></script>
<script>

var table;
$(document).ready(function () {
    $(".date").datepicker({
        autoclose: true,
        todayBtn: "linked",
        todayHighlight: !0,
        orientation: "bottom left",
        format: "yyyy-mm-dd",
        templates: {
            leftArrow: '<i class="fa fa-angle-left"></i>',
            rightArrow: '<i class="fa fa-angle-right"></i>'
        }
    });
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
        "pageLength": 10,
        "language": {
            processing: '<img class="loading-image" src="./public/svg/table-loading.svg" />',
            emptyTable: '<strong class="text-danger">No Data Found</strong>',
            infoEmpty: '',
            zeroRecords: '<strong class="text-danger">No Data Found</strong>',
        },
        // Load data for the table's content from an Ajax source//
        "ajax": {
            "url": "{{route('sale.list')}}",
            "type": "POST",
            "data": function (data) {
                data.from_date        = $('#form-filter #from_date').val();
                data.to_date          = $('#form-filter #to_date').val();
                data.customer_id      = $('#form-filter #customer_id').val();
                data.table_no         = $('#form-filter #table_no').val();
                data.status           = $('#form-filter #status').val();
                data.order_type       = "all";
                data._token           = "{{csrf_token()}}";
            }
        },

        //Set column definition initialisation properties.
        "columnDefs": [
            {
                "targets": [0,1,13],
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
                orientation: 'landscape',//'landscape', //portrait
                pageSize: 'A4', //A3 , A5 , A6 , legal , letter
                exportOptions: {
                    @if (Helper::permission('purchase-bulk-action-delete'))
                    columns: [1,2,3,4,5,6,7,8,9,10]
                    @else
                    columns: [0,1,2,3,4,5,6,7,8,9]
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
                filename: 'purchase-report',
                exportOptions: {
                    @if (Helper::permission('purchase-bulk-action-delete'))
                    columns: [1,2,3,4,5,6,7,8,9,10]
                    @else
                    columns: [0,1,2,3,4,5,6,7,8,9]
                    @endif
                }
            },
            {
                extend: 'csv',
                title: "{{ucwords($sub_title)}}",
                filename: 'purchase-report',
                exportOptions: {
                    @if (Helper::permission('purchase-bulk-action-delete'))
                    columns: [1,2,3,4,5,6,7,8,9,10]
                    @else
                    columns: [0,1,2,3,4,5,6,7,8,9]
                    @endif
                }
            },
            {
                extend: 'pdf',
                title: "{{ucwords($sub_title)}}",
                filename: 'purchase-report',
                orientation: 'portrait', //landscape
                pageSize: 'A4', //A3 , A5 , A6 , legal , letter
                exportOptions: {
                    @if (Helper::permission('purchase-bulk-action-delete'))
                    columns: [1,2,3,4,5,6,7,8,9,10]
                    @else
                    columns: [0,1,2,3,4,5,6,7,8,9]
                    @endif
                },
                customize: function ( doc ) {
                    doc.content[1].table.widths = ['8%','10%','12%','10%','10%','10%','10%','10%','10%'];
						//Remove the title created by datatTables
						doc.content.splice(0,1);
						//Create a date string that we use in the footer. Format is dd-mm-yyyy
						var now = new Date();
						var jsDate = now.getDate()+'.'+(now.getMonth()+1)+'.'+now.getFullYear();

						doc.pageMargins = [20,60,20,30];
						// Set the font size fot the entire document
						doc.defaultStyle.fontSize = 7;
						// Set the fontsize for the table header
						doc.styles.tableHeader.fontSize = 7;
						// Create a header object with 1 columns
                        //Site title and page title
						doc['header']=(function() {
							return {
								columns: [
                                    {
                                        alignment: 'center',
                                        text: ['\n{{Auth::user()->company->company_name}}','\n{{ucwords($page_title)}} Report'],
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
										text: ['Created on: ', '{{date("j-M-Y")}}']
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

    //BEGIN: DELETE ROLE DATA CODE
    $(document).on('click','.delete_data',function () {
        var row = table.row( $(this).parents('tr') );
        var id  = $(this).data('id');
        var url = "{{route('purchase.delete')}}";
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
            var url = "{{route('purchase.bulkaction')}}";
            bulk_action_delete(table,url,id,rows);
        }
    });
    //END: BULK ACTION DELETE AJAX CODE
}); 
</script>
@endpush