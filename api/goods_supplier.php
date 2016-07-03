<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * goods_supplier 商品供应商关系管理
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} goods_supplier/create 添加商品供应商关系
 * @apiName goods_supplier/create
 * @apiGroup GoodsSupplier
 * @apiVersion 0.0.1
 * @apiDescription 添加商品供应商关系
 *
 * @apiParam {string} gid *商品ID
 * @apiParam {string} scid *供应商ID
 *
 **/

/**
 * @api {post} goods_supplier/delete/:id 删除商品供应商关系
 * @apiName goods_supplier/delete
 * @apiGroup GoodsSupplier
 * @apiVersion 0.0.1
 * @apiDescription 物理删除商品供应商关系
 *
 */

function goods_supplier($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $my_model = new GoodsSupplier($id);
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    switch($action){
        case 'create':
            init_log_oper($action, '添加商品供应商关系');
            param_need($data, ['gid','scid']);

            $data['cid'] = $cid;
            $id = $my_model -> create($data);
            success(['id' => $id]);
            break;

        case 'delete':
            init_log_oper($action, '删除商品供应商关系');
            if(!is_numeric($id)){
                error(1100);
            }

            //数据权限验证
            $res = $my_model->read_by_id();
            if (!$res || $res[0]['cid'] != $cid){
                error(8110);
            }

            $my_model->delete_by_id(); //物理删除
            success();
            break;

        default:
            error(1100);
    }

}
