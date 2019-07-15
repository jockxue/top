<?php
/**
 * Created by PhpStorm.
 * User: jock
 * Date: 2019/6/7
 * Time: 9:11
 */

namespace app\api\validate;


class IDCollection extends BaseValidate
{
    protected $rule=[
        'ids' => 'require|checkIDs'
    ];
    protected $message=[
        'ids'=>'ids参数必须是以逗号分隔的多个正整数'
    ];
    protected function checkIDs($value)
    {
        $values = explode(',', $value);
        if (empty($values)) {
            return false;
        }
        foreach ($values as $id) {
            if (!$this->isPositiveInteger($id)) {
                // 必须是正整数
                return false;
            }
        }
        return true;
    }
}