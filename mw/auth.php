<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * middleware of route
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo
 * @version     0.0.1
 * @package     mw
 */

/**
 * Authentication user identity
 */
function auth(){
    $app = \Slim\Slim::getInstance();
    //验证用户ticket
    $data = $app->request->params();
    if(!isset($data['ticket'])){
    	error(8003);
    }
    $model = new Login();
    $user_info = $model->login_status($data['ticket']);

    //将用户信息写入当前请求中
    $app->Sneaker->uid = $user_info['id'];
    $app->Sneaker->uname = $user_info['name'];
    $app->Sneaker->cid = $user_info['cid'];
    $app->Sneaker->cname = get_value($user_info, 'cname');
    $app->Sneaker->sids = get_value($user_info, 'sids', []); //array
    $app->Sneaker->user_info = $user_info;
    $app->platform = $user_info['platform'];
}


