<?php
/**
 * Created by PhpStorm.
 * User: jock
 * Date: 2019/6/10
 * Time: 20:51
 */

namespace app\api\model;


class ProductProperty extends BaseModel
{
    protected $hidden=['product_id','delete_time','id'];
}