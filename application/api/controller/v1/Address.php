<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/27
 * Time: 23:01
 */

namespace app\api\controller\v1;


use app\api\validate\AddressNew;
use app\api\service\Token as TokenService;
use app\api\model\User as UserModel;
use app\lib\exception\SuccessMsg;
use app\lib\exception\UserException;

class Address
{
    public function createOrUpdateAddress(){

       $validate = new AddressNew();
       $validate->goCheck();

        //1. 根据Token来获取uid
        $uid = TokenService::getCurrentUid();

        //2. 根据uid来查找用户数据，判断用户是否存在，如果不存在，抛出异常
        $user = UserModel::get($uid);
        if(!$user){
            throw new UserException();
        }

        //3. 获取用户从客户端提交过来的地址信息
        $dataArray = $validate->getDataByRule(input('post.'));

        //4. 根据用户地址信息是否存在，从而判断是添加还是更新地址
        $userAddress = $user->address;
        if(!$userAddress){
            //不存在，则新增地址
            $user->address()->save($dataArray);
        }else{
            //存在，则更新地址
            $user->address->save($dataArray);
        }
        return json(new SuccessMsg(), 201);
    }
}