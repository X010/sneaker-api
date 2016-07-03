<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * API of balance config
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} config_balance/read/:id 查询结算形式详情
 * @apiName config_balance/read/id
 * @apiGroup config_balance
 * @apiVersion 0.0.1
 * @apiDescription 查询结算形式详情
 *
 * @apiSuccess {int} id ID
 * @apiSuccess {int} fid 主配置表ID
 * @apiSuccess {string} value 结算形式
 * @apiSuccess {int} status 状态
 * @apiSuccess {string} updatetime 最后更新时间
 * @apiSuccess {string} createtime 创建时间
 *
 *
 */

/**
 * @api {post} config_balance/read 浏览结算形式列表
 * @apiName config_balance/read
 * @apiGroup config_balance
 * @apiVersion 0.0.1
 * @apiDescription 浏览结算形式列表
 *
 *
 */

function config_balance($action, $id = NULL){
	init_menu_and_module_name(__FUNCTION__);
	$app = \Slim\Slim::getInstance();
	$data = $app->params;
	$fid = $app->config('config_balance_id'); //结算形式的主参数ID
	$o_system_config_detail = new SystemConfigDetail($id);
	switch($action){
		case 'read':
			if ($id){ 
				//init_log_oper($action, '查看结算形式详情');
				if(!is_numeric($id)) error('1100');
				$res = $o_system_config_detail->read_by_id();
				$res = $res ? $res[0] : $res;
			}else{ 
				//init_log_oper($action, '浏览结算形式列表');
				$res = $o_system_config_detail->read_list_by_fid($fid);
			}
			$res === False ? error(9900) : success($res); 
			break;

		default:
			error(1100);
	}
}


