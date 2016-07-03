<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * order 订单管理
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} order/create 新建采购订单
 * @apiName order/create
 * @apiGroup Order
 * @apiVersion 0.0.1
 * @apiDescription 新建采购订单
 *
 * @apiParam {int} in_sid *进货仓库ID
 * @apiParam {int} out_cid *出货公司ID
 * @apiParam {int} buid 采购员ID
 * @apiParam {int} rank 紧急程度
 * @apiParam {string} memo 备注
 * @apiParam {json} goods_list 商品列表JSON
 * @apiParam {json} - goods_list字段详情
 * @apiParam {string} gid *商品id
 * @apiParam {int} total *商品数量
 * @apiParam {string} unit_price *商品单价
 * @apiParam {string} amount_price *商品总价
 *
 * @apiSuccess {int} order_id 订单号
 *
 */

/**
 * @api {post} order/update(/:id) 更新采购订单
 * @apiName order/update
 * @apiGroup Order
 * @apiVersion 0.0.1
 * @apiDescription 更新采购订单，只能更新采购员
 *
 * @apiParam {int} buid *采购员ID
 *
 * @apiSuccess {int} order_id 订单号，为0时代表没有修改
 *
 */


/**
 * @api {post} order/delete/:id 取消采购订单
 * @apiName order/delete
 * @apiGroup Order
 * @apiVersion 0.0.1
 * @apiDescription 取消采购订单（审核不通过）
 *
 * @apiSuccess {int} order_id 订单号，为0时代表没有修改
 *
 *
 */

/**
 * @api {post} order/delete_out/:id 取消客户采购订单
 * @apiName order/delete_out
 * @apiGroup Order
 * @apiVersion 0.0.1
 * @apiDescription 取消客户采购订单（审核不通过）
 *
 * @apiSuccess {int} order_id 订单号，为0时代表没有修改
 *
 *
 */

/**
 * @api {post} order/check/:id 审核采购订单
 * @apiName order/check
 * @apiGroup Order
 * @apiVersion 0.0.1
 * @apiDescription 包含：创建并审核（URI中无ID时）、修改并审核（URI中有ID时）
 *
 * @apiParam {int} in_sid *进货仓库ID
 * @apiParam {int} out_cid *出货公司ID
 * @apiParam {int} buid 采购员ID
 * @apiParam {int} rank 紧急程度
 * @apiParam {string} memo 备注
 * @apiParam {json} goods_list 商品列表JSON
 * @apiParam {json} - goods_list字段详情
 * @apiParam {string} gid *商品id
 * @apiParam {int} total *商品数量
 * @apiParam {string} unit_price *商品单价
 * @apiParam {string} amount_price *商品总价
 *
 * @apiSuccess {int} order_id 订单号
 *
 *
 */



