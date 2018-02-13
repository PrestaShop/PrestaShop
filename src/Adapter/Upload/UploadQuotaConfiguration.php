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
namespace PrestaShop\PrestaShop\Adapter\Upload;

use PrestaShop\PrestaShop\Adapter\Configuration;
use Symfony\Component\OptionsResolver\OptionsResolver;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;

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
     * @{inheritdoc}
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
     * @{inheritdoc}
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
     * @return array the errors list during the update operation.
     */
    private function updateFileUploadConfiguration(array $configuration)
    {
        $uploadMaxSize = (int) str_replace('M', '', ini_get('upload_max_filesize'));
        $postMaxSize = (int) str_replace('M', '', ini_get('post_max_size'));
        $maxSize = $uploadMaxSize < $postMaxSize ? $uploadMaxSize : $postMaxSize;

        $errors = array();

        foreach ($configuration as $configurationKey => $configurationValue) {
            if ($configurationValue > $maxSize) {
                $errors[] = array(
                    'key' => 'The limit chosen is larger than the server\'s maximum upload limit. Please increase the limits of your server.',
                    'domain' => 'Admin.Advparameters.Notification',
                    'parameters' => array(),
                );
            }

            $this->configuration->set($this->getConfigurationKey($configurationKey), $configurationValue);
        }

        return $errors;
    }

    /**
     * Map array key to the related configuration property.
     *
     * @param string the array key
     * @return string the related configuration key.
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
     * @{inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        $resolver = new OptionsResolver();
        $resolver
            ->setRequired(
                array(
                    'max_size_attached_files',
                    'max_size_downloadable_product',
                    'max_size_product_image',
                )
            );
        $resolver->resolve($configuration);

        return true;
    }
}
