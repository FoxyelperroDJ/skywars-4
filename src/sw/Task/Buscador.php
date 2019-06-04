<?php

namespace sw\Task;

use pocketmine\{Player};
use pocketmine\scheduler\Task;
use pocketmine\utils\Config;
use sw\SkyWars;

class Buscador extends Task
{
    public $time = 5;
    public $game = 0;
    public $pl;
    public $player;

    public function __construct(SkyWars $eid, Player $player)
    {

        $this->pl = $eid;
        $this->player = $player;

    }

    public function onRun(int $currentTick)
    {

        if ($this->time > 0) {
            $this->time--;
        }
        if ($this->time == 1) {

            if ($this->pl->countArchivos() == 0) {
                $this->player->sendMessage($this->pl->t . "§7No hay partidas Disponibles");
                unset($this->pl->wating[$this->player->getName()]);
                $this->pl->getScheduler()->cancelTask($this->getTaskId());
            }
            if ($this->pl->countArchivos() > 0) {

                $name = "sw-" . $this->game;
                $arena = new Config($this->pl->getDataFolder() . "Arenas/" . $name . ".yml", Config::YAML);
                if ($arena->get("status") == "off") {
                    $this->game++;
                    $this->pl->getServer()->getLogger()->info($this->game);
                } else {
                    $arena = new Config($this->pl->getDataFolder() . "Arenas/sw-" . $this->game . ".yml", Config::YAML);
                    if ($arena->get("status") == "on") {
                        $this->pl->getServer()->getLogger()->info($this->game);
                        $this->pl->joinMatch($this->player, $this->game);
                        unset($this->pl->wating[$this->player->getName()]);
                        $this->pl->getScheduler()->cancelTask($this->getTaskId());
                    }

                }
                if ($this->game >= $this->pl->countArchivos()) {
                    $this->player->sendMessage($this->pl->t . "§7No hay partidas Disponibles");
                    unset($this->pl->wating[$this->player->getName()]);
                    $this->pl->getScheduler()->cancelTask($this->getTaskId());

                }

            }
        }
    }


}