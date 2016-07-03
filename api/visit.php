<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * visit 回访记录管理
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} visit/create 新增回访记录
 * @apiName visit/create
 * @apiGroup Visit
 * @apiVersion 0.0.1
 * @apiDescription 新增回访记录
 *
 * @apiParam {int} order_id 订单号（订单回访）
 * @apiParam {int} ccid 客户ID（客户回访）
 * @apiParam {int} type 类型1-客户回访 2-订单回访
 * @apiParam {int} score_service 服务分数
 * @apiParam {int} score_deliver 快递分数
 * @apiParam {int} score_goods 商品分数
 * @apiParam {int} score_salesman 业务员分数
 * @apiParam {int} score_activity 活动分数
 * @apiParam {string} memo 备注
 *
 */

/**
 * @api {post} visit/update/:id 修改回访记录
 * @apiName visit/update/:id
 * @apiGroup Visit
 * @apiVersion 0.0.1
 * @apiDescription 修改回访记录
 *
 * @apiParam {int} order_id 订单号（订单回访）
 * @apiParam {int} ccid 客户ID（客户回访）
 * @apiParam {int} type 类型1-客户回访 2-订单回访
 * @apiParam {int} score_service 服务分数
 * @apiParam {int} score_deliver 快递分数
 * @apiParam {int} score_goods 商品分数
 * @apiParam {int} score_salesman 业务员分数
 * @apiParam {int} score_activity 活动分数
 * @apiParam {string} memo 备注
 *
 */

/**
 * @api {post} visit/read/:id 查询回访记录详情
 * @apiName visit/read/:id
 * @apiGroup Visit
 * @apiVersion 0.0.1
 * @apiDescription 查询回访记录详情
 *
 * @apiSuccess {string} order_id 订单号（订单回访）
 * @apiSuccess {string} ccid 客户ID（客户回访）
 * @apiSuccess {string} ccname 客户名称
 * @apiSuccess {string} uid 创建者ID
 * @apiSuccess {string} uname 创建者名称
 * @apiSuccess {string} type 类型1-客户回访 2-订单回访
 * @apiSuccess {string} score_service 服务分数
 * @apiSuccess {string} score_deliver 快递分数
 * @apiSuccess {string} score_goods 商品分数
 * @apiSuccess {string} score_salesman 业务员分数
 * @apiSuccess {string} score_activity 活动分数
 * @apiSuccess {string} memo 备注
 *
 */


/**
 * @api {post} visit/read 查询回访记录列表
 * @apiName visit/read
 * @apiGroup Visit
 * @apiVersion 0.0.1
 * @apiDescription 查询回访记录列表
 *
 * @apiParam {int} uid 客服ID
 * @apiParam {int} ccid 客户ID
 * @apiParam {int} type 类型1-客户回访 2-订单回访
 * @apiParam {int} score_service 服务分数
 * @apiParam {int} score_deliver 快递分数
 * @apiParam {int} score_goods 商品分数
 * @apiParam {string} begin_date 开始日期
 * @apiParam {string} end_date 截止日期
 *
 * @apiSuccess {string} order_id 订单号（订单回访）
 * @apiSuccess {string} ccid 客户ID（客户回访）
 * @apiSuccess {string} ccname 客户名称
 * @apiSuccess {string} uid 创建者ID
 * @apiSuccess {string} uname 创建者名称
 * @apiSuccess {string} type 类型1-客户回访 2-订单回访
 * @apiSuccess {string} score_service 服务分数
 * @apiSuccess {string} score_deliver 快递分数
 * @apiSuccess {string} score_goods 商品分数
 * @apiSuccess {string} score_salesman 业务员分数
 * @apiSuccess {string} score_activity 活动分数
 * @apiSuccess {string} memo 备注
 *
 */

function visit($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    $my_model = new Visit($id);
    switch($action){
        case 'create':
            init_log_oper($action, '创建回访记录');

            param_need($data, ['type']); //必选
            param_check($data, [
                'type,score_service,score_deliver,score_goods,score_salesman,score_activity' => "/^\d+$/"
            ]);

            $data['cid'] = $cid;
            $data['cname'] = $app->Sneaker->cname;
            if(get_value($data, 'ccid')){
                $data['ccname'] = $my_model->get_name_by_id('o_company', $data['ccid']);
            }

            Power::set_oper($data);

            $res = $my_model -> my_create($data);

            success(['id'=>$res]);
            break;

        case 'update':
            if(!isset($id)) {
                error(1100);
            }
            init_log_oper($action, '修改回访记录');

            param_check($data, [
                'type,score_service,score_deliver,score_goods,score_salesman,score_activity' => "/^\d+$/"
            ]);

            $my_model->my_power($id, 0);

            if(get_value($data, 'ccid')){
                $data['ccname'] = $my_model->get_name_by_id('o_company', $data['ccid']);
            }

            $my_model->update_by_id($data);

            success(['id'=>$id]);
            break;

        case 'read':
            if(isset($id)) {
                //检测是否有权限，状态等
                $my_model->my_power($id, 0);
                $res = $my_model -> read_by_id();
                success($res[0]);
            }
            else{
                $data['cid'] = $cid;
                $res = $my_model -> read_list($data);
                success($res);
            }
            break;

        default:
            error(1100);
    }

}
