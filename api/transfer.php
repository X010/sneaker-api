<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * transfer 调拨单管理
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} transfer/create 生成调拨单
 * @apiName transfer/create
 * @apiGroup Transfer
 * @apiVersion 0.0.1
 * @apiDescription 生成一条未审核的调拨单
 *
 * @apiParam {int} in_sid *调入仓库ID
 * @apiParam {int} out_sid *调出仓库ID
 * @apiParam {string} name_do 经办人名字
 * @apiParam {string} memo 调拨单备注
 * @apiParam {json} goods_list *调拨单商品清单
 * @apiParam {json} - goods_list字段详情
 * @apiParam {string} gid *商品id
 * @apiParam {int} total *商品数量
 * @apiParam {int} unit_price *商品单价
 *
 * @apiSuccess {string} id 调拨单号 
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
 * @api {post} transfer/update/:id 更新调拨单信息
 * @apiName transfer/update/id
 * @apiGroup Transfer
 * @apiVersion 0.0.1
 * @apiDescription 更新调拨单
 *
 * @apiParam {string} name_do 经办人名字
 * @apiParam {string} memo *调拨单备注
 * @apiParam {json} goods_list *调拨单商品清单
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
 * @api {post} transfer/check 创建并审核调拨单
 * @apiName transfer/check
 * @apiGroup Transfer
 * @apiVersion 0.0.1
 * @apiDescription 创建并审核调拨单
 *
 * @apiParam {int} in_sid *调入仓库ID
 * @apiParam {int} out_sid *调出仓库ID
 * @apiParam {string} name_do 经办人名字
 * @apiParam {string} memo 调拨单备注
 * @apiParam {json} goods_list *调拨单商品清单
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
 * @api {post} transfer/check/:id 修改并审核调拨单
 * @apiName transfer/check/id
 * @apiGroup Transfer
 * @apiVersion 0.0.1
 * @apiDescription 修改并审核调拨单
 *
 * @apiParam {string} name_do 经办人名字
 * @apiParam {string} memo 调拨单备注
 * @apiParam {json} goods_list 调拨单商品清单
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
 * @api {post} transfer/receive 创建并审核并入库调拨单
 * @apiName transfer/receive
 * @apiGroup Transfer
 * @apiVersion 0.0.1
 * @apiDescription 创建并审核并入库调拨单
 *
 * @apiParam {int} in_sid *调入仓库ID
 * @apiParam {int} out_sid *调出仓库ID
 * @apiParam {string} name_do 经办人名字
 * @apiParam {string} memo 调拨单备注
 * @apiParam {json} goods_list *调拨单商品清单
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
 * @api {post} transfer/receive/:id 修改并入库调拨单
 * @apiName transfer/receive/id
 * @apiGroup Transfer
 * @apiVersion 0.0.1
 * @apiDescription 修改并入库调拨单
 *
 * @apiParam {string} name_do 经办人名字
 * @apiParam {string} memo 调拨单备注
 * @apiParam {json} goods_list 调拨单商品清单
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
 * @api {post} transfer/delete/:id 取消调拨单
 * @apiName transfer/delete/id
 * @apiGroup Transfer
 * @apiVersion 0.0.1
 * @apiDescription 取消调拨单
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
 * @api {post} transfer/read/:id 查询调拨单详情
 * @apiName transfer/read/id
 * @apiGroup Transfer
 * @apiVersion 0.0.1
 * @apiDescription 查询调拨单详情
 *
 * @apiParam {int} status 查询状态 1-未审核 2-已发货 3-已收货
 *
 * @apiSuccess {string} id 调拨单号
 * @apiSuccess {string} order_id 订单号
 * @apiSuccess {int} cid 公司ID
 * @apiSuccess {string} cname 公司名称
 * @apiSuccess {int} sid 仓库ID
 * @apiSuccess {string} sname 仓库名称
 * @apiSuccess {int} status 调拨单状态 1-未审核 2-已审核 3-已复核
 * @apiSuccess {int} type 调拨单类型 1-采购 2-退货 3-调拨 4-报溢
 * @apiSuccess {string} memo 调拨单备注
 * @apiSuccess {string} createtime 创建时间
 * @apiSuccess {string} updatetime 上次更新时间
 * @apiSuccess {int} uid 填单人ID
 * @apiSuccess {string} uname 填单人姓名
 * @apiSuccess {int} cuid 审核员ID
 * @apiSuccess {string} cuname 审核员姓名
 * @apiSuccess {int} ruid 复核员ID
 * @apiSuccess {string} runame 复核员姓名
 * @apiSuccess {string} name_do 经办人名字
 * @apiSuccess {json} goods_list 商品清单
 * @apiSuccess {json} - goods_list详细列表
 * @apiSuccess {int} id 商品清单ID
 * @apiSuccess {int} stock_in_id 调拨单号
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
 * @api {post} transfer/read 浏览调拨单列表
 * @apiName transfer/read
 * @apiGroup Transfer
 * @apiVersion 0.0.1
 * @apiDescription 浏览调拨单列表，列表字段详情参照“查询调拨单详情”接口
 *
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 */

