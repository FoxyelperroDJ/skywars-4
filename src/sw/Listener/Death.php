<?php

namespace sw\Listener;

use pocketmine\{Player};
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\level\sound\{DoorBumpSound};
use pocketmine\math\{Vector3};
use pocketmine\utils\Config;
use sw\SkyWars;

class Death implements Listener
{
    public $db;

    public function __construct(SkyWars $plugin)
    {

        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
        $this->db = $plugin;
    }

    public function onDeath(EntityDamageEvent $event)
    {
        $victima = $event->getEntity();
        if ($event instanceof EntityDamageByEntityEvent) {
            if ($victima instanceof Player && $event->getDamager() instanceof Player) {
                $scan = scandir($this->db->getDataFolder() . "Arenas/");
                foreach ($scan as $files) {
                    if ($files !== ".." and $files !== ".") {
                        $name = str_replace(".yml", "", $files);
                        if ($name == "") continue;
                        $arena = new Config($this->db->getDataFolder() . "Arenas/" . $name . ".yml", Config::YAML);
                        if ($event->getEntity()->getLevel()->getFolderName() == $arena->get("level")) {

                            if ($event->getFinalDamage() >= $victima->getHealth()) {

                                $status = $arena->get("status");
                                if ($status == "off") {
                                    $event->setCancelled(true);
                                    $this->db->addEspectador($victima, $name);
                                    foreach ($this->db->getServer()->getLevelByName($arena->get('level'))->getPlayers() as $p) {
                                        $this->db->getServer()->getLevelByName($arena->get('level'))->addSound(new DoorBumpSound($p));
                                        $damager = $event->getDamager();
                                        if ($damager instanceof Player) {
                                            $p->sendMessage($this->db->t . "§6" . $victima->getName() . " §emurio a causa de §6" . $damager->getName());
                                        }
                                    }
                                }
                            }


                        }
                    }
                }

            }
        }
    }

    public function Void(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        $scan = scandir($this->db->getDataFolder() . "Arenas/");
        foreach ($scan as $files) {
            if ($files !== ".." and $files !== ".") {
                $name = str_replace(".yml", "", $files);
                if ($name == "") continue;
                $arena = new Config($this->db->getDataFolder() . "Arenas/" . $name . ".yml", Config::YAML);
                if ($player->getLevel()->getFolderName() == $arena->get("level")) {
                    $min = $arena->get('minVoid');
                    if ($player->getY() + 1 <= $min && $arena->get('status') == 'off') {
                        if ($player->getGamemode() == 0) {
                            $this->db->addEspectador($player, $name);
                            foreach ($this->db->getServer()->getLevelByName($arena->get('level'))->getPlayers() as $p) {
                                $this->db->getServer()->getLevelByName($arena->get('level'))->addSound(new DoorBumpSound($p));
                                $p->sendMessage($this->db->t . "§6" . $player->getName() . " §emurio a causa de §6 Void");
                            }
                        } else {
                            if ($player->getGamemode() == 3) {
                                $lob = $arena->get('espectador');
                                $player->teleport(new Vector3($lob[0], $lob[1], $lob[2]));

                            }
                        }

                    }
                    if ($player->getY() <= $min && $arena->get('status') == 'on') {
                        $lob = $arena->get('lobby');
                        $player->teleport(new Vector3($lob[0], $lob[1], $lob[2]));
                    }
                    if ($player->getY() <= $min && $arena->get('status') == 'reset') {
                        $lob = $arena->get('lobby');
                        $player->teleport(new Vector3($lob[0], $lob[1], $lob[2]));
                    }


                }
            }
        }
    }


}
