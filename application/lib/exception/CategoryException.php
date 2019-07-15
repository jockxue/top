<?php
/**
 * Created by PhpStorm.
 * User: jock
 * Date: 2019/6/8
 * Time: 15:59
 */

namespace app\lib\exception;


class CategoryException extends BaseException
{
    public $code=404;
    public $msg='指定主题不存在，请检查参数';
    public $errorCode=50000;
}