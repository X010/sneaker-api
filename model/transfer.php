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

class Transfer extends Bill{

    /**
     * constructor
     *
     * @param  int  $id     ID
     */
    public function __construct($id = NULL){
        parent::__construct('b_order', $id);

    }

    /**
     * 审核调拨单
     *
     * @param array $data 字段列表
     * @param string $type 类型，新增或修改
     * @return int
     */
    public function my_check($data, $type){
        //增加库存
        $omodel = new Order($this->id);
        $somodel = new StockOut();
        if($type == 'create'){
            $order_id = $omodel->add($data);
        }
        else{
            $order_id = $omodel->modify($data);
            $ores = $this->read_by_id();
            $data['in_sid'] = $ores[0]['in_sid'];
            $data['out_sid'] = $ores[0]['out_sid'];
            $data['in_sname'] = $ores[0]['in_sname'];
            $data['out_sname'] = $ores[0]['out_sname'];
        }
        $data['order_id'] = $order_id;
        $data['cid'] = $this->app->Sneaker->cid;
        $data['cname'] = $this->app->Sneaker->cname;
        $data['uid'] = $data['cuid'];
        $data['uname'] = $data['cuname'];
        $data['type'] = 3;
        $data['status'] = 4;
        //创建并审核出库单
        $data['sid'] = $data['out_sid'];
        $data['sname'] = $data['out_sname'];
        $somodel -> my_check($data, 'create', 3);

        return $order_id;
    }

    /**
     * 收货调拨单
     *
     * @param array $data 字段列表
     * @param string $type 类型，新增或修改
     * @return int
     */
    public function my_receive($data, $type){
        //增加库存
        $omodel = new Order($this->id);
        $simodel = new StockIn();
        $somodel = new StockOut();
        if($type == 'create'){
            $order_id = $omodel->add($data);
            //是否需要出库，新创建肯定需要出库的
            $out_flag = 1;
        }
        else{
            $order_id = $this->id;
            $ores = $this->read_by_id();
            if($ores[0]['ouid']){
                $out_flag = 0;
            }
            else{
                $out_flag = 1;
            }

            if(!$ores[0]['cuid']){
                $order_id = $omodel->modify($data);
            }
            $data['in_sid'] = $ores[0]['in_sid'];
            $data['out_sid'] = $ores[0]['out_sid'];
            $data['in_sname'] = $ores[0]['in_sname'];
            $data['out_sname'] = $ores[0]['out_sname'];
        }
        $data['order_id'] = $order_id;
        $data['cid'] = $this->app->Sneaker->cid;
        $data['cname'] = $this->app->Sneaker->cname;

        Power::set_oper($data);
        $data['type'] = 3;
        //判断是否已经出库，如果已经出库就不创建出库单
        if($out_flag){
            //创建并审核出库单
            //$data['status'] = 4;
            $data['sid'] = $data['out_sid'];
            $data['sname'] = $data['out_sname'];
            $somodel -> my_check($data, 'create', 3);
        }
        //创建并审核入库单
        $data['status'] = 2;
        $data['sid'] = $data['in_sid'];
        $data['sname'] = $data['in_sname'];
        $simodel -> my_check($data, 'create', 3);
        return $order_id;
    }
    
}

