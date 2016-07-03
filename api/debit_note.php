<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * debit_note 收款单管理
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} debit_note/create 新建收款单
 * @apiName debit_note/create
 * @apiGroup DebitNote
 * @apiVersion 0.0.1
 * @apiDescription 新建收款单
 *
 * @apiParam {int} dcid *往来单位公司ID
 * @apiParam {int} pay_type *付款方式ID
 * @apiParam {json} account_list *科目明细
 * @apiParam {json} - 科目明细详情
 * @apiParam {int} account_id 会计科目ID
 * @apiParam {string} amount_price 金额
 * @apiParam {string} memo 备注
 *
 * @apiSuccess {int} id 收款单ID
 *
 */

/**
 * @api {post} debit_note/check 新建并审核收款单
 * @apiName debit_note/check
 * @apiGroup DebitNote
 * @apiVersion 0.0.1
 * @apiDescription 新建并审核收款单
 *
 * @apiParam {int} dcid *往来单位公司ID
 * @apiParam {int} pay_type *付款方式ID
 * @apiParam {json} account_list *科目明细
 * @apiParam {json} - 科目明细详情
 * @apiParam {int} account_id 会计科目ID
 * @apiParam {string} amount_price 金额
 * @apiParam {string} memo 备注
 *
 * @apiSuccess {int} id 收款单ID
 *
 */

/**
 * @api {post} debit_note/check/:id 修改并审核收款单
 * @apiName debit_note/check/:id
 * @apiGroup DebitNote
 * @apiVersion 0.0.1
 * @apiDescription 修改并审核收款单
 *
 * @apiParam {int} pay_type *付款方式ID
 * @apiParam {json} account_list *科目明细
 * @apiParam {json} - 科目明细详情
 * @apiParam {int} account_id 会计科目ID
 * @apiParam {string} amount_price 金额
 * @apiParam {string} memo 备注
 *
 * @apiSuccess {int} id 收款单ID
 *
 */

/**
 * @api {post} debit_note/read 读取收款单列表
 * @apiName debit_note/read
 * @apiGroup DebitNote
 * @apiVersion 0.0.1
 * @apiDescription 读取收款单列表
 *
 * @apiParam {int} account_id 会计科目ID
 * @apiParam {int} dcid 往来单位公司ID
 * @apiParam {int} status 会计科目名称
 *
 * @apiSuccess {int} id 收款单ID
 * @apiSuccess {int} dcid 往来单位ID
 * @apiSuccess {string} dcname 往来单位名称
 * @apiSuccess {int} status 状态1-未审核 2-已审核 9-已取消 10-已冲正 11-冲正单
 * @apiSuccess {int} amount_price 总金额
 * @apiSuccess {int} negative_id 关联的负单ID
 * @apiSuccess {int} pay_type 付款方式
 * @apiSuccess {int} uid 操作员ID
 * @apiSuccess {string} uname 操作员名称
 * @apiSuccess {int} cuid 审核员ID
 * @apiSuccess {string} cuname 审核员名称
 * @apiSuccess {string} checktime 审核时间
 */

/**
 * @api {post} debit_note/read/:id 读取收款单详情
 * @apiName debit_note/read/:id
 * @apiGroup DebitNote
 * @apiVersion 0.0.1
 * @apiDescription 读取收款单详情
 *
 * @apiSuccess {int} id 收款单ID
 * @apiSuccess {int} dcid 往来单位ID
 * @apiSuccess {string} dcname 往来单位名称
 * @apiSuccess {int} status 状态1-未审核 2-已审核 9-已取消 10-已冲正 11-冲正单
 * @apiSuccess {int} amount_price 总金额
 * @apiSuccess {int} negative_id 关联的负单ID
 * @apiSuccess {int} pay_type 付款方式
 * @apiSuccess {int} uid 操作员ID
 * @apiSuccess {string} uname 操作员名称
 * @apiSuccess {int} cuid 审核员ID
 * @apiSuccess {string} cuname 审核员名称
 * @apiSuccess {string} checktime 审核时间
 * @apiSuccess {json} account_list 科目明细
 * @apiSuccess {json} - 科目明细详情
 * @apiSuccess {int} account_id 会计科目ID
 * @apiSuccess {int} account_name 会计科目名称
 * @apiSuccess {string} amount_price 金额
 * @apiSuccess {string} memo 备注
 */

/**
 * @api {post} debit_note/flush/:id 冲正收款单
 * @apiName debit_note/flush/:id
 * @apiGroup DebitNote
 * @apiVersion 0.0.1
 * @apiDescription 冲正收款单
 *
 * @apiSuccess {int} id 收款单ID
 *
 */

/**
 * @api {post} debit_note/delete 取消收款单
 * @apiName debit_note/delete
 * @apiGroup DebitNote
 * @apiVersion 0.0.1
 * @apiDescription 取消收款单
 *
 * @apiSuccess {int} id 收款单号
 *
 */

function debit_note($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    $my_model = new DebitNote($id);
    switch($action){
        case 'create':
            init_log_oper($action, '创建收款单');

            param_need($data, ['dcid','pay_type','account_list']); //必选
            param_check($data, ['dcid,pay_type' => "/^\d+$/"]);

            $data['cid'] = $cid;
            $data['cname'] = $app->Sneaker->cname;
            //dcid为来往单位ID，补充其名称
            $data['dcname'] = $my_model->get_name_by_id('o_company', $data['dcid']);

            Power::set_oper($data);
            $data['status'] = 1;

            $res = $my_model -> my_create($data);

            success(['id'=>$res]);
            break;

        case 'check':
            Power::set_oper($data, 'cuid', 'cuname');
            $data['status'] = 2;
            $data['checktime'] = date('Y-m-d H:i:s');
            if(isset($id)){
                init_log_oper($action, '修改并审核收款单');
                param_need($data, ['account_list']); //必选
                //检测是否有权限，状态等
                $my_model->my_power($id, 1);
                $my_model->my_check($data, 'update');
            }
            else{
                init_log_oper($action, '创建并审核收款单');
                param_need($data, ['dcid','pay_type','account_list']); //必选
                param_check($data, ['dcid,pay_type' => "/^\d+$/"]);

                $data['cid'] = $cid;
                $data['cname'] = $app->Sneaker->cname;
                //dcid为来往单位ID，补充其名称
                $data['dcname'] = $my_model->get_name_by_id('o_company', $data['dcid']);

                Power::set_oper($data);
                $id = $my_model -> my_check($data, 'create');
            }
            success(['id'=>$id]);
            break;

        case 'read':
            if(isset($id)) {
                //init_log_oper($action, '读取收款单详情');

                //检测是否有权限，状态等
                $my_model->my_power($id, 0);
                $res = $my_model -> my_read();
                success($res[0]);
            }
            else{
                //init_log_oper($action, '读取收款单列表');
                $data['cid'] = $cid;
                $res = $my_model -> read_list($data);
                success($res);
            }
            break;

        case 'flush':
            init_log_oper($action, '冲正收款单');
            if(!isset($id)) {
                error(1100);
            }

            //检测是否有权限，状态等
            $my_model->my_power($id, 2);
            $my_model->my_flush();
            success(['id'=>$id]);
            break;

        case 'delete':
            init_log_oper($action, '取消收款单');
            if (!is_numeric($id)) error(1100);

            $my_model->my_power($id, 1);
            $data = ['status'=>9];
            Power::set_oper($data, 'cuid', 'cuname');
            $my_model->update_by_id($data); //修改订单
            success(['id'=>$id]);
            break;

        default:
            error(1100);
    }

}
