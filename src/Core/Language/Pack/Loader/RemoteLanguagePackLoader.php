<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Language\Pack\Loader;

use PrestaShop\PrestaShop\Core\Foundation\Version;

/**
 * Class RemoteLanguagePackLoader is responsible for retrieving language pack data from remote host.
 */
final class RemoteLanguagePackLoader implements LanguagePackLoaderInterface
{
    /**
     * The link from which available languages are retrieved.
     */
    public const PACK_LINK = 'http://i18n.prestashop-project.org/translations/%ps_version%/available_languages.json';

    /**
     * @var Version
     */
    private $version;

    /**
     * @param Version $version
     */
    public function __construct(Version $version)
    {
        $this->version = $version;
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguagePackList()
    {
        $normalizedLink = str_replace('%ps_version%', $this->version->getSemVersion(), self::PACK_LINK);
        $jsonResponse = file_get_contents($normalizedLink);

        $result = [];
        if ($jsonResponse) {
            $result = json_decode($jsonResponse, true);
        }

        return $result;
    }
}
