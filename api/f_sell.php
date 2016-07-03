<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * f_sell 销售类报表
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} f_sell/salesman 查看业务员业绩
 * @apiName f_sell/salesman
 * @apiGroup FSell
 * @apiVersion 0.0.1
 * @apiDescription 查看业务员业绩
 *
 * @apiParam {string} sids 仓库ID
 * @apiParam {string} begindate *开始日期，格式2015-10-23
 * @apiParam {string} enddate *截止日期，格式2015-10-23
 * @apiParam {string} suids 业务员ID
 * @apiParam {string} status 单据状态
 * @apiParam {string} orderby 排序,例如：amount^desc,order_count^asc,customer_count^asc
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {string} suid 业务员ID
 * @apiSuccess {string} suname 业务员姓名
 * @apiSuccess {string} amount 业务员业绩
 * @apiSuccess {string} order_count 业务员单数
 * @apiSuccess {string} customer_count 业务员客户数
 * @apiSuccess {string} return_amount 退货金额
 * @apiSuccess {string} return_order_count 退货个数
 * @apiSuccess {string} sub_amount 实际金额
 * @apiSuccess {string} sub_order_count 实际个数
 *
 */

/**
 * @api {post} f_sell/customer 查看客户排名
 * @apiName f_sell/customer
 * @apiGroup FSell
 * @apiVersion 0.0.1
 * @apiDescription 查看客户排名
 *
 * @apiParam {string} sids 仓库ID
 * @apiParam {string} suids 业务员ID
 * @apiParam {string} cctypes 客户类型
 * @apiParam {string} status 单据状态
 * @apiParam {string} begindate *开始日期，格式2015-10-23
 * @apiParam {string} enddate *截止日期，格式2015-10-23
 * @apiParam {string} orderby 排序,例如：amount^desc,order_count^asc
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {string} id 客户ID
 * @apiSuccess {string} name 客户名称
 * @apiSuccess {string} amount 客户金额
 * @apiSuccess {string} order_count 客户单数
 * @apiSuccess {string} return_amount 退货金额
 * @apiSuccess {string} return_order_count 退货个数
 * @apiSuccess {string} sub_amount 实际金额
 * @apiSuccess {string} sub_order_count 实际个数
 *
 */

/**
 * @api {post} f_sell/form_customer 查看客户报表
 * @apiName f_sell/form_customer
 * @apiGroup FSell
 * @apiVersion 0.0.1
 * @apiDescription 查看客户报表
 *
 * @apiParam {string} sids 仓库ID
 * @apiParam {string} suids 业务员ID
 * @apiParam {string} cctypes 客户类型
 * @apiParam {string} status 单据状态
 * @apiParam {string} begindate *开始日期，格式2015-10-23
 * @apiParam {string} enddate *截止日期，格式2015-10-23
 * @apiParam {string} orderby 排序,例如：amount^desc,order_count^asc
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {string} id 客户ID
 * @apiSuccess {string} name 客户名称
 * @apiSuccess {string} amount 客户金额
 * @apiSuccess {string} order_count 客户单数
 * @apiSuccess {string} return_amount 退货金额
 * @apiSuccess {string} return_order_count 退货个数
 * @apiSuccess {string} sub_amount 实际金额
 * @apiSuccess {string} sub_order_count 实际个数
 *
 */

