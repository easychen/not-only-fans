<?php
namespace Lazyphp\Core;

class LianmiException extends \Exception
{
    protected $message = 'Unknown exception';    
    protected $code    = 0;                      
    protected $info    = "";
    protected $args    = [];                     

    public function __construct($message, $code, $info, $args )
    {
        if (!$message) {
            throw new $this('Unknown '. get_class($this));
        }
        parent::__construct($message, $code);
        $this->info = $info;
        $this->args = $args;
    }
    
    public function __toString()
    {
        return get_class($this) . " '{$this->message}'";
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function getArgs()
    {
        return $this->args;
    }
}