<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * settle_proxy_supplier 供应商代销结算单管理
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} settle_proxy_supplier/create 新建供应商代销结算单
 * @apiName settle_proxy_supplier/create
 * @apiGroup SettleProxySupplier
 * @apiVersion 0.0.1
 * @apiDescription 新建供应商代销结算单
 *
 * @apiParam {int} scid *供应商ID
 * @apiParam {int} sid *仓库ID
 * @apiParam {string} date *结算日期
 * @apiParam {float} discount *折扣
 * @apiParam {int} pay_type *支付方式
 * @apiParam {string} memo 备注
 * @apiParam {json} goods_list 商品列表
 * @apiParam {json} - 单据列表详情字段
 * @apiParam {int} gid *商品ID
 * @apiParam {int} current_real_total *实结数量
 *
 * @apiSuccess {int} id 结算单ID
 *
 */


/**
 * @api {post} settle_proxy_supplier/check 新建并审核供应商代销结算单
 * @apiName settle_proxy_supplier/check
 * @apiGroup SettleProxySupplier
 * @apiVersion 0.0.1
 * @apiDescription 新建并审核供应商代销结算单
 *
 * @apiParam {int} scid *供应商ID
 * @apiParam {int} sid *仓库ID
 * @apiParam {string} date *结算日期
 * @apiParam {float} discount *折扣
 * @apiParam {int} pay_type *支付方式
 * @apiParam {string} memo 备注
 * @apiParam {json} goods_list 商品列表
 * @apiParam {json} - 单据列表详情字段
 * @apiParam {int} gid *商品ID
 * @apiParam {int} current_real_total *实结数量
 *
 * @apiSuccess {int} id 结算单ID
 *
 */

/**
 * @api {post} settle_proxy_supplier/check/:id 修改并审核供应商代销结算单
 * @apiName settle_proxy_supplier/check/:id
 * @apiGroup SettleProxySupplier
 * @apiVersion 0.0.1
 * @apiDescription 修改并审核供应商代销结算单
 *
 * @apiParam {int} scid *供应商ID
 * @apiParam {int} sid *仓库ID
 * @apiParam {string} date *结算日期
 * @apiParam {float} discount *折扣
 * @apiParam {int} pay_type *支付方式
 * @apiParam {string} memo 备注
 * @apiParam {json} goods_list 商品列表
 * @apiParam {json} - 单据列表详情字段
 * @apiParam {int} gid *商品ID
 * @apiParam {int} current_real_total *实结数量
 *
 * @apiSuccess {int} id 结算单ID
 *
 */

/**
 * @api {post} settle_proxy_supplier/read/:id 读取供应商代销结算单明细
 * @apiName settle_proxy_supplier/read/:id
 * @apiGroup SettleProxySupplier
 * @apiVersion 0.0.1
 * @apiDescription 读取供应商代销结算单明细
 *
 *
 * @apiSuccess {int} id 结算单ID
 * @apiSuccess {int} scid 供应商ID
 * @apiSuccess {string} scname 供应商名称
 * @apiSuccess {int} sid 仓库ID
 * @apiSuccess {string} sname 仓库名称
 * @apiSuccess {string} createtime 结算单生成时间
 * @apiSuccess {string} checktime 结算单审核时间
 * @apiSuccess {int} uid 操作员ID
 * @apiSuccess {string} uname 操作员名称
 * @apiSuccess {int} cuid 审核员ID
 * @apiSuccess {string} cuname 审核员名称
 * @apiSuccess {int} status 状态
 * @apiSuccess {string} memo 备注
 * @apiSuccess {int} pay_type 支付方式
 * @apiSuccess {string} settle_date 结算日期
 * @apiSuccess {string} current_after_discount_amount 优惠后实结金额
 * @apiSuccess {string} current_expect_amount 应结金额
 * @apiSuccess {string} current_expect_total 应结个数
 * @apiSuccess {string} current_real_amount 实结金额
 * @apiSuccess {string} current_real_total 实结个数
 * @apiSuccess {string} current_rest_amount 本期结余金额
 * @apiSuccess {string} current_rest_total 本期结余个数
 * @apiSuccess {string} current_sell_amount 本期销售金额
 * @apiSuccess {string} current_sell_total 本期销售个数
 * @apiSuccess {string} last_rest_amount 上期结余金额
 * @apiSuccess {string} last_rest_total 上期结余个数
 * @apiSuccess {string} last_settle_date 上期结算日
 * @apiSuccess {string} discount 优惠折扣
 * @apiSuccess {list} goods_list 商品清单
 * @apiSuccess {list} - 商品清单字段
 * @apiSuccess {string} current_after_discount_amount 优惠后实结金额
 * @apiSuccess {string} current_expect_amount 应结金额
 * @apiSuccess {string} current_expect_total 应结个数
 * @apiSuccess {string} current_real_amount 实结金额
 * @apiSuccess {string} current_real_total 实结个数
 * @apiSuccess {string} current_rest_amount 本期结余金额
 * @apiSuccess {string} current_rest_total 本期结余个数
 * @apiSuccess {string} current_sell_amount 本期销售金额
 * @apiSuccess {string} current_sell_total 本期销售个数
 * @apiSuccess {string} last_rest_amount 上期结余金额
 * @apiSuccess {string} last_rest_total 上期结余个数
 * @apiSuccess {string} last_settle_date 上期结算日
 * @apiSuccess {string} discount 优惠折扣
 * @apiSuccess {string} gbarcode 商品条码
 * @apiSuccess {string} gcode 商品编码
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {string} gspec 商品规格
 * @apiSuccess {string} gtax_rate 商品税率
 * @apiSuccess {string} gunit 商品单位
 * @apiSuccess {string} proxy_amount 代销价
 * @apiSuccess {int} reserve 库存数量
 * @apiSuccess {string} reserve_amount 库存金额
 * @apiSuccess {list} tax_group 税率分组统计字段
 * @apiSuccess {list} - 税率分组统计字段
 * @apiSuccess {string} amount_price 税率总计金额
 * @apiSuccess {string} tax_price 税额
 * @apiSuccess {string} tax_rate 税率
 */