/**
 * @api {post} f_sell/goods 查看商品排名
 * @apiName f_sell/goods
 * @apiGroup FSell
 * @apiVersion 0.0.1
 * @apiDescription 查看商品排名
 *
 * @apiParam {string} sids 仓库ID
 * @apiParam {string} suids 业务员ID
 * @apiParam {string} tids 商品分类ID
 * @apiParam {string} begindate *开始日期，格式2015-10-23
 * @apiParam {string} enddate *截止日期，格式2015-10-23
 * @apiParam {string} status 单据状态
 * @apiParam {string} orderby 排序,例如：amount^desc,order_count^asc
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {string} gid 商品ID
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {string} gcode 商品编码
 * @apiSuccess {string} gbarcode 商品条码
 * @apiSuccess {string} gtid 商品分类ID
 * @apiSuccess {string} gtname 商品分类名称
 * @apiSuccess {string} gunit 商品计量单位
 * @apiSuccess {string} gspec 商品规格
 * @apiSuccess {string} amount 商品金额
 * @apiSuccess {string} order_count 商品个数
 * @apiSuccess {string} return_amount 退货金额
 * @apiSuccess {string} return_order_count 退货个数
 * @apiSuccess {string} sub_amount 实际金额
 * @apiSuccess {string} sub_order_count 实际个数
 *
 */

/**
 * @api {post} f_sell/form_goods 查看商品报表
 * @apiName f_sell/form_goods
 * @apiGroup FSell
 * @apiVersion 0.0.1
 * @apiDescription 查看商品报表
 *
 * @apiParam {string} sids 仓库ID
 * @apiParam {string} suids 业务员ID
 * @apiParam {string} tids 商品分类ID
 * @apiParam {string} begindate *开始日期，格式2015-10-23
 * @apiParam {string} enddate *截止日期，格式2015-10-23
 * @apiParam {string} status 单据状态
 * @apiParam {string} orderby 排序,例如：amount^desc,order_count^asc
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {string} gid 商品ID
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {string} gcode 商品编码
 * @apiSuccess {string} gbarcode 商品条码
 * @apiSuccess {string} gtid 商品分类ID
 * @apiSuccess {string} gtname 商品分类名称
 * @apiSuccess {string} gunit 商品计量单位
 * @apiSuccess {string} gspec 商品规格
 * @apiSuccess {string} amount 商品金额
 * @apiSuccess {string} order_count 商品个数
 * @apiSuccess {string} return_amount 退货金额
 * @apiSuccess {string} return_order_count 退货个数
 * @apiSuccess {string} sub_amount 实际金额
 * @apiSuccess {string} sub_order_count 实际个数
 *
 */

/**
 * @api {post} f_sell/balance 查看日对账
 * @apiName f_sell/balance
 * @apiGroup FSell
 * @apiVersion 0.0.1
 * @apiDescription 查看日对账
 *
 * @apiParam {string} sids 仓库ID
 * @apiParam {string} paytypes 付款方式
 * @apiParam {string} date *日期，格式2015-10-23
 * @apiParam {string} status 单据状态
 * @apiParam {string} orderby 排序,例如：amount^desc,order_count^asc
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {string} id 仓库ID
 * @apiSuccess {string} name 仓库名称
 * @apiSuccess {string} amount 销售金额
 * @apiSuccess {string} order_count 销售单数
 * @apiSuccess {string} cost_amount 销售成本额
 * @apiSuccess {string} profit_amount 销售毛利额
 * @apiSuccess {string} profit_percent 销售毛利率
 * @apiSuccess {string} return_amount 退货金额
 * @apiSuccess {string} return_cost_amount 退货成本额
 * @apiSuccess {string} return_profit_amount 退货毛利额
 * @apiSuccess {string} return_profit_percent 退货毛利率
 * @apiSuccess {string} return_order_count 退货单数
 *
 */

