<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
