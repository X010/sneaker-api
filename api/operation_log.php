<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * API of operation log
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} operation_log/read/:id 查询操作日志详情
 * @apiName operation_log/read/id
 * @apiGroup operation_log
 * @apiVersion 0.0.1
 * @apiDescription 查询操作日志详情（info和err流水日志）
 *
 * @apiSuccess {int} id ID
 * @apiSuccess {int} imark 请求检索标识
 * @apiSuccess {string} request 请求数据
 * @apiSuccess {string} response 响应数据
 * @apiSuccess {string} api 接口URI
 * @apiSuccess {string} msg 自定义内容
 * @apiSuccess {string} uid 用户uid
 * @apiSuccess {string} timeused 接口耗时（秒）
 * @apiSuccess {string} createtime 日志创建时间
 *
 *
 */

/**
 * @api {post} operation_log/read 浏览操作日志列表
 * @apiName operation_log/read
 * @apiGroup operation_log
 * @apiVersion 0.0.1
 * @apiDescription 浏览操作日志列表
 *
 * @apiParam {string} begin_time *查询起始时间
 * @apiParam {string} end_time *查询截止时间
 * @apiParam {string} uid 员工ID
 * @apiParam {string} uname 员工名称
 * @apiParam {string} flag 操作结果（1：成功 / 0：失败）
 * @apiParam {string} module_id 模块ID
 * @apiParam {string} menu_id 菜单ID
 *
 * @apiSuccess {int} id 日志ID
 * @apiSuccess {int} uid 用户uid
 * @apiSuccess {string} uname 用户名
 * @apiSuccess {string} ip IP
 * @apiSuccess {int} flag 操作结果（1：成功 / 0：失败）
 * @apiSuccess {string} action_type 动作类型
 * @apiSuccess {string} action_msg 动作描述
 * @apiSuccess {int} module_id 模块ID
 * @apiSuccess {string} module_name 模块名称
 * @apiSuccess {int} menu_id 菜单ID
 * @apiSuccess {string} menu_name 菜单名称
 * @apiSuccess {string} imark 请求检索标识
 * @apiSuccess {string} createtime 日志创建时间
 *
 *
 */

function operation_log($action, $id = NULL){
	init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
	$app = \Slim\Slim::getInstance();
	$data = $app->params;
    $user_info= $app->Sneaker->user_info; //获取用户信息
	//if(!$user_info['admin']) error(8100); //不是管理员,没有操作权限，直接返回
	$data['cid']=$user_info['cid']; //填写cid
	$o_operation_log = new OperationLog($id);
	switch($action){

		case 'read':
			if ($id){ //获取单个日志详情
				//init_log_oper('read', '查询日志详情');
				if(!is_numeric($id)) error('1100');
				$res = $o_operation_log->read_detail(); //读取info或err日志(可能多条)
			}else{ //获取日志列表
				//查阅日志时会递归产生垃圾日志，先注释掉
				//init_log_oper('read', '浏览操作日志'); 
				param_need($data, ['begin_time','end_time']); //必填参数
				$res = $o_operation_log->read_list($data);
			}
			success($res);
			break;

		default:
			error(1100);
	}
}


