<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/3
 * Time: 11:53
 */

namespace app\api\validate;


use think\Validate;

class TestValidate extends Validate
{
    protected $rule=[
        'name'=>'require|max:10',
        'email'=>'email'
    ];
}