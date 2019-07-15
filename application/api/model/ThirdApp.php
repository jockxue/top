<?php
/**
 * Created by PhpStorm.
 * User: jock
 * Date: 2019/7/4
 * Time: 16:20
 */

namespace app\api\model;


class ThirdApp extends BaseModel
{
    public static function check($ac, $se)
    {
        $app = self::where('app_id','=',$ac)
            ->where('app_secret', '=',$se)
            ->find();
        return $app;

    }
}
