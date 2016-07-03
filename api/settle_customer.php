<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * settle_customer 客户结算单管理
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} settle_customer/create 新建客户结算单
 * @apiName settle_customer/create
 * @apiGroup SettleCustomer
 * @apiVersion 0.0.1
 * @apiDescription 新建客户结算单
 *
 * @apiParam {int} ccid *客户ID
 * @apiParam {int} pay_type 付款方式
 * @apiParam {json} stock_list *单据列表
 * @apiParam {json} - *单据列表详情字段
 * @apiParam {int} id *单据ID
 *
 * @apiSuccess {int} id 结算单ID
 *
 */

/**
 * @api {post} settle_customer/read_stock 获取出入库单据
 * @apiName settle_customer/read_stock
 * @apiGroup SettleCustomer
 * @apiVersion 0.0.1
 * @apiDescription 获取出入库单据
 *
 * @apiParam {int} ccid *客户ID
 * @apiParam {string} begintime *审核起始日期
 * @apiParam {string} endtime *审核截止日期
 * @apiParam {string} order_id 订单号（如果传订单号就不需要传其它检索条件）
 *
 * @apiSuccess {int} id 出入库单据号
 * @apiSuccess {int} order_id 订单号
 * @apiSuccess {int} stock_type 单据类型1-销售出库单 2-退货入库单
 * @apiSuccess {int} sid 仓库ID
 * @apiSuccess {string} sname 仓库名称
 * @apiSuccess {int} uid 操作员ID
 * @apiSuccess {string} uname 操作员名称
 * @apiSuccess {int} cuid 审核员ID
 * @apiSuccess {string} cuname 审核员名称
 * @apiSuccess {int} suid 业务员ID
 * @apiSuccess {string} suname 业务员名称
 * @apiSuccess {int} amount 单据总金额
 * @apiSuccess {int} tax_amount 单据总税额
 * @apiSuccess {string} checktime 审核时间
 * @apiSuccess {string} lastdate 结算日期
 */

/**
 * @api {post} settle_customer/check 新建并审核客户结算单
 * @apiName settle_customer/check
 * @apiGroup SettleCustomer
 * @apiVersion 0.0.1
 * @apiDescription 新建并审核客户结算单
 *
 * @apiParam {int} ccid *客户ID
 * @apiParam {int} pay_type 付款方式
 * @apiParam {json} stock_list *单据列表
 * @apiParam {json} - *单据列表详情字段
 * @apiParam {int} id *单据ID
 *
 * @apiSuccess {int} id 结算单ID
 *
 */

/**
 * @api {post} settle_customer/check/:id 修改并审核客户结算单
 * @apiName settle_customer/check/:id
 * @apiGroup SettleCustomer
 * @apiVersion 0.0.1
 * @apiDescription 修改并审核客户结算单
 *
 * @apiParam {int} pay_type 付款方式
 * @apiParam {json} stock_list *单据列表
 * @apiParam {json} - *单据列表详情字段
 * @apiParam {int} id *单据ID
 *
 * @apiSuccess {int} id 结算单ID
 *
 */

/**
 * @api {post} settle_customer/read/:id 读取客户结算单明细
 * @apiName settle_customer/read/:id
 * @apiGroup SettleCustomer
 * @apiVersion 0.0.1
 * @apiDescription 读取客户结算单明细
 *
 *
 * @apiSuccess {int} id 结算单ID
 * @apiSuccess {int} ccid 客户ID
 * @apiSuccess {string} ccname 客户名称
 * @apiSuccess {string} createtime 结算单生成时间
 * @apiSuccess {string} checktime 结算单审核时间
 * @apiSuccess {int} uid 操作员ID
 * @apiSuccess {string} uname 操作员名称
 * @apiSuccess {int} cuid 审核员ID
 * @apiSuccess {string} cuname 审核员名称
 * @apiSuccess {int} amount_price 总金额
 * @apiSuccess {int} tax_price 总税额
 * @apiSuccess {int} status 状态
 * @apiSuccess {list} tax_group 税率分组
 * @apiSuccess {list} - 税率分组详情字段
 * @apiSuccess {string} tax_rate 税率
 * @apiSuccess {string} amount 分组金额
 * @apiSuccess {list} stock_list 单据详情
 * @apiSuccess {list} - 单据详情字段
 * @apiSuccess {int} id 出入库单据号
 * @apiSuccess {int} order_id 订单号
 * @apiSuccess {int} stock_type 单据类型1-销售出库单 2-退货入库单
 * @apiSuccess {int} sid 仓库ID
 * @apiSuccess {string} sname 仓库名称
 * @apiSuccess {int} uid 操作员ID
 * @apiSuccess {string} uname 操作员名称
 * @apiSuccess {int} cuid 审核员ID
 * @apiSuccess {string} cuname 审核员名称
 * @apiSuccess {int} suid 业务员ID
 * @apiSuccess {string} suname 业务员名称
 * @apiSuccess {int} amount 单据总金额
 * @apiSuccess {int} tax_amount 单据总税额
 * @apiSuccess {string} checktime 审核时间
 * @apiSuccess {string} lastdate 结算日期
 *
 */

/**
 * @api {post} settle_customer/read 读取客户结算单列表
 * @apiName settle_customer/read
 * @apiGroup SettleCustomer
 * @apiVersion 0.0.1
 * @apiDescription 读取客户结算单列表
 *
 * @apiParam {int} ccid 客户ID
 * @apiParam {int} status 状态1-未审核 2-已审核 10-已冲正 11-冲正单（负单）
 *
 * @apiSuccess {int} id 结算单ID
 * @apiSuccess {int} ccid 客户ID
 * @apiSuccess {string} ccname 客户名称
 * @apiSuccess {string} createtime 结算单生成时间
 * @apiSuccess {string} checktime 结算单审核时间
 * @apiSuccess {int} uid 操作员ID
 * @apiSuccess {string} uname 操作员名称
 * @apiSuccess {int} cuid 审核员ID
 * @apiSuccess {string} cuname 审核员名称
 * @apiSuccess {list} stock_list 单据详情
 * @apiSuccess {int} status 状态
 * @apiSuccess {int} amount_price 总金额
 * @apiSuccess {int} tax_price 总税额
 */

