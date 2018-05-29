<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Localization\Pack\Loader;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;

/**
 * Class LocalLocalizationPackLoader is responsible for loading localization pack data from local host
 */
final class LocalLocalizationPackLoader implements LocalizationPackLoaderInterface
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
        $rootDir = $this->configuration->get('_PS_ROOT_DIR_');

        $localizationPackFile = sprintf('%s/localization/%s.xml', $rootDir, $countryIso);
        if (!file_exists($localizationPackFile)) {
            return null;
        }

        return $this->loadXml($localizationPackFile);
    }

    /**
     * Loads XML from local or remote file
     *
     * @param string $file
     *
     * @return SimpleXMLElement|null
     */
    private function loadXml($file)
    {
        $xml = simplexml_load_file($file);

        if (false === $xml) {
            return null;
        }

        return $xml;
    }
}
