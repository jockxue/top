<?php
/**
 * Created by PhpStorm.
 * User: jock
 * Date: 2019/7/6
 * Time: 11:28
 */

namespace app\api\behavior;

use think\Response;

class CORS
{
    public function appInit(&$params)
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: token,Origin, X-Requested-With, Content-Type, Accept");
        header('Access-Control-Allow-Methods: POST,GET');
        if(request()->isOptions()){
            exit();
        }
    }
}