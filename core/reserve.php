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
class Reserve extends Object{

    /**
    * 入库所需字段（必须），如果加星号，代表可以插入但是不可以修改
    */
    protected $format_data = ['cid','sid','gid','count','freeze_count','batch','order_id','from','unit_price',
        'amount_price','expdate','prodate','scid','scname'];

    /**
     * 检索可选字段
     */
    protected $query_data = ['id', 'gid', 'sid', 'cid','expdate[<=]','total[>]'];


    //需要分和元转换的金额字段
    protected $amount_data = ['unit_price','amount_price'];

    //可排序的字段
    protected $order_data = ['id','expdate','batch'];

    /**
     * constructor
     *
     * @param  int  $id     ID
     */
    public function __construct($id = NULL){
        parent::__construct('r_reserve', $id);
    }

    /**
     * 增加库存
     *  @param  array $goods_list 商品清单
     *  @param  int $store_id 仓库id
     *  @param  bigint $order_id 订单号
     *  @param  int $from 来源类型 1-进货 2-退货 3-调拨 4-报溢 5-盘盈 9-冲正 10-退货冲正
     */
    public function in($goods_list, $store_id, $order_id, $from, $ccid=Null, $ccname=Null){
        $app = \Slim\Slim::getInstance();
        $s_model = new Store();
        $res = $s_model->read_by_id($store_id);
        $company_id = $res[0]['cid'];
        $is_reserve = $s_model -> is_reserve($store_id);
        //如果不开启库存管理，直接退出
        if(!$is_reserve){
            return True;
        }
        $fss_model = new FSellSupplier();
        $fsc_model = new FSellCustomer();
        $p_model = new Price();
        $form_total = [];
        $form_amount = [];
        $form_gid = [];
        foreach($goods_list as $key=>$goods){
            if(!get_value($goods, 'sn')){
                $goods_list[$key]['sn']= $key+1;
            }
        }
        switch($from){
            case 1:
                //进货
                //找到一个最新的批次号
                foreach($goods_list as $goods){

                    $where = [
                        'AND' => [
                            'gid' => $goods['gid'],
                            'sid' => $store_id,
                        ],
                        'ORDER' => 'batch DESC',
                        'LIMIT' => [0,1],
                    ];
                    $temp_res = $this->read('*', $where);
                    if($temp_res){
                        $batch = intval($temp_res[0]['batch']) + 1;
                    }
                    else{
                        $batch = 1;
                    }
                    $unit_price = yuan2fen($goods['unit_price']);
                    $my_data = [
                        'cid' => $company_id,
                        'sid' => $store_id,
                        'gid' => $goods['gid'],
                        'total' => $goods['total'],
                        'freeze_total' => 0,
                        'batch' => $batch,
                        'order_id' => $order_id,
                        'from' => $from,
                        'unit_price' => $unit_price,
                        'amount_price' => $unit_price*$goods['total'],
                        'expdate' => get_value($goods, 'expdate'),
                        'prodate' => get_value($goods, 'prodate'),
                        'scid' => get_value($goods, 'scid'),
                        'scname' => get_value($goods, 'scname')
                    ];
                    $rid = $app->db->insert($this->tablename, $my_data);
                    if(!$rid) error(9900);

                    //开始准备入库日报字段
                    $sn = $goods['sn'];
                    if(!isset($form_total[$sn])){
                        $form_total[$sn] = 0;
                    }
                    if(!isset($form_amount[$sn])){
                        $form_amount[$sn] = 0;
                    }
                    $form_total[$sn] += $goods['total'];
                    $form_amount[$sn] += $unit_price*$goods['total'];
                    $form_gid[$sn] = $goods['gid'];
                }
            break;
            case 2:
            case 3:
            case 4:
            case 5:
            case 9:
            case 10:
                //自动插入最早的一个批次
                $result = [];
                foreach($goods_list as $goods){
                    $gid = $goods['gid'];

                    $sell_unit_price = yuan2fen($goods['unit_price']);

                    $where = [
                        'AND' => [
                            'gid' => $goods['gid'],
                            'sid' => $store_id,
                            'total[>]' => 0
                        ],
                        'ORDER' => 'batch ASC',
                        'LIMIT' => [0,1],
                    ];
                    $temp_res = $this->read('*', $where);
                    if(!$temp_res){
                        //如果没找到大于0的记录，那就找批次最大的一条记录
                        $where = [
                            'AND' => [
                                'gid' => $goods['gid'],
                                'sid' => $store_id,
                            ],
                            'ORDER' => 'batch DESC',
                            'LIMIT' => [0,1],
                        ];
                        $temp_res = $this->read('*', $where);
                    }
                    if($temp_res){
                        //如果有记录，直接往里面加
                        $unit_price = yuan2fen($temp_res[0]['unit_price']);
                        $result[$goods['gid']] = fen2yuan($goods['total']*$unit_price);
                        $rid = $app->db->update($this->tablename, [
                            'total[+]'=>$goods['total'],
                            'amount_price[+]'=>$goods['total']*$unit_price
                        ], ['id'=>$temp_res[0]['id']]);
                        if(!$rid) error(9900);

                        $scid = get_value($temp_res[0], 'scid', -1);

                    }
                    else{
                        //如果没记录，新增
                        $batch = 1;
                        $unit_price = yuan2fen($goods['unit_price']);
                        if(!$unit_price){
                            //如果金额为0，则取默认进货价
                            $unit_price = $p_model->get_price($goods['gid'], $company_id, $store_id, 'in_price');
                            $unit_price = yuan2fen($unit_price);
                        }
                        $result[$goods['gid']] = fen2yuan($unit_price*$goods['total']);
                        $my_data = [
                            'cid' => $company_id,
                            'sid' => $store_id,
                            'gid' => $goods['gid'],
                            'total' => $goods['total'],
                            'freeze_total' => 0,
                            'batch' => $batch,
                            'order_id' => $order_id,
                            'from' => $from,
                            'unit_price' => $unit_price,
                            'amount_price' => $unit_price*$goods['total'],
                            'expdate' => get_value($goods, 'expdate'),
                            'prodate' => get_value($goods, 'prodate'),
                            'scid' => get_value($goods, 'scid'),
                            'scname' => get_value($goods, 'scname')
                        ];
                        $rid = $app->db->insert('r_reserve', $my_data);
                        if(!$rid) error(9900);

                        $scid = get_value($goods, 'scid', -1);

                    }

                    if($from == 2){
                        $form_data['return_total'] = $goods['total'];
                        $form_data['return_amount'] = $goods['total']*$sell_unit_price;
                        $form_data['return_cost_amount'] = $unit_price*$goods['total'];
                        $fss_model->write($company_id, $store_id, $scid, $gid, $form_data);
                        $fsc_model->write($company_id, $store_id, $ccid, $ccname, $gid, $form_data);
                    }
                    elseif($from == 9){
                        $form_data['sell_total'] = 0-$goods['total'];
                        $form_data['sell_amount'] = 0-$goods['total']*$sell_unit_price;
                        $form_data['sell_cost_amount'] = 0-$unit_price*$goods['total'];
                        $fss_model->write($company_id, $store_id, $scid, $gid, $form_data);
                        $fsc_model->write($company_id, $store_id, $ccid, $ccname, $gid, $form_data);
                    }

                    //开始准备入库日报字段
                    $sn = $goods['sn'];
                    if(!isset($form_total[$sn])){
                        $form_total[$sn] = 0;
                    }
                    if(!isset($form_amount[$sn])){
                        $form_amount[$sn] = 0;
                    }
                    $form_total[$sn] += $goods['total'];
                    $form_amount[$sn] += $unit_price*$goods['total'];
                    $form_gid[$sn] = $goods['gid'];
                }
            break;
        }

        //开始写出库、入库、调整日报
        //form_* 成本
        //real_* 单据
        $fsi_model = new FStockIn();
        $fso_model = new FStockOut();
        $fa_model = new FAdjust();

        $real_amount = [];
        foreach($goods_list as $goods){
            $sn = $goods['sn'];
            if(!isset($real_amount[$sn])){
                $real_amount[$sn] = 0;
            }
            $real_amount[$sn] += yuan2fen($goods['unit_price'])*$goods['total'];
        }

        switch($from){
            case 1:
                //第一种情况，正常进货，写入库日报
                foreach($form_total as $key=>$val){
                    $fsi_model->write($company_id, $store_id, $form_gid[$key], [
                        'buy_total'=>$form_total[$key],
                        'buy_amount'=>$form_amount[$key]
                    ]);
                }
                break;
            case 2:
                //情况2，出库退货，写出库日报，按成本价记录，记得全是负数
                foreach($form_total as $key=>$val){
                    $fso_model->write($company_id, $store_id, $form_gid[$key], [
                        'return_total'=>0-$form_total[$key],
                        'return_amount'=>0-$form_amount[$key]
                    ]);
                }
                break;
            case 3:
                //情况3，调拨入库，按单据写入库日报，差额记录调整日报
                foreach($form_total as $key=>$val){
                    $fsi_model->write($company_id, $store_id, $form_gid[$key], [
                        'transfer_total'=>$form_total[$key],
                        'transfer_amount'=>$real_amount[$key]
                    ]);

                    $fa_model->write($company_id, $store_id, $form_gid[$key], [
                        'transfer_amount'=>$form_amount[$key]-$real_amount[$key]
                    ]);
                }
                break;
            case 4:
                //情况4，报溢
                foreach($form_total as $key=>$val){
                    $fa_model->write($company_id, $store_id, $form_gid[$key], [
                        'overloss_total'=>$form_total[$key],
                        'overloss_amount'=>$form_amount[$key]
                    ]);
                }
                break;
            case 5:
                //情况5，盘点
                foreach($form_total as $key=>$val){
                    $fa_model->write($company_id, $store_id, $form_gid[$key], [
                        'inventory_total'=>$form_total[$key],
                        'inventory_amount'=>$form_amount[$key]
                    ]);
                }
                break;
            case 9:
                //情况9，出库冲正，记录出库日报负单（单据价），差额记录调整日报
                foreach($form_total as $key=>$val){
                    $fso_model->write($company_id, $store_id, $form_gid[$key], [
                        'sell_total'=>0-$form_total[$key],
                        'sell_amount'=>0-$real_amount[$key]
                    ]);

                    $fa_model->write($company_id, $store_id, $form_gid[$key], [
                        'flush_amount'=>$form_amount[$key]-$real_amount[$key]
                    ]);
                }
                break;
            case 10:
                //情况10，入库退货冲正，冲退货记录，记调价额
                foreach($form_total as $key=>$val){
                    $fsi_model->write($company_id, $store_id, $form_gid[$key], [
                        'return_total'=>0-$form_total[$key],
                        'return_amount'=>0-$real_amount[$key]
                    ]);

                    $fa_model->write($company_id, $store_id, $form_gid[$key], [
                        'return_amount'=>$real_amount[$key]-$form_amount[$key]
                    ]);
                }
                break;
        }
        return $form_amount;
    }

