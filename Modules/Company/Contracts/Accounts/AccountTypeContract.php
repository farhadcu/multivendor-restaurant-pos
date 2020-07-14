<?php
namespace Modules\Company\Contracts\Accounts;

interface AccountTypeContract
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
    public function createAccountType(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function editAccountType(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function updateAccountType(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function deleteAccountType(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function bulk_action_delete(array $params);


}