<?php

namespace Modules\Company\Contracts\Accounts;

interface TransactionCategoryContract
{

    public function index($category);

    public function getList(array $params);

    public function createCategory(array $params);

    public function editCategory(array $params);

    public function updateCategory(array $params);

    public function deleteCategory(array $params);

    public function bulk_action_delete(array $params);
}
