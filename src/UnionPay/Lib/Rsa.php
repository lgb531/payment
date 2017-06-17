<?php

namespace leolei\Unionpay\Lib;

/**
 * 签名辅助类
 * Class Rsa
 * @package leolei\Unionpay\Lib
 */
class Rsa
{
    //获取证书ID
    public static function getCertId($certPath, $password)
    {
        $data = file_get_contents($certPath);
        openssl_pkcs12_read($data, $certs, $password);
        $x509data = $certs ['cert'];
        openssl_x509_read($x509data);
        $certData = openssl_x509_parse($x509data);
        return $certData['serialNumber'];
    }

    /**
     * 获取密码加密证书ID
     *
     * @param string $encryptPath
     * @return void
     * @author leolei <346991581@qq.com>
     */
    public static function getEncryptCertId($encryptPath)
    {
        $x509data = file_get_contents($encryptPath);
        openssl_x509_read ( $x509data );
        $certdata = openssl_x509_parse ( $x509data );
        return $certdata['serialNumber'];
    }

    //RSA签名
    public static function getParamsSignatureWithRSA($params, $certPath, $password)
    {
        $query = self::getStringToSign($params);

        $params_sha1x16 = sha1($query, false);
        $privateKey     = self::getPrivateKey($certPath, $password);
        openssl_sign($params_sha1x16, $signature, $privateKey, OPENSSL_ALGO_SHA1);

        return base64_encode($signature);
    }

    //MD5签名
    public static function getParamsSignatureWithMD5($params, $secret)
    {
        $query = self::getStringToSign($params);

        $signature = md5($query . '&' . md5($secret));

        return $signature;
    }

    //获取私钥
    protected static function getPrivateKey($certPath, $password)
    {
        $data = file_get_contents($certPath);
        openssl_pkcs12_read($data, $certs, $password);
        return $certs['pkey'];
    }

    //验签
    public static function verify($params, $certDir)
    {
        $publicKey        = self::getPublicKeyByCertId($params['certId'], $certDir);
        $requestSignature = $params ['signature'];
        unset($params['signature']);

        ksort($params);
        $query = http_build_query($params);
        $query = urldecode($query);

        $signature     = base64_decode($requestSignature);
        $paramsSha1x16 = sha1($query, false);
        $isSuccess     = openssl_verify($paramsSha1x16, $signature, $publicKey, OPENSSL_ALGO_SHA1);

        return (bool)$isSuccess;
    }

    // //通过证书ID获取公钥
    public static function getPublicKeyByCertId($certId, $certDir)
    {
        $handle = opendir($certDir);
        if ($handle) {
            while ($file = readdir($handle)) {
                //clearstatcache();
                $filePath = rtrim($certDir, '/\\') . '/' . $file;
                if (is_file($filePath) && self::endsWith($filePath, '.cer')) {
                    // if (self::getCertIdByCerPath($filePath) == $certId) {
                    //     closedir($handle);
                    //     return file_get_contents($filePath);
                    // }
                    closedir($handle);
                    return file_get_contents($filePath);
                }
            }
            throw new \Exception(sprintf('Can not find certId in certDir %s', $certDir));
        } else {
            throw new \Exception('certDir is not exists');
        }
    }

    //文件判断
    public static function endsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle === substr($haystack, -strlen($needle))) {
                return true;
            }
        }

        return false;
    }

    //通过证书路径获取证书ID
    protected static function getCertIdByCerPath($certPath)
    {
        $x509data = file_get_contents($certPath);
        openssl_x509_read($x509data);
        $certData = openssl_x509_parse($x509data);

        return $certData ['serialNumber'];
    }

    //发送请求
    public static function sendHttpRequest($url, $params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Content-type:application/x-www-form-urlencoded;charset=UTF-8'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    //过滤无效的参数
    public static function filterData($data)
    {
        $data = array_filter(
            $data,
            function ($v) {
                return $v !== '';
            }
        );

        return $data;
    }


    /**
     * 参数排列
     */
    public static function getStringToSign($params)
    {
        ksort($params);
        $query = http_build_query($params);
        $query = urldecode($query);

        return $query;
    }

    /**
     * wap 跳转支付
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

    /**
     *  数据化的结果
     */
    public static function post($url, $params)
    {
        $result = self::sendHttpRequest($url, $params);
        parse_str($result, $res_arr);

        if (is_array($res_arr) && isset($res_arr['respCode'])) {
            return $res_arr;
        }
        return [];
    }
    
    /**
     * map转换string，按新规范加密
     *
     * @param array $customerInfo
     * @return void
     * @author leolei <346991581@qq.com>
     */
    public static function getCustomerInfoWithEncrypt($customerInfo, $encryptPath)
    {
        if ($customerInfo == null || count($customerInfo) == 0) {
            return "";
        }
        $encryptedInfo = [];
        foreach ($customerInfo as $key => $value) {
            if ($key == 'phoneNo' || $key == 'cvn2' || $key == 'expired') {
            //if ($key == 'phoneNo' || $key == 'cvn2' || $key == 'expired' || $key == 'certifTp' || $key == 'certifId') {
                $encryptedInfo [$key] = $customerInfo [$key];
                unset ( $customerInfo [$key] );
            }
        }
        if (count ($encryptedInfo) > 0) {
            $encryptedInfo = self::createLinkString ( $encryptedInfo, false, false );
            $encryptedInfo = self::encryptData ( $encryptedInfo, $encryptPath );
            $customerInfo ['encryptedInfo'] = $encryptedInfo;
        }
        return base64_encode ( "{" . self::createLinkString ( $customerInfo, false, false ) . "}" );
    }
    
    /**
     * 加密数据
     *
     * @param string $data 待加密字符串
     * @param string $encryptPath 加密证书路径
     * @return void
     * @author leolei <346991581@qq.com>
     */
    static function encryptData($data, $encryptPath)
    {
        $x509data = file_get_contents($encryptPath);
        openssl_x509_read($x509data);
        $public_key = $x509data;
        openssl_public_encrypt($data,$crypted,$public_key);
        return base64_encode($crypted);
    }

    /**
     * 将数组转换为string
     *
     * @param $para 数组
     * @param $sort 是否需要排序
     * @param $encode 是否需要URL编码
     * @return string
     */
    public static function createLinkString($para, $sort, $encode)
    {
        if ($para == null || !is_array($para)) {
            return "";
        }
    
        $linkString = "";
        if ($sort) {
            $para = argSort ( $para );
        }
        while (list ( $key, $value ) = each ( $para )) {
            if ($encode) {
                $value = urlencode ( $value );
            }
            $linkString .= $key . "=" . $value . "&";
        }
        // 去掉最后一个&字符
        $linkString = substr ( $linkString, 0, count ( $linkString ) - 2 );
    
        return $linkString;
    }
}