    /**
     * 减少库存
     * @param  array $goods_list 商品清单
     * @param  int $store_id 仓库id
     * @param  int $from 1-销售 2-退货 3-调拨 4-报损 5-盘亏 9-冲正 10-退货冲正
     */
    public function out($goods_list, $store_id, $from, $ccid=Null, $ccname=Null){
        $app = \Slim\Slim::getInstance();

        $s_model = new Store();
        $fss_model = new FSellSupplier();
        $fsc_model = new FSellCustomer();
        $res = $s_model->read_by_id($store_id);
        $company_id = $res[0]['cid'];
        $is_reserve = $s_model -> is_reserve($store_id);
        //如果不开启库存管理，直接退出
        if(!$is_reserve){
            return True;
        }
        $cg_model = new CompanyGoods();

        $form_total = [];
        $form_amount = [];
        $form_gid = [];

        foreach($goods_list as $key=>$goods){
            if(!get_value($goods, 'sn')){
                $goods_list[$key]['sn']= $key+1;
            }
        }
        foreach($goods_list as $goods){
            $gid = $goods['gid'];
            //销售单价
            $sell_unit_price = yuan2fen($goods['unit_price']);

            //首先判断是否指定批次
            if(get_value($goods, 'reserveid')){
                //指定批次退出的情况
                $tres = $app->db->select($this->tablename, '*', [
                    'AND'=>[
                        'id'=>$goods['reserveid'],
                        'sid'=>$store_id
                    ]
                ]);
                if(!$tres){
                    $cg_model->my_error(3125, $goods['gid'], $company_id);
                }
                $unit_price = $tres[0]['unit_price'];

                //如果当前库存已经够本次出库
                $total = $goods['total'];
                if($tres[0]['total'] >= $total){
                    //只扣部分本批次库存
                    $my_data = [
                        'total[-]' => $total,
                        'amount_price[-]'=>$total*$unit_price
                    ];
                    $rid = $app->db->update($this->tablename, $my_data, ['id'=>$goods['reserveid']]);
                    if(!$rid) error(9900);

                    //开始准备入库日报字段
                    $sn = $goods['sn'];
                    if(!isset($form_total[$sn])){
                        $form_total[$sn] = 0;
                    }
                    if(!isset($form_amount[$sn])){
                        $form_amount[$sn] = 0;
                    }
                    $form_total[$sn] += $total;
                    $form_amount[$sn] += $total*$unit_price;
                    $form_gid[$sn] = $goods['gid'];

                }
                else{
                    //如果不够，直接报错
                    $cg_model->my_error(3125, $goods['gid'], $company_id);
                }
            }
            else{
                //从最早的批次开始扣，一批一批的扣，知道扣满total为止
                //当前商品盈亏值初始化
                $amount = 0;
                //首先计算库存量是不是足够
                $where = [
                    'AND' => [
                        'gid' => $goods['gid'],
                        'sid' => $store_id,
                    ]
                ];
                $gcount = $app->db->sum($this->tablename, 'total', $where);
                if($gcount<$goods['total']){
                    $cg_model->my_error(3124, $goods['gid'], $company_id);
                }
                $total = $goods['total'];

                //先把所有库存数据读取出来，按照批次从小到大
                $where = [
                    'AND' => [
                        'gid' => $goods['gid'],
                        'sid' => $store_id,
                        'total[>]' => 0
                    ],
                    'ORDER' => 'batch ASC',
                ];
                $reverse_res = $this->read('*', $where);
                $i = 0;

                $sn = $goods['sn'];
                if(!isset($form_total[$sn])){
                    $form_total[$sn] = 0;
                }
                if(!isset($form_amount[$sn])){
                    $form_amount[$sn] = 0;
                }
                $form_gid[$sn] = $goods['gid'];

                do{
                    $tres = $app->db->select($this->tablename, '*', ['id'=>$reverse_res[$i]['id']]);
                    $unit_price = $tres[0]['unit_price'];

                    //供应商销售日报
                    $scid = get_value($tres[0], 'scid', -1);

                    //如果当前库存已经够本次出库
                    if($reverse_res[$i]['total'] >= $total){
                        //只扣部分本批次库存
                        $cost_total = $total;
                        $cost_amount = $total*$unit_price;
                        $my_data = [
                            'total[-]' => $cost_total,
                            'amount_price[-]' => $cost_amount
                        ];
                        $rid = $app->db->update($this->tablename, $my_data, ['id'=>$reverse_res[$i]['id']]);
                        if(!$rid) error(9900);

                        $form_total[$sn] += $cost_total;
                        $form_amount[$sn] += $cost_amount;

                        //供应商销售日报
                        if($from == 1){
                            $form_data['sell_total'] = $cost_total;
                            $form_data['sell_amount'] = $cost_total*$sell_unit_price;
                            $form_data['sell_cost_amount'] = $cost_amount;
                            $fss_model->write($company_id, $store_id, $scid, $gid, $form_data);
                            $fsc_model->write($company_id, $store_id, $ccid, $ccname, $gid, $form_data);
                        }
                        elseif($from == 10){
                            $form_data['return_total'] = 0-$cost_total;
                            $form_data['return_amount'] = 0-$cost_total*$sell_unit_price;
                            $form_data['return_cost_amount'] = 0-$cost_amount;
                            $fss_model->write($company_id, $store_id, $scid, $gid, $form_data);
                            $fsc_model->write($company_id, $store_id, $ccid, $ccname, $gid, $form_data);
                        }
                        break;
                    }
                    else{
                        //如果不够
                        $total = $total-$reverse_res[$i]['total'];

                        $cost_total = $reverse_res[$i]['total'];
                        $cost_amount = $cost_total*$unit_price;

                        //扣完本批次库存
                        $my_data = [
                            'total[-]' => $cost_total,
                            'amount_price[-]'=>$cost_amount
                        ];
                        $rid = $app->db->update($this->tablename, $my_data, ['id'=>$reverse_res[$i]['id']]);
                        if(!$rid) error(9900);

                        $form_total[$sn] += $cost_total;
                        $form_amount[$sn] += $cost_amount;

                        //供应商销售日报
                        if($from == 1){
                            $form_data['sell_total'] = $cost_total;
                            $form_data['sell_amount'] = $cost_total*$sell_unit_price;
                            $form_data['sell_cost_amount'] = $cost_amount;
                            $fss_model->write($company_id, $store_id, $scid, $gid, $form_data);
                            $fsc_model->write($company_id, $store_id, $ccid, $ccname, $gid, $form_data);
                        }
                        elseif($from == 10){
                            $form_data['return_total'] = 0-$cost_total;
                            $form_data['return_amount'] = 0-$cost_total*$sell_unit_price;
                            $form_data['return_cost_amount'] = 0-$cost_amount;
                            $fss_model->write($company_id, $store_id, $scid, $gid, $form_data);
                            $fsc_model->write($company_id, $store_id, $ccid, $ccname, $gid, $form_data);
                        }
                    }
                    $i++;
                }while($total>=0);
            }
        }

        //开始写出库、入库、调整日报
        //form_* 成本
        //real_* 单据
        $fsi_model = new FStockIn();
        $fso_model = new FStockOut();
        $fa_model = new FAdjust();

        $real_amount = [];
        foreach($goods_list as $goods){
            $sn = $goods['sn'];
            if(!isset($real_amount[$sn])){
                $real_amount[$sn] = 0;
            }
            $real_amount[$sn] += yuan2fen($goods['unit_price'])*$goods['total'];
        }

        switch($from){
            case 1:
                //第一种情况，正常销售，写出库日报，记录成本金额
                foreach($form_total as $key=>$val){
                    $fso_model->write($company_id, $store_id, $form_gid[$key], [
                        'sell_total'=>$form_total[$key],
                        'sell_amount'=>$form_amount[$key]
                    ]);
                }
                break;
            case 2:
                //情况2，入库退货，写入库日报，按单据记录，记得全是负数，差额记录调整日报
                foreach($form_total as $key=>$val){
                    $fsi_model->write($company_id, $store_id, $form_gid[$key], [
                        'return_total'=>0-$form_total[$key],
                        'return_amount'=>0-$real_amount[$key]
                    ]);

                    $fa_model->write($company_id, $store_id, $form_gid[$key], [
                        'return_amount'=>$real_amount[$key]-$form_amount[$key]
                    ]);
                }
                break;
            case 3:
                //情况3，调拨出库，按单据写出库日报
                foreach($form_total as $key=>$val){
                    $fso_model->write($company_id, $store_id, $form_gid[$key], [
                        'transfer_total'=>$form_total[$key],
                        'transfer_amount'=>$form_amount[$key]
                    ]);
                }
                break;
            case 4:
                //情况4，报损，调整日报记负数
                foreach($form_total as $key=>$val){
                    $fa_model->write($company_id, $store_id, $form_gid[$key], [
                        'overloss_total'=>0-$form_total[$key],
                        'overloss_amount'=>0-$form_amount[$key]
                    ]);
                }
                break;
            case 5:
                //情况5，盘点，调整日报记负数
                foreach($form_total as $key=>$val){
                    $fa_model->write($company_id, $store_id, $form_gid[$key], [
                        'inventory_total'=>0-$form_total[$key],
                        'inventory_amount'=>0-$form_amount[$key]
                    ]);
                }
                break;
            case 9:
                //情况9，入库冲正，记录入库日报负单（单据价），差额记录调整日报
                foreach($form_total as $key=>$val){
                    $fsi_model->write($company_id, $store_id, $form_gid[$key], [
                        'buy_total'=>0-$form_total[$key],
                        'buy_amount'=>0-$real_amount[$key]
                    ]);

                    $fa_model->write($company_id, $store_id, $form_gid[$key], [
                        'flush_amount'=>$real_amount[$key]-$form_amount[$key]
                    ]);
                }
                break;
            case 9:
                //情况9，出库退货冲正，记录出库日报负单（单据价），差额记录调整日报
                foreach($form_total as $key=>$val){
                    $fso_model->write($company_id, $store_id, $form_gid[$key], [
                        'return_total'=>0-$form_total[$key],
                        'return_amount'=>0-$real_amount[$key]
                    ]);

                    $fa_model->write($company_id, $store_id, $form_gid[$key], [
                        'return_amount'=>$form_amount[$key]-$real_amount[$key]
                    ]);
                }
                break;
        }

        return $form_amount;
    }

