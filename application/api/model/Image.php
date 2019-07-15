<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/6
 * Time: 14:22
 */

namespace app\api\model;


use think\Model;

class Image extends BaseModel
{
    protected $hidden=['id','from','delete_time','update_time'];
    public function getUrlAttr($value,$data){
        return $this->prefixImgUrl($value,$data);
    }

}