/**
 * @api {post} settle_proxy_supplier/read 读取供应商代销结算单列表
 * @apiName settle_proxy_supplier/read
 * @apiGroup SettleProxySupplier
 * @apiVersion 0.0.1
 * @apiDescription 读取供应商代销结算单列表
 *
 * @apiParam {int} scid 供应商ID
 * @apiParam {int} sid 仓库ID
 * @apiParam {int} status 状态1-未审核 2-已审核 10-已冲正 11-冲正单（负单）
 *
 * @apiSuccess {int} id 结算单ID
 * @apiSuccess {int} scid 供应商ID
 * @apiSuccess {string} scname 供应商名称
 * @apiSuccess {int} sid 仓库ID
 * @apiSuccess {string} sname 仓库名称
 * @apiSuccess {string} createtime 结算单生成时间
 * @apiSuccess {string} checktime 结算单审核时间
 * @apiSuccess {int} uid 操作员ID
 * @apiSuccess {string} uname 操作员名称
 * @apiSuccess {int} cuid 审核员ID
 * @apiSuccess {string} cuname 审核员名称
 * @apiSuccess {int} status 状态
 * @apiSuccess {string} memo 备注
 * @apiSuccess {int} pay_type 支付方式
 * @apiSuccess {string} settle_date 结算日期
 * @apiSuccess {string} current_after_discount_amount 优惠后实结金额
 * @apiSuccess {string} current_expect_amount 应结金额
 * @apiSuccess {string} current_expect_total 应结个数
 * @apiSuccess {string} current_real_amount 实结金额
 * @apiSuccess {string} current_real_total 实结个数
 * @apiSuccess {string} current_rest_amount 本期结余金额
 * @apiSuccess {string} current_rest_total 本期结余个数
 * @apiSuccess {string} current_sell_amount 本期销售金额
 * @apiSuccess {string} current_sell_total 本期销售个数
 * @apiSuccess {string} last_rest_amount 上期结余金额
 * @apiSuccess {string} last_rest_total 上期结余个数
 * @apiSuccess {string} last_settle_date 上期结算日
 * @apiSuccess {string} discount 优惠折扣
 */

/**
 * @api {post} settle_proxy_supplier/read_proxy_goods/:id 查询供应商代销商品明细
 * @apiName settle_proxy_supplier/read_proxy_goods/:id
 * @apiGroup SettleProxySupplier
 * @apiVersion 0.0.1
 * @apiDescription 冲正供应商代销结算单
 *
 * @apiParam {int} scid *供应商ID
 * @apiParam {int} sid *仓库ID
 * @apiParam {string} date *结算日期
 *
 * @apiSuccess {string} current_expect_amount 应结金额
 * @apiSuccess {string} current_expect_total 应结个数
 * @apiSuccess {string} current_sell_amount 本期销售金额
 * @apiSuccess {string} current_sell_total 本期销售个数
 * @apiSuccess {string} last_rest_amount 上期结余金额
 * @apiSuccess {string} last_rest_total 上期结余个数
 * @apiSuccess {string} gbarcode 商品条码
 * @apiSuccess {string} gcode 商品编码
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {string} gspec 商品规格
 * @apiSuccess {string} gtax_rate 商品税率
 * @apiSuccess {string} gunit 商品单位
 * @apiSuccess {string} proxy_amount 代销价
 * @apiSuccess {int} reserve 库存数量
 * @apiSuccess {string} reserve_amount 库存金额
 *
 */