/**
 * @api {post} settle_customer/read_detail/:id 读取客户结算单商品明细
 * @apiName settle_customer/read_detail/:id
 * @apiGroup SettleCustomer
 * @apiVersion 0.0.1
 * @apiDescription 读取客户结算单明细
 *
 *
 * @apiSuccess {int} id 结算单ID
 * @apiSuccess {int} ccid 客户ID
 * @apiSuccess {string} ccname 客户名称
 * @apiSuccess {string} createtime 结算单生成时间
 * @apiSuccess {string} checktime 结算单审核时间
 * @apiSuccess {int} uid 操作员ID
 * @apiSuccess {string} uname 操作员名称
 * @apiSuccess {int} cuid 审核员ID
 * @apiSuccess {string} cuname 审核员名称
 * @apiSuccess {int} amount_price 总金额
 * @apiSuccess {int} tax_price 总税额
 * @apiSuccess {int} status 状态
 * @apiSuccess {list} goods_list 商品清单
 * @apiSuccess {list} - 商品清单字段
 * @apiSuccess {int} order_id 订单号
 * @apiSuccess {int} stock_id 出入库单号
 * @apiSuccess {int} stock_type 单据类型1-销售出库单 2-退货入库单
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {int} gcode 商品编码
 * @apiSuccess {string} gbarcode 商品条码
 * @apiSuccess {int} unit_price 单价
 * @apiSuccess {string} amount_price 总价
 * @apiSuccess {int} tax_price 税额
 * @apiSuccess {string} total 数目
 *
 */

/**
 * @api {post} settle_customer/flush/:id 冲正客户结算单
 * @apiName settle_customer/flush/:id
 * @apiGroup SettleCustomer
 * @apiVersion 0.0.1
 * @apiDescription 冲正客户结算单
 *
 * @apiSuccess {int} id 结算单ID
 *
 */

/**
 * @api {post} settle_customer/delete 取消客户结算单
 * @apiName settle_customer/delete
 * @apiGroup SettleCustomer
 * @apiVersion 0.0.1
 * @apiDescription 取消客户结算单
 *
 * @apiSuccess {int} id 结算单号
 *
 */

function settle_customer($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    $my_model = new SettleCustomer($id);

    switch($action){
        case 'create':
            init_log_oper($action, '新建客户结算单');

            param_need($data, ['ccid','stock_list']); //必选
            //param_check($data, ['ccid' => "/^\d+$/"]);

            $data['cid'] = $cid;
            $data['cname'] = $app->Sneaker->cname;
            if($data['ccid'] != -1){
                $data['ccname'] = $my_model->get_name_by_id('o_company', $data['ccid']);
            }
            $data['settle_type'] = 1;
            Power::set_oper($data);
            $data['status'] = 1;

            //生成结算单
            $res = $my_model -> my_create($data);

            success(['id'=>$res]);
            break;

        case 'check':

            Power::set_oper($data, 'cuid', 'cuname');

            if(isset($id)) {
                init_log_oper($action, '修改并审核客户结算单');
                param_need($data, ['stock_list']); //必选

                //检测是否有权限，状态等
                $my_model->my_power($id, 1);

                $my_model -> my_check($data, 'update');
            }
            else{
                init_log_oper($action, '新建并审核客户结算单');

                param_need($data, ['ccid','stock_list']); //必选
                //param_check($data, ['ccid' => "/^\d+$/"]);

                $data['cid'] = $cid;
                $data['cname'] = $app->Sneaker->cname;
                if($data['ccid'] != -1){
                    $data['ccname'] = $my_model->get_name_by_id('o_company', $data['ccid']);
                }
                $data['settle_type'] = 1;
                Power::set_oper($data);

                $id = $my_model -> my_check($data, 'create');

            }
            success(['id'=>$id]);
            break;

        case 'read':
            if(isset($id)) {
                //init_log_oper($action, '读取客户结算单详情');

                //检测是否有权限，状态等
                $my_model->my_power($id, 0);
                $res = $my_model -> my_read();
                success($res[0]);
            }
            else{
                //init_log_oper($action, '读取客户结算单列表');

                param_check($data, ['ccid,status' => "/^\d+$/"]);
                $data['cid'] = $cid;
                $res = $my_model -> read_list($data);
                success($res);
            }
            break;
        case 'read_detail':
            if (!is_numeric($id)) error(1100);

            //检测是否有权限，状态等
            $my_model->my_power($id, 0);

            $res = $my_model->my_read_detail();
            success($res[0]);
            break;

        case 'read_stock':
            //init_log_oper($action, '获取出入库单据列表');
            param_check($data, ['ccid,sid' => "/^\d+$/"]);

            $res = $my_model->read_stock($data);
            success($res);
            break;

        case 'flush':
            init_log_oper($action, '冲正客户结算单');
            if(!isset($id)) {
                error(1100);
            }

            //检测是否有权限，状态等
            $res = $my_model->my_power($id, 2);
            if(in_array($res['pay_type'],[1,2,6])){
                error(3407);
            }

            $my_model->my_flush();
            success();
            break;

        case 'delete':
            init_log_oper($action, '取消客户结算单');
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
