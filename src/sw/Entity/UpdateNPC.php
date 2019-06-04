<?php

namespace sw\Entity;

use pocketmine\level\Level;
use pocketmine\scheduler\Task;
use pocketmine\utils\Config;
use sw\SkyWars;

class UpdateNPC extends Task
{
    public $yawr = 0;
    public $plugin;

    public function __construct(SkyWars $eid)
    {

        $this->plugin = $eid;

    }

    public function onRun(int $currentTick)
    {
        if ($this->yawr < 360) {
            $r = $this->yawr + 30;
            $this->yawr = $r;
        }
        if ($this->yawr >= 360) {
            $this->yawr = 0;
        }
        $this->plugin->onUpdateStatic();
        foreach ($this->plugin->getServer()->getLevels() as $level) {
            foreach ($level->getEntities() as $entity) {
                if ($entity instanceof SWNPC) {
                    if (empty($this->plugin->getDataFolder() . "Arenas/")) return;
                    $scan = scandir($this->plugin->getDataFolder() . "Arenas/");
                    foreach ($scan as $files) {
                        if ($files !== ".." and $files !== ".") {
                            $name = str_replace(".yml", "", $files);
                            if ($name == "") continue;
                            $entity->setNameTag($this->plugin->updatename());
                            $entity->setNametagVisible(true);
                            $entity->setNameTagAlwaysVisible(true);
                            $entity->setImmobile(true);
                            $entity->setScale(1.4);
                        }
                    }
                }
            }
        }
        foreach ($this->plugin->getServer()->getLevels() as $level) {
            foreach ($level->getEntities() as $entity) {
                if ($entity instanceof GanadorNPC) {
                    $entity->setNametagVisible(true);
                    $entity->setNameTagAlwaysVisible(true);
                    $entity->setImmobile(true);
                    $entity->broadcastEntityEvent(4);
                    $entity->setRotation($this->yawr, 0);
                }
            }
        }
        $this->plugin->players = $this->plugin->countPlayers();
        if (empty($this->plugin->getDataFolder() . "Arenas/")) return;
        $scan = scandir($this->plugin->getDataFolder() . "Arenas/");
        foreach ($scan as $files) {
            if ($files !== ".." and $files !== ".") {
                $name = str_replace(".yml", "", $files);
                if ($name == "") continue;
                $arena = new Config($this->plugin->getDataFolder() . "Arenas/" . $name . ".yml", Config::YAML);
                if (empty($arena->get("level"))) return;
                $levelArena = $this->plugin->getServer()->getLevelByName($arena->get("level"));

                if ($levelArena instanceof Level) {

                    $players = $levelArena->getPlayers();
                    $pl = count($players);
                    $arena->set('playersOnlineArena', $pl);
                    $arena->save();

                }
            }
        }
    }


}