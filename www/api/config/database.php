<?php
if (getenv('WEB_ALIAS_DOMAIN')) {
    $GLOBALS['lpconfig']['database'] = array(
        'adapter' => 'mysql',
        'host' => 'mariadb',
        'name' => 'notonlyfans',
        'user' => 'root',
        'password' => '',
        'port' => 3306,
        'charset' => 'utf8mb4'
    );
} else {
    $GLOBALS['lpconfig']['database'] = array(
        'adapter' => 'mysql',
        'host' => '127.0.0.1',
        'name' => 'notonlyfans',
        'user' => 'root',
        'password' => '',
        'port' => 3306,
        'charset' => 'utf8mb4'
    );
}




$GLOBALS['lpconfig']['database']['dsn'] = $GLOBALS['lpconfig']['database']['adapter']
                                          .':host=' . $GLOBALS['lpconfig']['database']['host']
                                          . ';port=' . $GLOBALS['lpconfig']['database']['port']
                                          . ';dbname=' . $GLOBALS['lpconfig']['database']['name']
                                          . ';charset=' . $GLOBALS['lpconfig']['database']['charset'];
