<?php
namespace Lazyphp\Core;
use \PDO as PDO;


// Based on  Aura.Sql_Schema
// Rewrite for adding comment field 
class Datameta extends LmObject
{
    public function __construct( $pdo )
    {
        $this->db = new Database($pdo);
        $this->quote_name_prefix = "`";
        $this->quote_name_suffix = "`";
    }

    public function getFields($table)
    {
        if( !isset($this->fields) )
            $this->getTableCols( $table );

        return $this->fields;
    }

    public function getTableCols( $table )
    {
        $table = $this->quoteName($table);
        $sql = "SHOW FULL COLUMNS FROM " . $table . " " ;
        if( $data = $this->db->getData($sql)->toArray() )
            foreach( $data as $item )
            {
                $name = $item['Field'];
                $default = $this->getDefault($item['Default']);
                list($type, $size, $scale) = $this->getTypeSizeScope($item['Type']);

                $array = array
                (
                    'name' => $name,
                    'default' => $default,
                    'type' => $type ,
                    'size' => ($size  ? (int) $size  : null) ,
                    'scale' => ($scale ? (int) $scale : null) ,
                    'notnull' => (bool) ($item['Null'] != 'YES') ,
                    'auto' => (bool) (strpos($item['Extra'], 'auto_increment') !== false) ,
                    'primary' => (bool) ($item['Key'] == 'PRI'),
                    'comment' => $item['Comment'] ,
                );

                $ret[$name] = $array;
                $this->fields[] = $name;
            }

            
        return isset( $ret ) ? $ret : false;
    }

    protected function getDefault($default)
    {
        $upper = strtoupper($default);
        if ($upper == 'NULL' || $upper == 'CURRENT_TIMESTAMP') {
            // the only non-literal allowed by MySQL is "CURRENT_TIMESTAMP"
            return null;
        } else {
            // return the literal default
            return $default;
        }
    }

    protected function getTypeSizeScope($spec)
    {
        $spec  = strtolower($spec);
        $type  = null;
        $size  = null;
        $scale = null;

        // find the parens, if any
        $pos = strpos($spec, '(');
        if ($pos === false) {
            // no parens, so no size or scale
            $type = $spec;
        } else {
            // find the type first.
            $type = substr($spec, 0, $pos);

            // there were parens, so there's at least a size.
            // remove parens to get the size.
            $size = trim(substr($spec, $pos), '()');

            // a comma in the size indicates a scale.
            $pos = strpos($size, ',');
            if ($pos !== false) {
                $scale = substr($size, $pos + 1);
                $size  = substr($size, 0, $pos);
            }
        }

        return array($type, $size, $scale);
    }

    protected function splitName($name)
    {
        $pos = strpos($name, '.');
        if ($pos === false) {
            return array(null, $name);
        } else {
            return array(substr($name, 0, $pos), substr($name, $pos+1));
        }
    }

    public function quoteName($name)
    {
        // remove extraneous spaces
        $name = trim($name);

        // "name"."name"
        $pos = strrpos($name, '.');
        if ($pos) {
            $one = $this->quoteName(substr($name, 0, $pos));
            $two = $this->quoteName(substr($name, $pos + 1));
            return "{$one}.{$two}";
        }

        // "name"
        return $this->quote_name_prefix . $name . $this->quote_name_suffix;
    }
}