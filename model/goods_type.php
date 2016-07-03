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

class GoodsType extends Object{
	/**
	 * 入库所需字段（必须）
	 */
	protected $format_data = ['code','name'];

	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('o_goods_type', $id);
	}
	
	/**
	 * 创建商品类型
	 *  @param data 	写库参数
	 *	@param int  	父类型ID
	 *					如果为0 代表创建一个根节点类型
	 *					
	 */
	public function my_create($data, $fid = 0){
		if(!$fid){
			//如果是创建一个根节点
			
			//首先找到编码最大的那个根节点，然后在基础上加一
			$res = parent::read('*',[
				'code[~]' => '__',
				'ORDER' => 'code DESC'
			]);
			if(!$res){
				//一个根节点都没有的情况
				$new_code = '01';
			}
			else{
				$last_code = $res[0]['code'];
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
			$res = parent::read('*',[
				'id' => $fid
			]);
			if(!$res){
				error(1414);
			}
			$fcode = $res[0]['code'];
			//然后找到改父节点下所有子节点里code最大的一个
			$res = parent::read('*',[
				'code[~]' => $fcode.'__',
				'ORDER' => 'code DESC'
			]);
			if(!$res){
				//一个子节点都没有的情况
				$new_code = $fcode.'01';
			}
			else{
				$last_code = $res[0]['code'];
				$new_code = substr($last_code, -2, 2) + 1;
				if($new_code >= 100){
					error(1413);
				}
				$new_code = str_pad($new_code, 2, "0", STR_PAD_LEFT);
				$new_code = substr($last_code, 0, -2).$new_code;
			}
		}
		//创建节点
		$res = parent::create([
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
		$res = parent::update_by_id([
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
			'tid' => $this->id
		];
		$res = $app->db->has('o_goods', $db_where);
		if($res){
			error(1410);
		}
		
		//如果存在子类型，不可被删除
		//首先读取类型code
		$res = parent::read_by_id();
		if(!$res){
			error(1411);
		}
		$code = $res[0]['code'];
		
		$res = parent::read('*',[
			'AND' => [
				'code[~]' => $code.'%',
				'id[!]' => $this->id
		]]);
		if($res){
			error(1412);
		}
		
		//直接删除数据
		$res = parent::delete_by_id();
		return $res;
	}
	
	/**
	 * 读取树形商品类型
	 *	@param int 		根ID,如果为0代表取出所有根节点
	 *
	 */
	public function read_tree($rid = 0){
		if(!$rid){
			//取根节点
			$res = parent::read('*',[
				'code[~]' => '__',
				'ORDER' => 'code ASC'
			]);
			return $res;
		}
		else{
			//首先找到父节点的code
			$res = parent::read('*',[
				'id' => $rid
			]);
			if(!$res){
				error(1414);
			}
			$fcode = $res[0]['code'];
			
			//取子节点
			$res = parent::read('*',[
				'code[~]' => $fcode.'__',
				'ORDER' => 'code ASC'
			]);
			return $res;
		}
	}

	//读取本公司所有节点，按照id，pid，name的格式返回
	public function my_read_tree(){
		$res = $this->read_list_nopage([]);
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

	//通过父ID找到所有子ID（所有叶子）
	public function get_ids_by_fid($fid){
		$res = $this->read('*',[
			'id' => $fid
		]);
		if(!$res){
			error(1414);
		}
		$code = $res[0]['code'];
		$res = $this->read('*',[
			'code[~]' => $code.'%',
		]);
		$result = [];
		foreach($res as $val){
			$result[] = $val['id'];
		}
		return $result;

	}

}

