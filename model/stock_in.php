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

class StockIn extends Bill{

    /**
    * 入库所需字段（必须），如果加星号，代表可以插入但是不可以修改
    */
    protected $format_data = ['*order_id','*cid','*cname','sid','sname','type','memo','uid','uname','status',
        'cuid','cuname','ruid','runame', 'buid','buname','amount','repaired_id','negative_id','settle_id','out_cid',
        'out_cname','tax_amount','checktime','settletime','lastdate','cost_amount','pay_type','hash', 'business',
        'settle_status','gids','box_total','box_total2'];


    //搜索字段自动匹配
    protected $search_data = ['id','order_id'];

    /**
     * 数据库字段（只允许以下字段写入）
     */
    protected $format_data_glist = ['*id', '*order_id', 'sn', '*gid', '*gname', '*gpyname', '*gcode', '*gbarcode', '*gtid', '*gbid',
        '*gspec', '*gunit', '*gtax_rate', '*total', '*unit_price', '*amount_price', '*memo', '*tax_price', '*reserveid', '*expdate', '*prodate', 'cost_price'];

    //需要分和元转换的金额字段
    protected $amount_data = ['amount','tax_amount','cost_amount'];

    protected $order_data = ['id'];

    protected $sidname = 'sid';


    // /**
    // * 列表返回字段，如果无此字段默认返回全部
    // */
    // protected $list_return = ['id','code','name','address','contactor','phone','contactor','status','memo','updatetime','createtime'];

    /**
     * constructor
     *
     * @param  int  $id     ID
     */
    public function __construct($id = NULL){
        parent::__construct('b_stock_in', $id);
    }

    /**
     * 新建入库单
     *
     * @param array $data 新建字段
     * @return int 入库单号
     */
    public function my_create($data){

        //商品列表整理
        $data_format = Order::format($data, $data['cid']);
        $data = $data_format['data'];
        $glist = $data_format['glist'];

        //开始处理捆绑转换，将所有大商品转换成小商品
        $glist = $this -> get_sbind($glist, $data['cid'], $data['sid']);
        $glist = $this -> get_sn($glist);
        $data['hash'] = $this->my_hash($glist);

        start_action();

        $res = $this->create_all($data, $glist);
        //更新订单入库人字段
        if(isset($data['order_id'])){
            $o_order = new Order($data['order_id']);
            $o_order->modify([
                'iuid'=>$data['uid'], 
                'iuname'=>$data['uname']
            ]);
        }
        
        if (!$res) error(9902);
        return $res;
    }

    /**
     * 修改入库单
     *
     * @param array $data 修改字段列表
     * @return int 入库单号
     */
    public function my_update($data){
        start_action();
        if (get_value($data, 'goods_list')){
            //商品列表整理
            $data_format = Order::format($data);
            $data = $data_format['data'];
            $glist = $data_format['glist'];

            //开始处理捆绑转换，将所有大商品转换成小商品
            $glist = $this -> get_sbind($glist, $this->app->Sneaker->cid, $data['in_sid']);
            $glist = $this -> get_sn($glist);
            $data['hash'] = $this->my_hash($glist);
            $my_res = $this->read_by_id();

            if($data['hash'] == $my_res[0]['hash']){
                $res = $this->update_by_id($data);
            }
            else{
                //修改订单
                $res = $this->update_all($data, $glist);
            }
            if (!$res) error(9902);
        }
        else{
            start_action();
            $this->update_by_id($data);
        }

        return $this->id;
    }

    
    /**
     * 取消入库单
     *
     * @return int
     */
    public function my_delete(){
        $data = ['status'=>9];
        $res = $this->update_by_id($data);
        return $res;
    }


