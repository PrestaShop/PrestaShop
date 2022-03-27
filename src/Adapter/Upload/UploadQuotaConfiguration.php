<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Upload;

use Exception;
use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Administration\UploadQuotaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Manages the configuration data about upload quota options.
 */
class UploadQuotaConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = [
        UploadQuotaType::FIELD_MAX_SIZE_ATTACHED_FILES,
        UploadQuotaType::FIELD_MAX_SIZE_DOWNLOADABLE_FILE,
        UploadQuotaType::FIELD_MAX_SIZE_PRODUCT_IMAGE,
    ];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            UploadQuotaType::FIELD_MAX_SIZE_ATTACHED_FILES
                => (double) $this->configuration->get('PS_ATTACHMENT_MAXIMUM_SIZE', null, $shopConstraint),
            UploadQuotaType::FIELD_MAX_SIZE_DOWNLOADABLE_FILE 
                => (double) $this->configuration->get('PS_LIMIT_UPLOAD_FILE_VALUE', null, $shopConstraint),
            UploadQuotaType::FIELD_MAX_SIZE_PRODUCT_IMAGE 
                => (double) $this->configuration->get('PS_LIMIT_UPLOAD_IMAGE_VALUE', null, $shopConstraint),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $shopConstraint = $this->getShopConstraint();

            $updateConfigurationValue = function(string $configurationKey, string $fieldName) use ($configuration, $shopConstraint): void {
                $this->updateConfigurationValue($configurationKey, $fieldName, $configuration, $shopConstraint);
            };

            $updateConfigurationValue('PS_ATTACHMENT_MAXIMUM_SIZE', UploadQuotaType::FIELD_MAX_SIZE_ATTACHED_FILES);
            $updateConfigurationValue('PS_LIMIT_UPLOAD_FILE_VALUE', UploadQuotaType::FIELD_MAX_SIZE_DOWNLOADABLE_FILE);
            $updateConfigurationValue('PS_LIMIT_UPLOAD_IMAGE_VALUE', UploadQuotaType::FIELD_MAX_SIZE_PRODUCT_IMAGE);
        }

        return [];
    }

    /**
     * @return OptionsResolver
     */
    protected function buildResolver(): OptionsResolver
    {
        $resolver = (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes(UploadQuotaType::FIELD_MAX_SIZE_ATTACHED_FILES, ['double'])
            ->setAllowedTypes(UploadQuotaType::FIELD_MAX_SIZE_DOWNLOADABLE_FILE, ['double'])
            ->setAllowedTypes(UploadQuotaType::FIELD_MAX_SIZE_PRODUCT_IMAGE, ['double'])
        ;

        return $resolver;
    }
}
