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
     * 获取公钥文件夹路径
     *
     * @return void
     * @author leolei <346991581@qq.com>
     */
    public function getCerDir()
    {
        return config('unionpay.cer_dir');
    }

    /**
     * 获取证书路径
     *
     * @return void
     * @author leolei <346991581@qq.com>
     */
    public function getCerPath()
    {
        return config('unionpay.cer_path');
    }

    /**
     * 获取证书密码
     *
     * @return void
     * @author leolei <346991581@qq.com>
     */
    public function getCerPwd()
    {
        return config('unionpay.cer_pwd');
    }

    /**
     * 获取商户ID
     *
     * @return void
     * @author leolei <346991581@qq.com>
     */
    public function getMerchantId()
    {
        return config('unionpay.merchant_id');
    }
}
