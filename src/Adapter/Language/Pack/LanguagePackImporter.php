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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Language\Pack;

use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;
use PrestaShop\PrestaShop\Core\Language\Pack\Import\LanguagePackImporterInterface;
use PrestaShop\PrestaShop\Core\Language\Pack\LanguagePackInstallerInterface;

/**
 * Class LanguagePackImporter is responsible for importing language pack.
 */
final class LanguagePackImporter implements LanguagePackImporterInterface
{
    /**
     * @var LanguagePackInstallerInterface
     */
    private $languagePack;

    /**
     * @var CacheClearerInterface
     */
    private $entireCacheClearer;

    /**
     * @param LanguagePackInstallerInterface $languagePack
     * @param CacheClearerInterface $entireCacheClearer
     */
    public function __construct(
        LanguagePackInstallerInterface $languagePack,
        CacheClearerInterface $entireCacheClearer
    ) {
        $this->languagePack = $languagePack;
        $this->entireCacheClearer = $entireCacheClearer;
    }

    /**
     * {@inheritdoc}
     */
    public function import($isoCode)
    {
        $result = $this->languagePack->downloadAndInstallLanguagePack($isoCode);

        if (!empty($result)) {
            return $result;
        }

        $this->entireCacheClearer->clear();

        return [];
    }
}
