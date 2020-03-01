<?php

namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;
use EasySwoole\Rpc\Response;
use EasySwoole\Rpc\Rpc;

class Comment extends Controller
{
    private $method = "DES-ECB";
    private $secret = "docs321123";
    private $tlen = 30*60*60*24;
  
    /**
     * 获取用户名
     */
     public function Index()
     {
        $token = $this->request()->getQueryParam('token');
        $token = $token ?? "";
        $token = urldecode($token);
        $uid = openssl_decrypt($token, $this->method, $this->secret);
        if($uid === false  || !is_numeric($uid)){
            $this->writeJson(Status::CODE_BAD_REQUEST, [] , "failed");
            return false;
        }

        $uid = intval($uid);
        $ret = [];
        $client = Rpc::getInstance()->client();
        $client->addCall('comment','getName',["uid" => $uid])
            ->setOnSuccess(function (Response $response)use(&$ret){
                $ret = $response->toArray();
            })->setOnFail(function (Response $response)use(&$ret){
                $ret = $response->toArray();
            });
        $client->exec(2.0);

        if($ret['status'] === 0){
            if($ret['msg'] === "success"){
                $this->writeJson(Status::CODE_OK, $ret['result'] , "success");
            }else{
                $this->writeJson(Status::CODE_BAD_REQUEST, [] , "param error");
            }
        }else{
            $this->writeJson(Status::CODE_INTERNAL_SERVER_ERROR,[20001], "rpc server paused");
        }

        return false;
     }


    /**
     * 插入评论
     */
    public function inComment()
    {
        $post = $this->request()->getParsedBody();
        $token = $post['token'] ?? "";
        $name = $post['name'] ?? "";
        $con =  $post['content'] ?? 0;
        $did = $post['did'] ?? null;
        if(!is_numeric($did) || $con === 0 || ($token === "" && empty($name))){
            $this->writeJson(Status::CODE_BAD_REQUEST, [] , "content is empty");
            return false;
        }
        if($token !== ""){
            $token = urldecode($token);
            $uid = openssl_decrypt($token, $this->method, $this->secret, $this->tlen);
            if($uid === false){
                $this->writeJson(Status::CODE_BAD_REQUEST, [] , "failed");
                return false;
            }
            $uid = intval($uid);
        }else{
            $uid = "uid";
        }
        $did = intval($did);

        $ret = [];
        $client = Rpc::getInstance()->client();
        $client->addCall('comment','insertComent',['did'=> $did, 'uid' => $uid, 'name' => $name, 'con' => $con])
            ->setOnSuccess(function (Response $response)use(&$ret){
                $ret = $response->toArray();
            })->setOnFail(function (Response $response)use(&$ret){
                $ret = $response->toArray();
            });
        $client->exec(2.0);

        if($ret['status'] === 0){
            if($ret['msg'] === "success"){
                $cookie = openssl_encrypt($ret['result'][0], $this->method, $this->secret);
                $this->response()->setCookie("token", $cookie, time() + $this->tlen);
                $this->writeJson(Status::CODE_OK,  $ret['result'][1], "success");
            }else{
                $this->writeJson(Status::CODE_BAD_REQUEST, [] , "param error");
            }
        }else{
            $this->writeJson(Status::CODE_INTERNAL_SERVER_ERROR,[20001], "rpc server paused");
        }
        return false;
    }


    /**
     * 获取用户评论
     */
    public function getComment()
    {
        $id = $this->request()->getQueryParam('id');
        if(!is_numeric($id)){
            $this->writeJson(Status::CODE_BAD_REQUEST, [] , "failed");
            return false;
        }

        $id = intval($id);
        $ret = [];
        $client = Rpc::getInstance()->client();
        $client->addCall('comment','getComment',["did" => $id])
            ->setOnSuccess(function (Response $response)use(&$ret){
                $ret = $response->toArray();
            })->setOnFail(function (Response $response)use(&$ret){
                $ret = $response->toArray();
            });
        $client->exec(2.0);

        if($ret['status'] === 0){
            if(is_array($ret['result'])){
                $this->writeJson(Status::CODE_OK, $ret['result'] , "success");
            }else{
                $this->writeJson(Status::CODE_BAD_REQUEST, [] , "param error");
            }
        }else{
            $this->writeJson(Status::CODE_INTERNAL_SERVER_ERROR,[20001], "rpc server paused");
        }

        return false;
    }

    /**
     * 插入回复
     */
    public function inReply()
    {
        $post = $this->request()->getParsedBody();
        $token = $post['token'] ?? "";
        $fname = $post['fname'] ?? "";
        $tname = $post['tname'] ?? "";
        $con =  $post['content'] ?? 0;
        $cid = $post['cid'] ?? null;
        if(!is_numeric($cid) || $con === 0 || empty($tname) || ($token === "" && empty($fname))){
            $this->writeJson(Status::CODE_BAD_REQUEST, [] , "content is empty");
            return false;
        }
        if($token !== ""){
            $token = urldecode($token);
            $fuid = openssl_decrypt($token, $this->method, $this->secret, $this->tlen);
            if($fuid === false){
                $this->writeJson(Status::CODE_BAD_REQUEST, [] , "failed");
                return false;
            }
            $fuid = intval($fuid);
        }else{
            $fuid = "uid";
        }
        $cid = intval($cid);

        $ret = [];
        $client = Rpc::getInstance()->client();
        $client->addCall('comment','insertReply',['cid'=> $cid, 'fuid' => $fuid, 'tname' => $tname, 'name' => $fname, 'con' => $con])
            ->setOnSuccess(function (Response $response)use(&$ret){
                $ret = $response->toArray();
            })->setOnFail(function (Response $response)use(&$ret){
                $ret = $response->toArray();
            });
        $client->exec(2.0);

        if($ret['status'] === 0){
            if($ret['msg'] === "success"){
                $cookie = openssl_encrypt($ret['result'][0], $this->method, $this->secret);
                $this->response()->setCookie("token", $cookie, time() + $this->tlen);
                $this->writeJson(Status::CODE_OK,  $ret['result'][1], "success");
            }else{
                $this->writeJson(Status::CODE_BAD_REQUEST, [] , "param error");
            }
        }else{
            $this->writeJson(Status::CODE_INTERNAL_SERVER_ERROR,[20001], "rpc server paused");
        }
        return false;
    }
}