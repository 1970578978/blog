<?php
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use FastRoute\RouteCollector;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\Http\Message\Status;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
       $this->setGlobalMode(true);                 //只有关闭这个才能往后传值

        $routeCollector->get('/', '/Index');
        $routeCollector->get('/test', '/Index/test');
        $routeCollector->get('/docs[/{id:\d+}]', '/Index/docs');

        $routeCollector->addGroup('/data',function (RouteCollector $collector){
            $collector->get("/list/{start:\d+}[/{len:\d+}]", "/Docs/getDocsList");
            $collector->get('/docinfo/{id:\d+}', '/Docs/Index');
            $collector->get('/con/{id:\d+}', '/Docs/getCotent');
            $collector->get('/user/name', '/Comment/Index');
            $collector->get('/com/list', '/Comment/getComment');                        //获取评论列表
            $collector->post('/comment/in', '/Comment/inComment');              //获插入评论
            $collector->post('/reply/in', '/Comment/inReply');              //插入回复
        });
            


        //公共的404和405处理方式
        $this->setRouterNotFoundCallBack(function (Request $request,Response $response){
            $response->withStatus(Status::CODE_NOT_FOUND);
            $file = EASYSWOOLE_ROOT.'/public/error/404.html';
            $response->write(file_get_contents($file));
            return false;
        });

        $this->setMethodNotAllowCallBack(function (Request $request,Response $response){
            $response->withHeader('Content-type', 'application/json;charset=utf-8');
            $response->withStatus(Status::CODE_METHOD_NOT_ALLOWED);
            return false;
        });
        
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