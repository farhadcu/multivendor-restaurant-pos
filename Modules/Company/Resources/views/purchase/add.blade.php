@extends('company.layouts.app')

@section('title')
{{ucwords($page_title)}}
@endsection

@push('styles')
<link rel="stylesheet" href="./public/css/jquery-ui.css" />
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
        <form method="POST" id="saveDataForm">
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
                    
                    <a href="{{url('purchase')}}" class="btn btn-info btn-icon-sm btn-sm">
                        <i class="la la-long-arrow-left"></i>
                        Back
                    </a>
                </div>
            </div>
        </div>
        <div class="kt-portlet__body">
            <div class="kt-form kt-form--label-right kt-margin-b-10">
                
                <p class="italic text-danger">The field labels marked with (*) are required input fields.</p>
                        <div class="row ">
                            
                            <div class="form-group col-md-4">
                                <label for="supplier_id">Supplier</label>
                                <select class="form-control selectpicker" name="supplier_id" id="supplier_id" data-live-search="true" data-live-search-placeholder="Search" title="Select Please">
                                    <option value="">Select Please</option>
                                    @if (!empty($data['suppliers']))
                                        @foreach ($data['suppliers'] as  $value)
                                            <option value="{{$value->id}}">{{$value->supplier_name.' ('.$value->supplier_company_name.')'}}</option>
                                         @endforeach
                                    @endif
                                    
                                </select>
                            </div>
                            <div class="form-group col-md-4 required">
                                <label for="status">Purchase Status</label>
                                <select class="form-control selectpicker" name="status" id="status" onchange="show_received_qty(this.value)" data-live-search="true" data-live-search-placeholder="Search" title="Select Please">
                                    <option value="">Select Please</option>
                                    @foreach (PURCHASE_STATUS as $key => $value)
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                                
                            </div>
                            <div class="form-group col-md-4">
                                <label for="document">Attach Document <i class="fas fa-info-circle text-info" data-toggle="kt-tooltip" data-skin="dark"
                                    data-placement="top" title="" data-original-title="Only jpg, jpeg, png, gif, pdf, csv, docx, xlsx and txt file is supported"></i></label>
                                <input type="file" class="form-control" name="document" id="document">
                            </div>
                            <div class="form-group col-md-12">
                                <label for="">Select Product</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                      <span class="input-group-text bg-brand"><i class="fas fa-barcode text-white"></i></span>
                                    </div>
                                    <input type="text" class="form-control" id="product_search" placeholder="Please type product code and select">
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered" style="font-size:12px;" id="cart_table">
                                <thead>
                                    <th width="5%" class="text-center font-weight-bold">SL</th>
                                    <th width="20%" class="text-left font-weight-bold">Name</th>
                                    <th width="15%" class="text-left font-weight-bold">Code</th>
                                    <th width="8%" class="text-center font-weight-bold">Qty</th>
                                    <th width="10%" class="text-center font-weight-bold received_qty d-none">Received</th>
                                    <th width="12%" class="text-center font-weight-bold">Unit</th>
                                    <th width="10%" class="text-right font-weight-bold">Price</th>
                                    <th width="15%" class="text-right font-weight-bold">Subtotal</th>
                                    <th width="5%"></th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="row mt-5">
                            <div class="form-group col-md-3">
                                <label for="">Discount</label>
                                <input type="text" class="form-control" oninput="calculate_grand_total()" name="order_discount" id="order_discount" value="0" style="text-align:right">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="">Tax</label>
                                <input type="text" class="form-control" oninput="calculate_grand_total()" name="order_tax_amount" id="order_tax_amount" value="0" style="text-align:right">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="">Shipping Cost</label>
                                <input type="text" class="form-control" oninput="calculate_grand_total()" name="shipping_cost" id="shipping_cost" value="0" style="text-align:right">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="">Grand Total</label>
                                <input type="text" class="form-control" name="grand_total" id="grand_total" value="{{Cart::instance('purchase')->total()}}" style="text-align:right;" readonly>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="note">Note</label>
                                <textarea rows="5" class="form-control" name="note" id="note"></textarea>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            {{-- <div class="col-md-3"> --}}
                                @if (Helper::permission('purchase-add'))
                                <button type="submit"  class="btn btn-brand btn-icon-sm btn-sm mr-2 col-md-1" id="save-btn">
                                    <i class="fas fa-save"></i> Save
                                </button>
                                <button type="button"  class="btn btn-danger btn-icon-sm btn-sm col-md-1" onclick="clear_cart_product()">
                                    <i class="fas fa-ban"></i> Clear
                                </button>
                                @endif
                            {{-- </div> --}}
                        </div>

            </div>
            <!--end: Search Form -->
        </div>
        </form> 

    </div>
