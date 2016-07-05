<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * Configuration for environment
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo
 * @version     0.0.1
 * @package     conf
 */


/**
 * set mode: system environment
 * development OR test OR production
 */
$app->config('mode', 'development');


/**
 * config for development
 */
$app->configureMode('development', function () use ($app) {
    $app->Sneaker->cfg_mysql = [
        'database_type' => 'mysql',
        'database_name' => 'runner-jane',
        'server' => '115.28.8.173',
        'username' => 'root',
        'password' => 'runnerpassword',
        'charset' => 'utf8',
        'debug_mode' => true //set false when unit-test
    ];
    $app->Sneaker->cfg_mysql2 = [
        'database_type' => 'mysql',
        'database_name' => 'bmall-jane',
        'server' => '115.28.8.173',
        'username' => 'root',
        'password' => 'runnerpassword',
        'charset' => 'utf8',
        'debug_mode' => true //set false when unit-test
    ];

    $app->Sneaker->cfg_kv = [
        'host' => '115.28.8.173',
        'port' => 7717
    ];

    $app->config([
        'debug' => false,
    ]);

    $app->config('sms_verify', False);

    $app->config('my_url', 'http://115.28.8.173:809'); //erp后台地址
    $app->config('quartzUrl', 'http://quartz.ms9d.com/api/httpapi.action');  //计划任务接口
    $app->config('quartzRemoveUrl', 'http://115.28.93.117:8085/api/removeQuartzApi.do');  //计划任务取消接口
    $app->config('tofcUrl', 'http://115.28.8.173:8083/inc/orderStatus.do');  //商城订单状态变更接口
    $app->config('geoUrl', 'http://sms.ms9d.com/geo/geo.do');  //获取地域信息接口
    $app->config('smsUrl', 'http://sms.ms9d.com/message/sms.do');  //发送短信接口
    $app->config('orderCreateUrl', 'http://local.api.test.ms9d.com/mall/order_create'); //erp订单创建接口
    $app->config('orderNotifyUrl', 'http://local.api.test.ms9d.com/wx_pay/callback');   //支付网关回调接口
    $app->config('vipNotifyUrl', 'http://local.api.test.ms9d.com/wx_pay/callback_vip');   //支付网关回调接口 会员vip
    $app->config('erpPriceUrl', 'http://local.api.test.ms9d.com/mall/price_read_single'); //erp价格接口
    $app->config('orderCancelUrl', 'http://local.api.test.ms9d.com/mall/order_cancel/');  //erp取消订单接口
    $app->config('loginUrl', 'http://local.api.test.ms9d.com/login/in');  //用户登录接口
    $app->config('logoutUrl', 'http://local.api.test.ms9d.com/login/out');  //用户退出接口
    $app->config('bindUrl','http://local.api.test.ms9d.com/login/bind_third'); //绑定账号
    $app->config('bindHXUrl','http://local.api.test.ms9d.com/login/bind_third2'); //会销绑定账号
    $app->config('b2c_id', [
        'pbs' => 2346
    ]);
    $app->config('open_cache', false);

});

/**
 * config for test
 */
$app->configureMode('test', function () use ($app) {
    $app->Sneaker->cfg_mysql = [
        'database_type' => 'mysql',
        'database_name' => 'runner',
        'server' => '115.28.8.173',
        'username' => 'root',
        'password' => 'runnerpassword',
        'charset' => 'utf8',
        'debug_mode' => true //set false when unit-test
    ];
    $app->Sneaker->cfg_mysql2 = [
        'database_type' => 'mysql',
        'database_name' => 'bmall-admin',
        'server' => '115.28.8.173',
        'username' => 'root',
        'password' => 'runnerpassword',
        'charset' => 'utf8',
        'debug_mode' => true //set false when unit-test
    ];

    $app->Sneaker->cfg_kv = [
        'host' => 'localhost',
        'port' => 7717
    ];

    $app->config([
        'debug' => true,
    ]);

    $app->config('sms_verify', False);

    $app->config('my_url', 'http://115.28.8.173:808'); //erp后台地址
    $app->config('quartzUrl', 'http://quartz.ms9d.com/api/httpapi.action');  //计划任务接口
    $app->config('quartzRemoveUrl', 'http://115.28.93.117:8085/api/removeQuartzApi.do');  //计划任务取消接口
    $app->config('tofcUrl', 'http://115.28.8.173:8083/inc/orderStatus.do');  //商城订单状态变更接口
    $app->config('geoUrl', 'http://sms.ms9d.com/geo/geo.do');  //获取地域信息接口
    $app->config('smsUrl', 'http://sms.ms9d.com/message/sms.do');  //发送短信接口

    $app->config('orderCreateUrl', 'http://local.api.test.ms9d.com/mall/order_create'); //erp订单创建接口
    $app->config('orderNotifyUrl', 'http://local.api.test.ms9d.com/wx_pay/callback');   //支付网关回调接口
    $app->config('vipNotifyUrl', 'http://local.api.test.ms9d.com/wx_pay/callback_vip');   //支付网关回调接口 会员vip
    $app->config('erpPriceUrl', 'http://local.api.test.ms9d.com/mall/price_read_single'); //erp价格接口
    $app->config('orderCancelUrl', 'http://local.api.test.ms9d.com/mall/order_cancel/');  //erp取消订单接口
    $app->config('loginUrl', 'http://local.api.test.ms9d.com/login/in');  //用户登录接口
    $app->config('logoutUrl', 'http://local.api.test.ms9d.com/login/out');  //用户退出接口
    $app->config('bindUrl','http://local.api.test.ms9d.com/login/bind_third'); //绑定账号
    $app->config('bindHXUrl','http://local.api.test.ms9d.com/login/bind_third2'); //会销绑定账号
    $app->config('b2c_id', [
        'pbs' => 2346
    ]);
    $app->config('open_cache', false);
});

