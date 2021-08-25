<?php
namespace Lazyphp\Core;

use flight\net\Request,flight\net\Response,flight\net\Router;
use \Exception as Exception;
//use flight\util\Collection;
require_once FROOT . 'lib' . DS . 'flight' . DS . 'net' . DS . 'Request.php';
require_once FROOT . 'lib' . DS . 'flight' . DS . 'net' . DS . 'Response.php';
require_once FROOT . 'lib' . DS . 'flight' . DS . 'net' . DS . 'Router.php';
require_once FROOT . 'lib' . DS . 'flight' . DS . 'net' . DS . 'Route.php';
require_once FROOT . 'lib' . DS . 'flight' . DS . 'util' . DS . 'Collection.php';


class Application
{
    public function __construct()
    {
        // 初始化对象

        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router();
        $this->dispatcher = new Dispatcher(); // LP自己的Dispatcher

    }

    public function run()
    {
        $dispatched = false;

        if ($this->request->ajax)
        {
            $this->response->cache(false);
        }

        while ($route = $this->router->route($this->request))
        {
            $params = array_values($route->params);
            //TODO 修复参数顺序和meta不一致

            $continue = $this->dispatcher->execute(
                $route->callback,
                $params
            );

            $dispatched = true;

            if (!$continue) break;

            $this->router->next();
        }

        if (!$dispatched) {
            $this->notFound();
        }

    }

    public function notFound()
    {
        $code=404;
        if('OPTIONS'==$_SERVER['REQUEST_METHOD'])
        {
            //ajax跨域请求之前的options请求，为了避免如上传失败，返回200
            $code=200;
        }
        $this->response
            ->status($code)
            ->write(
                '<h1>404 Not Found</h1>'.
                '<h3>The page you have requested could not be found.</h3>'.
                str_repeat(' ', 512)
            )
            ->send();
    }

    public function route($pattern, $callback)
    {
        $this->router->map($pattern, $callback);
    }
}
