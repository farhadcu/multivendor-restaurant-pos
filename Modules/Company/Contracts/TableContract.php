<?php
namespace Modules\Company\Contracts;

interface TableContract
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
    public function createTable(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function editTable(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function updateTable(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function deleteTable(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function bulk_action_delete(array $params);
}