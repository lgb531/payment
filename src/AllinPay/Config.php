<?php
namespace leolei\AllinPay;

/**
 * 基础配置
 *
 * @author leolei <346991581@qq.com>
 */
class Config
{
    /**
     * H5交易请求地址
     * 测试/生产地址同一个
     * @return void
     * @author leolei <346991581@qq.com>
     */
    public static function wapPayUrl()
    {
        return 'https://cashier.allinpay.com/mobilepayment/mobile/SaveMchtOrderServlet.action';
    }

    /**
     * 隐性注册地址
     * 测试/生产地址同一个
     * @return void
     * @author leolei <346991581@qq.com>
     */
    public static function userSignUrl()
    {
        return 'https://service.allinpay.com/usercenter/merchant/UserInfo/reg.do';
    }

    /**
     * 获取商户ID
     *
     * @return void
     * @author leolei <346991581@qq.com>
     */
    public static function getMerId()
    {
        return config('allinpay.mer_id');
    }

    /**
     * 获取sign
     *
     * @return void
     * @author leolei <346991581@qq.com>
     */
    public static function getSign()
    {
        return config('allinpay.sign');
    }
}
