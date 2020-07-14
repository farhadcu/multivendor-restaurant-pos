<?php
namespace Modules\Company\Contracts\Product;

interface CategoryContract
{
    /**
     * @param array $params
     * @return mixed
     */
    public function getList(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function createCategory(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function editCategory(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function updateCategory(array $params);

    public function change_status(array $params);
    /**
     * @param array $params
     * @return mixed
     */
    public function deleteCategory(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function bulk_action_delete(array $params);

    public function category_list();

}