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

namespace PrestaShopBundle\Twig\Extension;

use Media;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Shop\ShopConstraintContextInterface;
use PrestaShopBundle\Bridge\AdminController\ControllerConfiguration;
use PrestaShopBundle\Bridge\Smarty\ConfiguratorInterface;
use PrestaShopBundle\Bridge\SymfonyLayoutFeature;
use PrestaShopBundle\EventListener\ContextShopListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tab;
use Tools;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

/**
 * Handles twig variables for pure symfony layout (that replaces the old legacy layout),
 * it is tightly coupled with ContextShopListener which is responsible for creating the ControllerConfiguration
 * and set the ControllerBridge on Context->controller, the controller configuration is then accessible via a request
 * attribute.
 *
 * These components/listeners were initially created for horizontal migration (which has been
 * abandoned since) but it turns out they fit the need we have for Symfony layout especially
 * handling most of the variables previously initialized by AdminLegacyLayoutController.
 */
class SymfonyLayoutExtension extends AbstractExtension implements GlobalsInterface
{
    /** @var LegacyContext */
    private $context;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var ShopConstraint
     */
    private $contextShopConstraint;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ConfiguratorInterface[]
     */
    private $configurators;

    /**
     * @var SymfonyLayoutFeature
     */
    private $symfonyLayoutFeature;

    public function __construct(
        LegacyContext $context,
        RequestStack $requestStack,
        ShopConstraintContextInterface $shopConstraintContextInterface,
        TranslatorInterface $translator,
        Configuration $configuration,
        iterable $configurators,
        SymfonyLayoutFeature $symfonyLayoutFeature
    ) {
        $this->context = $context;
        $this->requestStack = $requestStack;
        $this->contextShopConstraint = $shopConstraintContextInterface->getShopConstraint();
        $this->translator = $translator;
        $this->configuration = $configuration;
        $this->configurators = $configurators;
        $this->symfonyLayoutFeature = $symfonyLayoutFeature;
    }

    public function getGlobals(): array
    {
        $legacyLayoutVariables = [];
        $useSymfonyLayout = $this->symfonyLayoutFeature->isEnabled();
        if ($this->symfonyLayoutFeature->isEnabled()) {
            $request = $this->requestStack->getCurrentRequest();
            $controllerConfiguration = $request->attributes->get(ContextShopListener::CONTROLLER_CONFIGURATION_ATTRIBUTE);
            if ($controllerConfiguration instanceof ControllerConfiguration) {
                foreach ($this->configurators as $configurator) {
                    $configurator->configure($controllerConfiguration);
                }
                $legacyLayoutVariables = $this->getLegacyLayoutVariables($controllerConfiguration);
            }
        }

        return $legacyLayoutVariables + [
            'use_legacy_layout' => !$useSymfonyLayout,
            'use_symfony_layout' => $useSymfonyLayout,
        ];
    }

    /**
     * This method is responsible for returning all the variables previously handled by the AdminLegacyLayoutControllerCore
     * to render the legacy layout. This is a quick solution to make the new layout compatible and working without thinking
     * too much about what is needed. This should probably be replaced or refactored piece by piece.
     *
     * @return array
     */
    private function getLegacyLayoutVariables(ControllerConfiguration $controllerConfiguration): array
    {
        $link = $this->context->getContext()->link;

        $metaTitle = '';
        if (!empty($controllerConfiguration->metaTitle)) {
            $metaTitle = strip_tags(implode(' ' . $this->getConfiguration('PS_NAVIGATION_PIPE') . ' ', $controllerConfiguration->metaTitle));
        } elseif (!empty($controllerConfiguration->toolbarTitle)) {
            $metaTitle = strip_tags(implode(' ' . $this->getConfiguration('PS_NAVIGATION_PIPE') . ' ', $controllerConfiguration->toolbarTitle));
        }

        $shopName = $this->getConfiguration('PS_SHOP_NAME');
        if ($this->contextShopConstraint->getShopId()) {
            $editFieldFor = sprintf(
                '%s <b>%s</b>',
                $this->translator->trans('This field will be modified for this shop:', [], 'Admin.Notifications.Info'),
                $shopName
            );
        } elseif ($this->contextShopConstraint->getShopGroupId()) {
            $editFieldFor = sprintf(
                '%s <b>%s</b>',
                $this->translator->trans('This field will be modified for all shops in this shop group:', [], 'Admin.Notifications.Info'),
                $shopName
            );
        } else {
            $editFieldFor = $this->translator->trans('This field will be modified for all your shops.', [], 'Admin.Notifications.Info');
        }

        $employee = $this->context->getContext()->employee;
        if (isset($employee)) {
            $employeeToken = Tools::getAdminToken(
                'AdminEmployees' .
                (int) Tab::getIdFromClassName('AdminEmployees') .
                (int) $employee->id
            );
        } else {
            $employeeToken = '';
        }

        return $controllerConfiguration->templateVars + [
            'current_index' => $controllerConfiguration->legacyCurrentIndex,
            'display_header' => $controllerConfiguration->displayHeader,
            'display_header_javascript' => $controllerConfiguration->displayHeaderJavascript,
            'display_footer' => $controllerConfiguration->displayFooter,
            'js_def' => Media::getJsDef(),
            'toggle_navigation_url' => $link->getAdminLink('AdminEmployees', true, [], [
                'action' => 'toggleMenu',
            ]),
            'meta_title' => $metaTitle,
            'multi_shop_edit_for' => $editFieldFor,
            'employee_token' => $employeeToken,
            'baseAdminUrl' => __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_) . '/',
        ];
    }

    private function getConfiguration(string $configurationName): string
    {
        return $this->configuration->get($configurationName, null, $this->contextShopConstraint);
    }
}
