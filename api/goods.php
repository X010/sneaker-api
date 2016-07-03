<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * goods 商品管理
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} goods/create 添加商品
 * @apiName goods/create
 * @apiGroup Goods
 * @apiVersion 0.0.1
 * @apiDescription 添加商品
 *
 * @apiParam {string} name *商品名称
 * @apiParam {string} barcode *商品条码
 * @apiParam {int} bid *商品品牌ID
 * @apiParam {string} factory 厂商
 * @apiParam {string} place 产地
 * @apiParam {string} spec *规格
 * @apiParam {string} unit *单位
 * @apiParam {string} tax_rate *税率
 * @apiParam {int} valid_period 有效期，单位天
 * @apiParam {int} gtid *商品类型ID
 * @apiParam {int} out_cid *供应商ID
 * @apiParam {string} in_price *进货价格
 * @apiParam {string} out_price1 *出货价格1
 * @apiParam {string} out_price2 *出货价格2
 * @apiParam {string} out_price3 *出货价格3
 * @apiParam {string} out_price4 *出货价格4
 * @apiParam {string} business *经营方式
 * @apiParam {string} weight 箱重
 */

function goods($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $my_model = new Goods($id);
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    switch($action){
        case 'create':
            init_log_oper($action, '添加商品');
            param_need($data, ['name','barcode','spec','unit','tax_rate','out_cid','in_price','out_price1','out_price2',
                'out_price3','out_price4','business']);

            $data['cid'] = $cid;
            $id = $my_model -> my_create($data);
            success(['id' => $id]);
            break;

        default:
            error(1100);
    }

}
