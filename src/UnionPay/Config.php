<?php
namespace leolei\Unionpay;

/**
 * 基础配置
 *
 * @author leolei <346991581@qq.com>
 */
class Config
{
    /**
     * 前台交易请求地址
     *
     * @return void
     * @author leolei <346991581@qq.com>
     */
    public static function frontTransUrl()
    {
        //测试模式使用测试地址
        if (config('unionpay.sanbox') == 1) {
            return 'https://gateway.test.95516.com/gateway/api/frontTransReq.do';
        } else {
            return 'https://gateway.95516.com/gateway/api/frontTransReq.do';
        }
    }
    /**
     * 后台交易请求地址
     *
     * @return void
     * @author leolei <346991581@qq.com>
     */
    public static function backTransUrl()
    {
        //测试模式使用测试地址
        if (config('unionpay.sanbox') == 1) {
            return 'https://gateway.test.95516.com/gateway/api/backTransReq.do';
        } else {
            return 'https://gateway.95516.com/gateway/api/backTransReq.do';
        }
    }
    /**
     * APP交易请求地址
     *
     * @return void
     * @author leolei <346991581@qq.com>
     */
    public static function appTransUrl()
    {
        //测试模式使用测试地址
        if (config('unionpay.sanbox') == 1) {
            return 'https://gateway.test.95516.com/gateway/api/appTransReq.do';
        } else {
            return 'https://gateway.95516.com/gateway/api/appTransReq.do';
        }
    }
    /**
     * 单笔查询请求地址
     *
     * @return void
     * @author leolei <346991581@qq.com>
     */
    public static function singleQueryUrl()
    {
        //测试模式使用测试地址
        if (config('unionpay.sanbox') == 1) {
            return 'https://gateway.test.95516.com/gateway/api/queryTrans.do';
        } else {
            return 'https://gateway.95516.com/gateway/api/queryTrans.do';
        }
    }
    /**
     * 获取公钥文件夹路径
     *
     * @return void
     * @author leolei <346991581@qq.com>
     */
    public static function getCerDir()
    {
        return $_SERVER['DOCUMENT_ROOT'].config('unionpay.cer_dir');
    }

    /**
     * 获取证书路径
     *
     * @return void
     * @author leolei <346991581@qq.com>
     */
    public static function getCerPath()
    {
        return $_SERVER['DOCUMENT_ROOT'].config('unionpay.cer_path');
    }

    /**
     * 获取证书密码
     *
     * @return void
     * @author leolei <346991581@qq.com>
     */
    public static function getCerPwd()
    {
        return config('unionpay.cer_pwd');
    }

    /**
     * 获取商户ID
     *
     * @return void
     * @author leolei <346991581@qq.com>
     */
    public static function getMerchantId()
    {
        return config('unionpay.merchant_id');
    }

    /**
     * 获取密码加密证书路径
     *
     * @return void
     * @author leolei <346991581@qq.com>
     */
    public static function getEncryptPath()
    {
        return $_SERVER['DOCUMENT_ROOT'].config('unionpay.cer_encrypt');
    }
}
