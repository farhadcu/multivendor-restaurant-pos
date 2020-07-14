<div class="col-md-8">
    <div class="table-responsive">
        <table class="table table-borderless">
            <tr>
                <td width="30%"><b>Transaction No</b></td>
                <td width="70%" class="text-left"><b>:</b> {{$data['transaction']['transaction_no']}}</td>
            </tr>
            <tr>
                <td width="30%"><b>Account</b></td>
                <td width="70%" class="text-left"><b>:</b> {{$data['transaction']['account_title']}}</td>
            </tr>
            @if ($data['transaction']['transaction_type'] == 'Deposit' && !empty($data['transaction']['transfer_reference']))
            <tr>
                <td width="30%"><b>From Account</b></td>
                <td width="70%" class="text-left"><b>:</b> {{$data['transfer_account']['account_title']}}</td>
            </tr>
            @endif
            @if ($data['transaction']['transaction_type'] == 'TR' && !empty($data['transaction']['transfer_reference']))
            <tr>
                <td width="30%"><b>To Account</b></td>
                <td width="70%" class="text-left"><b>:</b> {{$data['transfer_account']['account_title']}}</td>
            </tr>
            @endif
            <tr>
                <td width="30%"><b>Type</b></td>
                <td width="70%" class="text-left"><b>:</b> {{$data['transaction']['transaction_type']}}</td>
            </tr>
            @if (!empty($data['transaction']['category_name']))
            <tr>
                <td width="30%"><b>Category</b></td>
                <td width="70%" class="text-left"><b>:</b> {{$data['transaction']['category_name']}}</td>
            </tr>
            @endif
            <tr>
                <td width="30%"><b>Amount</b></td>
                <td width="70%" class="text-left"><b>:</b> {{number_format($data['transaction']['amount'],2)}}Tk</td>
            </tr>
            <tr>
                <td width="30%"><b>Payment Method</b></td>
                <td width="70%" class="text-left"><b>:</b> {{$data['transaction']['payment_method']}}</td>
            </tr>
            <tr>
                <td width="30%"><b>Reference</b></td>
                <td width="70%" class="text-left"><b>:</b> {{$data['transaction']['reference']}}</td>
            </tr>
            <tr>
                <td width="30%"><b>Description</b></td>
                <td width="70%" class="text-left"><b>:</b> {{$data['transaction']['description']}}</td>
            </tr>
        </table>
    </div>
</div>
@if (!empty($data['transaction']['document']))
<div class="col-md-4 text-center">
    @php
        $extension = pathinfo($data['transaction']['document'], PATHINFO_EXTENSION);
    @endphp
    @if ($extension == 'pdf')
        <img src="{{asset('public/img/pdf.png')}}" alt="Transaction Dpcument" class="w-100">
    @elseif($extension == 'xls' || $extension == 'xlsx' || $extension == 'csv')
        <img src="{{asset('public/img/excel.png')}}" alt="Transaction Dpcument" class="w-100">
    @elseif($extension == 'docx' || $extension == 'doc')
        <img src="{{asset('public/img/doc.png')}}" alt="Transaction Dpcument" class="w-100">
    @elseif($extension == 'jpeg' || $extension == 'jpg' || $extension == 'png')
        <img src="{{asset(FOLDER_PATH.TRANSACTION_DOCUMENT.$data['transaction']['document'])}}" alt="Transaction Dpcument" class="w-100">
    @else 
        <img src="{{asset('public/img/no-image.png')}}" alt="Transaction Dpcument" class="w-100">
    @endif
    <a href="{{asset(FOLDER_PATH.TRANSACTION_DOCUMENT.$data['transaction']['document'])}}" class="btn btn-brand mt-3" download><i class="fas fa-download"></i> Download</a>
</div>
@endif