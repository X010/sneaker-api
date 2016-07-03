<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * commission 业务员提成
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} commission/create 新增业务员提成结算单
 * @apiName commission/create
 * @apiGroup Commission
 * @apiVersion 0.0.1
 * @apiDescription 新增业务员提成结算单
 *
 * @apiParam {string} begin_date *开始日期
 * @apiParam {string} end_date *截止日期
 * @apiParam {string} suid *业务员ID
 * @apiParam {string} customer_count *客户数
 * @apiParam {string} stock_list *提成单据ID集合，用逗号分隔
 * @apiParam {json} goods_list *提成商品信息集合
 * @apiParam {json} - goods_list内容
 * @apiParam {int} gid *商品ID
 * @apiParam {string} commission_unit_price 提成单价
 * @apiParam {string} commission_rate 提成率
 * @apiParam {string} commission_amount 应提额
 * @apiParam {string} commission_real_amount 实提额
 * @apiParam {string} memo 备注
 *
 */

/**
 * @api {post} commission/check 新增并审核业务员提成结算单
 * @apiName commission/check
 * @apiGroup Commission
 * @apiVersion 0.0.1
 * @apiDescription 新增并审核业务员提成结算单
 *
 * @apiParam {string} begin_date *开始日期
 * @apiParam {string} end_date *截止日期
 * @apiParam {string} suid *业务员ID
 * @apiParam {string} customer_count *客户数
 * @apiParam {string} stock_list *提成单据ID集合，用逗号分隔
 * @apiParam {json} goods_list *提成商品信息集合
 * @apiParam {json} - goods_list内容
 * @apiParam {int} gid *商品ID
 * @apiParam {string} commission_unit_price 提成单价
 * @apiParam {string} commission_rate 提成率
 * @apiParam {string} commission_amount 应提额
 * @apiParam {string} commission_real_amount 实提额
 * @apiParam {string} memo 备注
 *
 */

/**
 * @api {post} commission/check/:id 修改并审核业务员提成结算单
 * @apiName commission/check/:id
 * @apiGroup Commission
 * @apiVersion 0.0.1
 * @apiDescription 修改并审核业务员提成结算单
 *
 * @apiParam {string} begin_date *开始日期
 * @apiParam {string} end_date *截止日期
 * @apiParam {string} suid *业务员ID
 * @apiParam {string} customer_count *客户数
 * @apiParam {string} stock_list *提成单据ID集合，用逗号分隔
 * @apiParam {json} goods_list *提成商品信息集合
 * @apiParam {json} - goods_list内容
 * @apiParam {int} gid *商品ID
 * @apiParam {string} commission_unit_price 提成单价
 * @apiParam {string} commission_rate 提成率
 * @apiParam {string} commission_amount 应提额
 * @apiParam {string} commission_real_amount 实提额
 * @apiParam {string} memo 备注
 *
 */

/**
 * @api {post} commission/read 查询业务员提成结算单列表
 * @apiName commission/read
 * @apiGroup Commission
 * @apiVersion 0.0.1
 * @apiDescription 查询业务员提成结算单列表
 *
 * @apiParam {string} begin_date 开始日期
 * @apiParam {string} end_date 截止日期
 * @apiParam {string} suid 业务员ID
 * @apiParam {string} status 单据状态
 * @apiParam {string} search 单据号ID
 *
 * @apiSuccess {string} begin_date 开始日期
 * @apiSuccess {string} end_date 截止日期
 * @apiSuccess {string} suid 业务员ID
 * @apiSuccess {string} suname 业务员姓名
 * @apiSuccess {string} customer_count 客户数
 * @apiSuccess {string} stock_list 提成单据ID集合，用逗号分隔
 * @apiSuccess {string} amount 单据金额
 * @apiSuccess {string} commission_amount 应提金额
 * @apiSuccess {string} commission_real_amount 实提金额
 * @apiSuccess {string} uid 操作员ID
 * @apiSuccess {string} uname 操作员姓名
 * @apiSuccess {string} cuid 审核员ID
 * @apiSuccess {string} createtime 创建时间
 * @apiSuccess {string} checktime 审核时间
 * @apiSuccess {string} memo 备注
 * @apiSuccess {string} status 状态 1-未审核 2-已审核 9-已取消
 */


/**
 * @api {post} commission/read/:id 查询业务员提成结算单详情
 * @apiName commission/read/:id
 * @apiGroup Commission
 * @apiVersion 0.0.1
 * @apiDescription 查询业务员提成结算单详情
 *
 * @apiSuccess {string} begin_date 开始日期
 * @apiSuccess {string} end_date 截止日期
 * @apiSuccess {string} suid 业务员ID
 * @apiSuccess {string} suname 业务员姓名
 * @apiSuccess {string} customer_count 客户数
 * @apiSuccess {string} stock_list 提成单据ID集合，用逗号分隔
 * @apiSuccess {string} amount 单据金额
 * @apiSuccess {string} commission_amount 应提金额
 * @apiSuccess {string} commission_real_amount 实提金额
 * @apiSuccess {string} uid 操作员ID
 * @apiSuccess {string} uname 操作员姓名
 * @apiSuccess {string} cuid 审核员ID
 * @apiSuccess {string} createtime 创建时间
 * @apiSuccess {string} checktime 审核时间
 * @apiSuccess {string} memo 备注
 * @apiSuccess {string} status 状态 1-未审核 2-已审核 9-已取消
 * @apiSuccess {list} commission_glist *提成商品信息集合
 * @apiSuccess {list} - commission_glist
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {string} gcode 商品编码
 * @apiSuccess {string} gbarcode 商品条码
 * @apiSuccess {string} gspec 商品规格
 * @apiSuccess {string} gunit 商品单位
 * @apiSuccess {string} total 商品数量
 * @apiSuccess {string} box_total 商品箱数
 * @apiSuccess {string} unit_price 商品单价
 * @apiSuccess {string} amount_price 商品价格
 * @apiSuccess {string} commission_unit_price 提成单价
 * @apiSuccess {string} commission_rate 提成率
 * @apiSuccess {string} commission_amount 应提额
 * @apiSuccess {string} commission_real_amount 实提额
 * @apiSuccess {string} memo 备注
 *
 */

