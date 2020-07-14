<?php
namespace Modules\Admin\Contracts;

interface ModuleContract
{
    /**
     * @param array $params
     * @return mixed
     */
    public function getList(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function createModule(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function editModule(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function updateModule(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function deleteModule(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function bulk_action_delete(array $params);

    public function parent_module_list();

}