/**
 * @api {post} settle_proxy_supplier/flush/:id 冲正供应商代销结算单
 * @apiName settle_proxy_supplier/flush/:id
 * @apiGroup SettleProxySupplier
 * @apiVersion 0.0.1
 * @apiDescription 冲正供应商代销结算单
 *
 * @apiSuccess {int} id 结算单ID
 *
 */

/**
 * @api {post} settle_proxy_supplier/delete 取消供应商代销结算单
 * @apiName settle_proxy_supplier/delete
 * @apiGroup SettleProxySupplier
 * @apiVersion 0.0.1
 * @apiDescription 取消供应商代销结算单
 *
 * @apiSuccess {int} id 结算单号
 *
 */

function settle_proxy_supplier($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    $my_model = new SettleProxySupplier($id);

    switch($action){
        case 'create':
            init_log_oper($action, '新建供应商代销结算单');
            param_need($data, ['sid','scid','date','discount','pay_type','goods_list']); //必选
            param_check($data, ['scid,sid' => "/^\d+$/"]);

            $data['cid'] = $cid;
            $data['cname'] = $app->Sneaker->cname;
            $data['sname'] = $my_model->get_name_by_id('o_store', $data['sid']);
            $data['scname'] = $my_model->get_name_by_id('o_company', $data['scid']);

            Power::set_oper($data);
            $data['status'] = 1;

            $res = $my_model -> my_create($data);

            success(['id'=>$res]);
            break;

        case 'check':

            Power::set_oper($data, 'cuid', 'cuname');
            $data['status'] = 2;
            $data['checktime'] = date('Y-m-d H:i:s');

            if(isset($id)) {
                init_log_oper($action, '修改并审核供应商代销结算单');
                param_need($data, ['sid','scid','date','discount','pay_type','goods_list']); //必选

                //检测是否有权限，状态等
                $res = $my_model->my_power($id, 1);
                $data['date'] = $res['settle_date'];
                $data['cid'] = $cid;
                $data['cname'] = $app->Sneaker->cname;
                $data['sname'] = $my_model->get_name_by_id('o_store', $data['sid']);
                $data['scname'] = $my_model->get_name_by_id('o_company', $data['scid']);

                $my_model -> my_check($data, 'update');
            }
            else{
                init_log_oper($action, '新建并审核供应商代销结算单');
                param_need($data, ['sid','scid','date','discount','pay_type','goods_list']); //必选
                param_check($data, ['scid,sid' => "/^\d+$/"]);

                $data['cid'] = $cid;
                $data['cname'] = $app->Sneaker->cname;
                $data['sname'] = $my_model->get_name_by_id('o_store', $data['sid']);
                $data['scname'] = $my_model->get_name_by_id('o_company', $data['scid']);

                Power::set_oper($data);
                $id = $my_model -> my_check($data, 'create');

            }
            success(['id'=>$id]);
            break;

        case 'read':
            if(isset($id)) {
                //init_log_oper($action, '读取供应商代销结算单详情');
                //检测是否有权限，状态等
                $my_model->my_power($id, 0);

                $res = $my_model -> my_read();
                success($res[0]);
            }
            else{
                //init_log_oper($action, '读取供应商代销结算单列表');
                param_check($data, ['scid,sid,status' => "/^\d+$/"]);

                $data['cid'] = $cid;
                $res = $my_model -> read_list($data);
                success($res);
            }
            break;

        case 'read_proxy_goods':
            //init_log_oper($action, '查询供应商代销商品明细');
            param_need($data, ['sid','scid','date']); //必选
            param_check($data, ['scid,sid' => "/^\d+$/"]);

            $data['cid'] = $cid;
            $res = $my_model->read_proxy_goods($data);
            success($res);
            break;

        case 'flush':
            init_log_oper($action, '冲正供应商代销结算单');
            $res = $my_model->my_power($id, 2);

            $res2 = $my_model->has([
                'sid'=>$res['sid'],
                'cid'=>$res['cid'],
                'scid'=>$res['scid'],
                'status'=>2,
                'checktime[>]'=>$res['checktime'],
            ]);
            if($res2){
                error(3410);
            }

            $data = ['status'=>10];
            Power::set_oper($data, 'cuid', 'cuname');
            $my_model->update_by_id($data);
            success(['id'=>$id]);
            break;

        case 'delete':
            init_log_oper($action, '取消供应商代销结算单');
            $my_model->my_power($id, 1);

            $data = ['status'=>9];
            Power::set_oper($data, 'cuid', 'cuname');
            $my_model->update_by_id($data);

            success(['id'=>$id]);
            break;
        default:
            error(1100);
    }

}
