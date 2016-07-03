<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * f_finance 财务类报表
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} f_finance/debit 应收款查询
 * @apiName f_finance/debit
 * @apiGroup FFinance
 * @apiVersion 0.0.1
 * @apiDescription 应收款查询
 *
 * @apiParam {string} ccids 客户ID多选
 * @apiParam {string} sids 仓库ID多选
 * @apiParam {string} date *日期，格式2015-10-23
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 * @apiParam {string} orderby 排序,例如：all_amount^desc,real_amount^asc
 *
 * @apiSuccess {string} id 客户ID
 * @apiSuccess {string} name 客户名称
 * @apiSuccess {string} all_amount 应收金额
 * @apiSuccess {string} all_total 应收单据
 * @apiSuccess {string} exp_amount 到期应收金额
 * @apiSuccess {string} exp_total 到期应收单据
 * @apiSuccess {string} real_amount 实收金额
 * @apiSuccess {string} real_total 实收单据
 */

/**
 * @api {post} f_finance/payment 应付款查询
 * @apiName f_finance/payment
 * @apiGroup FFinance
 * @apiVersion 0.0.1
 * @apiDescription 应付款查询
 *
 * @apiParam {string} scids 供应商ID多选
 * @apiParam {string} sids 仓库ID多选
 * @apiParam {string} date *日期，格式2015-10-23
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 * @apiParam {string} orderby 排序,例如：all_amount^desc,real_amount^asc
 *
 * @apiSuccess {string} id 供应商ID
 * @apiSuccess {string} name 供应商名称
 * @apiSuccess {string} all_amount 应付金额
 * @apiSuccess {string} all_total 应付单据
 * @apiSuccess {string} exp_amount 到期应付金额
 * @apiSuccess {string} exp_total 到期应付单据
 * @apiSuccess {string} real_amount 实付金额
 * @apiSuccess {string} real_total 实付单据
 *
 */

/**
 * @api {post} f_finance/settle 日结算报表
 * @apiName f_finance/settle
 * @apiGroup FFinance
 * @apiVersion 0.0.1
 * @apiDescription 日结算报表
 *
 * @apiParam {string} pay_types 支付方式ID，多选
 * @apiParam {string} suid 业务员ID
 * @apiParam {string} date *日期，格式2015-10-23
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {string} suid 业务员ID
 * @apiSuccess {string} suname 业务员姓名
 * @apiSuccess {string} amount 实结金额
 * @apiSuccess {string} discount_amount 优惠金额
 * @apiSuccess {string} tax_amount 税额
 * @apiSuccess {string} pay_type 支付方式
 *
 */

/**
 * @api {post} f_finance/stock_out 出库单汇总查询
 * @apiName f_finance/stock_out
 * @apiGroup FFinance
 * @apiVersion 0.0.1
 * @apiDescription 出库单汇总查询
 *
 * @apiParam {int} sid 仓库ID
 * @apiParam {int} status 单据状态
 * @apiParam {int} settle_status 单据结算状态
 * @apiParam {int} ccid 客户ID
 * @apiParam {int} suid 业务员ID
 * @apiParam {string} begin_date 起始日期
 * @apiParam {string} end_date 截止日期
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {string} id 出库单号
 * @apiSuccess {string} order_id 订单号
 * @apiSuccess {int} cid 公司ID
 * @apiSuccess {string} cname 公司名称
 * @apiSuccess {int} sid 仓库ID
 * @apiSuccess {string} sname 仓库名称
 * @apiSuccess {int} status 出库单状态 1-未审核 2-已预审 3-已审核 4-已结算 9-已取消 10-已冲正 11-冲正单 12-已修正 13-修正单
 * @apiSuccess {int} type 出库单类型 1-销售 2-退货 3-调拨 4-报损 5-盘亏
 * @apiSuccess {string} memo 出库单备注
 * @apiSuccess {string} createtime 创建时间
 * @apiSuccess {string} updatetime 上次更新时间
 * @apiSuccess {string} checktime 审核时间
 * @apiSuccess {int} uid 填单人ID
 * @apiSuccess {string} uname 填单人姓名
 * @apiSuccess {int} cuid 审核员ID
 * @apiSuccess {string} cuname 审核员姓名
 * @apiSuccess {int} suid 业务员ID
 * @apiSuccess {string} suname 业务员姓名
 * @apiSuccess {int} ruid 结算员ID
 * @apiSuccess {string} runame 结算员姓名
 * @apiSuccess {int} settle_id 结算单号
 * @apiSuccess {int} pay_type 付款方式
 * @apiSuccess {string} lastdate 最终结算日
 * @apiSuccess {string} amount 入库单总金额
 * @apiSuccess {string} tax_amount 总税额
 * @apiSuccess {string} in_cname 客户名称
 * @apiSuccess {int} in_cid 客户公司ID
 *
 */

