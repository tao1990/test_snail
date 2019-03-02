<?php
ini_set("display_errors", "Off");
error_reporting(0);
header("Access-Control-Allow-Origin:*"); 
header('Access-Control-Allow-Headers:x-requested-with,content-type'); 
header("Content-type: application/json; charset=UTF-8");
//header ( "Content-type: text/html; charset=UTF-8" );
require_once("conn_mysql.php");
require_once("func_assist.php");
require_once("func_comm.php");
require_once("func_order.php");
define("IMG_SITE","http://img.neotv.cn");
define("ENCRY_KEY","snailkey2018");
//aliyun sms
define("SMS_ACCESS_KEY","LTAIMd1LXayHKDA6");
define("SMS_ACCESS_SECRET","3nYeOlhy5EuD7csW6H6PDtr1UxzhQI");
define("SMS_SIGN_NAME","略合科技");
define("SMS_REG_TEMPLATE_CN","SMS_157355780");//国内手机号短信模板id
define("SMS_REG_TEMPLATE_RU","SMS_157355620");//俄罗斯手机号短信模板id
define("SMS_PWD_TEMPLATE_CN","SMS_157350805");//国内手机号短信模板id（忘记密码）
define("SMS_PWD_TEMPLATE_RU","SMS_150744055");//俄罗斯手机号短信模板id（忘记密码）


//zfb
define("ZFB_APPID","2018110862076568");
define("ZFB_APP_PRIVATE_KEY","MIIEowIBAAKCAQEAxWDf507Ww1GPt1qsghuzC5Z0DJ/QNInw6rF6cCjzwZH4U8oZacnrZYJll3w6WA+p7LINWSFd4WMudlPZNXyYxfTUqTZfypuC763Bz77Hu/GD2RPO9PazfakQzO5WvJwON8DFauFRj4esInIMCDv39HDh+5Z/S4qDbe+oC048OPMAO3z+NhPiSFz7crzuhPrO+AcnjbhIwYn6W2EUR3LHrrYheAuP6dzQkR95VjcTW83sg92bJoQ4JnLFEMAvxtYXrYVSyX5WICLlc/IA6wBoItogyrLDV11DV85ZHGkKQHJseOChyPGCC9b9WzvnDzj8mp3mjdxw84p1NzDD2jzTzwIDAQABAoIBAQCgciFQjDv0RibHa4Pzt8SR1Nm9MWQ0cTVP8rmO1xte7OOqaQzDfApIV/lxbOCYmRMOf6ZuH7uK7e7k7UASRJiDwoPkkXjI4CVN5Dc6QuFmG+uL2JhRdQFvUrF/hPcpFspP8/oG6eY9AJKi5YZ2YxkqsWBh/XK4233/LeOyXQSDXUftO6f6+EbMM4eGG4HHhM9cyKuyFuFu3rJjVmmV1LR2H1Tc2oZIg1VA6mkHcBlvbjhkesDmPSiEM8XHh0l+AcfC4Nsu4gburK4BAA/vOlHUU2RC9sCUsLKGH84uBEIqJSAXe7S6CWkucQD8cSjylwT945Zm7MYmdG1w76HbQ3VxAoGBAOaoCufbiqI90ijsRLQX9sBqJROq9hbpt4p9G8OEK9L6nlXIW9IG9WCn4mJYn0yJNNBxxQVLBKjp9NGXAqd+Up1CxCwfS3ptTsLPiXsXWw2ich6BjTpCNu4VzUoMU/+L+sKo0ZObt2wZwUNN8v181tSjDhegi1agcvmfYQj4tcx7AoGBANsQx9terVbkotm0ycj8ZRn0V7Dmz7+Bf4dz5VHHxglhuIA7Wh1YKAWp5+G1q6+RKRkPJk/7oGVHcmFAkiKbA+gv6jUmP2aqcL0ARs3Qcb+mqRvjDCCC6EVeDmC3U0oV5PllXKQ4U5saPMmQ81nw8+n0WVfWpi5d1+OrlciIkIe9AoGAFJT6JCZbAI8zqaMrnkZlJlZSaKbgvrqsPhdb2t54aqMibdUrHFqymqVgdhYiYNn9dHwycH55M/lsdydafUewZ4gFqUpBmfMXDBso0WsMHPNZ647z4zb1X9liMDFZbXw4LBaUXO+QNn012aOjAyuYn/DM2R7iUCCRIeUu63YGAvECgYAW0fGv1XYDJkAco6udh0VU5dI4uGKN1YSebKFH2qKzmX1pxkUF54gLxv5D0fP3jwyT8rMQA+tagY3Vua8/cZx/lHY2YSybmkeyOJQnnn4q88aMBCPgQyLFDx46Tv6bKhq3LCrxZHi5IIuSh8oB5YrTJUQlVVPsYpQ5wEcTI77ClQKBgAH0XsIWgTalBVIDPDuEYQomm/b0tmIBK/YsH1jgcguhJZrGQD8WV4NfL29IFNU0KkhXTA5dVjhlALNUbROYELxJ64u3lS5uXYtY0S0CYieiq5JtSKE2zVC/lgW6/CatxuN8+ZmlScrBO215XGee+p+3dBv1ea5SBPTQPN2CnUgn");
define("ZFB_PUBLIC_KEY","MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA2lggOh2XIpcEjKl7Ya6UCQPDtTA1qgMgRSW5sDShHK7KDxWImG7kFd3ny7O6xvz6FD1zJCshMNK5kuvu8YacPQbZuFNI3kDMB/hfmULaO7wPsXan6eiXerzp5/sy4BqG0GU53andoA+50fHq9IM4Pi4Le4frLm77Xspuqq91g4V3hjJ7PgJzcWICPMsB1vgDIkt2wpJjaex/Itf8e9/OtuFhLWr7KmG9ObH03+Cb6QAAa9QrztHIMWR88NS5U3/uPDyDqvIH2+AwV8oynZI9vSlirO5OHXWp1cOXZxEbxGPIvGOAXl5eTDrkuiAtNfmlcTb5uhj7R/WNpnpgaf/diQIDAQAB");
//define("NOTIFY_ZFB","http://58.247.87.162:4003/app/pay/zfb_cb.php");
define("NOTIFY_ZFB","http://128.1.43.8/app/pay/zfb_cb.php");

//wx
//define("NOTIFY_WX","http://58.247.87.162:4003/app/pay/wx_cb.php");
define("NOTIFY_WX","http://128.1.43.8/app/pay/wx_cb.php");
define("WX_APPID","wx4c7fc3bd787e6967");
define("WX_MCHID","1520854271");
define("WX_API_KEY","d5ac70e307c46da82a25a8b9c22601fc");
//bonus
define("SEND_BONUS_IDS","1,2,3,4,5");

//order
define("DEFAULT_TERM",30);//广告默认展示期限

//价格
define("PRICE_100",0.01);
define("PRICE_200",0.01);
define("PRICE_TEST",0.01);



?>
