<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * stock_out 出库单管理
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} stock_out/precreate 预生成出库单（已停用）
 * @apiName stock_out/precreate
 * @apiGroup StockOut
 * @apiVersion 0.0.1
 * @apiDescription 预生成出库单，检查不一致的商品和价格
 *
 * @apiParam {string} order_id *订单号
 * @apiParam {string} orderdate 下单日期
 * @apiParam {int} out_sid *出库仓库ID
 * @apiParam {string} name_do 经办人名字
 * @apiParam {string} memo *出库单备注
 * @apiParam {json} goods_list *出库单商品清单
 * @apiParam {json} - goods_list字段详情
 * @apiParam {string} gid *商品id
 * @apiParam {int} total *商品数量
 * @apiParam {int} unit_price *商品单价
 *
 * @apiSuccess {array} ne_goods 不一致的商品ID列表
 * @apiSuccess {array} ne_price 不一致的价格列表
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *         "err": 0,
 *         "status": "0000",
 *         "msg": {
 *             "ne_goods": {
 *                  "1","2","3"
 *             },
 *             "ne_price": {
 *                  "4":"2.00"
 *             }
 *         }
 *     }
 *
 */

/**
 * @api {post} stock_out/create 生成出库单（已停用）
 * @apiName stock_out/create
 * @apiGroup StockOut
 * @apiVersion 0.0.1
 * @apiDescription 生成一条未审核的出库单
 *
 * @apiParam {string} order_id *订单号
 * @apiParam {string} orderdate 下单日期
 * @apiParam {int} out_sid *出库仓库ID
 * @apiParam {string} name_do 经办人名字
 * @apiParam {string} memo *出库单备注
 * @apiParam {json} goods_list *出库单商品清单
 * @apiParam {json} - goods_list字段详情
 * @apiParam {string} gid *商品id
 * @apiParam {int} total *商品数量
 * @apiParam {int} unit_price *商品单价
 *
 * @apiSuccess {string} id 出库单号
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *         "err": 0,
 *         "status": "0000",
 *         "msg": {
 *             "id": "123456789"
 *         }
 *     }
 *
 */