/**
 * @api {post} order/read_in/:id 查询订单详情
 * @apiName order/read_in/id
 * @apiGroup Order
 * @apiVersion 0.0.1
 * @apiDescription 查询订单详情
 *
 * @apiSuccess {int} id 订单号
 * @apiSuccess {int} in_cid 进货公司ID
 * @apiSuccess {string} in_cname 进货公司名称
 * @apiSuccess {int} in_sid 进货仓库ID
 * @apiSuccess {string} in_sname 进货仓库名称
 * @apiSuccess {int} out_cid 出货公司ID
 * @apiSuccess {string} out_cname 出货公司名称
 * @apiSuccess {int} out_sid 出货仓库ID
 * @apiSuccess {string} out_sname 出货仓库名称
 * @apiSuccess {int} uid 填单人ID
 * @apiSuccess {string} uname 填单人名字
 * @apiSuccess {int} cuid 审核员工ID
 * @apiSuccess {string} cuname 审核员工名字
 * @apiSuccess {int} ouid 出库员工ID
 * @apiSuccess {string} ouname 出库员工名字
 * @apiSuccess {int} iuid 入库员工ID
 * @apiSuccess {string} iuname 入库员工名字
 * @apiSuccess {int} buid 采购员ID
 * @apiSuccess {string} buname 采购员工名字
 * @apiSuccess {int} type 订单类型：1-采购订单 2-调拨订单 3-退货订单
 * @apiSuccess {int} amount 订单总金额
 * @apiSuccess {int} tax_amount 订单总税额
 * @apiSuccess {int} rank 紧急程度
 * @apiSuccess {string} memo 备注
 * @apiSuccess {string} status 订单状态
 * @apiSuccess {string} mall_orderno 商城订单号
 * @apiSuccess {int} ispaid 商城是否已经付款
 * @apiSuccess {int} pay_type 商城付款方式
 * @apiSuccess {string} contacts 商城收货人姓名
 * @apiSuccess {string} phone 商城收货人电话
 * @apiSuccess {string} receipt 商城收货地址
 * @apiSuccess {string} updatetime 最后更新时间
 * @apiSuccess {string} createtime 创建时间
 * @apiSuccess {string} checktime 审核时间
 * @apiSuccess {string} auto_delete_date 自动作废日期
 * @apiSuccess {array} goods_list 商品列表
 * @apiSuccess {json} - goods_list详细列表
 * @apiSuccess {int} id 商品清单ID
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {string} gcode 商品CODE
 * @apiSuccess {string} gbarcode 商品条码
 * @apiSuccess {int} gbid 商品品牌ID
 * @apiSuccess {string} gbname 商品品牌名称
 * @apiSuccess {int} gtid 商品类型ID
 * @apiSuccess {string} gtname 商品类型名称
 * @apiSuccess {string} gspec 商品规格
 * @apiSuccess {string} gunit 商品计量单位
 * @apiSuccess {string} gtax_rate 商品税率
 * @apiSuccess {int} reserve 商品库存数量
 * @apiSuccess {int} total 商品数量
 * @apiSuccess {string} unit_price 商品单价
 * @apiSuccess {string} amount_price 商品总价
 *
 *
 */

/**
 * @api {post} order/read_in 浏览采购订单列表
 * @apiName order/read_in
 * @apiGroup Order
 * @apiVersion 0.0.1
 * @apiDescription 浏览采购订单列表
 *
 * @apiParam {int} status 订单状态
 * @apiParam {int} out_cid 出货公司ID
 * @apiParam {int} buid 采购员ID
 * @apiParam {string} search 订单号检索关键字
 * @apiParam {string} begin_date 开始日期
 * @apiParam {string} end_date 截止日期
 *
 * @apiSuccess {int} id 订单号
 * @apiSuccess {int} in_cid 进货公司ID
 * @apiSuccess {string} in_cname 进货公司名称
 * @apiSuccess {int} in_sid 进货仓库ID
 * @apiSuccess {string} in_sname 进货仓库名称
 * @apiSuccess {int} out_cid 出货公司ID
 * @apiSuccess {string} out_cname 出货公司名称
 * @apiSuccess {int} out_sid 出货仓库ID
 * @apiSuccess {string} out_sname 出货仓库名称
 * @apiSuccess {int} uid 填单人ID
 * @apiSuccess {string} uname 填单人名字
 * @apiSuccess {int} cuid 审核员工ID
 * @apiSuccess {string} cuname 审核员工名字
 * @apiSuccess {int} ouid 出库员工ID
 * @apiSuccess {string} ouname 出库员工名字
 * @apiSuccess {int} iuid 入库员工ID
 * @apiSuccess {string} iuname 入库员工名字
 * @apiSuccess {int} buid 采购员ID
 * @apiSuccess {string} buname 采购员工名字
 * @apiSuccess {int} type 订单类型：1-采购订单 2-调拨订单 3-退货订单
 * @apiSuccess {int} amount 订单总金额
 * @apiSuccess {int} tax_amount 订单总税额
 * @apiSuccess {int} rank 紧急程度
 * @apiSuccess {string} memo 备注
 * @apiSuccess {string} status 订单状态
 * @apiSuccess {string} mall_orderno 商城订单号
 * @apiSuccess {int} ispaid 商城是否已经付款
 * @apiSuccess {int} pay_type 商城付款方式
 * @apiSuccess {string} contacts 商城收货人姓名
 * @apiSuccess {string} phone 商城收货人电话
 * @apiSuccess {string} receipt 商城收货地址
 * @apiSuccess {string} updatetime 最后更新时间
 * @apiSuccess {string} createtime 创建时间
 * @apiSuccess {string} checktime 审核时间
 * @apiSuccess {string} auto_delete_date 自动作废日期
 *
 */

