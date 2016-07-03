<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * overflow 报溢单管理
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */


/**
 * @api {post} overflow/create 生成报溢单
 * @apiName overflow/create
 * @apiGroup Overflow
 * @apiVersion 0.0.1
 * @apiDescription 生成一条未审核的报溢单
 *
 * @apiParam {int} sid *仓库ID
 * @apiParam {string} memo 报溢单备注
 * @apiParam {json} goods_list *报溢单商品清单
 * @apiParam {json} - goods_list字段详情
 * @apiParam {string} gid *商品id
 * @apiParam {int} total *商品数量
 * @apiParam {string} unit_price *商品单价
 * @apiParam {string} amount_price *商品总价
 *
 * @apiSuccess {string} id 报溢单号 
 *
 *
 */

/**
 * @api {post} overflow/update/:id 更新报溢单信息（已停用）
 * @apiName overflow/update/id
 * @apiGroup Overflow
 * @apiVersion 0.0.1
 * @apiDescription 更新报溢单
 *
 * @apiParam {string} name_do 经办人名字
 * @apiParam {string} memo *报溢单备注
 * @apiParam {json} goods_list *报溢单商品清单
 * @apiParam {json} - goods_list字段详情
 * @apiParam {string} gid *商品id
 * @apiParam {int} total *商品数量
 * @apiParam {int} unit_price *商品单价
 *
 *
 */

/**
 * @api {post} overflow/check 创建并审核报溢单
 * @apiName overflow/check
 * @apiGroup Overflow
 * @apiVersion 0.0.1
 * @apiDescription 创建并审核报溢单
 *
 * @apiParam {int} sid *仓库ID
 * @apiParam {string} memo 报溢单备注
 * @apiParam {json} goods_list *报溢单商品清单
 * @apiParam {json} - goods_list字段详情
 * @apiParam {string} gid *商品id
 * @apiParam {int} total *商品数量
 * @apiParam {string} unit_price *商品单价
 * @apiParam {string} amount_price *商品总价
 *
 *
 */

/**
 * @api {post} overflow/check/:id 修改并审核报溢单
 * @apiName overflow/check/id
 * @apiGroup Overflow
 * @apiVersion 0.0.1
 * @apiDescription 修改并审核报溢单
 *
 * @apiParam {int} sid 仓库ID
 * @apiParam {string} memo 报溢单备注
 * @apiParam {json} goods_list *报溢单商品清单
 * @apiParam {json} - goods_list字段详情
 * @apiParam {string} gid *商品id
 * @apiParam {int} total *商品数量
 * @apiParam {string} unit_price *商品单价
 * @apiParam {string} amount_price *商品总价
 *
 *
 */


/**
 * @api {post} overflow/delete/:id 取消报溢单
 * @apiName overflow/delete/id
 * @apiGroup Overflow
 * @apiVersion 0.0.1
 * @apiDescription 取消报溢单
 *
 *
 */

/**
 * @api {post} overflow/read/:id 查询报溢单详情
 * @apiName overflow/read/id
 * @apiGroup Overflow
 * @apiVersion 0.0.1
 * @apiDescription 查询报溢单详情
 *
 * @apiSuccess {int} id 报溢单号
 * @apiSuccess {int} cid 公司ID
 * @apiSuccess {string} cname 公司名称
 * @apiSuccess {int} sid 仓库ID
 * @apiSuccess {string} sname 仓库名称
 * @apiSuccess {int} status 报溢单状态 1-未审核 2-已审核 3-已复核
 * @apiSuccess {int} type 报溢单类型 1-采购 2-退货 3-报溢 4-报溢
 * @apiSuccess {string} memo 报溢单备注
 * @apiSuccess {string} createtime 创建时间
 * @apiSuccess {string} updatetime 上次更新时间
 * @apiSuccess {string} checktime 审核时间
 * @apiSuccess {int} uid 填单人ID
 * @apiSuccess {string} uname 填单人姓名
 * @apiSuccess {int} cuid 审核员ID
 * @apiSuccess {string} cuname 审核员姓名
 * @apiSuccess {int} ruid 复核员ID
 * @apiSuccess {string} runame 复核员姓名
 * @apiSuccess {string} amount 单据总金额
 * @apiSuccess {string} tax_amount 单据总税额
 * @apiSuccess {int} negative_id 负单单号
 * @apiSuccess {json} goods_list 商品清单
 * @apiSuccess {json} - goods_list详细列表
 * @apiSuccess {int} id 商品清单ID
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {string} gspec 商品规格
 * @apiSuccess {string} gunit 商品计量单位
 * @apiSuccess {string} gcode 商品CODE
 * @apiSuccess {string} gbarcode 商品条码
 * @apiSuccess {string} gtax_rate 商品税率
 * @apiSuccess {int} total 商品数量
 * @apiSuccess {string} unit_price 商品单价
 * @apiSuccess {string} amount_price 商品总价
 * @apiSuccess {string} tax_price 商品总税额
 * @apiSuccess {int} reserve 剩余库存数
 *
 */

