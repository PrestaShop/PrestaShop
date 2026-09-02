<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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

    public function getDefaultTabLink(): ?string
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

    public function getBaseImgUrl(): string
    {
        return $this->templateVariables->getBaseUrl() . 'img/';
    }
}
