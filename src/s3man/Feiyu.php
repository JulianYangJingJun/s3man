<?php

namespace s3man;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Feiyu
{

        public $header;
        public $timestamp;
        public $timeout;
        public $token_header;
        public $digestmode;
        public $token;
        public $key;
        public $page;
        public $pageSize = 10;
        public $base_uri = 'https://feiyu.oceanengine.com';
        public $pull_api = '/crm/v2/openapi/pull-clues/';

        /**
         * 初始化
         *
         * @return void
         */
        public function __construct($key, $token)
        {
                date_default_timezone_set("Asia/Shanghai");
                $this->header = 'Signature';
                $this->page = 1;
                $this->pageSize = 10;
                $this->timestamp = time();
                $this->timeout = 50;
                $this->token_header = 'Access-Token';
                $this->digestmode = 'sha256';
                $this->key = $key; //'TFFMT0U3TUdaVENC'; //签名密钥
                $this->token = $token; //'85231dc1612a42036a2da347be632b2df52017f4';
        }

        /**
         * 日期间隔(查询必备条件)
         *
         * @return []
         */
        public function dateInterval()
        {

                $endDate = date("Y-m-d");
                $startDate = date("Y-m-d", strtotime("-1 day"));
                $res = [$startDate, $endDate];
                return $res;
        }

        /**
         * Header数据
         *
         * @return []
         */
        public function headerData()
        {
                return [
                        'Content-Type' => 'application/json',
                        'Timestamp' => $this->timestamp,
                        $this->header => $this->generateSpliceString(),
                        $this->token_header => $this->token,
                ];
        }

        /**
         * Query数据
         *
         * @return []
         */
        public function bodyData($page)
        {
                $day = $this->dateInterval();
                return [
                        'start_time' => $day[0],
                        'end_time' => $day[1],
                        'page' => $page,
                        'page_size' => $this->pageSize,
                ];
        }

        /**
         * 迭代数据传输值
         *
         * @return str
         */
        public function request2QueryString()
        {
                $timestamp = $this->timestamp;
                $day = $this->dateInterval();
                $res = "/crm/v2/openapi/pull-clues/?start_time={$day[0]}&end_time={$day[1]} {$timestamp}";
                return $res;
        }

        /**
         * 签名
         *
         * @return str
         */
        public function generateSpliceString()
        {
                $data = $this->request2QueryString();
                $data = hash_hmac($this->digestmode, $data, $this->key);
                $data = base64_encode($data);
                return $data;
        }

        /**
         * 获取总数
         *
         * @return mixed
         */
        public function getTotalSize($page)
        {
                $totalSize = $this->getBody($page);
                if ($totalSize['status'] == 'error') {
                        return 0;
                }
                return $totalSize['count'];
        }

        /**
         * 数据结果
         *
         * @return []
         */
        public function getBody($page)
        {
                try {
                        $client = new Client(
                                [
                                        'base_uri' => $this->base_uri,
                                        'timeout' => $this->timeout,
                                ]
                        );
                        $response = $client->request(
                                'GET',
                                $this->pull_api,
                                [
                                        'headers' => $this->headerData(),
                                        'query' => $this->bodyData($page),
                                ]
                        );
                        $client = null;
                        return json_decode($response->getBody()->getContents(), true);
                } catch (RequestException $e) {
                        //TODO:日志
                        return ['status' => 'error'];
                        // throw new \Exception($e->getMessage());
                }
        }
}
