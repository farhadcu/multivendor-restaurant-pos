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
        @if (Helper::permission('transaction-bulk-action-delete') || Helper::permission('transaction-report'))
        <div class="row py-3">
            <div class="dataTableButton text-right col-md-12 col-sm-12 px-0 d-flex justify-content-center">
               
                @if (Helper::permission('transaction-report'))
                <div id="colvis-btn"></div>
                <div class="dropdown dropdown-inline">
                    <button type="button" class="btn btn-sm btn-label-info btn-bold mx-1" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-file-download"></i> Export
                    </button>
                    <div id="btn_group" class="dropdown-menu dropdown-menu-right"></div>
                </div>
                @endif
                @if (Helper::permission('transaction-bulk-action-delete'))
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
                    @if (Helper::permission('transaction-add'))
                    <button type="button" id="showModal" class="btn btn-brand btn-icon-sm btn-sm">
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
                                <div class="form-group col-md-3 kt-margin-b-20-tablet-and-mobile">
                                    <label for="from_date">From Date</label>
                                    <input type="text" class="form-control date" name="from_date" id="from_date" placeholder="Enter frmo date" />
                                </div>
                                <div class="form-group col-md-3 kt-margin-b-20-tablet-and-mobile">
                                    <label for="to_Date">To Date</label>
                                    <input type="text" class="form-control date" name="to_Date" id="to_Date" placeholder="Enter to date" />
                                </div>
                                <div class="form-group col-md-3 kt-margin-b-20-tablet-and-mobile">
                                    <label for="transaction_type">Transaction Type</label>
                                    <select class="form-control selectpicker" name="transaction_type" id="transaction_type"  data-live-search="true" data-live-search-placeholder="Search" title="Select Please">
                                        <option value="">Select Please</option>
                                        <option value="Deposit">Deposit</option>
                                        <option value="Expense">Expense</option>
                                        <option value="A/P">Accounts Payable</option>
                                        <option value="A/R">Accounts Receivable</option>
                                        <option value="Account Transfer">Account Transfer</option>                        
                                    </select>
                                </div>
                                <div class="form-group col-md-3 kt-margin-b-20-tablet-and-mobile">
                                    <label for="account">Account</label>
                                    <select class="form-control selectpicker" name="account" id="account"  data-live-search="true" data-live-search-placeholder="Search" title="Select Please">
                                        <option value="">Select Please</option>
                                        @if (!empty($data['accounts']))
                                        @foreach ($data['accounts'] as $key => $item)
                                        <option value="{{$item->id}}">{{$item->account_title}}</option>
                                        @endforeach      
                                        @endif                      
                                    </select>
                                </div>

                                <div class="form-group col-md-12 kt-margin-b-20-tablet-and-mobile">
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
                        @if (Helper::permission('transaction-bulk-action-delete'))
                        <th><label class="kt-checkbox kt-checkbox--single kt-checkbox--all kt-checkbox--solid"><input type="checkbox" class="selectall" onchange="select_all()">&nbsp;<span></span></label></th>
                        @endif
                        <th>SR</th>
                        <th>Transaction ID</th>
                        <th>Account</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th class="text-success">Debit(BDT)</th>
                        <th class="text-danger">Credit(BDT)</th>
                        <th class="text-info">Balance(BDT)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        @if (Helper::permission('transaction-bulk-action-delete'))
                        <td></td>
                        @endif
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-brand"><b>Total </b></td>
                        <td class="text-success text-right"></td>
                        <td class="text-danger text-right"></td>
                        <td class="text-info text-right"></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>  
            <!--end: Datatable -->
        </div>

        <div class="modal fade" id="saveDataModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-brand" id="modalTitle"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form method="POST" id="saveDataForm">
                        @csrf
                        <input type="hidden" class="form-control" name="update_id" id="update_id">
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-4 required">
                                    <label class="control-label" for="transaction_type">Transaction Type</label>
                                    <select class="form-control selectpicker" onchange="show_hide_dependent_input(this.value)" name="transaction_type" id="transaction_type"  data-live-search="true" data-live-search-placeholder="Search" title="Select Please">
                                        <option value="">Select Please</option>
                                        <option value="Deposit">Deposit</option>
                                        <option value="Expense">Expense</option>
                                        <option value="AP">Accounts Payable</option>
                                        <option value="AR">Accounts Receivable</option>
                                        <option value="TR">Account Transfer</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4 required account_section"  style="display:none;">
                                    <label class="control-label" for="account_id">Account</label>
                                    <select class="form-control selectpicker" name="account_id" id="account_id"  data-live-search="true" data-live-search-placeholder="Search" title="Select Please">
                                        <option value="">Select Please</option>
                                        @if (!empty($data['accounts']))
                                        @foreach ($data['accounts'] as $key => $item)
                                        <option value="{{$item->id}}">{{$item->account_title}}</option>
                                        @endforeach      
                                        @endif                          
                                    </select>
                                </div>
                                <div class="form-group col-md-4 required from_account_section" style="display:none;">
                                    <label class="control-label" for="from_account">From Account</label>
                                    <select class="form-control selectpicker" name="from_account" id="from_account"  data-live-search="true" data-live-search-placeholder="Search" title="Select Please">
                                        <option value="">Select Please</option>
                                        @if (!empty($data['accounts']))
                                        @foreach ($data['accounts'] as $key => $item)
                                        <option value="{{$item->id}}">{{$item->account_title}}</option>
                                        @endforeach      
                                        @endif                          
                                    </select>
                                </div>
                                <div class="form-group col-md-4 required to_account_section" style="display:none;">
                                    <label class="control-label" for="to_account">To Account</label>
                                    <select class="form-control selectpicker" name="to_account" id="to_account"  data-live-search="true" data-live-search-placeholder="Search" title="Select Please">
                                        <option value="">Select Please</option>
                                        @if (!empty($data['accounts']))
                                        @foreach ($data['accounts'] as $key => $item)
                                        <option value="{{$item->id}}">{{$item->account_title}}</option>
                                        @endforeach      
                                        @endif                          
                                    </select>
                                </div>
                                <div class="form-group col-md-4 required category_section">
                                    <label class="control-label" for="category_id">Category</label>
                                    <select class="form-control selectpicker" name="category_id" id="category_id"  data-live-search="true" data-live-search-placeholder="Search" title="Select Please">                      
                                    </select>
                                </div>
                                <div class="form-group col-md-4 required">
                                    <label for="amount" class="form-control-label">Amount</label>
                                    <input type="text" class="form-control" name="amount" id="amount" value="0.00" placeholder="Enter amount">
                                </div>
                                <div class="form-group col-md-4 required payment_method_section" style="display:none;">
                                    <label class="control-label" for="payment_method">Payment Method</label>
                                    <select class="form-control selectpicker" name="payment_method" id="payment_method"  data-live-search="true" data-live-search-placeholder="Search" title="Select Please">
                                        <option value="">Select Please</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Cheque">Cheque</option>
                                        <option value="Credit Card">Credit Card</option>
                                        <option value="Debit Card">Debit Card</option>
                                        <option value="Electronic Transfer">Electronic Transfer</option>
                                        <option value="Online Payment">Online Payment</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="reference" class="form-control-label">Reference</label>
                                    <input type="text" class="form-control" name="reference" id="reference" placeholder="Enter reference">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>File Browser</label>
                                    <div></div>
                                    <div class="custom-file">
                                        <input type="file" name="document" class="custom-file-input" id="customFile">
                                        <label class="custom-file-label" for="customFile">Choose file</label>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="description" class="form-control-label">Description</label>
                                    <textarea class="form-control" name="description" id="description" placeholder="Enter description"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-brand btn-sm" id="save-btn"></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="viewDataModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-brand" id="modalTitle"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="row" id="view-details">

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="./public/js/bootstrap-datepicker.min.js"></script>
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
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total debit over this page
            debitPageTotal = api
                .column(
                    @if (Helper::permission('transaction-bulk-action-delete'))
                    7
                    @else
                    6
                    @endif
                    , { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            // Total debit over this page
            creditPageTotal = api
                .column(
                    @if (Helper::permission('transaction-bulk-action-delete'))
                    8
                    @else
                    7
                    @endif
                    , { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            // Total debit over this page
            balancePageTotal = api
                .column(
                    @if (Helper::permission('transaction-bulk-action-delete'))
                    9
                    @else
                    8
                    @endif
                    , { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            // Total filtered rows on the selected column (code part added)
            // var sumDebitFiltered = display.map(el => data[el][7]).reduce((a, b) => intVal(a) + intVal(b), 0 );
            // var sumCrebitFiltered = display.map(el => data[el][8]).reduce((a, b) => intVal(a) + intVal(b), 0 );
            // var sumBalanceFiltered = display.map(el => data[el][9]).reduce((a, b) => intVal(a) + intVal(b), 0 );
            // Update footer for debit column
            $( api.column(
                @if (Helper::permission('transaction-bulk-action-delete'))
                    7
                    @else
                    6
                    @endif
            ).footer() ).html(
                debitPageTotal.toFixed(2)
            );
            // Update footer for credit column
            $( api.column(
                @if (Helper::permission('transaction-bulk-action-delete'))
                   8
                    @else
                    7
                    @endif
             ).footer() ).html(
                creditPageTotal.toFixed(2)
            );
            // Update footer for balance column
            $( api.column(
                @if (Helper::permission('transaction-bulk-action-delete'))
                    9
                    @else
                    8
                    @endif
             ).footer() ).html(
                balancePageTotal.toFixed(2)
            );
            $(api.column(@if (Helper::permission('transaction-bulk-action-delete'))
                    7
                    @else
                    6
                    @endif).footer()).css({'font-weight': 'bold','text-align': 'right'});
            $(api.column(@if (Helper::permission('transaction-bulk-action-delete'))
                    8
                    @else
                    7
                    @endif).footer()).css({'font-weight': 'bold','text-align': 'right'});
            $(api.column(@if (Helper::permission('transaction-bulk-action-delete'))
                    9
                    @else
                    8
                    @endif).footer()).css({'font-weight': 'bold','text-align': 'right'});
        },
        // Load data for the table's content from an Ajax source//
        "ajax": {
            "url": "{{route('transaction.list')}}",
            "type": "POST",
            "data": function (data) {
                data.from_date          = $('#form-filter #from_date').val();
                data.to_date            = $('#form-filter #to_date').val();
                data.transaction_type   = $('#form-filter #transaction_type').val();
                data.account            = $('#form-filter #account').val();
                data._token             = "{{csrf_token()}}";
            }
        },

        //Set column definition initialisation properties.
        "columnDefs": [
            {
                @if (Helper::permission('transaction-bulk-action-delete'))
                "targets": [0,10], //first column / numbering column
                @else 
                "targets": [9],
                @endif
                "orderable": false, //set not orderable
                "className": "text-center",
            },
            {
                "targets": @if (Helper::permission('transaction-bulk-action-delete'))
                    7
                    @else
                    6
                    @endif,
                "className": "text-success text-right",
            },
            {
                "targets": @if (Helper::permission('transaction-bulk-action-delete'))
                    8
                    @else
                    7
                    @endif,
                "className": "text-danger text-right",
            },
            {
                "targets": @if (Helper::permission('transaction-bulk-action-delete'))
                    9
                    @else
                    8
                    @endif,
                "className": "text-info text-right",
            },
        ],


    });
    /** END:: DATATABLE SERVER SIDE CODE **/

    /** BEGIN:: DATATABLE SEARCH FORM BUTTON TRIGGER CODE **/
    $('#btn-filter').click(function () {
        table.ajax.reload();
    });

    $('#btn-reset').click(function () {
        $('#form-filter')[0].reset();
        $('.selectpicker').selectpicker('refresh');
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
                    @if (Helper::permission('transaction-bulk-action-delete'))
                    columns: [1,2,3,4,5,6,7,8,9]
                    @else
                    columns: [0,1,2,3,4,5,6,7,8]
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
                filename: 'transaction-report',
                exportOptions: {
                    @if (Helper::permission('transaction-bulk-action-delete'))
                    columns: [1,2,3,4,5,6,7,8,9]
                    @else
                    columns: [0,1,2,3,4,5,6,7,8]
                    @endif
                }
            },
            {
                extend: 'csv',
                title: "{{ucwords($sub_title)}}",
                filename: 'transaction-report',
                exportOptions: {
                    @if (Helper::permission('transaction-bulk-action-delete'))
                    columns: [1,2,3,4,5,6,7,8,9]
                    @else
                    columns: [0,1,2,3,4,5,6,7,8]
                    @endif
                }
            },
            {
                extend: 'pdf',
                title: "{{ucwords($sub_title)}}",
                filename: 'transaction-report',
                orientation: 'portrait', //landscape
                pageSize: 'A4', //A3 , A5 , A6 , legal , letter
                exportOptions: {
                    @if (Helper::permission('transaction-bulk-action-delete'))
                    columns: [1,2,3,4,5,6,7,8,9]
                    @else
                    columns: [0,1,2,3,4,5,6,7,8]
                    @endif
                },
                customize: function ( doc ) {
                    doc.content[1].table.widths = ['5%','10%','25%','10%','10%','10%','10%','10%','10%'];
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

    $(document).on('click','#showModal',function () {
        $('#saveDataForm')[0].reset(); //reset form
        $(".error").each(function () {
            $(this).empty(); //remove error text
        });
        $("#saveDataForm").find('.is-invalid').removeClass('is-invalid'); //remover red border color
        $('#saveDataModal').modal({
            keyboard: false,
            backdrop: 'static', //make modal static
        });
        $(".account_section").hide();
        $(".from_account_section").hide();
        $(".to_account_section").hide();
        $(".payment_method_section").hide();
        $(".category_section").show();
        $('#saveDataForm .selectpicker').selectpicker('refresh');
        $('.custom-file-label').text('');
        $('.modal-title').html('<i class="fas fa-plus-square"></i> <span>Add New Transaction</span>'); //set modal title
        $('#save-btn').text('Save'); //set save button text
    });

    /** BEGIN:: DATA ADD/UPDATE AJAX CODE **/
    $('#saveDataForm').on('submit', function(event){
    event.preventDefault();
        
        var id  = $('#update_id').val();
        if(id){
            var method = 'update';
            var url = "{{route('transaction.update')}}";
        }else{
            var method = 'add';
            var url = "{{route('transaction.store')}}";
        }
        $.ajax({
            url: url,
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
                    if(data.status == 'success'){
                        if(method == 'update'){
                            table.ajax.reload( null, false );
                        }else{
                            table.ajax.reload();
                        }
                        $('.selectpicker').selectpicker('refresh');
                        $('#saveDataModal').modal('hide');
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

    //BEGIN: FETCHING DATA AJAX CODE
    $(document).on('click','.edit_data',function () {
        var id     = $(this).data('id');
        var _token = "{{csrf_token()}}";
        $('#saveDataForm')[0].reset(); // reset form on show modals
        $(".error").each(function () {
            $(this).empty();//remove error text
        });
        $("#saveDataForm").find('.is-invalid').removeClass('is-invalid');//remover red border color
        $('.custom-file-label').text('');
        $.ajax({
            url: "{{route('transaction.edit')}}",
            type: "POST",
            data:{id:id,_token:_token},
            dataType: "JSON",
            success: function (data) {
                $('#saveDataForm #update_id').val(data.transaction.id);
                show_hide_dependent_input(data.transaction.transaction_type,data.transaction.transaction_category_id,data.transaction.transfer_reference);
                $('#saveDataForm #transaction_type').val(data.transaction.transaction_type);
                if(data.transaction.transaction_type == 'TR'){
                    $('#saveDataForm #from_account').val(data.transaction.account_id);
                    if(data.transfer_account){
                        $('#saveDataForm #to_account').val(data.transfer_account.account_id);
                    }
                }else{
                    $('#saveDataForm #account_id').val(data.transaction.account_id);
                }
                $('#saveDataForm #amount').val(data.transaction.amount);
                $('#saveDataForm #payment_method').val(data.transaction.payment_method);
                $('#saveDataForm #reference').val(data.transaction.reference);
                $('#saveDataForm #description').val(data.transaction.description);
                $('#saveDataForm .selectpicker').selectpicker('refresh');
                $('#saveDataModal').modal({
                    keyboard: false,
                    backdrop: 'static', //make modal static
                });
                $('.modal-title').html('<i class="fas fa-edit"></i> <span>Edit '+data.transaction.transaction_no+' Data</span>');
                $('#save-btn').text('Update');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });
    //END: FETCHING DATA AJAX CODE

    //BEGIN: FETCHING DATA AJAX CODE
    $(document).on('click','.view_data',function () {
        var id     = $(this).data('id');
        var _token = "{{csrf_token()}}";

        $.ajax({
            url: "{{route('transaction.view')}}",
            type: "POST",
            data:{id:id,_token:_token},
            success: function (data) {
                console.log(data);
                $('#view-details').html(data.transaction);
                $('#viewDataModal').modal({
                    keyboard: false,
                    backdrop: 'static', //make modal static
                });
                $('.modal-title').html('<i class="kt-nav__link-icon flaticon2-expand"></i> <span> View Transaction Details</span>');

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
        var url = "{{route('transaction.delete')}}";
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
            var url = "{{route('transaction.bulkaction')}}";
            bulk_action_delete(table,url,id,rows);
        }
    });
    //END: BULK ACTION DELETE AJAX CODE
}); 



$("#from_account").change(function(){
    var from_account_id = $(this).val();
    var to_account_id = $("#to_account").val();
    if(from_account_id !== '' && to_account_id !==''){
        same_account_transfer_not_allowed(from_account_id,to_account_id);
    }
});

$("#to_account").change(function(){
    var to_account_id = $(this).val();
    var from_account_id = $("#from_account").val();
    if(from_account_id !== '' && to_account_id !==''){
        same_account_transfer_not_allowed(from_account_id,to_account_id);
    }
});

function same_account_transfer_not_allowed(from_account_id,to_account_id)
{
    if(from_account_id == to_account_id){
        Swal.fire({
            icon: 'error',
            text: 'Same account transfer not allowed!'
        });
        $('#saveDataForm #save-btn').prop('disabled',true);
    }else{
        $('#saveDataForm #save-btn').prop('disabled',false);
    }
}

function getCategoryList(transaction_type,category_id = null)
{
    var _token = "{{csrf_token()}}";
    $.ajax({
        url: "{{route('transaction.category.list')}}",
        type: "POST",
        data: {transaction_type: transaction_type,_token:_token},
        success: function (data) {
            if (data) {
                $('#saveDataForm #category_id').html(data);
                $('#saveDataForm #category_id.selectpicker').selectpicker('refresh');
                if(category_id){
                    $('#saveDataForm #category_id').val(category_id);
                    $('#saveDataForm #category_id.selectpicker').selectpicker('refresh');
                }
            } 
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
}
$(document).on('change', '#customFile', function () {
    $(this).parent().find('.custom-file-label').text($(this).val().replace(/C:\\fakepath\\/i, ''));
});

function show_hide_dependent_input(transaction_type, category_id = null,transfer_ref = null){
    if (transaction_type !== '')
    {
        $("#account_id").val('');
        $("#from_account").val('');
        $("#to_account").val('');
        $("#payment_method").val('');
        $('#saveDataForm .selectpicker').selectpicker('refresh');
        if(transaction_type == 'AP' || transaction_type == 'AR'){
            $(".account_section").show();
            $(".category_section").show();
            $(".from_account_section").hide();
            $(".to_account_section").hide();
            $(".payment_method_section").hide();
            getCategoryList(transaction_type,category_id);
        }
        else if(transaction_type == 'Deposit' || transaction_type == 'Expense'){
            $(".account_section").show();
            if(transfer_ref){
                $(".category_section").hide();
            }else{
                $(".category_section").show();
            }
            $(".from_account_section").hide();
            $(".to_account_section").hide();
            $(".payment_method_section").show();
            getCategoryList(transaction_type,category_id);
        }
        else if(transaction_type == 'TR'){
            $(".account_section").hide();
            $(".category_section").hide();
            $(".from_account_section").show();
            $(".to_account_section").show();
            $(".payment_method_section").show();
        }else{
            $(".account_section").hide();
            $(".from_account_section").hide();
            $(".to_account_section").hide();
            $(".payment_method_section").hide();
            $(".category_section").show();
        }
        
    }

}
</script>
@endpush