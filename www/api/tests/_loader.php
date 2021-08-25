<?php

// 检查并补全必须的常量
define( 'DS' , DIRECTORY_SEPARATOR );
define( 'AROOT' , __DIR__ . DS . '..' . DS  );

// 定义常用跟路径
define( 'FROOT' , AROOT . '_lp' . DS );

// 设置时区
@date_default_timezone_set('Asia/Chongqing');


// 载入composer autoload
require AROOT . 'vendor' . DS . 'autoload.php';


require_once FROOT . 'lib' . DS . 'functions.php'; // 公用函数
require_once FROOT . 'config' . DS . 'core.php'; // 核心配置
require_once AROOT . 'config' . DS . 'database.php'; // 数据库配置
require_once AROOT . 'lib' . DS . 'functions.php'; // 公用函数
require_once AROOT . 'config' . DS . 'app.php'; // 应用配置
require_once AROOT . 'lib' . DS . 'functions.php'; // 公用函数