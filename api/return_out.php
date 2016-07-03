<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * return_out 退货单退出管理
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */


/**
 * @api {post} return_out/create 生成退货单
 * @apiName return_out/create
 * @apiGroup ReturnOut
 * @apiVersion 0.0.1
 * @apiDescription 生成一条未审核的退货单
 *
 * @apiParam {int} in_sid *已放仓库ID
 * @apiParam {int} out_cid *供应商ID
 * @apiParam {string} name_do 经办人名字
 * @apiParam {string} memo 退货单备注
 * @apiParam {json} goods_list *退货单商品清单
 * @apiParam {json} - goods_list字段详情
 * @apiParam {string} gid *商品id
 * @apiParam {int} total *商品数量
 * @apiParam {int} unit_price *商品单价
 * @apiParam {int} reserveid *库存ID
 *
 * @apiSuccess {string} id 退货单号 
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
 * @api {post} return_out/update/:id 更新退货单信息
 * @apiName return_out/update/id
 * @apiGroup ReturnOut
 * @apiVersion 0.0.1
 * @apiDescription 更新退货单
 *
 * @apiParam {string} name_do 经办人名字
 * @apiParam {string} memo *退货单备注
 * @apiParam {json} goods_list *退货单商品清单
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
 * @api {post} return_out/check 创建并审核退货单
 * @apiName return_out/check
 * @apiGroup ReturnOut
 * @apiVersion 0.0.1
 * @apiDescription 创建并审核退货单
 *
 * @apiParam {int} in_sid *已放仓库ID
 * @apiParam {int} out_cid *供应商ID
 * @apiParam {string} name_do 经办人名字
 * @apiParam {string} memo 退货单备注
 * @apiParam {json} goods_list *退货单商品清单
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
 * @api {post} return_out/check/:id 修改并审核退货单
 * @apiName return_out/check/id
 * @apiGroup ReturnOut
 * @apiVersion 0.0.1
 * @apiDescription 修改并审核退货单
 *
 * @apiParam {string} memo 退货单备注
 * @apiParam {string} name_do 经办人名字
 * @apiParam {json} goods_list 退货单商品清单
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
 * @api {post} return_out/delete/:id 取消退货单
 * @apiName return_out/delete/id
 * @apiGroup ReturnOut
 * @apiVersion 0.0.1
 * @apiDescription 取消退货单
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
 * @api {post} return_out/read_in/:id 查询已方退货单详情
 * @apiName return_out/read_in/id
 * @apiGroup ReturnOut
 * @apiVersion 0.0.1
 * @apiDescription 查询退货单详情
 *
 * @apiSuccess {string} id 退货单号
 * @apiSuccess {string} order_id 订单号
 * @apiSuccess {int} cid 公司ID
 * @apiSuccess {string} cname 公司名称
 * @apiSuccess {int} sid 仓库ID
 * @apiSuccess {string} sname 仓库名称
 * @apiSuccess {int} status 退货单状态 1-未审核 2-已审核 3-已复核
 * @apiSuccess {int} type 退货单类型 1-采购 2-退货 3-退货 4-退货
 * @apiSuccess {string} memo 退货单备注
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
 * @apiSuccess {int} stock_in_id 退货单号
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
 * @api {post} return_out/read_out/:id 查询对方退货单详情
 * @apiName return_out/read_out/id
 * @apiGroup ReturnOut
 * @apiVersion 0.0.1
 * @apiDescription 查询退货单详情
 *
 * @apiSuccess {string} id 退货单号
 * @apiSuccess {string} order_id 订单号
 * @apiSuccess {int} cid 公司ID
 * @apiSuccess {string} cname 公司名称
 * @apiSuccess {int} sid 仓库ID
 * @apiSuccess {string} sname 仓库名称
 * @apiSuccess {int} status 退货单状态 1-未审核 2-已审核 3-已复核
 * @apiSuccess {int} type 退货单类型 1-采购 2-退货 3-退货 4-退货
 * @apiSuccess {string} memo 退货单备注
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
 * @apiSuccess {int} stock_in_id 退货单号
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
 * @api {post} return_out/read_in 浏览已方退货单列表
 * @apiName return_out/read_in
 * @apiGroup ReturnOut
 * @apiVersion 0.0.1
 * @apiDescription 浏览退货单列表，列表字段详情参照“查询退货单详情”接口
 *
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 */


