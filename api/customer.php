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
 * @api {post} customer/create 添加客户关系（已停用）
 * @apiName customer/create
 * @apiGroup Customer
 * @apiVersion 0.0.1
 * @apiDescription 添加客户关系操作（只能为本公司添加客户）
 *
 * @apiParam {int} ccid *客户公司ID
 * @apiParam {string} ccname *客户公司名称
 * @apiParam {int} cctype *客户公司类型
 *
 * @apiSuccess {int} id 客户关系ID
 *
 */

/**
 * @api {post} customer/create_batch 批量添加客户关系
 * @apiName customer/create_batch
 * @apiGroup Customer
 * @apiVersion 0.0.1
 * @apiDescription 批量添加客户关系操作（只能为本公司添加客户）
 *
 * @apiParam {string} data *客户公司信息块
 * @apiParam {json} - data字段详情
 * @apiParam {int} ccid *客户公司ID
 * @apiParam {string} ccname *客户公司名称
 * @apiParam {int} cctype *客户公司类型
 * @apiParam {int} period *客户账期
 * @apiParam {int} sid *出库仓库ID
 * @apiParam {int} suid *业务员ID
 *
 */

/**
 * @api {post} customer/update(/:id) 修改客户关系信息
 * @apiName customer/update
 * @apiGroup Customer
 * @apiVersion 0.0.1
 * @apiDescription 修改客户关系信息操作
 *
 * @apiParam {int} cctype *客户公司类型
 * @apiParam {int} period *客户账期
 * @apiParam {int} sid *出库仓库ID
 * @apiParam {int} suid *业务员ID
 *
 */

/**
 * @api {post} customer/delete/:id 删除客户关系
 * @apiName customer/delete
 * @apiGroup Customer
 * @apiVersion 0.0.1
 * @apiDescription 物理删除客户关系操作
 *
 *
 */


/**
 * @api {post} customer/register 注册客户
 * @apiName customer/register
 * @apiGroup Customer
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

/**
 * @api {post} customer/check_pass 审核客户通过
 * @apiName customer/check_pass
 * @apiGroup Customer
 * @apiVersion 0.0.1
 * @apiDescription 审核客户通过
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

/**
 * @api {post} customer/check_unpass 审核客户不通过
 * @apiName customer/check_unpass
 * @apiGroup Customer
 * @apiVersion 0.0.1
 * @apiDescription 审核客户不通过
 *
 * @apiParam {string} refuse_memo 拒绝原因
 *
 */

/**
 * @api {post} customer/add_salesman 增加业务员
 * @apiName customer/add_salesman
 * @apiGroup Customer
 * @apiVersion 0.0.1
 * @apiDescription 增加业务员
 *
 * @apiParam {string} suid 业务员ID
 * @apiParam {string} ccid 客户ID
 *
 * @apiSuccess {int} id 新增记录ID
 * @apiSuccess {int} default_id 默认记录ID
 */

/**
 * @api {post} customer/delete_salesman/:id 删除业务员
 * @apiName customer/delete_salesman/:id
 * @apiGroup Customer
 * @apiVersion 0.0.1
 * @apiDescription 删除业务员
 *
 * @apiSuccess {int} default_id 默认记录ID
 */

/**
 * @api {post} customer/default_salesman/:id 设置默认业务员
 * @apiName customer/default_salesman/:id
 * @apiGroup Customer
 * @apiVersion 0.0.1
 * @apiDescription 设置默认业务员
 *
 */

