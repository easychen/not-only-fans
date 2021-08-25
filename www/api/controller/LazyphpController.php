<?php
namespace Lazyphp\Controller;

class LazyphpController
{
	public function __construct()
    {
        
    }

    /**
     * Demo接口
     * @ApiDescription(section="Demo", description="乘法接口")
     * @ApiLazyRoute(uri="/",method="GET")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function index()
    {
        // return send_result( db()->getData("show tables")->toArray() );
    }

    /**
     * 翻译接口
     * @ApiDescription(section="translate", description="翻译接口")
     * @ApiLazyRoute(uri="/misswords",method="POST|GET")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function misswords()
    {
        $langs = json_decode( v('lng') , 1 );
        $ns = t(v('ns'));
        $key = t(v('key'));

        if( strlen( $ns ) < 1 || strlen( $key ) < 1 || !is_array( $langs ) ) return send_error(  "INPUT" , "错误的输入"  );
        
        foreach( $langs as $lang )
        {
            if( in_array( $lang , ['zh-cn','en','jp'] ) )
            {
                $dir = AROOT . 'i18n'. DS . $lang ;
                @mkdir( $dir , 0777 , true );
                $file = $dir . DS . $ns . '.json';
                if( file_exists( $file ) )
                {
                    $data = json_decode( file_get_contents( $file ));
                }
                else
                {
                    $data = new \stdClass();
                }

                $data->$key = $key;

                $output = json_encode( $data , JSON_UNESCAPED_UNICODE );
                file_put_contents( $file , $output );
                
            }    
        }

        return send_result( "saved" . time() );
        
        //file_put_contents( './trans.txt' , print_r( $_REQUEST , 1 ) , FILE_APPEND );
    }
    
    /**
     * Demo接口
     * @ApiDescription(section="Demo", description="乘法接口")
     * @ApiLazyRoute(uri="/demo/times",method="GET")
     * @ApiParams(name="first", type="string", nullable=false, description="first", check="check_not_empty", cnname="第一个数")
     * @ApiParams(name="second", type="string", nullable=false, description="second", check="check_not_empty", cnname="第二个数")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function demo($first,$second)
    {
        send_result(intval($first)*intval($second));
    }

    /**
     * Demo接口
     * @ApiDescription(section="Demo", description="乘法接口")
     * @ApiLazyRoute(uri="/test",method="GET")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function test()
    {
        $text = '@ft002@ft004 测试下这个~';
        if( $mention = lianmi_at( $text ) )
        {
            $mention = array_slice( $mention , 0 , c('max_mention_per_comment') );
            $mention_string = array_map( function( $item ){ return "'" . $item ."'"; } , $mention ); 
            print_r( $mention_string );
            
            if( is_array( $mention_string ) && count( $mention_string ) > 0 )
            {
                echo $sql = "SELECT `id` FROM `user` WHERE `username` IN ( " . join( ',' , $mention_string ) . " )";

                if( $mention_uids = db()->getData( $sql )->toColumn('id'))
                print_r( $mention_uids );
                    // foreach( $mention_uids as $muid )
                    // {
                    //     // 不要给自己和内容作者发at通知，因为ta已经会收到通知了
                    //     if( $muid != lianmi_uid() && $muid != $ouid )
                    //         system_notice( $muid , lianmi_uid() ,  lianmi_username() , lianmi_nickname() , '在评论中@了你' , '/feed/'.$feed['id']  );
                    // }   
            }
        }
    }

}