    //出库前检测库存够不够 返回True－够 False－不够
    public function check_out($goods_list, $store_id){
        $app = \Slim\Slim::getInstance();
        $s_model = new Store();
        $is_reserve = $s_model -> is_reserve($store_id);
        //如果不开启库存管理，直接退出
        if(!$is_reserve){
            return True;
        }

        foreach($goods_list as $key=>$goods){
            if(!get_value($goods, 'sn')){
                $goods_list[$key]['sn']= $key+1;
            }
        }
        foreach($goods_list as $goods){
            //首先判断是否指定批次
            if(get_value($goods, 'reserveid')){
                //指定批次退出的情况
                $tres = $app->db->select($this->tablename, '*', [
                    'AND'=>[
                        'id'=>$goods['reserveid'],
                        'sid'=>$store_id
                    ]
                ]);
                if(!$tres){
                    return False;
                }
                $total = $goods['total'];
                if($tres[0]['total'] < $total){
                    return False;
                }
            }
            else{
                //首先计算库存量是不是足够
                $where = [
                    'AND' => [
                        'gid' => $goods['gid'],
                        'sid' => $store_id,
                    ]
                ];
                $gcount = $app->db->sum($this->tablename, 'total', $where);
                if($gcount<$goods['total']){
                    return False;
                }
            }
        }
        return True;
    }


