<?php
namespace Modules\Admin\Contracts\Company;

interface UserContract
{
    public function index(int $company_id);
    /**
     * @param array $params
     * @return mixed
     */
    public function getList(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function createUser(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function showUser($id);

    /**
     * @param array $params
     * @return mixed
     */
    public function updateUser(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function deleteUser(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function bulk_action_delete(array $params);

    public function change_status(array $params);

    public function change_password(array $params);

    public function get_permission();
}