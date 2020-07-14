<?php

namespace Modules\Company\Http\Controllers\Accounts;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Modules\Company\Contracts\Accounts\AccountTypeContract AS AccountType;

class AccountTypeController extends BaseController
{
    private $account_type;
    public function __construct(AccountType $account_type)
    {
        parent::__construct();
        $this->account_type = $account_type;

    }
    public function index()
    {
        if($this->helper->permission('account-type-list')){
            $this->setPageData('Account Type','Account Type','');
            return view('company::accounts.account-type');
        }
    }

    public function getList(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('account-type-list')){
                $params              = $request->except('_token');
                $params['order']     = $request->input('order.0.column');
                $params['direction'] = $request->input('order.0.dir');
                $params['length']    = $request->input('length');
                $params['start']     = $request->input('start');
                $output = $this->account_type->getList($params);
                echo json_encode($output);
            }
        }
    }
   
    public function store(Request $request){
        if($request->ajax()){
            if($this->helper->permission('account-type-add')){
                $params = $request->except(['_token','update_id']);
                $this->output = $this->account_type->createAccountType($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function edit(Request $request){
        if($request->ajax()){
            if($this->helper->permission('account-type-edit')){
                $params = $request->except('_token');
                $this->output = $this->account_type->editAccountType($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
        
    }

    public function update(Request $request){
        if($request->ajax()){
            if($this->helper->permission('account-type-edit')){
                $params = $request->except('_token');
                $this->output = $this->account_type->updateAccountType($params);
            }else{
                $this->output= $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function destroy(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('account-type-delete')){
                $params = $request->except('_token');
                $this->output = $this->account_type->deleteAccountType($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function bulk_action_delete(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('account-type-bulk-action-delete')){
                $params = $request->except('_token');
                $this->output = $this->account_type->bulk_action_delete($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }
}
