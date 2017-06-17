<?php

/**
 * 通道聚合入口
 */

namespace leolei\Payment;

use leolei\Payment\Util\Sign;
use leolei\Payment\Util\Http;

class SmsGateWay
{
    const GATEWAY = 'https://eco.taobao.com/router/rest';//https方式 也可用http方式

    private $params;

    public function __construct()
    {
        $this->params = [
            'app_key'            => config('alidayu.app_key'),
            'timestamp'          => date("Y-m-d H:i:s", NOW_TIME),
            'format'             => 'json',
            'v'                  => '2.0',
            'sign_method'        => 'md5',
            'sms_free_sign_name' => config('alidayu.signature')
        ];
    }

    /**
     * 银联支付
     *
     * @param string $order_sn
     * @param string $money
     * @param string $type
     * @return void
     * @author leolei <346991581@qq.com>
     */
    public function union_pay($order_sn, $money, $type)
    {
        $params                      = [];
        $params['method']            = 'alibaba.aliqin.fc.sms.num.send';
        $params['sms_type']          = 'normal';
        $params['sms_param']         = json_encode($data);
        $params['rec_num']           = $mobile;
        $params['sms_template_code'] = $template;
        $params                      = array_merge($this->params, $params);
        $params['sign']              = Sign::create($params);
        $rsp                         = Http::post(self::GATEWAY, $params);
        $rsp                         = json_decode($rsp, true);
        return self::check_error($rsp);
    }

    
}
