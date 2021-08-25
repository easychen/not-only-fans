<?php
/**
 * This file is part of the php-apidoc package.
 */
namespace Lazyphp\Doc;

use Crada\Apidoc\Exception;

/**
 * Class imported from https://github.com/eriknyk/Annotations
 * @author  Erik Amaru Ortiz https://github.com/eriknyk‎
 *
 * @license http://opensource.org/licenses/bsd-license.php The BSD License
 * @author  Calin Rada <rada.calin@gmail.com>
 */
class Extractor
{
    /**
     * Static array to store already parsed annotations
     * @var array
     */
    private static $annotationCache;

    /**
     * Indicates that annotations should has strict behavior, 'false' by default
     * @var boolean
     */
    private $strict = false;

    /**
     * Stores the default namespace for Objects instance, usually used on methods like getMethodAnnotationsObjects()
     * @var string
     */
    public $defaultNamespace = '';

    /**
     * Sets strict variable to true/false
     * @param bool $value boolean value to indicate that annotations to has strict behavior
     */
    public function setStrict($value)
    {
        $this->strict = (bool) $value;
    }

    /**
     * Sets default namespace to use in object instantiation
     * @param string $namespace default namespace
     */
    public function setDefaultNamespace($namespace)
    {
        $this->defaultNamespace = $namespace;
    }

    /**
     * Gets default namespace used in object instantiation
     * @return string $namespace default namespace
     */
    public function getDefaultAnnotationNamespace()
    {
        return $this->defaultNamespace;
    }

    /**
     * Gets all anotations with pattern @SomeAnnotation() from a given class
     *
     * @param  string $className class name to get annotations
     * @return array  self::$annotationCache all annotated elements
     */
    public static function getClassAnnotations($className)
    {
        if (!isset(self::$annotationCache[$className])) {
            $class = new \ReflectionClass($className);
            self::$annotationCache[$className] = self::parseAnnotations($class->getDocComment());
        }

        return self::$annotationCache[$className];
    }

    public static function getAllClassAnnotations($className)
    {
        $class = new \ReflectionClass($className);

        foreach ($class->getMethods() as $object) {
            if ($object->class == $className) {
                self::$annotationCache['annotations'][$className][$object->name] = self::getMethodAnnotations($className, $object->name);
            }
        }

        return self::$annotationCache['annotations'];
    }


    /**  ↓ ↓ ↓ ↓ ↓ ** change for extending LP4 *************************************************/

    /**
     * Gets all anotations with pattern @SomeAnnotation() from a determinated method of a given class
     *
     * @param  string $className  class name
     * @param  string $methodName method name to get annotations
     * @return array  self::$annotationCache all annotated elements of a method given
     */
    public static function getMethodAnnotations($className, $methodName)
    {
        // 修改原有的注释
        // 根据meta将注释重新展开为apidoc格式
        
        // 如果未命中缓存
        if (!isset(self::$annotationCache[$className . '::' . $methodName])) 
        {
            try 
            {
                $method = new \ReflectionMethod($className, $methodName);
                $doccoment = $method->getDocComment();
                
                $doctext = substr($doccoment, 3, -2);

                
                $reg = '/@ApiLazyRoute\(.+\)/';
                if( preg_match( $reg , $doctext , $out ) )
                {
                    $old = $out[0];

                    //echo "\r\n\r\n ========== \r\n " . $old;
                    //echo "\r\n\r\n ========== \r\n " ;

                    //eval( '$auto_params = array( ' . $out[1] . ');' );
                    //print_r( $auto_params );

                    $key = cmkey( $className, $methodName );
                    if( isset( $GLOBALS['meta'] ) && isset( $GLOBALS['meta'][$key] ) )
                    {
                       
                        $meta = $GLOBALS['meta'][$key];

                        $routeinfo = $meta['LazyRoute'][0];

                        //print_r( $routeinfo );

                        $replace = "@ApiMethod".$routeinfo['ApiMethod'] . "\r\n * " . "@ApiRoute".$routeinfo['ApiRoute'];
                        
                        //echo $replace;
                        $doctext = str_replace( $old , $replace,  $doctext);
                        $doccoment = "/**".PHP_EOL . $doctext . "*/";
                    }
                }
                
                /*
                // 检查是否有 apiRequiredParams
                $reg = '/@ApiRequiredParams\((.+?)\)/is';
                if( preg_match( $reg , $doctext , $out ) )
                {
                    eval( '$require_params = array( ' . $out[1] . ');' );
                }
                else $require_params = array();

                //print_r( $require_params );

                // 检查是否有 apiAutoParams
                // @apiAutoParams('id','name','email')
                $reg = '/@ApiAutoParams\((.+?)\)/is';
                if( preg_match( $reg , $doctext , $out ) )
                {
                    $old = $out[0];
                    eval( '$auto_params = array( ' . $out[1] . ');' );
                    //print_r( $auto_params );

                    $key = cmkey( $className, $methodName );
                    if( isset( $GLOBALS['meta'] ) && isset( $GLOBALS['meta'][$key] ) )
                    {
                        $meta = $GLOBALS['meta'][$key];
                        $fields = $meta['table'][0]['fields'];

                        foreach( $auto_params as $aparam )
                        {
                            if( isset( $fields[$aparam] ) )
                            {
                                $f = $fields[$aparam];

                                if( in_array( $f['name'] , $require_params ) ) $null = 'false';
                                else $null = 'true' ;

                                $to_array[] = '@ApiParams(name="'. $f['name'] .'", type="' . $f['type'] . '", nullable='. $null .', description="' . $f['comment'] . '")';
                            }
                        }

                        if( isset( $to_array ) )
                        {
                            $replace = join( "\r\n * " , $to_array );
                            $doctext = str_replace( $old , $replace,  $doctext);
                            $doccoment = "/**\r\n" . $doctext . "*"."/";
                        }
                        
                    }
                }
                */

                //echo $doccoment;

                $annotations = self::parseAnnotations($doccoment);

                
                

            }
            catch (\ReflectionException $e) 
            {
                $annotations = array();
            }

            self::$annotationCache[$className . '::' . $methodName] = $annotations;
        }

        return self::$annotationCache[$className . '::' . $methodName];
    }

