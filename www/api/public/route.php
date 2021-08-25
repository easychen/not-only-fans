<?php

// 处理URL中包含+号的情况
$_SERVER["PHP_SELF"] = str_replace(' ', '+', $_SERVER["PHP_SELF"]  );

$path = $_SERVER['DOCUMENT_ROOT'] . $_SERVER["PHP_SELF"];
$uri = $_SERVER["REQUEST_URI"];

//print_r( $_SERVER );

// 如果文件和目录存在，直接访问
if (file_exists($path))  
{
    $path2 = pathinfo($_SERVER["SCRIPT_FILENAME"]);


	$header = false;
	if( $path2['extension'] == 'ttf' ) $header = 'application/font-sfnt';
	if( $path2['extension'] == 'eot' ) $header = 'application/vnd.ms-fontobject';
	if( $path2['extension'] == 'woff' ) $header = 'application/font-woff';

	if( $header )
	{
		header("Content-Type: ".$header);
    	readfile($_SERVER["SCRIPT_FILENAME"]);
	}
	else
    	return false;
}
else
{

	putenv("REQUEST_URI=".$_SERVER["REQUEST_URI"]);
	
	//if( $_SERVER["REQUEST_URI"] == '/misswords' )
		header("Access-Control-Allow-Origin: *");
	
	require  __DIR__ . '/index.php';
}



