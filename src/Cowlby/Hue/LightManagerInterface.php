<?php

namespace Cowlby\Hue;

use Cowlby\Hue\Entity\Light;

interface LightManagerInterface
{
    public function findAll();

    public function find($id);

    public function turnOn(Light $light);

    public function turnOff(Light $light);

//     public function setBrightness();

//     public function setHue();

    public function changeState($id, array $state);
}
