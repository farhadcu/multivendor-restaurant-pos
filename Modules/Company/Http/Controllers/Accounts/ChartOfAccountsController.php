<?php

namespace Modules\Company\Http\Controllers\Accounts;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Modules\Company\Contracts\Accounts\ChartOfAccountContract AS Account;
use Modules\Company\Contracts\Accounts\AccountTypeContract AS AccountType;

class ChartOfAccountsController extends BaseController
{
    private $account;
    private $accountType;
    public function __construct(Account $account, AccountType $accountType)
    {
        parent::__construct();
        $this->account        = $account;
        $this->accountType    = $accountType;

    }
    public function index()
    {
        if($this->helper->permission('chart-of-account-list')){
            $this->setPageData('Chart Of Account','Chart Of Account','');
            $data['account_types'] = $this->accountType->index();
            return view('company::accounts.chart-of-account',compact('data'));
        }
    }

    public function getList(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('chart-of-account-list')){
                $params              = $request->except('_token');
                $params['order']     = $request->input('order.0.column');
                $params['direction'] = $request->input('order.0.dir');
                $params['length']    = $request->input('length');
                $params['start']     = $request->input('start');
                $output = $this->account->getList($params);
                echo json_encode($output);
            }
        }
    }
   
    public function store(Request $request){
        if($request->ajax()){
            if($this->helper->permission('chart-of-account-add')){
                $params = $request->except(['_token','update_id']);
                $this->output = $this->account->createAccount($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function show(Request $request){
        if($request->ajax()){
            if($this->helper->permission('chart-of-account-view')){
                $params = $request->except('_token');
                $this->output = $this->account->editAccount($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
        
    }
    public function edit(Request $request){
        if($request->ajax()){
            if($this->helper->permission('chart-of-account-edit')){
                $params = $request->except('_token');
                $this->output = $this->account->editAccount($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
        
    }

    public function update(Request $request){
        if($request->ajax()){
            if($this->helper->permission('chart-of-account-edit')){
                $params = $request->except('_token');
                $this->output = $this->account->updateAccount($params);
            }else{
                $this->output= $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function destroy(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('chart-of-account-delete')){
                $params = $request->except('_token');
                $this->output = $this->account->deleteAccount($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function bulk_action_delete(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('chart-of-account-bulk-action-delete')){
                $params = $request->except('_token');
                $this->output = $this->account->bulk_action_delete($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }
}
