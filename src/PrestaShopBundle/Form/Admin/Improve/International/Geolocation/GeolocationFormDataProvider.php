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

namespace PrestaShopBundle\Form\Admin\Improve\International\Geolocation;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Class GeolocationFormDataProvider
 */
final class GeolocationFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $geolocationByIpAddressConfiguration;

    /**
     * @var DataConfigurationInterface
     */
    private $geolocationIpAddressWhitelistConfiguration;

    /**
     * @var DataConfigurationInterface
     */
    private $geolocationOptionsConfiguration;

    /**
     * @param DataConfigurationInterface $geolocationByIpAddressConfiguration
     * @param DataConfigurationInterface $geolocationIpAddressWhitelistConfiguration
     * @param DataConfigurationInterface $geolocationOptionsConfiguration
     */
    public function __construct(
        DataConfigurationInterface $geolocationByIpAddressConfiguration,
        DataConfigurationInterface $geolocationIpAddressWhitelistConfiguration,
        DataConfigurationInterface $geolocationOptionsConfiguration
    ) {
        $this->geolocationByIpAddressConfiguration = $geolocationByIpAddressConfiguration;
        $this->geolocationIpAddressWhitelistConfiguration = $geolocationIpAddressWhitelistConfiguration;
        $this->geolocationOptionsConfiguration = $geolocationOptionsConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'geolocation_by_id_address' => $this->geolocationByIpAddressConfiguration->getConfiguration(),
            'geolocation_ip_address_whitelist' => $this->geolocationIpAddressWhitelistConfiguration->getConfiguration(),
            'geolocation_options' => $this->geolocationOptionsConfiguration->getConfiguration(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {


        return array_merge(
            $this->geolocationByIpAddressConfiguration->updateConfiguration($data['geolocation_by_id_address']),
            $this->geolocationIpAddressWhitelistConfiguration->updateConfiguration($data['geolocation_ip_address_whitelist']),
            $this->geolocationOptionsConfiguration->updateConfiguration($data['geolocation_options'])
        );
    }
}
