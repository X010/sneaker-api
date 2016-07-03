<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * company 公司管理
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} company/update/:id 更新公司
 * @apiName company/update/:id
 * @apiGroup Company
 * @apiVersion 0.0.1
 * @apiDescription 更新公司实体操作
 *
 * @apiParam {string} name *公司名称
 * @apiParam {string} simple_name 公司简称
 * @apiParam {string} address 公司地址
 * @apiParam {string} areapro 省名称
 * @apiParam {string} areacity 市名称
 * @apiParam {string} areazone 区名称
 * @apiParam {string} gtids *经营范围，商品ID用逗号分隔
 * @apiParam {int} type *公司类型：1-厂商 2-一级经销商 3-二级经销商 4-零售商
 * @apiParam {string} tax_no *税号
 * @apiParam {string} account_no 帐号
 * @apiParam {string} license *营业执照
 * @apiParam {string} fax 传真
 * @apiParam {string} phone 电话
 * @apiParam {string} lawrep 企业法人
 * @apiParam {string} contactor *联系人姓名
 * @apiParam {string} contactor_phone *联系人电话
 * @apiParam {string} email Email地址
 * @apiParam {string} basedate *基准日，1到28之间
 * @apiParam {string} memo 备注
 *
 */

/**
 * @api {post} company/update_print_tpl 修改打印模版
 * @apiName company/update_print_tpl
 * @apiGroup Company
 * @apiVersion 0.0.1
 * @apiDescription 修改打印模版
 *
 * @apiSuccess {string} print_tpl 打印模版ID
 *
 */

/**
 * @api {post} company/reset_password/:id 重置公司默认用户密码
 * @apiName company/reset_password/:id
 * @apiGroup Company
 * @apiVersion 0.0.1
 * @apiDescription 重置公司默认用户密码
 *
 * @apiSuccess {string} username 用户名
 * @apiSuccess {string} password 新密码
 */

function company($action, $id = NULL){
	init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
	$app = \Slim\Slim::getInstance();
	$data = $app->params; //获取POST参数数组
	$my_model = new Company($id);
	$cid = $app->Sneaker->cid;
	switch($action){

		case 'update':
			init_log_oper($action, '修改公司资料');
			if (!is_numeric($id)) error(1100);

			Power::check_my_cid($id);
			param_need($data, ['name','gtids','basedate',
				'contactor','contactor_phone']);
			param_check($data, [
				'type,basedate' => "/^\d+$/",
				'gtids' => "/^[\d,]+$/",
			]);
			$data = format_data_ids($data, ['gtids']);

			//默认全部改成便利店
			//$data['type'] = 4;

			//获取公司地域级别
			$data['areatype'] = $my_model->get_area_type($data);

			//经营范围名称
			$gtids = get_value($data, 'gtids');
			if($gtids){
				$data['gtnames'] = $my_model->get_names_by_ids('o_goods_type', $gtids);
			}

			$my_model->my_update($data);
			success();
			break;

		case 'update_print_tpl':
			init_log_oper($action, '修改打印模版');
			param_need($data, ['print_tpl']);
			param_check($data, [
				'print_tpl' => "/^\d+$/",
			]);
			$my_model->set_id($cid);
			$my_model->update_by_id($data);
			success();
			break;

		case 'reset_password':
			init_log_oper($action, '重置默认用户密码');
			if (!is_numeric($id)) error(1100);
			$res = $my_model->my_reset_password();
			success($res);
			break;

		default:
			error(1100);
	}
}


