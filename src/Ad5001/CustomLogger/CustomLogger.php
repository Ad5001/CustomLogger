<?php


namespace Ad5001\CustomLogger;

use pocketmine\utils\MainLogger;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Terminal;
use LogLevel;
use pocketmine\Thread;
use pocketmine\Worker;
use pocketmine\Server;

class CustomLogger extends MainLogger {

    /*
    Constructs the class
    */
    public function __construct($logFile, $logDebug = false, array $cfg = []) {
		MainLogger::$logger = $this;
        $class = new \ReflectionClass('pocketmine\\Server');
        $property = $class->getProperty('logger');
        $property->setAccessible(true);
        $property->setValue(Server::getInstance(), $this);
        $property->setAccessible(false);
		touch($logFile);
		$this->logFile = $logFile;
		$this->logDebug = (bool) $logDebug;
		$this->logStream = new \Threaded;
        $this->cfg = $cfg;
		$this->start();
    }

    // Modified version of the MainLogger
    protected function send($message, $level, $prefix, $color){
		$now = time();

		$thread = \Thread::getCurrentThread();
		if($thread === null){
			$threadName = "PocketMine thread";
		}elseif($thread instanceof Thread or $thread instanceof Worker){
			$threadName = $thread->getThreadName() . " thread";
		}else{
			$threadName = (new \ReflectionClass($thread))->getShortName() . " thread";
		}

		if($this->shouldRecordMsg){
			if((time() - $this->lastGet) >= 10) $this->shouldRecordMsg = false; // 10 secs timeout
			else{
				if(strlen($this->shouldSendMsg) >= 10000) $this->shouldSendMsg = "";
				$this->shouldSendMsg .= $color . "|" . $prefix . "|" . trim($message, "\r\n") . "\n";
			}
		}

		$message = TextFormat::toANSI($this->translateMsg($this->cfg["LoggerLook"], $message, $prefix, $color, $threadName));
		$cleanMessage = TextFormat::clean($message);

		if(!Terminal::hasFormattingCodes()){
			echo str_repeat("\010", strlen(TextFormat::clean(TextFormat::toANSI(MainLogger::$logger->translateMsg($this->cfg["LoggerPrefix"], "", "", "§f", $threadName)))));
			echo $cleanMessage . PHP_EOL;
			echo TextFormat::toANSI($this->translateMsg($this->cfg["LoggerPrefix"], "", "", $color, $threadName));
		}else{
			echo str_repeat("\010", strlen(TextFormat::clean(TextFormat::toANSI(MainLogger::$logger->translateMsg($this->cfg["LoggerPrefix"], "", "", "§f", $threadName)))));
			echo $message . PHP_EOL;
			echo TextFormat::toANSI($this->translateMsg($this->cfg["LoggerPrefix"], "", "", $color, $threadName));
		}

		if(isset($this->consoleCallback)){
			call_user_func($this->consoleCallback);
		}

		if($this->attachment instanceof \ThreadedLoggerAttachment){
			$this->attachment->call($level, $message);
		}

		$this->logStream[] = date("Y-m-d", $now) . " " . $cleanMessage . "\n";
		if($this->logStream->count() === 1){
			$this->synchronized(function(){
				$this->notify();
			});
		}
	}



    /*
    Translate the message from config.
    @param     $message    string
    */
    public function translateMsg(string $msg, string $message, string $prefix, string $color, string $threadName) {
        $msg = str_ireplace("{time}", date("H:i:s"), $msg);
        $msg = str_ireplace("{prefixLower}", strtolower($prefix), $msg);
        $msg = str_ireplace("{prefixUpper}", strtoupper($prefix), $msg);
        $msg = str_ireplace("{prefix}", $prefix, $msg);
        $msg = str_ireplace("{message}", $message, $msg);
        $msg = str_ireplace("{msg}", $message, $msg);
        $msg = str_ireplace("{color}", $color, $msg);
        $msg = str_ireplace("{thread}", $threadName, $msg);
        $msg = preg_replace("/{color-(.+?)}/", "§$1", $msg);
        return $msg;
    }
}