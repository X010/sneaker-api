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
class Breturn extends Bill{

    /**
     * constructor
     *
     * @param  int  $id     ID
     */
    public function __construct($id = NULL){
        parent::__construct('b_order', $id);

    }

    /**
     * 审核退货单
     *
     * @param array $data 字段列表
     * @param string $type 类型，新建或更新
     * @return int 订单号
     */
    public function my_check($data, $type){
        //增加库存
        $omodel = new Order();
        $somodel = new StockOut($this->id);

        $somodel->my_check($data, $type, 2);
        //$somodel->set_id($stock_id);

        //创建并审核订单
        $data['out_cid'] = $this->app->Sneaker->cid;
        $data['out_cname'] = $this->app->Sneaker->cname;
        $data['ouid'] = $data['uid'] = $data['cuid'];
        $data['ouname'] = $data['uname'] = $data['cuname'];
        $data['out_sname'] = get_value($data, 'sname');
        $data['status'] = 2;
        $data['type'] = 2;

        //设置默认业务员和默认出货仓库
        $c_model = new Customer();
        $c_res = $c_model -> read_one([
            'cid' => $data['in_cid'],
            'ccid' => $data['out_cid']
        ]);
        if($c_res){
            $data['in_sid'] = $c_res['sid'];
            $data['in_sname'] = $this->get_name_by_id('o_store', $data['in_sid']);
            $data['buid'] = $c_res['suid'];
            $data['buname'] = $c_res['suname'];
        }

        $order_id = $omodel -> add($data);

        //反写订单号
        $somodel->update_by_id(['order_id'=>$order_id]);

        return $order_id;
    }

    /**
     * 冲正单据
     *-----------已废弃---------------
     * @return int 冲正单号
     */
    public function my_flush(){
        $rmodel = new Reserve();
        $somodel = new StockOut();
        $so_res = $somodel->read_one(['order_id'=>$this->id]);
        $somodel->set_id($so_res['id']);
        $data = $somodel -> read_all_by_id();
        $data = $data[0];
        //调出老单据

        start_action();
        $data_old = $data;
        //生成一个负单
        foreach($data['goods_list'] as $k1=>$v1){
            unset($data['goods_list'][$k1]['id']);
            foreach($v1 as $k2=>$v2){
                if(in_array($k2,['total','amount_price'])){
                    $data['goods_list'][$k1][$k2] = price_neg($data['goods_list'][$k1][$k2]);
                }
            }
        }
        $data['amount'] = price_neg($data['amount']);
        $data['status'] = 11;
        $data['goods_list'] = json_encode($data['goods_list']);
        unset($data['id']);
        unset($data['negative_id']);
        Power::set_oper($data);
        Power::set_oper($data, 'cuid', 'cuname');
        $data['checktime'] = date('Y-m-d H:i:s');
        $new_oid = $somodel->my_create($data);
        //开始入库这个单
        $rmodel->in($data_old['goods_list'], $data['sid'], $new_oid, 10);

        //把老单的字段和状态修正过来
        $somodel->update_by_id(['status'=>10, 'negative_id'=>$new_oid]);
        return $new_oid;
    }

    /**
     * 修正单据
     *-----------已废弃---------------
     * @param array $data_new 修正字段
     * @return int 修正单号
     */
    public function my_repaire($data_new){
        $rmodel = new Reserve();
        $somodel = new StockOut();
        $so_res = $somodel->read_one(['order_id'=>$this->id]);
        $somodel->set_id($so_res['id']);
        $data = $somodel -> read_all_by_id();
        $data = $data[0];
        //调出老单据

        start_action();
        $data_old = $data;
        //生成一个负单
        foreach($data['goods_list'] as $k1=>$v1){
            unset($data['goods_list'][$k1]['id']);
            foreach($v1 as $k2=>$v2){
                if(in_array($k2,['total','amount_price'])){
                    $data['goods_list'][$k1][$k2] = price_neg($data['goods_list'][$k1][$k2]);
                }
            }
        }
        $data['amount'] = price_neg($data['amount']);
        $data['status'] = 11;
        $data['goods_list'] = json_encode($data['goods_list']);
        unset($data['id']);
        unset($data['negative_id']);
        Power::set_oper($data);
        Power::set_oper($data, 'cuid', 'cuname');
        $data['checktime'] = date('Y-m-d H:i:s');
        $new_oid = $somodel->my_create($data);
        //开始入库这个单
        $rmodel->in($data_old['goods_list'], $data['sid'], $new_oid, 10);

        //把老单的字段和状态修正过来
        $somodel->update_by_id(['status'=>10, 'negative_id'=>$new_oid]);

        //开始入库新单
        $data['status'] = 3;
        $data['goods_list'] = $data_new['goods_list'];
        $data['repaired_id'] = $this->id;
        $res = $this->my_check($data, 'create');
        return $res;
    }

}

