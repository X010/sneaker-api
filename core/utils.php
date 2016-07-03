<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * common utils
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo & fish
 * @version     0.0.1
 * @package     core
 */

/**
 * handle system exception
 */
if (isset($app)){
    $app->error("HandleException");
}

/**
 * 处理异常
 * 记录错误日志，并强制成200状态
 * NOTICE:仅在 debug=false 时生效
 *
 * @param mixed $msg
 */
function HandleException($e){
    $app = \Slim\Slim::getInstance();
    //log_error($e);
    $app->response->setStatus(200);//强制HTTP状态

    $msg = $e->getMessage();
    $file = $e->getFile();
    $line = $e->getLine();
    $err_msg = "$msg~~~$file|$line";
    //err_log(json_encode($err_msg));
    error(9999, $err_msg, False);
}

/**
 * 写ERROR日志
 *
 * USAGE:
 * log_error('自定义错误内容', 1000);
 *
 * @param  mixed $e
 * @param  int   $errcode
 */
function log_error($e){
    $app = \Slim\Slim::getInstance();
    $request = $app->request->getMethod()=='POST' ? $app->request->getBody() : $_SERVER['QUERY_STRING'];
    $request = urldecode($request);
    $response = is_object($e) ? json_encode($e->getTrace()) : json_encode($e);
    $api = $app->request->getResourceUri();
    $timeused = floatval(make_imark()) - floatval($app->Sneaker->imark);

    $data = [
        'imark'     => $app->Sneaker->imark,
        'request'   => $request,
        'response'  => $response,
        'api'       => $api,
        //'msg'       => $msg,
        'uid'       => $app->Sneaker->uid,
        'timeused'  => $timeused,
        'createtime'=> date('Y-m-d H:i:s')
    ];
    err_log(json_encode($data));

}

/**
 * 写INFO日志
 *
 * USAGE:
 * log_info(); //自动记录接口流水日志
 * log_info('自定义内容'); //记录自定义日志
 * log_info(NULL, 'http://localhost/', 'a=1&b=2', '{"msg":123}', 0.123456); //记录调用外部接口日志
 *
 * @param  string $msg
 * @param  string $api
 * @param  string $request
 * @param  string $response
 * @param  float $timeused
 */
function log_info($err = False, $msg = NULL, $api = NULL, $request = NULL, $response = NULL, $timeused = NULL){
    $app = \Slim\Slim::getInstance();
    if (!isset($api))
        $api = $app->request->getResourceUri();
    if (!isset($request)) 
//        $request = $app->request->getMethod()=='POST' ? $app->request->getBody() : $_SERVER['QUERY_STRING'];
//        $request = urldecode($request);
        $request = json_encode($app->request->params());
    if (!isset($response))
        $response = $app->response->getBody();
    if (!isset($timeused))
        $timeused = floatval(make_imark()) - floatval($app->Sneaker->imark);
    $data = [
        'imark'     => $app->Sneaker->imark,
        'request'   => $request,
        'response'  => $response,
        'api'       => $api, 
        'msg'       => $msg,
        'uid'       => $app->Sneaker->uid,
        'timeused'  => $timeused,
        'createtime'=> date('Y-m-d H:i:s'),
        'browser'   => $app->request->getUserAgent(),
        'ip'        => $app->request->getIp()
    ];
    //$app->log->info(json_encode($data));
    if($err){
        err_log(json_encode($data));
    }
    else{
        info_log(json_encode($data));
    }
}


/**
 * 写操作日志
 *
 * @param   int     $flag           result of the execute: 1 success / 0 fail
 */
function log_oper($flag){
    $app = \Slim\Slim::getInstance();
    if(isset($app->Sneaker->module_id)){
    	$data = [
    			'uid'       => $app->Sneaker->uid,
    			'uname'     => $app->Sneaker->uname,
    			'ip'        => $app->request->getIp(),
    			'flag'      => intval($flag),
    			'action_type' => $app->Sneaker->action_type,
    			'action_msg'  => $app->Sneaker->action_msg,
    			'module_id'   => $app->Sneaker->module_id,
    			'module_name' => $app->Sneaker->module_name,
    			'menu_id'   => $app->Sneaker->menu_id,
    			'menu_name' => $app->Sneaker->menu_name,
    			'imark'     => $app->Sneaker->imark,
    			'createtime'=> date('Y-m-d H:i:s'),
				'cid'       => $app->Sneaker->cid,
                'platform'  => $app->platform,
    	];
    	$app->log->notice(json_encode($data));
    }
}

function debug_log($msg){
    syslog(LOG_DEBUG, $msg);
}

