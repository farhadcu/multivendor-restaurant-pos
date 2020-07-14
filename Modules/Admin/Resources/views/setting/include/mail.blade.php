<div class="tile">
        <form id="mail-form" method="POST">
            @csrf
            <h3 class="tile-title text-brand">Social Links</h3>
            <hr>
            <div class="tile-body">
                <div class="form-group">
                    <label class="control-label" for="mail_driver">Mail Driver</label>
                    <select name="mail_driver" id="mail_driver" class="form-control selectpicker" data-live-search="true" data-live-search-placeholder="Search" title="Select Please">
                        <option value="">Select Please</option>
                        @foreach (MAIL_DRIVER as $value)
                        <option @if (config('mail.driver') == $value) {{'selected'}} @endif value="{{$value}}">{{strtoupper($value)}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label" for="mail_host">Mail Host</label>
                    <input
                        class="form-control"
                        type="text"
                        placeholder="Enter mail host"
                        id="mail_host"
                        name="mail_host"
                        value="{{ config('mail.host') }}"
                    />
                </div>
                <div class="form-group">
                    <label class="control-label" for="mail_port">Mail Port</label>
                    <select name="mail_port" id="mail_port" class="form-control selectpicker" data-live-search="true" data-live-search-placeholder="Search" title="Select Please">
                        <option value="">Select Please</option>
                        @foreach (MAIL_PORT as $value)
                        <option @if (config('mail.port') == $value) {{'selected'}} @endif value="{{$value}}">{{$value}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label" for="mail_username">Mail Username</label>
                    <input
                        class="form-control"
                        type="text"
                        placeholder="Enter mail username"
                        id="mail_username"
                        name="mail_username"
                        value="{{ config('mail.username') }}"
                    />
                </div>
                <div class="form-group">
                    <label class="control-label" for="mail_password">Mail Password</label>
                    <input
                        class="form-control"
                        type="text"
                        placeholder="Enter mail password"
                        id="mail_password"
                        name="mail_password"
                        value="{{ config('mail.password') }}"
                    />
                </div>
                <div class="form-group">
                    <label class="control-label" for="mail_encryption">Mail Encryption</label>
                    <select name="mail_encryption" id="mail_encryption" class="form-control selectpicker" data-live-search="true" data-live-search-placeholder="Search" title="Select Please">
                        <option value="">Select Please</option>
                        @foreach (MAIL_ENCRYPTION as $value)
                        <option @if (config('mail.encryption') == $value) {{'selected'}} @endif value="{{$value}}">{{strtoupper($value)}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="tile-footer">
                <div class="row d-print-none mt-2">
                    <div class="col-12 text-right">
                        <button class="btn btn-brand" type="button" id="update-mail" onclick="update_data(form='mail')"><i class="fa fa-fw fa-lg fa-check-circle"></i>Update Settings</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    