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
class CompanyGoods extends Object{

    /**
    * 入库所需字段（必须），如果加星号，代表可以插入但是不可以修改
    */
    protected $format_data = ['*gid','gname','gpyname','*gcode','gbarcode','gbid','gtid','gspec','gunit','gtax_rate',
        'gisbind','*in_cid','in_price','out_price1','out_price2','out_price3','out_price4','status','limit_buy','weight',
        'business','pool_rate'];
    
    //搜索字段
    protected $search_data = ['gname','gpyname','gcode','gbarcode'];

    //需要分和元转换的金额字段
    protected $amount_data = ['in_price','out_price1','out_price2','out_price3','out_price4'];

    //可排序的字段
    protected $order_data = ['code','in_price','out_price1','out_price2','out_price3','out_price4'];

    /**
     * constructor
     *
     * @param  int  $id     ID
     */
    public function __construct($id = NULL){
        parent::__construct('o_company_goods', $id);
    }

    /**
     * 创建公司商品档案
     *
     * @param array $data 商品档案字段
     * @return False|int
     */
    public function my_create($data){
        start_action();     //开启事务
        $g_model = new Goods();
        $gs_model = new GoodsSupplier();
        $data_new = $data;
        foreach($data as $key=>$val){
            $has_res = $this->has([
                'in_cid' => $val['in_cid'],
                'gid' => $val['gid']
            ]);
            if($has_res){
                unset($data_new[$key]);
                continue;
            }
            $goods_res = $g_model->read_by_id($val['gid']);
            if(!$goods_res){
                error(1423);
            }
            //将系统商品表商品信息拷贝到公司商品表
            $data_new[$key]['gname'] = $goods_res[0]['name'];
            $data_new[$key]['gpyname'] = $goods_res[0]['py_name'];
            $data_new[$key]['gcode'] = $goods_res[0]['code'];
            $data_new[$key]['gbarcode'] = $goods_res[0]['barcode'];
            $data_new[$key]['gbid'] = $goods_res[0]['bid'];
            $data_new[$key]['gspec'] = $goods_res[0]['spec'];
            $data_new[$key]['gunit'] = $goods_res[0]['unit'];
            $data_new[$key]['gtax_rate'] = $goods_res[0]['tax_rate'];
            $data_new[$key]['gisbind'] = $goods_res[0]['isbind'];

            //创建商品供应商关系
            if($val['out_cid']){
                $res = $gs_model->has([
                    'cid' => $val['in_cid'],
                    'scid' => $val['out_cid'],
                    'gid' => $val['gid']
                ]);
                if(!$res){
                    $gs_model->create([
                        'cid' => $val['in_cid'],
                        'scid' => $val['out_cid'],
                        'gid' => $val['gid']
                    ]);
                }
            }

        }
        $res = $this->create_batch(dict2list($data_new));
        return $res;
    }

    /**
     * 设置商品分类
     *
     * @param $data
     */
    public function  my_update($data, $gid){

        $gbid = get_value($data, 'gbid');
        $gbname = get_value($data, 'gbname');
        if(!$gbid && $gbname){
            $gb_model = new GoodsBrand();
            $data['gbid'] = $gb_model->my_create(['name'=>$data['gbname']]);
        }
        $this->update_by_id($data);

        $gname = get_value($data, 'gname');
        $gbarcode = get_value($data, 'gbarcode');
        $factory = get_value($data, 'factory');
        $place = get_value($data, 'place');
        $g_model = new Goods();

        $db_set = [];
        if($gname){
            $db_set['name'] = $gname;
        }
        if($gbarcode){
            $db_set['barcode'] = $gbarcode;
        }
        if($factory){
            $db_set['factory'] = $factory;
        }
        if($place){
            $db_set['place'] = $place;
        }

        if($db_set){
            $g_model->update($db_set,['id'=>$gid]);
        }

        return $this->id;
    }


