<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * store
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     model
 */

class Store extends Object{

	/**
	* 入库所需字段（必须），如果加星号，代表可以插入但是不可以修改
	*/
	protected $format_data = ['name','*code','address','phone','contactor','*cid','memo','status','isreserve'];
	

	/**
     	*  code 自动生成的前缀  11-公司  21-商品  31-仓库 51-员工
     	*/
	protected $code_pre = '31';
	
	//搜索字段
    protected $search_data = ['name','code','py_name'];

	/**
	* 列表返回字段，如果无此字段默认返回全部
	*/
	#protected $list_return = ['id','code','name','address','contactor','phone','contactor','status','memo','updatetime','createtime','isreserve'];

	//可排序的字段
	protected $order_data = ['code','status','id'];


	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('o_store', $id);
	}

	/**
	 * 新建仓库
	 *
	 * @param array $data 字段列表
	 * @return int 仓库ID
     */
	public function my_create($data){
		$res = $this->has([
			'name'=>$data['name'],
			'cid'=>$data['cid'],
		]);
		if($res){
			error(1603);
		}

		//生成code
		$code = $this->get_code();
		$data['code'] = $code;
		start_action();
		//写入数据库
		$sid = $this->create($data);

		//写仓库区域表
		$this->set_area($data, $sid);

		return $sid;
	}

	public function my_update($data, $cid=None){
		if($cid){
			$res = $this->has([
				'name'=>$data['name'],
				'cid'=>$cid,
				'id[!]'=>$this->id
			]);
			if($res){
				error(1603);
			}
		}
		start_action();
		$this->update_by_id($data);
		$sa_model = new StoreArea();
		$sa_model->delete_by_sid($this->id);
		$this->set_area($data, $this->id);
		return $this->id;
	}

	public function my_read(){
		$res = $this -> read_by_id();
		if(!isset($res[0])){
			error(1601); //仓库不存在
		}
		if($res[0]['cid'] != $this->app->Sneaker->cid){
			error(1602);
		}

		$sa_model = new StoreArea();
		$sa_res = $sa_model->read_list([
			'sid'=>$this->id
		]);

		if($sa_res['count']){
			$res[0]['areatype'] = $sa_res['data'][0]['areatype'];
		}
		else{
			$res[0]['areatype'] = Null;
		}
		$res[0]['store_area'] = $sa_res['data'];
		return $res;
	}

	public function set_area($data, $sid){
		$sa_data = [];
		//先判断类型
		$areatype = 4;
		if(!get_value($data, 'areazone')){
			$areatype = 3;
		}
		if(!get_value($data, 'areacity')){
			$areatype = 2;
		}
		if(!get_value($data, 'areapro')){
			$areatype = 1;
		}
		switch($areatype){
			case 4:
				$areazones = explode(',', $data['areazone']);
				foreach($areazones as $areazone){
					if($areazone){
						$sa_data[] = [
							'sid'=>$sid,
							'areatype'=>$areatype,
							'areapro'=>get_value($data, 'areapro'),
							'areacity'=>get_value($data, 'areacity'),
							'areazone'=>$areazone
						];
					}
				}
				break;
			case 3:
				$areacitys = explode(',', $data['areacity']);
				foreach($areacitys as $areacity){
					if($areacity){
						$sa_data[] = [
							'sid'=>$sid,
							'areatype'=>$areatype,
							'areapro'=>get_value($data, 'areapro'),
							'areacity'=>$areacity,
						];
					}
				}
				break;
			case 2:
				$areapros = explode(',', $data['areapro']);
				foreach($areapros as $areapro){
					if($areapro){
						$sa_data[] = [
							'sid'=>$sid,
							'areatype'=>$areatype,
							'areapro'=>$areapro,
						];
					}
				}
				break;
		}
		$sa_model = new StoreArea();
		$sa_model->create_batch($sa_data);
		return True;
	}

	/**
	 * @param $data 模糊查询仓库
	 */
	public function  read_vague_by_name($data)
	{
		$app = \Slim\Slim::getInstance();
		$ret=$app->db->select('o_store',"*",[
			'AND'=>[
				#'status' =>1,
				'cid'    =>$data['cid'],
				'name[~]'=>'%'.$data['name'].'%'
			]
		]);
		return $ret;
	}

	//获取公司下的默认仓库
	public function get_first_store($cid){
		$res = $this->read_one([
			'cid'=>$cid,
			'orderby'=>'id^asc'
		]);
		return $res;
	}

	/**
	 * 删除仓库
	 *
	 * @return int
	 */
	public function my_delete(){
		
		//判断是否还有库存，如果还有库存，则不可删除仓库
		$app = \Slim\Slim::getInstance();
		
		$db_where = [
			'sid' => $this->id
		];
		$res = $app->db->has('r_reserve', $db_where); //faster
		if($res){
			error(1700); //门店还有库存
		}
		
		$res = parent::delete_only_status();
		return $res;
	}

	/**
	 * 是否开启库存
	 *
	 * @param int $sid 仓库ID
	 * @return bool True-开启 False-未开启
     */
	public function is_reserve($sid){
		$s_res = $this -> read_by_id($sid);
        if($s_res && $s_res[0]['isreserve']){
        	return True;
        }
        else{
        	return False;
        }
	}
	
}

