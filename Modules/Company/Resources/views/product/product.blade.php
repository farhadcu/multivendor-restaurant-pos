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
            <a href="{{route('admin.dashboard')}}" class="kt-subheader__breadcrumbs-link"><i class="m-nav__link-icon la la-home"></i>  Dashboard </a>
            <a class="kt-subheader__breadcrumbs-link"><i class="la la-angle-double-right"></i>  Product </a>
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
        @if (Helper::permission('product-bulk-action-delete') || Helper::permission('product-report'))
        <div class="row py-3">
            <div class="dataTableButton text-right col-md-12 col-sm-12 px-0 d-flex justify-content-center">
                
                @if (Helper::permission('product-report'))
                <div id="colvis-btn"></div>
                <div class="dropdown dropdown-inline">
                    <button type="button" class="btn btn-sm btn-label-info btn-bold mx-1" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-file-download"></i> Export
                    </button>
                    <div id="btn_group" class="dropdown-menu dropdown-menu-right"></div>
                </div>
                @endif
                @if (Helper::permission('product-bulk-action-delete'))
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
                    @if (Helper::permission('product-add'))
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
                                <div class="col-md-4 kt-margin-b-20-tablet-and-mobile">
                                    <label>Category Name</label>
                                    <input type="text" class="form-control" name="category_name" id="category_name" placeholder="Enter category name" />
                                </div>
                                <div class="col-md-8 kt-margin-b-20-tablet-and-mobile">
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
                        @if (Helper::permission('product-bulk-action-delete'))
                        <th><label class="kt-checkbox kt-checkbox--single kt-checkbox--all kt-checkbox--solid"><input type="checkbox" class="selectall" onchange="select_all()">&nbsp;<span></span></label></th>
                        @endif
                        <th>SR</th>
                        <th>Image</th>
                        <th>Model No.</th>
                        <th>Name</th>
                        <th>Stock On Hand</th>
                        <th>Min Stock Qty</th>
                        <th>Max Stock Qty</th>
                        <th>Purchase Price (BDT)</th>
                        <th>Selling Price (BDT)</th>
                        <th>Returnable</th>
                        <th>Rack No</th>
                        @if (Helper::permission('product-change-status'))
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
                        <div class="modal-body px-5">
                            
                            <p class="italic text-danger">The field labels marked with * are required input fields.</p>
                            <ul class="nav nav-pills nav-fill" role="tablist">
                                <li class="nav-item" id="general_tab">
                                    <a class="nav-link active" data-toggle="tab" href="#general">General</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#data">Data</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#variation">Variation</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#discount">Discount</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="general" role="tabpanel">
                                    @include('company::product.include.general')
                                </div>
                                <div class="tab-pane" id="data" role="tabpanel">
                                    @include('company::product.include.data')
                                </div>
                                <div class="tab-pane" id="variation" role="tabpanel">
                                    @include('company::product.include.variation')
                                </div>
                                <div class="tab-pane" id="discount" role="tabpanel">
                                    @include('company::product.include.discount')
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

        <!--start: Checkout modal -->
        <div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="checkoutModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-brand" id="modalTitle">Generate Barcode</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                        <form id="paymentDataForm">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" name="product_model" id="product_model" value=""/>
                                <div class="form-group required">
                                    <label for="product_name" class="form-control-label">Product Name</label>
                                    <select class="form-control" name="product_name" id="product_name"
                                                data-live-search="true" data-live-search-placeholder="Search"
                                                title="Select Product">
                                            </select>
                                </div>
                                <div class="form-group required">
                                    <label for="barcode_option" class="form-control-label">Barcode Option</label>
                                    <select class="form-control" name="barcode_option" id="barcode_option" multiple
                                                data-live-search="true" data-live-search-placeholder="Search"
                                                title="Select Option">
                                            </select>
                                </div>
                                <div class="form-group required">
                                    <label for="barcode_qty" class="form-control-label">Barcode Qty</label>
                                    <input type="number" class="form-control" name="barcode_qty" id="barcode_qty" min="1" value="1">
                                </div>
                                <div class="form-group">
                                    <div class="picture-container" id="choose-picture-box" style="border:1px dashed #555;height:70px;margin-top:0 !important;">
                                        <div class="picture">
                                            <table style="width: 100%;color: #111111;" cellpadding="5px" cellspacing="10px">
                                                <tbody id="barcode_table">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-success btn-sm" onclick="generate_barcode()">Generate</button>
                                <button type="button" class="btn btn-brand btn-sm" onclick="print_barcode()" id="print_barcode_btn" disabled>Print</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!--end: Checkout modal -->

    </div>
