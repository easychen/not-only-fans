<?php
use \Lazyphp\Core\Database as Database ;
use \Lazyphp\Core\Datameta as Datameta ;

require_once FROOT . 'lib' . DS . 'flight' . DS . 'net' . DS . 'Response.php';
use \flight\net\Response as response;



// == 日志函数 ==========================
function dlog($log,$type='log',$css='')
{
    $log_file = AROOT . 'compiled'.DS .'log.txt' ;
    if( is_array($log) ) $log = print_r( $log , true );
    if( is_writable( $log_file ) )
        file_put_contents( $log_file, $log . '@'.time() . PHP_EOL , FILE_APPEND );
    elseif( on_sae() )
    {
        sae_debug( $log );
    }

}




// == 状态相关函数 ==========================
function is_devmode(){ return c('mode') == 'dev'; }
function on_sae(){ return defined('SAE_APPNAME'); }

// == 迅捷函数 ==========================
function t( $str ){ return trim($str); }
function u( $str ){ return urlencode($str); }
function i( $str ){ return intval($str); }
function z( $str ){ return strip_tags($str); }
function v( $str )
{
    if(isset($_POST[$str]))
    {
        return $_POST[$str];
    }
    elseif(isset($_GET[$str]))
    {
        return $_GET[$str];
    }
    elseif(isset($_REQUEST[$str]))
    {
        return $_REQUEST[$str];
    }
    else
    {
        return false;
    }
}
function g( $str ){ return isset( $GLOBALS[$str] ) ? $GLOBALS[$str] : false; }
function ne( $str ){ return strlen($str) > 0 ; }
function nz( $int ){ return intval($int) > 0 ; }
// 读取配置文件，支持二维数组（通过subkey
function c( $key , $subkey = null )
{
    if( isset( $GLOBALS['lpconfig'][$key] ) )
    {
        if( $subkey != null && is_array( $GLOBALS['lpconfig'][$key] ) )
            return $GLOBALS['lpconfig'][$key][$subkey];
        else
            return $GLOBALS['lpconfig'][$key];
    }
    else return false;

}
// == 数据库相关函数 ==========================
function s( $str ){ return trim(db()->quote($str),"'"); }
function db()
{
    if( !isset( $GLOBALS['_LP4_DB'] ) )
        $GLOBALS['_LP4_DB'] = new Database();

    return  $GLOBALS['_LP4_DB'];
}

function get_data( $sql )
{
    return db()->getData($sql)->toArray();
}

function get_line( $sql )
{
    return db()->getData($sql)->toLine();
}

function get_var( $sql )
{
    return db()->getData($sql)->toVar();
}

function run_sql( $sql )
{
    return db()->runSql( $sql );
}

function get_bind_params( $sql )
{
    $reg = '/:([a-z_][0-9a-z_]*)/is';
    if( preg_match_all($reg, $sql, $out) )
    {
        return $out[1];
    }

    return false;
}

function type2pdo( $type )
{
    if( $type == 'int' )
    {
        return PDO::PARAM_INT;
    }
    else return PDO::PARAM_STR;
}

function load_data_from_file( $file , $pdo )
{
    $sql_contents = preg_replace( "/(#.+[\r|\n]*)/" , '' , file_get_contents( $file ));
    $sqls = split_sql_file( $sql_contents );

    foreach ($sqls as $sql)
        $pdo->exec( $sql );
}

function split_sql_file($sql, $delimiter = ';')
{
    $sql               = trim($sql);
    $char              = '';
    $last_char         = '';
    $ret               = array();
    $string_start      = '';
    $in_string         = FALSE;
    $escaped_backslash = FALSE;

    for ($i = 0; $i < strlen($sql); ++$i) {
            $char = $sql[$i];

            // if delimiter found, add the parsed part to the returned array
            if ($char == $delimiter && !$in_string) {
                    $ret[]     = substr($sql, 0, $i);
                    $sql       = substr($sql, $i + 1);
                    $i         = 0;
                    $last_char = '';
            }

            if ($in_string) {
                    // We are in a string, first check for escaped backslashes
                    if ($char == '\\') {
                            if ($last_char != '\\') {
                                    $escaped_backslash = FALSE;
                            } else {
                                    $escaped_backslash = !$escaped_backslash;
                            }
                    }
                    // then check for not escaped end of strings except for
                    // backquotes than cannot be escaped
                    if (($char == $string_start)
                            && ($char == '`' || !(($last_char == '\\') && !$escaped_backslash))) {
                            $in_string    = FALSE;
                            $string_start = '';
                    }
            } else {
                    // we are not in a string, check for start of strings
                    if (($char == '"') || ($char == '\'') || ($char == '`')) {
                            $in_string    = TRUE;
                            $string_start = $char;
                    }
            }
            $last_char = $char;
    } // end for

    // add any rest to the returned array
    if (!empty($sql)) {
            $ret[] = $sql;
    }
    return $ret;
}