    /**
     * 读取商品详情
     *
     * @return array 商品档案详情
     */
    public function my_read(){
        $res = $this->read_by_id();
        $g_model = new Goods();
        $goods_res = $g_model ->read_by_id($res[0]['gid']);
        $res[0]['goods'] = $goods_res[0];

        //完善商品品牌名称和类型名称
        $res = Change::go($res, 'gbid', 'gbname', 'o_goods_brand');
        $res = Change::go($res, 'gtid', 'gtname', 'o_company_goods_type');

        //商品类型归溯到根，返回到前段（用于树的展示）
        $cgt_model = new CompanyGoodsType();
        $res[0]['gtids'] = $cgt_model->read_tree_by_id($res[0]['gtid'], $res[0]['in_cid']);

        //返回供应商列表
        $gs_model = new GoodsSupplier();
        $gs_res = $gs_model->read_list_nopage([
            'cid'=>$res[0]['in_cid'],
            'gid'=>$res[0]['gid']
        ]);
        $gs_res = Change::go($gs_res, 'scid', 'scname', 'o_company');
        $res[0]['goods_supplier'] = $gs_res;

        return $res;
    }

    /**
     * 查询列表
     *
     * @param $data
     * @return array|False
     */
    public function my_read_list($data){
        //根据父类型定位条件为父类型下的所有子类型
        $gtid = get_value($data, 'gtid');
        if($gtid){
            $cgt_model = new CompanyGoodsType();
            $gtids = $cgt_model->get_ids_by_fid($gtid, $data['in_cid']);
            $data['gtid'] = $gtids;
        }
        $res = $this->read_list($data);
        return $res;
    }

    /**
     * 读取最老批次的价格
     *
     * @param $data
     * @return array|False
     */
    public function my_read_old($data){
        $res = $this->read_list($data);

        $r_model = new Reserve();

        $goods_list = [];
        if($res['count']){
            foreach($res['data'] as $key=>$val){
                unset($res['data'][$key]['in_price']);
                unset($res['data'][$key]['out_price1']);
                unset($res['data'][$key]['out_price2']);
                unset($res['data'][$key]['out_price3']);
                unset($res['data'][$key]['out_price4']);
                $goods_list[] = $val['gid'];
                //读取最早那个批次的价格
                $res['data'][$key]['price'] = $r_model->read_old_price($val['gid'], $data['sid'], $data['in_cid']);
            }

        }
        $cid = $this->app->Sneaker->cid;
        //获取库存信息
        $r_res = $r_model -> get_reserve($cid, $data['sid'], $goods_list);
        foreach($res['data'] as $key=>$val){
            $res['data'][$key]['reserve'] = get_value($r_res, $val['gid'], 0);
        }

        return $res;
    }

    /**
     * 读取商品列表（不获取价格）
     *
     * @param $data
     * @return array|False
     */
    public function my_read_noprice($data){
        $res = $this->read_list($data);

        $r_model = new Reserve();

        $goods_list = [];
        if($res['count']){
            foreach($res['data'] as $key=>$val){
                unset($res['data'][$key]['in_price']);
                unset($res['data'][$key]['out_price1']);
                unset($res['data'][$key]['out_price2']);
                unset($res['data'][$key]['out_price3']);
                unset($res['data'][$key]['out_price4']);
                $goods_list[] = $val['gid'];
            }
        }
        $cid = $this->app->Sneaker->cid;
        //获取库存信息
        $r_res = $r_model -> get_reserve($cid, $data['sid'], $goods_list);
        foreach($res['data'] as $key=>$val){
            $res['data'][$key]['reserve'] = get_value($r_res, $val['gid'], 0);
        }

        return $res;
    }

    //检测是否存在禁止购买的商品 True-存在 False-不存在
    public function has_limit_buy($goods_list, $cid){
        $goods_list = json_decode($goods_list, True);
        $gid_list = [];
        foreach($goods_list as $val){
            $gid_list[] = $val['gid'];
        }
        $res = $this->has([
            'gid'=>$gid_list,
            'in_cid'=>$cid,
            'limit_buy'=>2
        ]);
        return $res;
    }

    public function my_delete(){
        $res = $this->read_by_id();
        $gid = $res[0]['gid'];
        $r_model = new Reserve();
        //判断是否有库存记录
        $r_res = $r_model -> has([
            'gid'=>$gid,
            'cid'=>$this->app->Sneaker->cid
        ]);
        if($r_res){
            error(1420);
        }

        $this->delete_by_id();
        return True;
    }

