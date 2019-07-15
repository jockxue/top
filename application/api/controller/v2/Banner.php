<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/6
 * Time: 17:43
 */

namespace app\api\controller\v2;

use think\Exception;
class Banner
{
    public function getBanner($id)
    {
        return 'This is v2 Version';
    }

}