/**
 * @api {post} order/read_out/:id 浏览客户订单详情
 * @apiName order/read_out/:id
 * @apiGroup Order
 * @apiVersion 0.0.1
 * @apiDescription 浏览客户订单详情
 *
 * @apiSuccess {int} id 订单号
 * @apiSuccess {int} in_cid 进货公司ID
 * @apiSuccess {string} in_cname 进货公司名称
 * @apiSuccess {int} in_sid 进货仓库ID
 * @apiSuccess {string} in_sname 进货仓库名称
 * @apiSuccess {int} out_cid 出货公司ID
 * @apiSuccess {string} out_cname 出货公司名称
 * @apiSuccess {int} out_sid 出货仓库ID
 * @apiSuccess {string} out_sname 出货仓库名称
 * @apiSuccess {int} uid 填单人ID
 * @apiSuccess {string} uname 填单人名字
 * @apiSuccess {int} cuid 审核员工ID
 * @apiSuccess {string} cuname 审核员工名字
 * @apiSuccess {int} ouid 出库员工ID
 * @apiSuccess {string} ouname 出库员工名字
 * @apiSuccess {int} iuid 入库员工ID
 * @apiSuccess {string} iuname 入库员工名字
 * @apiSuccess {int} buid 采购员ID
 * @apiSuccess {string} buname 采购员工名字
 * @apiSuccess {int} type 订单类型：1-采购订单 2-调拨订单 3-退货订单
 * @apiSuccess {int} amount 订单总金额
 * @apiSuccess {int} tax_amount 订单总税额
 * @apiSuccess {int} rank 紧急程度
 * @apiSuccess {string} memo 备注
 * @apiSuccess {string} status 订单状态
 * @apiSuccess {string} mall_orderno 商城订单号
 * @apiSuccess {int} ispaid 商城是否已经付款
 * @apiSuccess {int} pay_type 商城付款方式
 * @apiSuccess {string} contacts 商城收货人姓名
 * @apiSuccess {string} phone 商城收货人电话
 * @apiSuccess {string} receipt 商城收货地址
 * @apiSuccess {string} updatetime 最后更新时间
 * @apiSuccess {string} createtime 创建时间
 * @apiSuccess {string} checktime 审核时间
 * @apiSuccess {string} auto_delete_date 自动作废日期
 * @apiSuccess {array} goods_list 商品列表
 * @apiSuccess {json} - goods_list详细列表
 * @apiSuccess {int} id 商品清单ID
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {string} gcode 商品CODE
 * @apiSuccess {string} gbarcode 商品条码
 * @apiSuccess {int} gbid 商品品牌ID
 * @apiSuccess {string} gbname 商品品牌名称
 * @apiSuccess {int} gtid 商品类型ID
 * @apiSuccess {string} gtname 商品类型名称
 * @apiSuccess {string} gspec 商品规格
 * @apiSuccess {string} gunit 商品计量单位
 * @apiSuccess {string} gtax_rate 商品税率
 * @apiSuccess {int} reserve 商品库存数量
 * @apiSuccess {int} total 商品数量
 * @apiSuccess {string} unit_price 商品单价
 * @apiSuccess {string} amount_price 商品总价
 *
 *
 */