</div>

@endsection

@push('scripts')
<script src="./public/js/bootstrap-datepicker.min.js"></script>
<script>

var table;
var row = 1;                                                                                                                                
function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $(input).prev('img').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

$(document).on('change','#product_name',function () {
    var id     = $('#product_name').val();
    get_product_variation(0,id);
});

$(document).on('click','.generate_barcode',function () {
    var id     = $(this).data('id');
    get_product_variation(id,0);
});

/* Start: Get product variation data */
function get_product_variation(id,variation_id){
    var _token = "{{csrf_token()}}";
    $.ajax({
        url: "{{route('variation.product')}}",
        type: "POST",
        data:{id:id,variation_id:variation_id,_token:_token},
        dataType: "JSON",
        success: function (data) {
            $('#barcode_option').find('option').remove();
            $('#barcode_table').empty();
            $('#barcode_qty').val(1);
            $('#print_barcode_btn').attr('disabled',true);
            if(variation_id == 0){
                $('#product_name').find('option').remove();
                if(data.length == 1){
                    $("#product_name").append('<option value="'+data[0].id+'" selected>'+data[0].name+'</option>');
                    $("#barcode_option").append('<option value="'+data[0].price+'" selected>Price</option>');
                    $("#barcode_option").append('<option value="'+data[0].discount+'">Discount</option>');
                    $("#product_model").val(data[0].model);
                }else{
                    $.each(data, function (key, value) {
                        $("#product_name").append(new Option(value.name, value.id));
                    });
                }
                $('#product_name').selectpicker('refresh');
            }else{
                $("#barcode_option").append('<option value="'+data[0].price+'" selected>Price</option>');
                $("#barcode_option").append('<option value="'+data[0].discount+'">Discount</option>');
                $("#product_model").val(data[0].model);
            }
            $('#barcode_option').selectpicker('refresh');
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
    if(variation_id == 0){
        $('#checkoutModal').modal('show');
    }
}
/* End: Get product variation data */


/* Start: Generate barcode */
function generate_barcode(){
    var _token = "{{csrf_token()}}";
    var product_name = $("#product_name option:selected").text().split("(")[0];
    var model = $("#product_model").val();
    var barcode_option = $("#barcode_option").val();
    $.ajax({
        url: "{{route('generate.barcode')}}",
        type: "POST",
        data:{model:model,_token:_token},
        dataType: "JSON",
        success: function (data) {
            var price = barcode_option;
            if(barcode_option.length > 1){
                price = barcode_option[0] - barcode_option[0] * barcode_option[1]/100;
            }
            var row = '<tr>'+
                            '<td><span style="font-size: 12px;text-align: center;">'+product_name+'</span><br>'+
                                '<img src="'+data+'" class="picture-src" id="barcode" alt="Barcode">'+
                                '<br><span style="font-size: 12px;text-align: center;">Price: '+price
                            '</span></td>'+
                        '</tr>';
            $('#barcode_table').empty().append(row);
            $('#print_barcode_btn').removeAttr('disabled');
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
}
/* End: Generate barcode */

/* Start: Print barcode */
function print_barcode(){
    var qty = $('#barcode_qty').val();
    var odd = 0;
    var even = 0;
    if(qty > 3)
    {
        odd = qty % 4;
        even = (qty - odd) / 4;
    }
    else
    {
        odd = qty;
    }
    var divToPrint=$('#barcode_table').find("td:first").html();
    var table = '<table style="width: 100%" cellpadding="5px" cellspacing="10px"><tbody>';
                    for(var i = 0;i < even; i++){
        table +=        '<tr>';
                        for(var j = 0;j < 4; j++){
        table +=            '<td  align="center" style="border: 1px solid black ;padding: 5px">'+divToPrint+'</td>';
                        }
        table +=        '</tr>';
                    }
                    if(odd > 0){
        table +=        '<tr>'
                        for(var i = 0;i < odd; i++){
        table +=             '<td  align="center" style="border: 1px solid black ;padding: 5px">'+divToPrint+'</td>';
                        }
        table +=        '</tr>';
                    }
        table +=   '</tbody></table>';
                
    var newWin=window.open('','Print-Window');
    newWin.document.open();
    newWin.document.write('<html><body onload="window.print()">'+table+'</body></html>');
    newWin.document.close();
    setTimeout(function(){newWin.close();},10);
}
/* End: Print barcode */


$('.description').show();
$(document).on('change', '#image', function () {
    readURL(this);
    $('.description').hide();
    $('#choose-picture-box').css({'border':'1px dashed #555'});
    $('#image.error').empty();
});
$(document).ready(function () {
    get_category_list();
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
        "autoWidth": false,
    "fixedHeader": {
        "header": true,
        "footer": false
    },
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
            "url": "{{route('product.list')}}",
            "type": "POST",
            "data": function (data) {
                data.name         = $('#form-filter #name').val();
                data.model        = $('#form-filter #model').val();
                data.returnable   = $('#form-filter #returnable').val();
                data.rack_no      = $('#form-filter #rack_no').val();
                data.status       = $('#form-filter #status').val();
                data._token       = "{{csrf_token()}}";
            }
        },

        //Set column definition initialisation properties.
        "columnDefs": [
            {
                @if (Helper::permission('product-bulk-action-delete') && Helper::permission('product-change-status'))
                "targets": [0,12,13],
                @elseif (!Helper::permission('product-bulk-action-delete') && Helper::permission('product-change-status'))
                "targets": [11,12], 
                @elseif (Helper::permission('product-bulk-action-delete') && !Helper::permission('product-change-status'))
                "targets": [0,12], 
                @else
                "targets": [11],
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
                    @if (Helper::permission('product-bulk-action-delete') && Helper::permission('product-change-status'))
                    columns: [1,3,4,5,6,7,8,9,10,11]
                    @else
                    columns: [0,2,3,4,5,6,7,8,9,10]
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
                filename: 'product-report',
                exportOptions: {
                    @if (Helper::permission('product-bulk-action-delete') && Helper::permission('product-change-status'))
                    columns: [1,3,4,5,6,7,8,9,10,11]
                    @else
                    columns: [0,2,3,4,5,6,7,8,9,10]
                    @endif
                }
            },
            {
                extend: 'csv',
                title: "{{ucwords($sub_title)}}",
                filename: 'product-report',
                exportOptions: {
                    @if (Helper::permission('product-bulk-action-delete') && Helper::permission('product-change-status'))
                    columns: [1,3,4,5,6,7,8,9,10,11]
                    @else
                    columns: [0,2,3,4,5,6,7,8,9,10]
                    @endif
                }
            },
            {
                extend: 'pdf',
                title: "{{ucwords($sub_title)}}",
                filename: 'product-report',
                orientation: 'landscape', //landscape
                pageSize: 'A4', //A3 , A5 , A6 , legal , letter
                exportOptions: {
                    @if (Helper::permission('product-bulk-action-delete') && Helper::permission('product-change-status'))
                    columns: [1,3,4,5,6,7,8,9,10,11]
                    @else
                    columns: [0,2,3,4,5,6,7,8,9,10]
                    @endif
                },
                customize: function ( doc ) {
                    doc.content[1].table.widths = ['5%','15%','15%','8%','8%','8%','12%','12%','7%','10%'];
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
						// Create a header object with 3 columns
						// Left side: Logo
						// Middle: brandname
						// Right side: A document title
						doc['header']=(function() {
							return {
								columns: [
                                    {
                                        alignment: 'center',
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
        $('.nav-item a').removeClass('active');
        $('.tab-content .tab-pane').removeClass('active');
        $('#general_tab a').addClass('active');
        $('#general').addClass('active');
        $('#saveDataModal').modal({
            keyboard: false,
            backdrop: 'static', //make modal static
        });

        $('#saveDataForm .selectpicker').selectpicker('refresh');
        $('#saveDataForm table tbody').html('');
        $('.modal-title').html('<i class="fas fa-plus-square"></i> <span>Add New Company</span>'); //set modal title
        $('#save-btn').text('Save'); //set save button text
    });

    /** BEGIN:: DATA ADD/UPDATE AJAX CODE **/
    $('#saveDataForm').on('submit', function(event){
    event.preventDefault();
        
        var id  = $('#update_id').val();
        if(id){
            var method = 'update';
            var url = "{{route('product.update')}}";
        }else{
            var method = 'add';
            var url = "{{route('product.store')}}";
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
                $("#saveDataForm table").find('.is-invalid').removeClass('is-invalid');
                $("#saveDataForm table").find('.error').remove();   
                if (data.status) {
                    bootstrap_notify(data.status,data.message);
                    if(data.status == 'success'){
                        if(method == 'update'){
                            table.ajax.reload( null, false );
                        }else{
                            table.ajax.reload();
                        }
                        get_category_list();
                        $('.selectpicker').selectpicker('refresh');
                        $('#saveDataModal').modal('hide');

                    }
                } else {
                    $.each(data.errors, function (key, value) {
                        var key = key.split('.').join("_");
                        $("#saveDataForm input[name='"+key+"']").addClass('is-invalid');
                        $("#saveDataForm select#" + key).parent().addClass('is-invalid');
                        $("#saveDataForm textarea[name='" + key + "']").parent().addClass('is-invalid');
                        $("#saveDataForm input[name='"+key+"']").after('<div id="'+key+'" class="error invalid-feedback">'+value+'</div>');
                        $("#saveDataForm select#" + key).parent().after('<div id="'+key+'" class="error invalid-feedback">'+value+'</div>');
                        $("#saveDataForm textarea[name='"+key+"']").after('<div id="'+key+'" class="error invalid-feedback">'+value+'</div>');
                        $("#saveDataForm table").find("#"+key).addClass('is-invalid');
                        $("#saveDataForm table").find("#"+key).after('<div id="'+key+'" class="error invalid-feedback">'+value+'</div>');
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
        $('.selectpicker').selectpicker('refresh');
        $('.nav-item a').removeClass('active');
        $('.tab-content .tab-pane').removeClass('active');
        $('#general_tab a').addClass('active');
        $('#general').addClass('active');
        $('#saveDataForm table tbody').html('');
        $.ajax({
            url: "{{route('product.edit')}}",
            type: "POST",
            data:{id:id,_token:_token},
            dataType: "JSON",
            success: function (data) {
                $('#saveDataForm #update_id').val(data.product.id);
                $('#saveDataForm #name').val(data.product.name);
                $('#saveDataForm #model').val(data.product.model);
                $('#saveDataForm select[name="supplier_id"]').val(data.product.supplier_id);
                $('#saveDataForm #sku').val(data.product.sku);
                $('#saveDataForm #upc').val(data.product.upc);
                $('#saveDataForm #mpn').val(data.product.mpn);
                $('#saveDataForm #purchase_price').val(data.product.purchase_price);
                $('#saveDataForm #selling_price').val(data.product.selling_price);
                $('#saveDataForm #qty').val(data.product.qty);
                $('#saveDataForm #min_qty').val(data.product.min_qty);
                $('#saveDataForm #max_qty').val(data.product.max_qty);
                $('#saveDataForm #stock_unit').val(data.product.stock_unit);
                $('#saveDataForm #subtract_stock').val(data.product.subtract_stock);
                $('#saveDataForm #rack_no').val(data.product.rack_no);
                $('#saveDataForm #length').val(data.product.length);
                $('#saveDataForm #width').val(data.product.width);
                $('#saveDataForm #height').val(data.product.height);
                $('#saveDataForm #weight').val(data.product.weight);
                $('#saveDataForm #returnable').val(data.product.returnable);
                if(data.product.image){
                    var image = "{{FOLDER_PATH.PRODUCT_IMAGE}}" + data.product.image;
                    $('#saveDataForm .picture-src').attr("src", image);
                }else{
                    $('#saveDataForm .picture-src').attr("src", './public/img/icon-choose.png');
                }
                if(data.category){
                    $('#saveDataForm #category_id').val(data.category);
                }
                if(data.variation){
                    $('#saveDataForm table tbody').html(data.variation);
                    row = data.row + 1;
                }
                if(data.discount){
                    $('#saveDataForm #discount_qty').val(data.discount.discount_qty);
                    $('#saveDataForm #discount_amount').val(data.discount.discount_amount);
                    $('#saveDataForm #start_date').val(data.discount.start_date);
                    $('#saveDataForm #end_date').val(data.discount.end_date);

                }
                $('.selectpicker').selectpicker('refresh');
                $('#saveDataModal').modal({
                    keyboard: false,
                    backdrop: 'static', //make modal static
                });
                $('.modal-title').html('<i class="fas fa-edit"></i> <span>Edit Category</span>');
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
            url: "{{route('product.view')}}",
            type: "POST",
            data:{id:id,_token:_token},
            success: function (data) {
                console.log(data);
                $('#view-details').html(data.transaction);
                $('#viewDataModal').modal({
                    keyboard: false,
                    backdrop: 'static', //make modal static
                });
                $('.modal-title').html('<i class="kt-nav__link-icon flaticon2-expand"></i> <span> View Product Details</span>');

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
                url: "{{route('product.change.status')}}",
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
        var url = "{{route('product.delete')}}";

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
            var url = "{{route('product.bulkaction')}}";

        }
    });
    //END: BULK ACTION DELETE AJAX CODE
    // $("#variant-section").hide();
    // $("input[name='is_variant']").on("change", function () {
    //     if ($(this).is(':checked')) {
    //         $("#variant-section").show(300);
    //     }
    //     else
    //         $("#variant-section").hide(300);
    // });

    
    $("input[name='variant']").on("input", function () {
        if($("#saveDataForm #model").val() == ''){
            $("input[name='variant']").val('');
            bootstrap_notify('danger','Please fillup required information first.');
        }
        else if($(this).val().indexOf(',') > -1) {
            var variant_name = $(this).val().slice(0, -1);
            var item_code = variant_name+'-'+$("#saveDataForm #model").val();
            var newRow = $("<tr>");
            var cols = '';
            cols += '<td><input type="text" class="form-control" id="variation_'+row+'_variation_name" name="variation['+row+'][variation_name]" value="' + variant_name + '" /></td>';
            cols += '<td><input type="text" class="form-control" id="variation_'+row+'_variation_model" name="variation['+row+'][variation_model]" value="'+item_code+'" /></td>';
            cols += '<td  class="text-center"><input type="text" id="variation_'+row+'_variation_qty" class="form-control text-center" name="variation['+row+'][variation_qty]" value="" step="any" /></td>';
            cols += '<td  class="text-center"><select class="form-control text-center" id="variation_'+row+'_price_prefix" name="variation['+row+'][price_prefix]">';
            cols += '    <option value="+">+</option>';
            cols += '    <option value="-">-</option>';
            cols += '</select></td>';
            cols += '<td class="text-right"><input type="text" class="form-control text-right" id="variation_'+row+'_variation_price" name="variation['+row+'][variation_price]" value="" step="any" /></td>';
            cols += '<td class="text-right"><input type="text" class="form-control text-right" id="variation_'+row+'_variation_weight" name="variation['+row+'][variation_weight]" value="" step="any" /></td>';
            cols += '<td  class="text-center"><select class="form-control text-center" id="variation_'+row+'_primary" name="variation['+row+'][primary]">';
            cols += '    <option value="1">Yes</option>';
            cols += '    <option value="2" selected>No</option>';
            cols += '</select></td>';
            cols += '<td class="text-right"><button type="button" class="vbtnDel btn btn-icon-sm btn-sm btn-danger" style="margin-top: 4px;"><i class="fas fa-trash"></i></button></td>';

            $("input[name='variant']").val('');
            newRow.append(cols);
            row++;
            $("table.variant-list tbody").append(newRow);
        }
    });
    $("table#variant-table tbody").on("click", ".vbtnDel", function(event) {
        $(this).closest("tr").remove();
    });
}); 
function get_category_list(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: "{{route('category.category.list')}}",
        type: "POST",
        success: function (data) {
            if (data) {
                $('#saveDataForm #category_id').html(data);
            } 
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
}
</script>
@endpush