<?php

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\FeatureFlag\EventListener;

use PrestaShop\PrestaShop\Adapter\Tab\TabDataProvider;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Tab\Command\UpdateTabStatusByClassNameCommand;
use PrestaShopBundle\Service\Hook\HookEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FeatureFlagTypeListener implements EventSubscriberInterface
{
    /** @var CommandBusInterface */
    private $commandBus;

    public function __construct(CommandBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public static function getSubscribedEvents()
    {
        return ['actionfeatureflagbetasave' => 'onFeatureFlagBetaSave'];
    }

    public function onFeatureFlagBetaSave(HookEvent $event): void
    {
        if (isset($event->getHookParameters()['form_data']['feature_flags']['authorization_server']['enabled'])) {
            $this->commandBus->handle(
                new UpdateTabStatusByClassNameCommand(
                    'AdminAuthorizationServer',
                    $event->getHookParameters()['form_data']['feature_flags']['authorization_server']['enabled']
                )
            );
            TabDataProvider::resetTabCache();
        }
    }
}