/**
 * @api {post} commission/delete/:id 取消业务员提成结算单详情
 * @apiName commission/delete/:id
 * @apiGroup Commission
 * @apiVersion 0.0.1
 * @apiDescription 取消业务员提成结算单详情
 *
 */

/**
 * @api {post} commission/flush/:id 冲正业务员提成结算单详情
 * @apiName commission/flush/:id
 * @apiGroup Commission
 * @apiVersion 0.0.1
 * @apiDescription 冲正业务员提成结算单详情
 *
 */

/**
 * @api {post} commission/read_stock 获取单据列表
 * @apiName commission/read_stock
 * @apiGroup Commission
 * @apiVersion 0.0.1
 * @apiDescription 获取单据列表
 *
 * @apiParam {string} begin_date 开始日期
 * @apiParam {string} end_date 截止日期
 * @apiParam {string} suid 业务员ID
 *
 * @apiSuccess {string} customer_count 客户数目
 * @apiSuccess {list} data 单据信息，二维数组返回
 * @apiSuccess {list} - data第二维数据内容
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {string} gcode 商品编码
 * @apiSuccess {string} gbarcode 商品条码
 * @apiSuccess {string} gspec 商品规格
 * @apiSuccess {string} gunit 商品单位
 * @apiSuccess {string} stock_id 单据ID
 * @apiSuccess {string} total 商品数量
 * @apiSuccess {string} box_total 商品箱数
 * @apiSuccess {string} unit_price 商品单价
 * @apiSuccess {string} amount_price 商品价格
 * @apiSuccess {string} memo 备注
 * @apiSuccess {string} group_amount 商品分组金额
 * @apiSuccess {string} group_total 商品分组数量
 * @apiSuccess {string} group_box_total 商品分组箱数
 */

function commission($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    $my_model = new Commission($id);

    switch($action){
        case 'create':
            init_log_oper($action, '新建业务员提成结算单');

            param_need($data, ['suid','customer_count','stock_list','goods_list']); //必选
            param_check($data, ['suid' => "/^\d+$/"]);

            $data = format_data_ids($data, ['stock_list']);

            $all = get_value($data, 'all');
            if($all == 1){
                $data['begin_date'] = '2016-01-01';
                $data['end_date'] = date('Y-m-d');
            }

            $data['cid'] = $cid;
            $data['cname'] = $app->Sneaker->cname;
            Power::set_oper($data);

            $data['suname'] = $my_model->get_name_by_id('o_user', $data['suid']);

            $data['status'] = 1;

            //生成提成结算单
            $res = $my_model -> my_create($data);

            success(['id'=>$res]);
            break;

        case 'check':

            Power::set_oper($data, 'cuid', 'cuname');

            if(isset($id)) {
                init_log_oper($action, '修改并审核业务员提成结算单');
                param_need($data, ['stock_list']); //必选

                $data = format_data_ids($data, ['stock_list']);

                $all = get_value($data, 'all');
                if($all == 1){
                    $data['begin_date'] = '2016-01-01';
                    $data['end_date'] = date('Y-m-d');
                }

                //检测是否有权限，状态等
                $my_model->my_power($id, 1);
                if(get_value($data, 'suid')){
                    $data['suname'] = $my_model->get_name_by_id('o_user', $data['suid']);
                }

                $my_model -> my_check($data, 'update');
            }
            else{
                init_log_oper($action, '新建并审核业务员提成结算单');

                param_need($data, ['suid','customer_count','stock_list','goods_list']); //必选
                param_check($data, ['suid' => "/^\d+$/"]);

                $data = format_data_ids($data, ['stock_list']);

                $all = get_value($data, 'all');
                if($all == 1){
                    $data['begin_date'] = '2016-01-01';
                    $data['end_date'] = date('Y-m-d');
                }

                $data['cid'] = $cid;
                $data['cname'] = $app->Sneaker->cname;
                Power::set_oper($data);

                $data['suname'] = $my_model->get_name_by_id('o_user', $data['suid']);

                $id = $my_model -> my_check($data, 'create');

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

        case 'read_stock':
            //init_log_oper($action, '获取出入库单据列表');
            param_need($data, ['suid']); //必选
            $data['cid'] = $cid;

            $all = get_value($data, 'all');
            if($all == 1){
                $data['begin_date'] = '2016-01-01';
                $data['end_date'] = date('Y-m-d');
            }

            $res = $my_model->read_stock($data);
            success($res);
            break;

        case 'flush':
            init_log_oper($action, '冲正业务员提成结算单');
            if(!isset($id)) {
                error(1100);
            }

            //检测是否有权限，状态等
            $res = $my_model->my_power($id, 2);

            $my_model->my_flush();
            success();
            break;

        case 'delete':
            init_log_oper($action, '取消业务员提成结算单');
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
