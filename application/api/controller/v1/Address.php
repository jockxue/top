<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/11
 * Time: 14:43
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\User as UserModel;
use app\api\model\UserAddress;
use app\api\service\Token as TokenService;
use app\api\validate\AddressNew;
use app\lib\exception\SuccessMessage;
use app\lib\exception\UserException;

class Address extends BaseController
{
    protected $beforeActionList=[
        'checkPrimaryScope'=>['only'=>'createOrUpdateAddress,getUserAddress']
    ];

    public function getUserAddress()
    {
        $uid = TokenService::getCurrentUid();
        $userAddress = UserAddress::where('user_id', $uid)
            ->find();
        if (!$userAddress) {
            throw new UserException([
            'msg' => '用户地址不存在',
            'errorCode' => 60001
            ]);
        }
        return $userAddress;
    }

    public function createOrUpdateAddress()
    {
        $validate=new AddressNew();
        $validate->goCheck();
     //   (new AddressNew())->goCheck();
        //根据token来获取uid
        //根据uid来查找用户数据，判断用户是否存在，如果不存在抛出异常。
        //获取用户从客户端提交的地址信息
        //根据用户地址信息是否存在，从而判断是添加地址还是更新地址
        $uid= TokenService::getCurrentUid();
        $user =UserModel::get($uid);
        if(!$user){
            throw new UserException();
        }
        $dataArray=$validate->getDataByRule(input('post.'));

        $userAddress=$user->address;
        if(!$userAddress)
        {
            $user->address()->save($dataArray);
        }
        else{
            $user->address->save($dataArray);
        }
        return new SuccessMessage();
    }
}