    /**
     * 审核入库单
     *
     * @param array $data 字段列表
     * @param string $type 类型，新增或修改
     * @param int $bill_type 入库类型
     * @return int
     */
    public function my_check($data, $type, $bill_type = 1){
        $data['status'] = 2;
        $data['checktime'] = date('Y-m-d H:i:s');

        //增加库存
        $rmodel = new Reserve();
        if($type == 'create'){
            $my_id = $this->my_create($data);

            $sid = $data['sid'];
        }
        elseif($type == 'update'){
            //修改审核的时候开启事务
            start_action();

            $res = $this->my_update($data);
            $my_id = $this->id;
            
            $ores = $this->read_by_id();
            $sid = $ores[0]['sid'];
        }
        $goods_list = $this->get_goods_list($my_id);

        foreach($goods_list as $key=>$goods){
            $goods_list[$key]['scid'] = get_value($data, 'out_cid');
            $goods_list[$key]['scname'] = get_value($data, 'out_cname');
        }
        //开始入库
        $ccid = get_value($data, 'out_cid');
        $ccname = get_value($data, 'out_cname');
        $r_res = $rmodel -> in($goods_list, $sid, $my_id, $bill_type, $ccid, $ccname);

        $cost_amount = 0;
        if($r_res !== True){
            foreach($r_res as $sn=>$val){
                $this->app->db->update('b_stock_in_glist', ['cost_price'=>$val], [
                    'AND'=>[
                        'sn'=>$sn,
                        'stock_in_id'=>$my_id
                    ]
                ]);
                $cost_amount += $val;
            }
        }

        $this->app->db->update($this->tablename, ['cost_amount'=>$cost_amount],[
            'id'=>$my_id
        ]);

        return $my_id;
    }

    /**
     * 读取入库单详情
     *
     * @return array
     */
    public function my_read(){
        $res = $this -> read_all_by_id();

        if($res[0]['buid']){
            $temp = $this->app->db->select('o_user','phone',['id'=>$res[0]['buid']]);
            $res[0]['buphone'] = $temp[0];
        }

        return $res;
    }

    /**
     * 冲正单据
     *
     * @return int 冲正单号
     */
    public function my_flush(){
        $rmodel = new Reserve();
        //调出老单据
        $data = $this -> read_all_by_id();
        $data = $data[0];
        //开启事务
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
        $new_oid = $this->my_create($data);
        //开始出库这个单
        $rmodel->out($data_old['goods_list'], $data['sid'], 9);

        //把老单的字段和状态修正过来
        $this->update_by_id(['status'=>10, 'negative_id'=>$new_oid]);

        //－－－2016-03-07改成不回写订单
        //如果老单据是根据订单生成，则修改订单的入库状态
//        if($data['order_id']){
//            $o_model = new Order($data['order_id']);
//            $o_model->update_by_id([
//                'iuid'=>Null,
//                'iuname'=>Null
//            ]);
//        }
        return $new_oid;
    }

    //修正单据
    public function my_repaire($data_new){
        $rmodel = new Reserve();

        //商品列表整理
        $data_format = Order::format($data_new);
        $data = $data_format['data'];
        $glist = $data_format['glist'];

        //开始处理捆绑转换，将所有大商品转换成小商品
        $glist = $this -> get_sbind($glist, $this->app->Sneaker->cid, $data['in_sid']);
        $glist = $this -> get_sn($glist);
        $new_hash = $this->my_hash($glist);

        //调出老单据
        $data = $this -> read_all_by_id();
        $data = $data[0];
        $old_hash = $data['hash'];

        if($old_hash == $new_hash){
            error(3114);
        }

        //开启事务
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
        $data['status'] = 13;
        $data['goods_list'] = json_encode($data['goods_list']);
        unset($data['id']);
        unset($data['negative_id']);
        Power::set_oper($data);
        Power::set_oper($data, 'cuid', 'cuname');
        $data['checktime'] = date('Y-m-d H:i:s');
        $new_oid = $this->my_create($data);

        //把老单的字段和状态修正过来
        $this->update_by_id(['status'=>12, 'negative_id'=>$new_oid]);

        //开始入库新单
        $data['status'] = 2;
        $data['goods_list'] = $data_new['goods_list'];
        $data['repaired_id'] = $this->id;
        $res = $this->my_check($data, 'create');

        //先入库再出库，防止库存不足
        //开始出库这个单
        $rmodel->out($data_old['goods_list'], $data['sid'], 9);

        return $res;
    }

    /**
     * 冲正退货单据
     *
     * @return int 冲正单号
     */
    public function my_return_flush(){
        $rmodel = new Reserve();
        //调出老单据
        $data = $this -> read_all_by_id();
        $data = $data[0];
        //开启事务
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
        $new_oid = $this->my_create($data);
        //开始出库这个单
        $goods_list = $data_old['goods_list'];
        $ccid = $data['out_cid'];
        $ccname = $data['out_cname'];
        $r_res = $rmodel->out($goods_list, $data['sid'], 10, $ccid, $ccname);

        //把老单的字段和状态修正过来
        $this->update_by_id(['status'=>10, 'negative_id'=>$new_oid]);

        //－－－2016-03-07改成不回写订单
        //如果老单据是根据订单生成，则修改订单的入库状态
//        if($data['order_id']){
//            $o_model = new Order($data['order_id']);
//            $o_model->update_by_id([
//                'iuid'=>Null,
//                'iuname'=>Null
//            ]);
//        }

        return $new_oid;
    }

