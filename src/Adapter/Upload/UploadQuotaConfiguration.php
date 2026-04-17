<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Upload;

use Exception;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;

/**
 * Manages the configuration data about upload quota options.
 */
class UploadQuotaConfiguration implements DataConfigurationInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'max_size_attached_files' => $this->configuration->get('PS_ATTACHMENT_MAXIMUM_SIZE'),
            'max_size_downloadable_product' => $this->configuration->get('PS_LIMIT_UPLOAD_FILE_VALUE'),
            'max_size_product_image' => $this->configuration->get('PS_LIMIT_UPLOAD_IMAGE_VALUE'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = [];

        if ($this->validateConfiguration($configuration)) {
            $errors = $this->updateFileUploadConfiguration($configuration);
        }

        return $errors;
    }

    /**
     * Update the file upload limit if possible.
     *
     * @return array the errors list during the update operation
     *
     * @throws Exception
     */
    private function updateFileUploadConfiguration(array $configuration)
    {
        $uploadMaxSize = (int) str_replace('M', '', ini_get('upload_max_filesize'));
        $sizes = [
            'max_size_attached_files' => $uploadMaxSize,
            'max_size_downloadable_product' => (int) str_replace('M', '', ini_get('post_max_size')),
            'max_size_product_image' => $uploadMaxSize,
        ];

        $errors = [];
        foreach ($configuration as $configurationKey => $configurationValue) {
            if (array_key_exists($configurationKey, $this->getConfiguration())) {
                if ((int) $configurationValue > $sizes[$configurationKey]) {
                    $errors[] = [
                        'key' => 'The limit chosen is larger than the server\'s maximum upload limit. Please increase the limits of your server.',
                        'domain' => 'Admin.Advparameters.Notification',
                        'parameters' => [],
                    ];
                }

                $this->configuration->set(
                    $this->getConfigurationKey($configurationKey),
                    max((int) $configurationValue, 1)
                );
            }
        }

        return $errors;
    }

    /**
     * Map array key to the related configuration property.
     *
     * @param string $key
     *
     * @return string the related configuration key
     */
    private function getConfigurationKey($key)
    {
        $properties = [
            'max_size_attached_files' => 'PS_ATTACHMENT_MAXIMUM_SIZE',
            'max_size_downloadable_product' => 'PS_LIMIT_UPLOAD_FILE_VALUE',
            'max_size_product_image' => 'PS_LIMIT_UPLOAD_IMAGE_VALUE',
        ];

        return $properties[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        return isset(
            $configuration['max_size_attached_files'],
            $configuration['max_size_downloadable_product'],
            $configuration['max_size_product_image']
        );
    }
}
