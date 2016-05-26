<?php

namespace PrestaShop\PrestaShop\Adapter\Hook;

use Hook;

class HookInformationProvider
{
    public function isDisplayHookName($hook_name)
    {
        return Hook::isDisplayHookName($hook_name);
    }
}
