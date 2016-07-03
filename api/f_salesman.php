<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * f_salesman 业务员类报表
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} f_salesman/order_rate 业务员订单达成率
 * @apiName f_salesman/order_rate
 * @apiGroup FSalesman
 * @apiVersion 0.0.1
 * @apiDescription 业务员订单达成率
 *
 * @apiParam {int} sids 仓库ID，多选
 * @apiParam {int} belong 所属关系 1-自有 2-外借
 * @apiParam {int} suid 业务员ID
 * @apiParam {string} begin_date 开始日期
 * @apiParam {string} end_date 截止日期
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {string} suid 业务员ID
 * @apiSuccess {string} suname 业务员姓名
 * @apiSuccess {string} order_count 总订单数
 * @apiSuccess {string} checked_order_count 有效订单数
 * @apiSuccess {string} checked_stock_out_count 出货单数
 * @apiSuccess {string} rate1 订单有效率
 * @apiSuccess {string} rate2 出货率
 *
 */

/**
 * @api {post} f_salesman/goods 业务员单品铺货查询
 * @apiName f_salesman/goods
 * @apiGroup FSalesman
 * @apiVersion 0.0.1
 * @apiDescription 业务员单品铺货查询
 *
 * @apiParam {int} gid 商品ID
 * @apiParam {int} belong 所属关系 1-自有 2-外借
 * @apiParam {int} suid 业务员ID
 * @apiParam {string} begin_date 开始日期
 * @apiParam {string} end_date 截止日期
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {string} suid 业务员ID
 * @apiSuccess {string} suname 业务员姓名
 * @apiSuccess {string} ccid 客户ID
 * @apiSuccess {string} ccname 客户名称
 * @apiSuccess {string} total 净销数
 * @apiSuccess {string} box_total 净销箱数
 * @apiSuccess {string} amount 净销额
 * @apiSuccess {string} free_total 赠品数
 * @apiSuccess {string} free_box_total 赠品箱数
 *
 */

/**
 * @api {post} f_salesman/task_rate 业务员业绩查询
 * @apiName f_salesman/task_rate
 * @apiGroup FSalesman
 * @apiVersion 0.0.1
 * @apiDescription 业务员业绩查询
 *
 * @apiParam {int} sids 仓库ID，多选
 * @apiParam {int} belong 所属关系 1-自有 2-外借
 * @apiParam {int} suid 业务员ID
 * @apiParam {string} date 日期
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {string} suid 业务员ID
 * @apiSuccess {string} suname 业务员姓名
 * @apiSuccess {string} all_customer_count 总有效客户数
 * @apiSuccess {string} day_customer_count 日新增有效客户数
 * @apiSuccess {string} day_order_amount 日订单额
 * @apiSuccess {string} day_order_count 日订单数
 * @apiSuccess {string} period_customer_count 月新增客户数
 * @apiSuccess {string} period_order_amount 月订单额
 * @apiSuccess {string} period_order_count 月订单数
 * @apiSuccess {string} period_box_total 月销售箱数
 * @apiSuccess {string} task_total 月任务箱数
 * @apiSuccess {string} complete_rate 月箱数完成率
 * @apiSuccess {string} amount_task_total 月金额任务
 * @apiSuccess {string} amount_complete_rate 月金额完成率
 *
 */

/**
 * @api {post} f_salesman/task 业务员销售任务查询
 * @apiName f_salesman/task
 * @apiGroup FSalesman
 * @apiVersion 0.0.1
 * @apiDescription 业务员销售任务查询
 *
 * @apiParam {int} gid 商品ID
 * @apiParam {int} belong 所属关系 1-自有 2-外借
 * @apiParam {int} suid 业务员ID
 * @apiParam {string} year 年份
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {string} suid 业务员ID
 * @apiSuccess {string} suname 业务员姓名
 * @apiSuccess {string} gid 商品ID
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {string} val1 1月任务
 * @apiSuccess {string} val2 2月任务
 * @apiSuccess {string} val3 3月任务
 * @apiSuccess {string} val4 4月任务
 * @apiSuccess {string} val5 5月任务
 * @apiSuccess {string} val6 6月任务
 * @apiSuccess {string} val7 7月任务
 * @apiSuccess {string} val8 8月任务
 * @apiSuccess {string} val9 9月任务
 * @apiSuccess {string} val10 10月任务
 * @apiSuccess {string} val11 11月任务
 * @apiSuccess {string} val12 12月任务
 * @apiSuccess {string} val_all 年任务
 *
 */

