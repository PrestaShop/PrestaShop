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

namespace PrestaShop\PrestaShop\Core\Language\Pack\Loader;

use PrestaShop\PrestaShop\Core\Foundation\Version;

/**
 * Class RemoteLanguagePackLoader is responsible for retrieving language pack data from remote host.
 */
final class RemoteLanguagePackLoader implements LanguagePackLoaderInterface
{
    /**
     * The languages repository base path
     *
     * @TODO : add this to {prestashop_repository}/app/config/config.yml
     */
    private const LANG_REPOSITORY_BASE_PATH = 'http://i18n.prestashop.com';

    /**
     * @var string Prestashop version
     */
    private $version;

    /**
     * @param string $version Prestashop version
     */
    public function __construct(string $version)
    {
        // Ensure version format
        $this->version = Version::buildFromString($version)->getSemVersion();
    }

    /**
     * @param string $locale
     *
     * @return string requested URL
     */
    public function getLanguagePackUrl(string $locale = null): string
    {
        return self::LANG_REPOSITORY_BASE_PATH . "/translations/{$this->version}/{$locale}/{$locale}.zip";
    }

    /**
     * @return string requested URL
     */
    public function getLanguagePackListUrl(): string
    {
        return self::LANG_REPOSITORY_BASE_PATH . "/translations/{$this->version}/available_languages.json";
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguagePackList()
    {
        $normalizedLink = $this->getLanguagePackListUrl();
        $jsonResponse = file_get_contents($normalizedLink);
        $result = json_decode($jsonResponse, true) ?? [];

        return $result;
    }

    /**
     * Builds an instance from version defined in environment (`_PS_VERSION_`) _
     *
     * @return self
     */
    public static function build()
    {
        return new self(_PS_VERSION_);
    }
}
