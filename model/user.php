<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * user
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     model
 */

class User extends Object{
	/**
	 * 入库所需字段（必须）
	 */

	protected $format_data = ['*code','username','password','name','idcard','sids','rids','*cid',
				'*cname','worktype','email','phone','memo','*admin','discount','status','belong','group_id'];

	//搜索字段
    protected $search_data = ['name','username','py_name'];

	/**
    *  code 自动生成的前缀  11-公司  21-商品  31-仓库  51-员工
    */
	protected $code_pre = '51';

	/**
	* 列表返回字段，如果无此字段默认返回全部
	*/
	protected $list_return = ['id','code','name','username','worktype','phone','email','rids','sids','status','logintime','discount','idcard','memo','admin',
		'belong','group_id'];

	//可排序的字段
	protected $order_data = ['code','worktype','logintime','status','id'];

	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('o_user', $id);
	}
	
	public function my_create($data){
		$app = \Slim\Slim::getInstance();
		//判断所属公司是否存在，考虑到创建一定是本公司ID，非前端传入不可能不存在，所以先不判断
		// $db_where = [
		// 	'AND' => [
		// 		'id' => $data['cid'],
		// 		'status' => 1
		// 	]	
		// ];
		// //$ret = $app -> db -> select('o_store','id', $db_where);
		// $ret = $app->db->has('o_company', $db_where); //faster
		// if(!$ret){
		// 	error(1340); //员工所属部门不存在
		// }

		//判断角色身份存在，角色和仓库不判断问题也不大，先不判断
		// $ret = $app -> db -> select('s_role','id',[
		// 	'id' => $data['rids'],
		// ]);
		// $rids_list = explode(',', $data['rids']);
		// if(count($ret) != count($rids_list)){
		// 	error(1341);
		// }

		//生成code
		$code = parent::get_code();
		$data['code'] = $code;

		//username不能重复
		$db_where = [
			'username' => $data['username'],
			#'status' => 1
		];
		$ret = $app -> db -> has('o_user', $db_where);
		if($ret){
			error(1345);
		}
		
		$data['password'] = my_password_hash($data['password']);
		$res = parent::create($data);
		return $res;
	}
	
	/**
	 * 修改员工信息
	 *
	 */
	public function my_update($data, $allow_all = false){
		if(isset($data['password'])){
			$data['password'] = my_password_hash($data['password']);
		}
		$res = parent::update_by_id($data, $allow_all);
		//更新登陆的ticket内容信息
		$login_module = new Login();
		$platform = $this->app->platform;
		$login_module->login_refresh($this->id, $platform);
		return $res;
	}

//	/**
//	 * @param $data 模糊查询用户
//	 */
//	public function  read_vague_by_name($data)
//	{
//		$app = \Slim\Slim::getInstance();
//		$ret=$app->db->select('o_user',"*",[
//			'AND'=>[
//				#'status' =>1,
//				'cid'    =>$data['cid'],
//				'name[~]'=>'%'.$data['name'].'%'
//			]
//		]);
//		return $ret;
//	}

	/**
	 * 删除员工信息
	 *
	 */
	public function my_delete($data){
		$res = parent::delete_only_status();

		$this->update_by_id([
			'username'=>'delete_'.$this->id,
			'name'=>'name_'.$this->id,
			//'phone'=>'delete_'.$this->id,
		]);

		//删除用户登陆信息
		$login_module = new Login();
		$platform = $this->app->platform;
		$login_module->logout_by_uid($this->id, $platform);
		return $res;
	}
	
	
	/**
	 * 修改密码
	 *
	 */
	public function change_password($uid, $password, $old_password){
		//验证旧密码
		$res = parent::read('*', [
			'id' => $uid
		]);
		if(!isset($res[0])){
			error(1300);
		}
		if(my_password_hash($old_password) != $res[0]['password']){
			error(1302);
		}
		
		//设置新密码
		$res = parent::update([
			'password' => my_password_hash($password),
		],[
			'id' => $uid
		]);
		return $res;
	}
	
	/**
	 * 重置密码
	 *
	 */
	public function reset_password($uid){
		$app = \Slim\Slim::getInstance();
		$password = $app -> config('default_password');
		$res = parent::update([
			'password' => my_password_hash($password),
		],[
			'id' => $uid
		]);
		return $res;
	}

	//获取公司第一个用户
	public function get_first_user($cid){
		$res = $this->read_one([
			'cid'=>$cid,
			'orderby'=>'id^ASC'
		]);
		return $res;
	}
	
}

