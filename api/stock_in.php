<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * stock_in 入库单管理
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} stock_in/create 生成入库单
 * @apiName stock_in/create
 * @apiGroup StockIn
 * @apiVersion 0.0.1
 * @apiDescription 生成一条未审核的入库单
 *
 * @apiParam {string} order_id *订单号
 * @apiParam {string} orderdate 下单日期
 * @apiParam {string} name_do 经办人名字
 * @apiParam {string} memo *入库单备注
 * @apiParam {json} goods_list *入库单商品清单
 * @apiParam {json} - goods_list字段详情
 * @apiParam {string} gid *商品id
 * @apiParam {int} total *商品数量
 * @apiParam {int} unit_price *商品单价
 * @apiParam {string} espdate 到效日期
 *
 * @apiSuccess {string} id 入库单号
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
 * @api {post} stock_in/update/:id 更新入库单信息
 * @apiName stock_in/update/id
 * @apiGroup StockIn
 * @apiVersion 0.0.1
 * @apiDescription 更新入库单
 *
 * @apiParam {string} memo *入库单备注
 * @apiParam {string} name_do 经办人名字
 * @apiParam {json} goods_list *入库单商品清单
 * @apiParam {json} - goods_list字段详情
 * @apiParam {string} gid *商品id
 * @apiParam {int} total *商品数量
 * @apiParam {int} unit_price *商品单价
 * @apiParam {string} espdate 到效日期
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
 * @api {post} stock_in/check 创建并审核入库单
 * @apiName stock_in/check
 * @apiGroup StockIn
 * @apiVersion 0.0.1
 * @apiDescription 创建并审核入库单
 *
 * @apiParam {string} order_id *订单号
 * @apiParam {string} orderdate 下单日期
 * @apiParam {string} name_do 经办人名字
 * @apiParam {string} memo *入库单备注
 * @apiParam {json} goods_list *入库单商品清单
 * @apiParam {json} - goods_list字段详情
 * @apiParam {string} gid *商品id
 * @apiParam {int} total *商品数量
 * @apiParam {int} unit_price *商品单价
 * @apiParam {string} espdate 到效日期
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
 * @api {post} stock_in/check/:id 修改并审核入库单
 * @apiName stock_in/check/id
 * @apiGroup StockIn
 * @apiVersion 0.0.1
 * @apiDescription 修改并审核入库单
 *
 * @apiParam {string} memo 入库单备注
 * @apiParam {string} name_do 经办人名字
 * @apiParam {json} goods_list 入库单商品清单
 * @apiParam {json} - goods_list字段详情
 * @apiParam {string} gid *商品id
 * @apiParam {int} total *商品数量
 * @apiParam {int} unit_price *商品单价
 * @apiParam {string} espdate 到效日期
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
 * @api {post} stock_in/recheck/:id 修改并复核入库单
 * @apiName stock_in/recheck/id
 * @apiGroup StockIn
 * @apiVersion 0.0.1
 * @apiDescription 修改并复核入库单
 *
 * @apiParam {string} memo 入库单备注
 * @apiParam {string} name_do 经办人名字
 * @apiParam {json} goods_list 入库单商品清单
 * @apiParam {json} - goods_list字段详情
 * @apiParam {string} gid *商品id
 * @apiParam {int} total *商品数量
 * @apiParam {int} unit_price *商品单价
 * @apiParam {string} espdate 到效日期
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
 * @api {post} stock_in/delete/:id 取消入库单
 * @apiName stock_in/delete
 * @apiGroup StockIn
 * @apiVersion 0.0.1
 * @apiDescription 取消入库单
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
 * @api {post} stock_in/read/:id 查询入库单详情
 * @apiName stock_in/read/id
 * @apiGroup StockIn
 * @apiVersion 0.0.1
 * @apiDescription 查询入库单详情
 *
 * @apiSuccess {string} id 入库单号
 * @apiSuccess {string} order_id 订单号
 * @apiSuccess {int} cid 公司ID
 * @apiSuccess {string} cname 公司名称
 * @apiSuccess {int} sid 仓库ID
 * @apiSuccess {string} sname 仓库名称
 * @apiSuccess {int} status 入库单状态 1-未审核 2-已审核 3-已复核
 * @apiSuccess {int} type 入库单类型 1-采购 2-退货 3-调拨 4-报溢
 * @apiSuccess {string} memo 入库单备注
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
 * @apiSuccess {string} out_cname 供应商名称
 * @apiSuccess {json} goods_list 商品清单
 * @apiSuccess {json} - goods_list详细列表
 * @apiSuccess {int} id 商品清单ID
 * @apiSuccess {int} stock_in_id 入库单号
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
 * @api {post} stock_in/read 浏览入库单列表
 * @apiName stock_in/read
 * @apiGroup StockIn
 * @apiVersion 0.0.1
 * @apiDescription 浏览入库单列表，列表字段详情参照“查询入库单详情”接口
 *
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 */