/**
 * @api {post} stock_out/update/:id 更新出库单信息
 * @apiName stock_out/update/id
 * @apiGroup StockOut
 * @apiVersion 0.0.1
 * @apiDescription 更新出库单
 *
 * @apiParam {string} memo *出库单备注
 * @apiParam {string} name_do 经办人名字
 * @apiParam {json} goods_list *出库单商品清单
 * @apiParam {json} - goods_list字段详情
 * @apiParam {string} gid *商品id
 * @apiParam {int} total *商品数量
 * @apiParam {int} unit_price *商品单价
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
 * @api {post} stock_out/precheck 创建并预审出库单
 * @apiName stock_out/precheck
 * @apiGroup StockOut
 * @apiVersion 0.0.1
 * @apiDescription 创建并预审出库单
 *
 * @apiParam {string} order_id *订单号
 * @apiParam {string} orderdate 下单日期
 * @apiParam {string} name_do 经办人名字
 * @apiParam {string} memo *出库单备注
 * @apiParam {json} goods_list *出库单商品清单
 * @apiParam {json} - goods_list字段详情
 * @apiParam {string} gid *商品id
 * @apiParam {int} total *商品数量
 * @apiParam {int} unit_price *商品单价
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
 * @api {post} stock_out/precheck/:id 修改并预审出库单
 * @apiName stock_out/precheck/id
 * @apiGroup StockOut
 * @apiVersion 0.0.1
 * @apiDescription 修改并预审出库单
 *
 * @apiParam {string} memo 出库单备注
 * @apiParam {string} name_do 经办人名字
 * @apiParam {json} goods_list 出库单商品清单
 * @apiParam {json} - goods_list字段详情
 * @apiParam {string} gid *商品id
 * @apiParam {int} total *商品数量
 * @apiParam {int} unit_price *商品单价
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
 * @api {post} stock_out/check 创建并审核出库单
 * @apiName stock_out/check
 * @apiGroup StockOut
 * @apiVersion 0.0.1
 * @apiDescription 创建并审核出库单
 *
 * @apiParam {string} order_id *订单号
 * @apiParam {string} orderdate 下单日期
 * @apiParam {string} name_do 经办人名字
 * @apiParam {string} memo *出库单备注
 * @apiParam {json} goods_list *出库单商品清单
 * @apiParam {json} - goods_list字段详情
 * @apiParam {string} gid *商品id
 * @apiParam {int} total *商品数量
 * @apiParam {int} unit_price *商品单价
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
 * @api {post} stock_out/check/:id 修改并审核出库单
 * @apiName stock_out/check/id
 * @apiGroup StockOut
 * @apiVersion 0.0.1
 * @apiDescription 修改并审核出库单
 *
 * @apiParam {string} memo 出库单备注
 * @apiParam {string} name_do 经办人名字
 * @apiParam {json} goods_list 出库单商品清单
 * @apiParam {json} - goods_list字段详情
 * @apiParam {string} gid *商品id
 * @apiParam {int} total *商品数量
 * @apiParam {int} unit_price *商品单价
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
 * @api {post} stock_out/delete/:id 取消出库单
 * @apiName stock_out/delete/id
 * @apiGroup StockOut
 * @apiVersion 0.0.1
 * @apiDescription 取消出库单
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
 * @api {post} stock_out/read/:id 查询出库单详情
 * @apiName stock_out/read/id
 * @apiGroup StockOut
 * @apiVersion 0.0.1
 * @apiDescription 查询出库单详情
 *
 * @apiSuccess {string} id 出库单号
 * @apiSuccess {string} order_id 订单号
 * @apiSuccess {int} cid 公司ID
 * @apiSuccess {string} cname 公司名称
 * @apiSuccess {int} sid 仓库ID
 * @apiSuccess {string} sname 仓库名称
 * @apiSuccess {int} status 出库单状态 1-未审核 2-已审核 3-已复核
 * @apiSuccess {int} type 出库单类型 1-采购 2-退货 3-调拨 4-报溢
 * @apiSuccess {string} memo 出库单备注
 * @apiSuccess {string} createtime 创建时间
 * @apiSuccess {string} updatetime 上次更新时间
 * @apiSuccess {int} uid 填单人ID
 * @apiSuccess {string} uname 填单人姓名
 * @apiSuccess {int} cuid 审核员ID
 * @apiSuccess {string} cuname 审核员姓名
 * @apiSuccess {int} ruid 复核员ID
 * @apiSuccess {string} runame 复核员姓名
 * @apiSuccess {string} name_do 经办人名字
 * @apiSuccess {string} amount 入库单总金额
 * @apiSuccess {string} in_cname 客户名称
 * @apiSuccess {int} in_cid 客户公司名称
 * @apiSuccess {int} in_sid 客户仓库名称
 * @apiSuccess {json} goods_list 商品清单
 * @apiSuccess {json} - goods_list详细列表
 * @apiSuccess {int} id 商品清单ID
 * @apiSuccess {int} stock_out_id 出库单号
 * @apiSuccess {string} gid 商品ID
 * @apiSuccess {string} gcode 商品CODE
 * @apiSuccess {string} total 商品数量
 * @apiSuccess {int} unit_price 商品单价
 * @apiSuccess {string} amount_price 商品总价
 * @apiSuccess {int} gname 商品名称
 * @apiSuccess {string} gspec 商品规格
 *
 */

/**
 * @api {post} stock_out/read 浏览出库单列表
 * @apiName stock_out/read
 * @apiGroup StockOut
 * @apiVersion 0.0.1
 * @apiDescription 浏览出库单列表，列表字段详情参照“查询出库单详情”接口
 *
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 */