/**
 * @api {post} f_salesman/geo 业务员客户维护记录
 * @apiName f_salesman/geo
 * @apiGroup FSalesman
 * @apiVersion 0.0.1
 * @apiDescription 业务员客户维护记录
 *
 * @apiParam {int} source 维护类型 1-老客户拜访 2-新客户申请 3-意见收集 4-照片收集
 * @apiParam {int} suid 业务员ID
 * @apiParam {int} ccid 客户ID
 * @apiParam {string} begin_date 开始日期
 * @apiParam {string} end_date 截止日期
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {string} suid 业务员ID
 * @apiSuccess {string} suname 业务员姓名
 * @apiSuccess {string} source 维护类型
 * @apiSuccess {string} ccid 客户ID
 * @apiSuccess {string} ccname 客户名称
 * @apiSuccess {string} memo 客户意见收集
 * @apiSuccess {string} pic_url 客户照片地址
 * @apiSuccess {string} baidu_address 客户地址信息
 * @apiSuccess {string} baidu_latitude 客户纬度
 * @apiSuccess {string} baidu_longgitude 客户经度
 * @apiSuccess {string} createtime 签到时间
 *
 */

function f_salesman($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    switch($action){
        case 'order_rate':
            //init_log_oper($action, '订单达成率');
            param_need($data, ['begin_date','end_date']);//suid,sids
            $data = format_data_ids($data, ['sids']);
            $o_model = new Order();
            $data['cid'] = $cid;
            $detail = get_value($data, 'detail');
            if($detail){
                $res = $o_model->form_order_rate_detail($data);
                if(get_value($data, 'download') == 'excel'){
                    $excel_data = [];
                    $excel_data[] = ['订单号','下单时间','出货单号','出货单状态','延期天数','客户名称','紧急度','回访记录'];
                    foreach($res['data'] as $val){
                        $excel_data[] = [$val['order_id'],$val['order_time'],$val['stock_out_id'],number_to_name($val['stock_out_status'],'stock_out_status'),
                            $val['delay_days'],$val['in_cname'],number_to_name($val['rank'],'rank'),$val['visit_memo']];
                    }
                    write_excel($excel_data, '业务员订单达成率-明细('.date('Y-m-d').')');
                }
            }
            else{
                $res = $o_model->form_order_rate($data);

                if(get_value($data, 'download') == 'excel'){
                    $excel_data = [];
                    $excel_data[] = ['业务员','总订单数','有效订单数','出货单数','订单有效率','出货率'];
                    foreach($res['data'] as $val){
                        $excel_data[] = [$val['suname'],$val['order_count'],$val['checked_order_count'],$val['checked_stock_out_count'],
                            $val['rate1'],$val['rate2']];
                    }
                    $excel_data[] = ['总计',$res['add_up']['order_count'],$res['add_up']['checked_order_count'],
                        $res['add_up']['checked_stock_out_count'],$res['add_up']['rate1'],$res['add_up']['rate2']];
                    write_excel($excel_data, '业务员订单达成率('.date('Y-m-d').')');
                }
            }
            success($res);
            break;

        case 'goods':
            //init_log_oper($action, '业务员单品铺货查询');
            param_need($data, ['gid','begin_date','end_date']);
            $o_model = new Order($id);
            $data['cid'] = $cid;
            $res = $o_model->form_salesman_goods($data);

            if(get_value($data, 'download') == 'excel'){
                $excel_data = [];
                $excel_data[] = ['客户名称','业务员','净销数','净销箱数','净销额','赠品数','赠品箱数'];
                foreach($res['data'] as $val){
                    $excel_data[] = [$val['ccname'],$val['suname'],$val['total'],$val['box_total'],$val['amount'],$val['free_total'],$val['free_box_total']];
                }
                $excel_data[] = ['总计','',$res['add_up']['total'],$res['add_up']['box_total'],$res['add_up']['amount'],
                    $res['add_up']['free_total'],$res['add_up']['free_box_total']];
                write_excel($excel_data, '业务员单品铺货查询-订单('.date('Y-m-d').')');
            }

            success($res);
            break;

        case 'task_rate':
            //业务员任务达成情况
            param_need($data, ['date']); //sid,suid
            $data = format_data_ids($data, ['sids']);
            $t_model = new Task();
            $data['cid'] = $cid;
            $res = $t_model->form_salesman_task($data);

            if(get_value($data, 'download') == 'excel'){
                $excel_data = [];
                $excel_data[] = ['业务员','总客户数','有效客户数','日订单数','日销售额','日新增有效客户数','周期累计订单数','周期累计销售额',
                    '周期新增有效客户数','任务完成率(箱数)','周期累计箱数','任务总箱数','任务完成率(金额)','任务总金额'];
                foreach($res['data'] as $val){
                    $excel_data[] = [$val['suname'],$val['all_customer_count2'],$val['all_customer_count'],$val['day_order_count'],$val['day_order_amount'],
                        $val['day_customer_count'],$val['period_order_count'],$val['period_order_amount'],$val['period_customer_count'],
                        $val['complete_rate'],$val['period_box_total'],$val['task_total'],$val['amount_complete_rate'],$val['amount_task_total']];
                }
                $excel_data[] = ['总计',$res['add_up']['all_customer_count2'],$res['add_up']['all_customer_count'],$res['add_up']['day_order_count'],$res['add_up']['day_order_amount'],
                    $res['add_up']['day_customer_count'],$res['add_up']['period_order_count'],$res['add_up']['period_order_amount'],
                    $res['add_up']['period_customer_count'],$res['add_up']['complete_rate'],$res['add_up']['period_box_total'],
                    $res['add_up']['task_total'],$res['add_up']['amount_complete_rate'],$res['add_up']['amount_task_total']];
                write_excel($excel_data, '业务员业绩查询('.date('Y-m-d').')');
            }

            success($res);

            break;

        case 'task':
            //业务员销售任务查询
            param_need($data, ['year']);//suid,gid
            $t_model = new Task();
            $data['cid'] = $cid;
            $res = $t_model->form_read_pro($data);

            if(get_value($data, 'download') == 'excel'){
                $excel_data = [];
                $excel_data[] = ['业务员','商品','一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月','总计'];
                foreach($res['data'] as $val){
                    $excel_data[] = [$val['suname'],$val['gname'],$val['val1'],$val['val2'],$val['val3'],$val['val4'],
                        $val['val5'],$val['val6'],$val['val7'],$val['val8'],$val['val9'],$val['val10'],
                        $val['val11'],$val['val12'],$val['val_all']];
                }
                if($res['add_up']){
                    $val = $res['add_up'];
                    $excel_data[] = ['','月度总计（商品）',$val['val1'],$val['val2'],$val['val3'],$val['val4'],
                        $val['val5'],$val['val6'],$val['val7'],$val['val8'],$val['val9'],$val['val10'],
                        $val['val11'],$val['val12'],$val['val_all']];
                }
                if($res['add_up2']){
                    $val = $res['add_up2'];
                    $excel_data[] = ['','月度总计',$val['val1'],$val['val2'],$val['val3'],$val['val4'],
                        $val['val5'],$val['val6'],$val['val7'],$val['val8'],$val['val9'],$val['val10'],
                        $val['val11'],$val['val12'],$val['val_all']];
                }
                write_excel($excel_data, '业务员销售任务查询('.date('Y-m-d').')');
            }

            success($res);
            break;

        case 'goods_salesman_sell':
            //init_log_oper($action, '业务员单品销量查询');
            param_need($data, ['begin_date','end_date']);
            $data = format_data_ids($data, ['sids']);
            $so_model = new StockOut($id);
            $data['cid'] = $cid;
            $res = $so_model->form_goods_salesman_sell($data);

            if(get_value($data, 'download') == 'excel'){
                $excel_data = [];
                $excel_data[] = ['商品名称','规格','业务员','销售额','销售数量','销售箱数','赠品数量','赠品箱数','退货额',
                    '退货数量','退货箱数','赠品退货数量','赠品退货箱数'];
                foreach($res['data'] as $val){
                    $excel_data[] = [$val['gname'],$val['gspec'],$val['suname'],$val['amount'],$val['total'],
                        $val['box_total'],$val['free_total'],$val['free_box_total'],$val['return_amount'],$val['return_total'],
                        $val['return_box_total'],$val['return_free_total'],$val['return_free_box_total']];
                }
                $excel_data[] = ['总计','','',$res['add_up']['amount'],$res['add_up']['total'],'',
                    $res['add_up']['free_total'],'',$res['add_up']['return_amount'],$res['add_up']['return_total'],'',
                    $res['add_up']['return_free_total'],''];
                write_excel($excel_data, '业务员单品销量查询('.date('Y-m-d').')');
            }

            success($res);
            break;

        case 'geo':
            $my_model = new Geolocation();

            if(get_value($data, 'suid')){
                $data['uid'] = $data['suid'];
            }
            $data['cid'] = $cid;
            $res = $my_model->form_salesman($data);
            if(get_value($data, 'download') == 'excel'){
                $excel_data = [];
                $excel_data[] = ['时间','业务员','维护类型','客户','信息内容','GPS位置'];
                foreach($res['data'] as $val){
                    $excel_data[] = [$val['createtime'],$val['suname'],number_to_name($val['source'], 'source'),$val['ccname'],$val['memo'],
                        $val['baidu_address']];
                }
                write_excel($excel_data, '业务员客户维护记录('.date('Y-m-d').')');
            }

            success($res);
            break;

        default:
            error(1100);
    }

}
