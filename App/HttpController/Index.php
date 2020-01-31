<?php


namespace App\HttpController;


use EasySwoole\EasySwoole\EasySwooleEvent;
use EasySwoole\Http\AbstractInterface\Controller;
use Swoole\Coroutine\System;
use EasySwoole\Component\WaitGroup;

class Index extends Controller
{

    function index()
    {

        $file = EASYSWOOLE_ROOT.'/public/index.html';
        $wait = new WaitGroup();

        $wait->add();
        go(function ()use($wait,$file){
            $html = System::readFile($file);
            $this->response()->write($html);
            $wait->done();
        });
        $wait->wait();

    }

    function test()
    {

    }

    protected function actionNotFound(?string $action)
    {
        $this->response()->withStatus(404);
        $file = EASYSWOOLE_ROOT.'/public/error/404.html';
        $this->response()->write(file_get_contents($file));
    }
}