/**
 * @api {post} f_sell/form_balance 查看日对账报表
 * @apiName f_sell/form_balance
 * @apiGroup FSell
 * @apiVersion 0.0.1
 * @apiDescription 查看日对账报表
 *
 * @apiParam {string} sids 仓库ID
 * @apiParam {string} paytypes 付款方式
 * @apiParam {string} date *日期，格式2015-10-23
 * @apiParam {string} status 单据状态
 * @apiParam {string} orderby 排序,例如：amount^desc,order_count^asc
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {string} id 仓库ID
 * @apiSuccess {string} name 仓库名称
 * @apiSuccess {string} amount 销售金额
 * @apiSuccess {string} order_count 销售单数
 * @apiSuccess {string} cost_amount 销售成本额
 * @apiSuccess {string} profit_amount 销售毛利额
 * @apiSuccess {string} profit_percent 销售毛利率
 * @apiSuccess {string} return_amount 退货金额
 * @apiSuccess {string} return_cost_amount 退货成本额
 * @apiSuccess {string} return_profit_amount 退货毛利额
 * @apiSuccess {string} return_profit_percent 退货毛利率
 * @apiSuccess {string} return_order_count 退货单数
 *
 */

function f_sell($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    switch($action){
        case 'salesman':
            //init_log_oper($action, '业务员业绩查询');
            param_need($data, ['begindate', 'enddate']);
            $data = format_data_ids($data, ['sids','suids']);
            $so_model = new StockOut($id);
            $data['cid'] = $cid;
            $res = $so_model->form_salesman($data);

            if(get_value($data, 'download') == 'excel'){
                $excel_data = [];
                $excel_data[] = ['业务员','销售客户数','总销售单数','总销售额','退货单数','退货金额','实际销售单数','实际销售额'];
                foreach($res['data'] as $val){
                    $excel_data[] = [$val['suname'],$val['customer_count'],$val['order_count'],$val['amount'],
                        $val['return_order_count'],$val['return_amount'],$val['sub_order_count'],$val['sub_amount']];
                }
                $excel_data[] = ['总计','',$res['add_up']['order_count'],$res['add_up']['amount'],$res['add_up']['return_order_count'],
                    $res['add_up']['return_amount'],$res['add_up']['sub_order_count'],$res['add_up']['sub_amount']];
                write_excel($excel_data, '业务员业绩查询('.date('Y-m-d').')');
            }

            success($res);
            break;

        case 'salesman_goods':
            //init_log_oper($action, '业务员单品铺货查询');
            param_need($data, ['gid','begin_date','end_date']);
            $so_model = new StockOut($id);
            $data['cid'] = $cid;
            $res = $so_model->form_salesman_goods($data);

            if(get_value($data, 'download') == 'excel'){
                $excel_data = [];
                $excel_data[] = ['客户名称','业务员','净销数','净销额'];
                foreach($res['data'] as $val){
                    $excel_data[] = [$val['ccname'],$val['suname'],$val['total'],$val['amount']];
                }
                $excel_data[] = ['总计','',$res['add_up']['total'],$res['add_up']['amount']];
                write_excel($excel_data, '业务员单品铺货查询('.date('Y-m-d').')');
            }

            success($res);
            break;

        case 'goods_salesman':
            //init_log_oper($action, '业务员单品销量查询');
            param_need($data, ['begin_date','end_date']);
            $data = format_data_ids($data, ['sids']);
            $so_model = new StockOut($id);
            $data['cid'] = $cid;
            $res = $so_model->form_goods_salesman($data);

            if(get_value($data, 'download') == 'excel'){
                $excel_data = [];
                $excel_data[] = ['商品名称','规格','业务员','订单商品金额','订单商品数量','订单商品箱数','任务箱数','任务完成率','订单赠品数','订单赠品箱数'];
                foreach($res['data'] as $val){
                    $excel_data[] = [$val['gname'],$val['gspec'],$val['suname'],$val['amount'],$val['total'],
                        $val['box_total'],$val['task_total'],$val['complete_rate'],$val['free_total'],$val['free_box_total']];
                }
                $excel_data[] = ['总计','','',$res['add_up']['amount'],$res['add_up']['total'],'',$res['add_up']['task_total'],'',
                    $res['add_up']['free_total'],''];
                write_excel($excel_data, '业务员单品订量查询('.date('Y-m-d').')');
            }

            success($res);
            break;

        case 'customer':
            //init_log_oper($action, '客户销量查询');
            param_need($data, ['begindate', 'enddate']);
            $data = format_data_ids($data, ['sids','suids','cctypes']);
            $so_model = new StockOut($id);
            $data['cid'] = $cid;
            $res = $so_model->form_customer($data);
            success($res);
            break;

        case 'goods':
            //init_log_oper($action, '商品销量查询');
            param_need($data, ['begindate', 'enddate']);
            $data = format_data_ids($data, ['sids','suids','gtids']);
            $so_model = new StockOut($id);
            $data['cid'] = $cid;
            $res = $so_model->form_goods($data);
            success($res);
            break;

        case 'balance':
            //init_log_oper($action, '日对账查询');
            param_need($data, ['date']);
            $data = format_data_ids($data, ['sids','suids','cctypes']);
            $so_model = new StockOut($id);
            $data['cid'] = $cid;
            $res = $so_model->form_balance($data);
            success($res);
            break;

        case 'form_customer':
            //init_log_oper($action, '查看客户报表');
            //sids,cctypes,begin_date,end_date,suid

            param_need($data, ['begin_date', 'end_date']);
            $data = format_data_ids($data, ['sids','cctypes']);

            $data['cid'] = $cid;
            $fsc_model = new FSellCustomer();
            $res = $fsc_model->my_form($data);

            if(get_value($data, 'download') == 'excel'){
                $excel_data = [];
                $excel_data[] = ['客户名称','实销数','实销额','实销成本','实销毛利','毛利率','退货数','退货额','退货成本','退货毛利'];
                foreach($res['data'] as $val){
                    $excel_data[] = [$val['ccname'],$val['sell_total'],$val['sell_amount'],$val['sell_cost_amount'],
                        $val['sell_profit_amount'],$val['sell_profit_percent'],$val['return_total'],$val['return_amount'],
                        $val['return_cost_amount'],$val['return_profit_amount']];
                }
                $excel_data[] = ['总计',$res['add_up']['sell_total'],$res['add_up']['sell_amount'],$res['add_up']['sell_cost_amount'],
                    $res['add_up']['sell_profit_amount'],'',$res['add_up']['return_total'],
                    $res['add_up']['return_amount'], $res['add_up']['return_cost_amount'],$res['add_up']['return_profit_amount']];
                write_excel($excel_data, '客户销量报表('.date('Y-m-d').')');
            }

            success($res);
            break;

        case 'form_customer_goods':
            //init_log_oper($action, '查看客户商品报表');
            //sids,cctypes,begin_date,end_date,suid

            param_need($data, ['begin_date', 'end_date', 'ccid']);
            $data = format_data_ids($data, ['sids','gtids']);

            $data['cid'] = $cid;
            $fsc_model = new FSellCustomer();
            $res = $fsc_model->my_goods_form($data);

            if(get_value($data, 'download') == 'excel'){
                $excel_data = [];
                $excel_data[] = ['商品编码','商品名称','商品条码','单位','规格','实销数','实销额','实销成本','实销毛利','毛利率','退货数','退货额','退货成本','退货毛利'];
                foreach($res['data'] as $val){
                    $excel_data[] = [$val['gcode'],$val['gname'],$val['gbarcode'],$val['gunit'],$val['gspec'],$val['sell_total'],
                        $val['sell_amount'],$val['sell_cost_amount'],$val['sell_profit_amount'],$val['sell_profit_percent'],
                        $val['return_total'],$val['return_amount'],$val['return_cost_amount'],$val['return_profit_amount']];
                }
                $excel_data[] = ['总计','','','','',$res['add_up']['sell_total'],$res['add_up']['sell_amount'],$res['add_up']['sell_cost_amount'],
                    $res['add_up']['sell_profit_amount'],'',$res['add_up']['return_total'],
                    $res['add_up']['return_amount'], $res['add_up']['return_cost_amount'],$res['add_up']['return_profit_amount']];
                write_excel($excel_data, '客户商品销量报表('.date('Y-m-d').')');
            }

            success($res);
            break;

        case 'form_supplier':
            //init_log_oper($action, '查看供应商报表');
            //sids,begin_date,end_date
            param_need($data, ['begin_date', 'end_date']);
            $data = format_data_ids($data, ['sids']);

            $data['cid'] = $cid;
            $fss_model = new FSellSupplier();
            $res = $fss_model->my_form($data);

            if(get_value($data, 'download') == 'excel'){
                $excel_data = [];
                $excel_data[] = ['供应商名称','实销数','实销额','实销成本','实销毛利','毛利率','退货数','退货额','退货成本','退货毛利'];
                foreach($res['data'] as $val){
                    $excel_data[] = [$val['scname'],$val['sell_total'],$val['sell_amount'],$val['sell_cost_amount'],
                        $val['sell_profit_amount'],$val['sell_profit_percent'],$val['return_total'],$val['return_amount'],
                        $val['return_cost_amount'],$val['return_profit_amount']];
                }
                $excel_data[] = ['总计',$res['add_up']['sell_total'],$res['add_up']['sell_amount'],$res['add_up']['sell_cost_amount'],
                    $res['add_up']['sell_profit_amount'],'',$res['add_up']['return_total'],
                    $res['add_up']['return_amount'], $res['add_up']['return_cost_amount'],$res['add_up']['return_profit_amount']];
                write_excel($excel_data, '供应商销量报表('.date('Y-m-d').')');
            }

            success($res);
            break;

        case 'form_supplier_goods':
            //init_log_oper($action, '查看供应商商品报表');
            //sids,begin_date,end_date
            param_need($data, ['begin_date', 'end_date', 'scid']);
            $data = format_data_ids($data, ['sids', 'gtids']);

            $data['cid'] = $cid;
            $fss_model = new FSellSupplier();
            $res = $fss_model->my_goods_form($data);

            if(get_value($data, 'download') == 'excel'){
                $excel_data = [];
                $excel_data[] = ['商品编码','商品名称','商品条码','单位','规格','实销数','实销额','实销成本','实销毛利','毛利率','退货数','退货额','退货成本','退货毛利'];
                foreach($res['data'] as $val){
                    $excel_data[] = [$val['gcode'],$val['gname'],$val['gbarcode'],$val['gunit'],$val['gspec'],$val['sell_total'],
                        $val['sell_amount'],$val['sell_cost_amount'],$val['sell_profit_amount'],$val['sell_profit_percent'],
                        $val['return_total'],$val['return_amount'],$val['return_cost_amount'],$val['return_profit_amount']];
                }
                $excel_data[] = ['总计','','','','',$res['add_up']['sell_total'],$res['add_up']['sell_amount'],$res['add_up']['sell_cost_amount'],
                    $res['add_up']['sell_profit_amount'],'',$res['add_up']['return_total'],
                    $res['add_up']['return_amount'], $res['add_up']['return_cost_amount'],$res['add_up']['return_profit_amount']];
                write_excel($excel_data, '供应商商品销量报表('.date('Y-m-d').')');
            }

            success($res);
            break;

        case 'form_goods':
            //init_log_oper($action, '查看商品报表');
            //sids,gtids,begin_date,end_date
            $detail = get_value($data, 'detail');
            if($detail == 1){
                param_need($data, ['begin_date', 'end_date', 'gid']);
                $data = format_data_ids($data, ['sids']);
                $data['cid'] = $cid;
                $fsg_model = new FSellSupplier();
                $res = $fsg_model->my_goods_form_detail($data);

                if(get_value($data, 'download') == 'excel'){
                    $excel_data = [];
                    $excel_data[] = ['日期','商品编码','商品名称','商品条码','单位','规格','实销数','实销额','实销成本','实销毛利','毛利率','退货数','退货额','退货成本','退货毛利'];
                    foreach($res['data'] as $val){
                        $excel_data[] = [$val['date'],$val['gcode'],$val['gname'],$val['gbarcode'],$val['gunit'],$val['gspec'],$val['sell_total'],
                            $val['sell_amount'],$val['sell_cost_amount'],$val['sell_profit_amount'],$val['sell_profit_percent'],
                            $val['return_total'],$val['return_amount'],$val['return_cost_amount'],$val['return_profit_amount']];
                    }
                    $excel_data[] = ['总计','','','','','',$res['add_up']['sell_total'],$res['add_up']['sell_amount'],$res['add_up']['sell_cost_amount'],
                        $res['add_up']['sell_profit_amount'],'',$res['add_up']['return_total'],
                        $res['add_up']['return_amount'], $res['add_up']['return_cost_amount'],$res['add_up']['return_profit_amount']];
                    write_excel($excel_data, '商品销量报表-单品明细('.date('Y-m-d').')');
                }
            }
            else{
                param_need($data, ['begin_date', 'end_date']);
                $data = format_data_ids($data, ['sids','gtids']);

                $data['cid'] = $cid;
                $fsg_model = new FSellSupplier();
                $res = $fsg_model->my_goods_form($data);

                if(get_value($data, 'download') == 'excel'){
                    $excel_data = [];
                    $excel_data[] = ['商品编码','商品名称','商品条码','单位','规格','实销数','实销额','实销成本','实销毛利','毛利率','退货数','退货额','退货成本','退货毛利'];
                    foreach($res['data'] as $val){
                        $excel_data[] = [$val['gcode'],$val['gname'],$val['gbarcode'],$val['gunit'],$val['gspec'],$val['sell_total'],
                            $val['sell_amount'],$val['sell_cost_amount'],$val['sell_profit_amount'],$val['sell_profit_percent'],
                            $val['return_total'],$val['return_amount'],$val['return_cost_amount'],$val['return_profit_amount']];
                    }
                    $excel_data[] = ['总计','','','','',$res['add_up']['sell_total'],$res['add_up']['sell_amount'],$res['add_up']['sell_cost_amount'],
                        $res['add_up']['sell_profit_amount'],'',$res['add_up']['return_total'],
                        $res['add_up']['return_amount'], $res['add_up']['return_cost_amount'],$res['add_up']['return_profit_amount']];
                    write_excel($excel_data, '商品销量报表('.date('Y-m-d').')');
                }
            }

            success($res);
            break;

        case 'form_balance':
            //init_log_oper($action, '查看日对账报表');
            //date
            param_need($data, ['date']);
            $so_model = new FSellCustomer($id);
            $data['cid'] = $cid;
            $res = $so_model->my_form_balance($data);

            if(get_value($data, 'download') == 'excel'){
                $excel_data = [];
                $excel_data[] = ['仓库名称','实销数','实销额','实销成本','实销毛利','毛利率','退货数','退货额','退货成本','退货毛利'];
                foreach($res['data'] as $val){
                    $excel_data[] = [$val['sname'],$val['sell_total'],$val['sell_amount'],$val['sell_cost_amount'],
                        $val['sell_profit_amount'],$val['sell_profit_percent'],$val['return_total'],$val['return_amount'],
                        $val['return_cost_amount'],$val['return_profit_amount']];
                }
                $excel_data[] = ['总计',$res['add_up']['sell_total'],$res['add_up']['sell_amount'],$res['add_up']['sell_cost_amount'],
                    $res['add_up']['sell_profit_amount'],$res['add_up']['sell_profit_percent'],$res['add_up']['return_total'],
                    $res['add_up']['return_amount'],$res['add_up']['return_cost_amount'],$res['add_up']['return_profit_amount']];
                write_excel($excel_data, '日对账报表('.date('Y-m-d').')');
            }

            success($res);
            break;

        default:
            error(1100);
    }

}
