<?php

$GLOBALS['lpconfig']['mode'] = 'dev';
require( '_loader.php' );


function _t( $string )
{
    if( !isset( $GLOBALS['WORDS'] ) )
    {
        $GLOBALS['WORDS'] = json_decode( file_get_contents( '/Users/Easy/Code/gitcode/lianmiweb/public/locales/en/translations.json' )  , true );
    }

    return isset( $GLOBALS['WORDS'][$string] ) ? $GLOBALS['WORDS'][$string] : $string;
}


class LoginCest 
{    
    
    public function _before(AcceptanceTester $I)
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
    }


// 
//     public function loginSuccessfully(AcceptanceTester $I)
//     {
//         // 用户注册并登入
//         $I->amOnPage( '/register' );

//         $I->fillField( '#email', 'easychen@gmail.com' );
//         $I->fillField( '#nickname', 'Easy' );
//         $I->fillField( '#username', 'easychen' );
//         $I->fillField( '#password', '******' );
//         $I->fillField( '#password2', '******' );
//         $I->click( '#lm-register-btn');
//         $I->waitForElementVisible('#lm-login-btn', 30); // secs
//         $I->click( '#lm-login-btn');

//         // 修改个人资料
//         $I->executeJS( 'history.replaceState({}, "profile", "/settings/profile");' );
//         // $I->amOnPage( '/settings/profile' );
//         $I->fillField( '#nickname', 'EasyChen' );

//         $I->click( '#lm-profile-update-btn');

//         $I->wait( 500);
        
//     }
    
//     public function loginWithInvalidPassword(AcceptanceTester $I)
//     {
//         // write a negative login test
//     }       
// 
}