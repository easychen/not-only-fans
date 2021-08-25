<?php
class ApiCest 
{    
    private function json(ApiTester $I)
    {
        print_r( json_decode( $I->grabResponse() , 1 ) );
    } 
    
    
    public function clean()
    {
        // 清理数据库
        $pdo = new PDO(c('database_dev','dsn'),c('database_dev','user'),c('database_dev','password'));
        $db =  new \Lazyphp\Core\Database($pdo);
        
        if($tables = $db->getData("SHOW TABLES")->toArray())
            foreach( $tables as $table )
               if($tablenames = array_values($table))
                    foreach( $tablenames as $tablename )
                    {
                        $db->runSql("DROP TABLES `{$tablename}`") ;
                    }
                        
        // add fresh data
        try
        {
            load_data_from_file( AROOT . 'sql' . DS . 'lianmi.sql' , $pdo );    
        }
        catch( Exception $e )
        {
            echo $e->getMessage();
        }
        
        // 清理图片 
        exec( "rm -rf /Users/Easy/Code/gitcode/lianmiapi/storage/*" );
    }
    
    // 注册
    public function Register(ApiTester $I)
    {
        
        //$this->clean();
        
        $I->sendPost( "/user/register" , [ 
            'email' => 'easychen@gmail.com', 
            'username' => 'easychen', 
            'nickname' => 'Easy', 
            'password' => '******'
        ] );
        
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('email' => 'easychen@gmail.com'));

    }

    // 重复注册（失败）
    public function ReRegister(ApiTester $I)
    {
        
        $I->sendPost( "/user/register" , [ 
            'email' => 'easychen@gmail.com', 
            'username' => 'easychen', 
            'nickname' => 'Easy', 
            'password' => '******'
        ] );
        
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('code' => '20001'));
        $I->seeResponseContainsJson(array('info' => 'email地址已被注册'));

    }

    public function Login(ApiTester $I)
    {
        
        $I->sendPost( "/user/login" , [ 
            'email' => 'easychen@gmail.com', 
            'password' => '******'
        ] );
        
        $I->seeResponseCodeIs(200);
        
        $I->seeResponseContainsJson(array('email' => 'easychen@gmail.com'));
        $tokens = $I->grabDataFromResponseByJsonPath('$.data.token') ;

        $this->token = $tokens[0] ;

    }

    public function BadLogin(ApiTester $I)
    {
        
        $I->sendPost( "/user/login" , [ 
            'email' => 'easychen@gmail.com', 
            'password' => '********'
        ] );
        
        $I->seeResponseCodeIs(200);
        
        $I->seeResponseContainsJson(array('code' => '20001'));
        $I->seeResponseContainsJson(array('info' => 'Email地址不存在或者密码错误'));
    }

    // 上传图片 
    public function imageUpload(ApiTester $I)
    {
        $I->sendPost( "/image/upload" , 
            [ 
                'token' => $this->token
            ],
            [
                'image' => codecept_data_dir( 'group_cover.png' )
            ] 
        );

        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('code' => '0'));
        
        $urls = $I->grabDataFromResponseByJsonPath('$.data.url') ;
        $this->cover_url = $urls[0] ;

    }

    // 创建 group
    public function groupCreate(ApiTester $I)
    {
        $I->sendPost( "/group/create" , [ 
            'name' => '第一个栏目', 
            'author_address' => '0xf05949e6d0ed5148843ce3f26e0f747095549bb4',
            'price_wei' => '100000000',
            'cover' => $this->cover_url,
            'token' => $this->token
        ] );

    
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('code' => '0'));
        $I->seeResponseContainsJson(array('name' => '第一个栏目'));

        $group_ids = $I->grabDataFromResponseByJsonPath('$.data.id') ;
        $this->group_id = $group_ids[0] ;

        // print_r( json_decode( $I->grabResponse() , 1 ) );
    }

    public function joinGroup(ApiTester $I)
    {
        
        $I->sendPost( "/group/join/" . $this->group_id , [ 
            'token' => $this->token
        ] );
        
        // $this->json( $I );
        
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('code' => '40001'));
        $I->seeResponseContainsJson(array('info' => '该栏目尚未启用或已被暂停'));
        
        
    }

    public function ActiveGroup()
    {
        db()->runSql( "UPDATE `group` SET `is_active` =  1 WHERE `id` = '" . intval( $this->group_id  ) . "' LIMIT 1" );
    }
    
    public function joinGroupAgain(ApiTester $I)
    {
        
        $I->sendPost( "/group/join/" . $this->group_id , [ 
            'token' => $this->token
        ] );
        
        // $this->json( $I );
        
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('code' => '0'));
        $I->seeResponseContainsJson(array('data' => 'done'));
        
        
    }

    // 上传附加图片 
    // 上传图片 
    public function imageUploadToThumb(ApiTester $I)
    {
        $I->sendPost( "/image/upload_thumb" , 
            [ 
                'token' => $this->token
            ],
            [
                'image' => codecept_data_dir( 'data1.jpg' )
            ] 
        );

        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('code' => '0'));
        
        $datas = $I->grabDataFromResponseByJsonPath('$.data') ;
        //$types = $I->grabDataFromResponseByJsonPath('$.data.type') ;
        $this->feed_image = $datas[0];

        

    }


    public function feedPublish(ApiTester $I)
    {
        
        $I->sendPost( "/feed/publish" , [ 
            'text' => '我的第一篇内容',
            'groups' => json_encode( [ $this->group_id ] ),
            'images' => json_encode( [ $this->feed_image ] ),
            'token' => $this->token
        ] );
        
        // $this->json( $I );
        
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('code' => '0'));
        $I->seeResponseContainsJson(array('text' => '我的第一篇内容'));

        $feed_ids = $I->grabDataFromResponseByJsonPath('$.data.feed_id') ;
        $this->feed_id = $feed_ids[0] ;
 
    }

    public function feedUpdate(ApiTester $I)
    {
        
        $I->sendPost( "/feed/update/" . $this->feed_id , [ 
            'text' => '我的第1.5篇内容',
            'images' => json_encode( [ $this->feed_image ] ),
            'token' => $this->token
        ] );
        
        // $this->json( $I );
        
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('code' => '0'));
        $I->seeResponseContainsJson(array('text' => '我的第1.5篇内容'));
 
    }


    public function paidFeedPublish(ApiTester $I)
    {
        
        $I->sendPost( "/feed/publish" , [ 
            'text' => '这是一篇付费内容',
            'is_paid' => 1,
            'groups' => json_encode( [ $this->group_id ] ),
            'images' => json_encode( [ $this->feed_image ] ),
            'token' => $this->token
        ] );
        
        // $this->json( $I );
        
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('code' => '0'));
        $I->seeResponseContainsJson(array('text' => '这是一篇付费内容'));

        $feed_ids = $I->grabDataFromResponseByJsonPath('$.data.feed_id') ;
        $this->paid_feed_id = $feed_ids[0] ;
 
    }

    // 注册第二个账号
    public function Register2(ApiTester $I)
    {
        
        $I->sendPost( "/user/register" , [ 
            'email' => 'fangtang@gmail.com', 
            'username' => 'fangtang', 
            'nickname' => '方糖君', 
            'password' => '******'
        ] );
        
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('email' => 'fangtang@gmail.com'));

    }

    // 登录并获得第二个token
    public function Login2(ApiTester $I)
    {
        
        $I->sendPost( "/user/login" , [ 
            'email' => 'fangtang@gmail.com', 
            'password' => '******'
        ] );
        
        $I->seeResponseCodeIs(200);
        
        $I->seeResponseContainsJson(array('email' => 'fangtang@gmail.com'));
        
        $tokens = $I->grabDataFromResponseByJsonPath('$.data.token') ;
        $this->token2 = $tokens[0] ;

    }

    // 权限测试，读取第一个栏目的list
    public function getGroupFeed(ApiTester $I)
    {
        
        $I->sendPost( "/group/feed/" . $this->group_id , [ 
       'token' => $this->token2
        ] );
        
        // $this->json( $I );
        
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('code' => '40001'));
        $I->seeResponseContainsJson(array('info' => '只有成员才能查看栏目内容'));
        
        
        
        
    }

    public function joinGroup2(ApiTester $I)
    {
        
        $I->sendPost( "/group/join/" . $this->group_id , [ 
            'token' => $this->token2
        ] );
        
        // $this->json( $I );
        
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('code' => '0'));
        $I->seeResponseContainsJson(array('data' => 'done'));
        
        
    }

    // 权限测试，读取第一个栏目的list
    public function getGroupFeed2(ApiTester $I)
    {
        
        $I->sendPost( "/group/feed/" . $this->group_id , [ 
       'token' => $this->token2
        ] );
        
        // $this->json( $I );
        
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('code' => '0'));
        $I->dontSeeResponseContainsJson(array('is_paid' => '1'));
        
    }

    // 自己读自己的栏目，应该能看到
    public function getGroupFeed3(ApiTester $I)
    {
        
        $I->sendPost( "/group/feed/" . $this->group_id , [ 
       'token' => $this->token
        ] );
        
        // $this->json( $I );
        
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('code' => '0'));
        $I->seeResponseContainsJson(array('is_paid' => '1'));
        
    }

    public function saveFeedComment(ApiTester $I)
    {
        
        $I->sendPost( "/feed/comment/" . $this->feed_id , [ 
            'text' => '评论一下',
            'token' => $this->token2
        ] );
        
        //$this->json( $I );
        
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('code' => '0'));
        $I->seeResponseContainsJson(array('text' => '评论一下'));

        
    }
    
    // 免费订户尝试评论付费内容
    public function savePaidFeedComment(ApiTester $I)
    {
        
        $I->sendPost( "/feed/comment/" . $this->paid_feed_id , [ 
            'text' => '评论一下本来看不到的付费内容',
            'token' => $this->token2
        ] );
        
        //$this->json( $I );
        
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('code' => '40001'));
        $I->seeResponseContainsJson(array('info' => '没有权限查看或评论此内容，可使用有权限的账号登入后评论'));

        
    }

    // 免费订户尝试阅读付费内容
    public function  getPaidFeedDetail(ApiTester $I)
    {
        $I->sendPost( "/feed/detail/" . $this->paid_feed_id , [ 
            
            'token' => $this->token2
        ] );
        
        // $this->json( $I );
        
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('code' => '40001'));
        $I->seeResponseContainsJson(array('info' => '该内容为付费内容，仅限VIP订户查看'));
    }

    // getUserDetail
    public function getUserDetail(ApiTester $I)
    {
        
        $I->sendPost( "/user/detail" , [ 
            'token' => $this->token2
        ] );
        
        // $this->json( $I );
        
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('code' => '0'));
        
        $ids = $I->grabDataFromResponseByJsonPath('$.data.id') ;
        $this->uid2 = $ids[0] ;
        
        
    }

    // 成为付费订户
    public function payVip()
    {
        $sql = "UPDATE `group_member` SET `is_vip` =  1 WHERE `group_id` = '" . intval( $this->group_id  ) . "' AND `uid` = '" . intval( $this->uid2 ) . "' LIMIT 1" ;
        db()->runSql( $sql );


    // print_r( db()->getData("SELECT * FROM `group_member` WHERE `group_id` = {$this->group_id} AND `uid` = {$this->uid2}")->toArray() );
    }

    // 付费订户尝试评论付费内容(个人主页上的内容，失败)
    public function savePaidFeedComment2(ApiTester $I)
    {
        
        $I->sendPost( "/feed/comment/" . $this->paid_feed_id , [ 
            'text' => '评论一下付费内容',
            'token' => $this->token2
        ] );
        
        //$this->json( $I );
        
        $I->seeResponseCodeIs(200);
        // $I->seeResponseContainsJson(array('code' => '40001'));
        // $I->seeResponseContainsJson(array('info' => '没有权限查看或评论此内容，可使用有权限的账号登入后评论'));

        
    }

    // 付费订户尝试阅读付费内容(个人主页上的内容，失败)
    public function  getPaidFeedDetail2(ApiTester $I)
    {
        $I->sendPost( "/feed/detail/" . $this->paid_feed_id , [ 
            
            'token' => $this->token2
        ] );
        
        //$this->json( $I );
        
        $I->seeResponseCodeIs(200);
        // $I->seeResponseContainsJson(array('code' => '40001'));
        // $I->seeResponseContainsJson(array('info' => '该内容为付费内容，仅限VIP订户查看'));
    }

    public function O2F()
    {
        $sql = "SELECT `id` FROM `feed` WHERE `forward_feed_id` = {$this->paid_feed_id} AND `forward_group_id` = {$this->group_id} ";
        
        $this->forward_paid_feed_id = db()->getData( $sql )->toVar();
        
    }

    // 付费订户尝试评论付费内容(个人主页上的内容，应成功)
    public function savePaidFeedComment4(ApiTester $I)
    {
        
        $I->sendPost( "/feed/comment/" . $this->forward_paid_feed_id , [ 
            'text' => '评论一下付费内容',
            'token' => $this->token2
        ] );
        
        //$this->json( $I );
        
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('code' => '0'));
        $I->seeResponseContainsJson(array('text' => '评论一下付费内容'));

        
    }

    // 付费订户尝试阅读付费内容(内容，应成功)
    public function  getPaidFeedDetail4(ApiTester $I)
    {
        $I->sendPost( "/feed/detail/" . $this->forward_paid_feed_id , [ 
            
            'token' => $this->token2
        ] );
        
        // $this->json( $I );
        
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('code' => '0'));
        $I->seeResponseContainsJson(array('text' => '这是一篇付费内容'));
    }





    // ========================================================
    /**
     * @skip
     */
    public function Demo(ApiTester $I)
    {
        
        $I->sendPost( "/feed/publish" , [ 
            'text' => '我的第一篇内容',
            // 'groups' => json_encode( [ $this->group_id ] ),
            // 'images' => json_encode( [ $this->feed_image_url ] ),
            'token' => $this->token
        ] );
        
        //$this->json( $I );
        
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('code' => '0'));
        $I->seeResponseContainsJson(array('text' => '我的第一篇内容'));

        $feed_ids = $I->grabDataFromResponseByJsonPath('$.data.feed_id') ;
        $this->feed_id = $feed_ids[0] ;
        
        
    }



}