<?php

namespace sw\Signs;

use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use sw\SkyWars;

class ChallangeSign implements Listener
{
    public $db;

    public function __construct(SkyWars $plugin)
    {

        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
        $this->db = $plugin;
    }

    public function signChange(SignChangeEvent $e)
    {
        $p = $e->getPlayer();
        if ($p->isOp()) {
            if ($e->getLine(0) == "sw") {
                $name = $e->getLine(1);
                if (!file_exists($this->db->getDataFolder() . "Arenas/" . $name . ".yml")) {
                    $e->setLine(0, "§cnull");
                    $e->setLine(1, "§cnull");
                    $e->setLine(2, "§cnull");
                    $e->setLine(3, "§cnull");
                } else {
                    $arena = new Config($this->db->getDataFolder() . "Arenas/" . $name . ".yml", Config::YAML);
                    $name = $arena->get("level");
                    $players = $arena->get("playersOnlineArena");
                    $st = null;
                    if ($arena->get("status") == "on") {
                        $st = "§l§aOnline";
                    }
                    if ($arena->get("status") == "off") {
                        $st = "§l§cOffline";
                    }
                    if ($arena->get("status") == "reset") {
                        $st = "§l§dRestarting";
                    }

                    $e->setLine(0, "§l§7[§eSky§6Wars§7]");
                    $e->setLine(1, $st);
                    $e->setLine(2, $name);
                    $e->setLine(3, "§9" . $players . "§7/§96");

                }


            }
        }
    }


}