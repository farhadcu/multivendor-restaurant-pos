<div class="tile">
    <h3 class="tile-title text-brand">General Settings</h3>
    <hr>
    <div class="tile-body">
        <div class="row">
            <div class="form-group col-md-6">
                <label class="control-label" for="company_name">Company Name</label>
                <input class="form-control" type="text" id="company_name" value="{{Auth::user()->company->company_name}}" readonly />
            </div>
            <div class="form-group col-md-6">
                <label class="control-label" for="owner_name">Owner Name</label>
                <input class="form-control" type="text" id="owner_name" value="{{Auth::user()->company->owner_name}}" readonly />
            </div>
            <div class="form-group col-md-6">
                <label class="control-label" for="email">Email</label>
                <input class="form-control" type="email" id="email" value="{{Auth::user()->company->email}}" readonly />
            </div>
            <div class="form-group col-md-6">
                <label class="control-label" for="mobile">Mobile No.</label>
                <input class="form-control" type="text" id="mobile" value="{{Auth::user()->company->mobile}}" readonly />
            </div>
            <div class="form-group col-md-6">
                <label class="control-label" for="phone">Phone No.</label>
                <input class="form-control" type="text" placeholder="Enter store currency symbol" id="phone"  name="phone"
                 value="{{Auth::user()->company->phone}}" />
            </div>
            <div class="form-group col-md-6">
                <label class="control-label" for="vat_no">VAT No.</label>
                <input class="form-control" type="text" placeholder="Enter company vat no" id="vat_no" name="vat_no"
                 value="{{Auth::user()->company->vat_no}}" />
            </div>
            <div class="form-group col-md-6">
                <label class="control-label" for="vat">VAT(%)</label>
                <input class="form-control" type="text" placeholder="Enter store vat" id="vat" name="vat" value="{{Auth::user()->company->vat}}" />
            </div>
        </div>
       
    </div>
</div>