<?php
namespace Modules\Company\Contracts;

interface OrderContract
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
    public function createOrder(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function invoice(array $params);
    /**
     * @param array $params
     * @return mixed
     */
    public function editOrder(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function updateOrder(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function deleteOrder(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function bulk_action_delete(array $params);
}