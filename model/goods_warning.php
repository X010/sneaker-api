<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * goods_supplier
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     model
 */

class GoodsWarning extends Object{
    /**
     * 入库所需字段（必须）
     */
    protected $format_data = ['cid','sid','gid','gcode','gname','gbarcode','gunit','gspec','total'];
    
    
    /**
     * constructor
     *
     * @param  int  $id     ID
     */
    public function __construct($id = NULL){
        parent::__construct('o_goods_warning', $id);
    }

    public function my_create($data){
        $cid = $data['cid'];
        $sid = $data['sid'];
        $goods_list = json_decode($data['goods_list'], True);
        $cg_model = new CompanyGoods();

        $gid_data = [];
        if($goods_list){
            $gid_list = [];
            foreach($goods_list as $val){
                $gid_list[] = $val['gid'];
            }

            $cg_res = $cg_model->read_list_nopage([
                'in_cid'=>$cid,
                'gid'=>$gid_list
            ]);

            foreach($cg_res as $val){
                $gid_data[$val['gid']] = $val;
            }
        }

        start_action();
        $this->delete([
            'AND'=>[
                'cid'=>$cid,
                'sid'=>$sid
            ]
        ]);
        if($goods_list){
            $param = [];
            foreach($goods_list as $val){
                $goods_temp = get_value($gid_data, $val['gid']);
                if(!$goods_temp){
                    $cg_model->my_error(3008, $val['gid'], $cid);
                }
                $param[] = [
                    'cid'=>$cid,
                    'sid'=>$sid,
                    'gid'=>$val['gid'],
                    'gname'=>$goods_temp['gname'],
                    'gcode'=>$goods_temp['gcode'],
                    'gbarcode'=>$goods_temp['gbarcode'],
                    'gunit'=>$goods_temp['gunit'],
                    'gspec'=>$goods_temp['gspec'],
                    'total'=>$val['total']
                ];
            }
            $this->create_batch($param);
        }
        return True;
    }

}

