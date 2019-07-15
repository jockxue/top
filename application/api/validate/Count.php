<?php
/**
 * Created by PhpStorm.
 * User: jock
 * Date: 2019/6/8
 * Time: 14:18
 */

namespace app\api\validate;


class Count extends BaseValidate
{
    protected $rule=[
        'count'=>'isPositiveInteger|between:1,15'
    ];

}