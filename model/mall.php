<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * role
 *
 * @author      jeffwu <x010@foxmail.com>
 * @copyright   2015 jeffwu
 * @version     0.0.1
 * @package     model
 */

class Mall{
    public function notice_order($orderno, $status, $msg='', $erporderno = ''){
        $app = \Slim\Slim::getInstance();
        $url = $app->config('tofcUrl');
        $data = [
            'orderno'=>$orderno,
            'status'=>$status,
            'action'=>$msg,
            'sign'=>'',
        ];
        if($erporderno){
            $data['erporderno'] = $erporderno;
        }

        $data = http_build_query($data);
        $res = curl($url.'?'.$data);
        $res_dict = json_decode($res, True);
        if($res_dict['status'] != '200'){
            //TODO:how todo when sync err
            return False;
            //error(9920, $res_dict['status']);
        }
        else{
            return True;
        }
    }

    public function get_location($x, $y){
        $app = \Slim\Slim::getInstance();
        $url = $app->config('geoUrl');
        $data = [
            'x'=>$x,
            'y'=>$y,
        ];
        $data = http_build_query($data);
        $res = curl($url.'?'.$data);
        $res_dict = json_decode($res, True);
        if($res_dict['status'] != '300'){
            //TODO:how todo when sync err
            return False;
            //error(9920, $res_dict['status']);
        }
        else{
            if(isset($res_dict['data']['result']['formatted_address'])){
                return $res_dict['data']['result']['formatted_address'];
            }
            else{
                return False;
            }
        }
    }
}

