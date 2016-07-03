<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * role
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     model
 */

class Role extends Object{
	/**
	 * 入库所需字段（必须）
	 */
	protected $format_data = ['name','level','cid','mids','status'];

	//搜索字段
    protected $search_data = ['name','py_name'];

	protected $order_data= ['name','cid'];
	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('s_role', $id);
	}

	public function my_delete(){
		//判断有没有有效员工属于该角色，如果有则不可删除
		//TODO貌似不好实现。。。
		//可否考虑直接删除，后面判断权限等地方如果遇到查询不到角色的情况，则当做角色不存在直接忽略即可？
		$res = parent::delete_only_status();
		return $res;
	}

	/**
	 * 自定义读取列表方法
	 */
	public  function my_readlist($data, $db_fields = '*', $status = False){
		//默认OR CID为-1的，表示系统自主创建

	}

	/**
	 * 读取角色权限
	 *
	 */
	public function read_power(){
		$app = \Slim\Slim::getInstance();
		$res = parent::read_by_id();
		$module_ids = $res[0]['mids'];
		$module_idlist = explode(',', $module_ids);
		$res = $app -> db -> select('s_module', '*', [
			'id' => $module_idlist
		]);
		return $res;
	}

    // /**
    //  * @param $data读取包含系统预设的角色名称
    //  */
    // public function read_include_company($data)
    // {
    //     $app = \Slim\Slim::getInstance();
    //     $ores = parent::read_list($data, '*', true); //先读取自己公司的数据
    //     $cres=$app->db->select("s_role",'*',[
    //         "AND"=>[
    //                 "status"=>1,
    //                 "cid"   =>-1
    //             ]
    //         ]);
    //     $res=array_merge($ores,$cres);
    //     return$res;
    // }

	/**
	 * 根据角色名称进行模糊查询
	 * @param $data 参数数组
	 */
	public function read_vague_by_name($data){
		$app = \Slim\Slim::getInstance();
		$ret=$app->db->select('s_role',"*",[
			'AND'=>[
				#'status' =>1,
				'cid'    =>$data['cid'],
				'name[~]'=>'%'.$data['name'].'%'
			]
		]);
		return $ret;
	}

	/**
	 * 设置角色权限
	 *
	 */
	public function update_power($mids){
		$app = \Slim\Slim::getInstance();

		//先确认这些模块ID是否都存在
		if($mids){
			$ret = $app -> db -> select('s_module', 'id', [
				'id' => $mids
			]);
			$mids_list = explode(',', trim($mids, ','));
			if(count($ret) != count($mids_list)){
				error(1330);
			}
		}
		$res = parent::update_by_id([
			'mids' => $mids
		]);
		return $res;
	}

}

