<?php
$GLOBALS['lpconfig']['site_name'] = 'NotOnlyFans';
$GLOBALS['lpconfig']['site_domain'] = 'notonlyfans.vip';

if (getenv('WEB_ALIAS_DOMAIN')) {
    $GLOBALS['lpconfig']['mode'] = 'pro';
} else {
    $GLOBALS['lpconfig']['mode'] = 'dev';
}

// 线上环境配置
if (c('mode') == 'pro') {
    // 本地测试环境配置
    $GLOBALS['lpconfig']['site_base_url'] = 'http://api.notonlyfans.vip/';
    $GLOBALS['lpconfig']['buildeverytime'] = false;
    $GLOBALS['lpconfig']['image_allowed_domain'] = ['notonlyfans.vip','api.notonlyfans.vip'];   // 图片链接可以允许的域名
    $GLOBALS['lpconfig']['contract_address'] = '0xf6351b9af2da7f8613c6763b42feedae6441f309' ;
    $GLOBALS['lpconfig']['web3_network'] = 'https://ropsten.infura.io/v3/d4b5f8a729cc491e97d91f1180030623' ;
    $GLOBALS['lpconfig']['default_avatar_url'] = 'http://notonlyfans.vip/image/avatar.jpg';
} else {
    // 本地测试环境配置
    $GLOBALS['lpconfig']['site_base_url'] = 'http://dd.ftqq.com:8088/';
    $GLOBALS['lpconfig']['mode'] = 'dev';
    $GLOBALS['lpconfig']['buildeverytime'] = true;
    $GLOBALS['lpconfig']['image_allowed_domain'] = ['localhost','dd.ftqq.com'];   // 图片链接可以允许的域名
    $GLOBALS['lpconfig']['contract_address'] = '0xf6351b9af2da7f8613c6763b42feedae6441f309' ;
    $GLOBALS['lpconfig']['web3_network'] = 'https://ropsten.infura.io/v3/d4b5f8a729cc491e97d91f1180030623' ;
    $GLOBALS['lpconfig']['default_avatar_url'] = 'http://dd.ftqq.com:3000/image/avatar.jpg';
}




$GLOBALS['lpconfig']['local_storage_path'] = AROOT . DS . 'storage'  ;



$GLOBALS['lpconfig']['sellers']['1'] = '0xF05949e6d0Ed5148843Ce3f26e0f747095549BB4';

$GLOBALS['lpconfig']['max_group_per_user'] = 100;   // 一个用户可以加入的栏目上限
$GLOBALS['lpconfig']['max_mention_per_comment'] = 5;   // 一条评论最多的可以at人的数量

$GLOBALS['lpconfig']['message_group_per_page'] = 20;   // 单页的消息分组条数
$GLOBALS['lpconfig']['history_per_page'] = 20;   // 单页的消息条数
$GLOBALS['lpconfig']['feeds_per_page'] = 20;   // 单页的内容条数
$GLOBALS['lpconfig']['users_per_page'] = 20;   // 单页的用户数
$GLOBALS['lpconfig']['blacklist_per_page'] = 20;   // 个人黑名单单页的显示用户数（栏目黑名单和memberlist都用 users_per_page）
$GLOBALS['lpconfig']['contribute_per_page'] = 100;   // 单页的投稿条数
$GLOBALS['lpconfig']['comments_per_feed'] = 5;   // 单页内容的评论数量
$GLOBALS['lpconfig']['user_normal_fields'] = ' `id`,`username`,`nickname`,`level`,`avatar`,`group_count`,`feed_count`,`up_count`,`timeline` ';   // 单页的内容条数


$GLOBALS['lpconfig']['forbiden_nicknames'] = ['admin','all','system'];
$GLOBALS['lpconfig']['forbiden_usernames'] = ['admin','all','system'];