/**
 * @api {post} stock_out/flush/:id 冲正出库单
 * @apiName stock_out/flush
 * @apiGroup StockOut
 * @apiVersion 0.0.1
 * @apiDescription 冲正入库单
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
 * @api {post} stock_out/repaire/:id 修正出库单
 * @apiName stock_out/repaire/id
 * @apiGroup StockOut
 * @apiVersion 0.0.1
 * @apiDescription 修正入库单
 *
 * @apiParam {json} goods_list 入库单商品清单
 * @apiParam {json} - goods_list字段详情
 * @apiParam {string} gid *商品id
 * @apiParam {int} total *商品数量
 * @apiParam {int} unit_price *商品单价
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

function stock_out($action, $id = Null)
{
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $my_model = new StockOut($id);
    $o_model = new Order();
    $mall_model = new Mall();
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    $bill_type = 1;
    switch ($action) {
//        case 'precreate':
//            init_log_oper($action, '预生成出库单');
//            param_need($data, ['order_id','out_sid','goods_list']);
//            param_check($data, ['order_id,out_sid' => "/^\d+$/"]);
//
//            //读取订单中的信息，判断该订单是否属于本商家，判断订单类型，并且判断订单状态是否已审核或已发货
//            $order = $o_model->my_power($data['order_id'], 2, $bill_type, 'out_cid', 'ouid');
//
//            //将订单中的供货方公司和仓库信息写入出库单中
//            $data['cid'] = $order['out_cid'];
//            $data['in_cid'] = $order['in_cid'];
//            $data['sid'] = $data['out_sid'];
//            $data['type'] = $bill_type;
//            $data['status'] = 1;
//
//            $res = $my_model -> my_precreate($data);
//            success($res);
//            break;

//        case 'create':
//            init_log_oper($action, '生成出库单');
//            param_need($data, ['order_id','out_sid','suid','goods_list']);
//            param_check($data, ['order_id,out_sid,suid' => "/^\d+$/"]);
//
//            //读取订单中的信息，判断该订单是否属于本商家，判断订单类型，并且判断订单状态是否已审核或已发货
//            $order = $o_model->my_power($data['order_id'], 2, $bill_type, 'out_cid', 'ouid');
//
//            //查看用户是否有订单中出库仓库的权限
//            Power::check_my_sid($data['out_sid']);
//
//            //将订单中的供货方公司和仓库信息写入出库单中
//            $data['cid'] = $order['out_cid'];
//            $data['cname'] = $order['out_cname'];
//            $data['in_cid'] = $order['in_cid'];
//            $data['in_cname'] = $order['in_cname'];
//            $data['sid'] = $data['out_sid'];
//            $data['sname'] = $my_model->get_name_by_id('o_store', $data['out_sid']);
//            $data['suname'] = $my_model->get_name_by_id('o_user', $data['suid']);
//
//            $data['type'] = $bill_type;
//            $data['status'] = 1;
//
//            Power::set_oper($data);
//            $id = $my_model -> my_create($data);
//            success(['id' => $id]);
//            break;

        case 'update':
            init_log_oper($action, '修改出库单');
            if (!is_numeric($id)) {
                error(1100);
            }

            //读取出库单中的信息，判断是否属于本商家，判断类型，并且判断状态是否未审核
            $res = $my_model->my_power($id, 0, $bill_type, 0);
            if ($res['status'] == 4) {
                unset($data['goods_list']);
            }

            //写入操作员姓名和id
            Power::set_oper($data);
            unset($data['status']);

            if (get_value($data, 'suid')) {
                $data['suname'] = $my_model->get_name_by_id('o_user', $data['suid']);
            }
            $my_model->my_update($data);
            success();
            break;

        case 'read':
            if (isset($id)) {
                //init_log_oper($action, '读取出库单详情');
                if (!is_numeric($id)) {
                    error(1100);
                }

                //读取出库单中的信息，判断是否属于本商家，判断类型，并且判断状态是否未审核
                $my_model->my_power($id, 0, 0);

                $res = $my_model->my_read();

                success($res[0]);
            } else {
                //init_log_oper($action, '读取出库单列表');
                param_check($data, ['page' => "/^\d+$/", 'page_num' => "/^\d+$/"]);

                //默认加上公司内的单据条件
                Power::set_my_sids($data);

                $gid = get_value($data, 'gid');
                if ($gid) {
                    $data['gids[~]'] = "%$gid%";
                }

                $settle_begin_date = get_value($data, 'settle_begin_date');
                if ($settle_begin_date) {
                    $data['settletime[>=]'] = $settle_begin_date . ' 00:00:00';
                }
                $settle_end_date = get_value($data, 'settle_end_date');
                if ($settle_end_date) {
                    $data['settletime[<=]'] = $settle_end_date . '23:59:59';
                }

                $data['type'] = $bill_type;
                $res = $my_model->read_list($data);

//                if($res['count']){
//                    $in_cid_list = [];
//                    foreach($res['data'] as $val){
//                        $in_cid_list[] = $val['in_cid'];
//                    }
//                    if($in_cid_list){
//                        $c_model = new Customer();
//                    }
//
//                }

                if ($res['count']) {
                    $res['data'] = Change::go($res['data'], 'sorting_id', 'duid', 'b_sorting', 'duid');
                    $res['data'] = Change::go($res['data'], 'sorting_id', 'duname', 'b_sorting', 'duname');
                    $res['data'] = Change::go($res['data'], 'duid', 'dphone', 'o_user', 'phone');

                    foreach ($res['data'] as $key => $val) {
                        $res['data'][$key]['delay_days'] = '';
                        if (!$val['settle_status'] && $val['lastdate'] && $val['lastdate'] <= date('Y-m-d')) {
                            $res['data'][$key]['delay_days'] = days_sub(date('Y-m-d'), $val['lastdate']);
                        }
                    }

                }

                success($res);
            }
            break;
        case 'precheck':
            init_log_oper($action, '预审出库单');
            Power::set_oper($data);
            Power::set_oper($data, 'puid', 'puname');
            $data['status'] = 2;

            if (isset($id)) {
                //修改后审核
                //读取出库单中的信息，判断是否属于本商家，判断类型，并且判断状态是否未审核
                $my_model->my_power($id, 1, $bill_type);
                $my_model->my_precheck($data, 'update');
            } else {
                $order_id = get_value($data, 'order_id');
                if ($order_id) {
                    //保存并审核
                    param_need($data, ['order_id', 'out_sid', 'suid', 'goods_list']);
                    param_check($data, ['order_id,out_sid,suid' => "/^\d+$/"]);

                    //读取订单中的信息，判断该订单是否属于本商家，判断订单类型，并且判断订单状态是否已审核或已发货
                    $order = $o_model->my_power($data['order_id'], 2, $bill_type, 'out_cid', 'ouid');

                    //查看用户是否有订单中出库仓库的权限
                    Power::check_my_sid($data['out_sid']);

                    //将订单中的供货方公司和仓库信息写入出库单中
                    $data['cid'] = $order['out_cid'];
                    $data['cname'] = $order['out_cname'];
                    $data['in_cid'] = $order['in_cid'];
                    $data['in_cname'] = $order['in_cname'];
                    $data['express'] = get_value($data, 'express', 0);
                    $data['mall_orderno'] = $order['mall_orderno'];
                    $data['receipt'] = $order['receipt'];
                    $data['contacts'] = $order['contacts'];
                    $data['phone'] = $order['phone'];
                    $data['rank'] = $order['rank'];

                    if (get_value($data, 'agree_discount', 0)) {
                        $data['discount_amount'] = $order['discount_amount'];
                    }

                    //通知商城订单状态
                    if ($order['mall_orderno']) {
                        $msg = "操作员：" . $data['puname'];
                        $mall_model->notice_order($order['mall_orderno'], 4, $msg);
                    }

                } else {
                    param_need($data, ['in_cid', 'out_sid', 'suid', 'goods_list']);
                    param_check($data, ['out_sid,suid' => "/^\d+$/"]);

                    //查看用户是否有订单中出库仓库的权限
                    Power::check_my_sid($data['out_sid']);

                    if ($data['in_cid'] != -1) {
                        $data['in_cname'] = $my_model->get_name_by_id('o_company', $data['in_cid']);
                    }

                    //将订单中的供货方公司和仓库信息写入出库单中
                    $data['cid'] = $cid;
                    $data['cname'] = $app->Sneaker->cname;

                }
                $data['sid'] = $data['out_sid'];
                $data['sname'] = $my_model->get_name_by_id('o_store', $data['out_sid']);
                $data['suname'] = $my_model->get_name_by_id('o_user', $data['suid']);

                $data['type'] = $bill_type;
                $data['status'] = 2;

                Power::set_oper($data);
                $id = $my_model->my_create($data);
            }
            success(['id' => $id]);
            break;

        case 'check':
            init_log_oper($action, '审核出库单');
            Power::set_oper($data, 'cuid', 'cuname');
            $period = 0;
            $mall_orderno = False;
            $order_split_status = 1;
            if (isset($id)) {
                //修改后审核
                //读取出库单中的信息，判断是否属于本商家，判断类型，并且判断状态是否未审核
                $res = $my_model->my_power($id, [2, 3], $bill_type);

                $data['discount_amount'] = $res['discount_amount'];

                if ($res['in_cid']) {
                    //增加最终结算日，当前时间加上账期
                    $c_model = new Customer();
                    $res2 = $c_model->read_one([
                        'cid' => $cid,
                        'ccid' => $res['in_cid']
                    ]);
                    if ($res2) {
                        $period = $res2['period'];
                    }
                }
                $data['lastdate'] = date('Y-m-d', time() + 24 * 3600 * $period);

                if (get_value($data, 'suid')) {
                    $data['suname'] = $my_model->get_name_by_id('o_user', $data['suid']);
                }

                if (get_value($data, 'out_sid')) {
                    $data['sid'] = $data['out_sid'];
                    $data['sname'] = $my_model->get_name_by_id('o_store', $data['out_sid']);
                } else {
                    $data['out_sid'] = $res['sid'];
                }

                $o_res = $o_model->read_by_id($res['order_id']);
                $mall_orderno = $o_res[0]['mall_orderno'];
                $order_split_status = $o_res[0]['split_status'];
                $order_id = $res['order_id'];
                $status = $my_model->my_check($data, 'update');
            } else {
                $order_id = get_value($data, 'order_id');

                if ($order_id) {
                    //保存并审核
                    param_need($data, ['order_id', 'out_sid', 'suid', 'goods_list']);
                    param_check($data, ['order_id,out_sid,suid' => "/^\d+$/"]);

                    //读取订单中的信息，判断该订单是否属于本商家，判断订单类型，并且判断订单状态是否已审核或已发货
                    $order = $o_model->my_power($data['order_id'], 2, $bill_type, 'out_cid', 'ouid');

                    //查看用户是否有订单中出库仓库的权限
                    Power::check_my_sid($data['out_sid']);

                    //将订单中的供货方公司和仓库信息写入出库单中
                    $data['cid'] = $order['out_cid'];
                    $data['cname'] = $order['out_cname'];
                    $data['in_cid'] = $order['in_cid'];
                    $data['in_cname'] = $order['in_cname'];

                    $data['mall_orderno'] = $order['mall_orderno'];
                    $data['receipt'] = $order['receipt'];
                    $data['contacts'] = $order['contacts'];
                    $data['phone'] = $order['phone'];
                    $data['rank'] = $order['rank'];

                    $in_cid = get_value($order, 'in_cid');
                    $mall_orderno = $order['mall_orderno'];
                    $order_split_status = $order['split_status'];

                    if (get_value($data, 'agree_discount', 0)) {
                        $data['discount_amount'] = $order['discount_amount'];
                    }

                } else {
                    param_need($data, ['in_cid', 'out_sid', 'suid', 'goods_list']);
                    param_check($data, ['out_sid,suid' => "/^\d+$/"]);

                    //查看用户是否有订单中出库仓库的权限
                    Power::check_my_sid($data['out_sid']);

                    if ($data['in_cid'] != -1) {
                        $data['in_cname'] = $my_model->get_name_by_id('o_company', $data['in_cid']);
                    }

                    //将订单中的供货方公司和仓库信息写入出库单中
                    $data['cid'] = $cid;
                    $data['cname'] = $app->Sneaker->cname;
                    $in_cid = $data['in_cid'];
                }

                $data['sid'] = $data['out_sid'];

                //补充仓库name
                $data['sname'] = $my_model->get_name_by_id('o_store', $data['out_sid']);
                $data['suname'] = $my_model->get_name_by_id('o_user', $data['suid']);

                $data['type'] = $bill_type;
                Power::set_oper($data);

                if ($in_cid && $in_cid != -1) {
                    //增加最终结算日，当前时间加上账期
                    $c_model = new Customer();
                    $res2 = $c_model->read_one([
                        'cid' => $cid,
                        'ccid' => $in_cid
                    ]);
                    if ($res2) {
                        $period = $res2['period'];
                    }
                }
                $data['lastdate'] = date('Y-m-d', time() + 24 * 3600 * $period);

                $status = $my_model->my_check($data, 'create');
            }

            $stock_out_id = $my_model->get_id();

            //通知商城订单状态
            if ($mall_orderno) {
                $msg = "审核员：" . $data['cuname'];
                if ($status == 3) {
                    $mall_status = 13;
                } elseif ($status == 4) {
                    $mall_status = 5;
                    //如果是拆单，要多附加商品信息
                    if ($order_split_status == 2) {
                        $goods_msg = "<br>拆单发货商品：";
                        $split_goods_list = $app->db->select('b_stock_out_glist', '*', ['stock_out_id' => $stock_out_id]);
                        foreach ($split_goods_list as $val) {
                            $goods_msg .= "<br>" . $val['gname'] . " ×" . $val['total'];
                        }
                        //$goods_msg = rtrim($goods_msg, ',');
                        $msg .= $goods_msg;
                    }
                }

                $mall_model->notice_order($mall_orderno, $mall_status, $msg);
            }

            success([
                'id' => $stock_out_id,
                'status' => $status
            ]);
            break;

        case 'delete':
            init_log_oper($action, '取消出库单');
            if (!is_numeric($id)) {
                error(1100);
            }
            $res = $my_model->my_power($id, 0, $bill_type);
            if (!in_array($res['status'], [1, 2, 3])) {
                error(3123);
            }
            if ($res['settle_status'] == 1) {
                error(3127);
            }

            $my_model->my_delete();

            //通知商城订单状态
            if ($res['order_id']) {
                $o_res = $o_model->read_by_id($res['order_id']);
                if ($o_res && $o_res[0]['mall_orderno']) {
                    if ($app->Sneaker->uname) {
                        $msg = "操作员：" . $app->Sneaker->uname;
                    } else {
                        $msg = "";
                    }
                    $mall_model->notice_order($o_res[0]['mall_orderno'], 8, $msg);
                }
            }
            success();
            break;

        case 'flush':
            //冲正单据
            init_log_oper($action, '冲正出库单');
            if (!is_numeric($id)) {
                error(1100);
            }

            //读取出库单中的信息，判断是否属于本商家，判断类型，并且判断状态是否未审核
            $res = $my_model->my_power($id, 4, 0, 0);
            $my_model->my_flush();

            //通知商城订单状态
            if ($res['order_id']) {
                $o_res = $o_model->read_by_id($res['order_id']);
                if ($o_res && $o_res[0]['mall_orderno']) {
                    if ($app->Sneaker->uname) {
                        $msg = "操作员：" . $app->Sneaker->uname;
                    } else {
                        $msg = "";
                    }
                    $mall_model->notice_order($o_res[0]['mall_orderno'], 8, $msg);
                }
            }

            success();
            break;

        case 'repaire':
            //修正单据
            init_log_oper($action, '修正出库单');
            if (!is_numeric($id)) {
                error(1100);
            }

            param_need($data, ['goods_list', 'out_sid']);

            //读取出库单中的信息，判断是否属于本商家，判断类型，并且判断状态是否未审核
            $my_model->my_power($id, 4, 0, 0);
            $my_model->my_repaire($data);
            success([
                'id' => $my_model->get_id()
            ]);
            break;

        default:
            error(1100);
    }

}
