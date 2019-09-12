<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
        return array(
            'max_size_attached_files' => $this->configuration->get('PS_ATTACHMENT_MAXIMUM_SIZE'),
            'max_size_downloadable_product' => $this->configuration->get('PS_LIMIT_UPLOAD_FILE_VALUE'),
            'max_size_product_image' => $this->configuration->get('PS_LIMIT_UPLOAD_IMAGE_VALUE'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = array();

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

        $errors = array();
        foreach ($configuration as $configurationKey => $configurationValue) {
            if (array_key_exists($configurationKey, $this->getConfiguration())) {
                if ((int) $configurationValue > $sizes[$configurationKey]) {
                    $errors[] = array(
                        'key' => 'The limit chosen is larger than the server\'s maximum upload limit. Please increase the limits of your server.',
                        'domain' => 'Admin.Advparameters.Notification',
                        'parameters' => array(),
                    );
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
     * @param string the array key
     *
     * @return string the related configuration key
     */
    private function getConfigurationKey($key)
    {
        $properties = array(
            'max_size_attached_files' => 'PS_ATTACHMENT_MAXIMUM_SIZE',
            'max_size_downloadable_product' => 'PS_LIMIT_UPLOAD_FILE_VALUE',
            'max_size_product_image' => 'PS_LIMIT_UPLOAD_IMAGE_VALUE',
        );

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
