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

use PrestaShop\PrestaShop\Adapter\LegacyContext;

/**
 * Allows you to define variables accessible globally in a twig rendering.
 * Only public methods will be accessible on the rendering.
 */
class PrestaShopLayoutGlobalVariables
{
    public function __construct(
        private readonly LegacyContext $context,
        private readonly TemplateVariables $templateVariables,
        private readonly SmartyVariablesFiller $assignSmartyVariables,
    ) {
    }

    /**
     * Enable New theme for smarty to avoid some problems with kpis for instance...
     * Allows you to fill variables in the smarty context
     * TODO: Need to be refactored, we need to find a proper way to initialize this smarty template directory when we display a migrated page
     */
    public function setupSmarty(string $title, string $metaTitle, bool $liteDisplay): void
    {
        $this->context->getContext()->smarty->setTemplateDir(_PS_BO_ALL_THEMES_DIR_ . 'new-theme/template/');
        $this->assignSmartyVariables->fill($title, $metaTitle, $liteDisplay);
    }

    public function getIsoUser(): string
    {
        return $this->templateVariables->getIsoUser();
    }

    public function isSymfonyLayoutEnabled(): bool
    {
        return $this->templateVariables->isSymfonyLayoutEnabled();
    }

    public function isRtlLanguage(): bool
    {
        return $this->templateVariables->isRtlLanguage();
    }

    public function getControllerName(): string
    {
        return $this->templateVariables->getControllerName();
    }

    public function isMultiShop(): bool
    {
        return $this->templateVariables->isMultiShop();
    }

    public function isMenuCollapsed(): bool
    {
        return $this->templateVariables->isMenuCollapsed();
    }

    public function getJsRouterMetadata(): array
    {
        return $this->templateVariables->getJsRouterMetadata();
    }

    public function isDebugMode(): bool
    {
        return $this->templateVariables->isDebugMode();
    }

    public function installDirExists(): bool
    {
        return $this->templateVariables->isInstallDirExists();
    }

    public function getVersion(): string
    {
        return $this->templateVariables->getVersion();
    }

    public function getDefaultTabLink(): string
    {
        return $this->templateVariables->getDefaultTabLink();
    }

    public function isMaintenanceEnabled(): bool
    {
        return $this->templateVariables->isMaintenanceEnabled();
    }

    public function isFrontOfficeAccessibleForAdmins(): bool
    {
        return $this->templateVariables->isFrontOfficeAccessibleForAdmins();
    }

    public function isDisplayedWithTabs(): bool
    {
        return $this->templateVariables->isDisplayedWithTabs();
    }

    public function getBaseUrl(): string
    {
        return $this->templateVariables->getBaseUrl();
    }
}