function info_log($msg){
    syslog(LOG_INFO, $msg);
}

function err_log($msg){
    syslog(LOG_ERR, $msg);
}

/**
 * 生成消息标识（日志检索标志）
 *
 * @return  string
 */
function make_imark()
{
    list($usec, $sec) = explode(" ", microtime());
    return $sec . trim((string)$usec, '0');
}

/**
 * 初始化当前操作的菜单和模块名
 *
 * @param   string $function_name
 */
function init_menu_and_module_name($function_name){
    $app = \Slim\Slim::getInstance();
    $route = $app->config('route');
    if(isset($route[$function_name])){
        list($app->Sneaker->menu_name, $app->Sneaker->menu_id) = explode('#', $route[$function_name]['menu']);
        list($app->Sneaker->module_name, $app->Sneaker->module_id) = explode('#', $route[$function_name]['module']);
    }
    else{
        list($app->Sneaker->menu_name, $app->Sneaker->menu_id) = [Null,Null];
        list($app->Sneaker->module_name, $app->Sneaker->module_id) = [Null,Null];
    }

}

/**
 * 获取数组的值
 *
 * @param array $data 数组
 * @param mixed $key 要取的键
 * @param mixed $default 如果未取到值，给此默认值
 * @return mixed
 */
function get_value($data, $key, $default = Null){
    return isset($data[$key]) ? $data[$key] : $default;
}


/**
 * 检测必填参数，检测param是否包含need中的每一个key
 *
 * @param array $param 要检测的对象数组
 * @param array $need 必须的key数组
 */
function param_need($param, $need)
{
	foreach($need as $name){
		if (!isset($param[$name]) || $param[$name]===''){
			error('1101', $name);
		}
	}
}


/**
 * 检测参数规则，检测param中的值是否符合rule指定的规则
 *
 * @param array $param 要检测的对象数组
 * @param array $rule 规则数组
 */
function param_check($param, $rule){
	foreach($rule as $key=>$value){
		if(strpos($key, ',')){
			$key_array = explode(',', $key);
			foreach($key_array as $v){
				if(isset($param[$v])){
					if(!preg_match($value, $param[$v])){
						error('1102', $v);
					}
				}
			}
		}
		else{
			if(isset($param[$key])){
				if(!preg_match($value, $param[$key])){
					error('1102', $key);
				}
			}
		}
		
	}
}


/**
 * 写操作日志 之 准备工作
 *
 * @param   string  $action_type
 * @param   string  $action_msg
 */
function init_log_oper($action_type, $action_msg){
    $app = \Slim\Slim::getInstance();
    $app->Sneaker->log_oper_off = 0; //打开操作日志
    $app->Sneaker->action_type = strval($action_type);
    $app->Sneaker->action_msg = strval($action_msg);
}


/**
 * 密码加密函数 
 *
 * @param string $password 密码明文
 *
 * @return string 密码密文
 */
function my_password_hash($password){
	return strtolower(md5(md5(md5('Z8#x@2'.$password.'^7t5c'))));
}

/**
 * 客户自下单挨批返回的
 * 数据结构
 *  
 */
function respCustomer($data, $total=0, $status=200, $msg='')
{
    return success('success', true,[
        'count'   => $total,
        'status'  => $status,
        'message' => empty($msg) ? 'success' : $msg,
        'data' => $data,
    ]);
}

/**
 * 统一Response（正确）
 * NOTICE：此函数输出响应后会终止程序运行！
 *
 * @param mixed $msg 要返回的具体数据
 */
function success($msg = 'success', $log = True, $dataFormated=[]){

	$app = \Slim\Slim::getInstance();
	$data = [
		'err'   => 0,
		'status'=> '0000',
		'msg'   => $msg
	];
    if ($dataFormated) {
        $data = $dataFormated;
    }

    //$fileSiz = mb_strlen(json_encode($data));
    //header("Content-Length: $fileSiz");

	$callback=$app->request->params('callback');
	if(isset($callback))
	{
		//支持JSONP
		$app->response->setBody($callback.'('.json_encode($data).')');
	}else {
		$app->response->setBody(json_encode($data)); //响应请求（response）
	}
    end_action();

	echo $app->response->getBody();
    if($log){
        log_info(); //自动记录接口info日志
        $app->Sneaker->log_oper_off || log_oper(1); //写操作日志（操作成功）
    }
    exit;
}

/**
 * 统一Response（错误）
 * NOTICE：此函数输出响应后会终止程序运行！
 *
 * @param int $code 错误码
 * @param string $msg 错误信息
 */
