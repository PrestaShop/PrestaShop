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

namespace PrestaShopBundle\Bridge\AdminController\Listener;

use Context;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Localization\Locale\Repository;
use PrestaShopBundle\Bridge\AdminController\FrameworkBridgeControllerInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

/**
 * Initializes FrameworkBridgeController
 *
 * @see FrameworkBridgeControllerInterface
 */
class InitFrameworkBridgeControllerListener
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var Repository
     */
    private $localeRepository;

    /**
     * @param LegacyContext $legacyContext
     * @param Repository $localeRepository
     */
    public function __construct(
        LegacyContext $legacyContext,
        Repository $localeRepository
    ) {
        $this->context = $legacyContext->getContext();
        $this->localeRepository = $localeRepository;
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
        if (!$this->supports($event)) {
            return;
        }

        /** @var FrameworkBridgeControllerInterface $controller */
        $controller = $event->getController()[0];

        if (!is_string(get_class($controller))) {
            return;
        }

        $this->context->smarty->assign('link', $this->context->link);

        $this->context->currentLocale = $this->localeRepository->getLocale(
            $this->context->language->getLocale()
        );

        $this->context->controller = $controller->getLegacyControllerBridge();
    }

    /**
     * @param ControllerEvent $event
     *
     * @return bool
     */
    private function supports(ControllerEvent $event): bool
    {
        if (!is_array($event->getController()) || !isset($event->getController()[0])) {
            return false;
        }

        if (!$event->getController()[0] instanceof FrameworkBridgeControllerInterface) {
            return false;
        }

        return true;
    }
}
