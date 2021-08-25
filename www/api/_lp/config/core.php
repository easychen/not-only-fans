<?php
$GLOBALS['lpconfig']['app_name'] = 'LazyPHP4';
$GLOBALS['lpconfig']['lp_version'] = '0.1';
$GLOBALS['lpconfig']['route_file'] = AROOT . 'compiled' . DS .'route.php';
$GLOBALS['lpconfig']['default_string_filter_func'] = 'z';

$GLOBALS['lpconfig']['error_type'] = [
    'INPUT'=>20001,
    'AUTH'=>40001,
    'NOTLOGIN'=>40301
];
