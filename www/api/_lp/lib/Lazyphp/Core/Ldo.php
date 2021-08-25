<?php
namespace Lazyphp\Core;

/**
 * 根据查询的函数名称自动生成查询语句
 * 
 * 使用实例
 *       
 *  $member = new \Lazyphp\Core\Ldo('member');
 *  $member->getAllById('1')->toLine();
 *  $member->getNameById('1')->toVar();
 *  $member->getDataById(array('name','avatar') , 1)->toLine();
 *  $member->getAllByArray(array('name'=>'easy'))->toLine();  
 *  $member->findNameByNothing()->col('name');
 *  $member->findNameByNothingLimit(array(2,5))->col('name'); 
 */
class Ldo extends LmObject
{
    public function __construct( $table )
    {
        $this->table = $table;
        $this->db = db();
    }

    public function __call( $name, $arguments )
    {
        if( !isset($this->table) ) throw new \Exception("LDO未绑定数据表");
        

        $reg = '/(get|find)([A-Z_]+[a-z0-9_]*)By([A-Z_]+[a-z0-9_]*)(Limit)*/s';
        if( preg_match( $reg , $name , $out ) )
        {
            //print_r($out);

            $type =  strtolower( t($out[1]) );
            $select = strtolower( t($out[2]) );

            if( isset($out[3]) )
                $where = strtolower( t($out[3]) );
            else
                $where = '';

            if( isset($out[4]) && strtolower( t($out[4])) == 'limit' )
                $limit = true;
            else
                $limit = false;


            
            switch( $select )
            {
                case 'data':
                    $array = array_shift($arguments);
                    if( is_array($array) )
                    {
                        foreach ($array as $value) 
                        {
                            $select_array[] = '`' . $value . '`'; 
                        }

                        if( isset( $select_array ) )
                            $select_sql = join( ' , ' , $select_array ) ;
                        else
                            $select_sql = ' * ';
                    }
                    else
                        $select_sql = ' * ';

                    break;
                case 'all':
                    $select_sql = ' * ';
                    break;
                default:
                    $select_sql = ' `' . $select . '` ';

                    break;        
            }

            if( ne($where) )
            {
                if( $where == 'nothing' ) 
                    $where_sql = " 1 ";
                elseif( $where == 'array' )
                {
                    $array = array_shift($arguments);
                    if( is_array($array) )
                    {
                        foreach ($array as $key => $value) 
                        {
                            $where_array[] = "`" . $key . "` = '" . s($value) . "'"; 
                        }

                        if( isset( $where_array ) )
                            $where_sql =  join( ' AND ' ,  $where_array ) ;
                        else
                            $where_sql = ' 1 ';
                    }
                    else
                        $where_sql = ' 1 ';
                } 
                else
                {
                    $value = array_shift($arguments);
                    $where_sql = " `" . $where . "` = '" . s( $value ) . "' ";
                }
                    
            }
            

            if( $limit )
            {
                if($limit_info = array_shift($arguments))
                {
                    if( is_array( $limit_info ) ) 
                        $limit_sql = " LIMIT ".$limit_info[0] . " , " . $limit_info[1];
                    elseif( ne( $limit_info  ) ) $limit_sql = " LIMIT ".$limit_info;
                    else $limit_sql = " ";
                }
                else $limit_sql = " ";
            }else $limit_sql = " ";

            $sql = "SELECT {$select_sql} FROM `{$this->table}` WHERE {$where_sql} {$limit_sql}";
            //echo $sql . "<br/>";
            return $this->db->getData($sql);       

        }
        else throw new \Exception("Method not exists".$name);
        
        
    }

    public function runSql()
    {
        $args = func_get_args();
        return call_user_func_array(array($this->db, 'runSql'),$args );
    }

    public function lastId()
    {
        $args = func_get_args();
        return call_user_func_array(array($this->db, 'lastId'),$args );
    }

    public function getData()
    {
        $args = func_get_args();
        return call_user_func_array(array($this->db, 'getData'),$args );
    }
}