/**
 * @api {post} f_finance/commission 提成结算报表
 * @apiName f_finance/commission
 * @apiGroup FFinance
 * @apiVersion 0.0.1
 * @apiDescription 提成结算报表
 *
 * @apiParam {int} belong 所属关系 1-自有 2-外借
 * @apiParam {int} suid 业务员ID
 * @apiParam {string} begin_date 开始日期
 * @apiParam {string} end_date 截止日期
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {string} suid 业务员ID
 * @apiSuccess {string} suname 业务员姓名
 * @apiSuccess {string} amount 单据金额
 * @apiSuccess {string} commission_amount 应结金额
 * @apiSuccess {string} commission_real_amount 实结税额
 *
 */

function f_finance($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    switch($action){
        case 'debit':
            //init_log_oper($action, '应收款查询');
            param_need($data, ['date']);
            $data = format_data_ids($data, ['ccids','sids']);
            $so_model = new StockOut();
            $data['cid'] = $cid;
            $res = $so_model->form_debit($data);

            if(get_value($data, 'download') == 'excel'){
                $excel_data = [];
                $excel_data[] = ['客户名称','应收单数','应收款','到期应收单数','到期应收款','已收单数','已收款'];
                foreach($res['data'] as $val){
                    $excel_data[] = [$val['name'],$val['all_total'],$val['all_amount'],$val['exp_total'],$val['exp_amount'],
                        $val['real_total'],$val['real_amount']];
                }
                $excel_data[] = ['总计',$res['add_up']['all_total'],$res['add_up']['all_amount'],
                    $res['add_up']['exp_total'],$res['add_up']['exp_amount'],$res['add_up']['real_total'],$res['add_up']['real_amount']];
                write_excel($excel_data, '应收款查询('.date('Y-m-d').')');
            }

            success($res);
            break;
        case 'payment':
            //init_log_oper($action, '应付款查询');
            $data = format_data_ids($data, ['scids','sids']);
            param_need($data, ['date']);
            $si_model = new StockIn();
            $data['cid'] = $cid;
            $res = $si_model->form_payment($data);

            if(get_value($data, 'download') == 'excel'){
                $excel_data = [];
                $excel_data[] = ['供应商名称','应付单数','应付款','到期应付单数','到期应付款','已付单数','已付款'];
                foreach($res['data'] as $val){
                    $excel_data[] = [$val['name'],$val['all_total'],$val['all_amount'],$val['exp_total'],$val['exp_amount'],
                        $val['real_total'],$val['real_amount']];
                }
                $excel_data[] = ['总计',$res['add_up']['all_total'],$res['add_up']['all_amount'],
                    $res['add_up']['exp_total'],$res['add_up']['exp_amount'],$res['add_up']['real_total'],$res['add_up']['real_amount']];
                write_excel($excel_data, '应付款查询('.date('Y-m-d').')');
            }

            success($res);
            break;
        case 'settle':
            //init_log_oper($action, '日结算报表');
            $data = format_data_ids($data, ['pay_types']);
            param_need($data, ['date']);
            $so_model = new StockOut();
            $data['cid'] = $cid;
            $res = $so_model->form_settle($data);

            if(get_value($data, 'download') == 'excel'){
                $excel_data = [];
                $excel_data[] = ['业务员','支付方式','应结金额','优惠金额','实结金额','税额'];
                foreach($res['data'] as $val){
                    $excel_data[] = [$val['suname'],number_to_name($val['pay_type'],'pay_type'),price_add($val['discount_amount'],$val['amount']),$val['discount_amount'],
                        $val['amount'],$val['tax_amount']];
                }
                $excel_data[] = ['总计','',price_add($res['add_up']['discount_amount'],$res['add_up']['amount']),
                    $res['add_up']['discount_amount'],$res['add_up']['amount'],$res['add_up']['tax_amount']];
                write_excel($excel_data, '结算日对账查询('.date('Y-m-d').')');
            }

            success($res);
            break;

        case 'stock_out':
            //param_need($data, ['check_begin_date','check_end_date']);	//判断参数是否必填
            param_check($data, ['page' => "/^\d+$/",'page_num' => "/^\d+$/"]);

            $my_model = new StockOut();
            //init_log_oper($action, '出库单汇总查询');
            //默认加上公司内的单据条件
            $data['cid'] = $cid;
            $data['type'] = 1;

            if(!get_value($data, 'status')){
                $data['status'] = [3,4];
            }


            if(get_value($data,'check_begin_date')){
                $data['checktime[>=]'] = $data['check_begin_date']. ' 00:00:00';
            }
            if(get_value($data,'check_end_date')) {
                $data['checktime[<=]'] = $data['check_end_date'] . ' 23:59:59';
            }
            //$data['settle_status'] = 1;

            $settle_begin_date = get_value($data, 'settle_begin_date');
            if($settle_begin_date){
                $data['settletime[>=]'] = $settle_begin_date. ' 00:00:00';
            }
            $settle_end_date = get_value($data, 'settle_end_date');
            if($settle_end_date){
                $data['settletime[<=]'] = $settle_end_date. '23:59:59';
            }

            $res = $my_model -> form_stock_out($data);

            foreach($res['data'] as $key=>$val){
                $res['data'][$key]['delay_days'] = '';
                if(!$val['settle_status'] && $val['lastdate'] && $val['lastdate']<=date('Y-m-d')){
                    $res['data'][$key]['delay_days'] = days_sub(date('Y-m-d'), $val['lastdate']);
                }
            }

            success($res);
            break;

        case 'commission':
            //提成单汇总
            param_need($data, ['begin_date','end_date']);	//判断参数是否必填  belong
            param_check($data, ['page' => "/^\d+$/",'page_num' => "/^\d+$/"]);

            $data['cid'] = $cid;
            $c_model = new Commission();
            $res = $c_model -> form_salesman($data);

            if(get_value($data, 'download') == 'excel'){
                $excel_data = [];
                $excel_data[] = ['业务员','单据金额','应提金额','实提金额'];
                foreach($res['data'] as $val){
                    $excel_data[] = [$val['suname'],$val['amount'],$val['commission_amount'],$val['commission_real_amount']];
                }
                $excel_data[] = ['总计',$res['add_up']['amount'],$res['add_up']['commission_amount'],
                    $res['add_up']['commission_real_amount']];
                write_excel($excel_data, '提成结算报表('.date('Y-m-d').')');
            }

            success($res);
            break;

        case 'commission_goods':
            //提成单商品汇总
            param_need($data, ['begin_date','end_date','suid']);	//判断参数是否必填  belong
            param_check($data, ['page' => "/^\d+$/",'page_num' => "/^\d+$/"]);

            $data['cid'] = $cid;
            $c_model = new Commission();
            $res = $c_model -> form_goods($data);

            if(get_value($data, 'download') == 'excel'){
                $excel_data = [];
                $excel_data[] = ['商品名称','商品条码','商品规格','商品单位','单据金额','应提金额','实提金额'];
                foreach($res['data'] as $val){
                    $excel_data[] = [$val['gname'],$val['gbarcode'],$val['gspec'],$val['gunit'],$val['amount'],$val['commission_amount'],$val['commission_real_amount']];
                }
                $excel_data[] = ['总计','','','',$res['add_up']['amount'],$res['add_up']['commission_amount'],
                    $res['add_up']['commission_real_amount']];
                write_excel($excel_data, '提成结算商品报表('.date('Y-m-d').')');
            }

            success($res);
            break;

        case 'real_back':
            //实际回款明细报表
            param_need($data, ['begin_date','end_date','sid']);  //belong
            param_check($data, ['page' => "/^\d+$/",'page_num' => "/^\d+$/"]);
            $data['cid'] = $cid;

            $so_model = new StockOut();
            $res = $so_model -> form_real_back($data);

            if(get_value($data, 'download') == 'excel'){
                $excel_data = [];
                $excel_data[] = ['业务员','客户名称','商品','数量','金额'];
                foreach($res['data'] as $val){
                    $excel_data[] = [$val['suname'],$val['ccname'],$val['gname'],$val['total'],$val['amount']];
                }
                $excel_data[] = ['总计','','',$res['add_up']['total'],$res['add_up']['amount']];
                write_excel($excel_data, '实际回款明细报表('.date('Y-m-d').')');
            }

            success($res);
            break;

        default:
            error(1100);
    }

}