/**
 * @api {post} order/read_out 浏览客户订单列表
 * @apiName order/read_out
 * @apiGroup Order
 * @apiVersion 0.0.1
 * @apiDescription 浏览客户订单列表
 *
 * @apiParam {int} in_cid 进货公司ID
 * @apiParam {int} suid 业务员ID
 * @apiParam {string} search 订单号检索关键字
 * @apiParam {string} begin_date 开始日期
 * @apiParam {string} end_date 截止日期
 *
 * @apiSuccess {int} id 订单号
 * @apiSuccess {int} in_cid 进货公司ID
 * @apiSuccess {string} in_cname 进货公司名称
 * @apiSuccess {int} in_sid 进货仓库ID
 * @apiSuccess {string} in_sname 进货仓库名称
 * @apiSuccess {int} out_cid 出货公司ID
 * @apiSuccess {string} out_cname 出货公司名称
 * @apiSuccess {int} out_sid 出货仓库ID
 * @apiSuccess {string} out_sname 出货仓库名称
 * @apiSuccess {int} uid 填单人ID
 * @apiSuccess {string} uname 填单人名字
 * @apiSuccess {int} cuid 审核员工ID
 * @apiSuccess {string} cuname 审核员工名字
 * @apiSuccess {int} ouid 出库员工ID
 * @apiSuccess {string} ouname 出库员工名字
 * @apiSuccess {int} iuid 入库员工ID
 * @apiSuccess {string} iuname 入库员工名字
 * @apiSuccess {int} buid 采购员ID
 * @apiSuccess {string} buname 采购员工名字
 * @apiSuccess {int} type 订单类型：1-采购订单 2-调拨订单 3-退货订单
 * @apiSuccess {int} amount 订单总金额
 * @apiSuccess {int} tax_amount 订单总税额
 * @apiSuccess {int} rank 紧急程度
 * @apiSuccess {string} memo 备注
 * @apiSuccess {string} status 订单状态
 * @apiSuccess {string} mall_orderno 商城订单号
 * @apiSuccess {int} ispaid 商城是否已经付款
 * @apiSuccess {int} pay_type 商城付款方式
 * @apiSuccess {string} contacts 商城收货人姓名
 * @apiSuccess {string} phone 商城收货人电话
 * @apiSuccess {string} receipt 商城收货地址
 * @apiSuccess {string} updatetime 最后更新时间
 * @apiSuccess {string} createtime 创建时间
 * @apiSuccess {string} checktime 审核时间
 * @apiSuccess {string} auto_delete_date 自动作废日期
 *
 *
 */

