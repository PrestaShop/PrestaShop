<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Twig\Layout;

/**
 * Container for variables in different templates or components
 */
class TemplateVariables
{
    private string $isoUser;
    private bool $isRtlLanguage;
    private string $controllerName;
    private bool $isMultiShop;
    private bool $isMenuCollapsed;
    private array $jsRouterMetadata;
    private bool $isDebugMode;
    private bool $installDirExists;
    private string $version;
    private ?string $defaultTabLink;
    private bool $isMaintenanceEnabled;
    private bool $isFrontOfficeAccessibleForAdmins;
    private bool $isDisplayedWithTabs;
    private string $baseUrl;

    public function __construct(
        string $isoUser,
        bool $isRtlLanguage,
        string $controllerName,
        bool $isMultiShop,
        bool $isMenuCollapsed,
        array $jsRouterMetadata,
        bool $isDebugMode,
        bool $installDirExists,
        string $version,
        ?string $defaultTabLink,
        bool $isMaintenanceEnabled,
        bool $isFrontOfficeAccessibleForAdmins,
        bool $isDisplayedWithTabs,
        string $baseUrl,
    ) {
        $this->isoUser = $isoUser;
        $this->isRtlLanguage = $isRtlLanguage;
        $this->controllerName = $controllerName;
        $this->isMultiShop = $isMultiShop;
        $this->isMenuCollapsed = $isMenuCollapsed;
        $this->jsRouterMetadata = $jsRouterMetadata;
        $this->isDebugMode = $isDebugMode;
        $this->installDirExists = $installDirExists;
        $this->version = $version;
        $this->defaultTabLink = $defaultTabLink;
        $this->isMaintenanceEnabled = $isMaintenanceEnabled;
        $this->isFrontOfficeAccessibleForAdmins = $isFrontOfficeAccessibleForAdmins;
        $this->isDisplayedWithTabs = $isDisplayedWithTabs;
        $this->baseUrl = $baseUrl;
    }

    public function getIsoUser(): string
    {
        return $this->isoUser;
    }

    public function isRtlLanguage(): bool
    {
        return $this->isRtlLanguage;
    }

    public function getControllerName(): string
    {
        return $this->controllerName;
    }

    public function isMultiShop(): bool
    {
        return $this->isMultiShop;
    }

    public function isMenuCollapsed(): bool
    {
        return $this->isMenuCollapsed;
    }

    public function getJsRouterMetadata(): array
    {
        return $this->jsRouterMetadata;
    }

    public function isDebugMode(): bool
    {
        return $this->isDebugMode;
    }

    public function isInstallDirExists(): bool
    {
        return $this->installDirExists;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getDefaultTabLink(): ?string
    {
        return $this->defaultTabLink;
    }

    public function isMaintenanceEnabled(): bool
    {
        return $this->isMaintenanceEnabled;
    }

    public function isFrontOfficeAccessibleForAdmins(): bool
    {
        return $this->isFrontOfficeAccessibleForAdmins;
    }

    public function isDisplayedWithTabs(): bool
    {
        return $this->isDisplayedWithTabs;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
}
