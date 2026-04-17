<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Improve\International\Geolocation;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Class GeolocationOptionsFormDataProvider is responsible for handling geolocation form data.
 */
final class GeolocationOptionsFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $dataConfiguration;

    /**
     * @param DataConfigurationInterface $dataConfiguration
     */
    public function __construct(DataConfigurationInterface $dataConfiguration)
    {
        $this->dataConfiguration = $dataConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $configuration = $this->dataConfiguration->getConfiguration();

        if (!empty($configuration['geolocation_countries'])) {
            $configuration['geolocation_countries'] = explode(';', $configuration['geolocation_countries']);
        } else {
            $configuration['geolocation_countries'] = [];
        }

        return $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        $errors = [];

        if (empty($data['geolocation_countries'])) {
            $errors[] = [
                'key' => 'Country selection is invalid.',
                'parameters' => [],
                'domain' => 'Admin.International.Notification',
            ];
        }

        if (!empty($errors)) {
            return $errors;
        }

        $data['geolocation_countries'] = implode(';', $data['geolocation_countries']);

        return $this->dataConfiguration->updateConfiguration($data);
    }
}
