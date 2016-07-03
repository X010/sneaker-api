<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * Configuration for error code
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo
 * @version     0.0.1
 * @package     conf
 */


/**
 * Configuration for menu
 */

$app->config('button', [
	10101 => '/company/update',  //公司资料修改
	10201 => '/store/update',	//仓库修改
	10301 => '/supplier/create_batch',	//供应商导入
	10302 => '/supplier/update',	//供应商修改

	10401 => '/customer/create_batch',	//客户导入
	10402 => '/customer/update',	//客户编辑
	10403 => '/customer/check_pass',	//审核客户通过
 	10404 => '/customer/check_unpass',	//审核客户不通过
	10405 => '/customer/add_salesman',	//向客户添加业务员
 	10406 => '/customer/delete_salesman',	//从客户移除业务员
 	10407 => '/customer/default_salesman',	//设置客户默认业务员
	10408 => '/vip/register',	//会员注册
	10409 => '/vip/update',	//会员修改
	10410 => '/vip/delete',	//会员停用
	10411 => '/vip/recover',	//会员启用

	10501 => '/company_goods_type/create',	//商品分类新建
	10502 => '/company_goods_type/delete',	//商品分类删除
	10503 => '/company_goods_type/update',	//商品分类改名
	10504 => '/company_goods_type/copy_system',	//导入系统商品分类
	10505 => '/company_goods_type/flush',	//清空商品分类
	10506 => '/goods_warning/create',

	10601 => '/company_goods/create',	//商品导入
	10602 => '/goods_supplier/create',	// 商品添加供应商
	10603 => '/company_goods/update',	// 商品修改分类
	10604 => '/company_goods/buy_off',	// 商品停止采购
	10605 => '/company_goods/buy_on',	// 商品开启采购
	10606 => '/company_goods/delete',	// 商品删除
	10607 => '/goods_supplier/delete',	// 商品删除供应商
	10608 => '/goods/create',  //新建商品

	10701 => '/user/update',	//	员工保存
	10702 => '/user/delete',	//	员工停用
	10703 => '/user/create',	//	员工创建
	10704 => '/user/move_customer',	//	批量移交客户
	10705 => '/user_group/create',	//	新建员工分组［OK］
 	10706 => '/user_group/delete',	//	删除员工分组［OK］
 	10707 => '/user_group/update',	//	改名员工分组［OK］

	10801 => '/car/create',	//	车辆新建
 	10802 => '/car/update',	//	车辆编辑
 	10803 => '/car/delete',	//	车辆删除

	11001 => '/order/update',	// 订单修改
	11002 => '/order/create',	// 订单保存
	11003 => '/order/check',	// 订单保存并审核
	11004 => '/order/check',	// 订单审核
	11005 => '/order/delete',	// 订单作废


	11006 => '/stock_in/create',	// 订单生成入库单
	11007 => '/stock_in/check',	// 订单生成入库单并审核
	11101 => '/stock_in/update',	// 入库单修改
	11102 => '/stock_in/create',	// 入库单保存
	11103 => '/stock_in/check',	// 入库单保存并审核
	11104 => '/stock_in/check',	// 入库单审核
	11105 => '/stock_in/delete',	// 入库单作废
	11106 => '/stock_in/repaire',	// 入库单修正
	11107 => '/stock_in/flush',	// 入库单冲单


	11201 => '/return_out/create',	//	退货出库单保存
	11202 => '/return_out/check',	//	退货出库单保存并审核
	11203 => '/return_out/check',	//	退货出库单审核
	11204 => '/return_out/delete',	//	退货出库单作废
	11205 => '/return_out/repaire',	//	退货出库单修正
	11206 => '/return_out/flush',	//	退货出库单冲单
	11207 => '/return_out/update',	//	修改退货出库单

	11301 => '/stock_out/precheck',	//	从订单生成出库单
	11302 => '/stock_out/check',	//	从订单生成出库单并审核
	11303 => '/stock_out/precheck',	//	出库单保存
	11304 => '/stock_out/check',	//	出库单保存并审核
	11305 => '/stock_out/check',	//	出库单审核
	11306 => '/stock_out/delete',	//	出库单作废
	11307 => '/stock_out/repaire',	//	出库单修正
	11308 => '/stock_out/flush',	//	出库单冲单
	11309 => '/stock_out/update',	//	出库单修改
	11310 => '/order/delete_out',	//	取消客户订单
	11311 => '/order/update_cs',	//  修改客户订单
	11312 => '/order/update_visit',	//	回访客户订单
	11313 => '/order/split',	//	客户订单拆单［OK］

	11401 => '/return_in/create',	//	从订单生成退货入库单
	11402 => '/return_in/check',	//	从订单生成退货出库单并审核
	11403 => '/return_in/create',	//	退货入库单保存
	11404 => '/return_in/check',	//	退货入库单审核
	11405 => '/return_in/delete',	//	退货入库单作废
	11406 => '/return_in/repaire',	//	退货入库单修正
	11407 => '/return_in/flush',	//	退货入库单冲单
	11408 => '/return_in/update',	//	修改退货入库单

	11501 => '/transfer/create',	//	调出单保存
	11502 => '/transfer/check',	//	调出单直接发货
	//11503 => '/transfer/receive',	//	调出单直接收货
	11504 => '/transfer/check',	//	调出单确认发货
	11505 => '/transfer/receive',	//	调入单确认收货
	11506 => '/transfer/delete',	//	作废调拨单
	11507 => '/transfer/flush',	//	调出单冲单

	11601 => '/loss/create',	//	报损单保存
	11602 => '/loss/check',	//	报损单保存并审核
	11603 => '/loss/check',	//	报损单审核
	11604 => '/loss/delete',	//	报损单作废
	11605 => '/loss/repaire',	//	报损单修正
	11606 => '/loss/flush',	//	报损单冲单

	11701 => '/overflow/create',	//	报溢单保存
	11702 => '/overflow/check',	//	报溢单保存并审核
	11703 => '/overflow/check',	//	报溢单审核
	11704 => '/overflow/delete',	//	报溢单作废
	11705 => '/overflow/repaire',	//	报溢单修正
	11706 => '/overflow/flush',	//	报溢单冲单

	11801 => '/price/create_in',	//	进货调价单保存
	11802 => '/price/check_in',	//	进货调价单保存并审核
	11803 => '/price/check_in',	//	进货调价单审核
	11804 => '/price/delete',	//	进货调价单作废
	11901 => '/price/create_out',	//	出货调价单保存
	11902 => '/price/check_out',	//	出货调价单保存并审核
	11903 => '/price/check_out',	//	出货调价单审核
	11904 => '/price/delete',	//	出货调价单作废

	12001 => '/price_temp/create_in',	//	进货促销调价单保存
	12002 => '/price_temp/check_in',	//	进货促销调价单保存并审核
	12003 => '/price_temp/check_in',	//	进货促销调价单审核
	12004 => '/price_temp/delete',	//	进货促销调价单作废
	12005 => '/price_temp/read',	//	查看促销价格

	12101 => '/inventory_phy/create',	// 实盘单保存
	12102 => '/inventory_phy/check',	// 实盘单保存并审核
	12103 => '/inventory_phy/check',	// 实盘单审核
	12104 => '/inventory_sys/check',	// 帐盘单记账

	12201 => '/settle_customer/create',	// 客户结算单保存
	12202 => '/settle_customer/check',	// 客户结算单保存并审核
	12203 => '/settle_customer/check',	// 客户结算单审核
	12204 => '/settle_customer/delete',	// 客户结算单作废
	12205 => '/settle_customer/flush',	// 客户结算单冲单
	12206 => '/settle_customer/read_detail',	// 查看客户结算单商品清单

	12301 => '/settle_supplier/create',	// 供应商结算单保存
	12302 => '/settle_supplier/check',	// 供应商结算单保存并审核
	12303 => '/settle_supplier/check',	// 供应商结算单审核
	12304 => '/settle_supplier/delete',	// 供应商结算单作废
	12305 => '/settle_supplier/flush',	// 供应商结算单冲单
	12306 => '/settle_supplier/read_detail',	// 查看供应商结算单商品清单

	12401 => '/debit_note/create',	// 收款单保存
	12402 => '/debit_note/check',	// 收款单保存并审核
	12403 => '/debit_note/check',	// 收款单审核
	12404 => '/debit_note/delete',	// 收款单作废
	12405 => '/debit_note/flush',	// 收款单冲单

	12501 => '/payment_note/create',	// 付款单保存
	12502 => '/payment_note/check',	// 付款单保存并审核
	12503 => '/payment_note/check',	// 付款单审核
	12504 => '/payment_note/delete',	// 付款单作废
	12505 => '/payment_note/flush',	// 付款单冲单

	12601 => '/sorting/create',	// 拣货派车单保存
 	12602 => '/sorting/delete',	// 拣货派车单作废

	12701 => '/settle_proxy_supplier/create',	// 代销结算单保存
	12702 => '/settle_proxy_supplier/check',	// 代销结算单保存并审核
	12703 => '/settle_proxy_supplier/check',	// 代销结算单审核
	12704 => '/settle_proxy_supplier/delete',	// 代销结算单作废
	12705 => '/settle_proxy_supplier/flush',	// 代销结算单冲单

	12801 => '/commission/create',	// 保存提成结算单［OK］
 	12802 => '/commission/check',	// 保存并审核提成结算单［OK］
	12803 => '/commission/check',	// 审核提成结算单［OK］
 	12804 => '/commission/delete',	// 作废提成结算单［OK］
 	12805 => '/commission/flush',	// 冲单提成结算单［OK］

	12901 => '/task/create',	// 新建销售任务单［OK］
 	12902 => '/task/update',	// 修改销售任务单［OK］
 	12903 => '/task/delete',	// 删除提成结算单［OK］

	13001 => '/visit/create',	// 新建回访记录［OK］
 	13002 => '/visit/update',	// 修改回访记录［OK］

	9001 => '/f_base/goods',   //  商品报表
	9002 => '/f_base/customer',   //  客户报表
	9003 => '/f_base/supplier',   //  供应商报表
	9004 => '/f_base/goods_price_in', //  商品价格查询
	9005 => '/f_base/goods_price_out', //  商品价格查询
	9006 => [9004,9005],			//  商品价格查询
	9007 => '/f_base/salesman', //  客户业务员排行

	9101 => '/f_reserve/snapshot',   //  库存日报
	9102 => '/f_reserve/read_goods',   //  实时库存查询
	9103 => '/f_reserve/book',   //  台帐查询
	9104 => '/f_reserve/erp',   //  进销存日报
	9105 => '/inventory_sys/read',   //  盘点预盈亏报表
	9106 => '/inventory_sys/read',   //  盘点实盈亏报表
	9107 => '/f_reserve/expdate_warning',   //  保质期预警
	9108 => '/f_reserve/stock_out',   //  出库单汇总
	9109 => '/f_reserve/stock_in',   //  入库单汇总
	9110 => '/f_reserve/reserve_warning',   //  库存预警

	9201 => '/f_sell/form_balance',   //  日对账报表
	9202 => '/f_sell/salesman',   //  业务员排名
	9203 => '/f_sell/goods',   //  商品排名
	9204 => '/f_sell/customer',   //  客户排名
	9205 => '/f_sell/form_goods',   //  商品报表
	9206 => '/f_sell/form_customer',   //  客户报表
	9207 => '/f_sell/form_supplier',   //  供应商报表
	9208 => '/f_sell/form_supplier_goods',   //  供应商商品报表
	9209 => '/f_sell/form_customer_goods',   //  客户商品报表
	9210 => '/f_sell/salesman_goods',   //  业务员单品铺货查询

	9301 => '/f_finance/payment',   //  应付款报表
	9302 => '/f_finance/debit',   //  应收款报表
	9305 => '/f_finance/settle',   //  日收款对账查询
	9306 => '/f_finance/stock_out',   //  出货单结算状态查询'
	9307 => '/f_finance/commission',   //  提成结算报表
	9308 => '/f_finance/commission_goods',   //  提成结算商品报表
	9309 => '/f_finance/real_back',   //  实际回款明细报表

	9401 => '/f_sell/goods_salesman',   //  业务员单品订量查询
	9402 => '/f_salesman/order_rate',   //  业务员订单达成率
	9403 => '/f_salesman/goods',   //  业务员单品铺货
	9404 => '/f_salesman/task_rate',   //  业务员业绩查询
	9405 => '/f_salesman/task',   //  业务员销售任务报表
	9406 => '/f_salesman/geo',   //  业务员客户维护记录
	9407 => '/f_salesman/goods_salesman_sell',   //  业务员单品销量
]);