/**
 * @api {post} stock_in/flush/:id 冲正入库单
 * @apiName stock_in/flush
 * @apiGroup StockIn
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
 * @api {post} stock_in/repaire/:id 修正入库单
 * @apiName stock_in/repaire/id
 * @apiGroup StockIn
 * @apiVersion 0.0.1
 * @apiDescription 修正入库单
 *
 * @apiParam {json} goods_list 入库单商品清单
 * @apiParam {json} - goods_list字段详情
 * @apiParam {string} gid *商品id
 * @apiParam {int} total *商品数量
 * @apiParam {int} unit_price *商品单价
 * @apiParam {string} espdate 到效日期
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

function stock_in($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $my_model = new StockIn($id);
    $o_model = new Order();
    $cg_model = new CompanyGoods();
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    $bill_type = 1;
    switch($action){

        case 'create':
            init_log_oper($action, '生成入库单');
            $order_id = get_value($data, 'order_id');
            if($order_id){
                param_need($data, ['order_id','buid','goods_list']);
                param_check($data, ['order_id,buid' => "/^\d+$/"]);

                //读取订单中的信息，判断该订单是否属于本商家，判断订单类型，并且判断订单状态是否已审核或已发货
                $order = $o_model->my_power($data['order_id'], 2, $bill_type, 'in_cid', 'iuid');

                //查看用户是否有订单中入库仓库的权限
                Power::check_my_sid($order['in_sid']);

                //将订单中的收货方公司和仓库信息写入入库单中
                $data['cid'] = $order['in_cid'];
                $data['cname'] = $order['in_cname'];

                //如果传了仓库，以传入的为准，如果没传取订单中的仓库
                if(get_value($data, 'in_sid')){
                    $data['sid'] = $data['in_sid'];
                    $data['sname'] = $my_model->get_name_by_id('o_store', $data['in_sid']);
                }
                else{
                    $data['sid'] = $order['in_sid'];
                    $data['sname'] = $order['in_sname'];
                }

                $data['out_cid'] = $order['out_cid'];
                $data['out_cname'] = $order['out_cname'];
            }
            else{
                param_need($data, ['in_sid','out_cid','buid','goods_list']);
                param_check($data, ['in_sid,out_cid,buid' => "/^\d+$/"]);

                //查看用户是否有订单中入库仓库的权限
                Power::check_my_sid($data['in_sid']);

                //将订单中的收货方公司和仓库信息写入入库单中
                $data['cid'] = $cid;
                $data['cname'] = $app->Sneaker->cname;
                $data['sid'] = $data['in_sid'];
                $data['sname'] = $my_model->get_name_by_id('o_store', $data['in_sid']);
                $data['out_cname'] = $my_model->get_name_by_id('o_company', $data['out_cid']);

            }

            $data['buname'] = $my_model->get_name_by_id('o_user', $data['buid']);
            $data['type'] = $bill_type;
            $data['status'] = 1;

            Power::set_oper($data);
            //检查商品是否包含停止采购功能的商品
            $res = $cg_model->has_limit_buy($data['goods_list'], $cid);
            if($res){
                error(3107);
            }

            $id = $my_model -> my_create($data);
            success(['id' => $id]);
            break;

        case 'update':
            init_log_oper($action, '修改入库单');
            if(!is_numeric($id)){
                error(1100);
            }

            //读取入库单中的信息，判断是否属于本商家，判断类型，并且判断状态是否未审核
            $my_model->my_power($id, 0, $bill_type);

            //写入操作员姓名和id
            Power::set_oper($data);

            unset($data['status']);
            //检查商品是否包含停止采购功能的商品
            if(get_value($data, 'goods_list')){
                $res = $cg_model->has_limit_buy($data['goods_list'], $cid);
                if($res){
                    error(3107);
                }
            }
            if(get_value($data, 'buid')){
                $data['buname'] = $my_model->get_name_by_id('o_user', $data['buid']);
            }

            $my_model -> my_update($data);
            success();
            break;

        case 'read':
            if(isset($id)){
                //init_log_oper($action, '读取入库单详情');
                if(!is_numeric($id)){
                    error(1100);
                }

                //读取入库单中的信息，判断是否属于本商家，判断类型，并且判断状态是否未审核
                $my_model->my_power($id, 0, 0);

                //增加供应商名称
                $res = $my_model -> my_read();

                $res[0]['in_sid'] = $res[0]['sid'];
                $res[0]['in_sname'] = $res[0]['sname'];

                success($res[0]);
            }
            else{
                //init_log_oper($action, '读取入库单列表');
                param_check($data, ['page' => "/^\d+$/",'page_num' => "/^\d+$/"]);

                //默认加上公司内的单据条件
                Power::set_my_sids($data);

                $gid =get_value($data, 'gid');
                if($gid){
                    $data['gids[~]'] = "%$gid%";
                }

                $data['type'] = $bill_type;
                $res = $my_model -> read_list($data);

                success($res);
            }
            break;
        case 'check':
            init_log_oper($action, '审核入库单');
            Power::set_oper($data, 'cuid', 'cuname');
            $period = 0;
            if(isset($id)){
                //修改后审核
                //读取入库单中的信息，判断是否属于本商家，判断类型，并且判断状态是否未审核
                $res = $my_model -> my_power($id, 1, $bill_type);
                if($res['out_cid']){
                    $s_model = new Supplier();
                    //增加最终结算日，当前时间加上账期
                    $res2 = $s_model->read_one([
                        'cid' => $cid,
                        'scid' => $res['out_cid']
                    ]);
                    if($res2){
                        $period = $res2['period'];
                    }
                    $data['out_cid'] = $res['out_cid'];
                    $data['out_cname'] = $res['out_cname'];
                }
                $data['lastdate'] = date('Y-m-d', time()+24*3600*$period);

                if(get_value($data, 'buid')){
                    $data['buname'] = $my_model->get_name_by_id('o_user', $data['buid']);
                }

                if(get_value($data, 'in_sid')){
                    $data['sid'] = $data['in_sid'];
                    $data['sname'] = $my_model->get_name_by_id('o_store', $data['in_sid']);
                }
                else{
                    $data['in_sid'] = $res['sid'];
                }

                //检查商品是否包含停止采购功能的商品
                $res = $cg_model->has_limit_buy($data['goods_list'], $cid);
                if($res){
                    error(3107);
                }
                $my_model -> my_check($data, 'update');
            }
            else{

                $order_id = get_value($data, 'order_id');
                //兼容传order_id 和不传 order_id 两种情况
                if($order_id){
                    //保存并审核
                    param_need($data, ['order_id','buid','goods_list']);
                    param_check($data, ['order_id,buid' => "/^\d+$/"]);

                    //读取订单中的信息，判断该订单是否属于本商家，判断订单类型，并且判断订单状态是否已审核或已发货
                    $order = $o_model->my_power($data['order_id'], 2, $bill_type, 'in_cid', 'iuid');

                    //查看用户是否有订单中入库仓库的权限
                    Power::check_my_sid($order['in_sid']);
                    //将订单中的收货方公司和仓库信息写入入库单中

                    $data['cid'] = $order['in_cid'];
                    $data['cname'] = $order['in_cname'];
                    //如果传了仓库，以传入的为准，如果没传取订单中的仓库
                    if(get_value($data, 'in_sid')){
                        $data['sid'] = $data['in_sid'];
                        $data['sname'] = $my_model->get_name_by_id('o_store', $data['in_sid']);
                    }
                    else{
                        $data['sid'] = $order['in_sid'];
                        $data['sname'] = $order['in_sname'];
                    }
                    $data['out_cid'] = get_value($order, 'out_cid');
                }
                else{
                    param_need($data, ['in_sid','out_cid','buid','goods_list']);
                    param_check($data, ['in_sid,out_cid,buid' => "/^\d+$/"]);

                    //查看用户是否有订单中入库仓库的权限
                    Power::check_my_sid($data['in_sid']);

                    //将订单中的收货方公司和仓库信息写入入库单中
                    $data['cid'] = $cid;
                    $data['cname'] = $app->Sneaker->cname;
                    $data['sid'] = $data['in_sid'];
                    $data['sname'] = $my_model->get_name_by_id('o_store', $data['in_sid']);
                }
                $data['buname'] = $my_model->get_name_by_id('o_user', $data['buid']);
                $data['type'] = 1;

                Power::set_oper($data);

                if($data['out_cid']){
                    //增加最终结算日，当前时间加上账期
                    $s_model = new Supplier();
                    $res2 = $s_model->read_one([
                        'cid' => $cid,
                        'scid' => $data['out_cid']
                    ]);
                    if($res2){
                        $period = $res2['period'];
                    }
                    $data['out_cname'] = $my_model->get_name_by_id('o_company', $data['out_cid']);
                }
                $data['lastdate'] = date('Y-m-d', time()+24*3600*$period);

                //检查商品是否包含停止采购功能的商品
                $res = $cg_model->has_limit_buy($data['goods_list'], $cid);
                if($res){
                    error(3107);
                }
                $id = $my_model -> my_check($data, 'create');

            }
            success(['id' => $id]);
            break;

        case 'delete':
            init_log_oper($action, '取消入库单');
            if(!is_numeric($id)){
                error(1100);
            }

            $my_model->my_power($id, 1, $bill_type);

            $my_model -> my_delete();
            success();
            break;

        case 'flush':
            //冲正单据
            init_log_oper($action, '冲正入库单');
            if(!is_numeric($id)){
                error(1100);
            }

            //读取出库单中的信息，判断是否属于本商家，判断类型，并且判断状态是否未审核
            $my_model->my_power($id, 2, 0);

            $my_model -> my_flush();
            success();
            break;

        case 'repaire':
            //修正单据
            init_log_oper($action, '修正入库单');
            if(!is_numeric($id)){
                error(1100);
            }

            param_need($data, ['goods_list','in_sid']);

            //读取出库单中的信息，判断是否属于本商家，判断类型，并且判断状态是否未审核
            $my_model->my_power($id, 2, 0);

            //检查商品是否包含停止采购功能的商品
            $res = $cg_model->has_limit_buy($data['goods_list'], $cid);
            if($res){
                error(3107);
            }
            $res = $my_model -> my_repaire($data);
            success(['id' => $res]);
            break;
        default:
            error(1100);
    }

}