/**
 * config for production
 */
$app->configureMode('production', function () use ($app) {
    $app->Sneaker->cfg_mysql = [
        'database_type' => 'mysql',
        'database_name' => 'runner',
        #'server' => '10.47.120.20',
        'server' => 'rdsf36c9dnv2zx106j95.mysql.rds.aliyuncs.com',
        #'username' => 'root',
        'username' => 'runner',
        'password' => 'runnerpassword',
        'charset' => 'utf8',
        'debug_mode' => false //set false when unit-test
    ];
    $app->Sneaker->cfg_mysql2 = [
        'database_type' => 'mysql',
        'database_name' => 'dbadmin',
        'server' => 'rdsf36c9dnv2zx106j95.mysql.rds.aliyuncs.com',
        'username' => 'dbadmin',
        'password' => 'runnerpassword',
        'charset' => 'utf8',
        'debug_mode' => true //set false when unit-test
    ];

    $app->Sneaker->cfg_kv = [
//        'host' => '127.0.0.1',
//        'port' => 7717,
        'host' => '66bf7d58a47148cd.m.cnhza.kvstore.aliyuncs.com',
        'port' => 6379,
        'password' => '66bf7d58a47148cd:Runner2015'
    ];

    $app->config([
        'debug' => false,
    ]);

    $app->config('sms_verify', True);

    $app->config('my_url', 'http://yc.api.ms9d.com');  //erp后台地址
    //$app->config('quartzUrl', 'http://pa.ms9d.com/api/addQuartzApi.do');  //计划任务接口
    $app->config('quartzUrl', 'http://quartz.ms9d.com/api/httpapi.action');  //计划任务接口
    $app->config('quartzRemoveUrl', 'http://pa.ms9d.com/api/removeQuartzApi.do');  //计划任务取消接口
    $app->config('tofcUrl', 'http://cg.api.ms9d.com/inc/orderStatus.do');  //商城订单状态变更接口
    $app->config('geoUrl', 'http://sms.ms9d.com/geo/geo.do');  //获取地域信息接口
    $app->config('smsUrl', 'http://sms.ms9d.com/message/sms.do');  //发送短信接口

    $app->config('orderCreateUrl', 'http://yc.api.ms9d.com/mall/order_create'); //erp订单创建接口
    $app->config('orderNotifyUrl', 'http://yc.api.ms9d.com/wx_pay/callback');   //支付网关回调接口 商品
    $app->config('vipNotifyUrl', 'http://yc.api.ms9d.com/wx_pay/callback_vip');   //支付网关回调接口 会员vip
    $app->config('erpPriceUrl', 'http://yc.api.ms9d.com/mall/price_read_single'); //erp价格接口
    $app->config('orderCancelUrl', 'http://yc.api.ms9d.com/mall/order_cancel/');  //erp取消订单接口
    $app->config('loginUrl', 'http://yc.api.ms9d.com/login/in');  //用户登录接口
    $app->config('logoutUrl', 'http://yc.api.ms9d.com/login/out');  //用户退出接口
    $app->config('bindUrl','http://yc.api.ms9d.com/login/bind_third'); //绑定账号
    $app->config('bindHXUrl','http://yc.api.ms9d.com/login/bind_third2'); //会销绑定账号
    $app->config('b2c_id', [
        'pbs' => 3329
    ]);
    $app->config('open_cache', false);
});

