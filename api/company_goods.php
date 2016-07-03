<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * company_goods 公司商品管理
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} company_goods/create 创建公司商品
 * @apiName company_goods/create
 * @apiGroup CompanyGoods
 * @apiVersion 0.0.1
 * @apiDescription 创建公司商品
 *
 * @apiParam {string} data *大JSON字段，是一个list，每个单元中字段如下 
 * @apiParam {json} - data字段详情
 * @apiParam {int} gid *商品ID
 * @apiParam {int} gtid *商品类型ID
 * @apiParam {int} out_cid *供应商ID
 * @apiParam {string} in_price *进货价格
 * @apiParam {string} out_price1 *出货价格1
 * @apiParam {string} out_price2 *出货价格2
 * @apiParam {string} out_price3 *出货价格3
 * @apiParam {string} out_price4 *出货价格4
 */

/**
 * @api {post} company_goods/update/:id 更新公司商品
 * @apiName company_goods/update/:id
 * @apiGroup CompanyGoods
 * @apiVersion 0.0.1
 * @apiDescription 更新公司商品，注意：目前只能更新分类信息
 *
 * @apiParam {int} gtid *公司商品类型ID
 *
 */

/**
 * @api {post} company_goods/delete/:id 删除公司商品
 * @apiName company_goods/delete/:id
 * @apiGroup CompanyGoods
 * @apiVersion 0.0.1
 * @apiDescription 删除公司商品
 */



/**
 * @api {post} company_goods/buy_off/:id 停用商品采购功能
 * @apiName company_goods/buy_off
 * @apiGroup CompanyGoods
 * @apiVersion 0.0.1
 * @apiDescription 停用商品采购功能
 *
 */

/**
 * @api {post} company_goods/buy_on/:id 启用商品采购功能
 * @apiName company_goods/buy_on
 * @apiGroup CompanyGoods
 * @apiVersion 0.0.1
 * @apiDescription 启用商品采购功能
 *
 */

function company_goods($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $my_model = new CompanyGoods($id);
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    switch($action){
        case 'create':
            init_log_oper($action, '创建公司商品档案');
            param_need($data, ['data']); //必选
            $params = json_decode($data['data'], True);
            if (!$params) error(1102);

            foreach($params as $key=>$val){
                param_need($val, ['gid','gtid','out_cid','in_price',
                    'out_price1','out_price2','out_price3','out_price4']); //必选
                param_check($val, [
                    'gid,gtid,out_cid' => "/^\d+$/",
                    'in_price,out_price1,out_price2,out_price3,out_price4' => "/^[-.\d]+$/",
                ]);
                //找到用户公司ID，补充到参数里
                $params[$key]['in_cid'] = $cid;
            }
            //创建数据
            $ret = $my_model->my_create($params);
            success($ret);
            break;

        case 'update':
            init_log_oper($action, '修改公司商品档案');
            if(!is_numeric($id)){
                error(1100);
            }

            param_check($data, [
                'gtid' => "/^\d+$/",
            ]);
            //检测数据合法性
            $res = $my_model -> my_power($id);
            //更新数据
            $my_model -> my_update($data, $res['gid']);
            success();
            break;

        case 'delete':
            init_log_oper($action, '删除公司商品档案');
            if(!is_numeric($id)){
                error(1100);
            }
            //检测数据合法性
            $my_model -> my_power($id);
            $my_model -> my_delete();
            success();
            break;

        case 'buy_off':
            init_log_oper($action, '停用公司商品采购功能');
            if(!is_numeric($id)){
                error(1100);
            }
            //检测数据合法性
            $my_model -> my_power($id);
            $my_model -> update_by_id([
                'limit_buy'=>2
            ]);
            success();
            break;

        case 'buy_on':
            init_log_oper($action, '启用公司商品采购功能');
            if(!is_numeric($id)){
                error(1100);
            }
            //检测数据合法性
            $my_model -> my_power($id);
            $my_model -> update_by_id([
                'limit_buy'=>1
            ]);
            success();
            break;

        default:
            error(1100);
    }

}
