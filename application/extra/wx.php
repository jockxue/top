<?php
/**
 * Created by PhpStorm.
 * User: jock
 * Date: 2019/6/9
 * Time: 14:52
 */
return[
    'app_id'=>'wx069e990611100075',
    'app_secret'=>'447b12b8044311b2ee80a3e784fdbe06',
    'login_url'=>"https://api.weixin.qq.com/sns/jscode2session?".
    "appid=%s&secret=%s&js_code=%s&grant_type=authorization_code",

    'access_token_url'=>"https://api.weixin.qq.com/cgi-bin/token?".
        "grant_type=client_credential&appid=%s&secret=%s",

];