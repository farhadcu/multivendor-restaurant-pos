<?php
namespace Modules\Admin\Contracts\Company;

interface MethodContract
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
    public function createMethod(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function editMethod(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function updateMethod(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function deleteMethod(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function bulk_action_delete(array $params);
}