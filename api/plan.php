<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * plan 计划任务管理（内部API）
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} plan/change_price 计划任务-调价
 * @apiName plan/change_price
 * @apiGroup Plan
 * @apiVersion 0.0.1
 * @apiDescription 计划任务-调价，只能由计划任务调用
 *
 * @apiParam {int} data 生成调价单所需数据
 *
 */

/**
 * @api {post} plan/reserve_snapshot 计划任务-库存快照
 * @apiName plan/reserve_snapshot
 * @apiGroup Plan
 * @apiVersion 0.0.1
 * @apiDescription 计划任务-库存快照，每天晚上定时备份库存，生成快照
 *
 *
 */

/**
 * @api {post} plan/delete_order 计划任务-自动作废过期的订单
 * @apiName plan/delete_order
 * @apiGroup Plan
 * @apiVersion 0.0.1
 * @apiDescription 计划任务-自动作废过期的订单
 *
 */

/**
 * @api {post} plan/health 计划任务-健康检查
 * @apiName plan/health
 * @apiGroup Plan
 * @apiVersion 0.0.1
 * @apiDescription 计划任务-健康检查
 *
 */

function plan($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $data = $app->params;
    switch($action){
        case 'change_price':
            //init_log_oper($action, '计划任务修改价格');
            $p_model = new Price($id);
            $my_data = json_decode($data['data'], True);
            $p_model->change_price($my_data);
            success();
            break;

        case 'reserve_snapshot':
            //init_log_oper($action, '库存快照');
            $r_model = new Reserve();
            $r_model->snapshot();
            success();
            break;

        case 'delete_order':
            //init_log_oper($action, '自动作废过期的订单');
            $r_model = new Order();
            $r_model->auto_delete();
            success();
            break;

        case 'vip_daily':
            //VIP 每日处理
            $c_model = new Customer();
            $c_res = $c_model->read_list_nopage([
                'vip_status'=>1,
                'cctype[>=]'=>2,
                'vip_type'=>2
            ]);
            $my_date = date('Y-m-d');
            $my_time = date('Y-m-d H:i:s');
            foreach($c_res as $val){
                //如果最近扣费日期不为空并且大于等于今天，认为已经扣费过了
                if($val['vip_reduce_time'] && substr($val['vip_reduce_time'], 0, 10)>=$my_date){
                    continue;
                }
                $id = $val['id'];

                if($my_date <= $val['vip_end_date']){
                    //未到期
                    $sql = "update r_customer set vip_balance=vip_balance-vip_daily_reduce,vip_reduce_time='$my_time' where id = $id";
                    $app->db->query($sql);
                }
                else{
                    //已到期
                    $sql = "update r_customer set cctype=1,vip_reduce_time='$my_time' where id = $id";
                    $app->db->query($sql);
                }
            }
            success();
            break;

        case 'health':
            die('ok');
            break;

        case 'test':
            //$a = 1/0;
            abc();
            success('test');
            break;

        default:
            error(1100);
    }

}
