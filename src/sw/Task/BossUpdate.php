<?php

namespace sw\Task;

use pocketmine\scheduler\Task;
use sw\SkyWars;

class BossUpdate extends Task
{

    public $plugin;

    public function __construct(SkyWars $eid)
    {

        $this->plugin = $eid;

    }

    public function onRun(int $currentTick)
    {
        if (empty($this->plugin->getDataFolder() . "Arenas/")) return;

        $scan = scandir($this->plugin->getDataFolder() . "Arenas/");

        foreach ($scan as $files) {

            if ($files !== ".." and $files !== ".") {

                $name = str_replace(".yml", "", $files);

                $this->plugin->sendBossBar($name);

            }
        }


    }
}