<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
    // define public methods as commands
    public function devApi()
    {
        $this->_exec("cd api/public && php -S 0.0.0.0:8088 route.php");
    }
    
    public function devClient()
    {
        $this->_exec("cd client && yarn start");
    }
}
