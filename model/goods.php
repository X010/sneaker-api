<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * goods
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     model
 */

class Goods extends Object{
	/**
	 * 入库所需字段（必须）
	 */
	protected $format_data = ['name','*code','spec','bid','tid','*cid','*ispkg','*isbind',
				'trademark','valid_period','price_type','shipping_price','tax_rate','pkgspec',
				'unit','marketing','factory','salerate','default_supplier','auto_add','place',
				'output_tax','buyer','distribution','distribution_units','barcode','status'];
	
	//搜索字段
    protected $search_data = ['name','code','py_name'];

	/**
     	*  code 自动生成的前缀  11-公司  21-商品  31-仓库 51-员工
     	*/
	protected $code_pre = '21';
	

	/**
	* 列表返回字段，如果无此字段默认返回全部
	*/
	protected $list_return = ['id','code','py_name','name','spec','bid','tid','barcode','unit','valid_period','tax_rate','status','trademark','isbind'];

	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('o_goods', $id);
	}
	
	/**
	 * 增加商品
	 *
	 */	
	public function my_create($data){
		$force = get_value($data, 'force');

		$barcode = get_value($data, 'barcode');
		$gid = 0;
		$cg_model = new CompanyGoods();

		start_action();
		$bid = get_value($data, 'bid');
		if(!$bid){
			param_need($data, ['bname']);
			$gb_model = new GoodsBrand();
			$bid = $gb_model->my_create(['name'=>$data['bname']]);
		}

		$barcode_res = $this->has(['barcode'=>$barcode]);
		if($barcode_res){

			//如果条码重复
			$cg_res = $cg_model->has([
				'in_cid'=>$data['cid'],
				'gbarcode'=>$barcode
			]);
			//如果公司级别条码重复，报错
			if($cg_res){
				error(1424);
			}

			if(!$force){
				return $gid;
			}

			//自动导入并更新公司级别
			$g_res = $this->read_one(['barcode'=>$barcode]);
			$data_new['gid'] = $g_res['id'];
			$data_new['gname'] = $data['name'];
			require_once 'core/pinyin.php';
			$data_new['gpyname'] = pinyin($data['name']);
			$data_new['gcode'] = $g_res['code'];
			$data_new['gbarcode'] = $g_res['barcode'];
			$data_new['gbid'] = $bid;
			$data_new['gspec'] = $data['spec'];
			$data_new['gunit'] = $data['unit'];
			$data_new['gtax_rate'] = $data['tax_rate'];
			$data_new['gisbind'] = $g_res['isbind'];
			$data_new['gtid'] = $data['gtid'];
			$data_new['in_cid'] = $data['cid'];
			$data_new['out_cid'] = $data['out_cid'];
			$data_new['in_price'] = $data['in_price'];
			$data_new['out_price1'] = $data['out_price1'];
			$data_new['out_price2'] = $data['out_price2'];
			$data_new['out_price3'] = $data['out_price3'];
			$data_new['out_price4'] = $data['out_price4'];
			$data_new['business'] = $data['business'];
			$data_new['weight'] = $data['weight'];

			$cg_model->create($data_new);

			//创建商品供应商关系
			$gs_model = new GoodsSupplier();
			$gs_model->create([
				'cid' => $data['cid'],
				'scid' => $data['out_cid'],
				'gid' => $gid
			]);
		}
		else{
			//如果是新条码
			//生成code
			$code = $this->get_code();
			$data['code'] = $code;
			$data['tid'] = -1;

			start_action(); //开启事务

			$gid = $this->create($data);

			//将系统商品表商品信息拷贝到公司商品表
			$goods_res = $this->read_by_id($gid);

			$data_new['gid'] = $gid;
			$data_new['gname'] = $goods_res[0]['name'];
			$data_new['gpyname'] = $goods_res[0]['py_name'];
			$data_new['gcode'] = $goods_res[0]['code'];
			$data_new['gbarcode'] = $goods_res[0]['barcode'];
			$data_new['gbid'] = $bid;
			$data_new['gspec'] = $goods_res[0]['spec'];
			$data_new['gunit'] = $goods_res[0]['unit'];
			$data_new['gtax_rate'] = $goods_res[0]['tax_rate'];
			$data_new['gisbind'] = $goods_res[0]['isbind'];
			$data_new['gtid'] = $data['gtid'];
			$data_new['in_cid'] = $data['cid'];
			$data_new['out_cid'] = $data['out_cid'];
			$data_new['in_price'] = $data['in_price'];
			$data_new['out_price1'] = $data['out_price1'];
			$data_new['out_price2'] = $data['out_price2'];
			$data_new['out_price3'] = $data['out_price3'];
			$data_new['out_price4'] = $data['out_price4'];
			$data_new['business'] = $data['business'];
			$data_new['weight'] = $data['weight'];

			$cg_model->create($data_new);

			//创建商品供应商关系
			$gs_model = new GoodsSupplier();
			$gs_model->create([
				'cid' => $data['cid'],
				'scid' => $data['out_cid'],
				'gid' => $gid
			]);

			$this->app->db->insert('r_input_code',[
				'gid'=>$gid,
				'gcode'=>$code,
				'itype'=>0,
				'createtime'=>date('Y-m-d H:i:s'),
				'input_code'=>$barcode
			]);
		}
		return $gid;
	}
	
	/**
	 * 修改商品
	 *
	 */
	public function my_update($data){
		$app = \Slim\Slim::getInstance();

		//判断品牌和类型是否已存在
		$this->_isset_brand_and_type($data);
	
		$res = parent::update_by_id($data);
		return $res;
	}

	/**
	 * @param $data 模糊查询商品
	 */
	public function  read_vague_by_name($data)
	{
		$app = \Slim\Slim::getInstance();
		$ret=$app->db->select('o_goods',"*",[
			'AND'=>[
				#'status' =>1,
				'cid'    =>$data['cid'],
				'name[~]'=>'%'.$data['name'].'%'
			]
		]);
		return $ret;
	}
	
	/**
	 * 删除商品
	 *
	 */
	public function my_delete(){
		$app = \Slim\Slim::getInstance();
		//判断是否还有库存，如果还有库存，则不可删除商品
		$db_where = [
				'gid' => $this->id
		];
		//$res = $app->db->select('r_reserve', 'count', $db_where);
		$res = $app->db->has('r_reserve', $db_where);
		if($res){
			error(1420); //该商品存在库存，不可被删除
		}
		
		$res = parent::delete_only_status();
		return $res;
	}

	public function my_read($data){


		$sql = "select code,name,id,barcode,unit,spec from `$this->tablename` where 1=1";

		$tid = get_value($data, 'tid');
		if($tid){
			$gt_model = new GoodsType();
			$tids = $gt_model->get_ids_by_fid($tid);
			$sql.= " and tid in(".implode(',', $tids).")";
		}

		$search = get_value($data, 'search');
		if($search){
			$search = $data['search'];
			$sql .= " and (name like '%$search%' or code like '%$search%' or py_name like '%$search%')";
		}
		$cid = $this->app->Sneaker->cid;

		if(!$search){
			$sql .= " and id not in(select gid from `o_company_goods` where in_cid=$cid)";
		}

		$page = get_value($data, 'page', 1);
		$page_num = get_value($data, 'page_num', 100);
		$start_count = ($page - 1) * $page_num;
		$sql .= ' limit '. $start_count. ','. $page_num;

		$res = $this->app->db->query($sql)->fetchAll();
		$result = [];
		foreach($res as $val){
			$result[] = [
				'code'=>$val['code'],
				'id'=>$val['id'],
				'name'=>$val['name'],
				'barcode'=>$val['barcode'],
				'unit'=>$val['unit'],
				'spec'=>$val['spec'],
			];
		}

		return $res;
	}

	public function my_error($code, $gid){
		$res = $this->read_by_id($gid);
		$gname = $res[0]['name'];
		error($code, $gname);
		return False;
	}


	/**
	 * 判断品牌和类型是否已存在
	 *
	 */
	private function _isset_brand_and_type($data){
		$app = \Slim\Slim::getInstance();

		//判断bid是否存在
		if(isset($data['bid'])){
			$db_where = [
				'id' => $data['bid']
			];
			//$ret = $app -> db -> select('o_goods_brand', '*', $db_where);
			$ret = $app -> db -> has('o_goods_brand', $db_where); //faster
			if(!$ret){
				error(1422); //商品品牌不存在
			}
		}
		//判断tid是否存在
		if(isset($data['tid'])){
			$db_where = [
				'id' => $data['tid']
			];
			//$ret = $app -> db -> select('o_goods_type', '*', $db_where);
			$ret = $app -> db -> has('o_goods_type', $db_where); //faster
			if(!$ret){
				error(1421); //商品类型不存在
			}

		}
	}
	
	
}

