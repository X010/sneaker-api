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

class StoreGoods extends Object{

    /**
    * 入库所需字段（必须），如果加星号，代表可以插入但是不可以修改
    */
    protected $format_data = ['*gid','*in_cid','*in_sid','in_price',
                            'out_price1','out_price2','out_price3','out_price4','status'];
    
    //搜索字段
    protected $search_data = ['gid'];

    //需要分和元转换的金额字段
    protected $amount_data = ['in_price','out_price1','out_price2','out_price3','out_price4'];

    //可排序的字段
    protected $order_data = ['code','out_cid','in_price','out_price1','out_price2','out_price3','out_price4'];

    /**
     * constructor
     *
     * @param  int  $id     ID
     */
    public function __construct($id = NULL){
        parent::__construct('o_store_goods', $id);
    }

    /**
     * 创建仓库商品档案
     *
     * @param $data
     * @return int
     */
    public function my_create($data){
        $app = \Slim\Slim::getInstance();
        $res = '';
        foreach($data as $key=>$val){
            $price0 = get_value($val, 'in_price');
            $price1 = get_value($val, 'out_price1');
            $price2 = get_value($val, 'out_price2');
            $price3 = get_value($val, 'out_price3');
            $price4 = get_value($val, 'out_price4');
            $has_res = $app->db->has('o_store_goods',[
                'AND' => [
                    'in_sid' => $val['in_sid'],
                    'gid' => $val['gid']
                    ] 
                ]);
            if($has_res){
                //如果全部为0就删除记录
                if(!$price1 && !$price2 && !$price3 && !$price4){
                    $this->delete([
                        'AND' => [
                            'in_sid' => $val['in_sid'],
                            'gid' => $val['gid']
                        ]      
                    ]);
                }
                else{
                //如果已经存在就更新
                    $this->update($val,[
                        'AND' => [
                            'in_sid' => $val['in_sid'],
                            'gid' => $val['gid']
                        ]      
                    ]);   
                }
                unset($data[$key]);
                continue;
            }
            else{
                //剔除全部为0的
                if(!$price0 && !$price1 && !$price2 && !$price3 && !$price4){
                    unset($data[$key]);
                    continue;
                }
                //如果不存在就insert
                $goods_res = $app->db->select('o_goods','*',['id'=>$val['gid']]);
                if(!$goods_res){
                    error(1423);
                }
                $val['gname'] = $goods_res[0]['name'];
                $val['gcode'] = $goods_res[0]['code'];

                $res = $this->create($val);
            }
        }
        return $res;
    }


//    /**
//     * @param $data 设置商品价格
//     */
//    public function  my_update($data)
//    {
//        $app = \Slim\Slim::getInstance();
//        if(isset($data['out_cid'])){
//            $db_set['out_cid'] = $data['out_cid'];
//        }
//        if(isset($data['in_price'])){
//            $db_set['in_price'] = $data['in_price'];
//            $db_where['in_price'] = 0;
//        }
//        if(isset($data['out_price1'])){
//            $db_set['out_price1'] = $data['out_price1'];
//            $db_where['out_price1'] = 0;
//        }
//        if(isset($data['out_price2'])){
//            $db_set['out_price2'] = $data['out_price2'];
//            $db_where['out_price2'] = 0;
//        }
//        if(isset($data['out_price3'])){
//            $db_set['out_price3'] = $data['out_price3'];
//            $db_where['out_price3'] = 0;
//        }
//        foreach($db_set as $key=>$temp){
//            if(in_array($key, $this->amount_data)){
//                $db_set[$key] = yuan2fen($data[$key]);
//            }
//        }
//        if(count($db_where)>1){
//            $temp = $db_where;
//            $db_where = [];
//            $db_where['AND'] = $temp;
//        }
//        $ret=$app->db->update($this->tablename,$db_set,$db_where);
//        return $ret;
//    }


//    /**
//     * 读取商品价格详情
//     *
//     */
//    public function my_read(){
//
//        $app = \Slim\Slim::getInstance();
//        $res = $this->read_by_id();
//        $goods_res = $app->db->select('o_goods','*',['id'=>$res[0]['gid']]);
//        $change_model = new Change();
//        $goods_res = $change_model -> go($goods_res, 'bid', 'bname', 'o_goods_brand');
//        $goods_res = $change_model -> go($goods_res, 'tid', 'tname', 'o_goods_type');
//        $res[0]['goods'] = $goods_res[0];
//
//        return $res;
//    }

    /**
     * 创建仓库商品档案
     *
     * @param array $data
     * @param int $in_sid 仓库ID
     * @param bool $old_price 是否启用原价
     * @return int
     */
    public function read_in($data, $in_sid, $old_price=False){
        $goods_list = [];
        $tp_model = new TempPrice();

        if($old_price){
            foreach($data as $key=>$val){
                $data[$key]['price'] = $val['in_price'];
            }
        }
        else{
            $gid_list = [];
            foreach($data as $key=>$val){
                $gid_list[] = $val['gid'];
                $cid = $val['in_cid'];
            }
            $temp_price = $tp_model -> get_temp_prices($gid_list, $cid, $in_sid);
            foreach($data as $key=>$val){
                $gid_list[] = $val['gid'];
                $data[$key]['price'] = get_value($temp_price, $val['gid'], $val['in_price']);
            }
        }

        foreach($data as $key=>$val){
            unset($data[$key]['in_price']);
            unset($data[$key]['out_price1']);
            unset($data[$key]['out_price2']);
            unset($data[$key]['out_price3']);
            unset($data[$key]['out_price4']);
            $goods_list[] = $val['gid'];
        }

        $r_model = new Reserve();
        $cid = $this->app->Sneaker->cid;
        $r_res = $r_model -> get_reserve($cid, $in_sid, $goods_list);
        foreach($data as $key=>$val){
            $data[$key]['reserve'] = get_value($r_res, $val['gid'], 0);
        }
        return $data;
    }
    
    //读取出货商品清单
    public function read_out($data, $in_cid, $out_sid){
        if($in_cid != -1){
            $c_res = $this->app->db->select('r_customer','*',[
                'AND' => [
                    'cid' => $this->app->Sneaker->cid,
                    'ccid' => $in_cid
                ]
            ]);
            if(!$c_res){
                error(1710);
            }
            $cctype = $c_res[0]['cctype'];
            $price_name = 'out_price'. $cctype;
        }
        $goods_list = [];
        foreach($data as $key=>$val){
            if($in_cid != -1){
                $data[$key]['price'] = $val[$price_name];
                unset($data[$key]['out_price1']);
                unset($data[$key]['out_price2']);
                unset($data[$key]['out_price3']);
                unset($data[$key]['out_price4']);
            }
            else{
                $data[$key]['price'] = '0.00';
            }
            unset($data[$key]['in_price']);
            $goods_list[] = $val['gid'];
        }
        $r_model = new Reserve();
        $cid = $this->app->Sneaker->cid;
        $r_res = $r_model -> get_reserve($cid, $out_sid, $goods_list);
        foreach($data as $key=>$val){
            $data[$key]['reserve'] = get_value($r_res, $val['gid'], 0);
        }

        return $data;
    }

}

