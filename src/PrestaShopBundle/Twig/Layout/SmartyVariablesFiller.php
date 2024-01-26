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

namespace PrestaShopBundle\Twig\Layout;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Context\CountryContext;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\LegacyControllerContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;

/**
 * Has the role of filling Smarty variables in the context.
 * To be used in a layout not based on a legacy controller.
 */
class SmartyVariablesFiller
{
    public function __construct(
        private readonly TemplateVariables $templateVariables,
        private readonly LegacyControllerContext $legacyControllerContext,
        private readonly LanguageContext $languageContext,
        private readonly LanguageContext $defaultLanguageContext,
        private readonly ShopContext $shopContext,
        private readonly CountryContext $countryContext,
        private readonly LegacyContext $legacyContext,
        private readonly Configuration $configuration,
    ) {
    }

    public function fill(string $title, string $metaTitle, bool $liteDisplay): void
    {
        $this->legacyContext->getSmarty()->assign(array_merge($this->getDefaultVariables(), [
            'title' => $title,
            'meta_title' => $metaTitle,
            'lite_display' => $liteDisplay,
        ]));
    }

    public function fillDefault(): void
    {
        $this->legacyContext->getSmarty()->assign($this->getDefaultVariables());
    }

    protected function getDefaultVariables(): array
    {
        $smartyVariables = [
            'maintenance_mode' => $this->templateVariables->isMaintenanceEnabled(),
            'maintenance_allow_admins' => $this->templateVariables->isFrontOfficeAccessibleForAdmins(),
            'debug_mode' => $this->templateVariables->isDebugMode(),
            'img_dir' => $this->templateVariables->getBaseUrl() . 'img/',
            'baseAdminUrl' => $this->templateVariables->getBaseUrl() . basename(_PS_ADMIN_DIR_) . '/',
            'base_url' => $this->templateVariables->getBaseUrl(),
            'lang_is_rtl' => $this->languageContext->isRTL(),
            'full_language_code' => $this->languageContext->getLanguageCode(),
            'country_iso_code' => $this->countryContext->getIsoCode(),
            'currentIndex' => $this->legacyControllerContext->currentIndex,
            'default_language' => $this->defaultLanguageContext->getId(),
            'js_router_metadata' => $this->templateVariables->getJsRouterMetadata(),
            'token' => $this->legacyControllerContext->token,
            'employee' => $this->legacyContext->getContext()->employee,
            'is_multishop' => $this->templateVariables->isMultiShop(),
            'shop_name' => $this->shopContext->getName(),
            'shop' => $this->legacyContext->getContext()->shop,
            'shop_group' => $this->legacyContext->getContext()->shop->getGroup(),
            'iso' => $this->templateVariables->getIsoUser(),
            'class_name' => $this->legacyControllerContext->className,
            'table' => $this->legacyControllerContext->table,
            'version' => $this->templateVariables->getVersion(),
            'link' => $this->legacyContext->getContext()->link,
            'controller_name' => $this->legacyControllerContext->controller_name,
            'login_link' => $this->legacyContext->getAdminLink('AdminLogin'),
            'logout_link' => $this->legacyContext->getAdminLink('AdminLogin', true, ['logout' => 1]),
            'round_mode' => $this->configuration->get('PS_PRICE_ROUND_MODE'),
            'help_box' => $this->configuration->get('PS_HELPBOX'),
            'url_post' => $this->legacyControllerContext->currentIndex . '&token=' . $this->legacyControllerContext->token,
            'stock_management' => $this->configuration->get('PS_STOCK_MANAGEMENT'),
            'install_dir_exists' => file_exists(_PS_ADMIN_DIR_ . '/../install'),
            'pic_dir' => $this->templateVariables->getBaseUrl() . 'upload/',
            'img_base_path' => $this->templateVariables->getBaseUrl() . basename(_PS_ADMIN_DIR_) . '/',
            'multishop_context' => $this->legacyControllerContext->multishop_context,
        ];

        $smartyVariablesAlias = [
            'multi_shop' => $smartyVariables['is_multishop'],
            'current_shop_name' => $smartyVariables['shop_name'],
            'iso_user' => $smartyVariables['iso'],
            'lang_iso' => $smartyVariables['iso'],
            'ps_version' => $smartyVariables['version'],
            'full_cldr_language_code' => $smartyVariables['full_language_code'],
            'current' => $smartyVariables['currentIndex'],
        ];

        return array_merge($smartyVariables, $smartyVariablesAlias);
    }
}
