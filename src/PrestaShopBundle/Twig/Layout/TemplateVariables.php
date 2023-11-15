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

/**
 * Container for variables in different templates or components
 */
class TemplateVariables
{
    private ?string $displayBackOfficeTop;
    private string $isoUser;
    private bool $isSymfonyLayoutEnabled;
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
        ?string $displayBackOfficeTop,
        string $isoUser,
        bool $isSymfonyLayoutEnabled,
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
        $this->displayBackOfficeTop = $displayBackOfficeTop;
        $this->isoUser = $isoUser;
        $this->isSymfonyLayoutEnabled = $isSymfonyLayoutEnabled;
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

    public function getDisplayBackOfficeTop(): ?string
    {
        return $this->displayBackOfficeTop;
    }

    public function getIsoUser(): string
    {
        return $this->isoUser;
    }

    public function isSymfonyLayoutEnabled(): bool
    {
        return $this->isSymfonyLayoutEnabled;
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
