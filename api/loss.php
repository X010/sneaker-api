<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * loss 报损单管理
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} loss/create 生成报损单
 * @apiName loss/create
 * @apiGroup Loss
 * @apiVersion 0.0.1
 * @apiDescription 生成一条未审核的报损单
 *
 * @apiParam {int} sid *仓库ID
 * @apiParam {string} memo 报损单备注
 * @apiParam {json} goods_list *报损单商品清单
 * @apiParam {json} - goods_list字段详情
 * @apiParam {int} gid *商品id
 * @apiParam {int} total *商品数量
 * @apiParam {string} unit_price *商品单价
 * @apiParam {string} amount_price *商品总价
 *
 * @apiSuccess {string} id 报损单号
 *
 */

/**
 * @api {post} loss/update/:id 更新报损单信息（已停用）
 * @apiName loss/update/id
 * @apiGroup Loss
 * @apiVersion 0.0.1
 * @apiDescription 更新报损单
 *
 * @apiParam {string} name_do 经办人名字
 * @apiParam {string} memo *报损单备注
 * @apiParam {json} goods_list *报损单商品清单
 * @apiParam {json} - goods_list字段详情
 * @apiParam {string} gid *商品id
 * @apiParam {int} total *商品数量
 * @apiParam {int} unit_price *商品单价
 *
 *
 */

/**
 * @api {post} loss/check 创建并审核报损单
 * @apiName loss/check
 * @apiGroup Loss
 * @apiVersion 0.0.1
 * @apiDescription 创建并审核报损单
 *
 * @apiParam {int} sid *仓库ID
 * @apiParam {string} memo 报损单备注
 * @apiParam {json} goods_list *报损单商品清单
 * @apiParam {json} - goods_list字段详情
 * @apiParam {int} gid *商品id
 * @apiParam {int} total *商品数量
 * @apiParam {string} unit_price *商品单价
 * @apiParam {string} amount_price *商品总价
 *
 * @apiSuccess {string} id 报损单号
 *
 */

/**
 * @api {post} loss/check/:id 修改并审核报损单
 * @apiName loss/check/id
 * @apiGroup Loss
 * @apiVersion 0.0.1
 * @apiDescription 修改并审核报损单
 *
 * @apiParam {int} sid *仓库ID
 * @apiParam {string} memo 报损单备注
 * @apiParam {json} goods_list *报损单商品清单
 * @apiParam {json} - goods_list字段详情
 * @apiParam {int} gid *商品id
 * @apiParam {int} total *商品数量
 * @apiParam {string} unit_price *商品单价
 * @apiParam {string} amount_price *商品总价
 *
 * @apiSuccess {string} id 报损单号
 *
 *
 */


/**
 * @api {post} loss/delete/:id 取消报损单
 * @apiName loss/delete/id
 * @apiGroup Loss
 * @apiVersion 0.0.1
 * @apiDescription 取消报损单
 *
 *
 */

