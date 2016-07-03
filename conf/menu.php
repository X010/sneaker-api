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

$app->config('menu', [
	121 => '/order/read_in',
    12 => '/order/create',
    161 => '/stock_in/read',
    16 => '/stock_in/create',
	181 => '/return_out/read_out',
	18 => '/return_out/create',
	22 => '/order/read_out',
	23 => '/order/read_out_cs',
	24 => '/return_out/read_in',
	25 => '/order/read_out_visit',
	261 => '/stock_out/read',
	26 => '/stock_out/precheck',
	27 => '/task/create',//新建销售任务单
	271 => '/task/read',//销售任务单
	281 => '/return_in/read',
	28 => '/return_in/create',
	291 => '/sorting/read',
	29 => '/sorting/create',
	321 => '/transfer/read',
	32 => '/transfer/create',
	331 => '/transfer/read',
	341 => '/loss/read',
	34 => '/loss/create',
	361 => '/overflow/read',
	36 => '/overflow/create',
	37 => '/goods_warning/read',
	662 => '/price/read_in',
	62 => '/price/create_in',
	663 => '/price/read_out',
	63 => '/price/create_out',
	664 => '/price_temp/read_in',
	64 => '/price_temp/create_in',
	722 => '/inventory_sys/read',
	72 => '/inventory_sys/create',
	733 => '/inventory_phy/read',
	73 => '/inventory_phy/check',
	744 => '/inventory_sys/check',
	811 => '/settle_customer/read',
	81 => '/settle_customer/create',
	822 => '/settle_supplier/read',
	82 => '/settle_supplier/create',
	833 => '/debit_note/read',
	83 => '/debit_note/create',
	844 => '/payment_note/read',
	84 => '/payment_note/create',
	85 => '/settle_proxy_supplier/create', //新建代销结算单
	855 => '/settle_proxy_supplier/read', //浏览代销结算单列表
	866 => '/commission/read', //提成结算单
	86 => '/commission/create', //新建提成结算单
	877 => '/settle_customer/read', //会员收款单
	87 => '/settle_customer/create', //新建会员收款单
	911 => '/visit/create', //新建回访记录
	91 => '/visit/read', //回访记录
	9000 => 1,
	9100 => 1,
	9200 => 1,
	9300 => 1,
	41 => 1,
	46 => 1,
	466 => '/store/create',
	42 => 1,
	422 => '/supplier/register',
	43 => 1,
	433 => '/customer/register',
	44 => 1,
	444 => '/goods/create',
	45 => '/user/read',
	455 => '/user/create',
	47 => 1,
	477 => '/car/create',
	48 => 1,
	499 => '/user/move_customer',	//	移交客户 （新增，页面）
	411 => 1,	//	会员管理
	412 => '/vip/register',	//	会员注册
	52 => 1,
	522 => '/role/create',
	53 => '/operation_log/read',
	54 => '/company/update_print_tpl',   //打印模版
]);