    /**
     *  获取库存里的成本单价金额
     *
     *  @param  array   商品清单
     *  @param  int     仓库id
     */
    public function get_unit_price($goods_list, $store_id){
        $gids = [];
        foreach($goods_list as $goods){
            $gids[] = $goods['gid'];
        }
        $gids = implode(',', $gids);

        $sql = "select gid,sum(total) as val,sum(amount_price) as val2 from `r_reserve` where ";
        $sql .= ' sid='. intval($store_id);
        $sql .= ' and gid in ('. $gids. ')';

        $res = $this->app->db->query($sql)->fetchAll();
        $result = [];
        foreach($res as $key=>$val){
            if($val['val']){
                $temp = intval($val['val2']/$val['val']);
                $price = fen2yuan($temp);
                $result[$val['gid']] = $price;
            }
            else{
                $where = [
                    'AND' => [
                        'gid' => $val['gid'],
                        'sid' => $store_id,
                    ],
                    'ORDER' => 'batch DESC',
                    'LIMIT' => [0,1],
                ];
                $temp_res = $this->read('*', $where);
                if($temp_res){
                    $price = $temp_res[0]['unit_price'];
                    $result[$val['gid']] = $price;
                }
            }
        }
        return $result;
    }

    /**
     * 冻结库存
     * -------------已废弃----------------------
     *  @param  array   商品清单
     *  @param  int     仓库id
     */

