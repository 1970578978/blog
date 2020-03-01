<?php
namespace App\RpcService;

use EasySwoole\Rpc\AbstractService;
use App\DocsSys\ReadDocs;
use App\RpcService\Rpcstatus;

class Articles extends AbstractService
{

    public function serviceName(): string
    {
        return 'articles';
    }

    /**
     * 获取文档列表
     */
    public function getDocsList()
    {
        $arg = $this->request()->getArg();
        $start = $arg['start'] ?? 0;
        $len = $arg['len'] ?? 3;
        if(!is_numeric($start) || !is_numeric($len)){
            $error = Rpcstatus::CODE_PARAM_ERROR;
            $this->response()->setResult($error);
            $this->response()->setMsg(Rpcstatus::getReasonPhrase($error));
        }
        $start = (int)$start;
        $len = (int)$len;
        $docs = ReadDocs::getInstance()->getDocs($start, $len);
        $this->response()->setResult($docs);
        $this->response()->setMsg('success');
    }

    /**
     * 获取redis中的文档信息
     */
    public function getDoc()
    {
        $arg = $this->request()->getArg();
        if(!is_numeric($arg['id'])){
            $error = Rpcstatus::CODE_PARAM_ERROR;
            $this->response()->setResult($error);
            $this->response()->setMsg(Rpcstatus::getReasonPhrase($error));
        }else{
            $id = (int)$arg['id'];
            $doc = ReadDocs::getInstance()->getDoc($id);
            $this->response()->setResult($doc);
            $this->response()->setMsg('success');
        }
    }

    /**
     * 获取文档内容并增加阅读量
     */
    public function getContent()
    {
        $arg = $this->request()->getArg();
        if(!is_numeric($arg['id'])){
            $error = Rpcstatus::CODE_PARAM_ERROR;
            $this->response()->setResult($error);
            $this->response()->setMsg(Rpcstatus::getReasonPhrase($error));
        }else{
            $id = (int)$arg['id'];
            $info = ReadDocs::getInstance()->getDoc($id);
            $content = ReadDocs::getInstance()->readContent($info['dir']);
            if($content === null){
                $info['status'] = 0;
                $this->response()->setMsg('The Doc Not Exist');
            }else{
                $this->response()->setMsg('success');
            }
            $this->response()->setResult($content);
            $info['watch']++;
            ReadDocs::getInstance()->setDoc($id, $info);
        }
    }

    /**
     * 下拉doc文档,读取文档
     */
    public function docInit()
    {
        $arg = $this->request()->getArg();
        $setclass = $arg['setclass'];
        if($setclass){
            ReadDocs::getInstance()->setClassName();
        }
        ReadDocs::getInstance()->gitInit();
        $ret = ReadDocs::getInstance()->saveDocs();
        $this->response()->setResult($ret);
        $this->response()->setMsg('success');
    }



}