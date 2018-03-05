<?php
/**
 * 配置文件
 */
//return array(
    //    'DB_TYPE' => 'mysql',
    //    'DB_HOST' => '192.168.1.252',
    //    'DB_NAME' => 'tongrenmiao',
    //    'DB_USER' => 'root',
    //    'DB_PWD' => 'root',
    //    'DB_PORT' => '3306',
    //    'DB_PREFIX' => 'cmf_',
    //    //密钥
    //    "AUTHCODE" => 'cbgB2Y89jlUqA1m9a2',
    //    //cookies
    //    "COOKIE_PREFIX" => '8h59iQ_',
//    'DB_TYPE' => 'mysql',
//    'DB_HOST' => 'localhost',
//    'DB_NAME' => 'tongrenmiao',
//    'DB_USER' => 'root',
//    'DB_PWD' => '',
//    'DB_PORT' => '3306',
//    'DB_PREFIX' => 'cmf_',
//    //密钥
//    "AUTHCODE" => 'cbgB2Y89jlUqA1m9a2',
//    //cookies
//    "COOKIE_PREFIX" => '8h59iQ_',
//);
return array(
    //闲情数据库连接
    'DB_TYPE' => 'mysql',
    'DB_HOST' => 'localhost',
    'DB_NAME' => 'tongrenmiao',
    'DB_USER' => 'root',
    'DB_PWD' => '',
    'DB_PORT' => '3306',
    'DB_PREFIX' => 'cmf_',
    //密钥
    "AUTHCODE" => 'cbgB2Y89jlUqA1m9a2',
    //cookies
    "COOKIE_PREFIX" => '8h59iQ_',
    
    //商城数据库链接,
    'DB_MALLBUILDER'=>array(
        'DB_TYPE' => 'mysql',
        'DB_HOST' => 'localhost',
        'DB_NAME' => 'mallbuilder',
        'DB_USER' => 'root',
        'DB_PWD' => '',
        'DB_PORT' => '3306',
        'DB_PREFIX' => 'mallbuilder_',
        //密钥
        "AUTHCODE" => 'cbgB2Y89jlUqA1m9a2',
        //cookies
        "COOKIE_PREFIX" => '8h59iQ_',
    )
);