/**
 * SQL Database
 * rely on Medoo(PDO:MySQL)
 */
try {
    $app->db = new \Slim\medoo($app->Sneaker->cfg_mysql);
    //unset($app->Sneaker->cfg_mysql);
} catch (Exception $e) {
    //print $e->getMessage();
    die('MySQL Connect Failed!');
}

try {
    $app->db2 = new \Slim\medoo($app->Sneaker->cfg_mysql2);
    //unset($app->Sneaker->cfg_mysql);
} catch (Exception $e) {
    //print $e->getMessage();
    die('MySQL Connect Failed!');
}

/**
 * noSQL Database
 * rely on Redis
 */
$app->kv = new Redis();
if ($app->kv->connect($app->Sneaker->cfg_kv['host'], $app->Sneaker->cfg_kv['port'])){
    //unset($app->Sneaker->cfg_kv);
    if(isset($app->Sneaker->cfg_kv['password'])){
        $app->kv->auth($app->Sneaker->cfg_kv['password']);
    }
} else {
    die('Redis Connect Failed!');
}

/**
 * common config
 */

$app->config([
    'log.writer' => new MySQLLogWriter(), //log driver
    'log.level' => \Slim\Log::INFO,
    'log.enabled' => true
]);

$app->config('weixin_config', [
    'ssmd_test' => [
        'APPID' => 'wx3e2ad08920ea75e0',
        'APPSECRET' => '72ac808bbd2da666fbe9ff334fbd2e2d',
    ],
    'ssmd' => [
        'APPID' => 'wx1c589097f3e7cdbe',
        'APPSECRET' => '65aef012c96d638bbf31aa2123aa6e3b',
    ],
    'langfei' => [
        'APPID' => 'wx3ec60ae27a01b14b',
        'APPSECRET' => 'f4b7a841925a56f13cb293add4a0f086',
    ],
    'ztjg' => [
        'APPID' => 'wxa7c952bef0f1c73c',
        'APPSECRET' => '3502850ea57fdd406e6b36112b9a6f2d',
    ],
]);

//define('APPID','wxa7c952bef0f1c73c');
//define('APPSECRET','3502850ea57fdd406e6b36112b9a6f2d');

$app->config('vip_config', [
    '1' => [],
    '2' => [
        'price' => ['30'=>30,'420'=>420]
    ],
    '3' => [
        'price' => ['365'=>600],
        'logistics' => 90,
    ],
    '4' => [
        'price' => ['365'=>1000],
        'logistics' => 150
    ]
]);
//$app->config('vip_product', [
//    [
//        'name'=>'计价会员（1个月）',
//        'price'=>30,
//        'product_id'=>'2_30',
//        'logistics' => 0,
//        'memo' => '计价会员描述信息'
//    ],
//    [
//        'name'=>'计价会员（14个月）',
//        'price'=>360,
//        'product_id'=>'2_420',
//        'logistics' => 0,
//        'memo' => '计价会员描述信息'
//    ],
//    [
//        'name'=>'包年会员',
//        'price'=>600,
//        'product_id'=>'3_365',
//        'logistics' => 90,
//        'memo' => '包年会员描述信息'
//    ],
//    [
//        'name'=>'合伙会员',
//        'price'=>1000,
//        'product_id'=>'4_365',
//        'logistics' => 150,
//        'memo' => '合伙会员描述信息'
//    ]
//]);
$app->config('vip_product', [
    [
        'name'=>'计价会员（1个月）',
        'price'=>0.01,
        'product_id'=>'2_30',
        'logistics' => 0,
        'memo' => '计价会员描述信息'
    ],
    [
        'name'=>'计价会员（14个月）',
        'price'=>0.02,
        'product_id'=>'2_420',
        'logistics' => 0,
        'memo' => '计价会员描述信息'
    ],
    [
        'name'=>'包年会员',
        'price'=>0.03,
        'product_id'=>'3_365',
        'logistics' => 90,
        'memo' => '包年会员描述信息'
    ],
    [
        'name'=>'合伙会员',
        'price'=>0.04,
        'product_id'=>'4_365',
        'logistics' => 150,
        'memo' => '合伙会员描述信息'
    ]
]);

$app->config('vip_month_limit', 600);

$app->config('pay_type_dict', [
    1 => '货到付款',
    2 => '立即支付',  //微信支付
    3 => '银行转账',
    4 => '对公汇款',
]);
$app->config('wc_cache_prefix','wc_');
$app->config('platform_dict',[
    2346 => 'pbs',
    ]);