// == 元数据构建相关函数 ==========================

function load_route_file( $force = false )
{
    if( !file_exists(c('route_file')) || true == $force  )
        build_route_file();

    if(!require c('route_file')) throw new \Exception("Build route file fail");
}

function build_route_file( $return = false )
{
    $meta = array();
    if($cfiles = glob( AROOT . 'controller' . DS . '*Controller.php' ))
        foreach( $cfiles as $cfile )
            require_once $cfile;

    if($classes = get_declared_classes())
    {
        $ret = array();
        foreach( $classes as $class )
        {
            if( end_with($class , 'Controller') )
            {
                // 开始遍历
                $ref = new \ReflectionClass($class);
                if($methods = $ref->getMethods())
                    foreach( $methods as $method )
                    {
                        $item = array();
                        if( $item['meta'] = format_meta(parse_comment( $method->getDocComment() )) )
                        {
                            $item['class'] = $class;
                            $item['method'] = $method->name;
                            $item['meta']['binding'] = get_param_info($method->getParameters());

                            if( isset( $item['meta']['LazyRoute'][0]['route'] ) )
                                $item['meta']['route'][] = array(  'uri' => $item['meta']['LazyRoute'][0]['route'] , 'params' => get_route_params($item['meta']['LazyRoute'][0]['route']));



                            $ret[] = $item;
                        }
                    }
            }

        }

        if( count($ret) > 0 )
        {
            foreach( $ret as $method_info )
            {
                // 只处理标记了路由的方法
                if( isset( $method_info['meta']['route'] ) )
                {
                    //print_r( $method_info['meta']['route'] );
                    $key = cmkey( $method_info );
                    //echo "{$method_info['class']} , {$method_info['method']} = $key (build) \r\n";
                    $meta[$key] = $method_info['meta'];
                    // 生成路由部分代码


                    foreach( $method_info['meta']['route'] as $route )
                    {
                        $source[] = '$app->'."route('" . t($route['uri']) . "',array( '" . $method_info['class'] . "','" . $method_info['method'] . "'));";
                    }
                }
            }
        }
    }

    $GLOBALS['meta'] = $meta;

    if( isset( $source ) && is_array($source) && count($source) > 0 )
    {
        $source_code = build_source_code( $source , $meta );

        if( $return ) return $source_code;
        else save_route_file( $source_code );
    }

}

function save_route_file($source_code)
{
    $dir = dirname(c('route_file'));
    if( !file_exists( $dir ) ) mkdir( $dir , 0777 );
    if( !is_writable( $dir ) )
    {
        throw new \Exception( "compiled dir not writable" );
        return false;
    }

    if( !file_put_contents(c('route_file'),$source_code) )
    {
        throw new \Exception( "Build route file fail" );
        return false;
    }


    return true;
}

function get_param_info( $params )
{
    if( !$params ) return false;
    $ret = array();
    foreach( $params as $param )
    {
        $info = array();
        $info['name'] = $param->getName();
        if( $param->isDefaultValueAvailable() ) $info['default'] = $param->getDefaultValue();
        $ret[$info['name']] = $info;
    }

    return $ret;
}

function build_source_code( $source , $meta = null )
{
    // header
    $content = '<' . '?php ' . "\r\n" ;
    $content .= 'namespace Lazyphp\Core {' . "\r\n" ;
    // exception
    $content .= build_exception_code();
    $content .= '}' . "\r\n" ;

    $content .= 'namespace{' . "\r\n" ;

    // meta
    if( is_array($meta) )
        $content .= '$GLOBALS[\'meta\'] = ' . var_export( $meta , true ) .';'. "\r\n";

    // route and exec
    $content .= '$app = new \Lazyphp\Core\Application();' . "\r\n" ;
    $content .= join( "\r\n" , $source )."\r\n" ;
    $content .= '$app->run();'. "\r\n";
    $content .= '}' . "\r\n" ;

    return $content;
}

