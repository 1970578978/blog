<?php
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use FastRoute\RouteCollector;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
//        $this->setGlobalMode(true);                 //只有关闭这个才能往后传值

        $routeCollector->get('/', '/Index');
        $routeCollector->get('/test', '/Index/test');



//        $routeCollector->get('/user', '/inde.html');
//        $routeCollector->get('/rpc', '/Rpc/index');
//        $routeCollector->get('/a', '/index/index');
//        $routeCollector->get('/', function (Request $request, Response $response) {
//            $response->write('this router index');
//        });
//        $routeCollector->get('/test', function (Request $request, Response $response) {
//            $response->write('this router test');
//            return '/Test';//重新定位到/a方法
//        });
//        $routeCollector->get('/user/{id:\d+}', function (Request $request, Response $response) {
//            $response->write("this is router user ,your id is {$request->getQueryParam('id')}");//获取到路由匹配的id
//            return false;//不再往下请求,结束此次响应
//        });

    }
}