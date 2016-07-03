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
 * @api {post} store_goods/create 创建商品价格
 * @apiName store_goods/create
 * @apiGroup StoreGoods
 * @apiVersion 0.0.1
 * @apiDescription 创建商品价格
 *
 * @apiParam {string} data *大JSON字段，是一个list，每个单元中字段如下 
 * @apiParam {json} - data字段详情
 * @apiParam {int} gid *商品ID
 * @apiParam {int} in_sid *进货仓库ID
 * @apiParam {int} out_cid 出货公司ID
 * @apiParam {int} out_sid 出货仓库ID
 * @apiParam {string} in_price 进货价格
 * @apiParam {string} out_price1 出货价格1
 * @apiParam {string} out_price2 出货价格2
 * @apiParam {string} out_price3 出货价格3
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *         "err": 0,
 *         "status": "0000",
 *         "msg": {"id":33}        //id：商品价格表ID
 *     }
 *
 */

/**
 * @api {post} store_goods/update/:id 更新商品价格
 * @apiName store_goods/update
 * @apiGroup StoreGoods
 * @apiVersion 0.0.1
 * @apiDescription 更新商品价格，注意：每个价格只能设置一次，第二次设置会失效
 *
 * @apiParam {int} out_cid 出货公司ID
 * @apiParam {int} out_sid 出货仓库ID
 * @apiParam {string} in_price 进货价格
 * @apiParam {string} out_price1 出货价格1
 * @apiParam {string} out_price2 出货价格2
 * @apiParam {string} out_price3 出货价格3
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *         "err": 0,
 *         "status": "0000",
 *         "msg": "success"
 *     }
 *
 */

/**
 * @api {post} store_goods/delete/:id 取消商品价格
 * @apiName store_goods/delete
 * @apiGroup StoreGoods
 * @apiVersion 0.0.1
 * @apiDescription 取消商品价格
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *         "err": 0,
 *         "status": "0000",
 *         "msg": "success"
 *     }
 *
 */

/**
 * @api {post} store_goods/read/:id 查询商品价格详情
 * @apiName store_goods/read/id
 * @apiGroup StoreGoods
 * @apiVersion 0.0.1
 * @apiDescription 查询商品价格详情
 *
 * @apiSuccess {int} id 商品价格表ID
 * @apiSuccess {int} in_cid 进货公司ID
 * @apiSuccess {int} in_sid 进货仓库ID
 * @apiSuccess {int} out_cid 出货公司ID
 * @apiSuccess {int} out_cname 出货公司名称
 * @apiSuccess {int} out_sid 出货仓库ID
 * @apiSuccess {int} out_sname 出货仓库名称
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {int} gname 商品名称
 * @apiSuccess {int} gcode 商品CODE
 * @apiSuccess {string} in_price 进货价格
 * @apiSuccess {string} out_price1 出货价格1
 * @apiSuccess {string} out_price2 出货价格2
 * @apiSuccess {string} out_price3 出货价格3
 * @apiSuccess {array} goods 商品基本信息详情
 *
 */

/**
 * @api {post} store_goods/read 浏览商品价格列表
 * @apiName store_goods/read
 * @apiGroup StoreGoods
 * @apiVersion 0.0.1
 * @apiDescription 商品价格详情
 *
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 * @apiParam {int} in_sid 进货仓库ID
 * @apiParam {string} orderby 排序字段：如 code^desc 按code倒序 id^asc 按ID正序
 *
 */

/**
 * @api {post} store_goods/read/:id 查询商品价格详情
 * @apiName store_goods/read/id
 * @apiGroup StoreGoods
 * @apiVersion 0.0.1
 * @apiDescription 查询商品价格详情
 *
 * @apiSuccess {int} id 商品价格表ID
 * @apiSuccess {int} in_cid 进货公司ID
 * @apiSuccess {int} in_sid 进货仓库ID
 * @apiSuccess {int} out_cid 出货公司ID
 * @apiSuccess {int} out_cname 出货公司名称
 * @apiSuccess {int} out_sid 出货仓库ID
 * @apiSuccess {int} out_sname 出货仓库名称
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {int} gname 商品名称
 * @apiSuccess {int} gcode 商品CODE
 * @apiSuccess {string} in_price 进货价格
 * @apiSuccess {string} out_price1 出货价格1
 * @apiSuccess {string} out_price2 出货价格2
 * @apiSuccess {string} out_price3 出货价格3
 * @apiSuccess {array} goods 商品基本信息详情
 *
 */

function store_goods($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $my_model = new StoreGoods($id);
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    switch($action){
//        case 'create':
//            init_log_oper($action, '创建商品价格');
//            param_need($data, ['data']); //必选
//            $params = json_decode($data['data'], True);
//            if (!$params) error(1102);
//
//            foreach($params as $key=>$val){
//                //找到用户公司ID，补充到参数里
//                $params[$key]['in_cid'] = $cid;
//                //查看当前用户有没有in_sid 仓库权限
//                Power::check_my_sid($val['in_sid']);
//            }
//            $ret = $my_model->my_create($params);
//
//            success($ret);
//            break;


        default:
            error(1100);
    }

}
