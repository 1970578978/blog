<?php
namespace App\RpcService;

use EasySwoole\Rpc\AbstractService;
use App\User\User;
use App\RpcService\Rpcstatus;
use EasySwoole\WordsMatch\WordsMatchClient;

class Comments extends AbstractService
{
    public function serviceName(): string
    {
        return 'comment';
    }


    /**
     * 文档敏感词匹配
     */
    public function wordMatch(string $content)
    {
        $res = WordsMatchClient::getInstance()->search($content);
        foreach($res as $v){
            $tmp = "";
            for($i = 0; $i < mb_strlen($v['word']); $i++){
                $tmp .= "*";
            }
            $content = str_replace($v['word'], $tmp, $content);
        }
        return $content;
   }


    /**
     * 给文档插入评论
     */
    public function insertComent()
    {
        $arg = $this->request()->getArg();
        $did = $arg['did'] ?? "did";
        $uid = $arg['uid'] ?? "uid";
        $name = $arg['name'] ?? "";
        $con = $arg['con'] ?? "";
        if($con === "" || !is_numeric($did) || (!is_numeric($uid) && $name === "")){
            $error = Rpcstatus::CODE_PARAM_ERROR;
            $this->response()->setResult($error);
            $this->response()->setMsg(Rpcstatus::getReasonPhrase($error));
        }else{
            $did = intval($did);
            if(is_numeric($uid)){
                $uid = intval($uid);
                $name = User::getInstance()->getUserName($uid);
            }else{
                $uid = User::getInstance()->setUser($name);
            }
            $con = $this->wordMatch($con);
            $cid = User::getInstance()->setCommet($did, $name, $con);
            $this->response()->setResult([$uid, $cid]);
            $this->response()->setMsg("success");
        }

    }

    /**
     * 插入回复
     */
    public function insertReply()
    {
        $arg = $this->request()->getArg();
        $cid = $arg['cid'] ?? "cid";
        $fuid = $arg['fuid'] ?? "fuid";
        $tname = $arg['tname'] ?? "";
        $name = $arg['name'] ?? "";
        $con = $arg['con'] ?? "";
        if($con === "" || !is_numeric($cid) || empty($tname) || (!is_numeric($fuid) && $name === "")){
            $error = Rpcstatus::CODE_PARAM_ERROR;
            $this->response()->setResult($error);
            $this->response()->setMsg(Rpcstatus::getReasonPhrase($error));
        }else{
            $cid = intval($cid);
            if(is_numeric($fuid)){
                $fuid = intval($fuid);
                $fname = User::getInstance()->getUserName($fuid);
            }else{
                $fname = $name;
                $fuid = User::getInstance()->setUser($name);
            }
            $con = $this->wordMatch($con);
            $rid = User::getInstance()->setReply($cid, $fname, $tname, $con);
            $this->response()->setResult([$fuid, $rid]);
            $this->response()->setMsg("success");
        }
    }

    /**
     * 获取评论
     */
    public function getComment()
    {
        $arg = $this->request()->getArg();
        $did = $arg['did'] ?? "did";  
        if(!is_numeric($did)){
            $error = Rpcstatus::CODE_PARAM_ERROR;
            $this->response()->setResult($error);
            $this->response()->setMsg(Rpcstatus::getReasonPhrase($error));
        }else{
            $did = intval($did);
            $com = User::getInstance()->getComment($did);
            $this->response()->setResult($com);
            $this->response()->setMsg("success");
        }
    }

    /**
     * 获取回复
     */
    public function getReply()
    {
        $arg = $this->request()->getArg();
        $cid = $arg['cid'] ?? "cid";
        if(!is_numeric($cid)){
            $error = Rpcstatus::CODE_PARAM_ERROR;
            $this->response()->setResult($error);
            $this->response()->setMsg(Rpcstatus::getReasonPhrase($error));
        }else{
            $cid = intval($cid);
            $com = User::getInstance()->getComment($cid);
            $this->response()->setResult($com);
            $this->response()->setMsg("success");
        }
    }

    /**
     * 获取用户名
     */
    public function getName()
    {
        $arg = $this->request()->getArg();
        $uid = $arg['uid'] ?? "uid";
        if(!is_numeric($uid)){
            $error = Rpcstatus::CODE_PARAM_ERROR;
            $this->response()->setResult($error);
            $this->response()->setMsg(Rpcstatus::getReasonPhrase($error));
        }else{
            $uid = intval($uid);
            $com = User::getInstance()->getUserName($uid);
            $this->response()->setResult($com);
            $this->response()->setMsg("success");
        }
    }
}