function build_exception_code()
{
    if( !isset($GLOBALS['rest_errors'])
        || !is_array($GLOBALS['rest_errors'])
        || count($GLOBALS['rest_errors']) < 1 )
        return "";
    else
    {
        foreach( $GLOBALS['rest_errors'] as $key => $value )
            $excode[] = 'Class '
                        . ucfirst(strtolower($key))
                        .'Exception extends \Lazyphp\Core\RestException {}';

        if( isset( $excode ) && is_array( $excode ) )
        {
            $code = 'Class RestException extends \Exception {}'."\r\n";
            $code .=  join( "\r\n" , $excode )."\r\n";
            return $code;

        }

    }

}

function cmkey( $array , $method = null )
{
    if( is_array( $array ) && isset( $array['class'] ) && isset( $array['method'] ) )
        return md5( $array['class'] . '-' .$array['method'] );
    else
        return md5( $array . '-' . $method );
}

function format_meta( $meta )
{
    if( !is_array( $meta ) ) return false;
    foreach( $meta as $key => $value )
    {
        if( function_exists( $func = 'meta_format_'.strtolower($key) ) && is_array( $value ) && count($value) > 0 )
            $value = array_map( $func , $value );

        $ret[$key] = $value;
    }

    return isset($ret)?$ret:false;

}

function meta_format_params( $value )
{
    if( isset($value['check'] ))
    {
        $ret['name'] = $value['name'];
        $func_string = $value['check'];
        $ret['filters'] = array_map( "trim" , explode('|',$func_string ) );
    }
    else
    {
        $ret['name'] = $value['name'];
        $ret['filters'] = array( 'donothing' );
    }

    if( isset($value['cnname']) ) $ret['cnname'] = $value['cnname'];

    return $ret;

}

function meta_format_lazyroute( $value )
{
    //print_r($value);

    return array
    (
        'route' => $value['method'] . ' ' . $value['uri'],
        'ApiMethod' => '(type="'.$value['method'].'")',
        'ApiRoute' => '(name="' . str2api( $value['uri'] ) . '")'
    );
}

function str2api( $str )
{
  // TODO
  // 需要一个更好的正则

  $str = str_replace( '(' , '' , $str);
  $str = str_replace( ')' , '' , $str);
  $str = preg_replace( '/(:(.+?))\//' , '/' , $str);
  $str = preg_replace( '/(:(.+?))$/' , '' , $str);
  $str = preg_replace( '/@(.+?)\//is' , '{$1}/' , $str);
  $str = preg_replace( '/@(.+?)$/is' , '{$1}' , $str);
  return $str;
  /*
  $reg = '/([a-zA-Z_-0-9]+?)/is';
  if( preg_match_all($reg, $str, $out) )
    {
        return $out;
    }
  */
}

function get_route_params( $route )
{
    $reg = '/@([a-z_][0-9a-z_]*)/is';
    if( preg_match_all($reg, $route, $out) )
    {
        return $out[1];
    }
    return false;
}


