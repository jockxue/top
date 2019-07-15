<?php
/**
 * Created by PhpStorm.
 * User: jock
 * Date: 2019/6/8
 * Time: 10:29
 */

namespace app\lib\exception;


class ThemeExcept extends BaseException
{
    public $code=404;
    public $msg='指定主题不存在，请检查主题ID';
    public $errorCode=30000;
}