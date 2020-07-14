<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\UploadAble;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\BaseController;
use Modules\Admin\Entities\Setting;

class SettingController extends BaseController
{
    use UploadAble;

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if($this->helper::permission('setting-general')){
            $this->setPageData('Settings', 'Settings','fas fa-tools');
            return view('admin::setting.setting');
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {

        if($this->helper::permission('setting-general')){
            if ($request->file('site_logo') instanceof UploadedFile) {

                if (config('settings.site_logo') != null) {
                    $this->delete_file(config('settings.site_logo'),LOGO);
                }
                $logo = $this->upload_file($request->file('site_logo'), LOGO, 'logo');
                Setting::set('site_logo', $logo);

            } 
            if ($request->file('site_favicon') instanceof UploadedFile) {

                if (config('settings.site_favicon') != null) {
                    $this->delete_file(config('settings.site_favicon'),LOGO);
                }
                $favicon = $this->upload_file($request->file('site_favicon'), LOGO);
                Setting::set('site_favicon', $favicon);

            } 
            if(empty($request->file('site_logo')) && empty($request->file('site_favicon'))){

                $keys = $request->except('_token');

                foreach ($keys as $key => $value)
                {
                    Setting::set($key, $value);
                }
            }
            return $this->responseRedirectBack('Settings updated successfully.', 'success');
        }
    }

    public function set_mail_info(Request $request)
    {
        if($request->ajax())
        {
            if($this->helper::permission('setting-smtp')){
                $this->data = $this->changeEnvData([
                    'MAIL_DRIVER'    => $request->mail_driver,
                    'MAIL_HOST'      => $request->mail_host,
                    'MAIL_PORT'      => $request->mail_port,
                    'MAIL_USERNAME'  => $request->mail_username,
                    'MAIL_PASSWORD'  => $request->mail_password,
                    'MAIL_ENCRYPTION'=> $request->mail_encryption
                ]);

                if($this->data){
                    $this->output['status']  = 'success';
                    $this->output['message'] = 'Data has been deleted successfully.';
                }else{
                    $this->output['status']  = 'error';
                    $this->output['message'] = 'Unable to delete data.';
                }
            }else{
                $this->output = $this->access_blocked();
            }
            return response(json_encode($this->output));
        }
    }
    public function set_sms_info(Request $request)
    {
        if($request->ajax())
        {
            if($this->helper::permission('setting-sms')){
                $this->data = $this->changeEnvData([
                    'SMS_USERNAME' => $request->sms_username,
                    'SMS_PASSWORD' => $request->sms_password,
                    'SMS_URL'      => $request->sms_url,
                    
                ]);

                if($this->data){
                    $this->output['status']  = 'success';
                    $this->output['message'] = 'Data has been deleted successfully.';
                }else{
                    $this->output['status']  = 'error';
                    $this->output['message'] = 'Unable to delete data.';
                }
            }else{
                $this->output = $this->access_blocked();
            }
            return response(json_encode($this->output));
        }
    }

    /** BEGIN:: .ENV FILE EDIT METHOD (NEVER TOUCH THIS METHOD) **/
    protected function changeEnvData(array $data){
        if(count($data) > 0){

            // Read .env-file
            $env = file_get_contents(base_path() . '/.env');

            // Split string on every " " and write into array
            $env = preg_split('/\s+/', $env);;

            // Loop through given data
            foreach((array)$data as $key => $value){

                // Loop through .env-data
                foreach($env as $env_key => $env_value){

                    // Turn the value into an array and stop after the first split
                    // So it's not possible to split e.g. the App-Key by accident
                    $entry = explode("=", $env_value, 2);

                    // Check, if new key fits the actual .env-key
                    if($entry[0] == $key){
                        // If yes, overwrite it with the new one
                        $env[$env_key] = $key . "=" . $value;
                    } else {
                        // If not, keep the old one
                        $env[$env_key] = $env_value;
                    }
                }
            }

            // Turn the array back to an String
            $env = implode("\n", $env);

            // And overwrite the .env with the new data
            file_put_contents(base_path() . '/.env', $env);
            
            return true;
        } else {
            return false;
        }
    }
    /** END:: .ENV FILE EDIT METHOD **/
}
