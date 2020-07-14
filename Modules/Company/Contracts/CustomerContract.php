<?php
namespace Modules\Company\Contracts;

interface CustomerContract
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
    public function createCustomer(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function showCustomer(array $params);
    /**
     * @param array $params
     * @return mixed
     */
    public function editCustomer(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function updateCustomer(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function deleteCustomer(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function bulk_action_delete(array $params);
}