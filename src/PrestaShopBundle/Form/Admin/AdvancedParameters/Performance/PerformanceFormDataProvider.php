<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShopBundle\Form\Admin\AdvancedParameters\Performance;

use PrestaShop\PrestaShop\Adapter\OptionalFeatures\OptionalFeaturesConfiguration;
use PrestaShop\PrestaShop\Adapter\Cache\CombineCompressCacheConfiguration;
use PrestaShop\PrestaShop\Adapter\Smarty\SmartyCacheConfiguration;
use PrestaShop\PrestaShop\Adapter\Media\MediaServerConfiguration;
use PrestaShop\PrestaShop\Adapter\Debug\DebugModeConfiguration;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShop\PrestaShop\Adapter\Cache\CachingConfiguration;

/**
 * This class is responsible of managing the data manipulated using forms
 * in "Configure > Advanced Parameters > Performance" page.
 */
final class PerformanceFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var SmartyCacheConfiguration
     */
    private $smartyCacheConfiguration;

    /**
     * @var DebugModeConfiguration
     */
    private $debugModeConfiguration;

    /**
     * @var CombineCompressCacheConfiguration
     */
    private $combineCompressCacheConfiguration;

    /**
     * @var OptionalFeaturesConfiguration
     */
    private $optionalFeaturesConfiguration;

    /**
     * @var MediaServerConfiguration
     */
    private $mediaServerConfiguration;

    /**
     * @var CachingConfiguration
     */
    private $cachingConfiguration;

    public function __construct(
        SmartyCacheConfiguration $smartyCacheConfiguration,
        DebugModeConfiguration $debugModeConfiguration,
        OptionalFeaturesConfiguration $optionalFeaturesConfiguration,
        CombineCompressCacheConfiguration $combineCompressCacheConfiguration,
        MediaServerConfiguration $mediaServerConfiguration,
        CachingConfiguration $cachingConfiguration
    ) {
        $this->smartyCacheConfiguration = $smartyCacheConfiguration;
        $this->debugModeConfiguration = $debugModeConfiguration;
        $this->optionalFeaturesConfiguration = $optionalFeaturesConfiguration;
        $this->combineCompressCacheConfiguration = $combineCompressCacheConfiguration;
        $this->mediaServerConfiguration = $mediaServerConfiguration;
        $this->cachingConfiguration = $cachingConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'smarty' => $this->smartyCacheConfiguration->getConfiguration(),
            'debug_mode' => $this->debugModeConfiguration->getConfiguration(),
            'optional_features' => $this->optionalFeaturesConfiguration->getConfiguration(),
            'ccc' => $this->combineCompressCacheConfiguration->getConfiguration(),
            'media_servers' => $this->mediaServerConfiguration->getConfiguration(),
            'caching' => $this->cachingConfiguration->getConfiguration(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        return $this->smartyCacheConfiguration->updateConfiguration($data['smarty']) +
            $this->debugModeConfiguration->updateConfiguration($data['debug_mode']) +
            $this->optionalFeaturesConfiguration->updateConfiguration($data['optional_features']) +
            $this->combineCompressCacheConfiguration->updateConfiguration($data['ccc']) +
            $this->mediaServerConfiguration->updateConfiguration($data['media_servers']) +
            $this->cachingConfiguration->updateConfiguration($data['caching']);
    }
}
