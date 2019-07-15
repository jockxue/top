<?php
/**
 * Created by PhpStorm.
 * User: jock
 * Date: 2019/6/9
 * Time: 10:45
 */

namespace app\api\validate;


class TokenGet extends BaseValidate
{
    protected $rule=[
        'code'=>'require|isNotEmpty'
    ];
    protected $message=[
        'code'=>'访问的code不存在'
    ];
}