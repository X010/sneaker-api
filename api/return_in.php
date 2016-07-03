<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * return_in 退货单退入管理
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */


/**
 * @api {post} return_in/create 生成出库退货单
 * @apiName return_in/create
 * @apiGroup ReturnIn
 * @apiVersion 0.0.1
 * @apiDescription 生成一条未审核的出库退货单（退货入库）
 *
 * @apiParam {string} order_id *订单号
 * @apiParam {string} name_do 经办人名字
 * @apiParam {string} memo *入库单备注
 * @apiParam {json} goods_list *入库单商品清单
 * @apiParam {json} - goods_list字段详情
 * @apiParam {string} gid *商品id
 * @apiParam {int} total *商品数量
 * @apiParam {int} unit_price *商品单价
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
 * @api {post} return_in/update/:id 修改出库退货单
 * @apiName return_in/update/id
 * @apiGroup ReturnIn
 * @apiVersion 0.0.1
 * @apiDescription 更新退货入库单
 *
 * @apiParam {string} memo *入库单备注
 * @apiParam {string} name_do 经办人名字
 * @apiParam {json} goods_list *入库单商品清单
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
 * @api {post} return_in/check 创建并审核出库退货单
 * @apiName return_in/check
 * @apiGroup ReturnIn
 * @apiVersion 0.0.1
 * @apiDescription 创建并审核退货入库单
 *
 * @apiParam {string} order_id *订单号
 * @apiParam {string} name_do 经办人名字
 * @apiParam {string} memo *入库单备注
 * @apiParam {json} goods_list *入库单商品清单
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
 * @api {post} return_in/check/:id 修改并审核出库退货单
 * @apiName return_in/check/id
 * @apiGroup ReturnIn
 * @apiVersion 0.0.1
 * @apiDescription 修改并审核退货入库单
 *
 * @apiParam {string} memo 入库单备注
 * @apiParam {string} name_do 经办人名字
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

/**
 * @api {post} return_in/delete/:id 取消出库退货单
 * @apiName return_in/delete
 * @apiGroup ReturnIn
 * @apiVersion 0.0.1
 * @apiDescription 取消退货入库单
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
 * @api {post} return_in/read/:id 查询出库退货单详情
 * @apiName return_in/read/id
 * @apiGroup ReturnIn
 * @apiVersion 0.0.1
 * @apiDescription 查询退货入库单详情
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
 * @api {post} return_in/read 浏览出库退货单单列表
 * @apiName return_in/read
 * @apiGroup ReturnIn
 * @apiVersion 0.0.1
 * @apiDescription 浏览退货入库单列表，列表字段详情参照“查询入库单详情”接口
 *
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 */