/**
 * @api {post} return_out/read_out 浏览对方退货单列表
 * @apiName return_out/read_out
 * @apiGroup ReturnOut
 * @apiVersion 0.0.1
 * @apiDescription 浏览退货单列表，列表字段详情参照“查询退货单详情”接口
 *
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 */

function return_out($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $o_model = new Order($id);    
    $r_model = new Breturn($id);
    $my_model = new StockOut($id);
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    $bill_type = 2;
    switch($action){
        case 'create':
            init_log_oper($action, '生成退货单');
            param_need($data, ['out_sid','in_cid','suid','goods_list']); //必选
            param_check($data, ['out_sid,in_cid,suid,rank' => "/^\d+$/"]);

            //查看用户是否有订单中出库仓库的权限
            Power::check_my_sid($data['out_sid']);

            $data['cid'] = $cid;
            $data['cname'] = $app->Sneaker->cname;
            $data['sid'] = $data['out_sid'];
            $data['sname'] = $o_model->get_name_by_id('o_store', $data['out_sid']);

            $data['in_cname'] = $o_model->get_name_by_id('o_company', $data['in_cid']);
            $data['suname'] = $o_model->get_name_by_id('o_user', $data['suid']);

            $data['type'] = $bill_type; //退货出库单
            $data['status'] = 2;    //自动预审

            //如果从来没有进过货，是无法进行退货的
            $si_model = new StockIn();
            $si_res = $si_model->has([
                'cid'=>$data['cid'],
                'out_cid'=>$data['in_cid'],
                'status'=>[2,3]
            ]);
            if(!$si_res){
                error(3012);
            }

            Power::set_oper($data);
            $id = $my_model->my_create($data);
            if (!$id) error(9902);

            //返回参数
            $ret = [
                'id' => $id,
            ];
            success($ret);
            break;

        case 'update':
            init_log_oper($action, '修改退货出库单');
            if(!is_numeric($id)){
                error(1100);
            }

            //读取出库单中的信息，判断是否属于本商家，判断类型，并且判断状态是否未审核
            $my_model->my_power($id, 0, $bill_type);

            //写入操作员姓名和id
            Power::set_oper($data);
            unset($data['status']);

            if(get_value($data, 'suid')){
                $data['suname'] = $my_model->get_name_by_id('o_user', $data['suid']);
            }
            $my_model -> my_update($data);
            success();
            break;

        case 'read_out':
            if (isset($id)){ //获取单个订单
                //init_log_oper('read', '查看退货单详情');
                if (!is_numeric($id)) error(1100);
                $my_model->my_power($id, 0, $bill_type);
                $res = $my_model->my_read();
                $res = $res[0];
            }else{ //获取列表
                //init_log_oper('read', '浏览退货单列表');
                param_check($data, [
                    'page,page_num' => "/^\d+$/", 
                ]);

                //只搜当前用户cid和sid的订单
                Power::set_my_sids($data);
                $gid =get_value($data, 'gid');
                if($gid){
                    $data['gids[~]'] = "%$gid%";
                }
                $data['type'] = $bill_type; //退货订单
                $res = $my_model->read_list($data);
            }
            success($res);
            break;

        case 'read_in':
            if (isset($id)){
                //init_log_oper('read', '查看退货单接收详情');
                if (!is_numeric($id)) error(1100);
                $res = $o_model->view();
            }else{ //获取列表
                //init_log_oper('read', '浏览退货单接收列表');
                param_check($data, [
                    'page,page_num' => "/^\d+$/", 
                ]);
                $data['in_cid'] = $cid;
                $data['iuid'] = 'null';
                $data['status'] = 2; //只搜已审核状态的
                $data['type'] = $bill_type; //退货订单
                $gid =get_value($data, 'gid');
                if($gid){
                    $data['gids[~]'] = "%$gid%";
                }
                $res = $o_model->view_list($data, '*');
            }
            success($res);
            break;

        case 'check': //创建并审核、修改并审核
            if (!$data) error(1101);
            Power::set_oper($data, 'cuid', 'cuname');
            $data['status'] = 4; //已审核
            $data['checktime'] = date('Y-m-d H:i:s');

            if ($id && is_numeric($id)){
                init_log_oper('check', '修改并审核退货单'); 
                param_check($data, ['out_sid,in_cid,rank' => "/^\d+$/"]);

                $my_model->my_power($id, 2, $bill_type);

                if(get_value($data, 'suid')){
                    $data['suname'] = $my_model->get_name_by_id('o_user', $data['suid']);
                }

                if(get_value($data, 'out_sid')){
                    $data['sid'] = $data['out_sid'];
                    $data['sname'] = $my_model->get_name_by_id('o_store', $data['out_sid']);
                }

                $id = $r_model->my_check($data, 'update'); //修改并审核退货单
            }
            else{
                init_log_oper('check', '创建并审核退货单');
                param_need($data, ['out_sid','in_cid','suid','goods_list']); //必选
                param_check($data, ['out_sid,in_cid,suid,rank' => "/^\d+$/"]);

                //查看用户是否有订单中出库仓库的权限
                Power::check_my_sid($data['out_sid']);

                $data['cid'] = $cid;
                $data['cname'] = $app->Sneaker->cname;
                $data['sid'] = $data['out_sid'];
                $data['sname'] = $o_model->get_name_by_id('o_store', $data['out_sid']);

                $data['in_cname'] = $o_model->get_name_by_id('o_company', $data['in_cid']);
                $data['suname'] = $o_model->get_name_by_id('o_user', $data['suid']);

                $data['type'] = $bill_type; //退货出库单

                //如果从来没有进过货，是无法进行退货的
                $si_model = new StockIn();
                $si_res = $si_model->has([
                    'cid'=>$data['cid'],
                    'out_cid'=>$data['in_cid'],
                    'status'=>[2,3]
                ]);
                if(!$si_res){
                    error(3012);
                }

                Power::set_oper($data);

                $id = $r_model->my_check($data, 'create'); //创建并审核退货单
            }
            //返回参数
            $ret = [
                'id' => $id, //为0时说明没有修改
            ];
            success($ret);
            break;

        case 'delete':
            init_log_oper('delete', '退货单取消'); //审核不通过
            if (!is_numeric($id)) error(1100);

            $my_model->my_power($id, 0, $bill_type);
            
            $my_model->my_delete(); //修改订单
            //返回参数
            $ret = [
                'id' => $id, //为0时说明没有修改
            ];
            success($ret);
            break;

        case 'flush':
            //冲正单据
            init_log_oper($action, '冲正退货单');
            if(!is_numeric($id)){
                error(1100);
            }

            //读取出库单中的信息，判断是否属于本商家，判断类型，并且判断状态是否未审核
            $my_model->my_power($id, 4, $bill_type);

            $my_model -> my_flush();
            success();
            break;

        case 'repaire':
            //修正单据
            init_log_oper($action, '修正退货单');
            if(!is_numeric($id)){
                error(1100);
            }

            param_need($data, ['goods_list']);

            //读取出库单中的信息，判断是否属于本商家，判断类型，并且判断状态是否未审核
            $my_model->my_power($id, 4, $bill_type);

            $res = $my_model->my_repaire($data);
            success(['id' => $res]);
            break;

        default:
            error(1100);
    }

}
