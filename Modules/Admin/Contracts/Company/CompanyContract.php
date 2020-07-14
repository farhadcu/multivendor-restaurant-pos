<?php
namespace Modules\Admin\Contracts\Company;

interface CompanyContract
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
    public function createCompany(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function editCompany(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function updateCompany(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function deleteCompany(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function bulk_action_delete(array $params);
}