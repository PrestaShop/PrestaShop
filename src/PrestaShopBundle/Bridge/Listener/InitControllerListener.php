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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Bridge\Listener;

use \Context;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShopBundle\Bridge\Controller\ControllerBridgeInterface;
use PrestaShopBundle\Bridge\Controller\ControllerConfigurationFactory;
use PrestaShop\PrestaShop\Core\Localization\Locale\Repository;
use \Shop;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use \Tab;
use \Tools;

/**
 * Init Controller by configuring something needs in all Controller migrate horizontally
 */
class InitControllerListener
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var ControllerConfigurationFactory
     */
    private $configurationFactory;

    /**
     * @var Repository
     */
    private $localeRepository;

    public function __construct(LegacyContext $legacyContext, ControllerConfigurationFactory $configurationFactory, Repository $localeRepository)
    {
        $this->context = $legacyContext->getContext();
        $this->configurationFactory = $configurationFactory;
        $this->localeRepository = $localeRepository;
    }

    public function onKernelController(ControllerEvent $event)
    {
        if (!$this->supports($event)) {
            return;
        }

        $controller = $event->getController()[0];

        $controller->php_self = get_class($controller);
        $this->context->controller = $controller;
        $this->context->smarty->assign('link', $this->context->link);

        $this->context->currentLocale = $this->localeRepository->getLocale(
            $this->context->language->getLocale()
        );

        $controller->controllerConfiguration = $this->configurationFactory->create([
            'id' => Tab::getIdFromClassName(
                get_class($controller)::CONTROLLER_NAME_LEGACY
            ),
            'controllerName' => get_class($controller),
            'controllerNameLegacy' => get_class($controller)::CONTROLLER_NAME_LEGACY,
            'positionIdentifier' => get_class($controller)::POSITION_IDENTIFIER,
            'table' => get_class($controller)::TABLE,
        ]);

        $this->setCurrentIndex($controller);
        $this->initToken($controller, $event->getRequest());
    }

    private function supports(ControllerEvent $event): bool
    {
        if (!is_array($event->getController()) || !isset($event->getController()[0])) {
            return false;
        }

        if (!$event->getController()[0] instanceof ControllerBridgeInterface) {
            return false;
        }

        return true;
    }

    private function setCurrentIndex(ControllerBridgeInterface $controller): void
    {
        // Set current index
        $currentIndex = 'index.php' . (($controllerName = Tools::getValue('controller')) ? '?controller=' . $controllerName : '');
        if ($back = Tools::getValue('back')) {
            $currentIndex .= '&back=' . urlencode($back);
        }

        $controller->controllerConfiguration->currentIndex = $currentIndex;
    }

    /**
     * Sets the smarty variables and js defs used to show / hide some notifications.
     */
    private function initToken(ControllerBridgeInterface $controller, Request $request)
    {
        $controller->controllerConfiguration->token = $request->query->get('_token');
    }
}
