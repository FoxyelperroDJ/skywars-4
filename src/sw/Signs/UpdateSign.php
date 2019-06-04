<?php

namespace sw\Signs;

use pocketmine\block\{BlockFactory};
use pocketmine\scheduler\Task;
use pocketmine\tile\Sign;
use pocketmine\utils\Config;
use sw\SkyWars;

class UpdateSign extends Task
{
    public $plugin;

    public function __construct(SkyWars $eid)
    {

        $this->plugin = $eid;

    }

    public function onRun(int $currentTick)
    {
        $world = $this->plugin->getServer()->getDefaultLevel();
        $tiles = $world->getTiles();
        foreach ($tiles as $sign) {
            if ($sign instanceof Sign) {
                $text = $sign->getText();
                $prefix = "§l§7[§eSky§6Wars§7]";
                if ($text[0] == $prefix) {
                    $game = $text[2];
                    if (file_exists($this->plugin->getDataFolder() . "Arenas/" . $game . ".yml")) {
                        $arena = new Config($this->plugin->getDataFolder() . "Arenas/" . $game . ".yml", Config::YAML);
                        $st = null;
                        $cartel = $sign->getBlock();
                        $bloqueAtras = $cartel->getSide($cartel->getDamage() ^ 0x01);
                        if ($arena->get("status") == "on") {
                            $st = "§l§aOnline";
                            $world->setBlock($bloqueAtras, BlockFactory::get(35, 5));
                        }
                        if ($arena->get("status") == "off") {
                            $st = "§l§cOffline";
                            $world->setBlock($bloqueAtras, BlockFactory::get(35, 14));
                        }
                        if ($arena->get("status") == "reset") {
                            $st = "§l§dRestarting";
                            $world->setBlock($bloqueAtras, BlockFactory::get(35, 1));
                        }
                        $playersa = $arena->get("playersOnlineArena");
                        $xd = "";
                        $rla = $xd . "§9" . $playersa . "§7/§96";
                        $sign->setText($prefix, $st, $game, $rla);
                    }
                }
            }
        }
    }

}