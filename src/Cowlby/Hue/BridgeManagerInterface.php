<?php

namespace Cowlby\Hue;

interface BridgeManagerInterface
{
    public function discover();

    public function register($username);
}