    //修正退入单据
    public function my_return_repaire($data_new){
        $rmodel = new Reserve();

        //商品列表整理
        $data_format = Order::format($data_new);
        $data = $data_format['data'];
        $glist = $data_format['glist'];

        //开始处理捆绑转换，将所有大商品转换成小商品
        $glist = $this -> get_sbind($glist, $this->app->Sneaker->cid, $data['in_sid']);
        $glist = $this -> get_sn($glist);
        $new_hash = $this->my_hash($glist);

        //调出老单据
        $data = $this -> read_all_by_id();
        $data = $data[0];
        $old_hash = $data['hash'];

        if($old_hash == $new_hash){
            error(3114);
        }

        //开启事务
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
        $data['status'] = 13;
        $data['goods_list'] = json_encode($data['goods_list']);
        unset($data['id']);
        unset($data['negative_id']);
        Power::set_oper($data);
        Power::set_oper($data, 'cuid', 'cuname');
        $data['checktime'] = date('Y-m-d H:i:s');
        $new_oid = $this->my_create($data);

        //把老单的字段和状态修正过来
        $this->update_by_id(['status'=>12, 'negative_id'=>$new_oid]);

        //开始入库新单
        $data['status'] = 2;
        $data['goods_list'] = $data_new['goods_list'];
        $data['repaired_id'] = $this->id;
        $res = $this->my_check($data, 'create');

        //先入库再出库，防止库存不足
        //开始出库这个单
        $ccid = $data['out_cid'];
        $ccname = $data['out_cname'];
        $goods_list = $data_old['goods_list'];
        $r_res = $rmodel->out($goods_list, $data['sid'], 10, $ccid, $ccname);

        return $res;
    }

    /**
     * 通过单号查找商品列表
     *
     */    
    public function get_goods_list($id){
        $app = \Slim\Slim::getInstance();
        $ret = $app->db->select('b_stock_in_glist', '*', ['stock_in_id'=>$id]);
        foreach($ret as $k=>$v){
            foreach($v as $key=>$val){
                if(in_array($key, ['unit_price','amount_price'])){
                    $ret[$k][$key] = fen2yuan($ret[$k][$key]);
                }
            }
        }
        return $ret;    
    }

    public function my_power($id, $status, $type, $settle_status=Null){
        $app = \Slim\Slim::getInstance();
        //$res = $app->db->select('b_stock_in', '*', ['id' => $id]);
        $res = $this->read_by_id($id);
        if(!$res){
            error(3110);
        }
        if(isset($res[0]['cid']) && $res[0]['cid'] != $app->Sneaker->cid){
            error(3111);
        }
        if($type && $res[0]['type'] != $type){
            error(3112);
        }
        if($status && $res[0]['status'] != $status){
            error(3113);
        }

        if($settle_status !== Null){
            if($res[0]['settle_status'] != $settle_status){
                error(3126);
            }
        }

        Power::check_my_sid($res[0]['sid']);
        
        return $res[0];
    }

