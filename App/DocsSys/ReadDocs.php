<?php
namespace App\DocsSys;

use EasySwoole\EasySwoole\Config;
use EasySwoole\Component\Singleton;
use Swoole\Coroutine\System;
use EasySwoole\RedisPool\Redis;

class ReadDocs
{
    use Singleton;

    public $conf_obj;
    public $doc_dir;
    public $dirtable = "dirtable";
    public $docs = "docs";

    public function __construct()
    {
        $this->conf_obj = Config::getInstance();
        $this->doc_dir = $this->conf_obj->getConf("APP.dir");

    }



    /**
     * 读取文档列表
     * @param 
     */
    private function readDocsList() : array
    {
        $conf_dir = $this->doc_dir."/list.php";
        if(!file_exists($conf_dir)){
            return [];
        }
        $list = require($conf_dir);
        if(is_array($list)){
            return $list;
        }
        return [];

    }

    /**
     * 通过git下拉文件合并分支
     * 
     */
    public function gitInit() : array
    {
        $ret = System::exec(EASYSWOOLE_ROOT."/shell/gitinit.sh ".$this->doc_dir);
        return $ret;
    }
    
    /**
     * 获取文档的分类
     */
    public function getClassName(int $nId, int $cId) : array
    {
        $ret = [];
        $redis = Redis::defer('redis');
        $ret[] = $redis->hGet("nature_class", $nId);
        $ret[] = $redis->hGet("content_class", $cId);
        return $ret;
    }

    /**
     * 设置类名
     */
    public function setClassName() : void
    {
        $redis = Redis::defer('redis');
        $redis->hSet("nature_class", 0, "随手笔记");
        $redis->hSet("nature_class", 1, "日常学习");
        $redis->hSet("nature_class", 2, "胡思乱想");
        $redis->hSet("content_class", 0, "php");
        $redis->hSet("content_class", 1, "c/c++");
        $redis->hSet("content_class", 2, "php/swoole");
        $redis->hSet("content_class", 3, "操作系统");
        $redis->hSet("content_class", 4, "杂项");

    }

    /**
     * 获取文档的数目
     */
    public function getDocsNum() : int
    {
        $redis = Redis::defer("redis");
        $num = $redis->hLen($this->docs);
        return $num;
    }

    /**
     * 读取文档数据并存入redis中
     */
    public function saveDocs() : int
    {
        $list = $this->readDocsList();
        $redis = Redis::defer("redis");
        $num = $redis->hLen($this->docs);
        for($i = 0; $i < count($list); $i++){
            $doc = $list[$i];
            if(!isset($doc['nature_class'])){
                $list[$i]['nature_class'] = 0;
            }
            if(!isset($doc['content_class'])){
                $list[$i]['content_class'] = 0;
            }
            $list[$i]['id'] = $num;
            $num++;
            $list[$i]['watch'] = 0;
            $list[$i]['ctime'] = time();
            $list[$i]['status'] = 1;
            if(!isset($doc["dir"]) || !isset($doc["title"]) || !isset($doc["descr"])){
                return 10001;
            }
            if($redis->hExists($this->dirtable, $doc['dir'])){
                return 10002;
            }
        }

        foreach($list as $v){
            $redis->hSet($this->dirtable, $v['dir'], $v['id']);
            $redis->hSet($this->docs, $v['id'], json_encode($v));
        }
        return 0;
    }

    /**
     * 读取redis中的文档列表
     * @param $start 文档起始id
     * @param 文档数量
     */
    public function getDocs(int $start = 0 , int $len = 3) : array
    {
        $redis = Redis::defer("redis");
        $alllen = $redis->hLen($this->docs);
        if($start !== 0 && $start < $alllen){
            $alllen = $start;
        }

        $temp = [];
        while(count($temp) < $len && $alllen > 0){
            $temp[] = --$alllen;
        }
        if($temp === []){
            return [];
        }
        $rett = $redis->hMGet($this->docs, $temp);
        $delnum = 0;
        $ret = [];
        foreach($rett as $v){
            $v = json_decode($v, true);
            $v["nature_class"] = $redis->hGet("nature_class", $v["nature_class"]);
            $v["content_class"] = $redis->hGet("content_class", $v["content_class"]);
            if($v['status'] === 0){
                $delnum++;
            }else{
                $ret[] = $v;
            }
        }
        $k = 0;
        $last = --$alllen;
        while($k < $delnum && $last >= 0){
            $temp = $redis->hGet($this->docs, $last);
            $temp = json_decode($temp, true);
            if($temp['status'] !== 0){
                $k++;
                $temp["nature_class"] = $redis->hGet("nature_class", $temp["nature_class"]);
                $temp["content_class"] = $redis->hGet("content_class", $temp["content_class"]);
                $ret[] = $temp;
            }
            $last--;
        }

        return $ret;

    }

    /**
     * 删除redis指定文档
     *@param int $id文档id
     */
    public function delDocs(int $id) : bool
    {
        $redis = Redis::defer("redis");
        $v = $redis->hGet($this->docs, $id);
        $v = json_decode($v, true);
        $v['status'] = 0;
        $ret = $redis->hSet($this->docs, $id, json_encode($v));
        if($ret === false){
            return false;
        }else{
            return true;
        }
    }

    /**
     * 读取指定文档的内容
     */
    public function readContent(string $path) : string
    {
        $doc = $this->doc_dir."/".$path;
        $content = System::readFile($doc);
        return $content;
    }

    /**
     * 创造100条假数据并存入redis中
     */
    public function inTextData() : int
    {
        $list = [];
        $redis = Redis::defer("redis");
        $num = $redis->hLen($this->docs);
        for($i = 0; $i < 100; $i++){
            
            $list[$i]['nature_class'] = rand(0,2);
            $list[$i]['content_class'] = rand(0,4);
            
            $list[$i]['id'] = $num++;
            $list[$i]['watch'] = 0;
            $list[$i]['ctime'] = time();
            $list[$i]['status'] = 1;
            $list[$i]["dir"] = "notes/mess/ubuntu 16.04 server Selenium环境搭建.md";
            $list[$i]["title"] = "ubuntu 16.04 server Selenium环境搭建简明教程";
            $list[$i]["descr"] = "Selenium常用在ui自动化测试中，搭配浏览器可以完成很多功能，来看看怎么在ubuntu 16.04 server 中怎么搭建简单环境吧";
        }

        foreach($list as $v){
            $redis->hSet($this->docs, $v['id'], json_encode($v));
        }
        return 0;
    }
}