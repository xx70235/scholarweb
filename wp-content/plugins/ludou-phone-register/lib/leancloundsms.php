<?php

class leancloundsms
{
    private $_id;
    
    private $_key;
    
    private $_setting = array();
    
    public $error = array();
    
    public function __construct($key = '', $id = '')
    {
        $this->_key = $key;
        $this->_id = $id;
    }
    
    public function sms_param(array $param = array())
    {
        $this->_setting['sms_param'] = json_encode($param);
    }
    
    public function sms_template($code = '')
    {
        $this->_setting['sms_template_code'] = $code;
    }
    
    public function sms_mobile($mobile = '')
    {
        $this->_setting['rec_num'] = $mobile;
    }

    public function sms_code($code = '')
    {
        $this->_setting['code'] = $code;
    }
    
    public function send()
    {
        $header = $this->_headers();
        $params = array(
            "mobilePhoneNumber"  => $this->_setting['rec_num']
        );
        $response = $this->_curl_sendsms($header, $params);
        if($response !== FALSE)
        {
            $res = json_decode($response, TRUE);
            if(count($res)==0) return TRUE;
            $this->error = $res;
        }
        else
        {
            $this->error = array('code' => 0, 'msg' => 'HTTP_RESPONSE_NOT_WELL_FORMED');
        }
        return FALSE;
    }

    public function verify()
    {
        $header = $this->_headers();
        $response = $this->_curl_verifyphone($header, $this->_setting['code'], $this->_setting['rec_num']);
        if($response !== FALSE)
        {
            $res = json_decode($response, TRUE);
            if(count($res)==0) return TRUE;
            $this->error = $res;
        }
        else
        {
            $this->error = array('code' => 0, 'msg' => 'HTTP_RESPONSE_NOT_WELL_FORMED');
        }
        return FALSE;
    }
    
    private function _params()
    {
        return array
        (
            'app_key' => $this->_key,
            'format' => 'json',
            'method' => 'alibaba.aliqin.fc.sms.num.send',
            'v' => '2.0',
            'timestamp' => date('Y-m-d H:i:s'),
            'sign_method' => 'md5',
            'sms_type' => 'normal',
        ) + $this->_setting;
    }

    private function _headers()
    {
        return array(
            'X-LC-Id: qR0mo4fcTFMdVLuXTkqbRcn2-gzGzoHsz',
            'X-LC-Key: mqSRbav4XC4RpEtmKINlWNgK',
            'Content-Type: application/json'
        );
    }
    
    private function _curl_sendsms($headers, $params)
    {
        $uri = 'https://api.leancloud.cn/1.1/requestSmsCode';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Verydows');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));

        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    private function _curl_verifyphone($headers, $code, $phone)
    {
        $uri = "https://api.leancloud.cn/1.1/verifySmsCode/$code?mobilePhoneNumber=$phone";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Verydows');
        curl_setopt($ch, CURLOPT_POST, 1);

        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}
?>
