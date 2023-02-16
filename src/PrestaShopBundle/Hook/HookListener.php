<?php

namespace PrestaShopBundle\Hook;

use PrestaShop\PrestaShop\Core\Hook\HookExecutorInterface;
use PrestaShopBundle\Service\Hook\RenderingHookEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class HookListener implements EventSubscriberInterface
{
    /**
     * @var HookExecutorInterface
     */
    private $hookRunner;

    public function __construct(HookExecutorInterface $hookRunner)
    {
        $this->hookRunner = $hookRunner;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            HookEvent::class => 'onHook',
            RenderingHookEvent::class => 'onHook',
        ];
    }

    public function onHook(HookEvent $event): void
    {
        $hook = $event->getHook();
        $this->hookRunner->exec($hook->getName(), $hook->getParameters());
    }
}
