<div class="row">
    <div class="form-group col-lg-3">
        <label for="discount_qty" class="form-control-label">Discount Quantity</label>
        <input type="text" class="form-control" name="discount_qty" id="discount_qty" value="@if(!empty($data['pdiscount']['discount_qty'])) {{$data['pdiscount']['discount_qty']}} @endif" placeholder="Enter discount quantity">
    </div>
    <div class="form-group col-lg-3">
        <label for="discount_amount" class="form-control-label">Discount Amount(%)</label>
        <input type="text" class="form-control" name="discount_amount" id="discount_amount" value="@if(!empty($data['pdiscount']['discount_amount'])) {{$data['pdiscount']['discount_amount']}} @endif" placeholder="Enter discount amnount">
    </div>
    <div class="form-group col-lg-3">
        <label for="start_date" class="form-control-label">Start Date</label>
        <input type="text" class="form-control date" name="start_date" id="start_date" value="@if(!empty($data['pdiscount']['start_date'])) {{$data['pdiscount']['start_date']}} @endif" placeholder="Enter start date">
    </div>
    <div class="form-group col-lg-3">
        <label for="end_date" class="form-control-label">End Date</label>
        <input type="text" class="form-control date" name="end_date" id="end_date" value="@if(!empty($data['pdiscount']['end_date'])) {{$data['pdiscount']['end_date']}} @endif" placeholder="Enter end date">
    </div>

</div>