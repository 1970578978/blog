<?php
namespace App\RpcService;

use EasySwoole\Rpc\AbstractService;

class Articles extends AbstractService
{

    public function serviceName(): string
    {
        return 'articles';
    }

    public function list()
    {
        $this->response()->setResult([
            [
                'articleId'=>'100001',
                'articleName'=>'学习的动力',
                'prices'=>1124
            ],
            [
                'articleId'=>'100002',
                'articleName'=>'学习的方法',
                'prices'=>599
            ]
        ]);
        $this->response()->setMsg('get goods list success');
    }
}