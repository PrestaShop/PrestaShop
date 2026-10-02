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

    /**
     * Shipped iso codes TinyMCE carries under a different name. Without these the editor would fall
     * all the way back to English for a language it actually supports.
     */
    private const EDITOR_LANGUAGES = [
        'mx' => 'es_MX',
        'no' => 'nb',
        'qc' => 'fr',
        'tw' => 'zh_TW',
        'vn' => 'vi',
    ];

    /**
     * TinyMCE ships a language file per iso code and fails to load when one is missing, which is the
     * case for several languages PrestaShop ships. Callers that hand an iso code to the editor must
     * use this instead of the raw one.
     */
    public function getIsoUserForEditor(): string
    {
        foreach ([self::EDITOR_LANGUAGES[$this->isoUser] ?? null, $this->isoUser, 'en'] as $candidate) {
            if ($candidate !== null && file_exists(_PS_CORE_DIR_ . '/js/tiny_mce/langs/' . $candidate . '.js')) {
                return $candidate;
            }
        }

        return 'en';
    }

    /**
     * Moment resolves "xx-yy" by falling back to "xx", so most language codes need no help here.
     * These are the shipped ones it cannot resolve at all, because it names them differently rather
     * than not carrying them: moment has nb, fr-ca and zh-tw, but no no, qc or tw.
     */
    private const MOMENT_LANGUAGE_CODES = [
        'no' => 'nb',
        'qc' => 'fr-ca',
        'tw' => 'zh-tw',
        'tw-tw' => 'zh-tw',
        'vn' => 'vi',
    ];

    /**
     * Language code to hand to moment. Without it a date widget is silently drawn in English
     * instead of the employee language, since moment falls back rather than failing.
     */
    public function getMomentLanguageCode(string $fullLanguageCode): string
    {
        if (isset(self::MOMENT_LANGUAGE_CODES[$fullLanguageCode])) {
            return self::MOMENT_LANGUAGE_CODES[$fullLanguageCode];
        }

        // Only when there is no region to fall back on: "vi-vn" already reaches moment's "vi",
        // and overriding it from the iso code would replace a working value with a guess.
        if (!str_contains($fullLanguageCode, '-') && isset(self::MOMENT_LANGUAGE_CODES[$this->isoUser])) {
            return self::MOMENT_LANGUAGE_CODES[$this->isoUser];
        }

        return $fullLanguageCode;
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