function error($code, $msg = '', $throw = True){
	$app = \Slim\Slim::getInstance();
	$errcode = $app->config('errcode');
	$errmsg = isset($errcode[$code]) ? $errcode[$code] : 'unknown error';
	$errmsg = str_replace('{%s}', $msg, $errmsg);
	$data = [
			'err'   => 1,
			'status'=> $code,
			'msg'   => $errmsg
	];
    $data = json_encode($data);

    $callback=$app->request->params('callback');
    if(isset($callback))
    {
        //支持JSONP
        $app->response->setBody($callback.'('.$data.')');
    }else {
        $app->response->setBody($data); //响应请求（response）
    }

    //$app->response->setBody($data); //响应请求（response）

    end_action(False);

    echo $app->response->getBody();
    if($code == '9900'){
        err_log(json_encode($app->db->pdo->errorInfo()));
    }

    log_info(True); //自动记录接口info日志
    if ($code<1310 || $code>1313){
        $app->Sneaker->log_oper_off || log_oper(0); //写操作日志（操作失败）
    }

    if($throw){
        throw new SneakerException($data);
    }
    else{
        exit;
    }

    //exit;
}

function daemon_error($code, $msg = ''){
    $data = [
        'err'   => 1,
        'status'=> $code,
        'msg'   => $msg
    ];
    $data = json_encode($data);
    err_log($data);
    end_action(False);
    throw new SneakerException($data);
    //exit;
}

/**
 * 生成19位单号
 * -----------------已废弃------------------------
 * @param int $type 单号类型：1-订单 2-入库单 3-出库单 
 *
 * @return  string  eg. 1150102112233123456
 */
function make_bill_id($type)
{
    if (!in_array($type, [1,2,3])) error(9901);
    $ret = $type . date('ymdHis');
    list($usec, $sec) = explode(" ", microtime());
    $ret .= substr(strval($usec), 2, 6);
    return $ret;
}


/**
 * 开始事务，支持多次调用，只会开启一次
 *
 */
function start_action(){
    $app = \Slim\Slim::getInstance();
    if($app->Sneaker->action == 0){
        $app->Sneaker->action = 1;
        $app->db->pdo->beginTransaction();
    }
}

/**
 * 结束事务，如果手工调用可以提前结束事务，不调用的话也会自动结束事务
 *
 */
function end_action($success = true){
    $app = \Slim\Slim::getInstance();
    if($app->Sneaker->action == 1){
        if($success){
            $app->db->pdo->commit();
        }
        else{
            $app->db->pdo->rollBack();
        }
        $app->Sneaker->action = 0;
    }
}

/**
 * 计算税金
 * @param float $price 售价
 * @param float $tax_rate 税率
 *
 * @return float tax_price 税额
 * @return float outtax_price 去税金额
 */
function get_tax($price, $tax_rate=0){
    $outtax_price = $price/(1+$tax_rate);
    return [
        'tax_price' => $price-$outtax_price, //税额
        'outtax_price' => $outtax_price  //去税金额
    ];
}

/**
 * 价格从分转换成元
 * @param int $val 金额，以分为单位
 *
 * @return string 金额，以元为单位
 */
function fen2yuan($val){
    return sprintf("%.2f", $val/100);
}

/**
 * 价格从元转换成分
 * @param string/float $val 金额，以元为单位
 *
 * @return int 金额，以分为单位
 */
function yuan2fen($val){
    if($val>0){
        return intval($val*100+0.5);
    }
    else{
        return intval($val*100-0.5);
    }
}

function format_yuan($val){
    return sprintf("%.2f", $val);
}

/**
 * 索引数组转列表数组
 * @param array $data 索引数组
 *
 * @return array 转换后的列表
 */
function dict2list($data){
    $new_data = [];
    foreach($data as $val){
        $new_data[] = $val;
    }
    return $new_data;
}

/**
 * 两个金额相加，返回总金额
 * @param string $price1 金额1
 * @param string $price2 金额2
 *
 * @return string 相加以后的金额
 */
function price_add($price1, $price2){
    $price = yuan2fen($price1)+yuan2fen($price2);
    return fen2yuan($price);
}

/**
 * 两个金额相减，返回总金额
 * @param string $price1 金额1
 * @param string $price2 金额2
 *
 * @return string 相减以后的金额
 */
function price_sub($price1, $price2){
    $price = yuan2fen($price1)-yuan2fen($price2);
    return fen2yuan($price);
}

/**
 * 产生一个负金额
 * @param string $price1 金额1
 *
 * @return string 金额1的负数
 */
function price_neg($price1){
    $price = 0-yuan2fen($price1);
    return fen2yuan($price);
}

