<?php

// 检查并补全必须的常量
if( !defined('AROOT') ) die('NO AROOT!');
if( !defined('DS') ) define( 'DS' , DIRECTORY_SEPARATOR );

// 定义常用跟路径
define( 'FROOT' , dirname( __FILE__ ) . DS );

// 设置时区
@date_default_timezone_set('Asia/Chongqing');
setlocale(LC_ALL,'C.UTF-8');

// 载入composer autoload
require AROOT . 'vendor' . DS . 'autoload.php';


// 初始化容器对象
try
{
    if( file_exists( AROOT . 'ONLINE.MARK' ) ) $GLOBALS['lpconfig']['mode'] = 'pro';
    
    require_once FROOT . 'lib' . DS . 'functions.php'; // 公用函数
    require_once FROOT . 'config' . DS . 'core.php'; // 核心配置
    require_once AROOT . 'config' . DS . 'database.php'; // 数据库配置
    require_once AROOT . 'lib' . DS . 'functions.php'; // 公用函数
    require_once AROOT . 'config' . DS . 'app.php'; // 应用配置
    require_once AROOT . 'lib' . DS . 'functions.php'; // 公用函数

    if( is_devmode() )
    {
        ini_set('display_errors',true);
        error_reporting(E_ALL);
    }

    $force_build = (!on_sae()) && is_devmode() && c('buildeverytime') ;
    load_route_file( $force_build );


}catch( PDOException $e )
{
    $error = [];
    $error['message'] = $e->getMessage();
    $error['code'] = 30001;
    $error['info'] = $error['message'];
    $error['args'] = null;
    send_json($error);
}
catch(\Lazyphp\Core\LianmiException $e)
{
    $error = [];
    $error['message'] = $e->getMessage();
    $error['code'] = $e->getCode();
    $error['info'] = $e->getInfo();
    $error['args'] = $e->getArgs();
    
    send_json($error);
}
catch(\Lazyphp\Core\RestException $e)
{
    $class_array = explode( '\\' , get_class( $e ) );
    $class = t(end( $class_array ));
    $prefix = strtoupper(rremove( $class , 'Exception' ));
    
    $error = get_error( $prefix ); 
    $error['message'] = $error['message']  . '- ' .$e->getMessage();
    send_json($error);

}
catch(\Exception $e)
{
    // alway send json format
    $class_array = explode( '\\' , get_class( $e ) );
    $class = t(end( $class_array ));
    $prefix = strtoupper(rremove( $class , 'Exception' ));

    $error = get_error( $prefix );
    $error['message'] = $error['message']  . '- ' .$e->getMessage();
    send_json($error);
}
