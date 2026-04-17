<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Improve\International\Geolocation;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShop\PrestaShop\Core\Validation\ValidatorInterface;

/**
 * Class GeolocationWhitelistFormDataProvider is responsible for handling geolocation form data.
 */
final class GeolocationWhitelistFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $dataConfiguration;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param DataConfigurationInterface $dataConfiguration
     * @param ValidatorInterface $validator
     */
    public function __construct(
        DataConfigurationInterface $dataConfiguration,
        ValidatorInterface $validator
    ) {
        $this->dataConfiguration = $dataConfiguration;
        $this->validator = $validator;
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
        if (!$this->validator->isCleanHtml($data['geolocation_whitelist'])) {
            $errors[] = [
                'key' => 'Invalid whitelist',
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