    /**
     * 权限检测
     *
     * @param int $id 需要检测的记录ID
     * @return array 返回ID对应的记录信息
     */
    public function my_power($id){
        $res = $this -> read_by_id($id);
        if(!$res){
            error(1471);
        }
        if($res[0]['in_cid'] != $this->app->Sneaker->cid){
            error(1602);
        }
        return $res[0];
    }

    /**
     * 商品档案报表
     *
     * @param $data
     * @return array|False
     */
    public function form_goods($data){
        $param = [
            'in_cid'=>$data['cid']
        ];
        //如果传了类型ID，找到类型下的所有子类型节点，做数据过滤
        if(get_value($data, 'tids')){
            $cgt_model = new CompanyGoodsType();
            $param['gtid'] = $cgt_model->get_ids_by_fids($data['tids'], $data['cid']);
        }
        //如果传了供应商信息，增加商品ID检索条件
        $gs_model = new GoodsSupplier();
        if(get_value($data, 'scids')){
            $scids = $data['scids'];
            $scid_list = explode(',', $scids);
            $gs_res = $gs_model->read_list_nopage([
                'cid'=>$data['cid'],
                'scid'=>$scid_list
            ]);
            $gids = [];
            foreach($gs_res as $val){
                if(!in_array($val['gid'], $gids)){
                    $gids[] = $val['gid'];
                }
            }
            $param['gid'] = $gids;
        }
        //价格带作为查询条件
        $price_min = get_value($data, 'price_min');
        $price_max = get_value($data, 'price_max');
        if($price_min || $price_max){
            $param['in_price[>=]'] = yuan2fen($price_min);
            if($price_max){
                $param['in_price[<=]'] = yuan2fen($price_max);
            }
        }
        //关键字
        if(get_value($data, 'search')){
            $param['search'] = $data['search'];
        }
        //分页
        if(get_value($data, 'page')){
            $param['page'] = $data['page'];
        }
        if(get_value($data, 'page_num')){
            $param['page_num'] = $data['page_num'];
        }

        $res = $this->read_list($param);
        //返回的信息中添加商品品牌名称和类型名称
        $res['data'] = Change::go($res['data'], 'gbid', 'gbname', 'o_goods_brand');
        $res['data'] = Change::go($res['data'], 'gtid', 'gtname', 'o_company_goods_type');

        $gid_list = [];
        foreach($res['data'] as $val){
            $gid_list[] = $val['gid'];
        }

        $g_model = new Goods();
        $g_res = $g_model->read_list_nopage([
            'id'=>$gid_list
        ],['id','valid_period','factory','place','isbind']);
        $g_list = [];
        foreach($g_res as $val){
            $g_list[$val['id']] = $val;
        }

        //添加返回商品的供应商信息
        $scid_list2 = [];
        foreach($res['data'] as $key=>$val){
            $res['data'][$key]['goods'] = get_value($g_list, $val['gid'], []);

            $gs_res = $gs_model->read_list_nopage([
                'cid'=>$data['cid'],
                'gid'=>$val['gid']
            ]);
            foreach($gs_res as $val2){
                if(!in_array($val2['scid'], $scid_list2)){
                    $scid_list2[] = $val2['scid'];
                }
            }
            //$gs_res = Change::go($gs_res, 'scid', 'scname', 'o_company');
            $res['data'][$key]['goods_supplier'] = $gs_res;
        }
        $c_model = new Company();
        $c_res = $c_model->read_list_nopage([
            'id'=>$scid_list2
        ]);
        $c_list = [];
        foreach($c_res as $val){
            $c_list[$val['id']] = $val['name'];
        }

        foreach($res['data'] as $key=>$val){
            foreach($val['goods_supplier'] as $key2=>$val2){
                $res['data'][$key]['goods_supplier'][$key2]['scname'] = get_value($c_list, $val2['scid'], '无');
            }
        }

        return $res;
    }

    //获取公司商品
    public function get_one_goods($cid, $gid){
        $cg_res = $this->read_one([
            'in_cid'=>$cid,
            'gid'=>$gid
        ]);
        return $cg_res;
    }

    public function my_error($code, $gid, $cid){
        $cg_res = $this->read_one([
            'in_cid'=>$cid,
            'gid'=>$gid
        ]);
        $gname = $cg_res['gname'];
        error($code, $gname);
        return False;
    }

}

