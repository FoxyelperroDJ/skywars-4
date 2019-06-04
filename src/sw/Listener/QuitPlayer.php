<?php

namespace sw\Listener;

use pocketmine\event\Listener;
use pocketmine\event\player\{PlayerJoinEvent, PlayerQuitEvent};
use pocketmine\utils\Config;
use sw\SkyWars;

class QuitPlayer implements Listener
{
    public $db;

    public function __construct(SkyWars $plugin)
    {

        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
        $this->db = $plugin;
    }

    public function onJoinArena(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        if (empty($this->db->getDataFolder() . "Arenas/")) return;
        $scan = scandir($this->db->getDataFolder() . "Arenas/");
        foreach ($scan as $files) {
            if ($files !== ".." and $files !== ".") {
                $name = str_replace(".yml", "", $files);
                if ($name == "") continue;
                $arena = new Config($this->db->getDataFolder() . "Arenas/" . $name . ".yml", Config::YAML);

                if ($event->getPlayer()->getLevel()->getFolderName() == $arena->get("level")) {
                    $this->db->removerKit($player);
                    $player->teleport($this->db->getServer()->getDefaultLevel()->getSpawnLocation());
                    $player->getInventory()->clearAll();
                    $player->getArmorInventory()->clearAll();
                    $player->setGamemode(2);
                }
            }
        }
    }

    public function quitPlayerArena(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer();
        if (empty($this->db->getDataFolder() . "Arenas/")) return;
        $scan = scandir($this->db->getDataFolder() . "Arenas/");
        foreach ($scan as $files) {
            if ($files !== ".." and $files !== ".") {
                $name = str_replace(".yml", "", $files);
                if ($name == "") continue;
                $arena = new Config($this->db->getDataFolder() . "Arenas/" . $name . ".yml", Config::YAML);
                $namep = $player->getName();
                if ($event->getPlayer()->getLevel()->getFolderName() == $arena->get("level")) {
                    foreach ($this->db->getServer()->getLevelByName($arena->get('level'))->getPlayers() as $pl) {
                        $pla = $arena->get('playersOnlineArena') - 1;
                        $pl->sendMessage($this->db->t . "§4" . $namep . " §esalio del juego §6(§c" . $pla . "§6/§c6§6)");
                    }
                    $this->db->removerKit($player);
                    if ($arena->get('status') == 'on') {
                        $data = new Config($this->db->getDataFolder() . "Arenas/" . $name . ".yml", Config::YAML);
                        if ($data->get('player1') == $player->getName()) {
                            $data->set('player1', null);
                            $data->save();
                        }
                        if ($data->get('player2') == $player->getName()) {
                            $data->set('player2', null);
                            $data->save();
                        }
                        if ($data->get('player3') == $player->getName()) {
                            $data->set('player3', null);
                            $data->save();
                        }
                        if ($data->get('player4') == $player->getName()) {
                            $data->set('player4', null);
                            $data->save();
                        }
                        if ($data->get('player5') == $player->getName()) {
                            $data->set('player5', null);
                            $data->save();
                        }
                        if ($data->get('player6') == $player->getName()) {
                            $data->set('player6', null);
                            $data->save();
                        }

                    }
                }
            }
        }
    }


}