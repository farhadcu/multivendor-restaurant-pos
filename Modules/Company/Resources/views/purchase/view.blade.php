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
                    <button type="button" id="printButton"  class="btn btn-warning btn-icon-sm btn-sm mr-2 text-white">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <a href="{{url('purchase')}}" class="btn btn-info btn-icon-sm btn-sm">
                        <i class="la la-long-arrow-left"></i>
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center bg-white">
        <div class="col-lg-12">
            <div class="purchase_invoice" id="purchase_invoice">
                <link href="{{asset('public/css/print.css')}}" rel="stylesheet" type="text/css" />
                <table class="table" id="header">
                    <tr style="padding-top:20px;">
                        <td width="10%" style="padding-top:20px;">@if (Auth::user()->company->logo != NULL)
                            <img alt="Logo" src="{{ asset(FOLDER_PATH.COMPANY_PHOTO.Auth::user()->company->logo) }}" style="max-width:100px;" />
                            @endif</td>
                        <td width="75%" class="text-left" style="padding-top:20px;">
                            <h4>{{Auth::user()->company->company_name}}</h4>
                        <h5>{{$data['purchase']->branch->branch_name}}</h5>
                        <span>{{$data['purchase']->branch->branch_mobile}}</span><br>
                        @if($data['purchase']->branch->branch_email)<span>{{$data['purchase']->branch->branch_email}}</span><br>@endif
                        @if($data['purchase']->branch->branch_address)<span>{{$data['purchase']->branch->branch_address}}</span>@endif
                   
                        </td>
                        <td width="15%" class="text-right" style="padding-top:20px;"><h1>INVOICE</h1></td>
                    </tr>
                </table>
                <div class="m-invoice__items">
                    <div class="m-invoice__item" style="padding-right:20px;width:65%;">
                        <span class="m-invoice__subtitle font-weight-bold">
                            To
                        </span>
                        @if (!empty($data['purchase']->supplier_id))
                        <span class="m-invoice__text"><strong>{{$data['supplier']->supplier_name}}</strong></span>
                        <span class="m-invoice__text phone">{{$data['supplier']->supplier_mobile}}</span>
                        <span class="m-invoice__text email">{{$data['supplier']->supplier_email}}</span>
                        <span class="m-invoice__text email">{{$data['supplier']->supplier_address}}</span>
                        @endif
                    </div>
                   
                    <div class="m-invoice__item text-right" style="width:35%;">
                        <span class="m-invoice__subtitle text-left invoice_no">
                            <b>Purchase No </b>
                            <strong class="float-right">{{$data['purchase']->purchase_no}}</strong>
                        </span>
                        <span class="m-invoice__text one"><b>Received Status</b> {{PURCHASE_STATUS[$data['purchase']->status]}}</span>
                        <span class="m-invoice__text one"><b>Ordered Date</b> {{date('j-M-Y',strtotime($data['purchase']->created_at))}}</span>
                        <span class="m-invoice__text one"><b>Payment Status</b> {{PAYMENT_STATUS[$data['purchase']->payment_status]}}</span>
                    
                    </div>
                </div>
                <div class="table-responsive mt-5">
                <table class="table table-striped">
                    <thead>
                        <th class="text-center text-bold">SL</th>
                        <th class="text-left text-bold">Product</th>
                        <th class="text-center text-bold">Qty</th>
                        @if ($data['purchase']->status == 2)
                        <th class="text-center text-bold">Received Qty</th>
                        @endif
                        <th class="text-right text-bold">Unit Price(BDT)</th>
                        <th class="text-right text-bold">Subtotal(BDT)</th>
                    </thead>
                    <tbody>
                    @if (!empty($data['products']))
                        @php
                            $i=1;
                        @endphp
                        @foreach ($data['products'] as $item)
                            <tr>
                                <td class="text-center">{{$i++}}</td>
                                <td>{{$item->name.' ('.$item->variation_name.')'}}<br>{{$item->variation_model}}</td>
                                <td class="text-center">{{$item->qty.$item->purchase_unit}}</td>
                                @if ($data['purchase']->status == 2)
                                <td class="text-center">{{$item->recieved.$item->purchase_unit}}</td>
                                @endif
                                <td class="text-right">{{number_format($item->net_unit_cost,2)}}</td>
                                <td class="text-right">{{number_format($item->total,2)}}</td>
                            </tr>
                        @endforeach
                    @endif
                    <tr>
                        <td colspan="@if ($data['purchase']->status == 2) 5 @else 4 @endif" style="text-align:right;"><b>Total</b></td>
                        
                        <td class="text-right text-bold">{{number_format($data['purchase']->total_cost,2)}}</td>
                    </tr>
                    <tr>
                        <td colspan="@if ($data['purchase']->status == 2) 5 @else 4 @endif" style="text-align:right;"><b>Discount</b></td>
                        
                        <td class="text-right text-bold">{{number_format($data['purchase']->order_discount,2)}}</td>
                    </tr>
                    <tr>
                        <td colspan="@if ($data['purchase']->status == 2) 5 @else 4 @endif" style="text-align:right;"><b>Tax</b></td>
                        
                        <td class="text-right text-bold">{{number_format($data['purchase']->order_tax_amount,2)}}</td>
                    </tr>
                    <tr>
                        <td colspan="@if ($data['purchase']->status == 2) 5 @else 4 @endif" style="text-align:right;"><b>Shipping Cost</b></td>
                        
                        <td class="text-right text-bold">{{number_format($data['purchase']->shipping_cost,2)}}</td>
                    </tr>
                    <tr>
                        <td colspan="@if ($data['purchase']->status == 2) 5 @else 4 @endif" style="text-align:right;"><b>Grand Total</b></td>
                        
                        <td class="text-right text-bold">{{number_format($data['purchase']->grand_total,2)}}</td>
                    </tr>
                    <tr>
                        <td colspan="@if ($data['purchase']->status == 2) 5 @else 4 @endif" style="text-align:right;"><b>Paid Amount</b></td>
                        
                        <td class="text-right text-bold">{{number_format($data['purchase']->paid_amount,2)}}</td>
                    </tr>
                    <tr>
                        <td colspan="@if ($data['purchase']->status == 2) 5 @else 4 @endif" style="text-align:right;"><b>Due Amount</b></td>
                        
                        <td class="text-right text-bold">{{number_format($data['purchase']->due_amount,2)}}</td>
                    </tr>
                    </tbody>
                </table>
                </div>
                <div class="table-responsive mt-5">
                    <h4 style="font-weigh:bold;">All Payments</h4>
                    <table class="table  table-striped">
                        <thead>
                            <th style="font-weight:bold;text-align:center;">SL</th>
                            <th style="font-weight:bold;text-align:center;">Date</th>
                            <th style="font-weight:bold;text-align:center;">Paid By</th>
                            <th style="font-weight:bold;text-align:right;">Amount(BDT)</th>
                        </thead>
                        <tbody>
                            @if (!empty($data['payments']))
                                @php
                                    $j=1;
                                @endphp
                                @foreach ($data['payments'] as $item)
                                <tr>
                                    <td style="text-align:center;">{{$j++}}</td>
                                    <td style="text-align:center;">{{date('j-M-Y',strtotime($item->created_at,2))}}</td>
                                    <td style="text-align:center;">{{PAYMENT_TYPE[$item->payment_method]}}</td>
                                    <td style="text-align:right;">{{number_format($item->amount,2)}}</td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="./public/js/jquery.printarea.js" type="text/javascript"></script>
<script>
$(document).ready(function(){

$("#printButton").click(function () {
    var mode = 'iframe'; // popup
    var close = mode == "popup";
    var options = {mode: mode, popClose: close};
    $("#purchase_invoice").printArea(options);
});
});
// function printPageArea(){
// var printContent = document.getElementById("purchase_invoice");
// var WinPrint = window.open('', '', 'width=900,height=650');
// WinPrint.document.write(printContent.innerHTML);
// WinPrint.document.close();
// WinPrint.focus();
// WinPrint.print();
// // WinPrint.close();
// }
</script>
@endpush