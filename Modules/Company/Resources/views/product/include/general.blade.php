<div class="row">
    <div class="col-lg-10">
        <div class="row">
            <div class="form-group col-lg-6 required">
                <label for="name" class="form-control-label">Name</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Enter contact email">
            </div>
            <div class="form-group col-lg-6 required">
                <label for="model" class="form-control-label">Model</label>
                <input type="text" class="form-control" name="model" id="model" placeholder="Enter contact phone">
            </div>
            <div class="form-group col-lg-6">
                <label for="supplier_id" class="form-control-label">Supplier</label>
                <select  class="form-control selectpicker" name="supplier_id" id="supplier_id" data-live-search="true" data-live-search-placeholder="Search" title="Select Please">
                    <option value="">Select Please</option>
                    @if (!empty($data['supplier']))
                        @foreach ($data['supplier'] as $value)
                        <option value="{{$value->id}}">{{$value->supplier_name.'-'.$value->supplier_mobile.' ('.$value->supplier_company_name.')'}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="form-group col-lg-6">
                <label for="category_id" class="form-control-label">Category</label>
                <select  class="form-control selectpicker" name="category_id[]" id="category_id" multiple data-selected-text-format="count > 3" data-live-search="true" data-live-search-placeholder="Search" title="Select Please"></select>
            </div>
        </div>
    </div>
    <div class="col-lg-2">
        <div class="picture-container" id="choose-picture-box" style="border:1px dashed #555;margin-top:0 !important;">
            <div class="picture">
                
                <img src="./public/img/icon-choose.png" class="picture-src"  id="wizardPicturePreview" alt="Employee Photo">
                
                
                <input type="file" id="image" name="image" onchange="readURL(this)">
            </div>
            <h6 class="description mt-4" style="color:#32a5dd !important;">Choose Picture</h6>
        </div>
    </div>
</div>