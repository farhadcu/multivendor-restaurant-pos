<?php 

namespace Modules\Company\Repositories\Accounts;


use App\Repositories\BaseRepository;
use Validator;
use Modules\Company\Entities\Accounts\Transaction;
use Modules\Company\Entities\Accounts\AccountHead;
use Illuminate\Support\Facades\Crypt;
use Modules\Company\Contracts\Accounts\TransactionContract;
use App\Traits\UploadAble;
use Illuminate\Http\UploadedFile;
use DB;
class TransactionRepository extends BaseRepository implements TransactionContract
{
    use UploadAble;
    private $rules = [
        'transaction_type'   => 'required|string',
        'amount'             => 'required|numeric',
        'image'              =>  'mimes:jpeg,jpg,png,pdf,doc,docx,csv,xlsx',
        'description'        => 'string',
    ];
    private $message = [];
    private $newTransactionBalance;
    protected $transaction_prefix = 100;
    private $id;

    public function __construct(Transaction $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function getList(array $params)
    {
        
        if(!empty($params['from_date'])){
            $this->model->setFromDate($params['from_date']);
        }
        if(!empty($params['to_Date'])){
            $this->model->setToDate($params['to_Date']);
        }
        if(!empty($params['transaction_type'])){
            $this->model->setTransactionType($params['transaction_type']);
        }
        if(!empty($params['account'])){
            $this->model->setAccount($params['account']);
        }

        $this->model->setOrderValue($params['order']);
        $this->model->setDirValue($params['direction']);
        $this->model->setLengthValue($params['length']);
        $this->model->setStartValue($params['start']);
        $list   = $this->model->getList();
        $data   = array();
        $no     = $params['start'];
        foreach ($list as $value) {
            $no++;
            $action = '';
            // if($this->helper->permission('transaction-edit')){
            //     $action .= '<li class="kt-nav__item"><a class="kt-nav__link edit_data" data-id="' . Crypt::encrypt($value->id) . '" >'.EDIT_ICON.'</a></li>';
            // }
            if($this->helper->permission('transaction-view')){
                $action .= '<li class="kt-nav__item"><a class="kt-nav__link view_data" data-id="' . Crypt::encrypt($value->id) . '" >'.VIEW_ICON.'</a></li>';
            }
            if($this->helper->permission('transaction-delete')){
                $action .= '<li class="kt-nav__item"><a class="kt-nav__link delete_data" data-id="'.Crypt::encrypt($value->id).'" >'.DELETE_ICON.'</a></li>';
            }
            $btngroup = '<span style="overflow: visible; position: relative;">   
                            <div class="dropdown"> 
                                <a data-toggle="dropdown" class="btn btn-sm btn-clean btn-icon btn-icon-lg cursor-pointer"> <i class="flaticon-more-1 text-brand"></i> </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <ul class="kt-nav">
                                        '.$action.'
                                    </ul>
                                </div>
                            </div>
                        </span>';

            $row    = array();
            if($this->helper->permission('transaction-bulk-action-delete')){
            $row[]  = '<label class="kt-checkbox kt-checkbox--single kt-checkbox--all kt-checkbox--solid"><input type="checkbox" name="did[]" value="' . $value->id . '" class="select_data">&nbsp;<span></span></label> ';
            }
            $row[]  = $no;
            $row[]  = $value->transaction_no;
            $row[]  = $value->account_title;
            $row[]  = $value->transaction_type;
            $row[]  =  (!empty($value->category_name)) ? $value->category_name : 'Account Transfer';
            $row[]  = date('d-M-Y',strtotime($value->created_at));
            if($value->transaction_type_id == 1 || $value->transaction_type_id == 4){
                $row[] = number_format($value->amount,2);
            }else{
                $row[] = number_format(0,2);
            }

            if($value->transaction_type_id == 2 || $value->transaction_type_id == 3 || $value->transaction_type_id == 5 ){
                $row[] = number_format($value->amount,2);
            }else{
                $row[] = number_format(0,2);
            }

            $row[] = number_format($value->balance,2);
            $row[]  = $btngroup;
            $data[] = $row;

        }
        return $this->dataTableDraw($params['draw'], $this->model->count_all(),
        $this->model->count_filtered(),$data);
        
    }


    public function createTransaction(array $params)
    {      
        if($params['transaction_type'] == 'Deposit' || $params['transaction_type'] == 'Expense' )
        {
            $this->rules['account_id']                    = 'required|numeric';
            $this->rules['category_id']                   = 'required|numeric';
            $this->rules['payment_method']                = 'required|string';
            $this->message['account_id.required']         = 'The account field is required';
            $this->message['account_id.numeric']          = 'The account field value must be numeric';
            $this->message['category_id.required']        = 'The category field is required';
            $this->message['category_id.numeric']         = 'The category field value must be numeric';

        }elseif ($params['transaction_type'] == 'TR') {

            $this->rules['from_account']                  = 'required|numeric';
            $this->rules['to_account']                    = 'required|numeric';
            $this->rules['payment_method']                = 'required|string';
            
        }elseif($params['transaction_type'] == 'AP' || $params['transaction_type'] == 'AR'){
            $this->rules['account_id']                    = 'required|numeric';
            $this->rules['category_id']                   = 'required|numeric';
            $this->message['account_id.required']         = 'The account field is required';
            $this->message['account_id.numeric']          = 'The account field value must be numeric';
            $this->message['category_id.required']        = 'The category field is required';
            $this->message['category_id.numeric']         = 'The category field value must be numeric';
        }

        $validator                                        = Validator::make($params, $this->rules,$this->message);
        if ($validator->fails()) {
            $output                                       = array(
                'errors' => $validator->errors()
            );
        } else {
            $transaction                                  = [];
            $transaction['company_id']                    = auth()->user()->company_id;
            $transaction['branch_id']                     = session()->get('branch');

            if($params['transaction_type'] == 'Deposit' || $params['transaction_type'] == 'Expense' ) {

                $transaction['account_id']                = $params['account_id'];
                $transaction['transaction_category_id']   = $params['category_id'];
                $transaction['payment_method']            = $params['payment_method'];

            }elseif($params['transaction_type'] == 'TR'){

                $from_account_id                          = $params['from_account'];
                $to_account_id                            = $params['to_account'];
                $transaction['payment_method']            = $params['payment_method'];


            }else{
                $transaction['account_id']                = $params['account_id'];
                $transaction['transaction_category_id']   = $params['category_id'];
            }
            $transaction_type                             = $this->transaction_type($params['transaction_type']);
            
            $transaction['transaction_type_id']           = $transaction_type[0];
            $transaction['transaction_type']              = $transaction_type[1];


            $transaction['amount']                        = $params['amount'];
            $transaction['reference']                     = $params['reference'];
            $transaction['description']                   = $params['description'];
            $history                                      = [];
            $history[] = [
                'title'  => 'Transaction data added',
                'text'   => 'Transaction data added by '.auth()->user()->name,
                'date'   => DATE_FORMAT,
                'mobile' => auth()->user()->mobile
            ];
            $transaction['history']                       = json_encode($history);

            if($transaction_type[0] == 3 || $transaction_type[0] == 4){//Accounts Payable(A/P) || Accounts Receivable(A/R)
                $account                                  = $this->account($params['account_id']);
                $transaction['balance']                   = $account->balance + $params['amount'];//transaction balance
                $account->balance                         = $account->balance + $params['amount'];//account balance
                $account->update();

            }elseif($transaction_type[0] == 5){//Transfer Balance
                $from_account                             = $this->account($from_account_id);
                $to_account                               = $this->account($to_account_id);

                $data_form['balance']                     = $from_account->balance - $params['amount'];//subtraction from account balance
                $data_to['balance']                       = $to_account->balance + $params['amount'];//addition to account balance

                $from_account->update(['balance' => $data_form['balance']]);
                $to_account->update(['balance'   => $data_to['balance']]);

            }else{//account
                $account                                  = $this->account($params['account_id']);
                if($transaction_type[0] == 1)//Deposit
                {
                    $transaction['balance']               = $account->balance + $params['amount'];
                    $account->balance                     = $account->balance + $params['amount'];
                }

                if($transaction_type[0] == 2)//Expense
                {
                    $transaction['balance']               = $account->balance - $params['amount'];
                    $account->balance                     = $account->balance - $params['amount'];
                }
                
                $account->update();
            }
            $collection                                   = collect($params)->only('document');
            if ($collection->has('document') && ($params['document'] instanceof  UploadedFile)) {
                $transaction['document']                  = $this->upload_file($params['document'], TRANSACTION_DOCUMENT);
            }
            
            if($transaction_type[0] != 5){//Not Transfer Balance
                $transaction_id                           = Transaction::create($transaction)->id;
                $prefix['transaction_no']                 = $this->transaction_prefix + $transaction_id;
                $this->update($prefix, $transaction_id);
                if($transaction_id){
                    $output                               = ['status' => 'success', 'message' => 'Data has been saved successfully.'];
                }else{
                    $output                               = ['status' => 'danger', 'message' => 'Data can not save.'];
                }
            }else{
                //from account transfer
                
                $trn_from_id                              = Transaction::create($transaction)->id;
                $data_form['transaction_no']              = $this->transaction_prefix + $trn_from_id;
                $data_form['transaction_type']            = 'Acount Transfer';
                $data_form['transaction_type_id']         = 5 ;
                $data_form['account_id']                  = $from_account_id ;
                $this->update($data_form,$trn_from_id);
                

                //to account Transfer
                $trn_to_id                                = Transaction::create($transaction)->id;
                $data_to['transaction_no']                = $this->transaction_prefix + $trn_to_id;
                $data_to['transaction_type']              = 'Deposit';
                $data_to['transaction_type_id']           = 1 ;
                $data_to['account_id']                    = $to_account_id;
                $this->update($data_to,$trn_to_id);
                $this->update(['transfer_reference'  => $data_to['transaction_no']],$trn_from_id);
                $this->update(['transfer_reference'  => $data_form['transaction_no']],$trn_to_id);
                if($trn_from_id && $trn_to_id){
                    $output                               = ['status' => 'success', 'message' => 'Data has been saved successfully.'];
                }else{
                    $output                               = ['status' => 'danger', 'message' => 'Data can not save.'];
                }
            }
            
        }
        return $output;
    }

    public function editTransaction(array $params)
    {
        if(!empty($params['id'])){
            $this->id                     = Crypt::decrypt($params['id']);
            $this->data                   = $this->find((int) $this->id);
            $collection                   = collect($this->data)->except(['id','company_id','branch_id','transaction_type','history'.'created_at','updated_at']);
            $id                           = $params['id'];
            $output['transfer_account']   = [];
            if($this->data->transaction_type == 'Deposit'){
                $transaction_type = 'Deposit';
            }elseif ($this->data->transaction_type == 'Expense') {
                $transaction_type = 'Expense';
            }elseif ($this->data->transaction_type == 'A/P') {
                $transaction_type = 'AP';
            }elseif ($this->data->transaction_type == 'A/R') {
                $transaction_type = 'AR';
            }else{
                $transaction_type = 'TR';
                $transfer_data = $this->model->where(['transaction_no' => $this->data->transfer_reference,
                'company_id'=>auth()->user()->company_id,'branch_id'=>session()->get('branch')])->first();
                if(!empty($transfer_data)){
                    $output['transfer_account'] = collect($transfer_data)->except(['id','company_id','branch_id','transaction_type','history'.'created_at','updated_at']);
                }
            }
            
            $merge                        = $collection->merge(compact('id','transaction_type'));
            if(!empty($merge))
            {
                $output['transaction']   = $merge->all();
            }else{
                $output                   = ['status' => 'danger','message' => 'No Data Found'];
            }
        }else{
            $output                       = ['status' => 'danger','message' => 'No Data Found'];
        }
        return $output;
    }
    public function showTransaction(array $params)
    {
        if(!empty($params['id'])){
            $this->id                     = Crypt::decrypt($params['id']);
            $this->data                   = DB::table('transactions as t')
            ->select('t.*','ah.account_title','tc.category_name')
            ->leftjoin('account_heads as ah','t.account_id','=','ah.id')
            ->leftjoin('transaction_categories as tc','t.transaction_category_id','=','tc.id')
            ->where('t.id',$this->id)
            ->first();

            $collection                   = collect($this->data)->except(['id','company_id','branch_id','transaction_type']);
            $id                           = $params['id'];
            $data['transfer_account']   = [];
            if($this->data->transaction_type == 'Deposit'){
                $transaction_type = 'Deposit';
                if(!empty($this->data->transfer_reference)){
                    $transfer_data = DB::table('transactions as t')
                                        ->select('t.*','ah.account_title')
                                        ->leftjoin('account_heads as ah','t.account_id','=','ah.id')
                                        ->where(['t.transaction_no'=>$this->data->transfer_reference,
                                        't.company_id'=>auth()->user()->company_id,'t.branch_id'=>session()->get('branch')])
                                        ->first();
                    if(!empty($transfer_data)){
                        $data['transfer_account'] = collect($transfer_data)->except(['id','company_id','branch_id','created_at','updated_at']);
                    }
                }
            }elseif ($this->data->transaction_type == 'Expense') {
                $transaction_type = 'Expense';
            }elseif ($this->data->transaction_type == 'A/P') {
                $transaction_type = 'AP';
            }elseif ($this->data->transaction_type == 'A/R') {
                $transaction_type = 'AR';
            }else{
                $transaction_type = 'TR';
                $transfer_data = DB::table('transactions as t')
                                    ->select('t.*','ah.account_title')
                                    ->leftjoin('account_heads as ah','t.account_id','=','ah.id')
                                    ->where(['t.transaction_no'=>$this->data->transfer_reference,
                                    't.company_id'=>auth()->user()->company_id,'t.branch_id'=>session()->get('branch')])
                                    ->first();
                if(!empty($transfer_data)){
                    $data['transfer_account'] = collect($transfer_data)->except(['id','company_id','branch_id','created_at','updated_at']);
                }
            }
            
            $merge                        = $collection->merge(compact('id','transaction_type'));
            $data['transaction'] = $merge->all();
            $output['transaction'] = view('company::accounts.transaction-details',compact('data'))->render();  
        }else{
            $output = '';
        }
        return $output;
    }

    public function updateTransaction(array $params)
    {
        if(!empty($params['update_id'])){
            $this->id         = Crypt::decrypt($params['update_id']);
            if($params['transaction_type'] == 'Deposit' || $params['transaction_type'] == 'Expense' )
            {
                $this->rules['account_id']                    = 'required|numeric';
                $this->rules['category_id']                   = 'required|numeric';
                $this->rules['payment_method']                = 'required|string';
                $this->message['account_id.required']         = 'The account field is required';
                $this->message['account_id.numeric']          = 'The account field value must be numeric';
                $this->message['category_id.required']        = 'The category field is required';
                $this->message['category_id.numeric']         = 'The category field value must be numeric';

            }elseif ($params['transaction_type'] == 'TR') {

                $this->rules['from_account']                  = 'required|numeric';
                $this->rules['to_account']                    = 'required|numeric';
                $this->rules['payment_method']                = 'required|string';
                
            }elseif($params['transaction_type'] == 'AP' || $params['transaction_type'] == 'AR'){
                $this->rules['account_id']                    = 'required|numeric';
                $this->rules['category_id']                   = 'required|numeric';
                $this->message['account_id.required']         = 'The account field is required';
                $this->message['account_id.numeric']          = 'The account field value must be numeric';
                $this->message['category_id.required']        = 'The category field is required';
                $this->message['category_id.numeric']         = 'The category field value must be numeric';
            }
            $validator        = Validator::make($params, $this->rules, $this->message);
            if ($validator->fails()) {
                $output       = array( 'errors' => $validator->errors());
            } else {
                $this->data                                   = $this->find((int) $this->id);
                $transaction                                  = [];
                $transaction['company_id']                    = auth()->user()->company_id;
                $transaction['branch_id']                     = session()->get('branch');

                if($params['transaction_type'] == 'Deposit' || $params['transaction_type'] == 'Expense' ) {

                    $transaction['account_id']                = $params['account_id'];
                    $transaction['transaction_category_id']   = $params['category_id'];
                    $transaction['payment_method']            = $params['payment_method'];

                }elseif($params['transaction_type'] == 'TR'){

                    $from_account_id                          = $params['from_account'];
                    $to_account_id                            = $params['to_account'];
                    $transaction['payment_method']            = $params['payment_method'];


                }else{
                    $transaction['account_id']                = $params['account_id'];
                    $transaction['transaction_category_id']   = $params['category_id'];
                }
                $transaction_type                             = $this->transaction_type($params['transaction_type']);
                
                $transaction['transaction_type_id']           = $transaction_type[0];
                $transaction['transaction_type']              = $transaction_type[1];


                $transaction['amount']                        = $params['amount'];
                $transaction['reference']                     = $params['reference'];
                $transaction['description']                   = $params['description'];
                $history                                      = [];
                $history[] = json_decode($this->data->history);
                $history[] = [
                    'title'  => 'Transaction data updated',
                    'text'   => 'Transaction data updated by '.auth()->user()->name,
                    'date'   => DATE_FORMAT,
                    'mobile' => auth()->user()->mobile
                ];
                $transaction['history']                       = json_encode($history);

                if( $transaction['transaction_type_id'] == 5){
                    $from_account = $this->account($from_account_id);

                    $to_account   = $this->account($to_account_id);
                    
                    $from_account->balance = ($from_account->balance + $this->data->amount) - $transaction["amount"];
                    $to_account->balance   = ($to_account->balance - $this->data->amount) + $transaction["amount"];
                    $transaction['balance'] = $from_account->balance;
                    // echo "Current Row = ".$this->data->balance." <br> Old Amount ".$this->data->amount."<br> balance=".$transaction['balance']."<br>From Account Balance = $from_account->balance <br> To Account balance = $to_account->balance";
                    // exit;
                    if(!empty($this->data->transfer_reference)){
                        $reference_transaction    = $this->model->where(['transaction_no' => $this->data->transfer_reference,
                        'company_id'=>auth()->user()->company_id,'branch_id'=>session()->get('branch')])->first();

                        if(!empty($reference_transaction)){
                            $reference_transaction->amount  = $transaction["amount"];
                            $reference_transaction->balance = $to_account->balance ;
                            dd($reference_transaction);
                            $reference_transaction->update();
                        }
                   
                    }
                    $from_account->update();
                    $to_account->update();
                }else{
                    $account = $this->account($transaction['account_id']);
                    
                    $amount =  $transaction["amount"] - $this->data->amount;
                    $transaction['balance'] = $this->data->balance + ($amount);
                    
                    $account->balance    = ($account->balance - $this->data->amount) + $transaction["amount"];
                    // echo "Amount = $amount<br> balance=".$transaction['balance']."<br>Account Balance = $account->balance ";
                    // exit;
                    $account->update();
                   
                    
                }
                $result = $this->update($transaction,$this->id);
                if ($result) {
                    $output   = ['status'  => 'success','message' => 'Data has been updated successfully'];
                }else{
                    $output   = ['status' => 'danger','message' => 'Data can not update'];
                }
            }
        }else{
            $output           = ['status' => 'danger','message' => 'Data can not update'];
        }
        return $output;
    }

    public function deleteTransaction(array $params)
    {
        if(!empty($params['id'])){
            $this->id   = Crypt::decrypt($params['id']);

            $result = $this->model->where('id','>',$this->id)->orderBy('id','asc')->get();

            $transaction = $this->find($this->id);

            /**
             * @deposit balance adjustment
             *
             * @deposit deduct from accounts head
             * @select delete transaction row amount and balance
             * @newTransactionBalance = balance - amount
             * @adjustTransactionBalance
             *
             * @deposit     =1
             * @expense     =2
             * @AP          =3
             * @AR          =4
             * @transfer    =5
             *
             */

            $account_balance = $this->account($transaction->account_id);

            if($transaction->transaction_type_id == 1){//deposit

                $account_balance->balance = $account_balance->balance - $transaction->amount;
                $account_balance->update();
    
                //Batch Update
                $this->newTransactionBalance = $transaction->balance - $transaction->amount;
                $this->_adjust_balance($result, $transaction);
    
                //Delete transactions
                $result = $this->delete($this->id);
    
                //if account transfer has
                if(!empty($transaction->transfer_reference)){
                    //Batch Update
                    $this->_transfer_adjestment($transaction->transfer_reference);
                }

                if ($result) {
                    $output = ['status'  => 'success','message' => 'Data has been deleted successfully.'];
                } else {
                    $output = ['status'  => 'error','message' => 'Unable to delete data.'];
                } 
    
    
            }elseif($transaction->transaction_type_id == 2 || $transaction->transaction_type_id == 5){//expense and transfer
    
                $account_balance->balance = $account_balance->balance + $transaction->amount;
    
                $account_balance->update();
    
                //Batch Update
                $this->newTransactionBalance = $transaction->balance + $transaction->amount;
                $this->_adjust_balance($result, $transaction);
    
                //Delete transactions
                $result = $this->delete($this->id);
    
                //if account transfer has
                if(!empty($transaction->transfer_reference)){
                    $this->_transfer_adjestment($transaction->transfer_reference);
                }
    
               
                if ($result) {
                    $output = ['status'  => 'success','message' => 'Data has been deleted successfully.'];
                } else {
                    $output = ['status'  => 'error','message' => 'Unable to delete data.'];
                } 
    
            }elseif($transaction->transaction_type_id == 3 || $transaction->transaction_type_id == 4){//accounts payable || account receivable
    
                $account_balance->balance = $account_balance->balance - $transaction->amount;
    
                $account_balance->update();
    
                //Batch Update
                $this->newTransactionBalance = $transaction->balance - $transaction->amount;
                $this->_adjust_balance_other($result, $transaction);
    
                //Delete transactions
                $result = $this->delete($this->id);
                if ($result) {
                    $output = ['status'  => 'success','message' => 'Data has been deleted successfully.'];
                } else {
                    $output = ['status'  => 'error','message' => 'Unable to delete data.'];
                } 
            }
              
        } else {
            $output     = ['status'  => 'error','message' => 'Unable to delete data.'];
        }
        return $output;
    }

    public function bulk_action_delete(array $params)
    {
        $this->id         = $params['id'];
        if(!empty($this->id) && count($this->id) > 0){
            $delete       = $this->destroy($this->id);
            if($delete){
                $output   = ['status'  => 'success','message' => 'Data has been deleted successfully.'];
            }else{
                $output   = ['status'  => 'error','message' => 'Unable to delete data.'];
            }
        }else{
            $output       = ['status'  => 'error','message' => 'Unable to delete data.'];
        }
        return $output;
    }

    private function account($account_id)
    {
        return AccountHead::find($account_id);
    }

    private function transaction_type($type)
    {
        /* @transaction_type
         *
         * Deposit
         * Expense
         * Accounts Payable
         * Accounts Payable
         *
         * @transaction_type_id
         *
         * 1 = Deposit
         * 2 = Expense
         * 3 = Accounts Payable(A/P)
         * 4 = Accounts Receivable(A/R)
         *
         * */

        switch ($type) {
            case "Deposit":
                $transaction[0] = '1';
                $transaction[1] = 'Deposit';
                return $transaction;
                break;
            case "Expense":
                $transaction[0] = '2';
                $transaction[1] = 'Expense';
                return $transaction;
                break;
            case "AP":
                $transaction[0] = '3';
                $transaction[1] = 'A/P';

                return $transaction;
                break;
            case "AR":
                $transaction[0] = '4';
                $transaction[1] = 'A/R';
                return $transaction;
                break;
            case "TR":
                $transaction[0] = '5';
                $transaction[1] = 'Account Transfer';
                return $transaction;
                break;

        }
    }

    private function _adjust_balance($result, $transaction)
    {
        foreach($result as $item){
            if($transaction->account_id == $item->account_id ) {

                if ($item->transaction_type_id == 1) {
                    $this->newTransactionBalance += $item->amount;

                } elseif ($item->transaction_type_id == 2 || $item->transaction_type_id == 5) {
                    $this->newTransactionBalance -= $item->amount;

                }
                $this->update(['balance' => $this->newTransactionBalance],$item->id);

            }
        }

    }

    private function _transfer_adjestment($transfer_reference)
    {
        $transfer = $this->model->where(['transaction_no' => $transfer_reference,
        'company_id'=>auth()->user()->company_id,'branch_id'=>session()->get('branch')])->first();
        
        $result = $this->model->where('id','>',$transfer->id)->orderBy('id','asc')->get();

        //account head
        $account_balance = $this->account($transfer->account_id);

        if($transfer->transaction_type_id == 5){
            $account_balance->balance = $account_balance->balance + $transfer->amount;
            $this->newTransactionBalance = $transfer->balance + $transfer->amount;
        }else{

            $account_balance->balance = $account_balance->balance - $transfer->amount;
            $this->newTransactionBalance = $transfer->balance - $transfer->amount;
        }
        $account_balance->update();

        foreach($result as $item){
            if($transfer->account_id == $item->account_id ) {

                if ($item->transaction_type_id == 1) {
                    $this->newTransactionBalance += $item->amount;
                } elseif ($item->transaction_type_id == 2 || $item->transaction_type_id == 5) {
                    $this->newTransactionBalance -= $item->amount;
                }
                 $this->update(['balance' => $this->newTransactionBalance],$item->id);
            }
        }

        $this->delete($transfer->id);

    }

    private function _adjust_balance_other($result, $transaction)
    {
        foreach($result as $item){
            if($transaction->account_id == $item->account_id ) {

                if ($item->transaction_type_id == 1 || $item->transaction_type_id == 3 || $item->transaction_type_id == 4 ) {
                    $this->newTransactionBalance += $item->amount;
                } elseif ($item->transaction_type_id == 2) {
                    $this->newTransactionBalance -= $item->amount;
                }
                $this->update(['balance' => $this->newTransactionBalance],$item->id);
            }
        }

    }

}