/*
function meta_format_table( $value )
{
    $table = t(reset($value));
    $pdo = new PDO(c('database_dev','dsn'),c('database_dev','user'),c('database_dev','password')) ;
    $datameta = new Datameta( $pdo );
    $ret['fields'] = $datameta ->getTableCols($table);
    $ret['names'] = $datameta->getFields($table);

    return isset($ret)?$ret:false;
}

function meta_format_route( $value )
{
    $ret['uri'] = join( " " , $value );
    $ret['params'] = get_route_params($ret['uri']);

    return $ret;
}

function get_route_params( $route )
{
    $reg = '/@([a-z_][0-9a-z_]*)/is';
    if( preg_match_all($reg, $route, $out) )
    {
        return $out[1];
    }
    return false;
}

function meta_format_auto_type_check( $value )
{
    return join( "" , $value );
}

function meta_format_field_check( $value )
{
    if($value){
        if( strpos($value[0],':') !== false )
        {
            $tinfo = explode(':',t($value[0]));
            $ret['name'] = array_shift( $tinfo );
            $func_string = array_shift( $tinfo );
            $ret['filters'] = array_map( "trim" , explode('|',$func_string ) );
        }
        else
        {
            $ret['name'] = $value[0];
            $ret['filters'] = array( 'donothing' );
        }

        if( isset($value[1]) ) $ret['cnname'] = $value[1];

        return $ret;
    }else
    {
        return $value;
    }



}

function get_auto_check_filters( $field , $check_null = false )
{
    switch ( $field['type'] )
    {
        case 'int':
            $ret[] = 'i';
            break;

        case 'bigint':
            $ret[] = 'wintval';
            break;

        default:
            $ret[] = c('default_string_filter_func');
    }

    if( $check_null && $field['notnull'] ) $ret[] = 'check_not_empty';

    return $ret;
}

function get_filters_by_type( $type )
{
    if( $type == 'int' ) return 'i';
    if( $type == 'bigint' ) return 'wintval';
    return c('default_string_filter_func');
}

function cnname( $name )
{
    if( isset( $GLOBALS['meta_key']) && isset( $GLOBALS['meta'] ) )
    {
        if( isset( $GLOBALS['meta'][$GLOBALS['meta_key']]['table'][0]['fields'][$name] ) )
        {
            $cnname = $GLOBALS['meta'][$GLOBALS['meta_key']]['table'][0]['fields'][$name]['comment'];
            return ne($cnname)?$cnname:$name;
        }
    }
    return $name;
}
*/

function array_key_index( $key , $array )
{
    $i = 0 ;
    foreach( $array as $k => $v )
    {
        if( $k == $key ) return $i;
        else $i++;
    }
}



/*

function meta_format_input_field( $value )
{
    $ret = array( 'name' => ltrim($value[0],'$') , 'type' => $value[1] , 'cnname' => trim($value[2],'"')  );

    if( strpos($value[1],':') !== false )
    {
        $tinfo = explode(':',t($value[1]));
        $ret['type'] = array_shift( $tinfo );
        $func_string = array_shift( $tinfo );
        $ret['filters'] = array_map( "trim" , explode('|',$func_string ) );
    }

    return $ret;


}

function meta_format_return_field($value)
{
    return array( 'name' => ltrim($value[0],'$') , 'type' => $value[1]  );
}

function meta_format_param($value)
{
    return array( 'name' => ltrim($value[0],'$') , 'type' => $value[1]  );
}
*/

/*
function filter_intval( $string )
{
    $func = end( explode('_',__FUNCTION__));

    if( function_exists($func) )
        return call_user_func( $func , $string );
}
*/

function str2value( $str , $tolower = 1 )
{
    $arr=array();
    preg_replace_callback('/(\w+)="(.*?)"/',function($m) use(&$arr,$tolower){
            $key=$tolower?strtolower($m[1]):$m[1];
            $arr[$key]=$m[2];
     },$str);
    return $arr;
}


function parse_comment( $comment )
{
    $comment = str_replace(PHP_EOL, "\n", $comment);
    $ret = false;

    $reg = '/@Api(.+?)\((.+?)\)$/im';
    if( preg_match_all($reg,$comment,$out) )
    {
        $ret = array() ; $i = 0 ;

        while( isset( $out[1][$i] ) )
        {
            $ret[$out[1][$i]][] = str2value( $out[2][$i] );

            $i++;

        }
    }
    return $ret;
}

// 支持双引号内部空格的参数explode
// by @luofei614
function explode_quote( $str )
{
    $reg = '/"[^"]+"|[^\s]+/i';

    if( preg_match_all($reg,$str,$out) )
        return $out[0];
    else
        return $str;

}

function get_source_code( $class , $method )
{
    $ref_class = new \ReflectionClass($class);
    $ref_method = $ref_class->getMethod( $method );
    $filename = $ref_class->getFileName();
    $start_line = $ref_method->getStartLine() + 1; // it's actually - 1, otherwise you wont get the function() block
    $end_line = $ref_method->getEndLine()-1;
    $length = $end_line - $start_line;

    $source = file($filename);
    return $body = join(PHP_EOL, array_map( "trim" , array_slice($source, $start_line, $length) ));
    //return str_replace( '{__THIS__CLASS__}' , $class , $body );
}
// == 格式检查相关函数 ==========================

