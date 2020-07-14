<?php

namespace Modules\Company\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Modules\Admin\Entities\Company;
use Validator;
use App\Traits\UploadAble;
use Illuminate\Http\UploadedFile;
class SettingController extends BaseController
{
    use UploadAble;
    public function index()
    {
        if($this->helper->permission('setting-manage')){
            $this->setPageData('Setting','Setting','fas fa-cogs');
            return view('company::setting.setting');
        }
    }

    public function update(Request $request)
    {
        if($request->ajax())
        {
            $rules = [
                'logo'         => 'image|mimes:jpeg,jpg,png',
                'favicon'      => 'image|mimes:jpeg,jpg,png',
                'invoice_logo' => 'image|mimes:jpeg,jpg,png',
            ];
            $validator               = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $output = array(
                    'errors' => $validator->errors()
                );
            } else {
                $company = Company::find(auth()->user()->company_id);
                $company->phone = $request->phone;
                $company->vat_no = $request->vat_no;
                $company->vat = $request->vat;
                $company->invoice_prefix = $request->invoice_prefix;
                //Logo
                if ($request->file('logo') instanceof UploadedFile) {
                    if ($company->logo != null) {
                        $this->delete_file($company->logo,COMPANY_PHOTO);
                    }
                    $company->logo = $this->upload_file($request->file('logo'), COMPANY_PHOTO);
                } 
                //Favicon
                if ($request->file('favicon') instanceof UploadedFile) {
                    if ($company->favicon != null) {
                        $this->delete_file($company->favicon,COMPANY_PHOTO);
                    }
                    $company->favicon = $this->upload_file($request->file('favicon'), COMPANY_PHOTO);
                } 
                //Invoice Logo
                if ($request->file('invoice_logo') instanceof UploadedFile) {
                    if ($company->invoice_logo != null) {
                        $this->delete_file($company->invoice_logo,COMPANY_PHOTO);
                    }
                    $company->invoice_logo = $this->upload_file($request->file('invoice_logo'), COMPANY_PHOTO);
                } 

                if($company->update())
                {
                    $output   = ['status' => 'success','message' => 'Data has been updated successfully.'];
                }else{
                    $output   = ['status' => 'danger','message' => 'Data can not update.'];
                }

                return response()->json($output);
                
            }

        }
    }

    
}
