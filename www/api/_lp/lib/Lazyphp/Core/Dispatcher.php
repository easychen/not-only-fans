<?php
namespace Lazyphp\Core;

class Dispatcher
{
    public static function execute($callback, array &$params = array()) {
        return is_array($callback) ?
            self::invokeMethod($callback, $params) :
            self::callFunction($callback, $params);
    }

    /**
     * Calls a function.
     *
     * @param string $func Name of function to call
     * @param array $params Function parameters
     * @return mixed Function results
     */
    public static function callFunction($func, array &$params = array()) {
        
        return $func(...$params);
        

        // switch (count($params)) {
        //     case 0:
        //         return $func();
        //     case 1:
        //         return $func($params[0]);
        //     case 2:
        //         return $func($params[0], $params[1]);
        //     case 3:
        //         return $func($params[0], $params[1], $params[2]);
        //     case 4:
        //         return $func($params[0], $params[1], $params[2], $params[3]);
        //     case 5:
        //         return $func($params[0], $params[1], $params[2], $params[3], $params[4]);
        //     default:
        //         return call_user_func_array($func, $params);
        // }
    }

    /**
     * Invokes a method.
     *
     * @param mixed $func Class method
     * @param array $params Class method parameters
     * @return mixed Function results
     */
    public static function invokeMethod($func, array &$params = array())
    {
        list($class, $method) = $func;
        $GLOBALS['__METHOD__'] = $method;
        $instance = new $class();

        $key = cmkey( $class, $method );
        if( isset($GLOBALS['meta'][$key]) )
        {
            $GLOBALS['meta_key'] = $key;
            // 获取方法所对应的Meta信息
            $meta = $GLOBALS['meta'][$key];
            $route_type = $meta['route'][0]['uri'];
            $route_type = substr($route_type, 0, strpos($route_type, ' '));
            if( $meta['route'][0]['params'] && is_array( $meta['route'][0]['params'] ) )
            {

            }
            if( $meta['route'][0]['params'] && is_array( $meta['route'][0]['params'] ) )
                $route_parmas = array_slice($meta['route'][0]['params'], 0, count($params));
            else
                $route_parmas = false;

            // 不管自动检查是否打开，先处理field_check
            if( isset( $meta['Params'] ) && is_array( $meta['Params'] ) )
            {
                foreach( $meta['Params'] as $item )
                {
                    if( isset( $item['name'] ) ) $item['name'] = ltrim($item['name'],'$');
                    if( isset( $item['cnname'] )) $item['cnname'] = trim($item['cnname'],'"');
                    $to_check[$item['name']] = $item;
                }
            }

            // 开始根据to_check数组，对输入项进行检查
            if( isset( $to_check ) && is_array( $to_check ) )
                foreach( $to_check as $key=>$item )
                {
                    if( isset($item['filters']) && is_array( $item['filters'] ) )
                    {
                        foreach( $item['filters'] as $check_function )
                        {
                            $tinfo = explode( '_' , $check_function );
                            $type = reset( $tinfo );
                            $type = strtolower(trim($type));
                            if( $type == 'check' )
                            {
                                // 当函数调用为false时直接输出错误信息
                                if( function_exists( $check_function ) )
                                {
                                    //echo $item['name']  . '~' . print_r( $meta['route'][0]['params'] , 1 );
                                    // 如果是路由器自带变量
                                    if( $route_parmas && isset($meta['route'][0]['params']) && in_array( $item['name'] , $route_parmas ) )
                                        $vv = $params[array_search( $item['name'] , $route_parmas )]; // 按顺序从参数中获取
                                    else
                                        $vv = v($item['name']); // 按名字从REQUEST中获取

                                    //echo $item['name'] .'s vv=' . $vv;

                                    if(!call_user_func( $check_function , $vv ) )
                                    {
                                        // 抛出异常
                                        // throw new InputException($item['cnname']."(" . $item['name'] . ")未提供或格式不正确 via ".$check_function);
                                        lianmi_throw( 'INPUT' , "%s格式不正确（经%s检查）vlaue=".$vv , [ $item['name']  , $check_function ]  );
                                    }
                                }

                            }
                            else
                            {
                                // filter
                                // 修改request数值
                                if( function_exists( $check_function ) )
                                {
                                    if( $route_parmas && isset($meta['route'][0]['params']) && in_array( $item['name'] , $route_parmas ) )
                                    {
                                       $params[array_search( $item['name'] , $route_parmas )] =
                                       call_user_func( $check_function , $params[array_search( $item['name'] , $route_parmas )] );
                                    }
                                    elseif( isset( $_REQUEST[$item['name']] ) )
                                    {
                                        $php_uri_type = '_'.strtoupper($route_type);
                                        switch ($php_uri_type) {
                                            case '_GET':
                                                $_GET[$item['name']] = call_user_func( $check_function , $_REQUEST[$item['name']] );
                                                break;
                                            case '_POST':
                                                $_POST[$item['name']] = call_user_func( $check_function , $_REQUEST[$item['name']] );
                                                break;
                                            case '_PUT':
                                                $_PUT[$item['name']] = call_user_func( $check_function , $_REQUEST[$item['name']] );
                                                break;
                                            case '_DELETE':
                                                $_DELETE[$item['name']] = call_user_func( $check_function , $_REQUEST[$item['name']] );
                                                break;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    // 如果写入了参数绑定
                    // 注意这个地方是依赖于参数顺序的

                    // 如果在路由中
                    if( !($route_parmas && in_array( $item['name'] , $route_parmas )))
                        if( isset($meta['binding'][$item['name']]) )
                        {
                            // 变量顺序按绑定顺序排序
                            $index = array_key_index( $item['name'],$meta['binding'] );
                            $request_params[$index] = (isset($meta['binding'][$item['name']]['default']) && !isset($_REQUEST[$item['name']]))?
                                    $meta['binding'][$item['name']]['default']:
                                    v($item['name']);
                        }
                    // slog($request_params);
                }
                //slog($meta['binding']);

        }

        // 强制request变量按function参数顺序进行绑定
        if( isset($request_params) && is_array( $request_params ) )
        {
            ksort( $request_params );
            $params = array_merge( $params , $request_params );
        }
        return call_user_func_array(array( $instance , $method  ) , $params);
    }

}
