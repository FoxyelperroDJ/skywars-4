<?php

namespace sw\Task;

use pocketmine\block\Air;
use pocketmine\level\particle\FlameParticle;
use pocketmine\level\particle\HeartParticle;
use pocketmine\level\particle\PortalParticle;
use pocketmine\level\particle\RedstoneParticle;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\utils\Config;
use sw\PacketSound\{HitSound, TimeSound, TotemSound};
use sw\SkyWars;

class GameTask extends Task
{
    public $pl;

    public function __construct(SkyWars $eid)
    {

        $this->pl = $eid;


    }

    public function onRun(int $currentTick)
    {
        if (empty($this->pl->getDataFolder() . "Arenas/")) return;
        $scan = scandir($this->pl->getDataFolder() . "Arenas/");
        foreach ($scan as $files) {
            if ($files !== ".." and $files !== ".") {
                $name = str_replace(".yml", "", $files);
                if ($name == "") continue;
                $arena = new Config($this->pl->getDataFolder() . "Arenas/" . $name . ".yml", Config::YAML);
                $players = $this->pl->getServer()->getLevelByName($arena->get('level'))->getPlayers();
                if (count($players) == 0 or count($players) == 1 && $arena->get('status') == 'on') {
                    $arena->set('lobbytime', 100);
                    $arena->set('status', 'on');
                    $arena->set('time', 900);
                    $arena->set('resetTime', 25);

                }
                if (count($players) == 0) {
                    $arena->set('player1', null);
                    $arena->set('player2', null);
                    $arena->set('player3', null);
                    $arena->set('player4', null);
                    $arena->set('player5', null);
                    $arena->set('player6', null);
                    $arena->set('lobbytime', 100);
                    $arena->set('resetTime', 25);
                }
                if (count($players) >= 2) {
                    if ($arena->get('lobbytime') > 0) {
                        $lob = $arena->get('lobbytime');
                        $lob--;
                        $arena->set('lobbytime', $lob);

                    }
                }
                if (count($players) == 6 && $arena->get('status') == 'on' && $arena->get('lobbytime') > 0) {
                    $arena->set('status', 'off');

                }
                if (count($players) < 6 && $arena->get('lobbytime') > 10) {
                    $arena->set('status', 'on');

                }
                if (count($players) < 6 && $arena->get('lobbytime') <= 10) {
                    if ($arena->get('status') == 'reset') {
                        $arena->set('status', 'reset');
                    } else {
                        $arena->set('status', 'off');
                    }

                }
                if ($arena->get('lobbytime') > 0) {
                    foreach ($players as $p) {
                        $p->setFood(20);
                        $p->setHealth(20);
                    }
                }
                if ($arena->get('lobbytime') == 10) {
                    foreach ($players as $pl) {
                        $pl->setImmobile(true);
                        $pl->getInventory()->clearAll();
                        $pl->setGamemode(0);
                        if ($arena->get('player1') == $pl->getName()) {
                            $sp = $arena->get('spawn1');
                            $pl->teleport(new Vector3($sp[0], $sp[1], $sp[2]));
                        }
                        if ($arena->get('player2') == $pl->getName()) {
                            $sp = $arena->get('spawn2');
                            $pl->teleport(new Vector3($sp[0], $sp[1], $sp[2]));
                        }
                        if ($arena->get('player3') == $pl->getName()) {
                            $sp = $arena->get('spawn3');
                            $pl->teleport(new Vector3($sp[0], $sp[1], $sp[2]));
                        }
                        if ($arena->get('player4') == $pl->getName()) {
                            $sp = $arena->get('spawn4');
                            $pl->teleport(new Vector3($sp[0], $sp[1], $sp[2]));
                        }
                        if ($arena->get('player5') == $pl->getName()) {
                            $sp = $arena->get('spawn5');
                            $pl->teleport(new Vector3($sp[0], $sp[1], $sp[2]));
                        }
                        if ($arena->get('player6') == $pl->getName()) {
                            $sp = $arena->get('spawn6');
                            $pl->teleport(new Vector3($sp[0], $sp[1], $sp[2]));
                        }
                    }
                }
                if ($arena->get('lobbytime') <= 10 && $arena->get('lobbytime') > 0) {
                    foreach ($players as $pl) {
                        $pl->addTitle("§a" . $arena->get('lobbytime'));
                    }
                }
                $lob = $arena->get('lobbytime');
                if ($lob == 60 || $lob == 50 || $lob == 40 || $lob == 30 || $lob == 20 || $lob == 10) {
                    foreach ($players as $pl) {
                        $pl->sendMessage($this->pl->t . "§7La partida comenzara en §a: §c" . $lob . " §7segundos..");
                        $this->pl->getServer()->getLevelByName($arena->get('level'))->addSound(new TimeSound($pl));
                    }
                }
                if ($lob == 5 || $lob == 4 || $lob == 3 || $lob == 2 || $lob == 1) {
                    foreach ($players as $pl) {
                        $pl->sendMessage($this->pl->t . "§7La partida comenzara en §a: §c" . $lob . " §7segundos..");
                        $this->pl->getServer()->getLevelByName($arena->get('level'))->addSound(new HitSound($pl));
                    }
                }
                if ($arena->get('lobbytime') == 0) {
                    if ($arena->get('status') == 'reset') {
                        $arena->set('status', 'reset');
                    } else {
                        $arena->set('status', 'off');
                    }


                }

                if ($this->pl->countPlayersGM($name) == 1 && $arena->get('lobbytime') == 0) {
                    $arena->set('status', 'reset');
                    $arena->save();
                    foreach ($players as $p) {
                        $namej = null;
                        if ($p->getGamemode() == 0) {
                            $namej = $p->getName();
                            $jug = $this->pl->getServer()->getPlayer($namej);
                            $this->pl->spawnGanador($jug, $name);
                            $jug->addTitle("§aYou Win§d!");
                        }
                        $l = $arena->get('lobby');
                        $p->teleport(new Vector3($l[0], $l[1], $l[2]));
                        $p->setGamemode(2);
                        $p->getArmorInventory()->clearAll();
                        $p->getInventory()->clearAll();
                    }

                }
                if ($arena->get('status') == 'off' or $arena->get('status') == 'reset') {
                    if (count($players) == 0) {
                        $this->pl->reload($this->pl->getServer()->getLevelByName($arena->get('level')));
                        $this->pl->reloadConfigPRIV($name);
                        foreach ($players as $p) {
                            $p->teleport($this->pl->getServer()->getDefaultLevel()->getSpawnLocation());
                            $p->setGamemode(2);
                            $this->pl->removeBossToPlayer($p, $name);
                            $this->pl->addBoss($p);
                            $this->pl->Menu($p);
                            $p->getInventory()->clearAll();
                            $p->getArmorInventory()->clearAll();
                            $this->pl->Menu($p);
                        }
                    }
                }
                if (!$arena->get('status') == 'reset') {
                    if ($this->pl->countPlayersGM($name) == 0 && $arena->get('lobbytime') == 0 && $arena->get('status') == 'off') {
                        $this->pl->reload($this->pl->getServer()->getLevelByName($arena->get('level')));
                        $this->pl->reloadConfigPRIV($name);
                        foreach ($players as $p) {
                            $p->teleport($this->pl->getServer()->getDefaultLevel()->getSpawnLocation());
                            $p->setGamemode(2);
                            $this->pl->removeBossToPlayer($p, $name);
                            $p->getInventory()->clearAll();
                            $p->getArmorInventory()->clearAll();
                        }
                    }
                }
                if ($arena->get('status') == 'off' && $arena->get('lobbytime') == 0) {
                    $time = $arena->get('time');
                    $time--;
                    $arena->set('time', $time);


                    if ($time == 899) {
                        $this->pl->fillChests($this->pl->getServer()->getLevelByName($arena->get('level')));

                        foreach ($players as $pl) {
                            $this->pl->getServer()->getLevelByName($arena->get('level'))->addSound(new TotemSound($pl));
                            $pl->setImmobile(false);
                            $pl->setGamemode(0);
                            $pl->addTitle("");
                            $pl->getInventory()->clearAll();
                            $pl->getArmorInventory()->clearAll();
                            $this->pl->addKit($pl);
                            //remove cristal
                            $level = $this->pl->getServer()->getLevelByName($arena->get('level'));
                            //blocks bajos
                            $x = $pl->getX();
                            $y = $pl->getY();
                            $z = $pl->getZ();

                            $level->setBlock(new Vector3($x, $y - 1, $z), new Air());
                            $level->setBlock(new Vector3($x + 1, $y - 1, $z), new Air());
                            $level->setBlock(new Vector3($x - 1, $y - 1, $z), new Air());
                            $level->setBlock(new Vector3($x, $y - 2, $z + 1), new Air());
                            $level->setBlock(new Vector3($x, $y - 2, $z - 1), new Air());


                        }
                        foreach ($players as $pl) {
                            $pl->sendMessage($this->pl->t . "§6Comenzo la partida buena suerte!");
                        }
                    }
                    if ($time == 300) {
                        $this->pl->fillChests($this->pl->getServer()->getLevelByName($arena->get('level')));

                        foreach ($players as $pl) {
                            $pl->addTitle("§aCofres rellenados§e!");
                        }
                    }
                    if ($time == 299) {
                        foreach ($players as $pl) {
                            $pl->addTitle("");
                        }
                    }
                    if ($time == 0) {
                        $this->pl->reload($this->pl->getServer()->getLevelByName($arena->get('level')));
                        $this->pl->reloadConfigPRIV($name);
                        foreach ($players as $p) {
                            $p->teleport($this->pl->getServer()->getDefaultLevel()->getSpawnLocation());
                            $p->setGamemode(2);
                            $this->pl->removeBossToPlayer($p, $name);
                            $p->getInventory()->clearAll();
                            $p->getArmorInventory()->clearAll();
                        }

                    }
//espectador an actions
                    foreach ($players as $pl) {
                        if ($arena->get('status') == 'off') {
                            if ($pl->getGamemode() == 3) {
                                $pl->sendTip("§l§bEres espectador");

                            }
                        }
                    }


                }

                if ($arena->get('status') == 'reset' && $arena->get('lobbytime') == 0) {
                    $reset = $arena->get('resetTime');
                    $reset--;
                    $arena->set('resetTime', $reset);
                    $g = $arena->get('ganador');
                    $center = new Vector3($g[0], $g[1] + 2, $g[2]);
                    $radius = 1;
                    $particles = array(new FlameParticle($center), new HeartParticle($center), new RedstoneParticle($center), new PortalParticle($center));
                    $rand = $particles[array_rand($particles)];
                    $particle = $rand;
                    for ($a = 0; $a < 100; $a++) {
                        $pitch = (mt_rand() / mt_getrandmax() - 0.5) * M_PI;
                        $yaw = mt_rand() / mt_getrandmax() * 2 * M_PI;
                        $yi = -sin($pitch);
                        $delta = cos($pitch);
                        $xi = -sin($yaw) * $delta;
                        $zi = cos($yaw) * $delta;
                        $vector = new Vector3($xi, $yi, $zi);
                        $pi = $center->add($vector->normalize()->multiply($radius));
                        $particle->setComponents($pi->x, $pi->y + 0.3, $pi->z);
                        $this->pl->getServer()->getLevelByName($arena->get('level'))->addParticle($particle);

                    }
                    foreach ($players as $p) {
                        $p->setFood(20);
                        $p->setHealth(20);
                    }
                    if ($reset <= 1) {
                        $this->pl->reload($this->pl->getServer()->getLevelByName($arena->get('level')));
                        $this->pl->reloadConfigPRIV($name);
                        foreach ($players as $p) {
                            $this->pl->addBoss($p);
                            $p->teleport($this->pl->getServer()->getDefaultLevel()->getSpawnLocation());
                            $p->setGamemode(2);
                            $this->pl->removeBossToPlayer($p, $name);
                            $p->getInventory()->clearAll();
                            $p->getArmorInventory()->clearAll();
                            $this->pl->Menu($p);
                        }
                    }
                }
                $arena->save();
            }
        }


    }
}
