<?php
/**
 * Created by PhpStorm.
 * User: jock
 * Date: 2019/6/9
 * Time: 10:57
 */

namespace app\api\model;


class User extends BaseModel
{
    public function address()
    {
        return $this->hasOne('UserAddress','user_id','id');
    }
    public static function getByOpenID($openid){
        $user=self::where('openid','=',$openid)
            ->find();
        return $user;
    }
}