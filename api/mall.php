<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * mall 通知接口（内部API）
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} mall/price_read 查询仓库商品价格
 * @apiName mall/price_read
 * @apiGroup Mall
 * @apiVersion 0.0.1
 * @apiDescription 查询仓库商品价格
 *
 * @apiParam {int} cid 公司ID
 * @apiParam {json} goods_list 商品清单详情
 * @apiParam {json} - 商品清单详情字段
 * @apiParam {int} gcode 商品编码
 * @apiParam {int} scid 供应商ID
 *
 * @apiSuccess {array} price 冻结数量
 * @apiSuccess {array} - *price字段详情
 * @apiSuccess {key} gcode 商品编码
 * @apiSuccess {value} price 商品价格    -1:未添加成客户 -2:供应商无此商品
 *
 */

/**
 * @api {post} mall/price_read_single 查询仓库商品价格-单个供应商
 * @apiName mall/price_read_single
 * @apiGroup Mall
 * @apiVersion 0.0.1
 * @apiDescription 查询仓库商品价格-单个供应商
 *
 * @apiParam {int} cid 公司ID
 * @apiParam {int} scid 供应商ID
 * @apiParam {json} goods_list 商品清单详情
 * @apiParam {json} - 商品清单详情字段
 * @apiParam {int} gcode 商品编码
 *
 * @apiSuccess {array} price 冻结数量
 * @apiSuccess {array} - *price字段详情
 * @apiSuccess {key} gcode 商品编码
 * @apiSuccess {value} price 商品价格    -1:未添加成客户 -2:供应商无此商品
 *
 */

/**
 * @api {post} mall/order_create 创建订单
 * @apiName mall/order_create
 * @apiGroup Mall
 * @apiVersion 0.0.1
 * @apiDescription 创建订单
 *
 * @apiParam {json} data 参数
 * @apiParam {json} - data参数详情详情
 * @apiParam {int} in_cid 客户公司ID
 * @apiParam {int} out_cid 供应商ID
 * @apiParam {int} rank 紧急程度
 * @apiParam {int} memo 备注
 * @apiParam {int} orderNo 商城订单号
 * @apiParam {int} receipt 商城收货地址
 * @apiParam {int} contacts 商城联系人姓名
 * @apiParam {int} phone 商城联系人电话
 * @apiParam {int} pay_type 付款方式ID
 * @apiParam {int} ispaid 是否已经付款 0-未付款 1-已付款
 * @apiParam {int} favorable 优惠金额
 * @apiParam {int} goods_list 商品清单
 * @apiParam {int} - goods_list字段详情
 * @apiParam {int} gid 商品ID
 * @apiParam {int} total 数目
 * @apiParam {int} amount_price 行总价
 *
 * @apiSuccess {string} orderNo 商城订单号
 * @apiSuccess {int} orderId ERP订单号
 *
 */

/**
 * @api {post} mall/order_cancel/:id 取消订单
 * @apiName mall/order_cancel/:id
 * @apiGroup Mall
 * @apiVersion 0.0.1
 * @apiDescription 取消订单,id为ERP订单号
 *
 */

/**
 * @api {post} mall/login 商城登陆
 * @apiName mall/login
 * @apiGroup Mall
 * @apiVersion 0.0.1
 * @apiDescription 创建订单
 *
 * @apiParam {string} username 用户名
 * @apiParam {string} password 密码
 *
 * @apiSuccess {int} id 员工ID
 * @apiSuccess {string} code 员工code
 * @apiSuccess {string} name 员工姓名
 * @apiSuccess {string} username 员工登陆账号
 * @apiSuccess {string} worktype 工种
 * @apiSuccess {int} cid 所属公司ID
 * @apiSuccess {string} cname 所属公司名称
 * @apiSuccess {string} areapro 省
 * @apiSuccess {string} areacity 市
 * @apiSuccess {string} areazone 区
 * @apiSuccess {array} sids 拥有仓库权限列表，是一个list类型
 * @apiSuccess {array} rids 拥有角色权限列表，是一个list类型
 * @apiSuccess {array} power 拥有API权限列表，是一个list类型
 * @apiSuccess {string} logintime 本次登陆的时间
 * @apiSuccess {int} admin 是否管理员 0-不是 1-是，如果是管理员，则不用判断power自动拥有所有API权限
 * @apiSuccess {string} scids 供应商ID列表，是list类型
 *
 */

