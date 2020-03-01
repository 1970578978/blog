<?php
namespace App\User;

use EasySwoole\Component\Singleton;
use EasySwoole\RedisPool\Redis;
use PhpParser\Node\Expr\Cast\String_;

class User
{
    use Singleton;

    private $user_table = "usertable";
    private $doc_to_comment = "doc_to_comment";
    private $com_table = "comm_table";
    private $c_to_reply = "c_to_reply";
    private $reply_table = "reply_table";
    private $reply;
    private $maxUser = 100000;
    private $userMsg;
    private $comment;
    private $id = 0;
    private $index = 0;

    public function __construct()
    {
        $this->userMsg = [
            "id" => 0,
            "name" => ""
        ];
        $this->comment = [
            "id" => 0,
            "name" => 0,
            "content" => ""
        ];
        $this->reply = [
            "id" => 0,
            "fname" => 0,
            "tname" => 0,
            "content" => ""
        ];
    }

    /**
     * 获取用户表大小
     */
    public function getUserLen() : int
    {
        $redis = Redis::defer("redis");
        $len = $redis->hLen($this->user_table);
        return $len;
    }

    /**
     * 获取用户名
     */
    public function getUserName(int $id) : string
    {
        $redis = Redis::defer("redis");
        $str = $redis->hGet($this->user_table, $id);
        $ary = json_decode($str, true);
        return $ary['name'];
    }
    /**
     * 新建用户
     */
    public function setUser(string $name) : int
    {
        $redis = Redis::defer("redis");
        $len = $redis->hLen($this->user_table);
        $user = $this->userMsg;
        $user['name'] = $name;
        if($len < $this->maxUser){
            $user['id'] = $len;
            $this->id = $len;
            $this->index = $len;
        }else{
            $user['id'] = ++$this->id;
            $this->index = $this->id%$this->maxUser;
        }
        $redis->hSet($this->user_table, $this->index , json_encode($user));

        return $this->id;
    }

    /**
     * 插入评论
     */
    public function setCommet(int $did, string $name, string $con) : int
    {
        $redis = Redis::defer("redis");
        $docToC = $redis->hGet($this->doc_to_comment, $did);
        if($docToC === false){
            $docToC = [];
        }else{
            $docToC = json_decode($docToC, true);
        }
        
        $clen = $redis->hLen($this->com_table);
        $com = $this->comment;
        $com['id'] = $clen;
        $com['name'] = $name;
        $com['content'] = $con;
        $com['time'] = time();
        $redis->hSet($this->com_table, $clen, json_encode($com));

        $docToC[] = $clen;
        $redis->hSet($this->doc_to_comment, $did, json_encode($docToC));
        return $clen;
    }

    /**
     * 插入回复
     */
    public function setReply(int $cid, string $fname, string $tname, string $con) : int
    {
        $redis = Redis::defer("redis");
        $docToC = $redis->hGet($this->c_to_reply, $cid);
        if($docToC === false){
            $docToC = [];
        }else{
            $docToC = json_decode($docToC, true);
        }

        $clen = $redis->hLen($this->reply_table);
        $com = $this->reply;
        $com['id'] = $clen;
        $com['fname'] = $fname;
        $com['tname'] = $tname;
        $com['content'] = $con;
        $com['time'] = time();
        $redis->hSet($this->reply_table, $clen, json_encode($com));

        $docToC[] = $clen;
        $redis->hSet($this->c_to_reply, $cid, json_encode($docToC));
        return $clen;
    }

    /**
     * 获取评论
     */
    public function getComment(int $did) : array
    {
        $redis = Redis::defer("redis");
        $all = $redis->hGet($this->doc_to_comment, $did);
        $allc = json_decode($all, true);
        $ret = [];
        if(!empty($allc)){
            $all = $redis->hMGet($this->com_table, $allc);
            foreach($all as $v){
                $v = json_decode($v, true);
                $v['reply'] = [];
                $allr = $redis->hGet($this->c_to_reply, $v['id']);
                $allr = json_decode($allr, true);
                if(!empty($allr)){
                    $ar = $redis->hMGet($this->reply_table, $allr);
                    foreach($ar as $rv){
                        $v['reply'][] = json_decode($rv, true);
                    }
                }
                $v['reply'] = array_reverse($v['reply']);
                $ret[] = $v;
            }
        }
        $ret = array_reverse($ret);
        return $ret;
    }

    /**
     * 获取回复
     * 
     *  */
    public function getReply(int $cid) : array
    {
        $redis = Redis::defer("redis");
        $all = $redis->hGet($this->c_to_reply, $cid);
        $allc = json_decode($all, true);
        $all = $redis->hMGet($this->reply_table, $allc);
        $ret = [];
        foreach($all as $v){
            $ret[] = json_decode($v, true);
        }
        return $ret;
    }
}