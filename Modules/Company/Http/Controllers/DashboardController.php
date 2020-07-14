<?php

namespace Modules\Company\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Modules\Admin\Contracts\Company\BranchContract AS Branch;
use Modules\Admin\Entities\Branch AS BranchModel;
class DashboardController extends BaseController
{

    private $branch;
    private $redirectTo;
    public function __construct(Branch $branch)
    {
        $this->branch     = $branch;
        parent::__construct();
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if($this->helper->permission('dashboard-manage')){
            return view('company::dashboard');
        }
        
    }

    public function select_branch()
    {
        $this->setPageData('Branch Selection','Branch Selection','fas fa-user-alt');
        $data['branches'] = $this->branch->index(auth()->user()->company_id);
        return view('company::select-branch',compact('data'));
    }

    public function branch_store_session(Request $request)
    {
        $request->validate([
            'branch' => 'required|numeric',
        ]);
        session(['branch'=>$request->branch]);
        return redirect('/dashboard');
    }

    public function invoice()
    {
        $this->setPageData('Sale Invoice','Sale Invoice','fas fa-user-alt');
        $branch = BranchModel::find(session()->get('branch'));
        return view('company::order.invoice',compact('branch'));
    }
    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('company::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('company::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('company::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
