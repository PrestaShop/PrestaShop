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

namespace PrestaShop\PrestaShop\Core\Localization\Pack;

use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;

/**
 * Class LocalizationPackDownloader is responsible for downloading localization pack
 */
final class LocalizationPackDownloader implements LocalizationPackDownloaderInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var Tools
     */
    private $tools;

    /**
     * @param ConfigurationInterface $configuration
     * @param Tools $tools
     */
    public function __construct(
        ConfigurationInterface $configuration,
        Tools $tools
    ) {
        $this->configuration = $configuration;
        $this->tools = $tools;
    }

    /**
     * @param string $countryIsoCode
     *
     * @return bool|null|string
     */
    public function download($countryIsoCode)
    {
        $psVersion = str_replace('.', '', $this->configuration->get('_PS_VERSION_'));
        $psVersion = substr($psVersion, 0, 2);

        $apiUrl = $this->configuration->get('_PS_API_URL_');
        $localizationPackUrl = $apiUrl.'/localization/'.$psVersion.'/'.$countryIsoCode.'.xml';

        $pack = $this->tools->getFileContents($localizationPackUrl);
        if (false === $pack) {
            return null;
        }

        return $pack;
    }
}