function return_in($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $my_model = new StockIn($id);
    $o_model = new Order();
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    $bill_type = 2;
    switch($action){

        case 'create':
            init_log_oper($action, '生成退货入库单');
            $order_id = get_value($data, 'order_id');
            if($order_id){
                param_need($data, ['order_id','in_sid','buid','goods_list']);
                param_check($data, ['order_id,in_sid,buid' => "/^\d+$/"]);

                //读取订单中的信息，判断该订单是否属于本商家，判断订单类型，并且判断订单状态是否已审核或已发货
                $order = $o_model->my_power($data['order_id'], 2, $bill_type);

                //将订单中的收货方公司和仓库信息写入入库单中
                $data['cid'] = $order['in_cid'];
                $data['cname'] = $order['in_cname'];
                $data['out_cid'] = $order['out_cid'];
                $data['out_cname'] = $order['out_cname'];
            }
            else{
                param_need($data, ['out_cid','in_sid','buid','goods_list']);
                param_check($data, ['out_cid,in_sid,buid' => "/^\d+$/"]);

                //查看用户是否有订单中入库仓库的权限
                Power::check_my_sid($data['in_sid']);

                //将订单中的收货方公司和仓库信息写入入库单中
                $data['cid'] = $cid;
                $data['cname'] = $app->Sneaker->cname;
                $data['out_cname'] = $my_model->get_name_by_id('o_company', $data['out_cid']);
            }

            $data['sid'] = $data['in_sid'];
            $data['sname'] = $my_model->get_name_by_id('o_store', $data['in_sid']);
            $data['buname'] = $my_model->get_name_by_id('o_user', $data['buid']);
            $data['type'] = $bill_type;
            $data['status'] = 1;
            Power::set_oper($data);

            $id = $my_model -> my_create($data);
            success(['id' => $id]);
            break;

        case 'update':
            init_log_oper($action, '修改退货入库单');
            if(!is_numeric($id)){
                error(1100);
            }

            //读取入库单中的信息，判断是否属于本商家，判断类型，并且判断状态是否未审核
            $my_model -> my_power($id, 1, $bill_type);

            //写入操作员姓名和id
            Power::set_oper($data);
            unset($data['status']);

            if(get_value($data, 'buid')){
                $data['buname'] = $my_model->get_name_by_id('o_user', $data['buid']);
            }
            $my_model -> my_update($data);
            success();
            break;

        case 'read':
            if(isset($id)){
                //init_log_oper($action, '读取退货入库单详情');
                if(!is_numeric($id)){
                    error(1100);
                }

                //读取入库单中的信息，判断是否属于本商家，判断类型，并且判断状态是否未审核
                $my_model -> my_power($id, 0, $bill_type);

                //增加客户名称
                $res = $my_model -> my_read();

                success($res[0]);
            }
            else{
                //init_log_oper($action, '读取退货入库单列表');
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
            init_log_oper($action, '审核退货入库单');
            Power::set_oper($data, 'cuid', 'cuname');
            $data['status'] = 2;
            $data['checktime'] = date('Y-m-d H:i:s');

            if(isset($id)){
                //修改后审核
                //读取入库单中的信息，判断是否属于本商家，判断类型，并且判断状态是否未审核
                $res = $my_model -> my_power($id, 1, $bill_type);
                $data['out_cid'] = get_value($res, 'out_cid');
                $data['out_cname'] = get_value($res, 'out_cname');

                if(get_value($data, 'buid')){
                    $data['buname'] = $my_model->get_name_by_id('o_user', $data['buid']);
                }

                if(get_value($data, 'in_sid')){
                    $data['sid'] = $data['in_sid'];
                    $data['sname'] = $my_model->get_name_by_id('o_store', $data['in_sid']);
                }

                $my_model -> my_check($data, 'update', $bill_type);
            }
            else{
                $order_id = get_value($data, 'order_id');
                if($order_id){
                    //保存并审核
                    param_need($data, ['order_id','in_sid','buid','goods_list']);
                    param_check($data, ['order_id,in_sid,buid' => "/^\d+$/"]);

                    //读取订单中的信息，判断该订单是否属于本商家，判断订单类型，并且判断订单状态是否已审核或已发货
                    $order = $o_model->my_power($data['order_id'], 2, $bill_type);

                    //将订单中的收货方公司和仓库信息写入入库单中
                    $data['cid'] = $order['in_cid'];
                    $data['cname'] = $order['in_cname'];
                    $data['out_cid'] = $order['out_cid'];
                    $data['out_cname'] = $order['out_cname'];

                }
                else{
                    param_need($data, ['out_cid','in_sid','buid','goods_list']);
                    param_check($data, ['out_cid,in_sid,buid' => "/^\d+$/"]);

                    //查看用户是否有订单中入库仓库的权限
                    Power::check_my_sid($data['in_sid']);

                    //将订单中的收货方公司和仓库信息写入入库单中
                    $data['cid'] = $cid;
                    $data['cname'] = $app->Sneaker->cname;
                    $data['out_cname'] = $my_model->get_name_by_id('o_company', $data['out_cid']);

                }
                $data['sid'] = $data['in_sid'];
                $data['sname'] = $my_model->get_name_by_id('o_store', $data['in_sid']);
                $data['buname'] = $my_model->get_name_by_id('o_user', $data['buid']);
                $data['type'] = $bill_type;

                Power::set_oper($data);

                $id = $my_model -> my_check($data, 'create', $bill_type);
                success(['id' => $id]);
            }
            success(['id' => $id]);
            break;

        case 'delete':
            init_log_oper($action, '取消退货入库单');
            if(!is_numeric($id)){
                error(1100);
            }

            //读取入库单中的信息，判断是否属于本商家，判断类型，并且判断状态是否未审核
            $my_model -> my_power($id, 1, $bill_type);

            $my_model -> my_delete();
            success();
            break;

        case 'flush':
            //冲正单据
            init_log_oper($action, '冲正退货入库单');
            if(!is_numeric($id)){
                error(1100);
            }

            //读取出库单中的信息，判断是否属于本商家，判断类型，并且判断状态是否未审核
            $my_model->my_power($id, 2, 0);

            $my_model -> my_return_flush();
            success();
            break;

        case 'repaire':
            //修正单据
            init_log_oper($action, '修正退货入库单');
            if(!is_numeric($id)){
                error(1100);
            }

            param_need($data, ['goods_list']);

            //读取出库单中的信息，判断是否属于本商家，判断类型，并且判断状态是否未审核
            $my_model->my_power($id, 2, 0);

            $res = $my_model -> my_return_repaire($data);
            success(['id' => $res]);
            break;

        default:
            error(1100);
    }

}