function customer($action, $id = NULL){
	init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
	$app = \Slim\Slim::getInstance();
	$data = $app->params;
	$my_model = new Customer($id);
	$cid = $app->Sneaker->cid;
	switch($action){
//		case 'create':
//			init_log_oper($action, '添加客户关系');
//			param_need($data, ['ccid','ccname','cctype','suid','sid','period']); //必选
//			param_check($data, [
//				'ccid,cctype,suid,sid,period' => "/^\d+$/",
//			]);
//			//判断客户不能为自己公司
//			if($data['ccid'] == $cid){
//				error(1720);
//			}
//			$data['cid'] = $cid;
//			$data['cname'] = $app->Sneaker->cname;
//			//创建客户关系
//			$id = $my_model->create($data);
//			success([
//				'id' => $id,
//			]);
//			break;

		case 'create_batch':
			init_log_oper($action, '批量添加客户关系');
			param_need($data, ['data']); //必选
			$params = json_decode($data['data'], True);
			if (!$params) error(1102);

			foreach($params as $val){
                param_need($val, ['ccid','ccname','cctype','suid','sid','period','contactor','contactor_phone']); //必选
				param_check($val, [
					'ccid,cctype,suid,sid,period' => "/^\d+$/",
				]);
            }

			//批量创建客户关系
			$my_model->add_batch($params, $cid, $app->Sneaker->cname);

			success();
			break;

		case 'update':
			init_log_oper($action, '修改客户信息');
			if (!is_numeric($id)) error(1100);
			param_need($data, ['cctype','sid','period']); //必选
			param_check($data, [
				'cctype,sid,period' => "/^\d+$/",
				//'phone' => "/^[0-9]{11}$/"
			]);

			$res = $my_model->read_by_id();
			if (!$res || $res[0]['cid'] != $cid) error(8110); //数据权限验证

			//客户联系人和公司联系人不一定一样，要分开
			$data2 = $data;
			$data2['contactor'] = $data['contactor_cus'];
			$data2['contactor_phone'] = $data['contactor_phone_cus'];
			if(get_value($data2, 'ccname')){
				require_once 'core/pinyin.php';
				$data2['ccpyname'] = pinyin($data2['ccname']);
			}
			$my_model->update_by_id($data2);

			$ccid = $res[0]['ccid'];
			$c_model = new Company($ccid);
			$sc_res = $c_model->read_by_id($ccid);
			//判断如果是本公司创建的并且非ERP公司，允许修改公司信息
			if($sc_res[0]['iserp'] == 0 && $sc_res[0]['create_cid'] == $cid){

				//获取公司地域级别
				$data['areatype'] = $c_model->get_area_type($data);
				//经营范围名称
				$gtids = get_value($data, 'gtids');
				if($gtids){
					$data['gtnames'] = $c_model->get_names_by_ids('o_goods_type', $gtids);
				}
				//更新公司信息
				$c_model->my_update($data);

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
			init_log_oper($action, '删除客户关系');
			if (!is_numeric($id)) error(1100);
			//权限校验
        	$my_model->my_power($id);

			$my_model->delete_by_id(); //物理删除
			success();
			break;

		case 'register':
			init_log_oper($action, '注册客户');
			param_need($data, ['name','gtids','type','contactor','contactor_phone','username',
				'password','my_suid','my_sid','my_period','address']); //必选
			param_check($data, [
				'type,my_suid,my_sid,my_period' => "/^\d+$/",
				'gtids' => "/^[,\d]+$/",
				//'phone' => "/^[0-9]{11}$/"
			]);

			$data = format_data_ids($data, ['gtids']);
			//地址校验，保证地址只能有4个空格分成5个段：省，市，区，街道，地址
			$address_list = explode(' ', $data['address']);
			$count = count($address_list);
			if($count > 5){
				for($i=5;$i<$count;$i++){
					$address_list[4] .= $address_list[$i];
					unset($address_list[$i]);
				}
			}
			$data['address'] = implode(' ', $address_list);

			$id = $my_model->my_register($data, $cid);
			success(['id'=>$id]);
			break;

		case 'check_pass':
			init_log_oper($action, '审核客户通过');
			if (!is_numeric($id)) error(1100);
			param_need($data, ['name','gtids','type','contactor','contactor_phone','username',
				'password','my_suid','my_sid','my_period']); //必选
			param_check($data, [
				'type,my_suid,my_sid,my_period' => "/^\d+$/",
				'gtids' => "/^[,\d]+$/",
				//'phone' => "/^[0-9]{11}$/"
			]);
			$data = format_data_ids($data, ['gtids']);

			//地址校验
			$address_list = explode(' ', $data['address']);
			$count = count($address_list);
			if($count > 5){
				for($i=5;$i<$count;$i++){
					$address_list[4] .= $address_list[$i];
					unset($address_list[$i]);
				}
			}
			$data['address'] = implode(' ', $address_list);

			//注册客户
			$my_model->my_register($data, $cid);
			$ct_model = new CustomerTmp($id);

			//回写临时客户表
			$ct_model->update_by_id([
				'account'=>$data['username'],
				'phone'=>$data['username'],
				'password'=>$data['password'],
				'province'=>$address_list[0],
				'city'=>$address_list[1],
				'country'=>$address_list[2],
				'street'=>$address_list[3],
				'address'=>$address_list[4],
				'areapro'=>$data['areapro'],
				'areacity'=>$data['areacity'],
				'areazone'=>$data['areazone'],
				'contractor'=>$data['contactor'],
				'ctype'=>$data['type'],
				'gtids'=>$data['gtids'],
				'cname'=>$data['name'],
				'suid'=>$data['my_suid'],
				'period'=>$data['my_period'],
				'status'=>1
			]);
			success($id);
			break;

		case 'check_unpass':
			init_log_oper($action, '审核客户不通过');
			if (!is_numeric($id)) error(1100);
			$ct_model = new CustomerTmp($id);

			$ct_res = $ct_model->read_by_id();
			if(!$ct_res){
				error(8110);
			}
			if($ct_res[0]['cid'] != $cid){
				error(8110);
			}

			//回写临时客户表
			$ct_model->update_by_id([
				'status'=>2,
				'memo'=>get_value($data, 'refuse_memo')
			]);
			success($id);
			break;

		case 'add_salesman':
			init_log_oper($action, '指定客户添加业务员');
			param_need($data, ['suid','ccid']);
			$data['cid'] = $cid;
			//添加业务员
			$res = $my_model->add_salesman($data);
			$cs_model = new CustomerSalesman();
			//读取业务员客户关系表中的主要业务员记录
			$d_res = $cs_model->read_one([
				'cid'=>$cid,
				'ccid'=>$data['ccid'],
				'type'=>1
			]);
			success([
				'id'=>$res,
				'default_id'=>$d_res['id']
			]);
			break;

		case 'delete_salesman':
			init_log_oper($action, '指定客户删除业务员');

			$cs_model = new CustomerSalesman();
			$cs_res = $cs_model->read_by_id();
			$ccid = $cs_res[0]['ccid'];

			//删除业务员记录
			$my_model->delete_salesman();

			//读取业务员客户关系表中的主要业务员记录，如果已经没有业务员了，返回0
			$d_res = $cs_model->read_one([
				'cid'=>$cid,
				'ccid'=>$ccid,
				'type'=>1
			]);
			if($d_res){
				$default_id = $d_res['id'];
			}
			else{
				$default_id = 0;
			}
			success([
				'default_id'=>$default_id
			]);
			break;

		case 'default_salesman':
			init_log_oper($action, '设置默认业务员');
			//设置默认业务员
			$my_model->default_salesman();
			success();
			break;

		default:
			error(1100);
	}
}