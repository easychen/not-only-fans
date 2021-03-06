<?php

/*
Swoole\Http\Request Object
(
    [fd] => 1
    [header] => Array
        (
            [host] => localhost:9501
            [connection] => Upgrade
            [pragma] => no-cache
            [cache-control] => no-cache
            [upgrade] => websocket
            [origin] => http://localhost:3000
            [sec-websocket-version] => 13
            [user-agent] => Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36
            [accept-encoding] => gzip, deflate, br
            [accept-language] => en-US,en;q=0.9,zh-CN;q=0.8,zh;q=0.7,ja;q=0.6,zh-TW;q=0.5
            [sec-websocket-key] => BW9/YZPEHWrH4b0SRD/Prw==
            [sec-websocket-extensions] => permessage-deflate; client_max_window_bits
        )

    [server] => Array
        (
            [query_string] => uid=1&to_uid=3
            [request_method] => GET
            [request_uri] => /
            [path_info] => /
            [request_time] => 1531565276
            [request_time_float] => 1531565276.9784
            [server_port] => 9501
            [remote_port] => 57101
            [remote_addr] => 127.0.0.1
            [master_time] => 1531565276
            [server_protocol] => HTTP/1.1
            [server_software] => swoole-http-server
        )

    [request] => 
    [cookie] => Array
        (
            [PHPSESSID] => 5b92717061944676544a4fa07b335e34
        )

    [get] => Array
        (
            [uid] => 1
            [to_uid] => 3
        )

    [files] => 
    [post] => 
    [tmpfiles] => 
)
*/
function mc()
{
    if( !isset( $GLOBALS['LP_MEMCACHED'] ) )
    {
        $GLOBALS['LP_MEMCACHED'] = new \Memcached();
        $GLOBALS['LP_MEMCACHED']->addServer('localhost', 11211);
    }

    return $GLOBALS['LP_MEMCACHED'];
}

mc()->flush();

$setConfig = [ 
    'ssl_key_file' => '/etc/letsencrypt/live/tokeneach.com/privkey.pem', 
    'ssl_cert_file' => '/etc/letsencrypt/live/tokeneach.com/fullchain.pem' 
]; 
$server = new swoole_websocket_server("0.0.0.0", 9501, SWOOLE_BASE, SWOOLE_SOCK_TCP | SWOOLE_SSL);
$server->set( $setConfig );
// ?????????????????????????????????????????????????????????mc

// websocket server??????????????????????????????????????????????????????????????????????????????

// ??????
$server->on('open', function (swoole_websocket_server $server, $request) 
{
    $uid = intval($request->get['uid']);    
    $to_uid = intval($request->get['to_uid']);
    
    if( $uid < 1 || $to_uid < 1 )
    {
        // ????????????
        $server->push( $request->fd , 'bad format' );
        $server->close( $request->fd );
        return false;
    }

    $watch_key = $uid < $to_uid ? $uid . '-' . $to_uid : $to_uid . '-' . $uid ;


    if( !$watchlist = mc()->get( 'LPWS_'.$watch_key )) $watchlist = [];

    // ??????????????????fd?????????????????????watchlist
    $watchlist[] = $request->fd;
    mc()->set( 'LPWS_'.$watch_key , $watchlist , time()+60*60 );
    
    echo "server: handshake success with fd{$request->fd}\n";

});

$server->on('message', function (swoole_websocket_server $server, $frame) 
{
    // ??????????????????????????????
    if( preg_match( '/refresh:([0-9\-]+)/is', $frame->data , $out ) )
    {
        echo "???????????????";
        $watch_key = trim( $out[1] );

        $watchlist = mc()->get( 'LPWS_'.$watch_key );
        if( isset( $watchlist ) )
        {
            //print_r( $watchlist );

            foreach( $watchlist as $key => $fd )
            {
                // ??????????????????????????????updated?????????????????????
                if( $fd == $frame->fd ) continue;
                echo "??????FD {$fd} ";
                if(!@$server->push( $fd , 'updated' ))
                {
                    // ??????????????????????????????fd?????????
                    unset( $watchlist[$key] );
                }
            }

            mc()->set( 'LPWS_'.$watch_key , $watchlist , time()+60*60 );
            
        }else echo $frame->fd;
    }
    
    echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
    // $server->push($frame->fd, "this is server");
});

$server->on('close', function ($server, $fd) 
{
    // ???????????????????????????????????????????????????????????????watchlist
    echo "client {$fd} closed\n";
});

/*
$server->on('request', function (swoole_http_request $request, swoole_http_response $response) {
        global $server;//???????????????server
        // $server->connections ????????????websocket???????????????fd????????????????????????
        foreach ($server->connections as $fd) {
            $server->push($fd, $request->get['message']);
        }
    });
*/
$server->start();

