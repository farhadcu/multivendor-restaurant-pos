<?php 
namespace Modules\Admin\Contracts;

interface UnitContract
{

    public function index();
    /**
     * @param array $params
     * @return mixed
     */
    public function getList(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function createUnit(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function editUnit(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function updateUnit(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function deleteUnit(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function bulk_action_delete(array $params);
}