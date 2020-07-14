<div class="tile">
        <form id="sms-form" method="POST" role="form">
            @csrf
            <h3 class="tile-title text-brand">Social Links</h3>
            <hr>
            <div class="tile-body">
                <div class="form-group">
                    <label class="control-label" for="sms_username">Username</label>
                    <input
                        class="form-control"
                        type="text"
                        placeholder="Enter username"
                        id="sms_username"
                        name="sms_username"
                        value="{{ env('SMS_USERNAME') }}"
                    />
                </div>
                <div class="form-group">
                    <label class="control-label" for="sms_password">Password</label>
                    <input
                        class="form-control"
                        type="text"
                        placeholder="Enter password"
                        id="sms_password"
                        name="sms_password"
                        value="{{ env('SMS_PASSWORD') }}"
                    />
                </div>
                <div class="form-group">
                    <label class="control-label" for="sms_url">Url</label>
                    <input
                        class="form-control"
                        type="text"
                        placeholder="Enter instagram profile link"
                        id="sms_url"
                        name="sms_url"
                        value="{{ env('SMS_URL') }}"
                    />
                </div>
            </div>
            <div class="tile-footer">
                <div class="row d-print-none mt-2">
                    <div class="col-12 text-right">
                        <button class="btn btn-brand" type="button" id="update-sms" onclick="update_data(form='sms')"><i class="fa fa-fw fa-lg fa-check-circle"></i>Update Settings</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    