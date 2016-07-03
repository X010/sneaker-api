<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * API of rank config
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} config_rank/read 浏览订单紧急度列表（已停用）
 * @apiName config_rank/read
 * @apiGroup config_rank
 * @apiVersion 0.0.1
 * @apiDescription 浏览订单紧急度列表
 *
 *
 */

function config_rank($action, $id = NULL){
	init_menu_and_module_name(__FUNCTION__);
	$app = \Slim\Slim::getInstance();
	$data = $app->params;
	$fid = $app->config('config_rank_id');
	$o_system_config_detail = new SystemConfigDetail($id);
	switch($action){
//		case 'read':
//			//init_log_oper('read', '浏览订单紧急度列表');
//			$res = $o_system_config_detail->read_list_by_fid($fid);
//			success($res);
//			break;

		default:
			error(1100);
	}
}


