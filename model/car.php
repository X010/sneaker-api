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

/**
 * TODO:
 */
class Car extends Object{

	/**
	* 入库所需字段（必须），如果加星号，代表可以插入但是不可以修改
	*/
	protected $format_data = ['*cid','style','license','ton','model','memo','status'];
	
	//搜索字段
    protected $search_data = ['license'];

	/**
	* 列表返回字段，如果无此字段默认返回全部
	*/
	#protected $list_return = ['id','code','name','address','contactor','phone','contactor','status','memo','updatetime','createtime','isreserve'];

	//可排序的字段
	protected $order_data = ['ton','status','id'];


	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('o_car', $id);
	}

	/**
	 * 新建仓库
	 *
	 * @param array $data 字段列表
	 * @return int 仓库ID
     */
	public function my_create($data){
		//车牌号强制转大写
		$data['license'] = strtoupper($data['license']);

		//判断车牌号不能重复
		$res = $this->has([
			'license'=>$data['license'],
		]);
		if($res){
			error(1800);
		}

		//写入数据库
		$res = $this->create($data);

		return $res;
	}

	/**
	 * @param $data
	 * @param $cid
	 * @return bool|int
     */
	public function my_update($data){
		//车牌号强制转大写
		$data['license'] = strtoupper($data['license']);

		//判断车牌号不能重复
		$res = $this->has([
			'license'=>$data['license'],
			'id[!]'=>$this->id
		]);
		if($res){
			error(1800);
		}

		//写入数据库
		$this->update_by_id($data);

		return $this->id;
	}
	
}