//数字转百分比
function num2per($val){
    return sprintf("%.2f", $val*100). '%';
}

function my_rate($val1,$val2){
    if($val2){
        return num2per($val1/$val2);
    }
    else{
        return num2per(0);
    }
}

/**
 * 普通事件格式转成crontab时间格式
 * @param string $datetime 时间格式 Y-m-d H:i:s
 *
 * @return string crontab时间格式 30 12 10 30 5 ? 2015
 */
function time_chg($datetime){
    $time = strtotime($datetime);
    $t1 = date('Y', $time);
    $t2 = intval(date('m', $time));
    $t3 = intval(date('d', $time));
    $t4 = intval(date('H', $time));
    $t5 = intval(date('i', $time));
    $t6 = intval(date('s', $time));

    $result = $t6. ' '. $t5. ' '. $t4. ' '. $t3. ' '. $t2. ' ? '. $t1. '-'. $t1;
    return $result;
}

/*
//处理前段传入的ids 类型参数，去掉最后一个逗号
function format_ids($ids){
    $len = strlen($ids);
    if($ids[$len-1] == ','){
        $ids = substr($ids, 0, $len-1);
    }
    return $ids;
}
*/

//批量处理前段传入的ids 类型参数，去掉最后一个逗号
function format_data_ids($data, $ids_list){
    foreach($ids_list as $ids){
        $temp_data = get_value($data, $ids);
        if($temp_data){
            //$data[$ids] = format_ids($data[$ids]);
            $data[$ids] = rtrim($data[$ids], ',');
        }
    }
    return $data;
}

//处理前端传入的ids 类型参数，保证第一个和最后一个字符都加上逗号
function format_sids_douhao($ids){
    /*
    if($ids[0] != ','){
        $ids = ','. $ids;
    }
    $len = strlen($ids-1);
    if($ids[$len-1] != ','){
        $ids = $ids . ',';
    }
    */
    $ids = ',' . trim($ids, ',') . ',';
    return $ids;
}

//批量去空格
function array_trim($data){
    $result = [];
    foreach($data as $key=>$val){
        $result[$key] = trim($val);
    }
    return $result;
}

function get_spec($val){
    $val_list = explode('*', $val);
    $res = 1;
    foreach($val_list as $val2){
        if(!is_numeric($val2)){
            //如果遇到非数字的情况，强制返回规格为1
            return 1;
        }
        $res *= $val2;
    }
    return $res;
}

//格式化地址
function format_address($val){
    return str_replace('null ', '', $val);
}

/**
 * 请求外部服务
 * @param string $url 请求地址
 * @param array $data POST请求参数，为空时自动成为GET请求
 *
 * @return string 请求返回内容
 */
