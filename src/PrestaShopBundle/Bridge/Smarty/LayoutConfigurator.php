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

namespace PrestaShopBundle\Bridge\Smarty;

use HelperShop;
use Media;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Tab\Repository\TabRepository;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Shop\ShopConstraintContextInterface;
use PrestaShopBundle\Bridge\AdminController\ControllerConfiguration;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tools;

class LayoutConfigurator implements ConfiguratorInterface
{
    public function __construct(
        private ConfigurationInterface $configuration,
        private LegacyContext $legacyContext,
        private TranslatorInterface $translator,
        private ShopConstraintContextInterface $shopConstraintContext,
        private TabRepository $tabRepository
    ) {
    }

    public function configure(ControllerConfiguration $controllerConfiguration)
    {
        $shopName = $this->configuration->get('PS_SHOP_NAME');
        $shopConstraint = $this->shopConstraintContext->getShopConstraint();
        if ($shopConstraint->getShopId()) {
            $editFieldFor = sprintf(
                '%s <b>%s</b>',
                $this->translator->trans('This field will be modified for this shop:', [], 'Admin.Notifications.Info'),
                $shopName
            );
        } elseif ($shopConstraint->getShopGroupId()) {
            $editFieldFor = sprintf(
                '%s <b>%s</b>',
                $this->translator->trans('This field will be modified for all shops in this shop group:', [], 'Admin.Notifications.Info'),
                $shopName
            );
        } else {
            $editFieldFor = $this->translator->trans('This field will be modified for all your shops.', [], 'Admin.Notifications.Info');
        }

        $employee = $this->legacyContext->getContext()->employee;
        if (isset($employee)) {
            $employeeToken = Tools::getAdminToken(
                'AdminEmployees' .
                $this->tabRepository->getIdByClassName('AdminEmployees')->getValue() .
                (int) $employee->id
            );
        } else {
            $employeeToken = '';
        }

        $baseUri = $this->legacyContext->getContext()->shop->getBaseURI();
        $helperShop = new HelperShop();
        $controllerConfiguration->templateVars['display_header'] = $controllerConfiguration->displayHeader;
        $controllerConfiguration->templateVars['display_header_javascript'] = $controllerConfiguration->displayHeaderJavascript;
        $controllerConfiguration->templateVars['display_footer'] = $controllerConfiguration->displayFooter;
        $controllerConfiguration->templateVars['js_def'] = Media::getJsDef();
        $controllerConfiguration->templateVars['toggle_navigation_url'] = $this->legacyContext->getAdminLink('AdminEmployees', true, [
            'action' => 'toggleMenu',
        ]);
        $controllerConfiguration->templateVars['shop_list'] = $helperShop->getRenderedShopList();

        $controllerConfiguration->templateVars['current_index'] = $controllerConfiguration->legacyCurrentIndex;
        $controllerConfiguration->templateVars['multi_shop_edit_for'] = $editFieldFor;
        $controllerConfiguration->templateVars['employee_token'] = $employeeToken;
        $controllerConfiguration->templateVars['baseAdminUrl'] = $baseUri . basename(_PS_ADMIN_DIR_) . '/';
        $controllerConfiguration->templateVars['shop_list'] = $helperShop->getRenderedShopList();
        $controllerConfiguration->templateVars['current_shop_name'] = $helperShop->getCurrentShopName();

        /* @see ControllerConfiguration::$errors */
        /* @see ControllerConfiguration::$warnings */
        /* @see ControllerConfiguration::$informations */
        /* @see ControllerConfiguration::$confirmations */
        foreach (['errors', 'warnings', 'informations', 'confirmations'] as $type) {
            if (!is_array($controllerConfiguration->$type)) {
                $controllerConfiguration->$type = (array) $controllerConfiguration->$type;
            }
            $controllerConfiguration->templateVars[$type] = $controllerConfiguration->json ? json_encode(array_unique($controllerConfiguration->$type)) : array_unique($controllerConfiguration->$type);
        }
        $controllerConfiguration->templateVars['baseAdminUrl'] = $baseUri . basename(_PS_ADMIN_DIR_) . '/';
    }
}
