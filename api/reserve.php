<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * store
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */


//----------------------------该文件已停用------------------------^_^



/**
 * @api {post} reserve/read_reserve 查询库存商品列表（已停用）
 * @apiName reserve/read_reserve
 * @apiGroup Reserve
 * @apiVersion 0.0.1
 * @apiDescription 查询库存商品列表
 *
 * @apiParam {int} sid 仓库ID
 * @apiParam {int} gids 商品ID，用逗号分隔
 *
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {int} total 商品总数
 *
 */

function reserve($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $my_model = new Reserve($id);
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    switch($action){

        default:
            error(1100);
    }

}
