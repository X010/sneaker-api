<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * Entry for API router
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo
 * @version     0.0.1
 * @package     Sneaker
 */

/**
 * framework initialization
 */
//header('Access-Control-Allow-Origin:*'); //for emberjs
header('Access-Control-Allow-Origin:*');
header('Content-type: text/html; charset=utf8');
date_default_timezone_set('Asia/Shanghai'); //set timezone
require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();            //初始化Slim对象
$app->Sneaker = new stdClass();     //初始化Sneaker对象

/**
 * public include
 */
require 'conf/include.php';
require 'conf/system.php';          //系统自定义配置
require 'conf/env.php';             //基础环境配置
require 'conf/route.php';           //载入路由配置
require 'conf/menu.php';
require 'conf/button.php';
require 'conf/errcode.php';

/**
 * router middleware
 */
require 'mw/init.php';              //进入API前的初始化
require 'mw/auth.php';              //用户身份验证
require 'mw/power.php';
/**
 * set route
 */


//model类自动加载
function my_autoload($class) {
    $file = 'model/'.cc_format($class).'.php';
    if(file_exists($file)){
        require($file);
        return;
    }
}

spl_autoload_register('my_autoload');
register_shutdown_function("my_errcatch");

$route = $app->config('route');
//加载机制优化：新：根据URI判断需要加载的文件进行加载
$my_uri = $app->request->getResourceUri();
$uri_list = explode('/',$my_uri);
$uri_length = count($uri_list);
if(is_numeric($uri_list[$uri_length-1])){
    array_pop($uri_list);
}
$uri_length = count($uri_list);

if($uri_length == 3){
    $my_api = $uri_list[1];
    $api = $route[$my_api];
    $api_filename = "api/$my_api.php";
    $uri = isset($api['uri']) ? $api['uri'] : "/$my_api/:action(/:id)";
}
elseif($uri_length == 4){
    $my_root = $uri_list[1];
    $my_api = $uri_list[2];
    $api = $route[$my_root][$my_api];
    $api_filename = "api/$my_root/$my_api.php";
    $uri = isset($api['uri']) ? $api['uri'] : "/$my_root/$my_api/:action(/:id)";
}
else{
    die('Access Denied');
}

if(!file_exists($api_filename)){
    die('Access Denied');
}
require $api_filename;

$hook = isset($api['hook']) ? $api['hook'] : ['init','auth','power'];
$method = isset($api['method']) ? $api['method'] : 'post'; //默认POST方式

$app->$method($uri, $my_api)->setMiddleware($hook);

//$my_param = array_merge([$api['uri']], $hook, [$my_api]);

//$app->$method(...$my_param);

//$auth = isset($api['auth']) ? $api['auth'] : true; //默认需要登录认证
//$power = isset($api['power']) ? $api['power'] : true; //默认需要验证权限
//$auth_name = $power ? 'auth' : 'auth_nopower';
//if($auth){
//    $app->$method($api['uri'], 'init', $auth_name, $my_api);
//}
//else{
//    $app->$method($api['uri'], 'init', $my_api);
//}

//老加载机制：全部加载
//foreach ($app->config('route') as $file => $api){
//    require 'api/' . $file . '.php';
//    $method = isset($api['method']) ? $api['method'] : 'post'; //默认POST方式
//    $auth = isset($api['auth']) ? $api['auth'] : true; //默认需要登录认证
//    $power = isset($api['power']) ? $api['power'] : true; //默认需要验证权限
//    $auth_name = $power ? 'auth' : 'auth_nopower';
//    if($auth){
//        $app->$method($api['uri'], 'init', $auth_name, $file);
//    }
//    else{
//        $app->$method($api['uri'], 'init', $file);
//    }
//}

/**
 * launch
 */

$app->run();


