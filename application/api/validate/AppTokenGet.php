<?php
/**
 * Created by PhpStorm.
 * User: jock
 * Date: 2019/7/4
 * Time: 15:41
 */

namespace app\api\validate;


class AppTokenGet extends BaseValidate
{
    protected $rule = [
        'ac' => 'require|isNotEmpty',
        'se' => 'require|isNotEmpty'
    ];
}