    //应付款查询
    public function form_payment($data){
        $begindate = get_value($data, 'begin_date');
        $last_date = get_value($data, 'last_date');

        $orderby = get_value($data, 'orderby');
        $where_db = ' cid='. $data['cid']. ' and out_cid is not null and type=1';
        //退货单流程条件
        $where_db2 = ' cid='. $data['cid']. ' and in_cid is not null and type=2 ';
        //如果传了仓库，要按仓库为检索条件
        if(get_value($data, 'sids')){
            $where_db .= ' and sid in ('. addslashes($data['sids']). ')';
            $where_db2 .= ' and sid in ('. addslashes($data['sids']). ')';
        }
        if(get_value($data, 'scids')){
            $where_db .= ' and out_cid in ('. addslashes($data['scids']). ')';
            $where_db2 .= ' and in_cid in ('. addslashes($data['scids']). ')';
        }
        //$where_db .= " and checktime>='". addslashes($data['date']). " 00:00:00'";
        $where_db .= " and checktime<='". addslashes($data['date']). " 23:59:59'";
        $where_db .= " and checktime>='". addslashes($begindate). " 00:00:00'";
        $where_db .= ' and status=2';
        $where_db2 .= " and checktime<='". addslashes($data['date']). " 23:59:59'";
        $where_db2 .= " and checktime>='". addslashes($begindate). " 00:00:00'";
        $where_db2 .= ' and status=4';

        $page = get_value($data, 'page', 1);
        $page_num = get_value($data, 'page_num', 200);
        $start_count = ($page - 1) * $page_num;

        $count_sql = "select count(distinct out_cid) as val,sum(case `settle_status` when 0 then 1 else 0 end) as all_total,".
            "sum(case `settle_status` when 0 then `amount` else 0 end) as all_amount,".
            "sum(case `settle_status` when 1 then `amount` else 0 end) as real_amount,".
            "sum(case `settle_status` when 1 then 1 else 0 end) as real_total,".
            "sum(case when lastdate<='". addslashes($last_date). "' and settle_status=0 then `amount` else 0 end) as exp_amount,".
            "sum(case when lastdate<='". addslashes($last_date). "' and settle_status=0 then 1 else 0 end) as exp_total from `b_stock_in` where ". $where_db;
        $count_res = $this->app->db->query($count_sql)->fetchAll();
        $all_count = $count_res[0]['val'];
        $add_up = [
            'all_amount'=>fen2yuan($count_res[0]['all_amount']),
            'real_amount'=>fen2yuan($count_res[0]['real_amount']),
            'all_total'=>$count_res[0]['all_total'],
            'real_total'=>$count_res[0]['real_total'],
            'exp_amount'=>fen2yuan($count_res[0]['exp_amount']),
            'exp_total'=>$count_res[0]['exp_total']
        ];
        $all_page = intval($all_count/$page_num);
        if($all_count%$page_num!=0){
            $all_page ++;
        }

        $sql = "select out_cid,out_cname,sum(case `settle_status` when 0 then 1 else 0 end) as all_total,".
            "sum(case `settle_status` when 0 then `amount` else 0 end) as all_amount,".
            "sum(case `settle_status` when 1 then `amount` else 0 end) as real_amount,".
            "sum(case `settle_status` when 1 then 1 else 0 end) as real_total,".
            "sum(case when lastdate<='". addslashes($last_date). "' and settle_status=0 then `amount` else 0 end) as exp_amount,".
            "sum(case when lastdate<='". addslashes($last_date). "' and settle_status=0 then 1 else 0 end) as exp_total  ".
            "from `b_stock_in` where  ". $where_db;
        if($orderby){
            $orderby = str_replace('^', ' ', $orderby);
        }
        else{
            $orderby = 'all_amount desc';
        }

        $sql .= ' group by out_cid order by '. $orderby;
        $sql .= ' limit '. $start_count. ','. $page_num;
        $r_res = $this->app->db->query($sql)->fetchAll();

        $cid_list = [];
        foreach($r_res as $val){
            $cid_list[] = $val['out_cid'];
        }
        if($cid_list){
            //把退货的数据计算在内
            $where_db2 .= ' and in_cid in ('. implode(',',$cid_list). ')';
            $count_sql = "select sum(case `settle_status` when 0 then 1 else 0 end) as all_total,".
                "sum(case `settle_status` when 0 then `amount` else 0 end) as all_amount,".
                "sum(case `settle_status` when 1 then `amount` else 0 end) as real_amount,".
                "sum(case `settle_status` when 1 then 1 else 0 end) as real_total from `b_stock_out` where ". $where_db2;
            $count_res = $this->app->db->query($count_sql)->fetchAll();
            $add_up['all_amount'] -= get_value($count_res[0], 'all_amount', 0);
            $add_up['real_amount'] -= get_value($count_res[0], 'real_amount', 0);
            $add_up['all_total'] += get_value($count_res[0], 'all_total', 0);
            $add_up['real_total'] += get_value($count_res[0], 'real_total', 0);
            $add_up['exp_amount'] -= get_value($count_res[0], 'all_amount', 0);
            $add_up['exp_total'] += get_value($count_res[0], 'all_total', 0);

            $sql2 = "select in_cid,in_cname,sum(case `settle_status` when 0 then 1 else 0 end) as all_total,".
                "sum(case `settle_status` when 0 then `amount` else 0 end) as all_amount,".
                "sum(case `settle_status` when 1 then `amount` else 0 end) as real_amount,".
                "sum(case `settle_status` when 1 then 1 else 0 end) as real_total from `b_stock_out` where  ". $where_db2;
            $sql2 .= ' group by in_cid';
            $r_res2 = $this->app->db->query($sql2)->fetchAll();
            $r_data2 = [];
            foreach($r_res2 as $val){
                $r_data2[$val['in_cid']] = $val;
            }

            foreach($r_res as $key=>$val){
                $temp_data = get_value($r_data2, $val['out_cid'], []);
                $r_res[$key]['all_amount'] -= get_value($temp_data, 'all_amount', 0);
                $r_res[$key]['real_amount'] -= get_value($temp_data, 'real_amount', 0);
                $r_res[$key]['all_total'] += get_value($temp_data, 'all_total', 0);
                $r_res[$key]['real_total'] += get_value($temp_data, 'real_total', 0);
                $r_res[$key]['exp_amount'] -= get_value($temp_data, 'all_amount', 0);
                $r_res[$key]['exp_total'] += get_value($temp_data, 'all_total', 0);
            }
        }

        $result = [];
        foreach($r_res as $val){
            $result[] = [
                'id'=>$val['out_cid'],
                'name'=>$val['out_cname'],
                'all_amount'=>fen2yuan($val['all_amount']),
                'real_amount'=>fen2yuan($val['real_amount']),
                'all_total'=>$val['all_total'],
                'real_total'=>$val['real_total'],
                'exp_amount'=>fen2yuan($val['exp_amount']),
                'exp_total'=>$val['exp_total']
            ];
        }

        return [
            'count'=>$all_count,
            'page_count'=>$all_page,
            'data'=>$result,
            'add_up'=>$add_up
        ];
    }

