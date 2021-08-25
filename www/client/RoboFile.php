<?php

define( "SRC" , __DIR__ . '/src' );

class RoboFile extends \Robo\Tasks
{
    // define public methods as commands
    /**
     * 创建一个新的页面
     */
    public function newScreen( $name = null )
    {
        return $this->copy_template( $name , 'screen' );
    }

    /**
     * 创建一个新的组件
     */
    public function newComponent( $name = null )
    {
        return $this->copy_template( $name , 'component' );
    }

    /**
     * 保存当前项目开发进度到gitlab
     */
    public function save( $note = null )
    {
        if( $note === null )
            $note = $this->askDefault( '请输入版本信息', date("Y-m-d").'快速保存' );

        // dump mysql 
        $this->_exec( 'mysqldump lianmi -uroot --no-data=true > ~/Code/gitcode/lianmiapi/sql/lianmi.sql' );
        
        
        foreach( ['lianmiapi' , 'lianmiweb'] as $project  )
        {
            $this->taskExecStack()
            ->exec('cd ~/Code/gitcode/'.$project)
            ->exec('git add .')
            ->exec('git commit -m "' . $note . '"')
            ->exec('git push -u origin master')
            ->run();
        }
        
        $this->say("运行完成 😋 ");
        
    }

    public function online( $type = 'all' )
    {
        if( $type == 'api' )
        {
            $this->push_to_online('api');  
        }
        elseif( $type == 'web' )
        {
            $this->push_to_online('web');
        }
        else
        {
            $this->push_to_online('api'); 
            $this->push_to_online('web');
        }
    }

    private function push_to_online( $type )
    {
        if( $type == 'api' )
            $this->_exec( 'cd  ~/Code/gitcode/lianmiapi && git push online master' );

        if( $type == 'web' )
            $this->_exec( 'cd  ~/Code/gitcode/lianmiweb && git push online master' );    
        

    }

    private function copy_template( $name , $type = 'component' )
    {
        
        $type = basename( $type );
        if( $type != 'component' ) $type = 'screen';
        
        if( $name === null ) $name =$this->ask("请输入组件名称");
        if( strlen( $name ) < 1 )
        {
            $this->say("错误组件的名称");
            return false;
        } 

        $file_path = SRC . '/' . $type . '/'. ucfirst( $name ) . '.js';

        if( file_exists( $file_path ) )
        {
            $this->say("组件已存在");
            return false;
        }
        
        $file_tmp = SRC .'/_template/'. $type .'.js';
        if( !file_exists( $file_tmp ) )
        {
            $this->say("模板文件 $file_tmp 不存在");
            return false;
        }

        $content = file_get_contents( $file_tmp );
        $content = str_replace( 'ClassNamePlaceHolder' ,  ucfirst( $name ) , $content);

        file_put_contents( $file_path , $content );
        
        if( $type == 'component' ) $path = '..';
        else $path = '.';
        $this->_exec(" echo \"import " . $name . " from '" . $path . "/" . $type . "/" . $name . "'; \" | pbcopy");

        $this->say( "组件初始化完成，import 语句已经复制到剪贴板" );

    }
}