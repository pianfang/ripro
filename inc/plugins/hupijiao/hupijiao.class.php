<?php

class Hupijiao{
    private $app_id;
    private $app_secret;

    //支付请求地址
    private $url;

    private $api_url;
    private $api_url_native;

    public function __construct($config = []) {
        $this->app_id = isset($config['app_id'])&&$config['app_id']?$config['app_id']:Config::APP_ID;
        $this->app_secret = isset($config['app_secret'])&&$config['app_secret']?$config['app_secret']:Config::APP_SECRET;
        $this->api_url = isset($config['api_url'])&&$config['api_url'] ? $config['api_url'] : Config::API_URL;
        $this->api_url_native = $this->api_url . '/do.html';
    }

    //请求支付
    public function request($data=[]){
        if(!$data)exit('Please pass in the correct request parameters!');
        $data=$this->formatData($data);
        $this->url=$this->api_url;
        $response=$this->httpRequest($this->url,$data);
        $response=json_decode($response,true);
        return $response;
    }

    //整合请求数据并返回
    public function formatData($data=[]){
        if(!$data) exit('Please pass in the request data!');
        if(!isset($data['appid']))$data['appid']=$this->app_id;
        $data['type'] = "WAP";
        $data['wap_url'] = home_url();
        $data['wap_name'] = home_url();
        $data['hash']=$this->generateHash($data);
        return $data;
    }

    //生成hash
    public function generateHash($data){
        if(array_key_exists('hash',$data)){
            unset($data['hash']);
        }
        ksort($data);

        $buff = "";
        foreach ($data as $k => $v) {
            if ($k != "hash" && $v !== "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        $string=$buff.$this->app_secret;

        return md5($string);
    }

    //验证返回参数
    public function checkResponse($data){
        if($data['status']!='OD'){
            exit($data['status']);
        }
        //校验签名
        $hish=$this->generateHash($data);
        if($hish!=$data['hash']){
            exit('签名校验失败');
        }
        return true;
    }

    //http请求
    public function httpRequest($url, $data = [],$headers = []){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS , $data);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error=curl_error($ch);
        curl_close($ch);
        if($httpStatusCode!=200){
            throw new Exception("invalid httpstatus:{$httpStatusCode} ,response:$response,detail_error:".$error,$httpStatusCode);
        }
         
        return $response;
    }
}