/**
 * @api {post} loss/read/:id 查询报损单详情
 * @apiName loss/read/id
 * @apiGroup Loss
 * @apiVersion 0.0.1
 * @apiDescription 查询报损单详情
 *
 * @apiSuccess {int} id 报损单号
 * @apiSuccess {int} cid 公司ID
 * @apiSuccess {string} cname 公司名称
 * @apiSuccess {int} sid 仓库ID
 * @apiSuccess {string} sname 仓库名称
 * @apiSuccess {int} status 报损单状态 1-未审核 2-已审核 3-已复核
 * @apiSuccess {int} type 报损单类型 1-采购 2-退货 3-报损 4-报损
 * @apiSuccess {string} memo 报损单备注
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
 * @api {post} loss/read 浏览报损单列表
 * @apiName loss/read
 * @apiGroup Loss
 * @apiVersion 0.0.1
 * @apiDescription 浏览报损单列表，列表字段详情参照“查询报损单详情”接口
 *
 * @apiParam {int} sid 仓库ID
 * @apiParam {string} search 单据号检索关键字
 * @apiParam {int} status 状态
 * @apiParam {string} begin_date 开始日期
 * @apiParam {string} end_date 截止日期
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {int} id 报损单号
 * @apiSuccess {int} cid 公司ID
 * @apiSuccess {string} cname 公司名称
 * @apiSuccess {int} sid 仓库ID
 * @apiSuccess {string} sname 仓库名称
 * @apiSuccess {int} status 报损单状态 1-未审核 2-已审核 3-已复核
 * @apiSuccess {int} type 报损单类型 1-采购 2-退货 3-报损 4-报损
 * @apiSuccess {string} memo 报损单备注
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

function loss($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $my_model = new StockOut($id);
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    $bill_type = 4;
    switch($action){
        case 'create':
            init_log_oper($action, '生成报损单');
            param_need($data, ['sid','goods_list']);
            param_check($data, ['sid' => "/^\d+$/"]);

            //补充默认信息
            $data['cid'] = $cid;
            $data['cname'] = $app->Sneaker->cname;
            $data['type'] = $bill_type;
            $data['status'] = 1;
            $data['sname'] = $my_model->get_name_by_id('o_store', $data['sid']);

            //操作员
            Power::set_oper($data);

            $id = $my_model -> my_create($data);
            success(['id' => $id]);
            break;

        case 'update':
            init_log_oper($action, '修改报损单');
            if (!is_numeric($id)) error(1100);

            param_check($data, ['sid' => "/^\d+$/"]);
            if (!$data) error(1101);

            Power::set_oper($data);
            unset($data['status']); //防止通过普通修改接口进行审核
            $my_model->my_power($id, 1, $bill_type);
            $so_id = $my_model->my_update($data);
            if (!$so_id) error(9903);

            //返回参数
            $ret = [
                'id' => $so_id,
            ];
            success($ret);
            break;

        case 'read':
            if(isset($id)){
                //init_log_oper($action, '读取报损单详情');
                if(!is_numeric($id)){
                    error(1100);
                }

                //读取入库单中的信息，判断是否属于本商家，判断类型，并且判断状态是否未审核
                $my_model->my_power($id, 0, 0);
                $res = $my_model -> my_read();
                success($res[0]);
            }
            else{
                //init_log_oper($action, '读取报损单列表');
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
            init_log_oper($action, '审核报损单');
            Power::set_oper($data, 'cuid', 'cuname');
            $data['status'] = 4;
            $data['checktime'] = date('Y-m-d H:i:s');

            if(isset($id)){
                //修改后审核
                $my_model->my_power($id, 1, $bill_type);

                if(get_value($data, 'sid')){
                    $data['sname'] = $my_model->get_name_by_id('o_store', $data['sid']);
                }
                $data['out_sid'] = $data['sid'];
                $my_model -> my_check($data, 'update', $bill_type);
            }
            else{
                param_need($data, ['sid','goods_list']);
                param_check($data, ['sid' => "/^\d+$/"]);

                //补充默认信息
                $data['cid'] = $cid;
                $data['cname'] = $app->Sneaker->cname;
                $data['type'] = $bill_type;
                $data['sname'] = $my_model->get_name_by_id('o_store', $data['sid']);
                Power::set_oper($data);
                $my_model -> my_check($data, 'create', $bill_type);
                $id = $my_model->get_id();
            }
            success(['id' => $id]);
            break;

        case 'delete':
            init_log_oper($action, '取消报损单');
            if (!is_numeric($id)) error(1100);

            $my_model->my_power($id, 1, $bill_type);
          
            $so_id = $my_model->my_delete(); //修改订单
            //返回参数
            $ret = [
                'id' => $so_id, //为0时说明没有修改
            ];
            success($ret);
            break;

        default:
            error(1100);
    }

}