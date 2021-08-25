<?php
if( !isset($argv)  ) die('Please run it via commandline');

// build 
/****  load lp framework  ***/
define( 'DS' , DIRECTORY_SEPARATOR );
define( 'AROOT' , dirname( __FILE__ ) . DS  );

// 定义常用跟路径
define( 'FROOT' , dirname( __FILE__ ) . DS . '_lp' . DS );

// 载入composer autoload
require AROOT . 'vendor' . DS . 'autoload.php';

require_once FROOT . 'lib' . DS . 'functions.php'; // 公用函数
require_once AROOT . 'lib' . DS . 'functions.php'; // 应用函数
require_once FROOT . 'config' . DS . 'core.php'; // 核心配置
require_once AROOT . 'config' . DS . 'database.php'; // 数据库配置
require_once AROOT . 'config' . DS . 'app.php'; // 应用配置

build_route_file();

// build doc
date_default_timezone_set('Asia/Chongqing');

use Lazyphp\Doc\Builder;
use Crada\Apidoc\Exception;

if($classes = get_declared_classes())
{
    $ret = array();
    foreach( $classes as $class )
    {
        if( end_with($class , 'Controller') )
        {
           $controll_classes[] = $class; 
        }

    }
}

if( isset( $controll_classes ) )
{
    $output_dir = AROOT.'docs';
    $output_file = 'index.html'; // defaults to index.html

    try {
        $builder = new Builder($controll_classes, $output_dir, $output_file);
        $builder->generate();
    } catch (Exception $e) {
        echo 'There was an error generating the documentation: ', $e->getMessage();
    }
}


