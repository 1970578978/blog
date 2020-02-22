<?php


namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;
use Swoole\Coroutine\System;
use EasySwoole\Component\WaitGroup;
use EasySwoole\EasySwoole\Config;
use EasySwoole\Rpc\Response;
use EasySwoole\Rpc\Rpc;
use App\DocsSys\ReadDocs;

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
ReadDocs::getInstance()->delDocs(0);
        $num = ReadDocs::getInstance()->getDocs();
        var_dump($num);
        //$this->response()->write($num);
       // $this->writeJson(200,$con);
    }

    protected function actionNotFound(?string $action)
    {
        $this->response()->withStatus(404);
        $file = EASYSWOOLE_ROOT.'/public/error/404.html';
        $this->response()->write(file_get_contents($file));
    }
}