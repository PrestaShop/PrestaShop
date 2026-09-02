<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Upload;

use Exception;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Manages the configuration data about upload quota options.
 */
class UploadQuotaConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = [
        'max_size_attached_files',
        'max_size_downloadable_product',
        'max_size_product_image',
    ];

    public function __construct(Configuration $configuration, Context $shopContext, FeatureInterface $multistoreFeature)
    {
        parent::__construct($configuration, $shopContext, $multistoreFeature);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'max_size_attached_files' => (int) $this->configuration->get('PS_ATTACHMENT_MAXIMUM_SIZE', 0, $shopConstraint),
            'max_size_downloadable_product' => (int) $this->configuration->get('PS_LIMIT_UPLOAD_FILE_VALUE', 0, $shopConstraint),
            'max_size_product_image' => (int) $this->configuration->get('PS_LIMIT_UPLOAD_IMAGE_VALUE', 0, $shopConstraint),
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
        $shopConstraint = $this->getShopConstraint();
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

                $this->updateConfigurationValue(
                    $this->getConfigurationKey($configurationKey),
                    $configurationKey,
                    array_map(static fn ($value): int => max((int) $value, 1), $configuration),
                    $shopConstraint
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
    protected function buildResolver(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('max_size_attached_files', 'int')
            ->setAllowedTypes('max_size_downloadable_product', 'int')
            ->setAllowedTypes('max_size_product_image', 'int');
    }
}
