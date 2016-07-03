<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * goods_type
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     model
 */

/**
 * TODO:
 */
class CompanyGoodsType extends Object{
	/**
	 * 入库所需字段（必须）
	 */
	protected $format_data = ['code','name','cid'];

	protected $order_data = ['code'];
	
	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('o_company_goods_type', $id);
	}
	
	/**
	 * 创建商品类型
	 *  @param data 	写库参数
	 *	@param int  	父类型ID
	 *					如果为0 代表创建一个根节点类型
	 *					
	 */
	public function my_create($data){
		if(!$this->id){
			//如果是创建一个根节点
			//首先找到编码最大的那个根节点，然后在基础上加一
			$res = $this->read_one([
				'cid'=> $data['cid'],
				'code[~]' => '__',
				'orderby' => 'code^DESC'
			]);
			if(!$res){
				//一个根节点都没有的情况
				$new_code = '01';
			}
			else{
				//新的code等于老的code加1，如果已经大于100了代表超过上限，不能再创建了
				$last_code = $res['code'];
				$new_code = $last_code + 1;
				if($new_code >= 100){
					error(1413);
				}
				$new_code = str_pad($new_code, 2, "0", STR_PAD_LEFT);
			}
			
		}
		else{
			//如果是创建一个子节点
			//首先找到父节点的code
			$res = $this->read_by_id();
			if(!$res){
				error(1414);
			}
			$fcode = $res[0]['code'];
			//然后找到改父节点下所有子节点里code最大的一个
			$res = $this->read_one([
				'cid'=>$data['cid'],
				'code[~]' => $fcode.'__',
				'orderby' => 'code^DESC'
			]);
			if(!$res){
				//一个子节点都没有的情况
				$new_code = $fcode.'01';
			}
			else{
				//生成一个新的code
				$last_code = $res['code'];
				$new_code = intval(substr($last_code, -2, 2)) + 1;
				if($new_code >= 100){
					error(1413);
				}
				$new_code = str_pad($new_code, 2, "0", STR_PAD_LEFT);
				$new_code = substr($last_code, 0, -2).$new_code;
			}
		}
		//创建节点
		$res = $this->create([
			'cid' => $data['cid'],
			'code' => $new_code,
			'name' => $data['name']
		]);
		return $res;
	}
	
	/**
	 * 修改商品类型名称
	 *
	 */
	public function my_update($data){
		$res = $this->update_by_id([
			'name' => $data['name']
		]);
		return $res;
	}
	
	/**
	 * 删除商品类型
	 *
	 */
	public function my_delete(){
		$app = \Slim\Slim::getInstance();
		//如果存在关联商品，不可被删除
		$db_where = [
			'AND' => [
				'in_cid' => $app->Sneaker->cid,
				'gtid' => $this->id
			]
		];
		$res = $app->db->has('o_company_goods', $db_where);
		if($res){
			error(1410);
		}

		//首先读取类型code
		$res = $this->read_by_id();
		if(!$res){
			error(1411);
		}
		$code = $res[0]['code'];

		//如果存在子类型，不可被删除
		$res = $this->has([
			'cid' => $app->Sneaker->cid,
			'code[~]' => $code.'%',
			'id[!]' => $this->id
		]);
		if($res){
			error(1412);
		}
		
		//直接删除数据
		$res = $this->delete_by_id();
		return $res;
	}
	
	/**
	 * 读取树形商品类型
	 *	@param int 		根ID,如果为0代表取出所有根节点
	 *
	 */
	public function read_tree($rid = 0){
		$cid = $this->app->Sneaker->cid;
		if(!$rid){
			//找到所有的根节点
			$res = $this->read_list([
				'cid' => $cid,
				'code[~]' => '__',
				'orderby' => 'code^ASC'
			]);
			return $res;
		}
		else{
			//首先找到父节点的code
			$res = $this->read_one([
				'cid' => $cid,
				'id' => $rid
			]);
			if(!$res){
				error(1414);
			}
			$fcode = $res['code'];
			
			//找到指定节点的子节点
			$res = $this->read_list([
				'cid' => $cid,
				'code[~]' => $fcode.'__',
				'orderby' => 'code^ASC'
			]);
			return $res;
		}
	}

	//通过父ID找到所有子ID（所有叶子）
	public function get_ids_by_fid($fid, $cid = Null){
		if(!$cid){
			$cid = $this->app->Sneaker->cid;
		}
		$res = $this->read_by_id($fid);
		if(!$res){
			error(1414);
		}
		$code = $res[0]['code'];
		$res = $this->read_list_nopage([
			'cid' => $cid,
			'code[~]' => $code.'%',
		]);
		$result = [];
		foreach($res as $val){
			$result[] = $val['id'];
		}
		return $result;

	}

	//通过批量父ID找到所有子ID
	public function get_ids_by_fids($fids, $cid = Null){
		$fid_list = explode(',', $fids);
		$all_tids = [];
		foreach($fid_list as $fid){
			if($fid){
				$temp_fids = $this->get_ids_by_fid($fid, $cid);
				$all_tids = array_merge($temp_fids, $all_tids);
			}
		}
		return $all_tids;
	}

	//读取本公司所有节点，按照id，pid，name的格式返回（前端指定格式参数）
	public function my_read_tree($cid){
		$res = $this->read_list_nopage([
			'cid' => $cid
		]);
		$code_dict = [];
		foreach($res as $val){
			$code_dict[$val['code']] = $val;
		}
		$result = [];
		foreach($res as $val){
			$my_code = $val['code'];
			if(strlen($my_code) == 2){
				$pid = 0;
			}
			else{
				$parent_code = substr($my_code, 0, -2);
				$parent_data = get_value($code_dict, $parent_code, []);
				$pid = get_value($parent_data, 'id', 0);
			}
			$result[] = [
				'id'=>$val['id'],
				'pId'=>$pid,
				'name'=>$val['name']
			];
		}
		return $result;
	}

	//通过一个节点ID追溯到所有父节点ID返回
	public function read_tree_by_id($id, $cid){
		$res = $this->read_by_id($id);
		if(!$res){
			return [];
		}
		$code = $res[0]['code'];
		$len = strlen($code);
		$result[] = $id;
		do{
			$code = substr($code, 0, -2);
			$len -= 2;
			if($len>0){
				$res = $this->read_one([
					'cid'=>$cid,
					'code'=>$code
				]);
				if(!$res){
					return [];
				}
				$result[] = $res['id'];
			}

		}while($len>0);

		$result = array_reverse($result);
		return $result;

	}

	//权限检测
	public function my_power(){
		$res = $this->read_by_id();
		if($res[0]['cid'] != $this->app->Sneaker->cid){
			error(8110);
		}
		return $res[0];
	}

	//复制系统分类到公司分类下，会先清除所有公司原有分类（初始化时候才可能调用）
	public function my_copy(){
		$cid = $this->app->Sneaker->cid;
		$now = date('Y-m-d H:i:s');
		//如果存在商品，不可被删除
		$db_where = [
			'in_cid' => $cid
		];
		$res = $this->app->db->has('o_company_goods', $db_where);
		if($res){
			error(1410);
		}

		//删除现有的分类
		$this->delete([
			'cid' => $cid
		]);

		$sql = "INSERT INTO `o_company_goods_type`(`cid`,`code`,`name`,`py_name`,`createtime`) SELECT ". $cid. ",`code`,`name`,`py_name`,'". $now.
			"' FROM `o_goods_type`";
		$this->app->db->query($sql);
		return True;
	}

	//清空商品分类
	public function my_flush(){
		//如果存在商品，不可被删除
		$cid = $this->app->Sneaker->cid;
		$db_where = [
			'in_cid' => $cid
		];
		$res = $this->app->db->has('o_company_goods', $db_where);
		if($res){
			error(1410);
		}

		//删除现有的分类
		$this->delete([
			'cid' => $cid
		]);

		return True;
	}

}

