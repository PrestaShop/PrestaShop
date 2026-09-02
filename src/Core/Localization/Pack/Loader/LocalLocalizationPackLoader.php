<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\Pack\Loader;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;

/**
 * Class LocalLocalizationPackLoader is responsible for loading localization pack data from local host.
 */
final class LocalLocalizationPackLoader extends AbstractLocalizationPackLoader
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocalizationPackList()
    {
        $rootDir = $this->configuration->get('_PS_ROOT_DIR_');

        $localizationFile = sprintf('%s/localization/localization.xml', $rootDir);
        if (!file_exists($localizationFile)) {
            return null;
        }

        return $this->loadXml($localizationFile);
    }

    /**
     * {@inheritdoc}
     */
    public function getLocalizationPack($countryIso)
    {
        $localizationPackFile = sprintf('%s/localization/%s.xml', $this->configuration->get('_PS_ROOT_DIR_'), $countryIso);
        if (!file_exists($localizationPackFile)) {
            return null;
        }

        return $this->loadXml($localizationPackFile);
    }
}