function transfer($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $order_model = new Order($id);
    $my_model = new Transfer($id);
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    $bill_type = 3;

    switch($action){
        case 'create':
            init_log_oper($action, '生成调拨单');
            param_need($data, ['in_sid','out_sid','goods_list']);
            param_check($data, ['in_sid,out_sid' => "/^\d+$/"]);

            if($data['in_sid'] == $data['out_sid']){
                error(3106);
            }

            //补充默认信息
            $data['in_cid'] = $data['out_cid'] = $cid;
            $data['in_cname'] = $data['out_cname'] = $app->Sneaker->cname;
            $data['type'] = $bill_type;

            //补充仓库name
            $data['in_sname'] = $my_model->get_name_by_id('o_store', $data['in_sid']);
            $data['out_sname'] = $my_model->get_name_by_id('o_store', $data['out_sid']);

            $data['status'] = 1;           
            Power::set_oper($data);
            
            $id = $order_model -> add($data);
            success(['id' => $id]);
            break;

//        case 'update':
//            init_log_oper($action, '修改调拨单');
//            if (!is_numeric($id)) error(1100);
//
//            param_check($data, ['in_sid,out_sid' => "/^\d+$/"]);
//            if (!$data) error(1101);
//
//            if($data['in_sid'] == $data['out_sid']){
//                error(3106);
//            }
//
//            Power::set_oper($data);
//            unset($data['status']); //防止通过普通修改接口进行审核
//
//            //检查订单信息：1-订单是否属于本机构 2-订单状态是否未审核 3-订单类型是否采购订单
//            $order_model->my_power($id, 1, $bill_type);
//
//            $order_id = $order_model->modify($data);
//            if (!$order_id) error(9903);
//
//            //返回参数
//            $ret = [
//                'id' => $order_id,
//            ];
//            success($ret);
//            break;

        case 'read':
            if (isset($id)){ //获取单个订单
                //init_log_oper('read', '查看调拨订单详情');
                if (!is_numeric($id)) error(1100);

                if(get_value($data, 'transfer_type') == 'out'){
                    $res = $order_model->view('out');
                }
                else{
                    $res = $order_model->view();
                }
            }else{ //获取列表
                //init_log_oper('read', '浏览调拨订单列表');
                //只搜当前用户cid和sid的订单

                param_check($data, [
                    'page,page_num' => "/^\d+$/", 
                ]);
                //状态判断，默认为未审核 2为已审核未入库 3为已审核已入库 4为已审核（不管是否入库）
                //注：审核的时候自动出库
                $status = get_value($data, 'status');
                if($status == 1){
                    if(!get_value($data, 'out_sid')){
                        Power::set_my_sids($data, 'out_cid', 'out_sid');
                    }
                    //$data['status'] = 1;
                }
                elseif($status == 2){
                    if(!get_value($data, 'in_sid')){
                        Power::set_my_sids($data, 'in_cid', 'in_sid');
                    }
                    $data['iuid'] = 'null';
                }
                elseif($status == 3){
                    if(!get_value($data, 'in_sid')){
                        Power::set_my_sids($data, 'in_cid', 'in_sid');
                    }
                    $data['status'] = 2;
                    $data['iuid[!]'] = 'null';
                }
                elseif($status == 4){
                    if(!get_value($data, 'out_sid')){
                        Power::set_my_sids($data, 'out_cid', 'out_sid');
                    }
                    $data['status'] = 2;
                }
                else{
                    if(!get_value($data, 'out_sid')){
                        Power::set_my_sids($data, 'out_cid', 'out_sid');
                    }
                }

                $data['type'] = $bill_type; //调拨订单
                $res = $order_model->view_list($data, '*');
            }
            success($res);
            break;
        case 'check':
            init_log_oper($action, '审核调拨单');
            Power::set_oper($data, 'cuid', 'cuname');
            $data['status'] = 2;
            $data['checktime'] = date('Y-m-d H:i:s');
            
            if(isset($id)){
                //修改后审核
                //检查订单信息：1-订单是否属于本机构 2-订单状态是否未审核 3-订单类型是否调拨订单
                param_need($data, ['in_sid','out_sid']);
                param_check($data, ['in_sid,out_sid' => "/^\d+$/"]);

                $order_model->my_power($id, 1, $bill_type);
                $my_model -> my_check($data, 'update');
                
            }
            else{
                //保存并审核
                param_need($data, ['in_sid','out_sid','goods_list']);
                param_check($data, ['in_sid,out_sid' => "/^\d+$/"]);

                if($data['in_sid'] == $data['out_sid']){
                    error(3106);
                }

                //读取订单中的信息，判断该订单是否属于本商家，判断订单类型，并且判断订单状态是否已审核或已发货
                //补充默认信息
                $data['in_cid'] = $data['out_cid'] = $cid;
                $data['in_cname'] = $data['out_cname'] = $app->Sneaker->cname;
                $data['type'] = $bill_type;

                //补充仓库name
                $data['in_sname'] = $my_model->get_name_by_id('o_store', $data['in_sid']);
                $data['out_sname'] = $my_model->get_name_by_id('o_store', $data['out_sid']);

                Power::set_oper($data, 'uid', 'uname');
                $id = $my_model -> my_check($data, 'create');

            }
            success(['id' => $id]);
            break;

        case 'receive':
            init_log_oper($action, '收货调拨单');
            Power::set_oper($data, 'cuid', 'cuname');
            $data['status'] = 2;
            $data['checktime'] = date('Y-m-d H:i:s');
            
            if(isset($id)){
                param_need($data, ['in_sid','out_sid']);
                param_check($data, ['in_sid,out_sid' => "/^\d+$/"]);

                //修改后审核
                //检查订单信息：1-订单是否属于本机构 2-订单状态是否未审核 3-订单类型是否调拨订单
                $order_model->my_power($id, 0, $bill_type);
                $my_model -> my_receive($data, 'update');
                
            }
            else{
                //保存并审核
                param_need($data, ['in_sid','out_sid','goods_list']);
                param_check($data, ['in_sid,out_sid' => "/^\d+$/"]);
                if($data['in_sid'] == $data['out_sid']){
                    error(3106);
                }
                //读取订单中的信息，判断该订单是否属于本商家，判断订单类型，并且判断订单状态是否已审核或已发货
                //补充默认信息
                $data['in_cid'] = $data['out_cid'] = $cid;
                $data['in_cname'] = $data['out_cname'] = $app->Sneaker->cname;
                $data['type'] = $bill_type;

                //补充仓库name
                $data['in_sname'] = $my_model->get_name_by_id('o_store', $data['in_sid']);
                $data['out_sname'] = $my_model->get_name_by_id('o_store', $data['out_sid']);

                Power::set_oper($data, 'uid', 'uname');
                $id = $my_model -> my_receive($data, 'create');

            }
            success(['id' => $id]);
            break;

        case 'delete':
            init_log_oper($action, '取消调拨单');
            if (!is_numeric($id)) error(1100);

            //检查订单信息：1-订单是否属于本机构 2-订单状态是否未审核 3-订单类型是否采购订单
            $order_model->my_power($id, 1, $bill_type);
            $data = ['status'=>9];
            Power::set_oper($data, 'cuid', 'cuname');
            $order_id = $order_model->modify($data); //修改订单
            //返回参数
            $ret = [
                'id' => $order_id, //为0时说明没有修改
            ];
            success($ret);
            break;

        case 'flush':
            //冲正单据
            init_log_oper($action, '冲正调出单');
            if(!is_numeric($id)){
                error(1100);
            }
            $so_model = new StockOut();
            $so_res = $so_model->read_one([
                'order_id'=>$id
            ]);
            if($so_res){
                $soid = $so_res['id'];
                $so_model->set_id($soid);
                $so_model->my_power($soid, 3, 0);
                $so_model->my_flush();
            }
            else{
                error(3109);
            }

            $data = ['status'=>9];
            Power::set_oper($data, 'cuid', 'cuname');
            $order_id = $order_model->modify($data); //修改订单

            success([
                'id' => $order_id,
            ]);
            break;

        default:
            error(1100);
    }

}
