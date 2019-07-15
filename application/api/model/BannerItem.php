<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/6
 * Time: 10:38
 */

namespace app\api\model;


use think\Model;

class BannerItem extends BaseModel
{
    protected $hidden=['id','update_time','delete_time','img_id','banner_id'];
    public function img(){
        return $this->belongsTo('Image','img_id','id');
    }

}