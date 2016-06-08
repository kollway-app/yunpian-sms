<?php
/**
 * Created by PhpStorm.
 * User: kollway3
 * Date: 16/6/7
 * Time: 17:23
 */
namespace src\yunSms;

class YunSmsClient {

    private $url;
    private $apikey;
    private $sign;

    public function __construct ($url,$apikey,$sing='')
    {
        $this->url = $url;
        $this->apikey = $apikey;
        $this->sign = $sing;
    }

    public function doSendSMS($phone, $smsText) {
        if (empty($phone) || empty($smsText)) {
            return false;
        }
        $smsText = sprintf('%s%s', $this->sign, $smsText);
        $encoded_text = urlencode("$smsText");
        $post_string = sprintf('apikey=%s&text=%s&mobile=%s', $this->apikey, $encoded_text, urlencode($phone));
        $response = $this->sock_post($this->url, $post_string);
        $response = json_decode($response, true);
        return $response && intval($response['code']) === 0;
    }

    /**
     * url 为服务的url地址
     * query 为请求串
     */
    private  function sock_post($url,$query){
        $data = "";
        $info=parse_url($url);
        $fp=fsockopen($info["host"],80,$errno,$errstr,30);
        if(!$fp){
            return $data;
        }
        $head="POST ".$info['path']." HTTP/1.0\r\n";
        $head.="Host: ".$info['host']."\r\n";
        $head.="Referer: http://".$info['host'].$info['path']."\r\n";
        $head.="Content-type: application/x-www-form-urlencoded\r\n";
        $head.="Content-Length: ".strlen(trim($query))."\r\n";
        $head.="\r\n";
        $head.=trim($query);
        $write=fputs($fp,$head);
        $header = "";
        while ($str = trim(fgets($fp,4096))) {
            $header.=$str;
        }
        while (!feof($fp)) {
            $data .= fgets($fp,4096);
        }
        return $data;
    }

}