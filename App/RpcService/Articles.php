<?php
namespace App\RpcService;

use EasySwoole\Rpc\AbstractService;
use App\DocsSys\ReadDocs;

class Articles extends AbstractService
{

    public function serviceName(): string
    {
        return 'articles';
    }

    public function getDocsList()
    {
        $arg = $this->request()->getArg();
        $start = $arg['start'] ?? 0;
        $len = $arg['len'] ?? 3;
        $docs = ReadDocs::getInstance()->getDocs($start, $len);
        $this->response()->setResult($docs);
        $this->response()->setMsg('success');
    }
}