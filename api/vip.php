<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * customer 客户管理
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} vip/register 注册会员
 * @apiName vip/register
 * @apiGroup Vip
 * @apiVersion 0.0.1
 * @apiDescription 注册客户
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
 * @apiParam {string} memo 备注
 * @apiParam {string} username *用户名
 * @apiParam {string} password *密码
 * @apiParam {string} my_suid *业务员ID
 * @apiParam {string} my_sid *出库仓库ID
 * @apiParam {string} my_period *客户账期
 */

function vip($action, $id = NULL){
	init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
	$app = \Slim\Slim::getInstance();
	$data = $app->params;
	$my_model = new Customer($id);
	$cid = $app->Sneaker->cid;
	switch($action){
		
		case 'register':
			init_log_oper($action, '注册会员');
			param_need($data, ['name','type','contactor','username','my_sid']); //必选
			param_check($data, [
				'type,my_sid' => "/^\d+$/",
			]);

			if(!get_value($data, 'vip_end_date')){
				$data['vip_end_date'] = Null;
			}
			if(!get_value($data, 'vip_logistics')){
				$data['vip_logistics'] = 0;
			}

			$days = days_sub($data['vip_end_date'], date('Y-m-d'));
			if($days < 0 ){
				$days = 0;
			}
			$vip_config = $app->config('vip_config');
			$daily_reduce = 0;
			if($data['type'] > 1){
				$price = $vip_config[$data['type']]['price'];
				foreach($price as $key=>$val){
					$daily_reduce = round($val/$key-0.005, 2);
				}
			}
			$data['daily_reduce'] = $daily_reduce;
			$data['vip_balance'] = ($days+1) * $daily_reduce;

			$id = $my_model->my_register2($data, $cid);
			success(['id'=>$id]);
			break;

		case 'update':
			init_log_oper($action, '修改会员信息');
			if (!is_numeric($id)) error(1100);
			param_need($data, ['name','type','contactor','username','my_sid']); //必选

			$res = $my_model->read_one([
				'cid' => $cid,
				'ccid' => $id
			]);
			$my_model->set_id($res['id']);
			//if (!$res || $res[0]['cid'] != $cid) error(8110); //数据权限验证

			if(get_value($data, 'name')){
				require_once 'core/pinyin.php';
				$data['ccpyname'] = pinyin($data['name']);
			}

			if(!get_value($data, 'vip_end_date')){
				$data['vip_end_date'] = Null;
			}
			if(!get_value($data, 'vip_logistics')){
				$data['vip_logistics'] = 0;
			}

			$days = days_sub($data['vip_end_date'], date('Y-m-d'));
			if($days < 0 ){
				$days = 0;
			}
			$vip_config = $app->config('vip_config');
			$daily_reduce = 0;
			if($data['type'] > 1){
				$price = $vip_config[$data['type']]['price'];
				foreach($price as $key=>$val){
					$daily_reduce = round($val/$key-0.005, 2);
				}
			}
			$data['vip_balance'] = ($days+1) * $daily_reduce;

			$data2 = [
				'ccname'=>$data['name'],
				'ccpyname'=>$data['ccpyname'],
				'cctype'=>$data['type'],
				'sid'=>$data['my_sid'],
				'contactor'=>$data['contactor'],
				'contactor_phone'=>$data['username'],
				'vip_end_date'=>$data['vip_end_date'],
				'vip_logistics'=>$data['vip_logistics'],
				'vip_balance' => $data['vip_balance'],
				'vip_daily_reduce' => $daily_reduce
			];
			$my_model->update_by_id($data2);

			$ccid = $id;
			$c_model = new Company($ccid);
			$sc_res = $c_model->read_by_id($ccid);
			if($sc_res[0]['create_cid'] == $cid){

				//更新公司信息
				$data3 = [
					'contactor_phone' => $data['username'],
					'contactor' => $data['name'],
					'name' => $data['name'],
					'py_name' => $data['ccpyname'],
					'type' => $data['type'],
					'memo' => $data['memo']
				];
				$c_model->my_update($data3);

				$u_model = new User();
				$u_res = $u_model->get_first_user($ccid);
				$u_model->set_id($u_res['id']);

				if(get_value($data, 'username')){
					$res = $u_model->has([
						'username'=>$data['username'],
						'id[!]'=>$u_res['id']
					]);
					if($res){
						error(1345);
					}
					$u_model->update_by_id([
						'phone'=>get_value($data, 'phone'),
						'username'=>$data['username']
					]);
				}
			}
			success();
			break;

		case 'delete':
			init_log_oper($action, '删除会员信息');

			$res = $my_model->read_one([
				'cid' => $cid,
				'ccid' => $id
			]);
			if(!$res){
				error(8110);
			}
			$my_model->set_id($res['id']);
			$my_model -> update_by_id(['vip_status' => 9]);
			success();
			break;

		case 'recover':
			init_log_oper($action, '启用会员信息');
			$res = $my_model->read_one([
				'cid' => $cid,
				'ccid' => $id
			]);
			if(!$res){
				error(8110);
			}
			$my_model->set_id($res['id']);

			$my_model -> update_by_id(['vip_status' => 1]);
			success();
			break;

		default:
			error(1100);
	}
}