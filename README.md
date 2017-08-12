# thinkphp 5 聚合支付通道

集成银联App,Wap方式支付，通联H5方式支付,后期有时间继续开发

参考[Unionpay & laravel & 银联支付](https://github.com/hyperbolaa/Unionpay)改造完成

## 配置说明
>ThinkPHP5格式用法
```php
<?php

return [
    'unionpay' =>[
        'merchant_id'=>'',//商户号
        'cer_pwd'=>'',//私钥密码
        'cer_path'=>'',//私钥路径
        'cer_dir'=>'',//公钥路径
        'sanbox'=>0//测试模式 0 关闭 1 开启
    ]
    'allinpay' =>[
        'mer_id'=>'',//商户号
        'sign'=>'',//私钥密码
        'sanbox'=>0//测试模式 0 关闭 1 开启
    ]
];
```


