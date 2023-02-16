<?php

namespace PrestaShopBundle\Hook;

use PrestaShop\PrestaShop\Core\Hook\Hook;
use PrestaShop\PrestaShop\Core\Hook\HookInterface;
use Symfony\Contracts\EventDispatcher\Event;

class HookEvent extends Event
{
    /**
     * @var Hook
     */
    private $hook;

    public function __construct(HookInterface $hook)
    {
        $this->hook = $hook;
    }

    /**
     * @return Hook|HookInterface
     */
    public function getHook()
    {
        return $this->hook;
    }
}
