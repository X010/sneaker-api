<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * f_reserve 库存类报表
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} f_reserve/snapshot 查看库存快照
 * @apiName f_reserve/snapshot
 * @apiGroup FReserve
 * @apiVersion 0.0.1
 * @apiDescription 查看库存快照
 *
 * @apiParam {string} sid *仓库ID
 * @apiParam {string} date *日期，格式2015-10-23
 * @apiParam {string} search 关键字检索，匹配商品名称、code、barcode等字段
 * @apiParam {string} gtids 商品类别，以逗号分隔
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {string} date 日期，格式2015-10-23
 * @apiSuccess {string} amount_begin 总期初金额
 * @apiSuccess {string} amount_end 总期末金额
 * @apiSuccess {string} total_begin 总期初数量
 * @apiSuccess {string} total_end 总期末数量
 * @apiSuccess {json} goods_list 商品清单
 * @apiSuccess {json} - goods_list商品清单
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {string} gcode 商品编码
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {string} gbarcode 商品条码
 * @apiSuccess {string} gunit 商品计量单位
 * @apiSuccess {string} gspec 商品规格
 * @apiSuccess {int} gtid 商品类型ID
 * @apiSuccess {string} gtname 商品类型名称
 * @apiSuccess {string} amount_begin 期初金额
 * @apiSuccess {string} amount_end 期末金额
 * @apiSuccess {string} total_begin 期初数量
 * @apiSuccess {string} total_end 期末数量
 *
 */

/**
 * @api {post} f_reserve/erp 查看进销存日报
 * @apiName f_reserve/erp
 * @apiGroup FReserve
 * @apiVersion 0.0.1
 * @apiDescription 查看进销存日报
 *
 * @apiParam {string} sid *仓库ID
 * @apiParam {string} date *日期，格式2015-10-23
 * @apiParam {string} search 关键字检索，匹配商品名称、code、barcode等字段
 * @apiParam {string} gtids 商品类别，以逗号分隔
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {string} gcode 商品编码
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {string} gbarcode 商品条码
 * @apiSuccess {int} gtid 商品类型ID
 * @apiSuccess {string} gtname 商品类型名称
 * @apiSuccess {int} total_begin 期初数量
 * @apiSuccess {int} total_end 期末数量
 * @apiSuccess {int} buy_total 采购数量
 * @apiSuccess {int} buy_return_total 采购退货数量
 * @apiSuccess {int} sell_total 销售数量
 * @apiSuccess {int} sell_return_total 销售退货数量
 * @apiSuccess {int} transfer_in_total 调入数量
 * @apiSuccess {int} transfer_out_total 调出数量
 * @apiSuccess {int} overloss_total 损益数量
 * @apiSuccess {int} inventory_total 盘点盈亏数量
 * @apiSuccess {string} amount_begin 期初金额
 * @apiSuccess {string} amount_end 期末金额
 * @apiSuccess {string} buy_amount 采购金额
 * @apiSuccess {string} buy_return_amount 采购退货金额
 * @apiSuccess {string} sell_amount 销售金额
 * @apiSuccess {string} sell_return_amount 销售退货金额
 * @apiSuccess {string} transfer_in_amount 调入金额
 * @apiSuccess {string} transfer_out_amount 调出金额
 * @apiSuccess {string} overloss_amount 损益金额
 * @apiSuccess {string} inventory_amount 盘点盈亏金额
 * @apiSuccess {string} adjust_amount 调价额
 *
 */

/**
 * @api {post} f_reserve/book 查看台账
 * @apiName f_reserve/book
 * @apiGroup FReserve
 * @apiVersion 0.0.1
 * @apiDescription 查看台账
 *
 * @apiParam {string} sid *仓库ID
 * @apiParam {string} gid *商品ID
 * @apiParam {string} begindate *起始日期，格式2015-10-23
 * @apiParam {string} enddate *截止日期，格式2015-10-23
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {string} date 日期
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {string} gcode 商品编码
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {string} gbarcode 商品条码
 * @apiSuccess {int} gtid 商品类型ID
 * @apiSuccess {string} gtname 商品类型名称
 * @apiSuccess {int} total_begin 期初数量
 * @apiSuccess {int} total_end 期末数量
 * @apiSuccess {int} buy_total 采购数量
 * @apiSuccess {int} buy_return_total 采购退货数量
 * @apiSuccess {int} sell_total 销售数量
 * @apiSuccess {int} sell_return_total 销售退货数量
 * @apiSuccess {int} transfer_in_total 调入数量
 * @apiSuccess {int} transfer_out_total 调出数量
 * @apiSuccess {int} overloss_total 损益数量
 * @apiSuccess {int} inventory_total 盘点盈亏数量
 * @apiSuccess {string} amount_begin 期初金额
 * @apiSuccess {string} amount_end 期末金额
 * @apiSuccess {string} buy_amount 采购金额
 * @apiSuccess {string} buy_return_amount 采购退货金额
 * @apiSuccess {string} sell_amount 销售金额
 * @apiSuccess {string} sell_return_amount 销售退货金额
 * @apiSuccess {string} transfer_in_amount 调入金额
 * @apiSuccess {string} transfer_out_amount 调出金额
 * @apiSuccess {string} overloss_amount 损益金额
 * @apiSuccess {string} inventory_amount 盘点盈亏金额
 * @apiSuccess {string} adjust_amount 调价额
 *
 */

