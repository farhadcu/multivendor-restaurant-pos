<div class="tile">
    <h3 class="tile-title text-brand">Site Logo</h3>
    <hr>
    <div class="tile-body">
        <div class="row">
            <div class="col-3">
                @if (Auth::user()->company->logo != null)
                <img src="{{ asset(FOLDER_PATH.COMPANY_PHOTO.Auth::user()->company->logo) }}" id="logoImg"
                    style="width: 80px; height: auto;">
                @else
                <img src="./public/img/no-image.png" id="logoImg"
                    style="width: 80px; height: auto;">
                @endif
            </div>
            <div class="col-9">
                <div class="form-group">
                    <label class="control-label">Site Logo</label>
                    <input class="form-control" type="file" name="logo" onchange="loadFile(event,'logoImg')" />
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-3">
                @if (Auth::user()->company->favicon != null)
                <img src="{{ asset(FOLDER_PATH.COMPANY_PHOTO.Auth::user()->company->favicon)}}" id="faviconImg"
                    style="width: 80px; height: auto;">
                @else
                <img src="./public/img/no-image.png" id="faviconImg"
                    style="width: 80px; height: auto;">
                @endif
            </div>
            <div class="col-9">
                <div class="form-group">
                    <label class="control-label">Site Favicon</label>
                    <input class="form-control" type="file" name="favicon"
                        onchange="loadFile(event,'faviconImg')" />
                </div>
            </div>
        </div>
    </div>
</div>