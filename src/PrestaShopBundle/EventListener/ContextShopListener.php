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

namespace PrestaShopBundle\EventListener;

use Country;
use Currency;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Localization\Locale\Repository;
use PrestaShopBundle\Bridge\AdminController\ControllerConfiguration;
use PrestaShopBundle\Bridge\AdminController\ControllerConfigurationFactory;
use PrestaShopBundle\Bridge\AdminController\LegacyControllerBridgeFactory;
use PrestaShopBundle\Bridge\SymfonyLayoutFeature;
use Shop;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Tools;

class ContextShopListener
{
    public const CONTROLLER_CONFIGURATION_ATTRIBUTE = 'configuration_controller';

    /**
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * @var Repository
     */
    private $localeRepository;

    /**
     * @var LegacyControllerBridgeFactory
     */
    private $controllerBridgeFactory;

    /**
     * @var ControllerConfigurationFactory
     */
    private $controllerConfigurationFactory;

    /**
     * @var SymfonyLayoutFeature
     */
    private $symfonyLayoutFeature;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    public function __construct(
        LegacyContext $legacyContext,
        Repository $localeRepository,
        LegacyControllerBridgeFactory $controllerBridgeFactory,
        ControllerConfigurationFactory $controllerConfigurationFactory,
        SymfonyLayoutFeature $symfonyLayoutFeature,
        ConfigurationInterface $configuration
    ) {
        $this->legacyContext = $legacyContext;
        $this->localeRepository = $localeRepository;
        $this->controllerBridgeFactory = $controllerBridgeFactory;
        $this->controllerConfigurationFactory = $controllerConfigurationFactory;
        $this->symfonyLayoutFeature = $symfonyLayoutFeature;
        $this->configuration = $configuration;
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
        // For now this listener is only used when the symfony layout feature is enabled, when everything will be done
        // this condition can be removed
        if (!$this->symfonyLayoutFeature->isEnabled()) {
            return;
        }

        $legacyControllerName = $event->getRequest()->attributes->get('_legacy_controller');
        if (empty($legacyControllerName)) {
            return;
        }

        $controllerConfiguration = $this->controllerConfigurationFactory->create($legacyControllerName);
        $event->getRequest()->attributes->set(self::CONTROLLER_CONFIGURATION_ATTRIBUTE, $controllerConfiguration);
        $legacyBridgeController = $this->controllerBridgeFactory->create($controllerConfiguration);

        // Perform all the initialisation that was previously done in AdminController::initShopContext
        $context = $this->legacyContext->getContext();
        $context->controller = $legacyBridgeController;
        $this->initShopContext($controllerConfiguration);
        $legacyBridgeController->setMedia(true);
    }

    private function initShopContext(ControllerConfiguration $controllerConfiguration): void
    {
        $context = $this->legacyContext->getContext();

        $context->smarty->assign('link', $context->link);
        $context->currentLocale = $this->localeRepository->getLocale(
            $context->language->getLocale()
        );

        // Change shop context ?
        if (Shop::isFeatureActive() && Tools::getValue('setShopContext') !== false) {
            $context->cookie->shopContext = Tools::getValue('setShopContext');
            $url = parse_url($_SERVER['REQUEST_URI']);
            $query = (isset($url['query'])) ? $url['query'] : '';
            parse_str($query, $parse_query);
            unset($parse_query['setShopContext'], $parse_query['conf']);
            $http_build_query = http_build_query($parse_query, '', '&');
            $controllerConfiguration->redirectAfter = $url['path'] . ($http_build_query ? '?' . $http_build_query : '');
        } elseif (!Shop::isFeatureActive()) {
            $context->cookie->shopContext = 's-' . (int) $this->configuration->get('PS_SHOP_DEFAULT');
        } elseif (Shop::getTotalShops(false, null) < 2 && $context->employee->isLoggedBack()) {
            $context->cookie->shopContext = 's-' . (int) $context->employee->getDefaultShopID();
        }

        $shop_id = null;
        Shop::setContext(Shop::CONTEXT_ALL);
        if ($context->cookie->shopContext && $context->employee->isLoggedBack()) {
            $split = explode('-', $context->cookie->shopContext);
            if (count($split) == 2) {
                if ($split[0] == 'g') {
                    if ($context->employee->hasAuthOnShopGroup((int) $split[1])) {
                        Shop::setContext(Shop::CONTEXT_GROUP, (int) $split[1]);
                    } else {
                        $shop_id = (int) $context->employee->getDefaultShopID();
                        Shop::setContext(Shop::CONTEXT_SHOP, $shop_id);
                    }
                } elseif (Shop::getShop((int) $split[1]) && $context->employee->hasAuthOnShop((int) $split[1])) {
                    $shop_id = (int) $split[1];
                    Shop::setContext(Shop::CONTEXT_SHOP, $shop_id);
                } else {
                    $shop_id = (int) $context->employee->getDefaultShopID();
                    Shop::setContext(Shop::CONTEXT_SHOP, $shop_id);
                }
            }
        }

        // Check multishop context and set right context if need
        if (!($controllerConfiguration->multiShopContext & Shop::getContext())) {
            if (Shop::getContext() == Shop::CONTEXT_SHOP && !($controllerConfiguration->multiShopContext & Shop::CONTEXT_SHOP)) {
                Shop::setContext(Shop::CONTEXT_GROUP, Shop::getContextShopGroupID());
            }
            if (Shop::getContext() == Shop::CONTEXT_GROUP && !($controllerConfiguration->multiShopContext & Shop::CONTEXT_GROUP)) {
                Shop::setContext(Shop::CONTEXT_ALL);
            }
        }

        // Replace existing shop if necessary
        if (!$shop_id) {
            $context->shop = new Shop((int) $this->configuration->get('PS_SHOP_DEFAULT'));
        } elseif ($context->shop->id != $shop_id) {
            $context->shop = new Shop((int) $shop_id);
        }

        // Replace current default country
        $context->country = new Country((int) $this->configuration->get('PS_COUNTRY_DEFAULT'));
        $context->currency = Currency::getDefaultCurrency();
    }
}
