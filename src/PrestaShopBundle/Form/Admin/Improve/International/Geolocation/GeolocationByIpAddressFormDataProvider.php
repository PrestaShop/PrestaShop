<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Improve\International\Geolocation;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShop\PrestaShop\Core\Geolocation\GeoLite\GeoLiteCityCheckerInterface;

/**
 * Class GeolocationByIpAddressFormDataProvider is responsible for handling geolocation form data.
 */
final class GeolocationByIpAddressFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $dataConfiguration;

    /**
     * @var GeoLiteCityCheckerInterface
     */
    private $geoLiteCityChecker;

    /**
     * @param DataConfigurationInterface $dataConfiguration
     * @param GeoLiteCityCheckerInterface $geoLiteCityChecker
     */
    public function __construct(
        DataConfigurationInterface $dataConfiguration,
        GeoLiteCityCheckerInterface $geoLiteCityChecker
    ) {
        $this->dataConfiguration = $dataConfiguration;
        $this->geoLiteCityChecker = $geoLiteCityChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->dataConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        $errors = [];

        if ($data['geolocation_enabled'] && !$this->geoLiteCityChecker->isAvailable()) {
            $errors[] = [
                'key' => 'The geolocation database is unavailable.',
                'parameters' => [],
                'domain' => 'Admin.International.Notification',
            ];
        }

        if (!empty($errors)) {
            return $errors;
        }

        return $this->dataConfiguration->updateConfiguration($data);
    }
}