    protected function  isNull( $notnull )
    {
        if( $notnull ) return "false";
        else return "true";
    }

    /**  ↑ ↑ ↑ ↑ ↑ ** change for extending LP4 *************************************************/

    /**
     * Gets all anotations with pattern @SomeAnnotation() from a determinated method of a given class
     * and instance its abcAnnotation class
     *
     * @param  string $className  class name
     * @param  string $methodName method name to get annotations
     * @return array  self::$annotationCache all annotated objects of a method given
     */
    public function getMethodAnnotationsObjects($className, $methodName)
    {
        $annotations = $this->getMethodAnnotations($className, $methodName);
        $objects     = array();

        $i = 0;

        foreach ($annotations as $annotationClass => $listParams) {
            $annotationClass = ucfirst($annotationClass);
            $class = $this->defaultNamespace . $annotationClass . 'Annotation';

            // verify is the annotation class exists, depending if Annotations::strict is true
            // if not, just skip the annotation instance creation.
            if (! class_exists($class)) {
                if ($this->strict) {
                    throw new Exception(sprintf('Runtime Error: Annotation Class Not Found: %s', $class));
                } else {
                    // silent skip & continue
                    continue;
                }
            }

            if (empty($objects[$annotationClass])) {
                $objects[$annotationClass] = new $class();
            }

            foreach ($listParams as $params) {
                if (is_array($params)) {
                    foreach ($params as $key => $value) {
                        $objects[$annotationClass]->set($key, $value);
                    }
                } else {
                    $objects[$annotationClass]->set($i++, $params);
                }
            }
        }

        return $objects;
    }

    /**
     * Parse annotations
     *
     * @param  string $docblock
     * @return array  parsed annotations params
     */
    private static function parseAnnotations($docblock)
    {
        $annotations = array();

        // Strip away the docblock header and footer to ease parsing of one line annotations
        $docblock = substr($docblock, 3, -2);

        if (preg_match_all('/@(?<name>[A-Za-z_-]+)[\s\t]*\((?<args>.*)\)[\s\t]*\r?$/m', $docblock, $matches)) {
            $numMatches = count($matches[0]);

            for ($i = 0; $i < $numMatches; ++$i) {
                // annotations has arguments
                if (isset($matches['args'][$i])) {
                    $argsParts = trim($matches['args'][$i]);
                    $name      = $matches['name'][$i];
                    $value     = self::parseArgs($argsParts);
                } else {
                    $value = array();
                }

                $annotations[$name][] = $value;
            }

        }

        return $annotations;
    }

