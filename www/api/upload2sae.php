<?php
if( !isset($argv)  ) die('Please run it via commandline');

$jump = false;

$action  = basename(trim($argv[1]));
if( !empty( $action ) )
{
    if( $action == '-fast' ) $jump = true; ;
}

echo "Update route file ...";
echo shell_exec('php _build.php');
echo " Done ".PHP_EOL;
/*
if( !$jump )
{
    echo "Update composer ...";
    echo shell_exec('composer update');
    echo " Done ".PHP_EOL;
}

echo "Run unittest  ...";
echo shell_exec('phpunit --colors');
echo " Done ".PHP_EOL;

echo "Run behat  ...";
echo shell_exec('vendor/bin/behat');
echo " Done ".PHP_EOL;
*/
echo "Check local changes ...".PHP_EOL;
echo shell_exec('svn st');

echo "Send to SAE via SVN...".PHP_EOL;
//if( !$comment = want("No svn comment? (Yes) ") ) $comment = 'update';
$comment = 'update';
echo shell_exec('svn ci -m "' . $comment . '" ');

function want( $str )
{
    echo $str ;
    return trim(fgets(STDIN));
}
