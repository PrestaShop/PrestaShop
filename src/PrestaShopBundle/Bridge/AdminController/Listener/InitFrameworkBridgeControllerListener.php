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

declare(strict_types=1);

namespace PrestaShopBundle\Bridge\AdminController\Listener;

use Context;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\Localization\Locale\Repository;
use PrestaShopBundle\Bridge\AdminController\ControllerConfigurationFactory;
use PrestaShopBundle\Bridge\AdminController\FrameworkBridgeControllerInterface;
use PrestaShopBundle\Bridge\AdminController\LegacyControllerBridgeFactory;
use PrestaShopBundle\Entity\Repository\FeatureFlagRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

/**
 * Initializes FrameworkBridgeController
 *
 * @see FrameworkBridgeControllerInterface
 */
class InitFrameworkBridgeControllerListener
{
    public const USE_SYMFONY_LAYOUT_ATTRIBUTE = 'use_symfony_layout';
    public const CONTROLLER_CONFIGURATION_ATTRIBUTE = 'configuration_controller';

    /**
     * @var Context
     */
    private $context;

    /**
     * @var Repository
     */
    private $localeRepository;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var FeatureFlagRepository
     */
    private $featureFlagRepository;

    /**
     * @var LegacyControllerBridgeFactory
     */
    private $controllerBridgeFactory;

    /**
     * @var ControllerConfigurationFactory
     */
    private $controllerConfigurationFactory;

    public function __construct(
        LegacyContext $legacyContext,
        Repository $localeRepository,
        RequestStack $requestStack,
        FeatureFlagRepository $featureFlagRepository,
        LegacyControllerBridgeFactory $controllerBridgeFactory,
        ControllerConfigurationFactory $controllerConfigurationFactory
    ) {
        $this->context = $legacyContext->getContext();
        $this->localeRepository = $localeRepository;
        $this->requestStack = $requestStack;
        $this->featureFlagRepository = $featureFlagRepository;
        $this->controllerBridgeFactory = $controllerBridgeFactory;
        $this->controllerConfigurationFactory = $controllerConfigurationFactory;
    }

    /**
     * This method is the first entry point of horizontal migration.
     * In this method we:
     *      - Assign an instance of itself to the controller in the php_self variable..
     *        This comportment is needed for legacies hooks.
     *      - Assign to the context an instance of the controller.
     *        This is needed for all legacy classes that use the context and used controller instance.
     *      - Define the current locale for this request.
     *      - Create the configuration object needed by each controller migrate horizontally.
     *      - Init the current index which is needed by legacy Helper
     *      - Get the token from the URL and store hit in the controller configuration object
     *
     * @param ControllerEvent $event
     *
     * @return void
     */
    public function onKernelController(ControllerEvent $event): void
    {
        $legacyBridgeController = null;
        $bridgeController = $this->getBridgeController($event);
        if (null !== $bridgeController) {
            $legacyBridgeController = $this->controllerBridgeFactory->create($bridgeController->getControllerConfiguration());
        } elseif ($this->isUsingSymfonyLayout()) {
            $legacyControllerName = $event->getRequest()->attributes->get('_legacy_controller');
            if (!empty($legacyControllerName)) {
                $event->getRequest()->attributes->set(self::USE_SYMFONY_LAYOUT_ATTRIBUTE, true);

                $controllerConfiguration = $this->controllerConfigurationFactory->create($legacyControllerName);
                $event->getRequest()->attributes->set(self::CONTROLLER_CONFIGURATION_ATTRIBUTE, $controllerConfiguration);
                $legacyBridgeController = $this->controllerBridgeFactory->create($controllerConfiguration);
                $legacyBridgeController->setMedia(true);
            }
        }

        if (null !== $legacyBridgeController) {
            $this->context->smarty->assign('link', $this->context->link);
            $this->context->currentLocale = $this->localeRepository->getLocale(
                $this->context->language->getLocale()
            );
            $this->context->controller = $legacyBridgeController;
        }
    }

    private function isUsingSymfonyLayout(): bool
    {
        if ($this->requestStack->getCurrentRequest()->query->getBoolean('use_symfony_layout', false)) {
            return true;
        }

        return $this->featureFlagRepository->isEnabled(FeatureFlagSettings::SYMFONY_LAYOUT);
    }

    private function getBridgeController(ControllerEvent $event): ?FrameworkBridgeControllerInterface
    {
        if (!is_array($event->getController()) || !isset($event->getController()[0])) {
            return null;
        }

        if (!$event->getController()[0] instanceof FrameworkBridgeControllerInterface) {
            return null;
        }

        /** @var FrameworkBridgeControllerInterface $controller */
        $controller = $event->getController()[0];

        if (!is_string(get_class($controller))) {
            return null;
        }

        return $controller;
    }
}