    public function freeze($goods_list, $store_id){
        $app = \Slim\Slim::getInstance();

        //冻结库存
        foreach($goods_list as $goods){
            //从最早的批次开始扣，一批一批的扣，知道扣满total为止

            //首先计算库存量是不是足够
            $where = [
                'AND' => [
                    'gid' => $goods['gid'],
                    'sid' => $store_id,
                ]
            ];
            $gcount = $app->db->sum($this->tablename, 'total', $where);
            if($gcount<$goods['total']){
                error(3124);
            }
            $total = $goods['total'];

            //先把所有库存数据读取出来，按照批次从小到大
            $where = [
                'AND' => [
                    'gid' => $goods['gid'],
                    'sid' => $store_id,
                    'total[>]' => 0
                ],
                'ORDER' => 'batch ASC',
            ];
            $reverse_res = $this->read('*', $where);
            $i = 0;
            do{
                //开始冻结库存，从最小批次开始

                //如果当前库存已经够本次出库冻结
                if($reverse_res[$i]['total'] >= $total){
                    //只扣部分本批次库存
                    $my_data = [
                        'total[-]' => $total,
                        'freeze_total[+]' => $total
                    ];
                    $rid = $app->db->update('r_reserve', $my_data, ['id'=>$reverse_res[$i]['id']]);
                    if(!$rid) error(9900);
                    break;
                }
                else{
                    //如果不够
                    $total = $total-$reverse_res[$i]['total'];
                    //扣完本批次库存

                    $my_data = [
                        'total[-]' => $reverse_res[$i]['total'],
                        'freeze_total[+]' => $reverse_res[$i]['total']
                    ];
                    $rid = $app->db->update('r_reserve', $my_data, ['id'=>$reverse_res[$i]['id']]);
                    if(!$rid) error(9900);
                }
                $i++;
            }while($total>=0);
        }
        return true;
    }

