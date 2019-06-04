<?php

namespace sw\Listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\math\{Vector3};
use pocketmine\utils\Config;
use sw\SkyWars;

class Debug implements Listener
{
    public $db;

    public function __construct(SkyWars $plugin)
    {

        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
        $this->db = $plugin;
    }

    public function debugDeath(PlayerDeathEvent $event)
    {
        $player = $event->getPlayer();
        $scan = scandir($this->db->getDataFolder() . "Arenas/");
        foreach ($scan as $files) {
            if ($files !== ".." and $files !== ".") {
                $name = str_replace(".yml", "", $files);
                if ($name == "") continue;
                $arena = new Config($this->db->getDataFolder() . "Arenas/" . $name . ".yml", Config::YAML);
                if ($player->getLevel()->getFolderName() == $arena->get("level")) {
                    $event->setDeathMessage("");
                    $espectador = $arena->get('espectador');
                    $levelArens = $arena->get('level');
                    $player->teleport($this->db->getServer()->getLevelByName($levelArens)->getSpawnLocation());
                    $player->teleport(new Vector3($espectador[0], $espectador[1], $espectador[2]));
                    $player->setGamemode(3);
                    $this->db->addEspectador($player, $name);
                }
            }
        }
    }


}
