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
use SimpleXMLElement;

/**
 * Class RemoteLocalizationPackLoader is responsible for loading localization pack data from prestashop.com
 */
final class RemoteLocalizationPackLoader implements LocalizationPackLoaderInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocalizationPackList()
    {
        $apiUrl = $this->configuration->get('_PS_API_URL_');

        $xmlLocalizationPacks = $this->loadXml($apiUrl.'/rss/localization.xml');
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
        $psVersion = str_replace('.', '', $this->configuration->get('_PS_VERSION_'));
        $psVersion = substr($psVersion, 0, 2);

        $apiUrl = $this->configuration->get('_PS_API_URL_');
        $localizationPackUrl = sprintf('%s/localization/%s/%s.xml', $apiUrl, $psVersion, $countryIso);

        $pack = $this->loadXml($localizationPackUrl);
        if (false === $pack) {
            return null;
        }

        return $pack;
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