function order($action, $id = NULL){
	init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
	$app = \Slim\Slim::getInstance();
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    $bill_type = 1;
	$my_model = new Order($id);
    $cg_model = new CompanyGoods();
	switch($action){
		case 'create': //创建
			init_log_oper($action, '新建订单');
			param_need($data, ['in_sid','out_cid','buid','goods_list']); //必选
			param_check($data, ['in_sid,out_cid,rank,buid' => "/^\d+$/"]);
			if (!$data) error(1101);

            //检查操作者是否有入库仓库的权限
            Power::check_my_sid($data['in_sid']);
            
            $data['in_sname'] = $my_model->get_name_by_id('o_store', $data['in_sid']);
            $data['out_cname'] = $my_model->get_name_by_id('o_company', $data['out_cid']);
            $data['buname'] = $my_model->get_name_by_id('o_user', $data['buid']);
            $data['in_cid'] = $cid;
            $data['in_cname'] = $app->Sneaker->cname;
            $data['type'] = $bill_type; //采购订单
            $data['status'] = 1;
            $data['from'] = 3;
            //检查商品是否包含停止采购功能的商品
            $res = $cg_model->has_limit_buy($data['goods_list'], $data['in_cid']);
            if($res){
                error(3107);
            }

            //设置默认业务员和默认出货仓库
            $c_model = new Customer();
            $c_res = $c_model -> read_one([
                'cid' => $data['out_cid'],
                'ccid' => $data['in_cid']
            ]);
            if($c_res){
                $data['out_sid'] = $c_res['sid'];
                $data['out_sname'] = $my_model->get_name_by_id('o_store', $data['out_sid']);
                $data['suid'] = $c_res['suid'];
                $data['suname'] = $c_res['suname'];
            }

            //写入自动作废日期
            $s_model = new Supplier();
            $s_res = $s_model -> read_one([
                'cid' => $data['in_cid'],
                'scid' => $data['out_cid']
            ]);
            if($s_res && $s_res['auto_delete']){
                $data['auto_delete_date'] = date('Y-m-d', time()+$s_res['auto_delete']*24*3600);
            }

            //设置操作员
            Power::set_oper($data);

            $order_id = $my_model->add($data);
            if (!$order_id) error(9902);

			//返回参数
			$ret = [
				'order_id' => $order_id,
			];
			success($ret);
			break;

		case 'update': //修改
			init_log_oper($action, '修改订单内容');
			if (!is_numeric($id)) error(1100);

            //param_need($data, ['buid']);
			param_check($data, ['buid,out_sid' => "/^\d+$/"]);
			if (!$data) error(1101);

            Power::set_oper($data);
            unset($data['status']); //防止通过普通修改接口进行审核

            //检测订单状态等属性
            $my_model->my_power($id, 0, $bill_type);

            $data['in_cid'] = $cid;
            //检查商品是否包含停止采购功能的商品
            if(get_value($data, 'goods_list')){
                $res = $cg_model->has_limit_buy($data['goods_list'], $data['in_cid']);
                if($res){
                    error(3107);
                }
            }
            //设置业务员和采购员姓名
            if(get_value($data, 'buid')){
                $data['buname'] = $my_model->get_name_by_id('o_user', $data['buid']);
            }

            if(get_value($data, 'suid')){
                $data['suname'] = $my_model->get_name_by_id('o_user', $data['suid']);
            }

            if(get_value($data, 'out_sid')){
                $data['out_sname'] = $my_model->get_name_by_id('o_store', $data['out_sid']);
            }

            $order_id = $my_model->modify($data);
            if (!$order_id) error(9903);

			//返回参数
			$ret = [
				'order_id' => $order_id,
			];
			success($ret);
			break;

        case 'update_cs': //修改
            init_log_oper($action, '客服修改订单内容');
            if (!is_numeric($id)) error(1100);

            //param_need($data, ['buid']);
            param_check($data, ['suid,out_sid' => "/^\d+$/"]);
            if (!$data) error(1101);

            //Power::set_oper($data);
            unset($data['status']); //防止通过普通修改接口进行审核

            //检测订单状态等属性
            $res = $my_model->read_by_id($id);
            if(!$res){
                error(3000);
            }
            if($res[0]['in_cid'] != $cid && $res[0]['out_cid'] != $cid){
                error(3101);
            }

            //检查商品是否包含停止采购功能的商品
            if(get_value($data, 'goods_list')){
                $res = $cg_model->has_limit_buy($data['goods_list'], $data['in_cid']);
                if($res){
                    error(3107);
                }
            }
            //设置业务员和采购员姓名
            if(get_value($data, 'buid')){
                $data['buname'] = $my_model->get_name_by_id('o_user', $data['buid']);
            }

            if(get_value($data, 'suid')){
                $data['suname'] = $my_model->get_name_by_id('o_user', $data['suid']);
            }

            if(get_value($data, 'out_sid')){
                $data['out_sname'] = $my_model->get_name_by_id('o_store', $data['out_sid']);
            }

            $order_id = $my_model->modify($data);
            if (!$order_id) error(9903);

            //返回参数
            $ret = [
                'order_id' => $order_id,
            ];
            success($ret);
            break;

		case 'check': //创建并审核、修改并审核
			if (!$data) error(1101);

            Power::set_oper($data, 'cuid', 'cuname');
            $data['status'] = 2; //已审核
            $data['checktime'] = date('Y-m-d H:i:s');

            if ($id && is_numeric($id)){
			    init_log_oper($action, '修改并审核订单');
			    param_check($data, ['in_sid,out_cid,rank' => "/^\d+$/"]);

                $my_model->my_power($id, 1, $bill_type);

                //检查商品是否包含停止采购功能的商品
                $res = $cg_model->has_limit_buy($data['goods_list'], $cid);
                if($res){
                    error(3107);
                }

                if(get_value($data, 'buid')){
                    $data['buname'] = $my_model->get_name_by_id('o_user', $data['buid']);
                }

			    $order_id = $my_model->modify($data); //修改订单
            } else {
			    init_log_oper($action, '创建并订单审核');
                param_need($data, ['in_sid','out_cid','buid','goods_list']); //必选
			    param_check($data, ['in_sid,out_cid,rank,buid' => "/^\d+$/"]);
                
                //检查操作者是否有入库仓库的权限
                Power::check_my_sid($data['in_sid']);

                $data['in_sname'] = $my_model->get_name_by_id('o_store', $data['in_sid']);
                $data['out_cname'] = $my_model->get_name_by_id('o_company', $data['out_cid']);
                $data['buname'] = $my_model->get_name_by_id('o_user', $data['buid']);

                Power::set_oper($data);

                $data['in_cid'] = $cid;
                $data['in_cname'] = $app->Sneaker->cname;
                $data['type']   = $bill_type; //采购订单
                $data['from'] = 3;

                //检查商品是否包含停止采购功能的商品
                $res = $cg_model->has_limit_buy($data['goods_list'], $data['in_cid']);
                if($res){
                    error(3107);
                }

                //设置默认业务员和默认出货仓库
                $c_model = new Customer();
                $c_res = $c_model -> read_one([
                    'cid' => $data['out_cid'],
                    'ccid' => $data['in_cid']
                ]);
                if($c_res){
                    $data['out_sid'] = $c_res['sid'];
                    $data['out_sname'] = $my_model->get_name_by_id('o_store', $data['out_sid']);
                    $data['suid'] = $c_res['suid'];
                    $data['suname'] = $c_res['suname'];
                }

                //写入自动作废日期
                $s_model = new Supplier();
                $s_res = $s_model -> read_one([
                    'cid' => $data['in_cid'],
                    'scid' => $data['out_cid']
                ]);
                if($s_res && $s_res['auto_delete']){
                    $data['auto_delete_date'] = date('Y-m-d', time()+$s_res['auto_delete']*24*3600);
                }

                $order_id = $my_model->add($data); //创建订单
            }
            //返回参数
            $ret = [
                'order_id' => $order_id, //为0时说明没有修改
            ];
            success($ret);
            break;

		case 'delete':
			init_log_oper($action, '采购订单取消'); //审核不通过
			if (!is_numeric($id)) error(1100);

            //检查订单信息：1-订单是否属于本机构 2-订单状态是否未审核 3-订单类型是否采购订单
            $my_model->my_power($id, 0, $bill_type);

            //设置订单状态、审核操作员
            $data = ['status'=>9];
            Power::set_oper($data, 'cuid', 'cuname');

			$order_id = $my_model->modify($data); //修改订单
            //返回参数
            $ret = [
                'order_id' => $order_id, //为0时说明没有修改
            ];
			success($ret);
			break;

        case 'delete_out':
            init_log_oper($action, '客户订单取消'); //审核不通过
            if (!is_numeric($id)) error(1100);

            //检查订单信息：1-订单是否属于本机构 2-订单状态是否未审核 3-订单类型是否采购订单
            $o_res = $my_model->my_power($id, 2, $bill_type, 'out_cid', 'ouid');

            //设置订单状态、审核操作员
            $data = ['status'=>9];
            Power::set_oper($data, 'cuid', 'cuname');

            $order_id = $my_model->modify($data); //修改订单

            //通知商城订单状态
            if($o_res['mall_orderno']){
                $msg = "操作员：". $app->Sneaker->uname;
                $mall_model = new Mall();
                $mall_model->notice_order($o_res['mall_orderno'], 8, $msg);
            }

            //返回参数
            $ret = [
                'order_id' => $order_id, //为0时说明没有修改
            ];
            success($ret);
            break;

		case 'read_in':
			if (isset($id)){ //获取单个订单
				//init_log_oper($action, '查看采购订单详情');
				if (!is_numeric($id)) error(1100);
                $res = $my_model->view();
			}else{ //获取列表
				//init_log_oper($action, '浏览采购订单列表');
                //只搜当前用户cid和sid的订单
                Power::set_my_sids($data, 'in_cid', 'in_sid');

                //未入库过的订单才显示
                $status = get_value($data, 'status');
                if($status == -1){
                    $data['status'] = 2;
                    $data['iuid'] = 'null';
                }

                $gid =get_value($data, 'gid');
                if($gid){
                    $data['gids[~]'] = "%$gid%";
                }

				param_check($data, [
                    'page,page_num' => "/^\d+$/", 
                ]);
                $data['type'] = $bill_type; //采购订单

                //优化性能，指定字段读取
                $col = ['amount','auto_delete_date','buid','buname','checktime','createtime',
                    'cuid','cuname','id','in_sid','in_sname','ispaid','mall_orderno','out_cid',
                    'out_cname','pay_type','phone','rank','status','suid','suname','tax_amount',
                    'type','uid','uname','updatetime','iuid','iuname','from'];

				$res = $my_model->view_list($data, $col);
			}
			success($res);
			break;

        case 'read_out':
            if (isset($id)){ //获取单个订单
                //init_log_oper($action, '查看出货订单详情');
                if (!is_numeric($id)) error(1100);

                //读取订单中的信息，判断该订单是否属于本商家，判断订单类型，并且判断订单状态是否已审核或已发货
                $my_model->my_power($id, 0, $bill_type, 'out_cid');

                $res = $my_model->view_out();
            }else{ //获取列表
                //init_log_oper($action, '浏览出货订单列表');
                $data['out_cid'] = $cid;
                $status = get_value($data, 'status', 2);
                $data['status'] = $status;
                //未出库过的订单才显示
                $data['ouid'] = 'null';
                param_check($data, [
                    'page,page_num' => "/^\d+$/", 
                ]);
                $data['type'] = $bill_type; //采购订单

                $gid =get_value($data, 'gid');
                if($gid){
                    $data['gids[~]'] = "%$gid%";
                }

                if($app->Sneaker->user_info['admin'] != 1){
                    //如果不是管理员，判断仓库权限
                    $data['out_sid'] = $app->Sneaker->sids;
                }

                //优化性能，指定字段读取
                $col = ['amount','auto_delete_date','buid','buname','checktime','createtime',
                    'cuid','cuname','id','in_cid','in_cname','ispaid','mall_orderno','out_sid',
                    'out_sname','pay_type','rank','status','suid','suname','tax_amount',
                    'type','uid','uname','updatetime','from'];

                $res = $my_model->view_list($data, $col);
            }
            success($res);
            break;

        case 'read_out_cs':
            if (isset($id)){ //获取单个订单
                //init_log_oper($action, '查看出货订单详情');
                if (!is_numeric($id)) error(1100);

                //读取订单中的信息，判断该订单是否属于本商家，判断订单类型，并且判断订单状态是否已审核或已发货
                $my_model->my_power($id, 0, $bill_type, 'out_cid', 'ouid');

                $res = $my_model->view_out();
            }else{ //获取列表
                //init_log_oper($action, '浏览出货订单列表');
                $data['out_cid'] = $cid;
                $status = get_value($data, 'status', 2);
                $data['status'] = $status;
                //未出库过的订单才显示
                $data['ouid'] = 'null';
                param_check($data, [
                    'page,page_num' => "/^\d+$/",
                ]);
                $data['type'] = $bill_type; //采购订单

                $gid =get_value($data, 'gid');
                if($gid){
                    $data['gids[~]'] = "%$gid%";
                }

                $out_sid = get_value($data, 'out_sid');
                if($out_sid){
                    if($out_sid == -1){
                        $out_sid = 'null';
                    }
                    $data['out_sid'] = $out_sid;
                }

                //优化性能，指定字段读取
                $col = ['amount','auto_delete_date','buid','buname','checktime','createtime',
                    'cuid','cuname','id','in_cid','in_cname','ispaid','mall_orderno','out_sid',
                    'out_sname','pay_type','rank','status','suid','suname','tax_amount',
                    'type','uid','uname','updatetime','from'];

                $res = $my_model->view_list($data, $col);
            }
            success($res);
            break;

        case 'read_out_visit':
            if (isset($id)){ //获取单个订单
                //init_log_oper($action, '查看出货订单详情');
                if (!is_numeric($id)) error(1100);

                //读取订单中的信息，判断该订单是否属于本商家，判断订单类型，并且判断订单状态是否已审核或已发货
                $my_model->my_power($id, 0, $bill_type, 'out_cid');

                $res = $my_model->view_out();
            }else{ //获取列表
                //init_log_oper($action, '浏览出货订单列表');
                $data['out_cid'] = $cid;
                $status = get_value($data, 'status', 2);
                $data['status'] = $status;

                $out_status = get_value($data, 'out_status');
                //出库状态 1-未出库 2-已出库
                if($out_status == 1){
                    $data['ouid'] = 'null';
                }
                elseif($out_status == 2){
                    $data['ouid[!]'] = 'null';
                }
                $gid =get_value($data, 'gid');
                if($gid){
                    $data['gids[~]'] = "%,$gid,%";
                }

                //自有员工还是外借员工
                $belong = get_value($data, 'belong');
                $uid_list = [];
                if($belong){
                    $u_model = new User();
                    $u_res = $u_model->read_list_nopage([
                        'cid'=>$cid,
                        'belong'=>$belong
                    ]);
                    foreach($u_res as $val){
                        $uid_list[] = $val['id'];
                    }
                    if(!$uid_list){
                        $uid_list[] = -1;
                    }
                    $data['suid'] = $uid_list;
                }

                param_check($data, [
                    'page,page_num' => "/^\d+$/",
                ]);
                $data['type'] = $bill_type; //采购订单

                $out_sid = get_value($data, 'out_sid');
                if($out_sid){
                    if($out_sid == -1){
                        $out_sid = 'null';
                    }
                    $data['out_sid'] = $out_sid;
                }

                //优化性能，指定字段读取
                $col = ['amount','auto_delete_date','buid','buname','checktime','createtime',
                    'cuid','cuname','id','in_cid','in_cname','ispaid','mall_orderno','out_sid',
                    'out_sname','pay_type','rank','status','suid','suname','tax_amount',
                    'type','uid','uname','updatetime','from'];

                $res = $my_model->view_list($data, $col);

                if($res['count']){
                    $ccid_list = [];
                    foreach($res['data'] as $val){
                        $ccid_list[] = $val['in_cid'];
                    }
                    $c_model = new Customer();
                    $c_res = $c_model->read_list_nopage([
                        'cid'=>$cid,
                        'ccid'=>$ccid_list
                    ]);
                    $c_data = [];
                    foreach($c_res as $val){
                        $c_data[$val['ccid']] = $val;
                    }
                    foreach($res['data'] as $key=>$val){
                        $temp = get_value($c_data, $val['in_cid'], []);
                        $res['data'][$key]['contactor_phone'] = get_value($temp, 'contactor_phone');
                    }
                }

            }
            success($res);
            break;

        case 'update_visit': //修改
            init_log_oper($action, '回访客户订单');
            if (!is_numeric($id)) error(1100);

            param_need($data, ['visit_memo']);

            $my_model->my_power($id, 0, $bill_type, 'out_cid');

            $order_id = $my_model->modify([
                'visit_memo'=>$data['visit_memo']
            ]);
            if (!$order_id) error(9903);

            //返回参数
            $ret = [
                'order_id' => $order_id,
            ];
            success($ret);
            break;

        case 'split'://拆单
            init_log_oper($action, '客户订单拆分');
            if (!is_numeric($id)) error(1100);
            param_need($data, ['goods_list']);

            //已审核未出库
            $o_res = $my_model->my_power($id, 2, $bill_type, 'out_cid', 'ouid');

            $res = $my_model->my_split($data);

            success($res);
            break;

		default:
			error(1100);
	}
}

