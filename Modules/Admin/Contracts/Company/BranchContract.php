<?php
namespace Modules\Admin\Contracts\Company;

interface BranchContract{

    public function index(int $compnay_id);
    /**
     * @param array $params
     * @return mixed
     */
    public function getList(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function createBranch(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function editBranch(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function updateBranch(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function change_status(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function deleteBranch(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function bulk_action_delete(array $params);
}