    /**
     * 解冻库存
     * -------------已废弃----------------------
     *  @param  array   商品清单
     *  @param  int     仓库id
     */
    public function unfreeze($goods_list, $store_id){
        $app = \Slim\Slim::getInstance();

        //扣除冻结库存
        foreach($goods_list as $goods){
            //找到被冻结的批次，进行解冻，优先最早的批次

            //首先计算冻结量是不是足够
            $where = [
                'AND' => [
                    'gid' => $goods['gid'],
                    'sid' => $store_id,
                ]
            ];
            $gcount = $app->db->sum($this->tablename, 'freeze_total', $where);
            if($gcount<$goods['total']){
                error(3124);
            }
            $total = $goods['total'];

            //先把所有冻结库存数据读取出来，按照批次从小到大减少
            $where = [
                'AND' => [
                    'gid' => $goods['gid'],
                    'sid' => $store_id,
                    'freeze_total[>]' => 0
                ],
                'ORDER' => 'batch ASC',
            ];
            $reverse_res = $this->read('*', $where);
            $i = 0;
            do{
                //开始解冻，从最小批次开始

                //如果当前库存已经够本次解冻
                if($reverse_res[$i]['freeze_total'] >= $total){
                    //只减少部分本批次库存
                    $my_data = [
                        'freeze_total[-]' => $total,
                        'total[+]' => $total,
                    ];
                    $rid = $app->db->update('r_reserve', $my_data, ['id'=>$reverse_res[$i]['id']]);
                    if(!$rid) error(9900);
                    break;
                }
                else{
                    //如果不够
                    $total = $total-$reverse_res[$i]['freeze_total'];
                    //完全解冻本批次库存

                    $my_data = [
                        'freeze_total[-]' => $reverse_res[$i]['freeze_total'],
                        'total[+]' => $reverse_res[$i]['freeze_total'],
                    ];
                    $rid = $app->db->update('r_reserve', $my_data, ['id'=>$reverse_res[$i]['id']]);
                    if(!$rid) error(9900);
                }
                $i++;
            }while($total>=0);
        }
        return true;
    }

    /**
     * 减少冻结库存
     * -------------已废弃----------------------
     *  @param  array   商品清单
     *  @param  int     仓库id
     */
    public function outfreeze($goods_list, $store_id){
        $app = \Slim\Slim::getInstance();

        //扣除冻结库存
        foreach($goods_list as $goods){
            //找到被冻结的批次，直接减掉，优先最早的批次

            //首先计算冻结量是不是足够
            $where = [
                'AND' => [
                    'gid' => $goods['gid'],
                    'sid' => $store_id,
                ]
            ];
            $gcount = $app->db->sum($this->tablename, 'freeze_total', $where);
            if($gcount<$goods['total']){
                error(3124);
            }
            $total = $goods['total'];

            //先把所有冻结库存数据读取出来，按照批次从小到大减少
            $where = [
                'AND' => [
                    'gid' => $goods['gid'],
                    'sid' => $store_id,
                    'freeze_total[>]' => 0
                ],
                'ORDER' => 'batch ASC',
            ];
            $reverse_res = $this->read('*', $where);
            
            $i = 0;
            do{
                //开始减少冻结库存，从最小批次开始

                //如果当前库存已经够本次出库冻结
                if($reverse_res[$i]['freeze_total'] >= $total){
                    //只减少部分本批次库存
                    $my_data = [
                        'freeze_total[-]' => $total
                    ];
                    $rid = $app->db->update('r_reserve', $my_data, ['id'=>$reverse_res[$i]['id']]);
                    if(!$rid) error(9900);
                    break;
                }
                else{
                    //如果不够
                    $total = $total-$reverse_res[$i]['freeze_total'];
                    //扣完本批次库存

                    $my_data = [
                        'freeze_total[-]' => $reverse_res[$i]['freeze_total']
                    ];
                    $rid = $app->db->update('r_reserve', $my_data, ['id'=>$reverse_res[$i]['id']]);
                    if(!$rid) error(9900);
                }
                $i++;
            }while($total>=0);
        }
        return true;
    }

