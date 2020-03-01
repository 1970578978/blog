<?php


namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;
use Swoole\Coroutine\System;
use EasySwoole\Http\Message\Status;
use EasySwoole\Component\WaitGroup;
use EasySwoole\EasySwoole\Config;
use EasySwoole\Rpc\Response;
use EasySwoole\Rpc\Rpc;
use App\DocsSys\ReadDocs;
use Parsedown;

class Index extends Controller
{

    function index()
    {
        $file = EASYSWOOLE_ROOT.'/public/index.html';
        $html = System::readFile($file);
        $this->response()->write($html);
        // return "/test";
    }

    function docs()
    {
        $file = EASYSWOOLE_ROOT.'/public/doc.html';
        $html = System::readFile($file);
        $this->response()->write($html);
    }

    function test()
    {
       /*
        $ret = [];
        $client = Rpc::getInstance()->client();
        $client->addCall('articles','list',['page'=>1])
            ->setOnSuccess(function (Response $response)use(&$ret){
                $ret['goods'] = $response->toArray();
            })->setOnFail(function (Response $response)use(&$ret){
                $ret['goods'] = $response->toArray();
            });
        $client->exec(2.0);
        $this->writeJson(200,$ret);
        */
        //ReadDocs::getInstance()->readDocsList();
        //$con = ReadDocs::getInstance()->gitInit();
        //ReadDocs::getInstance()->setClassName();
        //var_dump(ReadDocs::getInstance()->getClassName(0,0));
        //$num = ReadDocs::getInstance()->saveDocs();
        //ReadDocs::getInstance()->delDocs(0);
        // $num = ReadDocs::getInstance()->delDocs(199);
        // $num = ReadDocs::getInstance()->delDocs(197);
        // var_dump($num);
        //$this->response()->write($num);
       // $this->writeJson(200,$con);
    }

}