function check_email( $string )
{
    return filter_var($string, FILTER_VALIDATE_EMAIL);
}

function check_int( $string )
{
    return is_numeric($string);
}

function check_uint( $string )
{
    if( (string)$string === '0' ) return true;
    else return  !!preg_match( '/^[1-9][0-9]*$/' , $string , $out );
}

function check_not_empty( $string )
{
    return ne($string);
}

function check_not_zero( $int )
{
    return nz($int);
}


function donothing( $string ){ return $string; }

// == 字符串Helper函数 ==========================
function first( $array )
{
    return is_array($array)?reset($array):false;
}

function last( $array )
{
    return is_array($array)?end($array):false;
}

function end_with( $str , $find )
{
    $end = mb_substr( $str , 0-mb_strlen( $find  , 'UTF-8' ) );
    return $end == $find;

}

function begin_with( $str , $find )
{
    return mb_strpos( $str , $find , 0 , 'UTF-8' ) === 0 ;
}

function wintval( $string )
{
    $array = str_split( $string );
    $ret = '';
    foreach( $array as $v )
    {
        if( is_numeric( $v ) ) $ret .= intval( $v );
    }

    return $ret;
}

function rremove( $string , $remove )
{
    $len = mb_strlen($remove,'UTF-8');
    if( mb_substr( $string , -$len , $len , 'UTF-8' ) == $remove )
    {
        return mb_substr( $string , 0 , mb_strlen( $string , 'UTF-8' ) - $len , 'UTF-8' );
    }
}

function hclean( $string )
{
    $string = strip_tags($string,'<p><a><b><i><blockquote><h1><h2><ol><ul><li><img><div><br><pre><strike>');
    $string = checkhtml( $string );
    $string = tidytag( $string );

    return $string;
}

function tidytag( $content )
{
    $reg = '/(\.=["\'].+?["\'])/is';
    return preg_replace( $reg , '' ,  $content );
}

function checkhtml($html)
{
    preg_match_all("/\<([^\<]+)\>/is", $html, $ms);

    $searchs[] = '<';
    $replaces[] = '&lt;';
    $searchs[] = '>';
    $replaces[] = '&gt;';

    if($ms[1]) {
        $allowtags = 'img|a|font|div|table|tbody|caption|tr|td|th|br|p|b|strong|i|u|em|span|ol|ul|li|blockquote|h1|h2|pre|strike';
        $ms[1] = array_unique($ms[1]);
        foreach ($ms[1] as $value) {
            $searchs[] = "&lt;".$value."&gt;";

            $value = str_replace('&amp;', '_uch_tmp_str_', $value);
            $value = dhtmlspecialchars($value);
            $value = str_replace('_uch_tmp_str_', '&amp;', $value);

            $value = str_replace(array('\\','/*'), array('.','/.'), $value);
            $skipkeys = array('onabort','onactivate','onafterprint','onafterupdate','onbeforeactivate','onbeforecopy','onbeforecut','onbeforedeactivate',
                    'onbeforeeditfocus','onbeforepaste','onbeforeprint','onbeforeunload','onbeforeupdate','onblur','onbounce','oncellchange','onchange',
                    'onclick','oncontextmenu','oncontrolselect','oncopy','oncut','ondataavailable','ondatasetchanged','ondatasetcomplete','ondblclick',
                    'ondeactivate','ondrag','ondragend','ondragenter','ondragleave','ondragover','ondragstart','ondrop','onerror','onerrorupdate',
                    'onfilterchange','onfinish','onfocus','onfocusin','onfocusout','onhelp','onkeydown','onkeypress','onkeyup','onlayoutcomplete',
                    'onload','onlosecapture','onmousedown','onmouseenter','onmouseleave','onmousemove','onmouseout','onmouseover','onmouseup','onmousewheel',
                    'onmove','onmoveend','onmovestart','onpaste','onpropertychange','onreadystatechange','onreset','onresize','onresizeend','onresizestart',
                    'onrowenter','onrowexit','onrowsdelete','onrowsinserted','onscroll','onselect','onselectionchange','onselectstart','onstart','onstop',
                    'onsubmit','onunload','javascript','script','eval','behaviour','expression','style');
            $skipstr = implode('|', $skipkeys);
            $value = preg_replace(array("/($skipstr)/i"), '.', $value);
            if(!preg_match("/^[\/|\s]?($allowtags)(\s+|$)/is", $value)) {
                $value = '';
            }
            $replaces[] = empty($value)?'':"<".str_replace('&quot;', '"', $value).">";
        }
    }
    $html = str_replace($searchs, $replaces, $html);


    return $html;
}