/**
 * @api {post} f_reserve/expdate_warning 保质期预警
 * @apiName f_reserve/expdate_warning
 * @apiGroup FReserve
 * @apiVersion 0.0.1
 * @apiDescription 保质期预警
 *
 * @apiParam {string} sid *仓库ID
 * @apiParam {string} date *日期，列出在此日期之前过期的库存批次
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {string} gcode 商品编码
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {string} gbarcode 商品条码
 * @apiSuccess {string} gspec 商品规格
 * @apiSuccess {string} gunit 商品单位
 * @apiSuccess {int} total 商品该批次数量
 * @apiSuccess {int} batch 批次号
 * @apiSuccess {int} order_id 来源订单号
 * @apiSuccess {int} from 来源类型 1-进货 2-退货 3-调拨 4-报溢 5-冲正入库 6-盘赢入库
 * @apiSuccess {int} unit_price 库存单价
 *
 */

/**
 * @api {post} f_reserve/stock_out 出库单报表
 * @apiName f_reserve/stock_out
 * @apiGroup FReserve
 * @apiVersion 0.0.1
 * @apiDescription 出库单报表
 *
 * @apiParam {string} sid *仓库ID
 * @apiParam {string} type 单据类型
 * @apiParam {string} status 单据状态
 * @apiParam {string} in_cid 指定客户ID
 * @apiParam {string} suid 业务员ID
 * @apiParam {string} begin_date *起始日期
 * @apiParam {string} end_date *截止日期
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
 * @api {post} f_reserve/stock_in 入库单报表
 * @apiName f_reserve/stock_in
 * @apiGroup FReserve
 * @apiVersion 0.0.1
 * @apiDescription 入库单报表
 *
 * @apiParam {string} sid *仓库ID
 * @apiParam {string} begin_date *起始日期
 * @apiParam {string} end_date *截止日期
 * @apiParam {string} type 单据类型
 * @apiParam {string} status 单据状态
 * @apiParam {string} out_cid 指定供应商ID
 * @apiParam {string} buid 采购员ID
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {string} id 出库单号
 * @apiSuccess {string} order_id 订单号
 * @apiSuccess {int} cid 公司ID
 * @apiSuccess {string} cname 公司名称
 * @apiSuccess {int} sid 仓库ID
 * @apiSuccess {string} sname 仓库名称
 * @apiSuccess {int} status 出库单状态 1-未审核 2-已审核 3-已复核
 * @apiSuccess {int} type 出库单类型 1-采购 2-退货 3-调拨 4-报溢
 * @apiSuccess {string} memo 出库单备注
 * @apiSuccess {string} createtime 创建时间
 * @apiSuccess {string} updatetime 上次更新时间
 * @apiSuccess {string} checktime 审核时间
 * @apiSuccess {int} uid 填单人ID
 * @apiSuccess {string} uname 填单人姓名
 * @apiSuccess {int} cuid 审核员ID
 * @apiSuccess {string} cuname 审核员姓名
 * @apiSuccess {int} ruid 复核员ID
 * @apiSuccess {string} runame 复核员姓名
 * @apiSuccess {string} name_do 经办人名字
 * @apiSuccess {string} amount 入库单总金额
 * @apiSuccess {string} tax_amount 总税额
 * @apiSuccess {string} out_cname 供应商名称
 * @apiSuccess {int} out_cid 供应商公司ID
 *
 */


/**
 * @api {post} f_reserve/read_goods 查询库存商品列表
 * @apiName f_reserve/read_goods
 * @apiGroup FReserve
 * @apiVersion 0.0.1
 * @apiDescription 查询库存商品列表
 *
 * @apiParam {int} sid 仓库ID
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {string} gcode 商品编码
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {string} gbarcode 商品条码
 * @apiSuccess {string} gunit 商品计量单位
 * @apiSuccess {string} gspec 商品规格
 * @apiSuccess {int} gtid 商品类型ID
 * @apiSuccess {string} gtname 商品类型名称
 * @apiSuccess {int} total 商品总数
 * @apiSuccess {int} onway_total 在途数量
 * @apiSuccess {string} amount 商品总价
 * @apiSuccess {string} unit_price 商品平均单价
 */