    /**
     * Parse individual annotation arguments
     *
     * @param  string $content arguments string
     * @return array  annotated arguments
     */
    private static function parseArgs($content)
    {
        $data  = array();
        $len   = strlen($content);
        $i     = 0;
        $var   = '';
        $val   = '';
        $level = 1;

        $prevDelimiter = '';
        $nextDelimiter = '';
        $nextToken     = '';
        $composing     = false;
        $type          = 'plain';
        $delimiter     = null;
        $quoted        = false;
        $tokens        = array('"', '"', '{', '}', ',', '=');

        while ($i <= $len) {
            $c = substr($content, $i++, 1);

            //if ($c === '\'' || $c === '"') {
            if ($c === '"') {
                $delimiter = $c;
                //open delimiter
                if (!$composing && empty($prevDelimiter) && empty($nextDelimiter)) {
                    $prevDelimiter = $nextDelimiter = $delimiter;
                    $val           = '';
                    $composing     = true;
                    $quoted        = true;
                } else {
                    // close delimiter
                    if ($c !== $nextDelimiter) {
                        throw new Exception(sprintf(
                            "Parse Error: enclosing error -> expected: [%s], given: [%s]",
                            $nextDelimiter, $c
                        ));
                    }

                    // validating sintax
                    if ($i < $len) {
                        if (',' !== substr($content, $i, 1)) {
                            throw new Exception(sprintf(
                                "Parse Error: missing comma separator near: ...%s<--",
                                substr($content, ($i-10), $i)
                            ));
                        }
                    }

                    $prevDelimiter = $nextDelimiter = '';
                    $composing     = false;
                    $delimiter     = null;
                }
            } elseif (!$composing && in_array($c, $tokens)) {
                switch ($c) {
                    case '=':
                        $prevDelimiter = $nextDelimiter = '';
                        $level     = 2;
                        $composing = false;
                        $type      = 'assoc';
                        $quoted = false;
                        break;
                    case ',':
                        $level = 3;

                        // If composing flag is true yet,
                        // it means that the string was not enclosed, so it is parsing error.
                        if ($composing === true && !empty($prevDelimiter) && !empty($nextDelimiter)) {
                            throw new Exception(sprintf(
                                "Parse Error: enclosing error -> expected: [%s], given: [%s]",
                                $nextDelimiter, $c
                            ));
                        }

                        $prevDelimiter = $nextDelimiter = '';
                        break;
                    case '{':
                        $subc = '';
                        $subComposing = true;

                        while ($i <= $len) {
                            $c = substr($content, $i++, 1);

                            if (isset($delimiter) && $c === $delimiter) {
                                throw new Exception(sprintf(
                                    "Parse Error: Composite variable is not enclosed correctly."
                                ));
                            }

                            if ($c === '}') {
                                $subComposing = false;
                                break;
                            }
                            $subc .= $c;
                        }

                        // if the string is composing yet means that the structure of var. never was enclosed with '}'
                        if ($subComposing) {
                            throw new Exception(sprintf(
                                "Parse Error: Composite variable is not enclosed correctly. near: ...%s'",
                                $subc
                            ));
                        }

                        $val = self::parseArgs($subc);
                        break;
                }
            } else {
                if ($level == 1) {
                    $var .= $c;
                } elseif ($level == 2) {
                    $val .= $c;
                }
            }

            if ($level === 3 || $i === $len) {
                if ($type == 'plain' && $i === $len) {
                    $data = self::castValue($var);
                } else {
                    $data[trim($var)] = self::castValue($val, !$quoted);
                }

                $level = 1;
                $var   = $val = '';
                $composing = false;
                $quoted = false;
            }
        }

        return $data;
    }

    /**
     * Try determinate the original type variable of a string
     *
     * @param  string  $val  string containing possibles variables that can be cast to bool or int
     * @param  boolean $trim indicate if the value passed should be trimmed after to try cast
     * @return mixed   returns the value converted to original type if was possible
     */
    private static function castValue($val, $trim = false)
    {
        if (is_array($val)) {
            foreach ($val as $key => $value) {
                $val[$key] = self::castValue($value);
            }
        } elseif (is_string($val)) {
            if ($trim) {
                $val = trim($val);
            }

            $tmp = strtolower($val);

            if ($tmp === 'false' || $tmp === 'true') {
                $val = $tmp === 'true';
            } elseif (is_numeric($val)) {
                return $val + 0;
            }

            unset($tmp);
        }

        return $val;
    }
}
