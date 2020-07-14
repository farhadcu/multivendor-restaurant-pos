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
                <table class="table">
                    <tr>
                        <td width="15%">@if (Auth::user()->company->logo != NULL)
                            <img alt="Logo" src="{{ asset(FOLDER_PATH.COMPANY_PHOTO.Auth::user()->company->logo) }}" style="width:80px;" />
                            @endif</td>
                        <td width="70%" class="text-left">
                            <h4>{{Auth::user()->company->company_name}}</h4>
                        <h5>{{$branch->branch_name}}</h5>
                        <span>{{$branch->branch_mobile}}</span><br>
                        @if($branch->branch_email)<span>{{$branch->branch_email}}</span><br>@endif
                        <span>{{$branch->branch_address}}</span>
                   
                        </td>
                        <td width="15%" class="text-right"><h1>INVOICE</h1></td>
                    </tr>
                </table>
                <div class="m-invoice__items">
                    <div class="m-invoice__item" style="padding-right:20px;width:65%;">
                        <span class="m-invoice__subtitle font-weight-bold">
                            To
                        </span>
                        @if (!empty($invoiceData[0]->cname))
                        <span class="m-invoice__text"><strong>{{$invoiceData[0]->cname}}</strong></span>
                        <span class="m-invoice__text phone">{{$invoiceData[0]->mobile}}</span>
                        <span class="m-invoice__text email">{{$invoiceData[0]->email}}</span>
                        <span class="m-invoice__text email">{{$invoiceData[0]->address}}</span>
                        @else
                        <span class="m-invoice__text"><strong>Walking Customer</strong></span>
                        @endif
                    </div>
                   
                    <div class="m-invoice__item text-right" style="width:35%;">
                        <span class="m-invoice__subtitle text-left invoice_no">
                            <b>Invoice No </b>
                            <strong class="float-right">INV{{$invoiceData[0]->id}}</strong>
                        </span>
                        <span class="m-invoice__text one"><b>Order Status</b> {{ORDER_STATUS[$invoiceData[0]->status]}}</span>
                        <span class="m-invoice__text one"><b>Ordered Date</b> {{date('j-M-Y',strtotime($invoiceData[0]->created_at))}}</span>
                    </div>
                </div>
                <div class="table-responsive mt-5">
                <table class="table table-striped">
                    <thead>
                        <th class="text-center text-bold">SL</th>
                        <th class="text-left text-bold">Product</th>
                        <th class="text-center text-bold">Qty</th>
                        <th class="text-right text-bold">Unit Price(BDT)</th>
                        <th class="text-right text-bold">Subtotal(BDT)</th>
                    </thead>
                    <tbody>
                    @if (!empty($invoiceData))
                        @php
                            $i=1;
                        @endphp
                        @foreach ($invoiceData as $data)
                            <tr>
                                <td class="text-center">{{$i++}}</td>
                                <td>{{$data->name}}({{$data->variation_name}})</td>
                                <td class="text-center">{{$data->qty}}</td>
                                <td class="text-right">{{number_format($data->price,2)}}</td>
                                <td class="text-right">{{number_format($data->subtotal,2)}}</td>
                            </tr>
                        @endforeach
                    @endif
                    <tr>
                        <td colspan="4" style="text-align:right;"><b>Total</b></td>
                        
                        <td class="text-right text-bold">{{number_format($invoiceData[0]->total_amount,2)}}</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align:right;"><b>Discount</b></td>
                        
                        <td class="text-right text-bold">{{number_format($invoiceData[0]->discount_amount,2)}}</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align:right;"><b>Vat({{$invoiceData[0]->vat_type}}){{number_format(auth()->user()->company['vat'], 2, '.', '')}}%</b></td>
                        
                        <td class="text-right text-bold">{{number_format($invoiceData[0]->vat,2)}}</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align:right;"><b>Adjustment({{$invoiceData[0]->adjustment_type}})</b></td>
                        
                        <td class="text-right text-bold">{{number_format($invoiceData[0]->adjusted_amount,2)}}</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align:right;"><b>Grand Total</b></td>
                        
                        <td class="text-right text-bold">{{number_format($invoiceData[0]->grand_total,2)}}</td>
                    </tr>
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