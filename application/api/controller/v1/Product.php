<?php
/**
 * Created by PhpStorm.
 * User: jock
 * Date: 2019/6/8
 * Time: 14:13
 */

namespace app\api\controller\v1;

use app\api\model\Product as ProductModel;
use app\api\validate\Count;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\ProductException;

class Product
{
    public function getRecent($count=15){
        (new Count())->goCheck();
        $products=ProductModel::getMostRecent($count);
        if($products->isEmpty()){
            throw new ProductException();
        }
        //$collection=collection($products);
        $products=$products->hidden(['summary']);
        return $products;
    }
    public function getAllInCategory($id){
        (new IDMustBePositiveInt())->goCheck();
        $products=ProductModel::getProductsByCategoryID($id);
        if($products->isEmpty()){
            throw new ProductException();
        }
        $products=$products->hidden(['summary']);
        return $products;
    }
    public function getOne($id)//获取商品详情
    {
        (new IDMustBePositiveInt())->goCheck();
        $product=ProductModel::getProductDetail($id);
        if(!$product){
            throw new ProductException();
        }
        return $product;
    }
    public function deleteOne($id){

    }
}