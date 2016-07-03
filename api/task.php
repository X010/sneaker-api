<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * task 销售任务管理
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} task/create 新建业务员销售任务
 * @apiName task/create
 * @apiGroup Task
 * @apiVersion 0.0.1
 * @apiDescription 新建业务员销售任务
 *
 * @apiParam {int} suid 业务员ID
 * @apiParam {string} year 年份
 * @apiParam {int} type 类型 1-箱数任务 2-金额任务
 * @apiParam {json} goods_list 商品任务列表
 * @apiParam {json} - goods_list字段详情
 * @apiParam {int} gid 商品ID
 * @apiParam {string} val1 1月任务
 * @apiParam {string} val2 2月任务
 * @apiParam {string} val3 3月任务
 * @apiParam {string} val4 4月任务
 * @apiParam {string} val5 5月任务
 * @apiParam {string} val6 6月任务
 * @apiParam {string} val7 7月任务
 * @apiParam {string} val8 8月任务
 * @apiParam {string} val9 9月任务
 * @apiParam {string} val10 10月任务
 * @apiParam {string} val11 11月任务
 * @apiParam {string} val12 12月任务
 * @apiParam {json} add_up 总任务
 * @apiParam {json} - add_up字段详情
 * @apiParam {string} val1 1月任务
 * @apiParam {string} val2 2月任务
 * @apiParam {string} val3 3月任务
 * @apiParam {string} val4 4月任务
 * @apiParam {string} val5 5月任务
 * @apiParam {string} val6 6月任务
 * @apiParam {string} val7 7月任务
 * @apiParam {string} val8 8月任务
 * @apiParam {string} val9 9月任务
 * @apiParam {string} val10 10月任务
 * @apiParam {string} val11 11月任务
 * @apiParam {string} val12 12月任务
 *
 * @apiSuccess {int} id 任务单ID
 *
 */

/**
 * @api {post} task/update/:id 修改业务员销售任务
 * @apiName task/update/:id
 * @apiGroup Task
 * @apiVersion 0.0.1
 * @apiDescription 修改业务员销售任务
 *
 * @apiParam {int} suid 业务员ID
 * @apiParam {string} year 年份
 * @apiParam {int} type 类型 1-箱数任务 2-金额任务
 * @apiParam {json} goods_list 商品任务列表
 * @apiParam {json} - goods_list字段详情
 * @apiParam {int} gid 商品ID
 * @apiParam {string} val1 1月任务
 * @apiParam {string} val2 2月任务
 * @apiParam {string} val3 3月任务
 * @apiParam {string} val4 4月任务
 * @apiParam {string} val5 5月任务
 * @apiParam {string} val6 6月任务
 * @apiParam {string} val7 7月任务
 * @apiParam {string} val8 8月任务
 * @apiParam {string} val9 9月任务
 * @apiParam {string} val10 10月任务
 * @apiParam {string} val11 11月任务
 * @apiParam {string} val12 12月任务
 * @apiParam {json} add_up 总任务
 * @apiParam {json} - add_up字段详情
 * @apiParam {string} val1 1月任务
 * @apiParam {string} val2 2月任务
 * @apiParam {string} val3 3月任务
 * @apiParam {string} val4 4月任务
 * @apiParam {string} val5 5月任务
 * @apiParam {string} val6 6月任务
 * @apiParam {string} val7 7月任务
 * @apiParam {string} val8 8月任务
 * @apiParam {string} val9 9月任务
 * @apiParam {string} val10 10月任务
 * @apiParam {string} val11 11月任务
 * @apiParam {string} val12 12月任务
 *
 * @apiSuccess {int} id 任务单ID
 *
 */

