<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * model of customer
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo
 * @version     0.0.1
 * @package     model
 */


class Geolocation extends Object{

    /**
     * 数据库字段（只允许以下字段写入）
     */
    protected $format_data = ['uid', 'cid', 'ccid', 'latitude', 'longgitude', 'altitude','accuracy','altitudeAccuracy','heading',
        'speed','timestamp','createtime','source','memo'];

    protected $search_data = ['memo'];

    /**
     * constructor
	 *
     * @param  int 	$id 	ID
     */
	public function __construct($id = NULL){
		parent::__construct('o_geolocation', $id);
	}

    public function form_salesman($data){

        $ugid = get_value($data, 'ugid');
        if($ugid){
            $ug_model = new UserGroup();
            $ugids = $ug_model->get_ids_by_fid($ugid);
            $u_model = new User();
            $u_res = $u_model->read_list_nopage(['cid'=>$data['cid'],'group_id'=>$ugids]);
            $uid_list = [];
            foreach($u_res as $val){
                $uid_list[] = $val['id'];
            }
            $data['uid'] = $uid_list;
        }

        $res = $this->read_list($data);

        $ct_model = new CustomerTmp();

        if($res['count']){
            $res['data'] = Change::go($res['data'], 'uid', 'suname', 'o_user');
            $res['data'] = Change::go($res['data'], 'ccid', 'ccname', 'o_company');

            foreach($res['data'] as $key=>$val){
                if($val['source'] == 4 && $val['memo']){
                    $pic_url = $this->app->config('photo_url'). $val['memo']. $this->app->config('photo_format')['IMG_SPEC_LG'];
                    $res['data'][$key]['pic_url'] = $pic_url;
                }
                if($val['source'] == 2){
                    $ct_res = $ct_model->read_one(['id'=>$val['ccid']]);
                    $res['data'][$key]['ccname'] = $ct_res['cname'];
                }
            }

        }
        return $res;
    }

}




