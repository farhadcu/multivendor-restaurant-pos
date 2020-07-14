<?php
namespace Modules\Company\Contracts\Accounts;

interface ChartOfAccountContract
{
    public function index();

    public function getList(array $params);

    public function createAccount(array $params);

    public function showAccount(array $params);

    public function editAccount(array $params);

    public function updateAccount(array $params);

    public function deleteAccount(array $params);

    public function bulk_action_delete(array $params);


}