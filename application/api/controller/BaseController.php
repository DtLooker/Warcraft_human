<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/29
 * Time: 20:16
 */

namespace app\api\controller;


use app\api\service\Token as TokenService;
use think\Controller;

class BaseController extends Controller
{
    //app用户和CMS管理员才具备的权限
    protected function checkPrimaryScope(){
       TokenService::needPrimaryScope();
    }

    // app用户才具备的权限
    protected function checkExclusiveScope(){
        TokenService::needExclusiveScope();
    }
}