    //预应付款查询
    public function form_pre_payment($data){
        $orderby = get_value($data, 'orderby');
        $where_db = ' cid='. $data['cid']. ' and out_cid is not null and type in (1,2)';
        //如果传了仓库，要按仓库为检索条件
        if(get_value($data, 'sids')){
            $where_db .= ' and sid in ('. addslashes($data['sids']). ')';
        }
        if(get_value($data, 'scids')){
            $where_db .= ' and out_cid in ('. addslashes($data['scids']). ')';
        }
        if(get_value($data, 'begindate')){
            $where_db .= " and checktime>='". addslashes($data['begindate']). " 00:00:00'";
        }
        if(get_value($data, 'enddate')){
            $where_db .= " and checktime<='". addslashes($data['enddate']). " 23:59:59'";
        }
        $where_db .= ' and status=2';

        $page = get_value($data, 'page', 1);
        $page_num = get_value($data, 'page_num', 200);
        $start_count = ($page - 1) * $page_num;

        $count_sql = "select count(distinct out_cid) as val from `b_stock_in` where ". $where_db;
        $count_res = $this->app->db->query($count_sql)->fetchAll();
        $all_count = $count_res[0]['val'];
        $all_page = intval($all_count/$page_num);
        if($all_count%$page_num!=0){
            $all_page ++;
        }

        $sql = "select out_cid,out_cname,count(*) as debt_total,sum(amount) as debt_amount from `b_stock_in` where  ". $where_db;
        if($orderby){
            $orderby = str_replace('^', ' ', $orderby);
        }
        else{
            $orderby = 'debt_amount desc';
        }

        $sql .= ' group by out_cid order by '. $orderby;
        $sql .= ' limit '. $start_count. ','. $page_num;
        $r_res = $this->app->db->query($sql)->fetchAll();
        $result = [];
        foreach($r_res as $val){
            $result[] = [
                'id'=>$val['out_cid'],
                'name'=>$val['out_cname'],
                'debt_amount'=>fen2yuan($val['debt_amount']),
                'debt_total'=>$val['debt_total']
            ];
        }

        return [
            'count'=>$all_count,
            'page_count'=>$all_page,
            'data'=>$result
        ];
    }

    public function form_stock_in($data){
        $add_up = $this->sum(['amount'], $data);
        $res = $this->read_list($data);
        $res['add_up'] = $add_up;
        if(get_value($data, 'download') == 'excel'){
            $excel_data = [];
            $excel_data[] = ['订单号','入货单号','入货单类型','往来单位','填单人','审核人','采购员','结算人','单据金额','创建时间','单据状态','结算状态'];
            foreach($res['data'] as $val){
                $excel_data[] = [$val['order_id'],$val['id'],number_to_name($val['type'],'stock_in_type'),$val['out_cname'],
                    $val['uname'],$val['cuname'], $val['buname'],$val['runame'],$val['amount'],$val['createtime'],
                    number_to_name($val['status'],'stock_in_status'),number_to_name($val['settle_status'],'stock_in_settle_status')];
            }
            $excel_data[] = ['总计','','','','','','','',$res['add_up']['amount'],'','',''];
            write_excel($excel_data, '入库单汇总('.date('Y-m-d').')');
        }
        return $res;
    }

}

