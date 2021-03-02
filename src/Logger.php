<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/8/14
 * Time: 下午6:15
 */

namespace EasySwoole\EasySwoole;


use EasySwoole\Component\Event;
use EasySwoole\Component\Singleton;
use EasySwoole\Log\LoggerInterface;

class Logger
{
    private $logger;

    private $callback;

    private $logConsole = true;

    private $ignoreCategory = [];

    private $logLevel = LoggerInterface::LOG_LEVEL_INFO;

    use Singleton;

    function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->callback = new Event();
    }

    public function logLevel(?int $level = null)
    {
        if($level !== null){
            $this->logLevel = $level;
            return $this;
        }
        return $this->logLevel;
    }

    public function logConsole(?bool $is = null)
    {
        if($is === null){
            return $this->logConsole;
        }else{
            $this->logConsole = $is;
            return $this;
        }
    }

    public function ignoreCategory(?array $arr = null)
    {
        if($arr === null){
            return $this->ignoreCategory;
        }else{
            $this->ignoreCategory = $arr;
            return $this;
        }
    }

    public function log(?string $msg,int $logLevel = LoggerInterface::LOG_LEVEL_DEBUG,string $category = 'debug')
    {
        if($logLevel < $this->logLevel){
            return;
        }
        $this->logger->log($msg,$logLevel,$category);
        $calls = $this->callback->all();
        foreach ($calls as $call){
            call_user_func($call,$msg,$logLevel,$category);
        }
    }

    public function console(?string $msg,int $logLevel = LoggerInterface::LOG_LEVEL_DEBUG,string $category = 'debug')
    {
        if($logLevel < $this->logLevel){
            return;
        }
        $this->logger->console($msg,$logLevel,$category);
        if($this->logConsole){
            $this->log($msg,$logLevel,$category);
        }
    }

    public function debug(?string $msg,string $category = 'debug')
    {
        $this->console($msg,LoggerInterface::LOG_LEVEL_DEBUG,$category);
    }

    public function info(?string $msg,string $category = 'info')
    {
        $this->console($msg,LoggerInterface::LOG_LEVEL_INFO,$category);
    }

    public function notice(?string $msg,string $category = 'notice')
    {
        $this->console($msg,LoggerInterface::LOG_LEVEL_NOTICE,$category);
    }

    public function waring(?string $msg,string $category = 'waring')
    {
        $this->console($msg,LoggerInterface::LOG_LEVEL_WARNING,$category);
    }

    public function error(?string $msg,string $category = 'error')
    {
        $this->console($msg,LoggerInterface::LOG_LEVEL_ERROR,$category);
    }

    public function onLog():Event
    {
        return $this->callback;
    }
}