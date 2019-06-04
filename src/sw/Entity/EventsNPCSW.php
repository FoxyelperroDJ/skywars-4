<?php

namespace sw\Entity;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use sw\SkyWars;

class EventsNPCSW implements Listener
{
    public $db;

    public function __construct(SkyWars $plugin)
    {

        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
        $this->db = $plugin;
    }

    public function dano(EntityDamageEvent $evento)
    {
        if ($evento instanceof EntityDamageByEntityEvent) {
            $damager = $evento->getDamager();
            if ($damager instanceof Player) {
                if ($evento->getEntity() instanceof SWNPC) {
                    $evento->setCancelled(true);
                    $this->db->system($damager);
                }
            }
            if ($evento->getEntity() instanceof GanadorNPC) {
                $evento->setCancelled(true);
            }
        }
    }


}