<?php
use GuzzleHttp\Client;

namespace s3man;

const ENUMBER = 222809;
const WSURL   = 'http://wxsms.api.ums86.com:8892/sms_hb/services/Sms?wsdl';
const HTTPURL = 'http://api.ums86.com:8888/sms/Api/Send.do';
const USER    = 'bxnjk';
const PWD     = 'Pi-2=1.14';

class Extsms {

    public $tel;
    
    public $mes;

    public function test()
    {
        print_r($this->generateParams());
    }


    public function generateParams()
    {
        $param = [
            "SpCode"          => ENUMBER,
            "LoginName"       => USER,
            "Password"        => PWD,
            "MessageContent"  => iconv('UTF-8', 'GBK', $this->mes),            
            "UserNumber"      => $this->tel,
            "SerialNumber"    => $this->swiftNum(),
            "ScheduleTime"    => '',
            "ExtendAccessNum" => '',
            "f"               =>'',
        ];
        return http_build_query($param);
    }


    public function swiftNum()
    {
        $res = substr(date("YmdHis"),2,30).mt_rand(10000000,99999999);
        return $res;
    }


    public function verifiPhone()
    {
        $verifi = '/^(1(([35789][0-9])|(47)))\d{8}$/';
        if (preg_match($verifi, $this->tel)) {
            return true;
        }
        return false;
    }

    public function sendMsg()
    {
        if (!$this->verifiPhone()) {
            throw new Exception('Mobile phone number format error');
        }
        
        $client = new Client(
            ['timeout' => 3.0]
        );

        $response = $client->request('POST', HTTPURL, [
            'body' => $this->generateParams()
        ]);

        return $response->getBody();
    }



    
}