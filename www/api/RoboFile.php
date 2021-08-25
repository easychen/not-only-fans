<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
    // define public methods as commands
    public function get_trans_key()
    {
        $file = './i18n/jp/translations.json';
        $info = json_decode( file_get_contents( $file ) , 1 );
        
        $ret = '';
        foreach( $info as $key )
        {
            $ret .= "$key\r\n";
        }

        file_put_contents(  './keys.txt' , $ret );
    }

    public function build_contract()
    {
        $this->_exec( 'rm -f '. __DIR__  .'/contract/build/*' );
        $this->_exec( 'solcjs ' . __DIR__ . '/contract/src/LianMiGroupOne.sol -o' . __DIR__  .'/contract/build --bin --abi' );

        $this->_exec( 'mv '. __DIR__  .'/contract/build/*.abi '. __DIR__  .'/contract/build/lianmi.abi' );
        $this->_exec( 'mv '. __DIR__  .'/contract/build/*.bin '. __DIR__  .'/contract/build/lianmi.bin' );
    }

    public function deploy_contract()
    {
        $this->_exec( 'node --harmony ' . __DIR__ . '/contract/deploy.js' );
    }

    public function start()
    {
        $this->_exec( 'cd ' . __DIR__ . '/public && php -S 0.0.0.0:8088 route.php' );
    }

    public function wlan()
    {
        $this->_exec( 'cd ' . __DIR__ . '/public && php -S 192.168.8.144:8088 route.php' );
    }



    /**
     * 启动 web socket 服务
     */
    public function ws()
    {
        $this->_exec( 'php ' . __DIR__ . '/ws.server.php' );
    }


    /**
     * 启用 chrome webdriver （ 验收测试用 ）
     */
    public function chrome()
    {
        $this->_exec('chromedriver --url-base=/wd/hub');
    }

    /**
     * 运行测试
     */
    public function test()
    {
        $this->_exec('codecept run');
    }
    
    /**
     * 运行单元测试
     */
    public function unit()
    {
        $this->_exec('codecept run unit');
    }

    /**
     * 运行单元测试
     */
    public function api()
    {
        $this->_exec("clear");
        $this->_exec('codecept run api');
    }
    
}