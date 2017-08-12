<?php

namespace leolei\Unionpay;

use leolei\Unionpay\Lib\Rsa;
use leolei\Unionpay\Config;

/**
 * 银联代付接口
 *
 * @author leolei <346991581@qq.com>
 */
class DrPay
{
    //请求地址
    private $frontTransUrl;
    private $backTransUrl;
    private $appTransUrl;
    private $singleQueryUrl;
    //基本信息
    private $version        = '5.0.0';
    private $sign_method    = '01';//rsa
    //商户信息---构造
    private $merchant_id;
    private $back_url;
    //common---构造
    private $cert_dir;
    private $cert_path;
    private $cer_encrypt;
    private $cert_pwd;
    //订单信息
    private $order_id;
    private $txn_amt;
    private $txn_time;
    private $origin_query_id;
    //卡号信息
    private $accNo;
    private $customerInfo;

    /**
     * 初始化参数配置
     *
     * @author leolei <346991581@qq.com>
     */
    public function __construct()
    {
        //通讯网址
        $this->frontTransUrl = Config::frontTransUrl();
        $this->backTransUrl = Config::backTransUrl();
        $this->appTransUrl = Config::appTransUrl();
        $this->singleQueryUrl = Config::singleQueryUrl();
        //参数配置
        $this->cert_dir = Config::getCerDir(); //公钥目录
        $this->cert_path = Config::getCerPath(); //证书路径
        $this->cert_pwd = Config::getCerPwd(); //证书密码
        $this->merchant_id = Config::getMerchantId(); //商户号
        $this->cer_encrypt = Config::getEncryptPath(); //证书路径
    }

    /**
     * 获取APP支付参数
     */
    public function consume()
    {
        $params = [
            'version'       => $this->version,          //版本号
            'encoding'      => 'utf-8',                 //编码方式
            'signMethod'    => $this->sign_method,      //签名方法
            'txnType'       => '12',                    //交易类型
            'txnSubType'    => '00',                    //交易子类
            'bizType'       => '000401',                //业务类型
            'accessType'    => '0',                     //接入类型
            'channelType'   => '08',                    //渠道类型，07-PC，08-手机
            'currencyCode'  => '156',                   //交易币种，境内商户固定156
            'backUrl'       => $this->back_url,         //后台通知地址

            'encryptCertId' => $this->getEncryptCertId(),      //验签证书序列号
        
            //订单等信息
            'merId'         => $this->merchant_id,      //商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
            'orderId'       => $this->order_id,     //商户订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数，可以自行定制规则
            'txnTime'       => $this->txn_time,     //订单发送时间，格式为YYYYMMDDhhmmss，取北京时间，此处默认取demo演示页面传递的参数
            'txnAmt'        => $this->txn_amt,  //交易金额，单位分，此处默认取demo演示页面传递的参数

            //转账卡号等信息
            // 'accNo' => 'kRhxYEsv4RUAtbEJvsmNJSZlffTu+uWEOh1xEAVWR+ugL3zjeM9HFn4kH/Tmzfl2pW4S8fbGkjdLZ8J5XtX5CtoQgw5DlidEYsMJO0vyjqjIlzv0VsZa1y2hwFIUQmF4O10KHMz34wW1e3qdUq4rmc0mYMcDIjYEt8/nMyxc9++k4NbK07cchLlBjVnYlN/cHNCrQgRXBIHQPezMql3ZLM/0gQd8l9s6po5z/aPQ3TfHTv/03iXMa5+5DKqX9/xGowv8mYR/PsPyvVveHpptGrSSpnFl/SuJgEoS1l1ldIMm6IAaiPrc6UqKWtshQGo3x86ctaAtTXCmtheAXc2w+g==' ,     //卡号，新规范请按此方式填写$this->accNo
            // 'customerInfo' => 'e2NlcnRpZlRwPTAxJmNlcnRpZklkPTUxMDI2NTc5MDEyODMwMyZjdXN0b21lck5tPeW8oOS4iX0=', //持卡人身份信息，新规范请按此方式填写$this->customerInfo
            'accNo' => $this->accNo,
            'customerInfo' => $this->customerInfo,
            'certId' =>$this->getCertId()
        ];
        $params['signature'] = $this->makeSignature($params);
        pre($params);
        //发送数据
        $result_arr = Rsa::post($this->backTransUrl, $params);
        pre($result_arr);
        //验证请求
        if (sizeof($result_arr) <= 0) {
            return null;
        }

        //接收处理结果
        if ($result_arr["respCode"] == "00") {
            return $result_arr['respMsg'];
        }
        return null;
    }

    /**
     *  验签
     */
    public function verify($data = null)
    {
        if (!$data) {
            if (empty($_POST) && empty($_GET)) {
                return false;
            }
            $data = $_POST ?  : $_GET;
        }

        return Rsa::verify($data, $this->cert_dir);
    }

    /**
     * 生成签名
     */
    private function makeSignature($params)
    {
        return  Rsa::getParamsSignatureWithRSA($params, $this->cert_path, $this->cert_pwd);
    }

    /**
     * 获取秘钥ID
     */
    private function getCertId()
    {
        return Rsa::getCertId($this->cert_path, $this->cert_pwd);
    }

    /**
     * 获取密码加密证书ID
     *
     * @return void
     * @author leolei <346991581@qq.com>
     */
    private function getEncryptCertId()
    {
        return Rsa::getEncryptCertId($this->cer_encrypt);
    }

    public function setMerId($value)
    {
        $this->merchant_id = $value;
        return $this;
    }

    public function setNotifyUrl($value)
    {
        $this->back_url = $value;
        return $this;
    }

    public function setOrderId($value)
    {
        $this->order_id = $value;
        return $this;
    }

    public function setTxnAmt($value)
    {
        $this->txn_amt = $value;
        return $this;
    }

    public function setTxnTime($value)
    {
        $this->txn_time = $value;
        return $this;
    }

    public function setCertDir($value)
    {
        $this->cert_dir = $value;
        return $this;
    }

    public function setCertPath($value)
    {
        $this->cert_path = $value;
        return $this;
    }

    public function setCertPwd($value)
    {
        $this->cert_pwd = $value;
        return $this;
    }

    public function setOriginQueryId($value)
    {
        $this->origin_query_id = $value;
        return $this;
    }
    
    public function setAccNo($value)
    {
        $this->accNo = Rsa::encryptData($value, $this->cer_encrypt);
        return $this;
    }

    public function setCustomerInfo($value)
    {
        $this->customerInfo = Rsa::getCustomerInfoWithEncrypt($value, $this->cer_encrypt);
        return $this;
    }
}
