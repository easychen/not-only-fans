<?php
function lianmi_throw($type, $info, $args = null)
{
    if (!is_array($args)) {
        $args = [ $args ] ;
    }
    $code = isset(c('error_type')[$type]) ? c('error_type')[$type] : 99999;
    $message = '[' . $type . ']' . sprintf($info, ...$args);
    throw new \Lazyphp\Core\LianmiException($message, $code, $info, $args);
}

function lianmi_now()
{
    return date("Y-m-d H:i:s");
}

function storage()
{
    if (!isset($GLOBALS['LP_FLYSTOR'])) {
        $GLOBALS['LP_FLYSTOR'] = new \League\Flysystem\Filesystem(new \League\Flysystem\Adapter\Local(c('local_storage_path')));
    }

    return $GLOBALS['LP_FLYSTOR'];
}

function mc()
{
    if (!isset($GLOBALS['LP_MEMCACHED'])) {
        $GLOBALS['LP_MEMCACHED'] = new \Memcached();
        $GLOBALS['LP_MEMCACHED']->addServer('localhost', 11211);
    }

    return $GLOBALS['LP_MEMCACHED'];
}

function lianmi_uid()
{
    return isset($_SESSION['uid'])? intval($_SESSION['uid']) : 0 ;
}

function lianmi_nickname()
{
    return isset($_SESSION['nickname'])? $_SESSION['nickname'] : '[-已注销-]' ;
}

function lianmi_username()
{
    return isset($_SESSION['username'])? $_SESSION['username'] : 'notexists' ;
}

function lianmi_at($text)
{
    $reg = '/@([a-zA-Z][a-zA-Z0-9\-_]*)/is';
    if (preg_match_all($reg, $text, $out)) {
        return $out[1];
    } else {
        return false;
    }
}

function indexed_array($array)
{
    if (!isset($array) || !is_array($array)) {
        return false;
    }
    foreach ($array as $line) {
        $ret[$line[$name]] = $line;
    }

    return isset($ret)?$ret:false;
}

function path2url($path, $action = 'image')
{
    return c('site_base_url') . $action . '/' . $path;
}

function web3()
{
    if (!isset($GLOBALS['LP_WEB3'])) {
        $GLOBALS['LP_WEB3'] = new \Web3\Web3(c('web3_network'));
    }

    return $GLOBALS['LP_WEB3'];
}

function table($name)
{
    if (!isset($GLOBALS['LP_LDO_'.$name])) {
        $GLOBALS['LP_LDO_'.$name] = new \Lazyphp\Core\Ldo($name);
    }

    return $GLOBALS['LP_LDO_'.$name];
}

function extend_field($array, $field, $table, $join = 'id')
{
    if (!is_array($array)) {
        return $array;
    }
    $ids = array_map(function ($item) use ($field) {
        return "'" . $item[$field] . "'";
    }, $array);

    if ($table == 'user') {
        $sql = "SELECT " . c('user_normal_fields') . " FROM `" . s($table) . "` WHERE `" . s($join) . "` IN ( " . join(',', $ids) . " )";
    } else {
        $sql = "SELECT * FROM `" . s($table) . "` WHERE `" . s($join) . "` IN ( " . join(',', $ids) . " )";
    }
    
    if ($data = db()->getData($sql)->toIndexedArray($join)) {
        foreach ($array as $key => $item) {
            if (isset($data[$item[$field]])) {
                $array[$key][$field] = $data[$item[$field]];
            }
        }
    }

    return $array;
}

// 系统消息
function system_notice($to_uid, $uid, $username, $nickname, $action, $link)
{
    if (intval($to_uid) < 1
    || strlen($username) < 1
    || strlen($nickname) < 1
    || strlen($uid) < 1
    || strlen($action) < 1
    || strlen($link) < 1
    ) {
        return false;
    }

    $text = json_encode(compact('username', 'nickname', 'uid', 'action', 'link'));
    
    // 因为是系统消息，所以只插入单条消息就够了，毕竟 user 0 也不可能登录系统查看
    $sql = "INSERT INTO `message` ( `uid` , `from_uid` , `to_uid` , `text` , `timeline` , `is_read` ) VALUES ( '" . intval($to_uid) . "' , '0' , '" . intval($to_uid) . "' , '" . s($text) . "' , '". s(lianmi_now()) ."' , '0' ) ";
    db()->runSql($sql);

    $sql = "REPLACE INTO `message_group` ( `uid` , `from_uid` , `to_uid` , `text` , `timeline` , `is_read` ) VALUES ( '" . intval($to_uid) . "' , '0' , '" . intval($to_uid) . "' , '" . s($text) . "' , '". s(lianmi_now()) ."' , '0' ) ";
    db()->runSql($sql);

    return true;
}