/**
 * @api {post} mall/price_read_by_gcode 查询单个商品的所有价格
 * @apiName mall/price_read_by_gcode
 * @apiGroup Mall
 * @apiVersion 0.0.1
 * @apiDescription 查询单个商品的所有价格
 *
 * @apiParam {int} cid 公司ID
 * @apiParam {string} gcode 商品编码
 *
 * @apiSuccess {list} - *list字段详情
 * @apiSuccess {int} sid 仓库ID    sid为－1代表公司价格
 * @apiSuccess {string} sname 仓库名称
 * @apiSuccess {string} out_price1 出货价1
 * @apiSuccess {string} out_price2 出货价2
 * @apiSuccess {string} out_price3 出货价3
 * @apiSuccess {string} out_price4 出货价4
 *
 */

/**
 * @api {post} mall/price_read_by_company 查询指定商品的公司价格
 * @apiName mall/price_read_by_company
 * @apiGroup Mall
 * @apiVersion 0.0.1
 * @apiDescription 查询指定商品的公司价格
 *
 * @apiParam {int} cid 公司ID
 * @apiParam {json} goods_list 商品清单详情
 * @apiParam {json} - 商品清单详情字段
 * @apiParam {int} gcode 商品编码
 *
 * @apiSuccess {key} gcode 仓库ID
 * @apiSuccess {value} - value字段详情
 * @apiSuccess {string} out_price1 出货价1
 * @apiSuccess {string} out_price2 出货价2
 * @apiSuccess {string} out_price3 出货价3
 * @apiSuccess {string} out_price4 出货价4
 *
 */

/**
 * @api {post} mall/achievement 我的业绩
 * @apiName mall/achievement
 * @apiGroup Mall
 * @apiVersion 0.0.1
 * @apiDescription 查询我的业绩
 *
 * @apiParam {int} uid 业务员ID
 *
 * @apiSuccess {int} uid 业务员ID
 * @apiSuccess {string} uname 业务员名称
 * @apiSuccess {string} all_customer_count 业务员总客户数
 * @apiSuccess {string} day_order_count 日新增订单数
 * @apiSuccess {string} day_order_amount 日销售额
 * @apiSuccess {string} day_customer_count 日新增客户数
 * @apiSuccess {string} period_order_count 月新增订单数
 * @apiSuccess {string} period_order_amount 月销售额
 * @apiSuccess {string} period_customer_count 月新增客户数
 * @apiSuccess {string} complete_rate 箱数任务完成率
 * @apiSuccess {string} period_box_total 月完成箱数
 * @apiSuccess {string} task_total 箱数任务指标
 * @apiSuccess {string} amount_complete_rate 金额任务完成率
 * @apiSuccess {string} amount_task_total 金额任务指标
 * @apiSuccess {int} ranking 排名
 * @apiSuccess {string} ranking_percent 排名百分比，击败了百分之多少的人
 * @apiSuccess {list} goods_list 商品销售TOP列表，LIST
 * @apiSuccess {list} - LIST内部字段
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {string} gspec 商品规格
 * @apiSuccess {string} total 商品销售数量
 * @apiSuccess {int} box_total 商品销售箱数
 * @apiSuccess {string} amount 商品销售金额
 */


