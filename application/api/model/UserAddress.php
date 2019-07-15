<?php
/**
 * Created by PhpStorm.
 * User: jock
 * Date: 2019/6/11
 * Time: 22:47
 */

namespace app\api\model;


class UserAddress extends BaseModel
{
    protected $hidden=['id','delete_time','user_id'];
}