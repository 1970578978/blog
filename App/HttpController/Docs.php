<?php


namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;
use EasySwoole\Rpc\Response;
use EasySwoole\Rpc\Rpc;

class Docs extends Controller
{
    public function Index()
    {
        return false;
    }


    public function getDocsList()
    {
        $start = $this->request()->getQueryParam('start');
        $len = $this->request()->getQueryParam('len');
        $len = $len ?? 3;
        $ret = [];

        $client = Rpc::getInstance()->client();
        $client->addCall('articles','getDocsList',['start'=> $start, 'len' => $len])
            ->setOnSuccess(function (Response $response)use(&$ret){
                $ret = $response->toArray();
            })->setOnFail(function (Response $response)use(&$ret){
                $ret = $response->toArray();
            });
        $client->exec(2.0);

        if($ret['status'] === 0){
            $this->writeJson(Status::CODE_OK,$ret["result"], "success");
        }else{
            $this->writeJson(Status::CODE_INTERNAL_SERVER_ERROR,[20001], "rpc server paused");
        }
        return false;
    }

}