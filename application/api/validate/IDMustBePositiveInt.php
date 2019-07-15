<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/3
 * Time: 11:54
 */

namespace app\api\validate;


use think\Validate;

class IDMustBePositiveInt extends BaseValidate
{
    protected $rule=[
        'id'=>'require|isPositiveInteger'
    ];
    protected $message=[
      'id'=>'id必须是正整数'
    ];

}