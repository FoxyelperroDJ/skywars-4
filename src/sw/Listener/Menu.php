<?php

namespace sw\Listener;

use pocketmine\event\Listener;
use pocketmine\event\player\{PlayerInteractEvent};
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\utils\Config;
use sw\SkyWars;

class Menu implements Listener
{
    public $db;

    public function __construct(SkyWars $plugin)
    {

        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
        $this->db = $plugin;
    }

    public function onMenuPacket(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $item = $event->getItem()->getId();
        $scan = scandir($this->db->getDataFolder() . "Arenas/");
        foreach ($scan as $files) {
            if ($files !== ".." and $files !== ".") {
                $name = str_replace(".yml", "", $files);
                if ($name == "") continue;
                $arena = new Config($this->db->getDataFolder() . "Arenas/" . $name . ".yml", Config::YAML);
                if ($event->getPlayer()->getLevel()->getFolderName() == $arena->get("level")) {
                    if ($item == 426) {
                        //leave game
                        $this->db->quitGame($player);
                    }
                    if ($item == 339) {
                        //ksit
                        $this->db->addForm($player);
                    }
                }
            }
        }
    }

    public function onMenuPacketName(PlayerItemHeldEvent $event)
    {
        $player = $event->getPlayer();
        $item = $event->getItem()->getId();
        if ($item == 426) {
            //leave game
            $player->sendPopup("§l§cSalir del Juego");
        }
        if ($item == 339) {
            //kits
            $player->sendPopup("§l§aSeleccionar Kit");
        }
    }


}
