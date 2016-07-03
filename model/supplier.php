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

class Supplier extends Object{

    /**
     * 数据库字段（只允许以下字段写入）
     */
    protected $format_data = ['*cid', '*cname', '*scid', 'scname', 'period', 'discount', 'auto_delete',
        'contactor', 'contactor_phone'];


    protected $search_data = ['scname'];

    //可排序的字段
    protected $order_data = ['createtime','updatetime','scname'];

    /**
     * constructor
     *
     * @param  int  $id     ID
     */
    public function __construct($id = NULL){
        parent::__construct('r_supplier', $id);
    }

//    /**
//     * 浏览供应商客户关系列表
//     *
//     * @param array $data   sql where
//     * @param array $fileds sql fields
//     */
//    public function view_list($data, $fileds = '*'){
//        $res = $this->read_list($data, $fileds);
//
//        return $res;
//    }


    /**
     * 批量添加客户关系
     *
     * @param array $datas   
     */
    public function add_batch($datas, $cid, $cname){
        foreach ($datas as $k => $v){
            $datas[$k]['cid'] = $cid;
            if($datas[$k]['scid'] == $cid){
                error(1730);
            }
            $datas[$k]['cname'] = $cname;
        }
        $res = $this->create_batch($datas);
        return $res;
    }

    public function my_recommend($cid){
        //找到本公司地域
        $c_model = new Company();
        $c_res = $c_model->read_by_id($cid);
        //查找包含本地域的那些仓库
        $db_where = [];
        if($c_res[0]['areatype'] == 1){
            $db_where = [
                'areatype' => 1
            ];
        }
        elseif($c_res[0]['areatype'] == 2){
            $db_where = [
                'OR'=>[
                    'areatype'=>1,
                    'AND'=>[
                        'areatype'=>2,
                        'areapro'=>$c_res[0]['areapro']
                    ]
                ]
            ];
        }
        elseif($c_res[0]['areatype'] == 3){
            $db_where = [
                'OR'=>[
                    'areatype'=>1,
                    'AND #the first'=>[
                        'areatype'=>2,
                        'areapro'=>$c_res[0]['areapro']
                    ],
                    'AND #the second'=>[
                        'areatype'=>3,
                        'areapro'=>$c_res[0]['areapro'],
                        'areacity'=>$c_res[0]['areacity']
                    ]
                ]
            ];
        }
        elseif($c_res[0]['areatype'] == 4){
            $db_where = [
                'OR'=>[
                    'areatype'=>1,
                    'AND #the first'=>[
                        'areatype'=>2,
                        'areapro'=>$c_res[0]['areapro']
                    ],
                    'AND #the second'=>[
                        'areatype'=>3,
                        'areapro'=>$c_res[0]['areapro'],
                        'areacity'=>$c_res[0]['areacity']
                    ],
                    'AND #the third'=>[
                        'areatype'=>4,
                        'areapro'=>$c_res[0]['areapro'],
                        'areacity'=>$c_res[0]['areacity'],
                        'areazone'=>$c_res[0]['areazone']
                    ]
                ]
            ];
        }
        $sa_model = new StoreArea();
        $sa_res = $sa_model->read_list($db_where);
        $sids = [];
        foreach($sa_res['data'] as $val){
            if(!in_array($val['sid'], $sids)){
                $sids[] = $val['sid'];
            }
        }
        //找到这些仓库，组合成供应商列表
        $s_model = new Store();
        $s_res = $s_model->read_list(['id'=>$sids]);
        $cids = [];
        foreach($s_res['data'] as $val){
            if(!in_array($val['cid'], $cids)){
                $cids[] = $val['cid'];
            }
        }
        //剔除掉已经加为供应商的
        $su_model = new Supplier();
        $su_res = $su_model->read_list(['cid'=>$cid]);
        $my_cids = [];
        foreach($su_res['data'] as $val){
            $my_cids[] = $val['scid'];
        }
        $my_cids[] = $cid;

        $new_cids = array_diff($cids, $my_cids);
        if($new_cids){
            //推荐列表
            $c_res = $c_model->read_list([
                'id'=>$new_cids
            ]);
            return $c_res['data'];
        }
        else{
            return [];
        }
    }

    public function my_register($data, $my_cid){
        //写公司信息
        start_action();
        $c_model = new Company();
        $data['areatype'] = $c_model->get_area_type($data);

        $gtids = get_value($data, 'gtids');
        if($gtids){
            $data['gtnames'] = $this->get_names_by_ids('o_goods_type', $gtids);
        }
        $data['iserp'] = 0;
        $data['create_cid'] = $my_cid;
        $cid = $c_model->add($data);

        //本公司把新公司加为供应商
        $this->create([
            'cid'=>$my_cid,
            'cname'=>$this->get_name_by_id('o_company', $my_cid),
            'scid'=>$cid,
            'scname'=>$data['name'],
            'period'=>$data['my_period'],
            'discount'=>$data['my_discount'],
            'auto_delete'=>$data['my_auto_delete'],
            'contactor'=>$data['contactor'],
            'contactor_phone'=>$data['contactor_phone'],
        ]);

        //注册一个默认仓库，公用公司的部分属性，仓库未开启库存管理功能
        $data['cid'] = $cid;
        $data['cname'] = $data['name'];
        $data['isreserve'] = 0;
        $data['name'] = $this->app->config('mall_default_store_name');
        $s_model = new Store();
        $s_model->my_create($data);

        return $cid;
    }
}




