<?php
namespace EasySwoole\EasySwoole;


use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\WordsMatch\WordsMatchClient;
use EasySwoole\WordsMatch\WordsMatchServer;
use App\RpcService\Articles;
use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\RedisPool\RedisPool;
use EasySwoole\RedisPool\Redis;
use EasySwoole\Rpc\NodeManager\RedisManager;
use EasySwoole\Rpc\Config as RpcConfig;
use EasySwoole\Rpc\Rpc;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // TODO: Implement mainServerCreate() method.

        //文字匹配服务
        WordsMatchServer::getInstance()
        ->setMaxMem('512M') // 每个进程最大内存
        ->setProcessNum(2) // 设置进程数量
        ->setServerName('Easyswoole words-match')// 服务名称
        ->setTempDir(EASYSWOOLE_TEMP_DIR)// temp地址
        ->setWordsMatchPath(EASYSWOOLE_ROOT.'/WordsMatch/')
        ->setDefaultWordBank('comment.txt')// 服务启动时默认导入的词库文件路径
        ->setSeparator(',')// 词和其它信息分隔符
        ->attachToServer(ServerManager::getInstance()->getSwooleServer());

        /**
         * rpc服务
         */
        //定义节点Redis管理器
        $redisPool = new RedisPool(new RedisConfig([
            'host'=>'127.0.0.1'
        ]));
        $manager = new RedisManager($redisPool);
        //配置Rpc实例
        $config = new RpcConfig();
        //这边用于指定当前服务节点ip，如果不指定，则默认用UDP广播得到的地址
        $config->setServerIp('127.0.0.1');
        $config->setNodeManager($manager);
        //配置并初始化
        Rpc::getInstance($config);
        //添加服务
        Rpc::getInstance()->add(new Articles());
        Rpc::getInstance()->attachToServer(ServerManager::getInstance()->getSwooleServer());
        
        /**
         * redis连接池注册
         */
        Redis::getInstance()->register('redis',new RedisConfig());
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.

        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }
}