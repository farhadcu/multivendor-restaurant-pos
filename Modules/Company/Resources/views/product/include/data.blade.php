<div class="row">
    <div class="form-group col-lg-3">
        <label for="sku" class="form-control-label"  data-toggle="kt-tooltip" data-skin="dark"
        data-placement="top" title="" data-original-title="Stock Keeping Unit">SKU <i class="fas fa-info-circle text-info"></i></label>
        <input type="text" class="form-control" name="sku" id="sku"  placeholder="Enter SKU">
    </div>
    <div class="form-group col-lg-3">
        <label for="upc" class="form-control-label"  data-toggle="kt-tooltip" data-skin="dark"
        data-placement="top" title="" data-original-title="Universal Product Code">UPC <i class="fas fa-info-circle text-info"></i></label>
        <input type="text" class="form-control" name="upc" id="upc"  placeholder="Enter UPC">
    </div>
    <div class="form-group col-lg-3">
        <label for="mpn" class="form-control-label"  data-toggle="kt-tooltip" data-skin="dark"
        data-placement="top" title="" data-original-title="Manufacturer Part Number">MPN <i class="fas fa-info-circle text-info"></i></label>
        <input type="text" class="form-control" name="mpn" id="mpn"  placeholder="Enter MPN">
    </div>
    <div class="form-group col-lg-3">
        <label for="purchase_price" class="form-control-label">Purcahse Price</label>
        <input type="text" class="form-control" name="purchase_price" id="purchase_price"  placeholder="Enter purchase price">
    </div>
    <div class="form-group col-lg-3 required">
        <label for="selling_price" class="form-control-label">Selling Price</label>
        <input type="text" class="form-control" name="selling_price" id="selling_price"  placeholder="Enter selling price">
    </div>
    <div class="form-group col-lg-3">
        <label for="qty" class="form-control-label">Quantity</label>
        <input type="text" class="form-control" name="qty" id="qty"  placeholder="Enter quantity">
    </div>
    <div class="form-group col-lg-3">
        <label for="min_qty" class="form-control-label">Minimum Quantity</label>
        <input type="text" class="form-control" name="min_qty" id="min_qty" placeholder="Enter minimum quantity">
    </div>
    <div class="form-group col-lg-3">
        <label for="max_qty" class="form-control-label">Maximum Quantity</label>
        <input type="text" class="form-control" name="max_qty" id="max_qty" placeholder="Enter maximum quantity">
    </div>
    <div class="form-group col-lg-3">
        <label for="stock_unit" class="form-control-label">Stock Unit</label>
        <select class="form-control selectpicker" name="stock_unit" id="stock_unit" data-live-search="true" data-live-search-placeholder="Search" title="Select Please">
            <option value="">Select Please</option>
            @if (!empty($data['units']))
                @foreach ($data['units'] as $value)
                <option value="{{$value->unit_short}}">{{$value->unit_short.' ('.$value->unit_name.')'}}</option>
                @endforeach
            @endif
        </select>
    </div>
    <div class="form-group col-lg-3">
        <label for="subtract_stock" class="form-control-label">Subtract Stock</label>
        <select class="form-control selectpicker" name="subtract_stock" id="subtract_stock" data-live-search="true" data-live-search-placeholder="Search" title="Select Please">
            <option value="">Select Please</option>
            <option value="1">Yes</option>
            <option value="2">No</option>
        </select>
    </div>
    <div class="form-group col-lg-3">
        <label for="rack_no" class="form-control-label">Rack No.</label>
        <input type="text" class="form-control" name="rack_no" id="rack_no" placeholder="Enter stock rack no">
    </div>
    <div class="form-group col-lg-3">
        <label for="length" class="form-control-label">Length</label>
        <input type="text" class="form-control" name="length" id="length" placeholder="Enter stock rack no">
    </div>
    <div class="form-group col-lg-3">
        <label for="width" class="form-control-label">Width</label>
        <input type="text" class="form-control" name="width" id="width"  placeholder="Enter stock rack no">
    </div>
    <div class="form-group col-lg-3">
        <label for="height" class="form-control-label">Height</label>
        <input type="text" class="form-control" name="height" id="height" placeholder="Enter stock rack no">
    </div>
    <div class="form-group col-lg-3">
        <label for="weight" class="form-control-label">Weight</label>
        <input type="text" class="form-control" name="weight" id="weight"  placeholder="Enter stock rack no">
    </div>
    <div class="form-group col-lg-3">
        <label for="returnable" class="form-control-label">Product Returnable</label>
        <select class="form-control selectpicker" name="returnable" id="returnable" data-live-search="true" data-live-search-placeholder="Search" title="Select Please">
            <option value="">Select Please</option>
            <option value="1">Yes</option>
            <option value="2">No</option>
        </select>
    </div>
</div>