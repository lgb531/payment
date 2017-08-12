<?php
namespace leolei\AllinPay;

use leolei\AllinPay\Config;

/**
 * 通联H5支付类
 * Class WapPay
 */
class WapPay
{
    //请求地址
    private $wapPayUrl;
    private $userSignUrl;
    //基本信息
    private $version  = 'v1.0';
    private $signType = '0';
    private $payType = '33';
    //商户信息
    private $mer_id;
    private $front_url;
    private $back_url;
    //订单信息
    private $order_id;
    private $txn_amt;
    private $txn_time;
    private $user_id;
    //common
    private $key;

    /**
     * 初始化参数配置
     *
     * @author leolei <346991581@qq.com>
     */
    public function __construct()
    {
        //通讯网址
        $this->wapPayUrl = Config::wapPayUrl();
        $this->userSignUrl = Config::userSignUrl();
        //参数配置
        $this->mer_id = Config::getMerId(); //商户号
        $this->key = Config::getSign(); //证书密码
    }

    /**
     * H5快捷支付接口
     *
     * @return void
     * @author leolei <346991581@qq.com>
     */
    public function consume()
    {
        //配置参数
        $params = [
            'inputCharset'  => '1', //编码方式 1:utf-8 2:gbk 3:gb2312
            'pickupUrl'     => $this->front_url, //前台通知地址
            'receiveUrl'    => $this->back_url, //后台通知地址
            'version'       => $this->version, //版本号
            'language'      => '1', //语言类型 1-简体中文 2-繁体中文 3-英文
            'signType'      => '0', // 0-md5 1-证书
            'merchantId'    => $this->mer_id,
            'payerName'     => '',
            'payerEmail'    => '',
            'payerTelephone'=> '',
            'payerIDCard'   => '',
            'pid'           => '',
            'orderNo'       => $this->order_id,//订单号
            'orderAmount'   => $this->txn_amt,//订单金额
            'orderCurrenc'  => '0',
            'orderDatetime' => $thi->txn_time,//交易发起时间
            'orderExpireDatetime'=> '',
            'productName'   => '',
            'productPrice'  => '',
            'productNum'    => '',
            'productId'     => '',
            'productDesc'   => '',
            'ext1'          => '<USER>'.$this->user_id.'</USER>',
            'ext2'          => '',
            'extTL'         => '',
            'payType'       => $this->payType,
            'issuerId'      => '',
            'pan'           => '',
            'tradeNature'   => '' //选填
        ];

        //生成签名
        $params['signMsg'] = $this->makeSignature($params);
        //抛出表单---前台回调
        $html_form = self::createAutoFormHtml($params, $this->wapPayUrl);

        return $html_form;
    }

    /**
     * 通联隐性注册
     *
     * @return void
     * @author leolei <346991581@qq.com>
     */
    public function user_sign()
    {
        //配置参数
        $params = [
            'signType'      => '0', // 0-md5 1-证书
            'merchantId'    => $this->mer_id,
            'partnerUserId' => $this->user_id,
        ];

        //生成签名
        $params['signMsg'] = $this->makeSignature($params);

        $data = http_build_query($params);
        
        $opts = [
            'http' => [
                'method'=>"POST",
                'header'=>"Content-type: application/x-www-form-urlencoded\r\n"."Content-length:".strlen($data)."\r\n" ."Cookie: foo=bar\r\n" ."\r\n",
                'content' => $data
            ]
        ];

        $cxContext = stream_context_create($opts);
        $sFile = file_get_contents($this->userSignUrl, false, $cxContext);
        $extra = json_decode($sFile, true);

        if ($extra) {
            return $extra['userId'];
        } else {
            return fasle;
        }
    }

    /**
     * 返回字符串验证签名
     *
     * @param array $data
     * @return void
     * @author leolei <346991581@qq.com>
     */
    public function verify($data = null)
    {
        // 先判断是否有返回参数
        if (!$data) {
            if (empty($_POST) && empty($_GET)) {
                return false;
            }
            $data = $_POST ?  : $_GET;
        }

        $sign = $data ['signMsg'];
        unset($data['signMsg']);
        $res_sign = $this->makeSignature($data);
        if ($sign == $res_sign) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 支付字符串签名
     *
     * @param array $params
     * @return void
     * @author leolei <346991581@qq.com>
     */
    private function makeSignature($params)
    {
        // 清除空数据
        foreach ($params as $key => $val) {
            if ($val == '') {
                unset($params[$key]);
            }
        }
        $query = http_build_query($params);
        $query .= $query."&key=".$this->key;
        return strtoupper(md5($query));
    }

    /**
     * 构造H5支付表单
     * @param $params
     * @param $reqUrl
     * @return string
     */
    public static function createAutoFormHtml($params, $reqUrl)
    {
        $encodeType = isset ( $params ['encoding'] ) ? $params ['encoding'] : 'UTF-8';
        $html = <<<eot
        <html>
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset={$encodeType}" />
        </head>
        <body onload="javascript:document.pay_form.submit();">
        <form id="pay_form" name="pay_form" action="{$reqUrl}" method="post">
eot;
        foreach ($params as $key => $value) {
            $html .= "<input type=\"hidden\" name=\"{$key}\" id=\"{$key}\" value=\"{$value}\" />\n";
        }
        $html .= <<<eot
        </form>
        </body>
        </html>
eot;
        return $html;
    }
}
