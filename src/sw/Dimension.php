<?php

namespace sw;

use pocketmine\{Player};
use pocketmine\network\mcpe\protocol\{PlayStatusPacket};
use pocketmine\scheduler\Task;

class Dimension extends Task
{
    public $p;
    public $pl;

    public function __construct(SkyWars $eid, PLayer $p)
    {

        $this->pl = $eid;
        $this->p = $p;

    }

    public function onRun(int $currentTick)
    {
        $pk = new PlayStatusPacket();
        $pk->status = 3;
        $this->p->dataPacket($pk);
    }


}