    /**
     * 按照商品分组显示库存
     * @param $data
     * @return array
     */
    public function read_goods($data){
        $page = get_value($data, 'page', 1);
        $page_num = get_value($data, 'page_num', 500);
        $start_count = ($page - 1) * $page_num;
        $cid = $data['cid'];

        $where_db =  " where cid=".$cid. " and sid=". intval($data['sid']);
        $cg_model = new CompanyGoods();

        if(get_value($data, 'search')){
            $cg_model = new CompanyGoods();
            $g_res = $cg_model->read_list_nopage([
                'search' => $data['search'],
                'in_cid' => $cid
            ]);
            $gids = [];
            foreach($g_res as $val){
                $gids[] = $val['gid'];
            }
            if($gids){
                $where_db .= " and gid in (". implode(',', $gids). ")";
            }
            else{
                $where_db .= " and gid is null";
            }
        }

        $sql_all = "select sum(total) as val,sum(amount_price) as val2 from `r_reserve`". $where_db;
        $res_all = $this->app->db->query($sql_all)->fetchAll();
        $add_up = [
            'total'=>$res_all[0]['val'],
            'amount'=>fen2yuan($res_all[0]['val2'])
        ];


        $sql = "select count(distinct gid) as val from `r_reserve`".$where_db;
        $res = $this->app->db->query($sql)->fetchAll();
        $ret_count = $res[0]['val'];

        $sql = "select gid,sum(total) as val,sum(amount_price) as val2 from `r_reserve`". $where_db;
        $sql .= ' group by gid';
        $sql .= ' order by gid desc';
        $sql .= ' limit '. $start_count. ','. $page_num;
        $res = $this->app->db->query($sql)->fetchAll();

        $my_gids = [];
        $my_gids_dict = [];
        foreach($res as $key=>$val){
            $my_gids[] = $val['gid'];
        }
        if($my_gids){
            $cg_res = $cg_model->read_list([
                'gid'=>$my_gids,
                'in_cid'=>$data['cid']
            ]);
            foreach($cg_res['data'] as $val){
                $my_gids_dict[$val['gid']] = $val;
            }
        }

        $result = [];
        foreach($res as $key=>$val){
            $temp = [
                'gid' => $val['gid'],
                'total' => $val['val'],
                'amount' => fen2yuan($val['val2'])
            ];
            if($val['val']){
                $temp['unit_price'] = fen2yuan($val['val2']/$val['val']);
            }
            else{
                $temp['unit_price'] = '0.00';
            }
            if(!isset($my_gids_dict[$val['gid']])){
                $cg_model->my_error(3008, $val['gid'], $cid);
            }
            $gres = $my_gids_dict[$val['gid']];
            if($gres){
                $temp['gname'] = $gres['gname'];
                $temp['gcode'] = $gres['gcode'];
                $temp['gspec'] = $gres['gspec'];
                $temp['gunit'] = $gres['gunit'];
                $temp['gtid'] = $gres['gtid'];
                $temp['gbarcode'] = $gres['gbarcode'];
            }

            $result[$val['gid']] = $temp;
        }

        $result = dict2list($result);

        $ret_page = intval($ret_count/$page_num);
        if($ret_count%$page_num!=0){
            $ret_page ++;
        }
        $ret = [
            'data' => $result,
            'count' => $ret_count,
            'page_count' => $ret_page,
            'add_up' => $add_up
        ];
        return $ret;
    }

    /**
     * 获取商品库存数量
     * @param $cid
     * @param $sid
     * @param $gids
     * @return array
     */
    public function get_reserve($cid, $sid, $gids){
        $s_model = new Store();
        $gids_list = $gids;
        $is_reserve = $s_model -> is_reserve($sid);
        $result = [];
        if($is_reserve && $gids){
            $gids = implode(',', $gids);
            $sql = "select gid,sum(total) as val from `r_reserve` where cid=".$cid;
            $sql .= ' and sid='. intval($sid). ' and gid in ('.$gids. ')';
            $sql .= ' group by gid';
            $sql .= ' order by gid desc';
            $res = $this->app->db->query($sql)->fetchAll();
            foreach($res as $key=>$val){
                $result[$val['gid']] = $val['val'];
            }
            foreach($gids_list as $gid){
                if(!isset($result[$gid])){
                    $result[$gid] = 0;
                }
            }
        }
        else{
            foreach($gids as $gid){
                $result[$gid] = '未开启';
            }
        }
        return $result;
    }

    /**
     * 获取商品库存数量和成本金额
     * @param $cid
     * @param $sid
     * @param $gids
     * @return array
     */
    public function get_reserve_amount($cid, $sid, $gids){
        $s_model = new Store();
        $gids_list = $gids;
        $is_reserve = $s_model -> is_reserve($sid);
        $result = [];
        if($is_reserve && $gids){
            $gids = implode(',', $gids);
            $sql = "select gid,sum(total) as val,sum(amount_price) as val2 from `r_reserve` where cid=".$cid;
            $sql .= ' and sid='. intval($sid). ' and gid in ('.$gids. ')';
            $sql .= ' group by gid';
            $sql .= ' order by gid desc';
            $res = $this->app->db->query($sql)->fetchAll();
            foreach($res as $key=>$val){
                $result[$val['gid']] = [
                    'total'=>$val['val'],
                    'amount'=>fen2yuan($val['val2'])
                ];
            }
            foreach($gids_list as $gid){
                if(!isset($result[$gid])){
                    $result[$gid] = [
                        'total'=>0,
                        'amount'=>'0.00'
                    ];
                }
            }
        }
        else{
            foreach($gids as $gid){
                $result[$gid] = [
                    'total'=>'未开启',
                    'amount'=>'未开启',
                ];
            }
        }
        return $result;
    }

