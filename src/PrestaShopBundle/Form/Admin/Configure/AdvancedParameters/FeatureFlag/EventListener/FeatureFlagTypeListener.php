<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\FeatureFlag\EventListener;

use PrestaShop\PrestaShop\Adapter\Tab\TabDataProvider;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Tab\Command\UpdateTabStatusByClassNameCommand;
use PrestaShopBundle\Service\Hook\HookEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @experimental
 */
class FeatureFlagTypeListener implements EventSubscriberInterface
{
    /** @var CommandBusInterface */
    private $commandBus;

    /** @var TabDataProvider */
    private $tabDataProvider;

    public function __construct(CommandBusInterface $commandBus, TabDataProvider $tabDataProvider)
    {
        $this->commandBus = $commandBus;
        $this->tabDataProvider = $tabDataProvider;
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
            $this->tabDataProvider->resetTabCache();
        }
    }
}
