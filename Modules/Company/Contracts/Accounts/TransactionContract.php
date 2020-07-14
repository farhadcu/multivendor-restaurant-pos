<?php
namespace Modules\Company\Contracts\Accounts;

interface TransactionContract{

    public function getList(array $params);

    public function createTransaction(array $params);

    public function showTransaction(array $params);

    public function editTransaction(array $params);

    public function updateTransaction(array $params);

    public function deleteTransaction(array $params);

    public function bulk_action_delete(array $params);

}