/**
 * @api {post} f_reserve/read 查询商品批次列表
 * @apiName f_reserve/read
 * @apiGroup FReserve
 * @apiVersion 0.0.1
 * @apiDescription 查询商品批次列表
 *
 * @apiParam {int} sid 仓库ID
 * @apiParam {int} gid 商品ID
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {int} id 批次表ID
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {string} gcode 商品编码
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {string} gbarcode 商品条码
 * @apiSuccess {string} gunit 商品计量单位
 * @apiSuccess {string} gspec 商品规格
 * @apiSuccess {int} cid 公司ID
 * @apiSuccess {int} scid 供应商ID
 * @apiSuccess {int} scname 供应商名称
 * @apiSuccess {int} sid 仓库ID
 * @apiSuccess {int} total 商品该批次数量
 * @apiSuccess {int} batch 批次号
 * @apiSuccess {int} order_id 来源订单号
 * @apiSuccess {int} from 来源类型 1-进货 2-退货 3-调拨 4-报溢 5-冲正入库 6-盘赢入库
 * @apiSuccess {int} unit_price 库存单价
 *
 */

function f_reserve($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    switch($action){
        case 'snapshot':
            //init_log_oper($action, '查看库存日报');
            param_need($data, ['sid','date']);	//判断参数是否必填
            $data = format_data_ids($data, ['gtids']);
            $fr_model = new FReserve($id);
            Power::check_my_sid($data['sid']);
            $res = $fr_model->my_read($data);
            success($res);
            break;

        case 'erp':
            //init_log_oper($action, '查看进销存日报');
            $detail = get_value($data, 'detail');
            if($detail == 1){
                param_need($data, ['sid','begin_date','end_date','gid']);	//判断参数是否必填
                $fr_model = new FReserve($id);
                Power::check_my_sid($data['sid']);
                $res = $fr_model->my_erp_detail($data);
                success($res);
            }
            else{
                param_need($data, ['sid','begin_date','end_date']);	//判断参数是否必填
                $data = format_data_ids($data, ['gtids']);
                $fr_model = new FReserve($id);
                Power::check_my_sid($data['sid']);
                $res = $fr_model->my_erp($data);
            }
            success($res);
            break;

        case 'book':
            //init_log_oper($action, '查看台账');
            param_need($data, ['gid','begin_date','end_date']);	//判断参数是否必填
            $fr_model = new FReserve($id);
            //Power::check_my_sid($data['sid']);
            $data['cid'] = $cid;
            $res = $fr_model->my_book($data);
            success($res);
            break;

        case 'expdate_warning':
            //init_log_oper($action, '保质期预警查询');
            param_need($data, ['sid','date']);	//判断参数是否必填
            $s_model = new Store();
            $is_reserve = $s_model -> is_reserve($data['sid']);
            //如果不开启库存管理，直接报错
            if(!$is_reserve){
                error(1610);
            }
            //保质期排序，最快过期的排到最前面
            $data['orderby'] = 'expdate^asc';
            $data['expdate[<=]'] = $data['date'];
            $data['total[>]'] = '0';
            $data['cid'] = $cid;
            $r_model = new Reserve();
            $res = $r_model->my_read_list($data);

            if(get_value($data, 'download') == 'excel'){
                $excel_data = [];
                $excel_data[] = ['商品编码','商品名称','商品条码','单位','规格','到效日期','批次号','库存单价','库存数量','库存箱数','来源类型','来源订单号'];
                foreach($res['data'] as $val){
                    $excel_data[] = [$val['gcode'],$val['gname'],$val['gbarcode'],$val['gunit'],$val['gspec'],$val['expdate'],
                        $val['batch'],$val['unit_price'], $val['total'],round($val['total']/$val['gspec'],2),
                        number_to_name($val['from'],'reserve_from'),$val['order_id']];
                }
                $excel_data[] = ['总计','','','','','','','',$res['add_up']['total'],'','',''];
                write_excel($excel_data, '保质期预警('.date('Y-m-d').')');
            }

            success($res);
            break;

        case 'reserve_warning':
            //init_log_oper($action, '库存预警查询');
            param_need($data, ['sid']);
            $sid = $data['sid'];
            $gw_model = new GoodsWarning();
            $gw_res = $gw_model -> read_list_nopage([
                'cid'=>$cid,
                'sid'=>$sid,
                'orderby'=>'id^asc'
            ]);
            $res = [
                'data'=>[],
                'count'=>0
            ];
            if($gw_res){
                $gid_list = [];
                foreach($gw_res as $val){
                    $gid_list[] = $val['gid'];
                }

                $r_model = new Reserve();
                $r_res = $r_model->get_reserve($cid, $sid, $gid_list);

                foreach($gw_res as $val){
                    $gid = $val['gid'];
                    $r_temp = get_value($r_res, $gid);

                    if($r_temp < $val['total']){
                        $res_temp = $val;
                        $res_temp['box_total'] = round($val['total']/$val['gspec'], 2);
                        $res_temp['total_now'] = $r_temp;
                        $res_temp['box_total_now'] = round($res_temp['total_now']/$val['gspec'], 2);
                        $res['data'][] = $res_temp;
                    }
                }
                $res['count'] = count($res['data']);
            }

            if(get_value($data, 'download') == 'excel'){
                $excel_data = [];
                $excel_data[] = ['商品编码','商品名称','商品条码','规格','单位','预警值','当前库存','预警值(箱)','当前库存(箱)'];
                foreach($res['data'] as $val){
                    $excel_data[] = [$val['gcode'],$val['gname'],$val['gbarcode'],$val['gspec'],$val['gunit'],$val['total'],
                        $val['total_now'],$val['box_total'], $val['box_total_now']];
                }
                write_excel($excel_data, '库存预警('.date('Y-m-d').')');
            }

            success($res);
            break;

        case 'stock_out':
            param_need($data, ['sid','begin_date','end_date']);	//判断参数是否必填
            param_check($data, ['page' => "/^\d+$/",'page_num' => "/^\d+$/"]);

            $my_model = new StockOut();
            //init_log_oper($action, '出库单汇总查询');
            //默认加上公司内的单据条件
            $data['cid'] = $cid;
            $res = $my_model -> form_stock_out($data);
            success($res);
            break;

        case 'stock_in':
            param_need($data, ['sid','begin_date','end_date']);	//判断参数是否必填
            param_check($data, ['page' => "/^\d+$/",'page_num' => "/^\d+$/"]);

            $my_model = new StockIn();
            //init_log_oper($action, '入库单汇总查询');
            //默认加上公司内的单据条件
            $data['cid'] = $cid;
            $res = $my_model -> form_stock_in($data);
            success($res);
            break;

        case 'read_goods':
            //init_log_oper($action, '商品库存实时查询');
            param_need($data, ['sid']);
            param_check($data, ['page' => "/^\d+$/",'page_num' => "/^\d+$/"]);

            //检测是否有仓库权限
            Power::check_my_sid($data['sid']);
            $data['cid'] = $cid;

            $s_model = new Store();
            $is_reserve = $s_model -> is_reserve($data['sid']);
            //如果不开启库存管理，直接报错
            if(!$is_reserve){
                error(1610);
            }
            $my_model = new Reserve();
            $res = $my_model -> read_goods($data);

            //转换商品类型名称显示
            if($res['count']){
                $res['data'] = Change::go($res['data'], 'gtid', 'gtname', 'o_company_goods_type');

                $o_model = new Order();

                $gid_list = [];
                foreach($res['data'] as $val){
                    $gid_list[] = $val['gid'];
                }
                $o_res = $o_model->get_on_way($data['sid'], $gid_list);
                foreach($res['data'] as $key=>$val){
                    $res['data'][$key]['onway_total'] = get_value($o_res, $val['gid'], 0);
                }
            }

            success($res);
            break;

        case 'read':
            //init_log_oper($action, '商品库存批次查询');
            param_need($data, ['sid','gid']);
            param_check($data, ['page' => "/^\d+$/",'page_num' => "/^\d+$/"]);

            //检测是否有仓库权限
            Power::check_my_sid($data['sid']);

            $s_model = new Store();
            $is_reserve = $s_model -> is_reserve($data['sid']);
            //如果不开启库存管理，直接报错
            if(!$is_reserve){
                error(1610);
            }
            $data['cid'] = $cid;

            $cg_model = new CompanyGoods();
            $cg_res = $cg_model->get_one_goods($cid, $data['gid']);
            $spec = $cg_res['gspec'];

//            $g_model = new Goods($data['gid']);
//            $g_res = $g_model->read_by_id();
//            $spec = $g_res[0]['spec'];

            //默认使用正序
            $data['orderby'] = 'id^asc';

            $my_model = new Reserve();
            $res = $my_model -> my_read_list($data);

            //增加箱数返回显示
            foreach($res['data'] as $key=>$val){
                $res['data'][$key]['box_total'] = $val['total']/$spec;
            }

            success($res);
            break;

        default:
            error(1100);
    }

}
