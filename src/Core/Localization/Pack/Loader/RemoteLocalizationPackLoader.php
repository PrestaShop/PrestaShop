<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\Pack\Loader;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Foundation\Version;

/**
 * Class RemoteLocalizationPackLoader is responsible for loading localization pack data from prestashop.com.
 */
final class RemoteLocalizationPackLoader extends AbstractLocalizationPackLoader
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var Version
     */
    private $version;

    /**
     * @param ConfigurationInterface $configuration
     * @param Version $version
     */
    public function __construct(ConfigurationInterface $configuration, Version $version)
    {
        $this->configuration = $configuration;
        $this->version = $version;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocalizationPackList()
    {
        $apiUrl = $this->configuration->get('_PS_API_URL_');

        $xmlLocalizationPacks = $this->loadXml($apiUrl . '/rss/localization.xml');
        if (!$xmlLocalizationPacks) {
            return null;
        }

        return $xmlLocalizationPacks;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocalizationPack($countryIso)
    {
        $apiUrl = $this->configuration->get('_PS_API_URL_');
        $localizationPackUrl = sprintf('%s/localization/%s/%s.xml', $apiUrl, $this->version->getMajorVersion(), $countryIso);

        return $this->loadXml($localizationPackUrl);
    }
}
