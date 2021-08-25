<?php
namespace Lazyphp\Core;
use \PDO as PDO;

class Database extends LmObject
{
    var $result = false;

    public function __construct( $dsn = null , $user = null , $password = null )
    {
        if( is_object( $dsn ) && strtolower(get_class( $dsn )) == 'pdo' )
        {
            $this->pdo = $dsn;
        }
        else
        {
            if( $dsn == null )
            {
                // if( is_devmode() )
                // {
                //     $dsn = c('database_dev','dsn');
                //     $user = c('database_dev','user');
                //     $password = c('database_dev','password');
                // }
                // else
                // {
                    $dsn = c('database','dsn');
                    $user = c('database','user');
                    $password = c('database','password');
                // }
                
                
            }
            $this->pdo = new PDO( $dsn , $user , $password );
        }

        if( is_devmode() )
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->pdo->exec("SET NAMES 'utf8mb4';");
    }

    // get data to result set

    public function getData( $sql )
    {
        $args = func_get_args();
        array_unshift($args, 'getdata');
        return call_user_func_array(array($this, 'bindData'),$args );
    }




    public function runSql()
    {
        $args = func_get_args();
        array_unshift($args, 'runsql');
        return call_user_func_array(array($this, 'bindData'),$args );
    }

    /**
     * bindData 用于处理带绑定支持的SQL
     * 第一个参数为 TYPE ， 当 TYPE = getdata 时，产生返回内容。否则为执行语句。
     */
    protected function bindData()
    {
        $this->result=false;
        $arg_num = func_num_args();
        $arg_num = $arg_num - 1;
        $args = func_get_args();
        $type = array_shift($args);

        if( $arg_num < 1 )
        {
            throw new \PdoException("NO SQL PASSBY");
            return $this;
        }
        else
        {
            if( $arg_num == 1 )
            {
                $sql = $args[0];
            }
            else
            {
                // 绑定

                $sql = array_shift($args);
                if( $params = get_bind_params($sql) )
                {
                    //$sth = $this->pdo->prepare( $sql );
                    $meta = $GLOBALS['meta'][$GLOBALS['meta_key']];

                    if( isset( $meta['table'][0]['fields'] ) )
                        $fields = $meta['table'][0]['fields'];

                    $replace = array();

                    foreach( $params as $param )
                    {
                        $value = array_shift( $args );
                        if( isset( $fields[$param] ) && type2pdo($fields[$param]['type']) == PDO::PARAM_INT )
                        {

                            $replace[':'.$param] = intval($value);
                            //$sth->bindValue(':'.$param, $value , type2pdo($fields[$param]['type']));
                        }
                        else
                        {
                            $replace[':'.$param] = "'" . s($value) . "'";
                            //$sth->bindValue(':'.$param, $value , PDO::PARAM_STR);
                        }
                    }

                    $sql = str_replace( array_keys($replace), array_values($replace), $sql );
                }
            }

            if( 'getdata' == $type )
            {
                foreach( $this->pdo->query( $sql , PDO::FETCH_ASSOC ) as $item )
                {

                    if( is_array($this->result) ) $this->result[] = $item;
                    else $this->result = array( '0' => $item );
                }

            }
            else
            {
                $this->result = $this->pdo->exec( $sql );
            }

            //print_r( $this->result );



            return $this;
        }

         return $this;

    }



    // export
    public function toLine()
    {
        if( !isset($this->result) ) return false;

        $ret = $this->result;
        $this->result = null;
        return first($ret);
    }

    public function toVar( $field = null )
    {
        if( !isset($this->result) ) return false;

        $ret = $this->result;
        $this->result = null;

        if( $field == null )
            return first(first($ret));
        else
            return isset($ret[0][$field])?$ret[0][$field]:false;
    }

    public function toArray()
    {
        if( !isset($this->result) ) return false;

        $ret = $this->result;
        $this->result = null;
        return $ret;
    }

    public function col( $name )
    {
        return $this->toColumn($name);
    }

    public function toColumn( $name )
    {
        if( !isset($this->result) ) return false;

        $rs = $this->result;
        $this->result = null;

        if( !isset( $rs ) || !is_array($rs) ) return false;
        foreach( $rs as $line )
            if( isset($line[$name]) ) $ret[] = $line[$name];

        return isset($ret)?$ret:false;
    }

    public function index( $name )
    {
        return $this->toIndexedArray($name);
    }

    public function toIndexedArray( $name )
    {
        if( !isset($this->result) ) return false;

        $rs = $this->result;
        $this->result = null;

        if( !isset( $rs ) || !is_array($rs) ) return false;
        foreach( $rs as $line )
            $ret[$line[$name]] = $line;

        return isset($ret)?$ret:false;
    }

    public function quote( $string )
    {
        return $this->pdo->quote( $string );
    }

    public function lastId()
    {
        return $this->pdo->lastInsertId();
    }






}
