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

class UserThird extends Object{
	/**
	 * 入库所需字段（必须）
	 */

	protected $format_data = ['uid','openid','cid','type','state','access_token','expires_in','refresh_token'];


	/**
	 * constructor
	 *
	 * @param  int 	$id 	ID
	 */
	public function __construct($id = NULL){
		parent::__construct('o_user_third', $id);
	}
	
	public function my_create($data){
//		$hres = $this->has([
//			'uid'=>$data['uid'],
//			'type'=>$data['type'],
//			'state'=>$data['state']
//		]);
//		if($hres){
//			error(6211);
//		}

		$hres = $this->has([
			'openid'=>$data['openid'],
			'state' => $data['state'],
			'type'=>$data['type']
		]);
		if($hres){
			error(6212);
		}

		$res = $this->create($data);
		return $res;
	}

	public function my_delete($data){
		$res = $this->delete([
			'AND' => [
				'openid'=>$data['openid'],
				'type'=>$data['type']
			]
		]);

		return $res;
	}
	
}