function extend_field_oneline($array, $field, $table, $join = 'id')
{
    if (!is_array($array)) {
        return $array;
    }
    $id = $array[$field];

    if ($table == 'user') {
        $sql = "SELECT " . c('user_normal_fields') . " FROM `" . s($table) . "` WHERE `" . s($join) . "` =  " . intval($id) . "  LIMIT 1";
    } else {
        $sql = "SELECT * FROM `" . s($table) . "` WHERE `" . s($join) . "` = " . intval($id) . "  LIMIT 1";
    }
    
    // echo $sql;
    
    if ($line = db()->getData($sql)->toLine()) {
        $array[$field] = $line;
    }

    return $array;
}

function get_group_info($uid)
{
    $sql = "SELECT * , `group_id` as `group`  FROM `group_member` WHERE `uid` = '" . intval($uid) . "' LIMIT ".c('max_group_per_user');
    
    $groups = [];
    $vip_groups = [];
    $admin_groups = [];

    if ($data = db()->getData($sql)->toArray()) {
        $data = extend_field($data, 'group', 'group');

        // print_r( $data );
        
        foreach ($data as $item) {
            // 不返回已经关闭的栏目
            if ($item['group']['is_active'] == 1) {
                $groups[] = [ 'value' => $item['group_id'] , 'label' => $item['group']['name'] ];
                if ($item['is_vip'] == 1) {
                    $vip_groups[] = [ 'value' => $item['group_id'] , 'label' => $item['group']['name'] ];
                }
                if ($item['is_author'] == 1) {
                    $admin_groups[] = [ 'value' => $item['group_id'] , 'label' => $item['group']['name'] ];
                }
            }
        }
    }
    
    return compact("groups", "vip_groups", "admin_groups");
}

function fo_check_user_tx($account, $order, $price_wei, $token = 'FOUSDT@eosio')
{
    $url = 'http://api.fowallet.net/1.1';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/graphql']);

    $postdata = '{
        find_fibos_tokens_action(
         order:"-id"
         where:{
                         account_to_id: "'.$account.'",
                         contract_action:{
                             in:["eosio.token/transfer","eosio.token/extransfer"]
                         }
                     }
        ){
                     action{
                         rawData
                         transaction
                        {
                            block
                            {
                                status
                            }
                        }
                     }
         token_from{
          token_name
         }
        }
    }';

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    $data = curl_exec($ch);
    curl_close($ch);

    if (!$data_array = json_decode($data, true)) {
        return false;
    }

    //
    foreach ($data_array['data']['find_fibos_tokens_action'] as $item) {
        // 检测token类型
        if ($item['token_from']['token_name'] == $token) {
            // 检测交易状态
            if ($item['action']['transaction']['block']['status'] == 'lightconfirm') {
                // 检测订单号
                if (trim($item['action']['rawData']['act']['data']['memo']) === 'order='.$order) {
                    // 检测金额
                    $paid_price_wei = intval(100 * explode(" ", trim($item['action']['rawData']['act']['data']['quantity']['quantity']))[0]);

                    if ($paid_price_wei >= intval($price_wei)) {
                        return true;
                    }
                }
                
                
                // print_r( $item );
            }
        }
    }

    return false;
}

function check_image_url($url)
{
    $info = parse_url($url);
    return in_array(strtolower($info['host']), c('image_allowed_domain'));
}

function login_by_stoken($stoken)
{
    if (strlen($stoken) < 3) {
        return lianmi_throw("AUTH", "错误的SToken");
    }

    if (!$user = db()->getData($sql = "SELECT * FROM `user` WHERE `stoken` = '" . s($stoken) . "' LIMIT 1")->toLine()) {
        return lianmi_throw("INPUT", "SToken错误 token=`".$stoken."`");
    }

    unset($user['password']) ;

    // 检查 level ， level 小于 1 的表示账号已经被封禁
    if (intval($user['level']) < 1) {
        return lianmi_throw("INPUT", "账号不存在或已被限制登入");
    }

    // 开始登入
    // 每次启用新的 session id
    session_start();
    session_regenerate_id(true);

    $user['uid'] = $user['id'];
    $user['token'] = session_id();

    $user = array_merge($user, get_group_info($user['id'])) ;

    // if( strlen( $user['avatar'] )  < 1 ) $user['avatar'] = c('default_avatar_url');

    foreach ([ 'uid' , 'email' , 'nickname' , 'username' , 'level' , 'avatar' ] as $field) {
        $_SESSION[$field] = $user[$field];
    }
}

/**
 * Bigint demo
 * add / subtract / multiply / divide
*/

function bigintval($x)
{
    $_x = new Math_BigInteger($x);
    return $_x->toString();
}
