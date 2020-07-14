@extends('company.layouts.app')

@section('title')
{{ucwords($page_title)}}
@endsection

@push('styles')
<link rel="stylesheet" href="./public/css/jquery-ui.css" />
<style>
#pending_order_table_filter label .form-control{
    width:150px !important;
}
</style>
@endpush



@section('content')
<!-- begin:: Subheader -->
<div class="kt-subheader   kt-grid__item" id="kt_subheader">
    <div class="kt-subheader__main">
        {{-- <h3 class="kt-subheader__title"> Dashboard </h3> --}}
        <span class="kt-subheader__separator kt-hidden"></span>
        <div class="kt-subheader__breadcrumbs">
            <a href="{{route('admin.dashboard')}}" class="kt-subheader__breadcrumbs-link"><i
                    class="m-nav__link-icon la la-home"></i> Dashboard </a>
            <a class="kt-subheader__breadcrumbs-link"><i class="la la-angle-double-right"></i> Sale </a>
            <a class="kt-subheader__breadcrumbs-link"><i class="la la-angle-double-right"></i> {{ucwords($sub_title)}}
            </a>

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
            <div class="kt-form kt-form--label-right kt-margin-b-10">
                <div class="row">
                    <div class="col-lg-5  pt-3 pb-5">
                        <div class="row">
                            <div class="col-md-12 py-3 px-3" style="background:#f2f2f2;">
                                <form>
                                    @csrf
                                    <div class="row ">
                                        <div class="form-group col-lg-6 d-flex">
                                            <select class="form-control" name="customer" id="customer"
                                                data-live-search="true" data-live-search-placeholder="Search"
                                                title="Select Customer">
                                            </select>
                                            <button type="button" class="btn btn-info btn-icon-sm btn-sm ml-2"
                                                data-skin="dark" data-placement="top" title="" data-original-title="Add New Customer" 
                                                data-toggle="modal" data-target="#saveDataModal"><i class="fas fa-plus"></i></button>
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <input type="text" class="form-control" name="product_model" id="product_model"
                                                placeholder="Enter product model">
                                        </div>
                                    </div>
                                    <div class="row table-responsive">
                                        <table class="table table-border" style="font-size:12px;" id="cart_table">
                                            <thead>
                                                <th width="5%" class="text-center font-weight-bold">SL</th>
                                                <th width="30%" class="text-left font-weight-bold">Name</th>
                                                <th width="20%" class="text-center font-weight-bold">Qty</th>
                                                <th width="15%" class="text-right font-weight-bold">Price</th>
                                                <th width="5%" class="text-center font-weight-bold">Dis</th>
                                                <th width="20%" class="text-right font-weight-bold">Subtotal</th>
                                                <th width="5%"></th>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1; $total_discount = []; ?>
                                                @foreach($cartData as $data)
                                                <tr class="item_row" id="item_row_id_{{$data->rowId}}">
                                                    <td class="text-center">{{$i}}</td>
                                                    <td class="text-left">{{$data->name}}</td>
                                                    <td class="text-center d-flex">
                                                        <button type="button" class="btn btn-danger btn-icon pull-left"
                                                            onclick="cart_qty_update('less',1,'<?php echo $data->rowId ?>')"
                                                            style="width:20px;height:20px;" data-skin="dark"
                                                            data-toggle="kt-tooltip" data-placement="top" title=""
                                                            data-original-title="Quantity Minus">-</button>
                                                        <input class="form-control text-center"
                                                            onkeyup="cart_qty_update('default',this.value,'<?php echo $data->rowId ?>')"
                                                            id="qty_{{$data->rowId}}" type="text" value="{{$data->qty}}"
                                                            style="height: 20px;width:40px;padding:0;" />
                                                        <button type="button" class="btn btn-info btn-icon pull-right"
                                                            onclick="cart_qty_update('plus',1,'<?php echo $data->rowId ?>')"
                                                            style="width:20px;height:20px;" data-skin="dark"
                                                            data-toggle="kt-tooltip" data-placement="top" title=""
                                                            data-original-title="Quantity Plus">+</button>
                                                    </td>
                                                    <td class="text-right" id="price_{{$data->rowId}}">{{number_format($data->price, 2, '.', '')}}</td>
                                                    <?php 
                                            $discount = $data->options->discount;
                                            $sub_total = ($data->price - ($data->price * ($data->options->discount/100))) * $data->qty;
                                            array_push($total_discount,($data->price * ($data->options->discount/100)) * $data->qty);
                                            ?>
                                                    <td class="text-center" id="discount_{{$data->rowId}}">{{number_format($discount, 2, '.', '')}}%
                                                    </td>
                                                    <td class="text-right" id="sub_total_{{$data->rowId}}">{{number_format($sub_total, 2, '.', '')}}
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button"
                                                            onclick="cart_item_remove('<?php echo $data->rowId ?>')"
                                                            class="btn btn-danger btn-icon pull-right mt-1"
                                                            style="width:20px;height:20px;" data-skin="dark"
                                                            data-toggle="kt-tooltip" data-placement="top" title=""
                                                            data-original-title="Remove Product">
                                                            <i class="fas fa-trash text-white" style="font-size:10px;"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php $i++ ?>
                                                @endforeach
                                                <tr id="total_row">
                                                    <td class="text-right font-weight-bold" colspan="4">Total</td>
                                                    <td class="text-right font-weight-bold">BDT</td>
                                                    <td class="text-right font-weight-bold" id="cart_sub_total">
                                                        {{number_format($subTotal, 2, '.', '')}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right font-weight-bold" colspan="4">Discount</td>
                                                    <td class="text-right font-weight-bold">BDT</td>
                                                    <td class="text-right font-weight-bold" id="cart_discount">
                                                        {{number_format(array_sum($total_discount), 2, '.', '')}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right font-weight-bold font-weight-bold" colspan="4">

                                                        Vat({{number_format($vat, 2, '.', '')}}%)
                                                        <select name="vat_type" id="vat_type" onchange="vat_calculate(this)"
                                                            style="width:45px;height: 20px;">
                                                            <option value="inc">Inc.</option>
                                                            <option value="exc">Exc.</option>
                                                        </select>
                                                    </td>
                                                    <td class="text-right font-weight-bold">BDT</td>
                                                    <td class="text-right font-weight-bold" id="cart_vat">0.00</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right font-weight-bold" colspan="4">Grand Total</td>
                                                    <td class="text-right font-weight-bold">BDT</td>
                                                    <td class="text-right font-weight-bold" id="cart_total">
                                                        {{$subTotal - array_sum($total_discount)}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right font-weight-bold" colspan="4">
                                                        Adjustment
                                                        <select name="adjustment_type" id="adjustment_type"
                                                            onchange="adjustment_calculate(this.value)"
                                                            style="width:45px;height: 20px;">
                                                            <option value="+">+</option>
                                                            <option value="-">-</option>
                                                        </select>
                                                    </td>
                                                    <td class="text-right font-weight-bold">BDT</td>
                                                    <td><input class="form-control text-right font-weight-bold" type="text"
                                                            onkeyup="adjustment_calculate(event)" name="adjustment"
                                                            id="adjustment" value="0.00" style="height: 20px;padding:0;" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right font-weight-bold" colspan="4">Payable</td>
                                                    <td class="text-right font-weight-bold">BDT</td>
                                                    <td class="text-right font-weight-bold" id="payable">{{$subTotal - array_sum($total_discount)}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right font-weight-bold" colspan="4">Received</td>
                                                    <td class="text-right font-weight-bold">BDT</td>
                                                    <td><input class="form-control text-right font-weight-bold" type="text"
                                                            onkeyup="receive_calculate(event)" name="received"
                                                            id="received" value="0.00" style="height: 20px;padding:0;" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right font-weight-bold" colspan="4">Change</td>
                                                    <td class="text-right font-weight-bold">BDT</td>
                                                    <td class="text-right font-weight-bold" id="changed">0.00</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row justify-content-center pt-5">
                                        <button class="btn btn-brand col-md-3" onclick="checkout_pending_order(0,'','','')" style="margin:10px;"><i
                                                class="fab fa-opencart"></i> Checkout</button>
                                        <button class="btn btn-info col-md-3" style="margin:10px;"
                                            onclick="hold_cart_data()"><i class="fas fa-hand-holding-usd"></i>Hold
                                            Order</button>
                                        <button class="btn btn-danger col-md-3" style="margin:10px;"
                                            onclick="delete_pending_order(0)"><i class="far fa-times-circle"></i> Clear
                                            Cart</button>

                                    </div>

                                </form>
                            </div>
                            <div class="col-md-12 py-5 mt-5 table-responsive" style="background:#f2f2f2;">
                                <table class="table table-striped- table-bordered table-hover table-checkable"
                                    id="pending_order_table">
                                    <thead>
                                        <tr>
                                            <th>
                                                <label
                                                    class="kt-checkbox kt-checkbox--single kt-checkbox--all kt-checkbox--solid">
                                                    <input type="checkbox" class="selectall"
                                                        onchange="select_all()">&nbsp;<span></span>
                                                </label>
                                            </th>
                                            <th>SR</th>
                                            <th>Table</th>
                                            <th>Customer</th>
                                            <th>Total</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7 pt-3">
                        <div class="row align-items-center">
                            <div class="col-xl-12 pt-3">
                                <div class="row">
                                    <div class="form-group col-lg-6 pull-right">
                                        <select class="form-control selectpicker" name="category" id="category"
                                            data-live-search="true" onchange="get_product_by_category(this)"
                                            data-live-search-placeholder="Search" title="Select Category"></select>
                                    </div>
                                    <div class="form-group col-lg-6 pull-right">
                                        <input type="text" class="form-control" name="product_name" id="product_name"
                                            onkeyup="get_product_by_name(this)"
                                            placeholder="Search by product name or code">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="products">
                        </div>
                    </div>
                </div>

            </div>
            <!--end: Search Form -->
      
            <!--start: Customer modal -->
            <div class="modal fade" id="saveDataModal" tabindex="-1" role="dialog" aria-labelledby="saveDataModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-brand" id="modalTitle">Add New Customer</h5>
                            <button type="button" onclick="customer_modal_btn('cancel')" class="close" data-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                        <form id="saveDataForm">
                            @csrf
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-6 required">
                                        <label for="customer_group" class="form-control-label">Customer Group</label>
                                        <select class="form-control" name="customer_group" id="customer_group" data-live-search="true" data-live-search-placeholder="Search" title="Select Please">
                                            @foreach (CUSTOMER_GROUP as $key => $value)
                                                <option value="{{$key}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6 required">
                                        <label for="name" class="form-control-label">Name</label>
                                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter supplier name">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="email" class="form-control-label">Email</label>
                                        <input type="text" class="form-control" name="email" id="email" placeholder="Enter supplier email">
                                    </div>
                                    <div class="form-group col-md-6 required">
                                        <label for="mobile" class="form-control-label">Mobile No.</label>
                                        <input type="text" class="form-control" name="mobile" id="mobile" placeholder="Enter supplier mobile no.">
                                    </div>
                                    <div class="form-group col-md-6 required">
                                        <label for="city" class="form-control-label">City</label>
                                        <input type="text" class="form-control" name="city" id="city" placeholder="Enter city name">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="postal_code" class="form-control-label">Postal Code</label>
                                        <input type="text" class="form-control" name="postal_code" id="postal_code" placeholder="Enter postal code">
                                    </div>
                                    <div class="form-group col-md-12 required">
                                        <label for="address" class="form-control-label">Address</label>
                                        <input type="text" class="form-control" name="address" id="address" placeholder="Enter supplier address">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" onclick="customer_modal_btn('cancel')">Close</button>
                                <button type="button" class="btn btn-brand btn-sm" id="save-btn" onclick="customer_modal_btn('save')">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!--end: Customer modal -->

            <!--start: Checkout modal -->
            <div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="checkoutModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-brand" id="modalTitle">Check Out Order</h5>
                            <button type="button" onclick="checkout_cart_data('cancel')" class="close" data-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                        <form id="paymentDataForm">
                            @csrf
                            <div class="modal-body">
                                <div class="row">
                                    <input type="hidden" name="pending_order" id="pending_order"/>
                                    <div class="form-group col-md-6">
                                        <label for="checkout_customer" class="form-control-label">Customer Name</label>
                                        <input type="text" class="form-control" name="checkout_customer" id="checkout_customer" value="" disabled>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="checkout_total" class="form-control-label">Grand Total</label>
                                        <input type="text" class="form-control" name="checkout_total" id="checkout_total" value="" disabled>
                                    </div>
                                    <div class="form-group col-md-6 required">
                                        <label for="checkout_table_no" class="form-control-label">Table No</label>
                                        <select class="form-control selectpicker" name="checkout_table_no" id="checkout_table_no" data-live-search="true" data-live-search-placeholder="Search" title="Select Please">
                                            @foreach ($order_table as $table)
                                                <option value="{{$table->id}}">{{$table->table_no}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6 required">
                                        <label for="payment_type" class="form-control-label">Payment Type</label>
                                        <select class="form-control selectpicker" name="payment_type" onchange="payment_type_change(this.value)" id="payment_type" data-live-search="true" data-live-search-placeholder="Search" title="Select Please">
                                            @foreach (PAYMENT_TYPE as $key => $value)
                                            @if($key != 3)
                                                <option value="{{$key}}">{{$value}}</option>
                                            @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6 mobile_card_common">
                                        <label for="card_holder" class="form-control-label">Account Name</label>
                                        <input type="text" class="form-control" name="card_holder" id="card_holder" placeholder="Enter account owner name">
                                    </div>
                                    <div class="form-group col-md-6 required c_banking">
                                        <label for="card_no" class="form-control-label">Card No.</label>
                                        <input type="text" class="form-control" name="card_no" id="card_no" placeholder="Enter card no.">
                                    </div>
                                    <div class="form-group col-md-6 required c_banking">
                                        <label for="expire_date" class="form-control-label">Expiry Date</label>
                                        <input type="text" class="form-control" onkeyup="check_expiry(this.value)" name="expire_date" id="expire_date" placeholder="Enter card expiry date">
                                    </div>
                                    <div class="form-group col-md-6 required c_banking">
                                        <label for="cvc_no" class="form-control-label">Cvc No.</label>
                                        <input type="text" maxlength="3" class="form-control" name="cvc_no" id="cvc_no" placeholder="Enter card cvc no.">
                                    </div>
                                    <div class="form-group col-md-6 required m_banking">
                                        <label for="m_banking_no" class="form-control-label">Mobile Banking No.</label>
                                        <input type="text" class="form-control" name="m_banking_no" id="m_banking_no" placeholder="Enter mobile banking no.">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" onclick="checkout_cart_data('cancel')">Close</button>
                                <button type="button" class="btn btn-brand btn-sm" onclick="checkout_cart_data('save')">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!--end: Checkout modal -->

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="./public/js/jquery-ui.js"></script>
<script>
    var limit = 12;
    var start = 0;
    var category = '';
    var name = '';
    var action = 'inactive';
    var table_options = [];
    var pendingOrderTable;

    $(document).ready(function () {
        get_category_list();
        if (action == 'inactive') {
            action = 'active';
            get_product_list(limit, start, category, name);
        }

        $(window).scroll(function () {
            if ($(window).scrollTop() + $(window).height() > $("#products").height() && action ==
                "inactive") {
                start = start + limit;
                action = 'active';
                setTimeout(function () {
                    get_product_list(limit, start, category, name);
                }, 1000);
            }
        });
        vat_calculate($("#vat_type option:selected").val());

        $.each(JSON.parse('<?php echo $order_table?>'), function (index, value) {
            table_options[value.id] = value.table_no;
        });

        get_customer_list();

        pendingOrderTable = $('#pending_order_table').DataTable({

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
                    data.order_type = "pending";
                    data._token = "{{csrf_token()}}";
                },
            },

            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0,5],
                    "orderable": false, //set not orderable
                    "className": "text-center",
                }
            ]
        });

        $('#pending_order_table_wrapper div:first').children('div:last').append('<button class="btn btn-danger float-right" onclick="delete_checked_order()"><i class="far fa-times-circle"></i>Delete All</button>')

        //BEGIN: SELECT ALL CHECKBOX CHECKED IF ANY ROW SELECTED CODE
        $(document).on('change', '.select_data', function () {
            var total = $('.select_data').length;
            var number = $('.select_data:checked').length;
            if ($(this).is(':checked')) {
                $(this).closest('tr').addClass('bg-danger');
                $(this).closest('tr').children('td').addClass('text-white');
            } else {
                $(this).closest('tr').removeClass('bg-danger');
                $(this).closest('tr').children('td').removeClass('text-white');
            }
            if (total == number) {
                $('.selectall').prop('checked', true);
            } else {
                $('.selectall').prop('checked', false);
            }
        });
        //END: SELECT ALL CHECKBOX CHECKED IF ANY ROW SELECTED CODE

        /* Start: Add item into cart by model*/
        $('#product_model').autocomplete({
            source: "{{route('autocomplete.search.product')}}",
            minLength: 1,
            response: function(event, ui) {
                if (ui.content.length == 1) {
                    $(this).autocomplete( "close" );
                    var product = ui.content[0];
                    add_to_cart(product.name, product.product_id, product.id, product.price, product.discount, product.qty, product.weight);
                };
            },
            select: function (event, ui) {
                $(this).val(ui.item.name);
                var product = ui.item;
                add_to_cart(product.name, product.product_id, product.id, product.price, product.discount, product.qty, product.weight);
            },
        }).data('ui-autocomplete')._renderItem = function (ul, item) {
            return $("<li class='ui-autocomplete-row'></li>")
                .data("item.autocomplete", item)
                .append(item.label)
                .appendTo(ul);
        };
        /* End: Add item into cart by model*/
    });

    // function cart_qty_plus(rowId) {
    //     var quantity = parseInt($('#qty_' + rowId).val());
    //     $('#qty_' + rowId).val(quantity + 1);
    //     var new_qty = $('#qty_' + rowId).val();
    //     // update_cart(rowId,new_qty);
    // }
    // function cart_qty_minus(rowId) {
    //     var quantity = parseInt($('#qty_' + rowId).val());
    //     if (quantity > 1) {
    //         $('#qty_' + rowId).val(quantity - 1);
    //     }
    //     var new_qty = $('#qty_' + rowId).val();
    //     if (new_qty >= 1) {
    //         // update_cart(rowId,new_qty);
    //     }

    // }



    /* Start: Product category list load*/
    function get_category_list() {
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
                    $('#category').html(data);
                    $('.selectpicker').selectpicker('refresh');
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }
    /* End: Product category list load*/


    /* Start: Product list load*/
    function get_product_list(limit, start, category, name) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{route('pos.product')}}",
            data: {
                limit: limit,
                start: start,
                category: category,
                name: name
            },
            type: "POST",
            success: function (data) {
                if (data) {
                    $('#products').append(data);
                    action = "inactive";
                } else {
                    action = "active";
                }
                $('[data-toggle="kt-tooltip"]').tooltip({
                        trigger: 'hover',
                        template: '<div class="tooltip tooltip-dark tooltop-auto-width" role="tooltip">\
                        <div class="arrow"></div>\
                        <div class="tooltip-inner"></div>\
                        </div>',
                });
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }
    /* End: Product list load*/


    /* Start: Search product by name or model*/
    function get_product_by_name(search_name) {
        if (event.keyCode == '13' || event.keyCode == '9') {
            name = $("#product_name").val();
            start = 0;
            $('#products').children().remove();
            get_product_list(limit, start, category, name);
        }
    }
    /* End: Search product by name or model*/


    /* Start: Search product by category*/
    function get_product_by_category(search_category) {
        start = 0;
        category = search_category.value;
        $('#products').children().remove();
        get_product_list(limit, start, category, name);
    }
    /* End: Search product by category*/


    /* Start: Add item into cart*/
    function add_to_cart(product_name, product_id, variation_id, price, discount, stock, weight) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{route('sale.store.cart')}}",
            data: {
                product_name: product_name,
                product_id: product_id,
                variation_id: variation_id,
                price: price,
                discount: discount,
                stock: stock,
                weight: weight
            },
            type: "POST",
            success: function (data) {
                bootstrap_notify(data.status, data.message);
                if (data.status == 'success') {
                    discount = discount > 0 ? discount : 0;

                    if (data.result_qty == 1) {
                        var sub_total = (price - (price * (discount / 100))) * 1;
                        cart_item_row(data.row_id,product_name,1,price,discount)
                    } else {
                        $('#qty_' + data.row_id).val(data.result_qty);
                        var sub_total = (price - (price * (discount / 100))) * data.result_qty;
                        $('#sub_total_' + data.row_id).text(parseFloat(sub_total).toFixed(2));
                    }
                    $('#cart_sub_total').text(parseFloat(data.sub_total).toFixed(2));
                    var total_discount = parseFloat($('#cart_discount').text()) + (price * (discount /
                    100));
                    $('#cart_discount').text(parseFloat(total_discount).toFixed(2));
                    var total = parseFloat($('#cart_sub_total').text()) - parseFloat($('#cart_discount').text());
                    $('#cart_total').text(total);
                    $('#payable').text(total);
                    vat_calculate($("#vat_type option:selected").val());
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }
    /* End: Add item into cart*/


    /* Start: Remove item from cart*/
    function cart_item_remove(item_row_id) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{route('sale.delete.item')}}",
            data: {
                item_row_id: item_row_id
            },
            type: "POST",
            success: function (data) {
                bootstrap_notify(data.status, data.message);
                $('[data-toggle="kt-tooltip"]').tooltip('hide');
                if (data.status == 'success') {
                    $('#cart_sub_total').text(parseFloat(data.sub_total).toFixed(2));
                    var total_discount = parseFloat($('#cart_discount').text());
                    if (total_discount > 0) {
                        var discount = parseFloat($('#price_' + item_row_id).text() * (parseFloat($(
                            '#discount_' + item_row_id).text()) / 100));
                        total_discount = total_discount - (discount * parseInt($('#qty_' + item_row_id)
                        .val()));
                        $('#cart_discount').text(parseFloat(total_discount).toFixed(2));
                    }
                    var total = parseFloat($('#cart_sub_total').text()) - parseFloat($('#cart_discount').text());
                    $('#cart_total').text(total);
                    $('#payable').text(total);
                    $('#item_row_id_' + item_row_id).remove();
                    var rows = $('.item_row').length;
                    for (var i = 0; i < rows; i++) {
                        $.each($('#cart_table tbody tr').eq(i), function (index, tr) {
                            $(this).find("td:first").text(i + 1);
                        });
                    }
                    vat_calculate($("#vat_type option:selected").val());
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }
    /* End: Remove item from cart*/


    /* Start: Update item quantity in cart*/
    function cart_qty_update(type, quantity, item_row_id) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{route('sale.update.cart')}}",
            data: {
                type: type,
                quantity: quantity,
                item_row_id: item_row_id
            },
            type: "POST",
            success: function (data) {
                bootstrap_notify(data.status, data.message);

                if (data.status == 'success') {
                    if (type == 'plus') {
                        quantity = parseInt($('#qty_' + item_row_id).val()) + 1;
                    } else if (type == 'less') {
                        quantity = parseInt($('#qty_' + item_row_id).val()) - 1;
                    }
                    $('#qty_' + item_row_id).val(quantity);
                    var row_sub_total = data.row_sum - (data.row_sum * (parseFloat($('#discount_' +
                        item_row_id).text()) / 100));
                    $('#sub_total_' + item_row_id).text(parseFloat(row_sub_total).toFixed(2));
                    $('#cart_sub_total').text(parseFloat(data.sub_total).toFixed(2));
                    var total_discount = parseFloat($('#cart_discount').text()) + data.discount_change;
                    $('#cart_discount').text(parseFloat(total_discount).toFixed(2));
                    var total = parseFloat($('#cart_sub_total').text()) - parseFloat($('#cart_discount').text());
                    $('#cart_total').text(total);
                    $('#payable').text(total);
                    vat_calculate($("#vat_type option:selected").val());
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }
    /* End: Update item quantity in cart*/


    /* Start: Calculate vat in cart*/
    function vat_calculate(vat) {
        var total = parseFloat($('#cart_sub_total').text()) - parseFloat($('#cart_discount').text());
        var total_vat = parseFloat(total * (parseFloat('{{$vat}}') / 100));
        $('#cart_vat').text(parseFloat(total_vat).toFixed(2));
        if (vat.value == 'exc') {
            total = parseFloat($('#cart_total').text()) + total_vat;
        }
        $('#cart_total').text(parseFloat(total).toFixed(2));
        $('#payable').text(parseFloat(total).toFixed(2));
    }
    /* End: Calculate vat in cart*/


    /* Start: Calculate adjustment in cart*/
    function adjustment_calculate(adjustment) {
        var total = parseFloat($('#cart_total').text());
        if (adjustment == '+') {
            total += parseFloat($('#adjustment').val());
            $('#payable').text(parseFloat(total).toFixed(2));
        } else if (adjustment == '-') {
            total -= parseFloat($('#adjustment').val());
            $('#payable').text(parseFloat(total).toFixed(2));
        } else {
                adjustment_calculate($("#adjustment_type option:selected").val());
        }
    }
    /* End: Calculate adjustment in cart*/


    /* Start: Calculate change amount in cart*/
    function receive_calculate(receive) {
        var changed = parseFloat($('#received').val()) - parseFloat($('#payable').text());
        if(changed > 0){
            $('#changed').text(parseFloat(changed).toFixed(2));
        }
    }
    /* End: Calculate change amount in cart*/


    /* Start: Clear cart or specific pending order*/
    function delete_pending_order(id) {
        event.preventDefault();
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
                url = "{{route('sale.delete.cart')}}";
                if(id != 0){
                    url = "{{route('sale.delete')}}";
                }if(id.length > 0){
                    url = "{{route('sale.bulkaction')}}";
                }

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                        url: url,
                        type: 'POST',
                        data: {id: id},
                        dataType: 'json'
                    })
                    .done(function (response) {
                        if (response.status == 'success') {
                            Swal.fire("Deleted!", response.message, "success").then(function () {
                                if(id == 0){
                                    clear_card_form();
                                    $("#customer option[value='']").attr("selected","selected");
                                    $('#customer').selectpicker('refresh');
                                }else{
                                    pendingOrderTable.ajax.reload();
                                }
                            });
                        } else if (response.status == 'danger') {
                            Swal.fire('Error deleting!', response.message, 'error');
                        }
                    })
                    .fail(function () {
                        swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            }
        })
    }
    /* End: Clear cart or specific pending order*/


    /* Start: Get cart form data*/
    function get_form_data(){
        return details = {
                customer: $('#customer').val(),
                discount: $('#cart_discount').text(),
                vat_type: $('#vat_type').val(),
                vat: $('#cart_vat').text(),
                adjustment_type: $('#adjustment_type').val(),
                adjustment: $('#adjustment').val(),
                total: $('#payable').text(),
                recevied: $('#received').val(),
                changed: $('#changed').text(),
            };
    }
    /* End: Get cart form data*/


    /* Start: Hold cart*/
    function hold_cart_data() {
        event.preventDefault();
        Swal.fire({
                title: 'Are you sure?',
                text: "Please select order table!",
                type: 'question',
                input: 'select',
                inputOptions: table_options,
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Confirm',
                confirmButtonColor: '#6610f2',
                cancelButtonColor: '#fd397a',
            })
            .then((result) => {
                if (result.value) {
                    var details = get_form_data();
                    details['status'] = 2;
                    details['table'] = $('.swal2-select').val();

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                            url: "{{route('sale.store')}}",
                            type: 'POST',
                            data: {
                                details: details
                            },
                            dataType: 'json'
                        })
                        .done(function (response) {
                            if (response.status == 'success') {
                                Swal.fire("Saved!", response.message, "success").then(function () {
                                    clear_card_form();
                                    pendingOrderTable.ajax.reload();
                                    $("#customer option[value='']").attr("selected","selected");
                                    $('#customer').selectpicker('refresh');
                                });
                            } else if (response.status == 'danger') {
                                Swal.fire('Error saving!', response.message, 'error');
                            }
                        })
                        .fail(function () {
                            swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                        });
                }
            })
    }
    /* End: Hold cart*/


    /* Start: Checkout cart*/
    function checkout_cart_data(type) {
        event.preventDefault();
        if(type == 'save'){
            var details = get_form_data();
            details['status'] = 1;
            details['order_id'] = $('#pending_order').val();
            details['table'] = $('#checkout_table_no').val();
            details['payment_type'] = $('#payment_type').val();
            details['card_holder'] = $('#card_holder').val();
            details['card_no'] = $('#card_no').val();
            details['expire_date'] = $('#expire_date').val();
            details['cvc_no'] = $('#cvc_no').val();
            details['m_banking_no'] = $('#m_banking_no').val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('sale.store')}}",
                type: 'POST',
                data: {
                    details: details
                },
                dataType: 'json'
            })
            .done(function (data) {
                $("#paymentDataForm").find('.is-invalid').removeClass('is-invalid');
                $("#paymentDataForm").find('.error').remove();

                if (data.status) {
                    bootstrap_notify(data.status,data.message);
                    if(data.status == 'success'){
                        $('#checkoutModal').modal('hide');
                        clear_card_form();
                        pendingOrderTable.ajax.reload();
                        var url = "{{url('sale/sale-invoice')}}/"+data.order_id;
                        window.open(url, "_blank");
                    }
                } else {
                    $.each(data.errors, function (key, value) {
                        key = key == 'table'?'checkout_table_no':key;
                        $("#paymentDataForm input[name='"+key+"']").addClass('is-invalid');
                        $("#paymentDataForm select#" + key).parent().addClass('is-invalid');
                        $("#paymentDataForm input[name='"+key+"']").after('<div id="'+key+'" class="error invalid-feedback">'+value+'</div>');
                        $("#paymentDataForm select#" + key).parent().after('<div id="'+key+'" class="error invalid-feedback">'+value+'</div>');
                    });
                }
            })
            .fail(function () {
                swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
            });
        }else{
            $("#paymentDataForm")[0].reset();
        }
    }
    /* End: Checkout cart*/


    /* Start: Expire date type generate*/
    function check_expiry(date){
        $('#expire_date').attr('maxlength', '');
        var number = date.match(/[0-9]/gi);
        var date = '';
        $.each(number, function (index, value) {
            date += date.length == 2?'/':'';
            date += value;
        });
        if($('#expire_date').val().length <= 5){
            $('#expire_date').attr('maxlength', '5');
            $('#expire_date').val(date);
        }
    }
    /* End: Expire date type generate*/


    /* Start: Customer data insert or reset*/
    function customer_modal_btn(type) {
        if(type == 'save'){
            $.ajax({
                url: "{{route('customer.store')}}",
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
                            $('#saveDataModal').modal('hide');
                            get_customer_list();
                        }
                    } else {
                        $.each(data.errors, function (key, value) {
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
        }else{
            $("#saveDataForm")[0].reset();
        }
    }
    /* End: Customer data insert or reset*/


    /* Start: Get customer list*/
    function get_customer_list() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{route('customer.list.pos')}}",
            type: "POST",
            success: function (data) {
                $('#customer').find('option').remove();
                $("#customer").append('<option value="" >Walking Customer</option>');
                $.each(data, function (key, value) {
                    $("#customer").append(new Option(value.name, value.id));
                });
                var update_order = JSON.parse('<?php echo json_encode(session()->get('update_order')); ?>');
                if(update_order){
                    $("#customer option[value='"+update_order[1]+"']").attr("selected","selected");
                }else{
                    $("#customer option[value='']").attr("selected","selected");
                }
                $('#customer').selectpicker('refresh');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }
    /* End: Get customer list*/


    /* Start: Cart item row*/
    function cart_item_row(rowId,name,qty,price,discount){
        var sub_total = (price - (price * (discount / 100))) * qty;
        var row = '<tr class="item_row" id="item_row_id_' + rowId + '">' +
                        '<td class="text-center">' + ($('.item_row').length + 1) + '</td>' +
                        '<td class="text-left">' + name + '</td>' +
                        '<td class="text-center d-flex">' +
                        '<button type="button" class="btn btn-danger btn-icon pull-left" onclick="cart_qty_update(\'less\',1,\'' +
                        rowId +
                        '\')" style="width:20px;height:20px;" data-skin="dark"  data-toggle="kt-tooltip" data-placement="top" title="" data-original-title="Quantity Minus">-</button>' +
                        '<input class="form-control text-center" onkeyup="cart_qty_update(\'default\',this.value,\'' +
                        rowId + '\')" id="qty_' + rowId +
                        '" type="text"  value="'+qty+'" style="height: 20px;width:40px;padding:0;"/>' +
                        '<button type="button" class="btn btn-info btn-icon pull-right" onclick="cart_qty_update(\'plus\',1,\'' +
                        rowId +
                        '\')" style="width:20px;height:20px;" data-skin="dark"  data-toggle="kt-tooltip" data-placement="top" title="" data-original-title="Quantity Plus">+</button>' +
                        '</td>' +
                        '<td class="text-right" id="price_' + rowId + '">' + parseFloat(price).toFixed(2) + '</td>' +
                        '<td class="text-center" id="discount_' + rowId + '">' + parseFloat(discount).toFixed(2) +
                        '%</td>' +
                        '<td class="text-right" id="sub_total_' + rowId + '">' + parseFloat(sub_total).toFixed(2) +
                        '</td>' +
                        '<td class="text-center">' +
                        '<button type="button" onclick="cart_item_remove(\'' + rowId +
                        '\')" class="btn btn-danger btn-icon pull-right mt-1" style="width:20px;height:20px;" data-skin="dark"  data-toggle="kt-tooltip" data-placement="top" title="" data-original-title="Remove Product">' +
                        '<i class="fas fa-trash text-white" style="font-size:10px;"></i>' +
                        '</button>' +
                        '</td>' +
                        '</tr>';

                    $('#total_row').before(row);
                    $('[data-toggle="kt-tooltip"]').tooltip({
                        trigger: 'hover',
                        template: '<div class="tooltip tooltip-dark tooltop-auto-width" role="tooltip">\
                        <div class="arrow"></div>\
                        <div class="tooltip-inner"></div>\
                        </div>',
                });
    }
    /* End: Cart item row*/


    /* Start: Edit & update pending order*/
    function edit_pending_order(id) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{route('sale.edit')}}",
            data: {id:id},
            type: "POST",
            success: function (data) {
                $('.item_row').remove();
                clear_card_form();
                var sub_total = 0;
                var total_discount = 0;
                $.each(data.order, function (index, value) {
                    sub_total = sub_total + (value.price * value.qty);
                    total_discount = total_discount + parseFloat($('#cart_discount').text()) + ((value.price * (value.options['discount'] / 100))  * value.qty);
                    cart_item_row(value.rowId,value.name,value.qty,value.price,value.options['discount'])
                });
                $('#cart_sub_total').text(parseFloat(sub_total).toFixed(2));
                $('#cart_discount').text(parseFloat(total_discount).toFixed(2));
                var total = parseFloat($('#cart_sub_total').text()) - parseFloat($('#cart_discount').text());
                $('#cart_total').text(parseFloat(total).toFixed(2));
                $('#payable').text(parseFloat(total).toFixed(2));
                vat_calculate($("#vat_type option:selected").val());
                $('#customer option:selected').removeAttr('selected');
                $("#customer option[value='"+data.update_order[1]+"']").attr("selected","selected");
                $('#customer').selectpicker('refresh');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }
    /* End: Edit & update pending order*/


    /* Start: Checkout pending order*/
    function checkout_pending_order(id,customer,table,total) {
        event.preventDefault();
        if(id == 0){
            customer = $( "#customer option:selected" ).text();
            total = $('#cart_total').text();
        }
        $('#checkoutModal').modal('show');
        $('#pending_order').val(id);
        $('#checkout_customer').val(customer);
        $('#checkout_total').val(total);
        $('#checkout_table_no').val(table);
        $('.selectpicker').selectpicker('refresh');
        payment_type_change(1);
    }
    /* End: Checkout pending order*/


    /* Start: Delete all pending orders*/
    function delete_checked_order(){
        var id = [];
        var rows;
        $('.select_data:checked').each(function(i){
            id.push($(this).val());
            rows = pendingOrderTable.rows( $('.select_data:checked').parents('tr') );
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
            var url = "{{route('sale.bulkaction')}}";
            delete_pending_order(id);
        }
    }
    /* End: Delete all pending orders*/


    /* Start: Clear cart form*/
    function clear_card_form() {
        $('.item_row').remove();
        $('#cart_sub_total').text('0.00');
        $('#cart_discount').text('0.00');
        $('#cart_vat').text('0.00');
        $('#cart_total').text('0.00');
        $('#payable').text('0.00');
        $('#adjustment').val('0.00');
        $('#received').val('0.00');
        $('#changed').text('0.00');
        $("#vat_type").val("inc");
        $("#adjustment_type").val("+");
    }
    /* End: Clear cart form*/


    /* Start: Payment input form change*/
    function payment_type_change(type){
        if(type == 1){
            $('.c_banking').hide();
            $('.m_banking').hide();
            $('.mobile_card_common').hide();
        }else if(type == 2){
            $('.c_banking').show();
            $('.m_banking').hide();
            $('.mobile_card_common').show();
        }else{
            $('.c_banking').hide();
            $('.m_banking').show();
            $('.mobile_card_common').show();
        }
    }
    /* End: Payment input form change*/
</script>
@endpush