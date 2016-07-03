<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * a_salesman 业务员APP接口
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

function customer($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    switch($action){
        case 'test':

            success(['success']);
            break;



        default:
            error(1100);
    }

}
