<?php
namespace s3man;

// use GuzzleHttp\Client;

const ENUMBER = 222809;
const WSURL   = 'http://wxsms.api.ums86.com:8892/sms_hb/services/Sms?wsdl';
const HTTPURL = 'http://api.ums86.com:8888/sms/Api/Send.do';
const USER    = 'bxnjk';
const PWD     = 'Pi-2=1.14';

class Extsms {

    public $tel;
    
    public $msg;

    public function generateParams()
    {
        $param = [
            "SpCode"          => ENUMBER,
            "LoginName"       => USER,
            "Password"        => PWD,
            "MessageContent"  => iconv('UTF-8', 'GBK', $this->msg),            
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
        $res = substr(date("YmdHis"), 2, 30).mt_rand(10000000,99999999);
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
        try {
            $res = [];
            $sendMsg = $this->httpClient();
            $exlode = explode("&", $sendMsg);        
            if ($exlode[0] == 'result=0') {
                $res['res'] = 'Success';
            } else {
                $res['res'] = 'Failed';            
            }
            $res['description'] = explode("=", $exlode[1])[1];
            $res['taskid'] = explode("=", $exlode[2])[1];
            $res['faillist'] = explode("=", $exlode[3])[1];
            return $res;
        } catch (\Exception $e) {
            return false;
        }

    }

	public function httpClient() {		
		try {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, HTTPURL);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->generateParams());
			$res = curl_exec($ch);
			curl_close($ch);
			return $res;
		} catch (Exception $e) {
			$this->errorMsg = $e->getMessage();
			return false;
		}
	}	




    
}