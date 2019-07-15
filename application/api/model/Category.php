<?php
/**
 * Created by PhpStorm.
 * User: jock
 * Date: 2019/6/8
 * Time: 15:42
 */

namespace app\api\model;


class Category extends BaseModel
{
    protected $hidden=['delete_time','update_time','create_time'];
    public function img(){
        return $this->belongsTo('Image','topic_img_id','id');
    }

}