/**
 * @api {post} overflow/read 浏览报溢单列表
 * @apiName overflow/read
 * @apiGroup Overflow
 * @apiVersion 0.0.1
 * @apiDescription 浏览报溢单列表
 *
 * @apiParam {int} sid 仓库ID
 * @apiParam {string} search 单据号检索关键字
 * @apiParam {int} status 状态
 * @apiParam {string} begin_date 开始日期
 * @apiParam {string} end_date 截止日期
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {int} id 报溢单号
 * @apiSuccess {int} cid 公司ID
 * @apiSuccess {string} cname 公司名称
 * @apiSuccess {int} sid 仓库ID
 * @apiSuccess {string} sname 仓库名称
 * @apiSuccess {int} status 报溢单状态 1-未审核 2-已审核 3-已复核
 * @apiSuccess {int} type 报溢单类型 1-采购 2-退货 3-报溢 4-报溢
 * @apiSuccess {string} memo 报溢单备注
 * @apiSuccess {string} createtime 创建时间
 * @apiSuccess {string} updatetime 上次更新时间
 * @apiSuccess {string} checktime 审核时间
 * @apiSuccess {int} uid 填单人ID
 * @apiSuccess {string} uname 填单人姓名
 * @apiSuccess {int} cuid 审核员ID
 * @apiSuccess {string} cuname 审核员姓名
 * @apiSuccess {int} ruid 复核员ID
 * @apiSuccess {string} runame 复核员姓名
 * @apiSuccess {string} amount 单据总金额
 * @apiSuccess {string} tax_amount 单据总税额
 * @apiSuccess {int} negative_id 负单单号
 *
 */

function overflow($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $my_model = new StockIn($id);
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    $bill_type = 4;
    switch($action){
        case 'create':
            init_log_oper($action, '生成报溢单');
            param_need($data, ['sid','goods_list']);
            param_check($data, ['sid' => "/^\d+$/"]);

            //补充默认信息
            $data['cid'] = $cid;
            $data['cname'] = $app->Sneaker->cname;
            $data['type'] = $bill_type;
            $data['status'] = 1;
            $data['sname'] = $my_model->get_name_by_id('o_store', $data['sid']);
            
            Power::set_oper($data);
            $id = $my_model -> my_create($data);
            success(['id' => $id]);
            break;

        case 'update':
            init_log_oper($action, '修改报溢单');
            if (!is_numeric($id)) error(1100);

            param_check($data, ['sid' => "/^\d+$/"]);
            if (!$data) error(1101);

            Power::set_oper($data);
            unset($data['status']); //防止通过普通修改接口进行审核
            
            $my_model->my_power($id, 1, $bill_type);

            $si_id = $my_model->my_update($data);
            if (!$si_id) error(9903);

            //返回参数
            $ret = [
                'id' => $si_id,
            ];
            success($ret);
            break;

        case 'read':
            if(isset($id)){
                //init_log_oper($action, '读取报溢单详情');
                if(!is_numeric($id)){
                    error(1100);
                }
                $my_model->my_power($id, 0, 0);

                $res = $my_model -> my_read();

                success($res[0]);
            }
            else{
                //init_log_oper($action, '读取报溢单列表');
                param_check($data, ['page' => "/^\d+$/",'page_num' => "/^\d+$/"]);

                //默认加上公司内的单据条件
                if(!get_value($data, 'sid')){
                    Power::set_my_sids($data);
                }
                $data['type'] = $bill_type;

                $res = $my_model -> read_list($data);

                success($res);
            }
            break;
        case 'check':

            Power::set_oper($data, 'cuid', 'cuname');
            $data['status'] = 2;
            $data['checktime'] = date('Y-m-d H:i:s');
            if(isset($id)){
                //修改后审核
                init_log_oper($action, '修改并审核报溢单');
                $my_model->my_power($id, 1, $bill_type);

                if(get_value($data, 'sid')){
                    $data['sname'] = $my_model->get_name_by_id('o_store', $data['sid']);
                }
                $data['in_sid'] = $data['sid'];
                $my_model -> my_check($data, 'update', $bill_type);
            }
            else{
                init_log_oper($action, '创建并审核报溢单');
                param_need($data, ['sid','goods_list']);
                param_check($data, ['sid' => "/^\d+$/"]);

                //补充默认信息
                $data['cid'] = $cid;
                $data['cname'] = $app->Sneaker->cname;
                $data['type'] = $bill_type;

                $data['sname'] = $my_model->get_name_by_id('o_store', $data['sid']);
                Power::set_oper($data);

                $id = $my_model -> my_check($data, 'create', $bill_type);
            }
            success(['id' => $id]);
            break;

        case 'delete':
            init_log_oper($action, '取消报溢单');
            if (!is_numeric($id)) error(1100);
            $my_model->my_power($id, 1, $bill_type);
            $si_id = $my_model->my_delete(); //修改订单
            //返回参数
            $ret = [
                'id' => $si_id, //为0时说明没有修改
            ];
            success($ret);
            break;

        default:
            error(1100);
    }

}