function curl($url, $data=Null, $timeout=5){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if($data){
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    else{
        curl_setopt($ch, CURLOPT_HEADER, 0);
    }
    $time1 = microtime(true);
    $output = curl_exec($ch);
    $time2 = microtime(true);
    curl_close($ch);

    if(!$data){
        $data = 'type=get';
    }
    else{
        $data = http_build_query($data);
    }
    log_info(NULL, NULL, $url, $data, $output, $time2-$time1);
    return $output;
}

function zh($val){
    return iconv("UTF-8", "gbk", $val);
}

// DATA : 2维数组
function write_excel($data, $title){
    require_once 'core/PHPExcel.php';
    $file_name = $title.'.xlsx';

    foreach($data as $key=>$val){
        $data[$key] = dict2list($val);
    }

    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
        ->setLastModifiedBy("Maarten Balliauw")
        ->setTitle("Office 2007 XLSX Test Document")
        ->setSubject("Office 2007 XLSX Test Document")
        ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("Test result file");

    $num_hang = 1;
    $obj = $objPHPExcel->setActiveSheetIndex(0);
    foreach($data as $key=>$val){
        foreach($val as $key2=>$val2){
            $obj = $obj->setCellValueExplicit(get_chr($key2).$num_hang, $val2, PHPExcel_Cell_DataType::TYPE_STRING);
        }
        $num_hang ++;
    }

//    foreach($data[0] as $key=>$val){
//        $objPHPExcel->getActiveSheet()->getColumnDimension(get_chr($key))->setAutoSize(true);
//    }

    $objPHPExcel->getActiveSheet()->setTitle($file_name);
    //$objPHPExcel->setActiveSheetIndex(0);
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');

    exit;
}

function get_chr($num){
    $chr = 'A';
    if($num<=25){
        return chr(ord($chr)+$num);
    }
    else{
        $ans = $num/26-1;
        $yus = $num%26;
        return chr(ord($chr)+$ans).chr(ord($chr)+$yus);
    }
}

function number_to_name($number, $type){
    $chg = [];
    switch($type){
        case 'stock_out_type':
            $chg = [
                1=>'销售',
                2=>'退货',
                3=>'调出',
                4=>'报损',
                5=>'盘亏'
            ];
            break;
        case 'stock_out_status':
            $chg = [
                1=>'未审核',
                2=>'未审核',
                3=>'缺货待配',
                4=>'已审核',
                9=>'已作废',
                10=>'已冲单',
                11=>'冲单（负单）',
                12=>'已修正',
                13=>'修正单（负单）'
            ];
            break;
        case 'stock_out_settle_status':
            $chg = [
                0=>'未结算',
                1=>'已结算'
            ];
            break;
        case 'stock_in_type':
            $chg = [
                1=>'采购',
                2=>'退货',
                3=>'调入',
                4=>'报溢',
                5=>'盘盈'
            ];
            break;
        case 'stock_in_status':
            $chg = [
                1=>'未审核',
                2=>'已审核',
                9=>'已作废',
                10=>'已冲单',
                11=>'冲单（负单）',
                12=>'已修正',
                13=>'修正单（负单）'
            ];
            break;
        case 'stock_in_settle_status':
            $chg = [
                0=>'未结算',
                1=>'已结算'
            ];
            break;
        case 'reserve_from':
            //1-进货 2-退货 3-调拨 4-报溢 5-盘盈 9-冲正 10-退货冲正
            $chg = [
                1=>'进货',
                2=>'退货',
                3=>'调拨',
                4=>'报溢',
                5=>'盘盈',
                9=>'冲正',
                10=>'退货冲正',
            ];
            break;
        case 'pay_type':
            $chg = [
                1=>'微信',
                2=>'支付宝',
                3=>'现金',
                4=>'银行卡',
                5=>'支票',
                6=>'网银',
            ];
            break;
        case 'rank':
            $chg = [
                0=>'不限制',
                1=>'立即送',
                2=>'当日送',
                3=>'隔日送',
                4=>'本周送',
            ];
            break;
        case 'source':
            $chg = [
                1=>'签到',
                2=>'新客户申请',
                3=>'老客户意见',
                4=>'老客户图像',
            ];
            break;
    }
    return get_value($chg, $number);
}

function days_sub($time1, $time2){
    if(!$time1 || !$time2){
        return 0;
    }
    $time1 = substr($time1, 0, 10);
    $time2 = substr($time2, 0, 10);
    $my_time = strtotime($time1)-strtotime($time2);
    return intval($my_time/24/3600 + 0.5);
}

function days_add($time1, $days){
    if(!$time1 || !$days){
        return 0;
    }
    $time1 = substr($time1, 0, 10);

    $my_time = strtotime($time1)+$days*24*3600;
    return date('Y-m-d', $my_time);
}

function cc_format($name){
    $temp_array = array();
    for($i=0;$i<strlen($name);$i++){
        $ascii_code = ord($name[$i]);
        if($ascii_code >= 65 && $ascii_code <= 90){
            if($i == 0){
                $temp_array[] = chr($ascii_code + 32);
            }else{
                $temp_array[] = '_'.chr($ascii_code + 32);
            }
        }else{
            $temp_array[] = $name[$i];
        }
    }
    return implode('',$temp_array);
}

function rsa_decrypt($val){
    // Encryption exponent and modulus generated via
    // openssl genrsa -out private_key.pem 2048
    // $key = file_get_contents('key/private_key.pem');
    $private_key = openssl_pkey_get_private('file://key/private_key.pem');
    // ciphertext generated by JavaScript uses PKCS1 padding, emitted as base-64 string...
    // ...convert to binary.
    $bin_ciphertext = base64_decode($val);
    $res = openssl_private_decrypt($bin_ciphertext, $plaintext, $private_key, OPENSSL_PKCS1_PADDING);
    if(!$res){
        error(9930);
    }
    return $plaintext;
}


//致命异常处理
function my_errcatch(){
    $_error=error_get_last();
    if($_error && in_array($_error['type'],array(1,4,16,64,256,4096,E_ALL))){
        //log_error($e);
        $app = \Slim\Slim::getInstance();
        $app->response->setStatus(200);//强制HTTP状态

        $msg = $_error['message'];
        $file = $_error['file'];
        $line = $_error['line'];
        $err_msg = "$msg~~!$file|$line";
        //err_log(json_encode($err_msg));
        error(9999, $err_msg, False);
    }
}