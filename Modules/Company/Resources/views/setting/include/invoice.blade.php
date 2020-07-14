<div class="tile">
    <h3 class="tile-title text-brand">Site Invoice Data</h3>
    <hr>
    <div class="tile-body">
        
        <div class="row">
            <div class="col-3">
                @if (Auth::user()->company->invoice_logo != null)
                <img src="{{ asset(FOLDER_PATH.COMPANY_PHOTO.Auth::user()->company->invoice_logo) }}" id="invoiceImg"
                    style="width: 80px; height: auto;">
                @else
                <img src="./public/img/no-image.png" id="invoiceImg"
                    style="width: 80px; height: auto;">
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-9">
                <div class="form-group">
                    <label class="control-label">Favicon</label>
                    <input class="form-control" type="file" name="invoice_logo"
                        onchange="loadFile(event,'invoiceImg')" />
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-9">
                <div class="form-group">
                    <label class="control-label">Invoice Prefix</label>
                    <input class="form-control" type="text" name="invoice_prefix" id="invoice_prefix" value="{{Auth::user()->company->invoice_prefix}}"  />
                </div>
            </div>
        </div>
    </div>
</div>