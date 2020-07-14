@php
Cart::instance('purchase')->setGlobalTax(0);
@endphp
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
@if (Cart::instance('purchase')->count() > 0)
<tbody>

    @php
        $i = 1;
    @endphp
    @foreach(Cart::instance('purchase')->content() as $item)
        <tr>
            <td>
                {{$i++}}
                <input type="hidden" id="product_id_{{$item->rowId}}" value="{{$item->options->product_id}}">
                <input type="hidden" id="variation_model_{{$item->rowId}}" value="{{$item->options->variation_model}}">
            </td>
            <td  class="text-left">{{$item->name}}</td>
            <td class="text-left">{{$item->options->variation_model}}</td>
            <td><input type="text" onkeyup="update_cart_product('{{$item->rowId}}')" class="form-control" id="qty_{{$item->rowId}}" value="{{$item->qty}}" required style="margin:0 auto;text-align: center;" /></td>
            <td class=" received_qty d-none"><input type="text" onkeyup="update_cart_product('{{$item->rowId}}')" class="form-control" id="received_qty_{{$item->rowId}}" value="{{$item->options->received_qty}}" style="margin:0 auto;text-align: center;" /></td>
            <td>
                <select class="form-control" onchange="update_cart_product('{{$item->rowId}}')"  id="purchase_unit_{{$item->rowId}}" style="margin:0 auto;" required>
                    <option value="">Select Please</option>
                    @if (!empty($units))
                        @foreach ($units as $unit)
                            <option @if ($unit->unit_short == $item->options->purchase_unit) {{'selected'}} @endif value="{{$unit->unit_short}}">{{$unit->unit_short.'('.$unit->unit_name.')'}}</option>
                        @endforeach
                    @endif
                </select>
            </td>
            <td class="text-right"><input type="text" onkeyup="update_cart_product('{{$item->rowId}}')" class="form-control" id="price_{{$item->rowId}}" value="{{number_format($item->price,2)}}" required style="margin:0 auto;text-align: right;" /></td>
            <td class="text-right">{{number_format(($item->qty * $item->price),2)}}</td>
            <td class="text-center">
                <button type="button" onclick="remove_cart_product('{{$item->rowId}}')" class="btn btn-danger btn-icon pull-right mt-1" style="width:20px;height:20px;" data-skin="white"  data-toggle="kt-tooltip" data-placement="top" title="" data-original-title="Remove Product">
                    <i class="fas fa-trash text-white" style="font-size:10px;"></i>
                </button>
            </td>
        </tr>
    @endforeach
    <tr>
        <td colspan="6" class="text-right col-section">Total</td>
        <td class="text-right" id="total_amount">{{Cart::instance('purchase')->subtotal()}}</td>
    </tr>

</tbody>

@endif