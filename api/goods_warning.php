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

function goods_warning($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $my_model = new GoodsWarning($id);
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    switch($action){
        case 'create':
            init_log_oper($action, '添加商品预警');
            param_need($data, ['sid','goods_list']);

            $data['cid'] = $cid;
            $my_model -> my_create($data);
            success();
            break;

        case 'read':
            //init_log_oper($action, '查询商品预警');
            param_need($data, ['sid']);
            $data['cid'] = $cid;
            $data['orderby'] = 'id^asc';
            $res = $my_model->read_list_nopage($data);

            foreach($res as $key=>$val){
                $res[$key]['box_total'] = round($val['total']/$val['gspec'], 2);
            }

            success($res);
            break;

        default:
            error(1100);
    }

}
