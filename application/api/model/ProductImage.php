<?php
/**
 * Created by PhpStorm.
 * User: jock
 * Date: 2019/6/10
 * Time: 20:46
 */

namespace app\api\model;


class ProductImage extends BaseModel
{
    protected $hidden=['img_id','delete_time','product_id'];

    public function imgUrl()
    {
        return $this->belongsTo('Image','img_id','id');
    }
}