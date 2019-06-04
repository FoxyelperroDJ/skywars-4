<?php

namespace sw\Signs;

use pocketmine\event\Listener;
use pocketmine\event\player\{PlayerInteractEvent};
use pocketmine\tile\Sign;
use pocketmine\utils\Config;
use sw\SkyWars;

class JoinSign implements Listener
{
    public $db;

    public function __construct(SkyWars $plugin)
    {

        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
        $this->db = $plugin;
    }


    public function onJoinGame(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $tile = $player->getLevel()->getTile($block);
        if ($tile instanceof Sign) {
            $text = $tile->getText();
            $prefix = "§l§7[§eSky§6Wars§7]";
            if ($text[0] == $prefix) {
                $name = $text[2];
                $arena = new Config($this->db->getDataFolder() . "Arenas/" . $name . ".yml", Config::YAML);
                $status = $arena->get("status");
                $players = $arena->get("playersOnlineArena");
                if ($status == "on" && $players < 6) {
                    //joinGame
                    $this->db->joinMatchSign($player, $name);
                }
                if ($status == "on" && $players == 6) {
                    $player->sendMessage($this->db->t . " El juego ya esta completo");
                }
                if ($status == "off") {
                    $player->sendMessage($this->db->t . " El juego  ya comenzo!");
                }
                if ($status == 'reset') {
                    $player->sendMessage($this->db->t . " El juego  esta terminando!");
                }


            }

        }

    }

}