function mall($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $data = $app->params;
    switch($action){
        case 'price_read':
            //init_log_oper($action, '读取商品仓库价格列表');
            $my_model = new Price($id);
            param_need($data, ['cid','goods_list']);
            $g_model = new Goods();
            $c_model = new Customer();
            $goods_list = json_decode($data['goods_list'], True);
            $result = [];
            foreach($goods_list as $goods){
                param_need($goods, ['gcode','scid']);
                //先用商品code获取商品ID
                $g_res = $g_model->read_one([
                    'code'=>$goods['gcode']
                ]);
                if(!$g_res){
                    error(1423);
                }
                $goods['gid'] = $g_res['id'];
                //找到客户关系，如果没找到客户关系，价格返回-1
                $c_res = $c_model->read_one([
                    'cid' => $goods['scid'],
                    'ccid' => $data['cid']
                ]);
                if(!$c_res){
                    $price = '-1';
                }
                else{
                    //根据客户关系决定到底使用哪个级别的价格
                    $price_name = 'out_price'.$c_res['cctype'];
                    $price = $my_model->get_price($goods['gid'], $goods['scid'], $c_res['sid'], $price_name, 0);
                }
                //按传来的code组装返回的价格列表
                $result[$goods['gcode']] = $price;
            }
            success($result);
            break;

        case 'price_read_single':
            //init_log_oper($action, '读取商品仓库价格列表-单供应商');
            $my_model = new Price($id);
            param_need($data, ['scid','goods_list']);
            $g_model = new Goods();
            $c_model = new Customer();
            $goods_list = json_decode($data['goods_list'], True);
            $result = [];

            $ccid = get_value($data, 'cid');
            if($ccid){
                $c_res = $c_model->read_one([
                    'cid' => $data['scid'],
                    'ccid' => $ccid
                ]);
                if(!$c_res){
                    $price_name = False;
                }
                else{
                    //根据客户关系决定到底使用哪个级别的价格
                    $price_name = 'out_price'.$c_res['cctype'];
                }
                $sid = $c_res['sid'];
            }
            else{
                param_need($data, ['sid','cctype']);
                $sid = $data['sid'];
                $price_name =  'out_price'.$data['cctype'];
            }

            $gcode_list = [];
            foreach($goods_list as $val){
                param_need($val, ['gcode']);
                $gcode_list[] = $val['gcode'];
            }

            $g_res = $g_model->read_list_nopage([
                'code'=>$gcode_list
            ]);
            $gid_list = [];
            $code2id = [];
            foreach($g_res as $val){
                $gid_list[] = $val['id'];
                $code2id[$val['code']] = $val['id'];
            }

            if($price_name){
                $price_res = $my_model->get_prices($gid_list, $data['scid'], $sid, $price_name);
                foreach($goods_list as $val){
                    $id = $code2id[$val['gcode']];
                    $result[$val['gcode']] = get_value($price_res, $id, -2);
                }
            }
            else{
                foreach($goods_list as $val){
                    $result[$val['gcode']] = -1;
                }
            }

            success($result);
            break;

        case 'order_create':
            //init_log_oper($action, '批量创建并审核订单（入队列）');
            param_need($data, ['data']); //必选
            $result = [];
            $my_data = json_decode($data['data'], true);

            $queue_name = $app->config('mall_order_queue_name');

            foreach($my_data as $order_data){
                $temp = [
                    'orderNo'=>$order_data['orderNo'],
                    'orderId'=>'',
                ];
                $result[] = $temp;
                $order_data = json_encode($order_data);
                $app->kv->rpush($queue_name, $order_data);
            }
            success($result);
            break;

        case 'order_create2':
            //init_log_oper($action, '批量创建并审核订单');
            $my_model = new Order($id);
            $s_model = new Store();
            $c_model = new Customer();
            $u_model = new User();

            param_need($data, ['data']); //必选
            $result = [];
            $my_data = json_decode($data['data'], true);

            foreach($my_data as $order_data){
                $goods_list = $order_data['goods_list'];
                $flag = 0;
                foreach($goods_list as $goods){
                    if($goods['total'] <=0 || $goods['unit_price']<0){
                        $flag = 1;
                        break;
                    }
                }
                if($flag){
                    $result[] = [
                        'orderNo'=>$order_data['orderNo'],
                        'errCode'=>'3006'
                    ];
                    continue;
                }

                //获取入库公司的第一个仓库，作为默认入库仓库
                $store_res = $s_model->get_first_store($order_data['in_cid']);
                if(!$store_res){
                    $result[] = [
                        'orderNo'=>$order_data['orderNo'],
                        'errCode'=>'5101'
                    ];
                    continue;
                    //error(5101); 没有仓库
                }
                $order_data['in_sid'] = $store_res['id'];
                $order_data['in_sname'] = $store_res['name'];
                $order_data['rank'] = $order_data['delivery'];
                $order_data['in_cname'] = $my_model->get_name_by_id('o_company', $order_data['in_cid']);
                $order_data['out_cname'] = $my_model->get_name_by_id('o_company', $order_data['out_cid']);

                $order_data['checktime'] = date('Y-m-d H:i:s');
                $order_data['status'] = 2;
                $order_data['type']   = 1; //采购订单
                $order_data['mall_orderno'] = $order_data['orderNo'];

                $user_res = $u_model->get_first_user($order_data['in_cid']);
                if(!$user_res){
                    $result[] = [
                        'orderNo'=>$order_data['orderNo'],
                        'errCode'=>'1342'
                    ];
                    continue;
                }
                $order_data['buid'] = $user_res['id'];
                $order_data['buname'] = $user_res['name'];

                //设置默认业务员和默认出货仓库

                $c_res = $c_model -> read_one([
                    'cid' => $order_data['out_cid'],
                    'ccid' => $order_data['in_cid']
                ]);
                if($c_res){
                    $order_data['out_sid'] = $c_res['sid'];
                    $order_data['out_sname'] = $my_model->get_name_by_id('o_store', $order_data['out_sid']);

                    $suid = get_value($order_data, 'suid');
                    if($suid){
                        $order_data['suname'] = $my_model->get_name_by_id('o_user', $suid);
                    }
                    else{
                        $order_data['suid'] = $c_res['suid'];
                        $order_data['suname'] = $c_res['suname'];
                    }
                }
                else{
                    $result[] = [
                        'orderNo'=>$order_data['orderNo'],
                        'errCode'=>'1710'
                    ];
                    continue;
                    //error(1710);
                }
                //检测商城订单是否重复下单过
                $res = $my_model->check_mall_orderno($order_data['mall_orderno']);
                if($res){
                    $result[] = [
                        'orderNo'=>$order_data['orderNo'],
                        'errCode'=>'5100'
                    ];
                    continue;
                    //error(5100);
                }

                $order_data['goods_list'] = json_encode($order_data['goods_list']);
                $order_id = $my_model->add($order_data); //创建订单
                $temp = [
                    'orderNo'=>$order_data['orderNo'],
                    'orderId'=>$order_id
                ];
                $result[] = $temp;
            }
            success($result);
            break;

        case 'order_cancel':
            //init_log_oper($action, '商城取消订单');
            $my_model = new Order();
            if(!$id){
                error(1100);
            }
            $res = $my_model->has([
                'mall_orderno'=>$id
            ]);
            if($res){
                //如果订单存在，则取消订单
                $my_model->update(['status'=>9],['mall_orderno'=>$id]);
            }
            else{
                //如果订单不存在，则预取消订单
                $app->kv->setex('delete_'.$id, 3600, 1);
            }
            success();
            break;

        case 'login':
            //init_log_oper($action, '登录'); //尽早记录操作日志
            param_need($data, ['username','password']);	//判断参数是否必填
            param_check($data, [
                'password' => "/^\w+$/",
            ]);	//判断所传参数是否符合规范，正则
            $my_model = new Login();

            $login_res = $my_model -> mall_login($data['username'], $data['password']);
            success($login_res);
            break;

        case 'price_read_by_gcode':
            param_need($data, ['cid','gcode']);

            //先获取公司下的所有仓库
            $s_model = new Store();
            $s_res = $s_model->read_list_nopage([
                'cid'=>$data['cid'],
                'status'=>1
            ]);
            $sid_list = [];
            $sname_dict = [];
            foreach($s_res as $val){
                $sid_list[] = $val['id'];
                $sname_dict[$val['id']] = $val['name'];
            }
            if(!$sid_list){
                error(5101);
            }

            $cg_model = new CompanyGoods();
            $sg_model = new StoreGoods();
            $cg_res = $cg_model->read_list_nopage([
                'gcode' => $data['gcode'],
                'in_cid' => $data['cid']
            ]);
            if(!$cg_res){
                error(1423);
            }
            $gid = $cg_res[0]['gid'];

            $result[] = [
                'sid' => -1,
                'sname' => '',
                'out_price1' => $cg_res[0]['out_price1'],
                'out_price2' => $cg_res[0]['out_price2'],
                'out_price3' => $cg_res[0]['out_price3'],
                'out_price4' => $cg_res[0]['out_price4'],
            ];

            $sg_res = $sg_model->read_list_nopage([
                'gid' => $gid,
                'in_cid' => $data['cid']
            ]);
            $sg_data = [];
            foreach($sg_res as $val){
                $sg_data[$val['in_sid']] = $val;
            }

            foreach($sid_list as $sid){
                if(isset($sg_data[$sid])){
                    $result[] = [
                        'sid' => $sid,
                        'sname' => $sname_dict[$sid],
                        'out_price1' => $sg_data[$sid]['out_price1'],
                        'out_price2' => $sg_data[$sid]['out_price2'],
                        'out_price3' => $sg_data[$sid]['out_price3'],
                        'out_price4' => $sg_data[$sid]['out_price4'],
                    ];
                }
                else{
                    $result[] = [
                        'sid' => $sid,
                        'sname' => $sname_dict[$sid],
                        'out_price1' => $cg_res[0]['out_price1'],
                        'out_price2' => $cg_res[0]['out_price2'],
                        'out_price3' => $cg_res[0]['out_price3'],
                        'out_price4' => $cg_res[0]['out_price4'],
                    ];
                }
            }
            success($result);

            break;

        case 'price_read_by_company':
            param_need($data, ['cid','goods_list']);

            $sid = get_value($data, 'sid');

            $goods_list = json_decode($data['goods_list'], True);
            $result = [];

            $gcode_list = [];
            foreach($goods_list as $val){
                param_need($val, ['gcode']);
                $gcode_list[] = $val['gcode'];
            }
            if(!$gcode_list){
                success([]);
            }

            $cg_model = new CompanyGoods();
            $cg_res = $cg_model->read_list_nopage([
                'gcode' => $gcode_list,
                'in_cid' => $data['cid']
            ]);
            if(!$cg_res){
                error(1423);
            }

            $gid_list = [];
            $gid_2_code = [];
            foreach($cg_res as $val){
                $result[$val['gcode']] = [
                    'out_price1' => $val['out_price1'],
                    'out_price2' => $val['out_price2'],
                    'out_price3' => $val['out_price3'],
                    'out_price4' => $val['out_price4'],
                ];
                $gid_list[] = $val['gid'];
                $gid_2_code[$val['gid']] = $val['gcode'];
            }

            if($sid){
                $sg_model = new StoreGoods();
                $sg_res = $sg_model->read_list_nopage([
                    'gid'=>$gid_list,
                    'in_cid'=>$data['cid'],
                    'in_sid'=>$sid
                ]);
                foreach($sg_res as $val){
                    $gid = $val['gid'];
                    $result[$gid_2_code[$gid]] = [
                        'out_price1' => $val['out_price1'],
                        'out_price2' => $val['out_price2'],
                        'out_price3' => $val['out_price3'],
                        'out_price4' => $val['out_price4'],
                    ];
                }
            }

            success($result);
            break;

        case 'achievement':
            //业务员－我的业绩
            //分3部分数据：1.排名 2.总业绩 3.单品业绩TOP3
            param_need($data, ['uid']);

            $uid = $data['uid'];

            $end_date = date('Y-m-d');

            $u_model = new User();
            $u_res = $u_model->read_by_id($uid);

            $cid = $u_res[0]['cid'];


            //获取财务结账日
            $c_model = new Company();
            $c_res = $c_model->read_one(['id'=>$cid]);
            $finance_date = $c_res['financedate'];

            //确定年份
            $my_time = strtotime($end_date);

            if(!$finance_date){
                //如果没有设置财务结账日，使用自然月法则
                $year = date('Y', $my_time);
                $month = date('m', $my_time);
                $month_first_day = "$year-$month-01";
            }
            else{
                //如果设置了，使用规定法则，大于基准日月份加一，超过12月年份加一
                $year = date('Y', $my_time);
                $month = date('m', $my_time);
                $day = date('d', $my_time);
                $finance_date_temp = str_pad($finance_date,2,'0',STR_PAD_LEFT);

                if($day > $finance_date){
                    $month_first_day = "$year-$month-$finance_date_temp";
                }
                else{
                    $month_temp = $month-1;
                    if($month_temp < 1){
                        $month_temp = 1;
                        $year_temp = $year-1;
                    }
                    else{
                        $year_temp = $year;
                    }
                    $month_first_day = "$year_temp-$month_temp-$finance_date_temp";
                }
                //第一天是财务结账日的下一日
                $month_first_day = date('Y-m-d',strtotime($month_first_day)+24*3600);
            }
            $begin_date = $month_first_day;

            //总业绩
            $t_model = new Task();
            $t_data = [
                'cid'=>$cid,
                'suid'=>$uid,
                'date'=>$end_date
            ];
            $res = $t_model->form_salesman_task($t_data);
            $result = $res['data'][0];

            $result['uid'] = $result['suid'];
            $result['uname'] = $result['suname'];
            //单品业绩TOP3

            $so_model = new StockOut($id);

            $s_data = [
                'cid'=>$cid,
                'suid'=>$uid,
                'top'=>5,
                'begin_date'=>$begin_date,
                'end_date'=>$end_date,
                'orderby'=>'val3'
            ];
            $res = $so_model->form_goods_salesman($s_data);
            $result['goods_list'] = $res['data'];

            //排名计算
            $my_amount = yuan2fen($result['period_order_amount']);
            $rank_sql = "select suid from b_order where out_cid=$cid and".
                " `type`=1 and `status`=2 and checktime>='$begin_date 00:00:00' and checktime<='$end_date 23:59:59' group by suid having sum(amount)>$my_amount";
            $rank_res = $app->db->query($rank_sql)->fetchAll();
            $rank = count($rank_res)+1;

            //总业务员，计算自己超过了百分之多少的人，总数包括自有和第三方
            $all_sql = "select count(distinct(suid)) as val1 from r_customer_salesman where cid=$cid";
            $all_res = $app->db->query($all_sql)->fetchAll();
            $all_count = $all_res[0]['val1'];
            $rank_percent = num2per(($all_count-$rank)/$all_count);
            $result['ranking'] = $rank;
            $result['ranking_percent'] = $rank_percent;

            success($result);
            break;

        case 'price_change':
            param_need($data, ['cid','gid','price_list']);
            //price_list: [{'sid':1,'cctype':1,'price':2.00}...]
            $price_list = get_value($data, 'price_list');
            $price_list = json_decode($price_list, True);
            $cid = $data['cid'];
            $gid = $data['gid'];

            $sid_data = [];
            $sg_model = new StoreGoods();
            foreach($price_list as $val){
                $sid = $val['sid'];
                if(!isset($sid_data[$sid])){
                    $sid_data[$sid] = [];
                }
                $cctype = $val['cctype'];
                $sid_data[$sid]['out_price'.$cctype] = $val['price'];
            }
            foreach($sid_data as $sid=>$val){
                $sg_res = $sg_model->read_one([
                    'in_cid'=>$cid,
                    'in_sid'=>$sid,
                    'gid'=>$gid
                ]);
                if($sg_res){
                    //如果存在，update
                    $sg_model->update($val,['id'=>$sg_res['id']]);
                }
                else{
                    //不存在，insert
                    $val['in_price'] = 0;
                    $val['gid'] = $gid;
                    $val['in_cid'] = $cid;
                    $val['in_sid'] = $sid;
                    $val['status'] = 1;
                    $sg_model->create($val);
                }
            }
            success();
            break;

        case 'verify':
            //phone,platform
            param_need($data, ['phone','platform']); //必选
            $my_model = new Login();
            $phone = $my_model -> create_verify3($data['phone'], $data['platform']);
            success(['phone'=>$phone]);
            break;

        case 'vip_store_list':
            $b2c_id = $app->config('b2c_id');
            $scid = $b2c_id[$data['platform']];

            $s_model = new Store();
            $res = $s_model->read_list([
                'cid' => $scid,
                'state' => 1
            ]);
            success($res);
            break;

        case 'vip_register':
            init_log_oper($action, '会员自注册');
            param_need($data, ['contactor','username','verify','my_sid','openid','platform']); //必选
            param_check($data, [
                'type,my_sid' => "/^\d+$/",
            ]);

            $s_verify = $app->kv->get('verify_'.$data['platform'].'_'.$data['username']);
            if($s_verify != $data['verify']){
                error(1305);
            }
            $b2c_id = $app->config('b2c_id');
            $scid = $b2c_id[$data['platform']];

            $u_model = new User();
            $res = $u_model->read_one(['username'=>$data['username']]);
            if($res){
                //帐号已存在，绑定
                $uid = $res['id'];
                $cid = $res['cid'];
                $c_model = new Customer();
                $c_res = $c_model->has([
                    'cid' => $scid,
                    'ccid' => $cid
                ]);
                if(!$c_res){
                    error(6213);
                }
                $data['uid'] = $uid;
                $data['cid'] = $cid;
            }
            else{
                $my_model = new Customer($id);
                $data['name'] = $data['contactor']."(".$data['username'].")";
                $data['type'] = 1;

                $my_res = $my_model->my_register2($data, $scid);
                $data['uid'] = $my_res['uid'];
                $data['cid'] = $my_res['cid'];
            }

            //开启绑定
            $data['type'] = 1;

            $open_data = $app->kv->get('openid_'.$data['openid']);
            $open_data = json_decode($open_data, true);
            $data['state'] = $open_data['state'];
            $data['access_token'] = $open_data['access_token'];
            $data['expires_in'] = $open_data['expires_in'];
            $data['refresh_token'] = $open_data['refresh_token'];

            $weixin_config = Other::get_weixin_data($data['state']);
            if(!$weixin_config){
                error(1105);
            }

            $ut_model = new UserThird();
            $ut_model->my_create($data);
            $l_model = new Login();
            $login_res = $l_model -> login2($data['openid'], $data['platform'], $data['state']);
            success($login_res);
            break;

        case 'vip_order_check':
            //vip 下单前检测
            //param： cid,scid,price

            $c_model = new Customer();
            $c_res = $c_model->read_one([
                'cid'=>$data['scid'],
                'ccid'=>$data['cid']
            ]);
            if(!$c_res){
                $result = False;
            }
            else{
                if($c_res['cctype'] != 2){
                    $result = True;
                }
                else{
                    $so_model = new StockOut();
                    $first_date = date('Y-m-01');
                    $last_date = date('Y-m-32');
                    $s_res = $so_model->sum('amount', [
                        'cid'=>$data['scid'],
                        'in_cid'=>$data['cid'],
                        'checktime[>]'=>$first_date,
                        'checktime[<]'=>$last_date
                    ]);
                    $month_limit = $app->config('vip_month_limit');
                    if($s_res+$data['price'] <= $month_limit){
                        $result = True;
                    }
                    else{
                        $result = False;
                    }
                }
            }
            success(['result'=>$result]);
            break;

        case 'errlog':
            param_need($data, ['errmsg']);
            success();
            break;

        default:
            error(1100);
    }

}