</div>

@endsection

@push('scripts')
<script src="./public/js/jquery-ui.js"></script>
<script>

$(document).ready(function () {

    $('#product_search').val('');
    cart_content();
    /*
    By  : Mohammad Arman
    Text: Autocomplete Product Search Code
    Date: 10-Feb-2020
    */
    $('#product_search').autocomplete({
        source: "{{route('autocomplete.search.product')}}",
        minLength: 1,
        response: function(event, ui) {
            if (ui.content.length == 1) {
                $(this).autocomplete( "close" );
                add_cart_product(ui.content[0].id);
            };
        },
        select: function (event, ui) {
            $(this).val(ui.item.value);
            add_cart_product(ui.item.id);
        },
    }).data('ui-autocomplete')._renderItem = function (ul, item) {
        return $("<li class='ui-autocomplete-row'></li>")
            .data("item.autocomplete", item)
            .append(item.label)
            .appendTo(ul);
    };

    // $('#paid_amount').on('keyup',function(){
    //     if(parseFloat($('#paid_amount').val().replace(',', '')) > parseFloat($('#grand_total').val().replace(',', '')))
    //     {
    //         $('#paid_amount').val($('#grand_total').val());
    //     }
    //     due_amount();
    // });

    $('#saveDataForm').on('submit', function(event){
        event.preventDefault();

        $.ajax({
            url: "{{route('purchase.store')}}",
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
                $("#saveDataForm table tbody").find('.is-invalid').removeClass('is-invalid');
                $("#saveDataForm table tbody").find('.error').remove();   
                if (data.status) {
                    bootstrap_notify(data.status,data.message);
                    if(data.status == 'success'){
                        window.location.replace("{{url('purchase')}}");
                    }
                } else {
                    $.each(data.errors, function (key, value) {
                        var key = key.split('.').join("_");
                        $("#saveDataForm input[name='"+key+"']").addClass('is-invalid');
                        $("#saveDataForm select#" + key+".selectpicker").parent().addClass('is-invalid');
                        $("#saveDataForm textarea[name='" + key + "']").parent().addClass('is-invalid');
                        $("#saveDataForm input[name='"+key+"']").after('<div id="'+key+'" class="error invalid-feedback">'+value+'</div>');
                        $("#saveDataForm select#" + key+".selectpicker").parent().after('<div id="'+key+'" class="error invalid-feedback">'+value+'</div>');
                        $("#saveDataForm textarea[name='"+key+"']").after('<div id="'+key+'" class="error invalid-feedback">'+value+'</div>');
                    
                        $("#saveDataForm table tbody").find("#"+key).addClass('is-invalid');
                        $("#saveDataForm table tbody").find("#"+key).after('<div id="'+key+'" class="error invalid-feedback">'+value+'</div>');
                   
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });
});

function show_received_qty(value)
{
    if(value == 2){
        $("#cart_table .received_qty").removeClass("d-none");
        $("#cart_table .col-section").attr("colspan",'7');
    }else {
        $("#cart_table .received_qty").addClass("d-none");
        $("#cart_table .col-section").attr("colspan",'6');
    }
}

function add_cart_product(product_variation_id) {
    if(product_variation_id){
        var _token = "{{csrf_token()}}";
        $.ajax({
            url: '{{url("purchase-cart-product-add")}}',
            type: "POST",
            data: {
                product_variation_id: product_variation_id,
                _token: _token
            },
            dataType: "JSON",
            success: function (data) {
                $('#product_search').val('');
                cart_content();
                if(data == 'Exist'){
                    bootstrap_notify('danger','Product already in list.')
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR + '<br>' + textStatus + '<br>' + errorThrown);
            }
        });
    }
}

function update_cart_product(product_variation_id)
{
    if(product_variation_id){
        var _token            = "{{csrf_token()}}";
        var qty               = $('#qty_'+product_variation_id).val();
        var price             = $('#price_'+product_variation_id).val().replace(',', '');
        var product_id        = $('#product_id_'+product_variation_id).val();
        var variation_model   = $('#variation_model_'+product_variation_id).val();
        var received_qty      = $('#received_qty_'+product_variation_id).val();
        var purchase_unit     = $('#purchase_unit_'+product_variation_id+' option:selected').val();
        if(qty && price && purchase_unit){
            $.ajax({
                url: '{{url("purchase-cart-product-update")}}',
                type: "POST",
                data: {
                    product_variation_id: product_variation_id,qty:qty,price:price,product_id:product_id,
                    variation_model:variation_model,received_qty:received_qty,purchase_unit:purchase_unit,_token: _token
                },
                dataType: "JSON",
                success: function (data) {
                    cart_content();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR + '<br>' + textStatus + '<br>' + errorThrown);
                }
            });
        }
        
    }
}

function remove_cart_product(product_variation_id)
{
    if(product_variation_id){
        var _token = "{{csrf_token()}}";
        $.ajax({
            url: '{{url("purchase-cart-product-remove")}}',
            type: "POST",
            data: {
                product_variation_id: product_variation_id,
                _token: _token
            },
            dataType: "JSON",
            success: function (data) {
                cart_content();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR + '<br>' + textStatus + '<br>' + errorThrown);
            }
        });
    }
}

function clear_cart_product()
{
    var _token = "{{csrf_token()}}";
    $.ajax({
        url: '{{url("purchase-cart-clear")}}',
        type: "POST",
        data: { _token: _token},
        dataType: "JSON",
        success: function (data) {
            cart_content();
            $('#saveDataForm')[0].reset();
            $('#saveDataForm .selectpicker').selectpicker('refresh');
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR + '<br>' + textStatus + '<br>' + errorThrown);
        }
    });
}

function cart_content() {
    $.ajax({
        url: '{{url("purchase-cart-content")}}',
        type: "GET",
        success: function (data) {
            $('#cart_table').html('');
            $('#cart_table').html(data);
            
            if($("#status option:selected").val() == 2){
                $("#cart_table .received_qty").removeClass("d-none");
                $("#cart_table .col-section").attr("colspan",'7');
            }else {
                $("#cart_table .received_qty").addClass("d-none");
                $("#cart_table .col-section").attr("colspan",'6');
            }

            if($('#cart_table tbody tr').length > 0){
                $('.payment_section').removeClass('d-none');
                calculate_grand_total();
                $('#saveDataForm #save-btn').prop('disabled',false);
            }else{
                $('.payment_section').addClass('d-none');
                $('#saveDataForm #save-btn').prop('disabled',true);
            }
            

           
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR + '<br>' + textStatus + '<br>' + errorThrown);
        }
    });
}

function calculate_grand_total()
{
    var grand_total  = (parseFloat($('#total_amount').text().replace(',', '')) - parseFloat($('#order_discount').val().replace(',', ''))) 
                        + parseFloat($('#order_tax_amount').val().replace(',', '')) + parseFloat($('#shipping_cost').val().replace(',', ''));
    $('#grand_total').val(parseFloat(grand_total).toFixed(2));
}

// function due_amount()
// {
//     var due_amount = parseFloat($('#grand_total').val().replace(',', '')) - parseFloat($('#paid_amount').val().replace(',', ''));
//     $('#due_amount').val(parseFloat(due_amount).toFixed(2));
// }
</script>
@endpush