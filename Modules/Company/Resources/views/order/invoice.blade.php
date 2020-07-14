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
                    <div class="col-sm-12">
                        <div class="panel panel-bd">
                            <div id="printableArea">
                                <div class="panel-body">
                                    <div bgcolor='#e4e4e4' text='#ff6633' link='#666666' vlink='#666666' alink='#ff6633'
                                        style='margin:0;font-family:Arial,Helvetica,sans-serif;border-bottom:1'>
                                        <table border="0" width="35%">
                                            <tr>
                                                <td>
                                                    <table border="0" width="100%">
                                                        <tr>
                                                            <td align="center" style="border-bottom:2px #333 solid;">

                                                                <span style="font-size: 13pt; font-weight:bold;">
                                                                    {{Auth::user()->company->company_name}}
                                                                </span><br>
                                                                <span style="font-size: 10pt; font-weight:bold;">
                                                                    {{$branch->branch_name}}
                                                                </span><br>
                                                                {{$branch->branch_address}}<br>
                                                                {{$branch->branch_mobile}}<br>

                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td align="center" style="padding-top:10px;">
                                                                <nobr>
                                                                    <date>
                                                                        Date: {{date('j-M-Y',strtotime($invoiceData[0]->created_at))}}
                                                                    </date>
                                                                </nobr>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Sell No : INV{{$invoiceData[0]->id}}</strong></td>
                                                        </tr>
                                                    </table>
                                                    <table width="100%" style="font-size: 12px;">
                                                        <tr style="line-height: 20px;">
                                                            <td width="10%"><b>Sl</b></th>
                                                            <td width="30%"><b>Item</b></td>
                                                            <td width="15%" align="center"><b>Qty</b></td>
                                                            <td width="20%" align="right"><b>Price</b></td>
                                                            <td width="25%" align="right"><b>Subtotal</b></td>
                                                        </tr>

                                                        <tr style="">
                                                            <td colspan="5" style="border-top:#333 1px solid;">
                                                                <nobr></nobr>
                                                            </td>
                                                        </tr>
                                                        <?php $i = 1; $total_discount = []; ?>
                                                        @foreach($invoiceData as $data)
                                                        <tr>
                                                            <td>{{$i}}</td>
                                                            <td>{{$data->name}}({{$data->variation_name}})</td>
                                                            <td align="center">{{$data->qty}}</td>
                                                            <td align="right">{{$data->price}}</td>
                                                            <td align="right">{{$data->subtotal}}</td>
                                                        </tr>
                                                        <?php $i++ ?>
                                                        @endforeach
                                                        <tr style="">
                                                            <td colspan="5" style="border-top:#333 1px solid;">
                                                                <nobr></nobr>
                                                            </td>
                                                        </tr>
                                                        <tr style="height: 25px;">
                                                            <td align="left" colspan="4">
                                                                <nobr><b>Total</b></nobr>
                                                            </td>
                                                            <td align="right">
                                                                <span style="float:left;">BDT</span>
                                                                <nobr> {{$invoiceData[0]->total_amount}} </nobr>
                                                            </td>
                                                        </tr>
                                                        <tr style="height: 25px;">
                                                            <td align="left" colspan="4">
                                                                <nobr><b>Discount</b></nobr>
                                                            </td>
                                                            <td align="right">
                                                                <span style="float:left;">BDT</span>
                                                                <nobr>{{$invoiceData[0]->discount_amount}} </nobr>
                                                            </td>
                                                        </tr>
                                                        <tr style="height: 25px;">
                                                            <td align="left" colspan="4">
                                                                <nobr><b>Vat({{$invoiceData[0]->vat_type}}){{number_format(auth()->user()->company['vat'], 2, '.', '')}}%</b></nobr>
                                                            </td>
                                                            <td align="right">
                                                                <span style="float:left;">BDT</span>
                                                                <nobr>{{$invoiceData[0]->vat}} </nobr>
                                                            </td>
                                                        </tr>
                                                        <tr style="height: 25px;">
                                                            <td align="left" colspan="4">
                                                                <nobr><b>Adjustment({{$invoiceData[0]->adjustment_type}})</b></nobr>
                                                            </td>
                                                            <td align="right">
                                                                <span style="float:left;">BDT</span>
                                                                <nobr>{{$invoiceData[0]->adjusted_amount}} </nobr>
                                                            </td>
                                                        </tr>
                                                        <tr style="height: 25px;">
                                                            <td colspan="5" style="border-top:#333 1px solid;">
                                                                <nobr></nobr>
                                                            </td>
                                                        </tr>
                                                        <tr style="height: 25px;">
                                                            <!--<td align="left"><nobr></nobr></td>-->
                                                            <td align="left" colspan="4">
                                                                <nobr><strong><b>Grand Total</b></strong></nobr>
                                                            </td>
                                                            <td align="right">
                                                                <span style="float:left;">BDT</span>
                                                                <nobr>{{$invoiceData[0]->grand_total}} </nobr>
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td colspan="5" style="border-top:#333 1px solid;">
                                                                <nobr></nobr>
                                                            </td>
                                                        </tr>
                                                    </table>

                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Powered By: <a href="https://www.erevo.net" style="text-decoration:none;"><strong>EREVO</strong></a></td>

                                            </tr>
                                        </table>


                                    </div>


                                </div>
                            </div>

                            <div class="panel-footer text-left">
                                <a class="btn btn-danger" href="">Cancel</a>
                                <button type="button" class="btn btn-info" onclick="printDiv('printableArea')"><span  class="fa fa-print"></span></button>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function () {

    });

    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        // document.body.style.marginTop="-45px";
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
@endpush