/**
 * @api {post} task/read/:id 查询业务员销售任务详情
 * @apiName task/read/:id
 * @apiGroup Task
 * @apiVersion 0.0.1
 * @apiDescription 查询业务员销售任务详情
 *
 * @apiSuccess {int} suid 业务员ID
 * @apiSuccess {string} suname 业务员姓名
 * @apiSuccess {string} year 年份
 * @apiSuccess {int} type 类型 1-箱数任务 2-金额任务
 * @apiSuccess {int} status 状态 1-正常 9-取消
 * @apiSuccess {string} val1 1月任务
 * @apiSuccess {string} val2 2月任务
 * @apiSuccess {string} val3 3月任务
 * @apiSuccess {string} val4 4月任务
 * @apiSuccess {string} val5 5月任务
 * @apiSuccess {string} val6 6月任务
 * @apiSuccess {string} val7 7月任务
 * @apiSuccess {string} val8 8月任务
 * @apiSuccess {string} val9 9月任务
 * @apiSuccess {string} val10 10月任务
 * @apiSuccess {string} val11 11月任务
 * @apiSuccess {string} val12 12月任务
 * @apiSuccess {string} val_all 总任务
 * @apiSuccess {string} createtime 创建时间
 * @apiSuccess {json} goods_list 商品任务列表
 * @apiSuccess {json} - goods_list字段详情
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {string} gcode 商品编码
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {string} gbarcode 商品条码
 * @apiSuccess {string} gunit 商品计量单位
 * @apiSuccess {string} gspec 商品规格
 * @apiSuccess {string} val1 1月任务
 * @apiSuccess {string} val2 2月任务
 * @apiSuccess {string} val3 3月任务
 * @apiSuccess {string} val4 4月任务
 * @apiSuccess {string} val5 5月任务
 * @apiSuccess {string} val6 6月任务
 * @apiSuccess {string} val7 7月任务
 * @apiSuccess {string} val8 8月任务
 * @apiSuccess {string} val9 9月任务
 * @apiSuccess {string} val10 10月任务
 * @apiSuccess {string} val11 11月任务
 * @apiSuccess {string} val12 12月任务
 * @apiSuccess {string} val_all 总任务
 *
 */

/**
 * @api {post} task/read 查询业务员销售任务列表
 * @apiName task/read
 * @apiGroup Task
 * @apiVersion 0.0.1
 * @apiDescription 查询业务员销售任务列表
 *
 * @apiParam {int} suid 业务员ID
 * @apiParam {int} status 状态
 * @apiParam {int} type 类型 1-箱数任务 2-金额任务
 * @apiParam {string} begin_date 开始日期
 * @apiParam {string} end_date 截止日期
 *
 * @apiSuccess {int} suid 业务员ID
 * @apiSuccess {string} suname 业务员姓名
 * @apiSuccess {string} year 年份
 * @apiSuccess {int} type 类型 1-箱数任务 2-金额任务
 * @apiSuccess {int} status 状态 1-正常 9-取消
 * @apiSuccess {string} val1 1月任务
 * @apiSuccess {string} val2 2月任务
 * @apiSuccess {string} val3 3月任务
 * @apiSuccess {string} val4 4月任务
 * @apiSuccess {string} val5 5月任务
 * @apiSuccess {string} val6 6月任务
 * @apiSuccess {string} val7 7月任务
 * @apiSuccess {string} val8 8月任务
 * @apiSuccess {string} val9 9月任务
 * @apiSuccess {string} val10 10月任务
 * @apiSuccess {string} val11 11月任务
 * @apiSuccess {string} val12 12月任务
 * @apiSuccess {string} val_all 总任务
 * @apiSuccess {string} createtime 创建时间
 *
 */

/**
 * @api {post} task/delete/:id 取消业务员销售任务
 * @apiName task/delete/:id
 * @apiGroup Task
 * @apiVersion 0.0.1
 * @apiDescription 取消业务员销售任务
 *
 */

function task($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    $my_model = new Task($id);

    switch($action){
        case 'create':
            init_log_oper($action, '新建业务员销售任务');

            param_need($data, ['suid','year','type','add_up']); //必选
            param_check($data, ['suid' => "/^\d+$/"]);

            $data['cid'] = $cid;
            $data['cname'] = $app->Sneaker->cname;
            Power::set_oper($data);

            $data['suname'] = $my_model->get_name_by_id('o_user', $data['suid']);
            $data['status'] = 1;

            //生成提成结算单
            $res = $my_model -> my_create($data);

            success(['id'=>$res]);
            break;

        case 'update':
            Power::set_oper($data);
            if(isset($id)) {
                init_log_oper($action, '修改业务员销售任务');

                $data['cid'] = $cid;

                //检测是否有权限，状态等
                $my_model->my_power($id, 1);
                if(get_value($data, 'suid')){
                    $data['suname'] = $my_model->get_name_by_id('o_user', $data['suid']);
                }

                $my_model -> my_update($data);
            }
            success(['id'=>$id]);
            break;

        case 'read':
            if(isset($id)) {
                //检测是否有权限，状态等
                $my_model->my_power($id, 0);
                $res = $my_model -> my_read();
                success($res);
            }
            else{
                param_check($data, ['ccid,status' => "/^\d+$/"]);
                $data['cid'] = $cid;
                $res = $my_model -> read_list($data);
                success($res);
            }
            break;

        case 'delete':
            init_log_oper($action, '取消业务员销售任务');
            if (!is_numeric($id)) error(1100);

            $my_model->my_power($id, 1);

            $data = ['status'=>9];
            Power::set_oper($data, 'cuid', 'cuname');
            $my_model->my_delete($data); //修改订单
            success(['id'=>$id]);
            break;

        default:
            error(1100);
    }

}
