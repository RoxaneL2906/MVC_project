<?php

require_once(__DIR__."/../models/ProductModel.php");

class ProductController{
    public function view(string $method,array $params = []){
        try {
            call_user_func([$this,$method],$params);
        } catch (Error $e) {
            console($e);
        }
    }
    public function show(array $params = []){
        $id = $params[0];

        $productModel = new ProductModel();
        $product = $productModel->get($id);

        require_once(__DIR__."/../views/single-product.php");
    }
}