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
 * Sneaker init
 */
function init(){
    $app = \Slim\Slim::getInstance();
    $app->platform = NULL;
    $app->Sneaker->log_oper_off = 1;    //初始化关闭操作日志，等具体init时打开
    $app->Sneaker->imark = make_imark();//生成一个消息标识
    $app->Sneaker->uid = NULL;          //初始化uid
    $app->Sneaker->uname = NULL;        //初始化uname 
    $app->Sneaker->action_type = NULL;  //初始化操作日志字段
    $app->Sneaker->action_msg = NULL;   //初始化操作日志字段
    $app->Sneaker->cid=NULL;            //初始化所属公司字段
    $app->Sneaker->cname=NULL;          //初始化所属公司名字
    $app->Sneaker->action = 0;          //事务状态 0-未开始 1-已开始

    //初始化请求参数，去空格
    $params = $app->request->params();
    $params = array_trim($params);
    //限制每页最大数量
    $page_num = get_value($params, 'page_num', 0);
    if($page_num > 1000){
        $params['page_num'] = 1000;
    }

    foreach($params as $key=>$val){
        $temp = json_decode($val, True);
        if(!$temp){
            $params[$key] = str_replace("'", "`", $params[$key]);
            $params[$key] = str_replace('"', "`", $params[$key]);
        }
    }

    $app->params = $params;

    openlog("sneaker", LOG_PID, LOG_LOCAL0);

};




