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
 * power check-erp
 */
function power(){
    $app = \Slim\Slim::getInstance();

    if(!$app->Sneaker->user_info){
        error(8100);
    }
    $user_info = $app->Sneaker->user_info;

    if(!$user_info['admin']){
    	$power = $user_info['power'];
    	$uri = $app->request->getResourceUri();
    	$uri_list = explode("/", $uri);
    	$uri_need = "/".$uri_list[1]."/".$uri_list[2];
    	if(!in_array($uri_need, $power)){
    		error(8100);
    	}
    }

}


