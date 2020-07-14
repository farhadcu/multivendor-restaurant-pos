<?php
namespace Modules\Company\Contracts;

interface SupplierContract
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
    public function createSupplier(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function showSupplier(array $params);
    /**
     * @param array $params
     * @return mixed
     */
    public function editSupplier(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function updateSupplier(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function deleteSupplier(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function bulk_action_delete(array $params);
}