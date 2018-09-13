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

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\TrafficSeo;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Class MetaFormDataProvider is responsible for providing configurations data and responsible for persisting data
 * in configuration database.
 */
final class MetaFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $setUpUrlDataConfiguration;

    /**
     * @var DataConfigurationInterface
     */
    private $shopUrlsDataConfiguration;

    /**
     * @var DataConfigurationInterface
     */
    private $urlSchemaDataConfiguration;

    /**
     * MetaFormDataProvider constructor.
     *
     * @param DataConfigurationInterface $setUpUrlDataConfiguration
     * @param DataConfigurationInterface $shopUrlsDataConfiguration
     * @param DataConfigurationInterface $urlSchemaDataConfiguration
     */
    public function __construct(
        DataConfigurationInterface $setUpUrlDataConfiguration,
        DataConfigurationInterface $shopUrlsDataConfiguration,
        DataConfigurationInterface $urlSchemaDataConfiguration
    ) {
        $this->setUpUrlDataConfiguration = $setUpUrlDataConfiguration;
        $this->shopUrlsDataConfiguration = $shopUrlsDataConfiguration;
        $this->urlSchemaDataConfiguration = $urlSchemaDataConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'set_up_urls' => $this->setUpUrlDataConfiguration->getConfiguration(),
            'shop_urls' => $this->shopUrlsDataConfiguration->getConfiguration(),
            'url_schema' => $this->urlSchemaDataConfiguration->getConfiguration(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        return array_merge(
            $this->setUpUrlDataConfiguration->updateConfiguration($data['set_up_urls']),
            $this->shopUrlsDataConfiguration->updateConfiguration($data['shop_urls']),
            $this->urlSchemaDataConfiguration->updateConfiguration($data['url_schema'])
        );
    }
}
