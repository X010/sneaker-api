<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * Unit-test common function
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo
 * @version     0.0.1
 * @package     tests
 */

include_once "../Slim/medoo.php";

//用于单元测试的DB
function get_db(){
    $cfg_mysql = [
        'database_type' => 'mysql',
        'database_name' => 'runner',
        'server' => '115.28.8.173',
        'username' => 'root',
        'password' => 'runnerpassword',
        'charset' => 'utf8',
        'debug_mode' => false //set false when unit-test
    ];
    $db = new \Slim\medoo($cfg_mysql);
    return $db;
}

//用于单元测试的API host
function get_api_host(){
    return 'http://localhost'; //localhost
}

function my_password_hash($password){
    return strtolower(md5(md5(md5('Z8#x@2'.$password.'^7t5c'))));
}

/**
 * 登录
 *
 * @param string $username
 * @param string $password
 */
function login($username, $password){
    $url = get_api_host() . "/login/in";
    $res = curl_post($url, ['username'=>$username, 'password'=>$password]);
    $ret = json_decode($res, True);
    if (!$ret || $ret['err'] != 0){var_dump($res);}
    return $ret;
}

/**
 * 注册用户
 */
function reg_user($username = 'unittest'){
    $url = get_api_host() . "/login/register";
    $param = [
                    'username'  => $username,
                    'password'  => '111111',
                    'name' => $username,
            ];
    $res = curl_post($url, $param);
    $ret = json_decode($res, True);
    if (!$ret || $ret['err'] != 0){var_dump($res);}
    return $ret;
}

/**
 * 添加员工
 */
function add_user($sids, $rids){
    $n = make_imark();
    $url = get_api_host() . "/user/create";
    $param = [
                    'username'  => 'unittest-'.$n,
                    'password'  => '111111',
                    'name'      => 'ut',
                    'idcard'    => $n,
                    'sids'      => $sids, //仓库IDs
                    'rids'      => $rids, //角色IDs
                    'worktype'  => '测试员',
                    'email'     => 'unittest@sneaker.com',
                    'phone'     => '13700001111',
                    'memo'      => '单元测试专用帐号',
            ];
    $res = curl_post($url, $param);
    $ret = json_decode($res, True);
    if (!$ret || $ret['err'] != 0){var_dump($res);}
    return $ret;
}

/** 
 * 
 * @param   string  url 
 * @param   array   数据 
 * @param   int     请求超时时间 
 * @param   bool    HTTPS时是否进行严格认证 
 * @return  string 
 */  
function curl_post($url, $data = array(), $timeout = 10, $CA = false){    

    $cacert = getcwd() . "/cacert.pem"; //CA根证书  
    $SSL = substr($url, 0, 8) == "https://" ? true : false;  

    $ch = curl_init();  
    curl_setopt($ch, CURLOPT_URL, $url);  
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);  
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout-2);  
    if ($SSL && $CA) {  
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);   // 只信任CA颁布的证书  
        curl_setopt($ch, CURLOPT_CAINFO, $cacert); // CA根证书（用来验证的网站证书是否是CA颁布）  
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 检查证书中是否设置域名，并且是否与提供的主机名匹配  
    } else if ($SSL && !$CA) {  
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书  
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 检查证书中是否设置域名  
    }  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect:")); //避免data数据过长问题  
    curl_setopt($ch, CURLOPT_POST, true);  
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  
    //curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); //data with URLEncode  

    $ret = curl_exec($ch);  
    //var_dump(curl_error($ch));  //查看报错信息  

    curl_close($ch);  
    return $ret;    
}    


/**
 * 生成唯一值
 *
 * @return  string
 */
function make_imark()
{
    list($usec, $sec) = explode(" ", microtime());
    return $sec . trim((string)$usec, '0');
}
