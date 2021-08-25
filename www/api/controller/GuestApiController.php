<?php
namespace Lazyphp\Controller;
set_time_limit( 80 );

class GuestApiController
{
	public function __construct()
    {
        // Guest 下的接口支持token，但不验证。
        // 不认 cookie 带来的 php sessionid
        $token = t(v('token'));
        if( strlen( $token ) > 0 )
        {
            session_id( $token );
            session_start();
        }
        
        $stoken = t(v('stoken'));
        if( strlen( $stoken ) > 0 ) login_by_stoken( $stoken );
    }

    /**
     * 用户注册接口
     * @ApiDescription(section="User", description="用户注册接口")
     * @ApiLazyRoute(uri="/user/register",method="POST|GET")
     * @ApiParams(name="email", type="string", nullable=false, description="email", check="check_not_empty", cnname="email")
     * @ApiParams(name="nickname", type="string", nullable=false, description="nickname", check="check_not_empty", cnname="用户昵称")
    * @ApiParams(name="username", type="string", nullable=false, description="username", check="check_not_empty", cnname="用户唯一ID")
    * @ApiParams(name="password", type="string", nullable=false, description="password", check="check_not_empty", cnname="用户密码")
    * @ApiParams(name="address", type="string", nullable=false, description="address",  cnname="钱包地址")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function register( $email , $nickname , $username , $password, $address = '' )
    {
        // email 全部转换为小写
        $email = strtolower( $email );

        $nickname = mb_substr( $nickname , 0 , 15 , 'UTF-8' );
        
        // 检查 email 格式是否正确
        if( !filter_var( $email , FILTER_VALIDATE_EMAIL) ) 
            return lianmi_throw( "INPUT" , "Email格式不正确" );

        // 检查 email 是否唯一
        if( db()->getData("SELECT COUNT(*) FROM `user` WHERE `email` = '" . s( $email ) . "' ")->toVar() > 0 )
            return lianmi_throw( "INPUT" , "email地址已被注册" );    

        // 检查UserName长度 
        if( mb_strlen( $username , 'UTF-8' ) < 3 )
            return lianmi_throw( "INPUT" , "UserName长度不能少于3" );

        if( mb_strlen( $username , 'UTF-8' ) > 15 )
            return lianmi_throw( "INPUT" , "UserName长度不能大于15" );    
        
        if( !preg_match( '/^([A-Za-z]+[A-Za-z0-9_\-]*)$/is' , $username , $out ) )
        //     return lianmi_throw( "INPUT" , print_r( $out , 1 ) );
        // else
            return lianmi_throw( "INPUT" , "UserID格式错误，只能字母开始，并包含数字、字母、减号和下划线，长度不能少于3" );

        // 检查UserName是否唯一
        if( db()->getData("SELECT COUNT(*) FROM `user` WHERE `username` = '" . s( $username ) . "' ")->toVar() > 0 )
            return lianmi_throw( "INPUT" , "UserName已被占用" );

        if( in_array( strtolower($nickname) , c('forbiden_nicknames') ) ) 
            return lianmi_throw( 'INPUT' , '此用户昵称已被系统保留，请重新选择' ); 
            
        if( in_array( strtolower($username) , c('forbiden_usernames') ) ) 
            return lianmi_throw( 'INPUT' , '此UserName已被系统保留，请重新选择' );     

        if( strlen( $password ) < 6 ) return lianmi_throw( 'INPUT' , '密码长度不能短于6位' );
        
        // 数据检测完成，开始入库
        // 处理密码
        $hash = password_hash( $password , PASSWORD_DEFAULT );

        $sql = "INSERT INTO `user` ( `email` , `nickname` , `username` , `password` , `address` , `timeline` ) VALUES ( " . 
            "'" . s( $email ) .  "'," .
            "'" . s( $nickname ) .  "',".
            "'" . s( $username ) .  "',".
            "'" . s( $hash ) .  "'," .
            "'" . s( $address ) .  "'," .
            "'" . lianmi_now() . "'"
            ." )";

        db()->runSql( $sql );

        // 返回新増用户的基本信息（以便于显示），为了方便以后主从分离，这里直接拼接。
        $user = [];
        $user['id'] = db()->lastId();
        $user['email'] = $email;
        $user['username'] = $username;
        $user['nickname'] = $nickname;
        
        return send_result( $user );


        
    }

    /**
     * 用户登入接口
     * @ApiDescription(section="User", description="用户登入接口")
     * @ApiLazyRoute(uri="/user/login",method="POST|GET")
     * @ApiParams(name="email", type="string", nullable=false, description="email", check="check_not_empty", cnname="email")
     * @ApiParams(name="password", type="string", nullable=false, description="password", check="check_not_empty", cnname="用户密码")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function login( $email , $password )
    {
        // email 全部转换为小写
        $email = strtolower( $email );
        
        // 检查 email 格式是否正确
        if( !filter_var( $email , FILTER_VALIDATE_EMAIL) ) 
            return lianmi_throw( "INPUT" , "Email格式不正确" );

        if( !$user = db()->getData("SELECT * FROM `user` WHERE `email` = '" . s( $email ) . "' LIMIT 1")->toLine() )
            return lianmi_throw( "INPUT" , "Email地址不存在或者密码错误" );

        // 这里的报错信息，特意不写精确，以免被遍历
        if( !password_verify( $password , $user['password'] ) )
            return lianmi_throw( "INPUT" , "Email地址不存在或者密码错误" );
        
        // 清空密码 hash 以免在之后的流程中出错
        unset( $user['password'] ) ;

        // 检查 level ， level 小于 1 的表示账号已经被封禁
        if( intval( $user['level'] ) < 1 ) 
            return lianmi_throw( "INPUT" , "账号不存在或已被限制登入" );

        // 开始登入
        // 每次启用新的 session id
        session_start();
        session_regenerate_id( true );
        
        $user['uid'] = $user['id'];
        $user['token'] = session_id(); // 将 session id 作为 token 传回前端

        // 取得当前用户参加的group
        // $user['groups'] = db()->getData("SELECT `group_id` FROM `group_member` WHERE `uid` = '" . intval( $user['id'] ) . "' ")->toColumn('group_id');

        // $user['vip_groups'] = db()->getData("SELECT `group_id` FROM `group_member` WHERE `uid` = '" . intval( $user['id'] ) . "' AND `is_vip` = 1 ")->toColumn('group_id');
       
        // $user['admin_groups'] = db()->getData("SELECT `group_id` FROM `group_member` WHERE `uid` = '" . intval( $user['id'] ) . "' AND `is_author` = 1 ")->toColumn('group_id');
        
        // 添加当前用户的group分组信息
        $user = array_merge( $user , get_group_info( $user['id'] )) ;

        // if( strlen( $user['avatar'] )  < 1 ) $user['avatar'] = c('default_avatar_url');

        foreach( [ 'uid' , 'email' , 'nickname' , 'username' , 'level' , 'avatar' ] as $field )
            $_SESSION[$field] = $user[$field];
        
        return send_result( $user );
 
    }

    /**
     * 显示图片
     * @TODO 此接口不需要登入，以后会使用云存储或者x-send来替代
     * @ApiDescription(section="Global", description="显示图片接口")
     * @ApiLazyRoute(uri="/image/@uid/@inner_path",method="GET|POST")
     * @ApiParams(name="uid", type="string", nullable=false, description="uid", check="check_not_empty", cnname="图片路径")
     * @ApiParams(name="inner_path", type="string", nullable=false, description="inner_path", check="check_not_empty", cnname="图片路径")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function showImage( $uid , $inner_path )
    {
        $path = $uid .'/' . $inner_path;
        if( !$content = storage()->read( $path )) return lianmi_throw( 'FILE' , '文件数据不存在' );
        $mime = storage()->getMimetype($path);

        header('Content-Type: ' . $mime );
        echo $content;

        return true;
        
    }

    /**
     * 显示栏目基本信息
     * 此接口不需要登入
     * @ApiDescription(section="Group", description="显示栏目基本信息接口")
     * @ApiLazyRoute(uri="/group/detail/@id",method="GET|POST")
     * @ApiParams(name="id", type="int", nullable=false, description="id", check="check_uint", cnname="栏目ID")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function getGroupDetail( $id )
    {
        if( !$group = db()->getData( "SELECT * FROM `group` WHERE `id` = '". intval( $id ) . "' LIMIT 1" )->toLine())
        {
            return lianmi_throw( 'INPUT' , 'ID对应的栏目已删除或不存在' );
        }
        else
            return send_result( $group );

    }

    /**
     * 获得栏目列表
     * 此接口不需要登入，注意这是一个不完全列表
     * @ApiDescription(section="Group", description="获得栏目列表")
     * @ApiLazyRoute(uri="/group/top100",method="GET|POST")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function getGroupTop100()
    {
        return send_result( $groups = db()->getData( "SELECT * FROM `group` WHERE `is_active` = 1 ORDER BY `promo_level` DESC , `member_count` DESC , `id` DESC LIMIT 100 " )->toArray() );
    }

    /**
     * 获取用户基本信息
     * 此接口不需要登入
     * @ApiDescription(section="User", description="获取用户基本信息接口")
     * @ApiLazyRoute(uri="/user/detail(/@id)",method="GET|POST")
     * @ApiParams(name="id", type="int", nullable=false, description="id", cnname="用户ID")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function getUserDetail( $id = null )
    {
        if( $id === null && lianmi_uid() > 0 ) $id = lianmi_uid();

        $id = abs(intval( $id ));
        
        if( lianmi_uid() > 0 && lianmi_uid() == $id )
        {
            $sql = "SELECT * FROM `user` WHERE `id` = '". intval( $id ) . "' AND `level` > 0 LIMIT 1";
        }
        else
        {
            $sql = "SELECT " . c('user_normal_fields') .  " FROM `user` WHERE `id` = '". intval( $id ) . "' AND `level` > 0 LIMIT 1" ;
        }
        
        if( !$user = db()->getData( $sql )->toLine())
        {
            return lianmi_throw( 'INPUT' , 'ID对应的用户已删除或不存在' );
        }
        else
        {
            if( isset( $user['password'] ) ) unset( $user['password'] );
            return send_result( $user );   
        }

    }

    /**
     * 获取内容的全部内容
     * 此接口不需要登入
     * @ApiDescription(section="Feed", description="获取内容的全部内容")
     * @ApiLazyRoute(uri="/feed/detail/@id",method="GET|POST")
     * @ApiParams(name="id", type="int", nullable=false, description="id", check="check_uint", cnname="内容ID")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function getFeedDetail( $id )
    {
        if( !$feed = db()->getData( "SELECT *, `uid` as `user` , `group_id` as `group` FROM `feed` WHERE `id` = '". intval( $id ) . "' AND `is_delete` = 0 LIMIT 1" )->toLine())
        {
            return lianmi_throw( 'INPUT' , 'ID对应的内容不存在或者你没有权限阅读' );
        }
        else
        {
            // 鉴权
            $can_see = true;

            if( $feed['is_paid'] == 1 )
            {
                // 鉴权
                $can_see = false;

                // lianmi_throw( "DATA" , "UID=".lianmi_uid()."<<<" ); 

                // 用户必须已经登入
                if( lianmi_uid() > 0 )
                {
                    // 转发的情况，这是从栏目里边点出来的
                    if( $feed['is_forward'] == 1 )
                    {
                        // 栏目VIP或者栏主可以看到
                        $sql = "SELECT * FROM `group_member` WHERE `group_id` = '" . intval( $feed['forward_group_id'] ) . "' AND `uid` = '" . intval( lianmi_uid() ) . "' LIMIT 1";

                       //  lianmi_throw( "DATA" , $sql );

                        if($member_ship = db()->getData($sql)->toLine())
                        {
                            
                            // lianmi_throw( "DATA" , $member_ship );
                            
                            if( $member_ship['is_author'] == 1 ) $can_see = true;
                            if( $member_ship['is_vip'] == 1 ) $can_see = true;

                            // 这里不检查 $member_ship['vip_expire'] , 交给每日定时脚本去做。
                        }
                    }
                    else
                    {
                        // 原发的情况，这是从作者的页面点出来的
                        // 只有作者本人才能看到个人页面上的付费内容
                        if( lianmi_uid() == $feed['uid'] ) $can_see = true;
                    }

                }
                else
                {
                    // lianmi_throw( "DATA" , $feed );
                }
                
            }

            if( !$can_see ) return lianmi_throw( 'AUTH' , '该内容为付费内容，仅限VIP订户查看' );
            if( $feed['is_forward'] == 1 ) $feed['group'] = $feed['forward_group_id'];
            
            $feed = extend_field_oneline( $feed , 'user' , 'user' );
            $feed = extend_field_oneline( $feed , 'group' , 'group' );
            
            return send_result( $feed );   
        }
    }

    /**
     * 获取内容评论列表
     * @ApiDescription(section="Feed", description="对内容发起评论")
     * @ApiLazyRoute(uri="/feed/comment/list/@id",method="GET|POST")
     * @ApiParams(name="since_id", type="int", nullable=false, description="since_id", cnname="游标ID")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function listFeedComment( $id , $since_id = 0  )
    {
        if( !$feed = db()->getData( "SELECT *, `uid` as `user` , `group_id` as `group` FROM `feed` WHERE `id` = '". intval( $id ) . "' AND `is_delete` = 0 LIMIT 1" )->toLine())
        {
            return lianmi_throw( 'INPUT' , 'ID对应的内容不存在或者你没有权限阅读' );
        }
        else
        {
            $group_id = $feed['is_forward'] == 1 ? $feed['forward_group_id'] : $feed['group_id'];
            

            // 鉴权
            $can_see = true;
            
            if( $feed['is_paid'] == 1 )
            {
                
                // 在用户登录的情况下
                if( lianmi_uid() > 0 )
                {
                    $member_ship = db()->getData("SELECT * FROM `group_member` WHERE `group_id` = '" . intval( $group_id ) . "' AND `uid` = '" . intval( lianmi_uid() ) . "' LIMIT 1")->toLine();

                    // 鉴权
                    $can_see = false;

                    // 转发的情况，这是从栏目里边点出来的
                    if( $feed['is_forward'] == 1 )
                    {
                        if( $member_ship['is_author'] == 1 ) $can_see = true;
                        if( $member_ship['is_vip'] == 1 ) $can_see = true;    
                    }
                    else
                    {
                        // 原发的情况，这是从作者的页面点出来的
                        // 只有作者本人才能看到个人页面上的付费内容
                        if( lianmi_uid() == $feed['uid'] )
                        {
                            $can_see = true;
                            $can_comment = true;
                        } 
                    } 

                }
                else
                {
                    $can_see = false;
                    $can_comment = false;
                }
                     
            }

            // 免费内容可以直接看

            if( !$can_see ) return lianmi_throw( 'AUTH' , '没有权限查看此内容的评论，可使用有权限的账号登入后查看' );
            
            $since_sql = $since_id == 0 ? "" : " AND `id` < '" . intval( $since_id ) . "' ";

            
            $totalcount = db()->getData("SELECT COUNT(*) FROM `comment` WHERE `feed_id` = '" . intval( $id ) . "' AND `is_delete` = 0 "   )->toVar();
            // comments_per_feed
            $sql = "SELECT *, `uid` as `user` , `feed_id` as `feed` FROM `comment` WHERE `feed_id` = '" . intval( $id ) . "' AND `is_delete` = 0 "  . $since_sql. " ORDER BY `id` DESC LIMIT " . c('comments_per_feed');
            if( $data = db()->getData( $sql )->toArray() )
            {
                $data = extend_field( $data , 'user' , 'user' );
                $data = extend_field( $data , 'feed' , 'feed' );
            }
            else 
                $data = [];

            if( is_array( $data ) && count( $data ) > 0  )
            {
                $maxid = $minid = $data[0]['id'];
                foreach( $data as $item )
                {
                    if( $item['id'] > $maxid ) $maxid = $item['id'];
                    if( $item['id'] < $minid ) $minid = $item['id'];
                }
            }
            else
            $maxid = $minid = null;
                
            return send_result( ['comments'=>$data , 'count'=>count($data) , 'maxid'=>$maxid , 'minid'=>$minid , 'total' => $totalcount, 'comments_per_feed'=>c('comments_per_feed') ] );
        }
    }

    /**
     * 获取用户内容列表
     * 此接口不需要登入
     * @ApiDescription(section="User", description="获取用户内容列表")
     * @ApiLazyRoute(uri="/user/feed/@id",method="GET|POST")
     * @ApiParams(name="id", type="int", nullable=false, description="id", check="check_uint", cnname="用户ID")
     * @ApiParams(name="since_id", type="int", nullable=false, description="since_id", cnname="游标ID")
     * @ApiParams(name="filter", type="int", nullable=false, description="filter", cnname="过滤选项")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function getUserFeed( $id , $since_id = 0 , $filter = 'all')
    {
        $filter_sql = '';
        if( $filter == 'paid' ) $filter_sql = " AND `is_paid` = 1 ";
        if( $filter == 'media' ) $filter_sql = " AND `images` !='' ";
        
        // VIP订户和栏主可以查看付费内容
        $since_sql = $since_id == 0 ? "" : " AND `id` < '" . intval( $since_id ) . "' ";

        $sql = "SELECT *, `uid` as `user` , `forward_group_id` as `group` FROM `feed` WHERE `is_delete` != 1 AND  `is_forward` = '0' AND `uid` = '" . intval( $id ) .  "' AND ( `is_paid` = '0' OR `uid` = '" . intval( lianmi_uid() ) . "' ) " . $since_sql . $filter_sql ." ORDER BY `id` DESC LIMIT " . c('feeds_per_page');

        $data = db()->getData( $sql )->toArray();
        $data = extend_field( $data , 'user' , 'user' );
        $data = extend_field( $data , 'group' , 'group' );
        
        
        if( is_array( $data ) && count( $data ) > 0  )
        {
            $maxid = $minid = $data[0]['id'];
            foreach( $data as $item )
            {
                if( $item['id'] > $maxid ) $maxid = $item['id'];
                if( $item['id'] < $minid ) $minid = $item['id'];
            }
        }
        else
        $maxid = $minid = null;
            
        return send_result( ['feeds'=>$data , 'count'=>count($data) , 'maxid'=>$maxid , 'minid'=>$minid ] );

    }



    /**
     * 检查合约数据，并更新setGroup的内容
     * @TODO 这个接口性能非常的慢，稍后分离出去做成独立服务，同时需要串行化，不管是通过加锁还是队列。
     * @ApiDescription(section="Group", description="检查栏目购买数据")
     * @ApiLazyRoute(uri="/group/contract/check/@id",method="GET|POST")
     * @ApiParams(name="id", type="int", nullable=false, description="id", check="check_uint", cnname="栏目ID")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function checkGroupPay( $id )
    {
        $abi = json_decode( file_get_contents( AROOT . DS . 'contract' . DS . 'build' . DS . 'lianmi.abi' ) );
        $contract = new \Web3\Contract( c('web3_network') , $abi );
        
        $contract->at( c('contract_address') )->call( 'feeOf' , $id , function( $error , $data ) use( $id , $contract )
        {
            if( $error != null )
            {
                return lianmi_throw( 'CONTRACT' , '合约调用失败：' . $error->getMessage() );
            }
            else
            {
                $data = reset( $data );
                if( $data->compare( new \phpseclib\Math\BigInteger('1000000000000000')) >= 0 )
                {
                    // 当支付过超过 0.001 eth 时
                    if( !$group_info = db()->getData("SELECT * FROM `group` WHERE `id` = '" . intval( $id ) . "' LIMIT 1")->toLine())
                    {
                        return lianmi_throw( 'INPUT' , '栏目不存在或已被删除' );
                    }

                    // 只发布一次数据
                    if( $group_info['is_paid'] == 1 )
                    {
                        return lianmi_throw( 'INPUT' , '栏目完成过初始化，如合约发生问题，请联系管理员' );
                    }

                    $seller_address = @c('sellers')[$group_info['seller_uid']];

                    if( strlen( $seller_address ) < 1 ) $seller_address = $group_info['author_address'];

                    // 调用命令行，写入合约
                    // --groupid=1 --price=10000000000000000 --author_address=0xF05949e6d0Ed5148843Ce3f26e0f747095549BB4 --seller_address=0xF05949e6d0Ed5148843Ce3f26e0f747095549BB4
                    
                    $lastline = exec( 'node --harmony ' . AROOT . DS . 'contract' . DS . 'group.js --groupid=' . intval( $group_info['id'] ) . ' --price=' . bigintval( $group_info['price_wei'] ) . ' --author_address=' . $group_info['author_address'] . ' --seller_address=' . $seller_address , $output , $val );

                    
                    $ret = strtolower(t(join( "" , $output )));

                    if( t($lastline) == 'ok' )
                    {
                        // 在调用检测下结果
                        $contract->at( c('contract_address') )->call( 'settingsOf' , $group_info['id'] , function( $error , $data ) use( $group_info )
                        {
                            if( strtolower($data['author']) == strtolower($group_info['author_address']) )
                            {
                                // 设置正确
                                db()->runSql( "UPDATE `group` SET `is_paid` = 1 , `is_active` = 1 WHERE `id` = '" . intval( $group_info['id'] ) . "' LIMIT 1" );

                                // 将支付人加入到栏目里
                                //db()->


                                return send_result( "done" );
                            }
                        });
                    }
                    else
                    {
                        return lianmi_throw( 'CONTRACT' , '合约调用失败:'.$ret );
                    }



                }
                else
                {
                    return lianmi_throw( 'CONTRACT' , '支付的金额不足'.$data );
                }
                //return var_dump( $data) );
            }
        });

    }

    /**
     * 退出当前用户
     * @ApiLazyRoute(uri="/logout",method="GET|POST")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function logout()
    {
        if( isset( $_SESSION ) )
            foreach( $_SESSION as $key => $value )
            {
                unset( $_SESSION[$key] );
            }

        return  send_result( intval(!isset( $_SESSION['uid'] )) );
    }

    /**
     * 获取栏目的免费内容
     * @ApiDescription(section="Group", description="检查栏目购买数据")
     * @ApiLazyRoute(uri="/group/feed2/@id",method="GET|POST")
     * @ApiParams(name="id", type="int", nullable=false, description="id", check="check_uint", cnname="栏目ID")
     * @ApiParams(name="since_id", type="int", nullable=false, description="since_id", cnname="游标ID")
     * @ApiParams(name="filter", type="int", nullable=false, description="filter", cnname="过滤选项")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function getGroupFeed2( $id , $since_id = 0 , $filter = 'all' )
    {
        if( lianmi_uid() > 0 )
        {
            $info = db()->getData( "SELECT * FROM `group_member` WHERE `uid` = '" . intval( lianmi_uid() ) . "' AND `group_id` = '" . intval( $id ) . "' LIMIT 1" )->toLine();
        }

        // 
        $filter_sql = '';
        if( $filter == 'paid' ) $filter_sql = " AND `is_paid` = 1 ";
        if( $filter == 'media' ) $filter_sql = " AND `images` !='' ";
        
        // VIP订户和栏主可以查看付费内容
        $paid_sql = '';

        if(  $info &&  $info['is_vip'] != 1 && $info['is_author'] != 1 )
            $paid_sql  = " AND `is_paid` != 1 ";
        
        $since_sql = $since_id == 0 ? "" : " AND `id` < '" . intval( $since_id ) . "' ";

        $sql = "SELECT *, `uid` as `user` , `forward_group_id` as `group` FROM `feed` WHERE `is_delete` != 1 AND `forward_group_id` = '". intval( $id ) . "'" . $paid_sql . $since_sql . $filter_sql ." ORDER BY `id` DESC LIMIT " . c('feeds_per_page');

        $data = db()->getData( $sql )->toArray();
        $data = extend_field( $data , 'user' , 'user' );
        $data = extend_field( $data , 'group' , 'group' );
        
        
        if( is_array( $data ) && count( $data ) > 0  )
        {
            $maxid = $minid = $data[0]['id'];
            foreach( $data as $item )
            {
                if( $item['id'] > $maxid ) $maxid = $item['id'];
                if( $item['id'] < $minid ) $minid = $item['id'];
            }
        }
        else
        $maxid = $minid = null;

        // 获取栏目置顶feed
        $sql = "SELECT * FROM `group` WHERE `id` = '" . intval( $id ) . "' LIMIT 1";
        $groupinfo = db()->getData($sql)->toLine();
        if( $groupinfo && isset( $groupinfo['top_feed_id'] ) && intval($groupinfo['top_feed_id']) > 0  )
        {
            $topfeed = db()->getData("SELECT *, `uid` as `user` , `forward_group_id` as `group` FROM `feed` WHERE `is_delete` != 1 AND `id` = '" . intval($groupinfo['top_feed_id']) . "' LIMIT 1")->toLine();

            $topfeed = extend_field_oneline( $topfeed, 'user' , 'user' );
            $topfeed = extend_field_oneline( $topfeed, 'group' , 'group' );

        }
        else
            $topfeed = false;
        
        $paid_feed_count = db()->getData("SELECT COUNT(`id`) FROM `feed` WHERE `is_delete` != 1 AND `forward_group_id` = '". intval( $id ) . "' AND `is_paid` = 1 ")->toVar();    

        return send_result( ['feeds'=>$data , 'count'=>count($data) , 'maxid'=>$maxid , 'minid'=>$minid , 'topfeed' => $topfeed , 'paid_feed_count' => $paid_feed_count ] );

    }
}