    /**
     * 库存快照，每日凌晨开始备份
     */
    public function snapshot(){

        $fr_model = new FReserve();
        $frd_model = new FReserveDetail();

        $last_date = date('Y-m-d', time()-24*3600);
        $last2_date = date('Y-m-d', time()-48*3600);

        $s_res = $this->app->db->select('o_store','*',[
            'AND'=>[
                'status'=>1,
                'isreserve'=>1
            ]
        ]);
        foreach($s_res as $store){
            //每个仓库一次事务
            start_action();
            $sid = intval($store['id']);

            //先做防重，一个仓库一天只能有一条总记录
            $has_res = $fr_model->has([
                'date'=>$last_date,
                'sid'=>$sid
            ]);
            //如果已存在，可能今天该仓库已经跑过脚本了，不可重复跑
            if($has_res){
                continue;
            }

            //读取昨天的记录
            $sql = "select * from `f_reserve` where sid=$sid and `date`='$last2_date'";
            $f_res = $this->app->db->query($sql)->fetchAll();
            if($f_res){
                $last_total = $f_res[0]['total_end'];
                $last_amount = fen2yuan($f_res[0]['amount_end']);
            }
            else{
                $last_total = 0;
                $last_amount = 0;
            }

            //读取昨天的库存明细
            $sql = "select * from `f_reserve_detail` where sid=$sid and `date`='$last2_date'";
            $f_res = $this->app->db->query($sql)->fetchAll();
            $last_glist = [];
            foreach($f_res as $val){
                $last_glist[$val['gid']] = $val;
            }

            $temp_data = [];
            $sql = "select gid,sum(total) as val,sum(amount_price) as val2 from `r_reserve` where  sid=". intval($store['id']);
            $sql .= ' group by gid';
            $r_res = $this->app->db->query($sql)->fetchAll();
            if(!$r_res){
                continue;
            }
            $amount = 0;
            $total = 0;
            foreach($r_res as $val){
                $total += $val['val'];
                $amount += $val['val2'];

                $my_amount = fen2yuan($val['val2']);
                $last_temp = get_value($last_glist, $val['gid'], []);

                //新建昨天数据
                $temp_data[] = [
                    'cid'=>$store['cid'],
                    'sid'=>$store['id'],
                    'gid'=>$val['gid'],
                    'date'=>$last_date,
                    'amount_begin'=>fen2yuan(get_value($last_temp, 'amount_end', 0)),
                    'amount_end'=>$my_amount,
                    'total_begin'=>get_value($last_temp, 'total_end', 0),
                    'total_end'=>$val['val']
                ];
            }

            $amount = fen2yuan($amount);
            $gids = [];
            foreach($temp_data as $val){
                $gids[] = $val['gid'];
            }
            $g_data = [];
            if($gids){
                $g_res = $this->app->db->select('o_company_goods','*',[
                    'AND'=>[
                        'gid'=>$gids,
                        'in_cid'=>$store['cid']
                    ]
                ]);
                foreach($g_res as $val){
                    $g_data[$val['gid']] = $val;
                }
                foreach($temp_data as $key=>$val){
                    $g_temp = get_value($g_data, $val['gid'], []);
                    $temp_data[$key]['gname'] = get_value($g_temp, 'gname');
                    $temp_data[$key]['gcode'] = get_value($g_temp, 'gcode');
                    $temp_data[$key]['gbarcode'] = get_value($g_temp, 'gbarcode');
                    $temp_data[$key]['gspec'] = get_value($g_temp, 'gspec');
                    $temp_data[$key]['gunit'] = get_value($g_temp, 'gunit');
                    $temp_data[$key]['gtid'] = get_value($g_temp, 'gtid');
                }
                $temp_data = Change::go($temp_data, 'gtid', 'gtname', 'o_company_goods_type');
            }

            $frd_model->create_batch($temp_data);
            $fr_model->create([
                'cid'=>$store['cid'],
                'sid'=>$store['id'],
                'date'=>$last_date,
                'amount_begin'=>$last_amount,
                'amount_end'=>$amount,
                'total_begin'=>$last_total,
                'total_end'=>$total
            ]);

            end_action();
        }

        return True;
    }

    //按照一定的算法读取价格
    public function read_old_price($gid, $sid, $cid){
        //默认最早一个不为0的批次
        $my_res = $this->read_one([
            'total[>]'=>'0',
            'sid'=>$sid,
            'gid'=>$gid,
            'orderby'=>'batch^ASC',
        ]);
        $price = '0.00';
        if($my_res){
            $price = $my_res['unit_price'];
        }
        else{
            //然后是最晚的一个为0的批次
            $my_res = $this->read_one([
                'sid'=>$sid,
                'gid'=>$gid,
                'orderby'=>'batch^DESC',
            ]);
            if($my_res){
                $price = $my_res['unit_price'];
            }
            else{
                //取商品的进货价
                $p_model = new Price();
                $price = $p_model->get_price($gid, $cid, $sid, 'in_price');
            }
        }
        return $price;
    }

    public function my_read_list($data){
        $add_up = $this->sum(['total'], $data);
        $res = $this->read_list($data);

        $gid_list = [];
        foreach($res['data'] as $val){
            $gid_list[] = $val['gid'];
        }

        if($gid_list){
            $cg_model = new CompanyGoods();
            $cg_res = $cg_model->read_list_nopage([
                'in_cid'=>$data['cid'],
                'gid'=>$gid_list
            ]);

            $g_data = [];
            foreach($cg_res as $val){
                $g_data[$val['gid']] = $val;
            }

            foreach($res['data'] as $key=>$val){
                $g_temp = get_value($g_data, $val['gid'], []);
                $res['data'][$key]['gname'] = get_value($g_temp, 'gname');
                $res['data'][$key]['gcode'] = get_value($g_temp, 'gcode');
                $res['data'][$key]['gbarcode'] = get_value($g_temp, 'gbarcode');
                $res['data'][$key]['gunit'] = get_value($g_temp, 'gunit');
                $res['data'][$key]['gspec'] = get_value($g_temp, 'gspec');
            }
        }
        $res['add_up'] = $add_up;

        return $res;
    }

    
}

