<?php
namespace Modules\Company\Contracts;

interface RoleContract
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
    public function createRole(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function editRole(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function updateRole(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function deleteRole(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function bulk_action_delete(array $params);
}