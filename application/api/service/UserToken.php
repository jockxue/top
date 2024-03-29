<?php
/**
 * Created by PhpStorm.
 * User: jock
 * Date: 2019/6/9
 * Time: 10:59
 */

namespace app\api\service;


use app\lib\enum\ScopeEnum;
use app\lib\exception\TokenException;
use app\lib\exception\WeChatException;
use think\Exception;
use app\api\model\User as UserModel;

class UserToken extends Token
{
    protected $code;
    protected $wxAppID;
    protected $wxAppSecret;
    protected $wxLoginUrl;//成员属性变量

    function __construct($code)
    {
        $this->code=$code;
        $this->wxAppID=config('wx.app_id');
        $this->wxAppSecret=config('wx.app_secret');
        $this->wxLoginUrl=sprintf(config('wx.login_url'),
            $this->wxAppID,$this->wxAppSecret,$this->code);
    }

    public function get(){
        $result=curl_get($this->wxLoginUrl);
        $wxResult= json_decode($result,true);
        if (empty($wxResult)){
            throw new Exception('获取session_key及openID时异常，微信内部错误');
        }
        else{
            $loginFail=array_key_exists('errcode',$wxResult);
            if ($loginFail){
                $this->processLoginError($wxResult);
            }
            else{
                return $this->grantToken($wxResult);
            }
        }
    }
    private function grantToken($wxResult){
        //拿到openid
        //数据库里面看一下，这个openid是否存在
        //如果存在则不处理，如果不存在则新增一条user记录
        //生产令牌，准备缓存数据，写入缓存
        //把令牌返回到客户端
        //key:令牌
        //value:wxResult,uid,scope
        $openid=$wxResult['openid'];
        $user=UserModel::getByOpenID($openid);
        if($user){
            $uid=$user->id;
        }
        else{
            $uid=$this->newUser($openid);
        }
        $cachedValue=$this->prepareCachedValue($wxResult,$uid);
        $token =$this->saveToCache($cachedValue);
        return $token;
    }
    private function saveToCache($cachedValue){
        $key=self::generateToken();
        $value=json_encode($cachedValue);//数组转换为字符串
        $expire_in=config('setting.token_expire_in');
        $request =cache($key,$value,$expire_in);
        if(!$request){
            throw new TokenException([
                'msg'=>'服务器缓存异常',
                'errorCode'=>10005
            ]);
        }
       return $key;
    }
    private function prepareCachedValue($wxResult,$uid){
       $cachedValue=$wxResult;
       $cachedValue['uid']=$uid;
       //scope=16代表App用户的权限数值
       $cachedValue['scope']=ScopeEnum::User;
        //$cachedValue['scope']=32;
        //scope=32代表cms管理员用户的权限数值
       return $cachedValue;
    }
    private function newUser($openid){
        $user=UserModel::create([
            'openid'=>$openid
        ]);
        return $user->id;
    }
    private function processLoginError($wxResult){
        throw new WeChatException([
            'msg'=>$wxResult['errmsg'],
            'errorCode'=>$wxResult['errcode']
        ]);
    }
}