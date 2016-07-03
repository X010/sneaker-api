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

function pbs($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    $pbs_id = $app->config('b2c_id')['pbs'];
    switch($action){
        case 'product_list':
            //获取产品列表
            $product_list = $app->config('vip_product');

            $c_model = new Customer();
            $c_res = $c_model->read_one([
                'cid' => $pbs_id,
                'ccid' => $cid
            ]);
            if(!$c_res){
                error(6314);
            }

            $cctype = $c_res['cctype'];

            foreach($product_list as $key=>$val){
                $temp = explode('_', $val['product_id']);
                $product_list[$key]['cctype'] = $temp[0];
            }

            $res = [
                'count' => count($product_list),
                'page_count' => 1,
                'data' => $product_list,
                'cctype' => $cctype
            ];
            success($res);
            break;

        case 'get_price':
            //批价
            //param: product_id
            //return: type 1-余额够 2-余额不够；price 余额差价
            $c_model = new Customer();
            $res = $c_model->get_price($data['product_id'], $cid, $app->platform);
            success($res);
            break;

        case 'change_vip':
            //转换VIP剩余天数
            //param: product_id
            $c_model = new Customer();
            $c_res = $c_model->change_vip($data['product_id'], $cid, $app->platform);
            if($c_res !== True){
                error($c_res);
            }
            success();
            break;

        default:
            error(1100);
    }

}
