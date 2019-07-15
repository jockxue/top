<?php
/**
 * Created by PhpStorm.
 * User: jock
 * Date: 2019/6/10
 * Time: 9:05
 */

namespace app\api\service;

use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\TokenException;
use think\Cache;
use think\Exception;
use think\Request;

class Token
{
    public static function generateToken(){
        //32个字符组成一组随机字符串
        $randChars=getRandChar(32);
        //用三组字符串进行md5加密
        $timestamp=$_SERVER['REQUEST_TIME_FLOAT'];//自定义规则加密
        $salt=config('secure.token_salt');
        return md5($randChars.$timestamp.$salt);
    }
    public static function getCurrentTokenVar($key){
        //指明获取缓存中的哪一个变量
        $token=Request::instance()
            ->header('token');
        $vars=Cache::get($token);
        if(!$vars) {
            throw new TokenException();
        }

        else{
            if(!is_array($vars))
            {
                $vars = json_decode($vars,true);
            }
            if(array_key_exists($key,$vars)){
                return $vars[$key];
            }
            else{
                throw new Exception('尝试获取Token变量并不存在');
            }
        }
    }
    public static function getCurrentUid(){
        //token
        $uid = self::getCurrentTokenVar('uid');
        return $uid;
    }
    public static function needPrimaryScope()
    //需要用户和cms管理员都可以访问的权限
    {
        $scope=self::getCurrentTokenVar('scope');
        if($scope)
        {
            if ($scope >= ScopeEnum::User) {
                return true;
            } else {
                throw new ForbiddenException();
            }
        }else{
            throw new TokenException();
        }
    }
    public static function needExclusiveScope()
    //只有用户才能访问的接口权限
    {
        $scope=self::getCurrentTokenVar('scope');
        if($scope)
        {
            if ($scope == ScopeEnum::User) {
                return true;
            } else {
                throw new ForbiddenException();
            }
        }else{
            throw new TokenException();
        }
    }
    public static function isValidOperate($checkedUID)//UID比对
    {
        if(!$checkedUID)
        {
            throw new Exception('检查UID时必须传入一个被检查的UID');
        }
        $currentOperateUID=self::getCurrentUid();
        if ($currentOperateUID==$checkedUID){
            return true;
        }
        return false;
    }

    public static function verifyToken($token)
    {
        $exist=Cache::get($token);
        if ($exist){
            return true;
        }
        else{
            return false;
        }
    }
}