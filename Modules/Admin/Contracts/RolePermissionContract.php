<?php
namespace Modules\Admin\Contracts;

interface RolePermissionContract{

    /**
    * @param nothing
    * @return role_list
    */
    public function index();

    /**
    * @param array $params
    * @return mixed
    */
    public function store(array $params);

    /**
    * @param int $role_id
    * @return mixed
    */
    public function get_role_permission(int $role_id);
}
?>