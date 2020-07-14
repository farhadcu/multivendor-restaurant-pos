<?php
namespace Modules\Admin\Contracts;

interface SubscriptionContract
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
    public function createSubscription(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function editSubscription(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function updateSubscription(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function deleteSubscription(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function bulk_action_delete(array $params);
}