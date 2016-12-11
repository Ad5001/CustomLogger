<?php


namespace Ad5001\CustomLogger;


use pocketmine\command\CommandSender;


use pocketmine\command\Command;


use pocketmine\event\Listener;


use pocketmine\plugin\PluginBase;


use pocketmine\utils\TextFormat;


use pocketmine\utils\MainLogger;


use pocketmine\Server;


use pocketmine\Player;






class Main extends PluginBase implements Listener {




   public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }




    public function onLoad(){
        $this->saveDefaultConfig();
        if(!file_exists($this->getDataFolder() . "logs.txt")) {
            file_get_contents($this->getDataFolder() . "logs.txt", '');
        }
        $logger = new CustomLogger($this->getDataFolder() . "logs.txt", $this->getConfig()->get("Log_debug"), $this->getConfig()->getAll());
		$thread = \Thread::getCurrentThread();
		if($thread === null){
			$threadName = "PocketMine thread";
		}elseif($thread instanceof Thread or $thread instanceof Worker){
			$threadName = $thread->getThreadName() . " thread";
		}else{
			$threadName = (new \ReflectionClass($thread))->getShortName() . " thread";
		}
        echo str_repeat(" ", strlen(TextFormat::clean(TextFormat::toANSI(MainLogger::$logger->translateMsg($this->getConfig()->get("LoggerPrefix"), "", "", "§f", $threadName)))));
    }

    /*
    When the owner enters a command in the console. Here to addsome space.
    @param     $event    \pocketmine\event\server\ServerCommandEvent
    */
    public function onServerCommand(\pocketmine\event\server\ServerCommandEvent $event) {
		$thread = \Thread::getCurrentThread();
		if($thread === null){
			$threadName = "PocketMine thread";
		}elseif($thread instanceof Thread or $thread instanceof Worker){
			$threadName = $thread->getThreadName() . " thread";
		}else{
			$threadName = (new \ReflectionClass($thread))->getShortName() . " thread";
		}
        echo str_repeat(" ", strlen(TextFormat::clean(TextFormat::toANSI(MainLogger::$logger->translateMsg($this->getConfig()->get("LoggerPrefix"), "", "", "§f", $threadName)))));
    }


}