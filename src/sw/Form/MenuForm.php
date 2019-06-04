<?php

namespace sw\Form;

use pocketmine\form\Form;
use pocketmine\Player;

class MenuForm implements Form
{

    private $datos;

    private $proceso;

    public function __construct(array $datos, callable $proceso)

    {

        $this->datos = $datos;

        $this->proceso = $proceso;

    }


    public function handleResponse(Player $player, $data): void

    {

        $llamada = $this->proceso;

        $llamada($player, $data);

    }

    public function jsonSerialize()

    {

        return $this->datos;

    }


}