function dhtmlspecialchars($string, $flags = null)
{
    if(is_array($string)) {
        foreach($string as $key => $val) {
            $string[$key] = dhtmlspecialchars($val, $flags);
        }
    } else {
        if($flags === null) {
            $string = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string);
            if(strpos($string, '&amp;#') !== false) {
                $string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string);
            }
        } else {
            if(PHP_VERSION < '5.4.0') {
                $string = htmlspecialchars($string, $flags);
            } else {
                if(strtolower(CHARSET) == 'utf-8') {
                    $charset = 'UTF-8';
                } else {
                    $charset = 'ISO-8859-1';
                }
                $string = htmlspecialchars($string, $flags, $charset);
            }
        }
    }
    return $string;
}

// */


// == 请求相关函数 ==========================
function ajax_echo( $info )
{
    if( !headers_sent() )
    {
        header("Content-Type:text/html;charset=utf-8");
        header("Expires: Thu, 01 Jan 1970 00:00:01 GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
    }

    echo $info;
}

if (!function_exists('apache_request_headers'))
{
    function apache_request_headers()
    {
        foreach($_SERVER as $key=>$value)
        {
            if (substr($key,0,5)=="HTTP_")
            {
                $key=str_replace(" ","-",ucwords(strtolower(str_replace("_"," ",substr($key,5)))));
                $out[$key]=$value;
            }
            else
            {
                $out[$key]=$value;
            }
        }

        return $out;
    }
}

function is_ajax_request()
{
    $headers = apache_request_headers();
    return (isset( $headers['X-Requested-With'] ) && ( $headers['X-Requested-With'] == 'XMLHttpRequest' )) || (isset( $headers['x-requested-with'] ) && ($headers['x-requested-with'] == 'XMLHttpRequest' ));
}

function is_json_request()
{
    $headers = apache_request_headers();
    return (isset( $headers['Content-Type'] ) && ( clean_header($headers['Content-Type']) == 'application/json' ));
}

// == 响应相关函数 ==========================
function response()
{
    if( !isset( $GLOBALS['_LP4_RSP'] ) )
        $GLOBALS['_LP4_RSP'] = new response();

    return  $GLOBALS['_LP4_RSP'];
}

function send_json( $obj )
{
    response()
        ->status(200)
        //->header("Access-Control-Allow-Origin","*")
        //->header('Content-Type', 'application/json')
        ->write(json_encode( $obj , JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ))
        ->send();
}

function send_result( $data )
{
    $ret['code'] = 0 ;
    $ret['message'] = '' ;
    $ret['data'] = $data ;
    send_json( $ret );
}


function send_error( $type , $info = null )
{
    $error = get_error( $type );
    if( $info != null )
        $error['message'] = $error['message'].' -' . $info ;

    send_json($error);
}


function get_error( $type )
{
    if( !isset( $GLOBALS['rest_errors'][$type] ) )
        $error = array( 'code' => 99999 , 'message' => '其他' );
    else
        $error = $GLOBALS['rest_errors'][$type];

    return $error;
}

// == REST错误类型定义和Exception元数据 ==========================
$GLOBALS['rest_errors']['ROUTE'] = array( 'code' => '10000' , 'message' => 'route error' );
$GLOBALS['rest_errors']['INPUT'] = array( 'code' => '10001' , 'message' => 'input error' );
$GLOBALS['rest_errors']['DATABASE'] = array( 'code' => '30001' , 'message' => 'database error' );
$GLOBALS['rest_errors']['DATA'] = array( 'code' => '40001' , 'message' => 'data error' );
$GLOBALS['rest_errors']['AUTH'] = array( 'code' => '20001' , 'message' => 'auth error' );
