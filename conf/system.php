<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * Configuration for system
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo
 * @version     0.0.1
 * @package     conf
 */


/**
 * the main config id for system
 */
$app->config('default_page_num', 100);       //默认每页个数
$app->config('default_password', '123456'); //重置默认密码

$app->config('default_ticket_period', 3600);  //默认ticket有效期
$app->config('erp_ticket_period', 3600);       //ticket有效期
$app->config('salesman_ticket_period', 10080);       //ticket有效期
$app->config('customer_ticket_period', 604800);       //ticket有效期
$app->config('pbs_ticket_period', 10080);       //ticket有效期

$app->config('verify_period', 300);       //验证码有效期

$app->config('config_balance_id', 1);   //结算形式ID
$app->config('config_rank_id', 2);      //订单紧急度ID
//$app->config('config_company_id', 3);   //企业性质ID
//$app->config('config_unit_id', 4);      //计量单位ID

$app->config('config_account', [        //会计科目
    1=>'优惠费用',
    2=>'广告费用',
    99=>'其它费用',
]);

$app->config('mall_buyer_role', '50');
$app->config('mall_default_store_name', '默认仓库');
$app->config('mall_default_user_name', '默认用户');
$app->config('mall_order_queue_name', 'queue_mo_jane');


$app->config('photo_url', 'http://photo.ms9d.com/');
$app->config('photo_format',[
    'IMG_SPEC_SM' => '@0o_0l_160w_90q.src',
    'IMG_SPEC_MD' => '@1e_1c_0o_0l_400h_350w_90q.src',
    'IMG_SPEC_LG' => '@0o_0l_800w_90q',
    'IMG_SPEC_HB' => '@1e_1c_0o_0l_250h_768w_90q.src'
]);

$app->config('platform', [
    'erp','salesman','customer','pbs'
]);