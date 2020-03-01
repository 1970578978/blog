<?php


namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;
use EasySwoole\Rpc\Response;
use EasySwoole\Rpc\Rpc;
use Parsedown;

class Docs extends Controller
{
    /**
     * 获取文档信息
     */
    public function Index()
    {
        $ret = [];
        $id = $this->request()->getQueryParam('id');
        $id = $id ?? 0;
        $client = Rpc::getInstance()->client();
        $client->addCall('articles','getDoc',['id'=> $id])
            ->setOnSuccess(function (Response $response)use(&$ret){
                $ret = $response->toArray();
            })->setOnFail(function (Response $response)use(&$ret){
                $ret = $response->toArray();
            });
        $client->exec(2.0);

        if($ret['status'] === 0){
            if(is_array($ret["result"])){
                $this->writeJson(Status::CODE_OK,$ret["result"], "success");
            }else{
                $this->writeJson(Status::CODE_BAD_REQUEST, [] , "param error");
            }
        }else{
            $this->writeJson(Status::CODE_INTERNAL_SERVER_ERROR,[20001], "rpc server paused");
        }
        return false;
    }


    /**
     * 获取文档列表
     */
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

    /**
     * 获取文档内容
     */
    public function getCotent()
    {
        $ret = [];
        $id = $this->request()->getQueryParam('id');
        $id = $id ?? 0;
        $client = Rpc::getInstance()->client();
        $client->addCall('articles','getContent',['id'=> $id])
            ->setOnSuccess(function (Response $response)use(&$ret){
                $ret = $response->toArray();
            })->setOnFail(function (Response $response)use(&$ret){
                $ret = $response->toArray();
            });
        $client->exec(2.0);

        if($ret['status'] === 0){
            if(is_string($ret["result"])){
                $html = Parsedown::instance()->text($ret["result"]); 
                $this->writeJson(Status::CODE_OK,$html, "success");
            }else{
                $this->writeJson(Status::CODE_BAD_REQUEST, "" , $ret['msg']);
            }
        }else{
            $this->writeJson(Status::CODE_INTERNAL_SERVER_ERROR,[20001], "rpc server paused");
        }
        return false;
    }
}