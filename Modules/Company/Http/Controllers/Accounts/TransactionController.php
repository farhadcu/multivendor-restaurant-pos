<?php

namespace Modules\Company\Http\Controllers\Accounts;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Modules\Company\Contracts\Accounts\TransactionContract AS Transaction;
use Modules\Company\Contracts\Accounts\ChartOfAccountContract AS Account;
use Modules\Company\Contracts\Accounts\TransactionCategoryContract AS Category;
class TransactionController extends BaseController
{
    private $transaction;
    private $account;
    private $category;
    public function __construct(Transaction $transaction,Account $account, Category $category)
    {
        parent::__construct();
        $this->transaction    = $transaction;
        $this->account        = $account;
        $this->category       = $category;

    }
    public function index()
    {
        if($this->helper->permission('transaction-list')){
            $this->setPageData('Account Transaction','Account Transaction','');
            $data['accounts'] = $this->account->index();
            return view('company::accounts.transaction',compact('data'));
        }
    }

    public function getList(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('transaction-list')){
                $params              = $request->except('_token');
                $params['order']     = $request->input('order.0.column');
                $params['direction'] = $request->input('order.0.dir');
                $params['length']    = $request->input('length');
                $params['start']     = $request->input('start');
                $output = $this->transaction->getList($params);
                echo json_encode($output);
            }
        }
    }
   
    public function store(Request $request){
        if($request->ajax()){
            if($this->helper->permission('transaction-add')){
                $params = $request->except(['_token','update_id']);
                $this->output = $this->transaction->createTransaction($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function show(Request $request){
        if($request->ajax()){
            if($this->helper->permission('transaction-view')){
                $params = $request->except('_token');
                $this->output = $this->transaction->showTransaction($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
        
    }
    public function edit(Request $request){
        if($request->ajax()){
            if($this->helper->permission('transaction-edit')){
                $params = $request->except('_token');
                $this->output = $this->transaction->editTransaction($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
        
    }

    public function update(Request $request){
        if($request->ajax()){
            if($this->helper->permission('transaction-edit')){
                $params = $request->except('_token');
                $this->output = $this->transaction->updateTransaction($params);
            }else{
                $this->output= $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function destroy(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('transaction-delete')){
                $params = $request->except('_token');
                $this->output = $this->transaction->deleteTransaction($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function bulk_action_delete(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('transaction-bulk-action-delete')){
                $params = $request->except('_token');
                $this->output = $this->transaction->bulk_action_delete($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function category_list(Request $request)
    {
        if($request->ajax()){
            
            $params = $request->except('_token');
            if($params['transaction_type'] == 'Deposit' || $params['transaction_type'] == 'AR'){
                $category_type = 1;
            }else{
                $category_type = 2;
            }
            $data = $this->category->index($category_type);
            $output = '';
            if(!empty($data)){
                $output .= '<option value="">Select Please</option>';
                foreach ($data as $value) {
                    $output .= '<option value="'.$value->id.'">'.$value->category_name.'</option>';
                }
            }
            return response()->json($output);
        }
    }


}
