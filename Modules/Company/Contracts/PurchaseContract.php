<?php
namespace Modules\Company\Contracts;

interface PurchaseContract
{
    public function getList(array $params);

    public function createPurchase(array $params);

    public function showPurchase($id);

    public function editPurchase($id);

    public function updatePurchase(array $params);

    public function deletePurchase(array $params);

    public function bulk_action_delete(array $params);

    public function payment_list(array $params);

    public function add_payment(array $params);

    public function edit_payment(array $params);

    public function update_payment(array $params);

    public function delete_payment(array $params);
}