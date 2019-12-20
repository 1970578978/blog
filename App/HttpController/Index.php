<?php


namespace App\HttpController;


use EasySwoole\EasySwoole\EasySwooleEvent;
use EasySwoole\Http\AbstractInterface\Controller;

class Index extends Controller
{

    function index()
    {
        $file = EASYSWOOLE_ROOT.'/public/index.html';
        $this->response()->write(file_get_contents($file));
    }

    protected function actionNotFound(?string $action)
    {
        $this->response()->withStatus(404);
        $file = EASYSWOOLE_ROOT.'/public/error/404.html';
        $this->response()->write(file_get_contents($file));
    }
}