<?php

namespace Cowlby\Hue\Entity;

class Light
{
    protected $id;

    protected $name;

    protected $brightness;

    protected $hue;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setHue($hue)
    {
        $this->hue = $hue;
        return $this;
    }

    public function setBrightness($brightness)
    {
        $this->brightness = $brightness;
        return $this;
    }

    public function getBrightness()
    {
        return $this->brightness;
    }

    public function getHue()
    {
        return $this->hue;
    }
}
