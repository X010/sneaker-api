<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * car 车辆管理
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} car/create 新增车辆
 * @apiName car/create
 * @apiGroup Car
 * @apiVersion 0.0.1
 * @apiDescription 新增车辆
 *
 * @apiParam {string} license *车牌号码
 * @apiParam {float} ton *车辆载重
 * @apiParam {string} style *车辆类型
 * @apiParam {string} model *车辆型号
 * @apiParam {string} memo *备注
 *
 */

/**
 * @api {post} car/update/:id 更新车辆信息
 * @apiName car/update
 * @apiGroup Car
 * @apiVersion 0.0.1
 * @apiDescription 更新车辆信息
 *
 * @apiParam {string} license *车牌号码
 * @apiParam {float} ton *车辆载重
 * @apiParam {string} style *车辆类型
 * @apiParam {string} model *车辆型号
 * @apiParam {string} memo *备注
 *
 */

/**
 * @api {post} car/delete/:id 删除车辆信息
 * @apiName car/delete
 * @apiGroup Car
 * @apiVersion 0.0.1
 * @apiDescription 删除车辆信息
 *
 */

function car($action, $id = Null){
	init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
	$app = \Slim\Slim::getInstance();
	$my_model = new Car($id);
	$data = $app->params;
	$cid = $app->Sneaker->cid;
	switch($action){
		case 'create':
			init_log_oper($action, '创建车辆信息');
			param_need($data, ['license','ton']);

			//找到用户公司ID，补充到参数里
			$data['cid'] = $cid;
			
			//创建车辆
			$res = $my_model -> my_create($data);

			success(['id' => $res]);
			break;

		case 'update':
			init_log_oper($action, '修改车辆信息');
			if(!is_numeric($id)){
				error(1100);
			}

			//判断用户身份是否拥有该车辆权限
			$res = $my_model->read_by_id();
			if(!$res){
				error(1801);
			}
			Power::check_my_cid($res[0]['cid']);

			//更新车辆信息
			$my_model -> my_update($data);
			success();
			break;

		case 'delete':
			init_log_oper($action, '删除车辆信息');
			if(!is_numeric($id)){
				error(1100);
			}

			//判断用户身份是否拥有该车辆权限
			$res = $my_model->read_by_id();
			if(!$res){
				error(1801);
			}
			Power::check_my_cid($res[0]['cid']);

			//删除车辆信息
			$my_model -> delete_by_id();
			success();
			break;

		default:
			error(1100);
	}

}
