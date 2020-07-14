<?php
namespace Modules\Company\Contracts\Product;

interface ProductContract
{

    /**
     * @param array $params
     * @return mixed
     */
    public function index(array $params);
    /**
     * @param array $params
     * @return mixed
     */
    public function getList(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function createProduct(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function showProduct(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function editProduct(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function updateProduct(array $params);

    public function change_status(array $params);
    /**
     * @param array $params
     * @return mixed
     */
    public function deleteProduct(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function bulk_action_delete(array $params);

    public function autocomplete_search_product($params);

    public function variation_product($params);

}