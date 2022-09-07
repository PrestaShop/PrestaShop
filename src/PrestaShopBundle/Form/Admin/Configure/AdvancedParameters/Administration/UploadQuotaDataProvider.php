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

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Administration;

use PHP_CodeSniffer\Util\Common;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\ErrorMessage\AdministrationConfigurationError;
use PrestaShop\PrestaShop\Core\Form\ErrorMessage\CommonConfigurationError;
use PrestaShop\PrestaShop\Core\Form\ErrorMessage\ConfigurationErrorCollection;
use PrestaShop\PrestaShop\Core\Form\ErrorMessage\ConfigurationErrorInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShopBundle\Form\Exception\FormDataProviderException;

/**
 * This class is responsible of managing the data manipulated using Upload Quota form
 * in "Configure > Advanced Parameters > Administration" page.
 */
final class UploadQuotaDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $dataConfiguration;

    public function __construct(
        DataConfigurationInterface $dataConfiguration
    ) {
        $this->dataConfiguration = $dataConfiguration;
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
        $this->validate($data);

        return $this->dataConfiguration->updateConfiguration($data);
    }

    /**
     * Perform validations on form data.
     *
     * @param array $data
     */
    private function validate(array $data): void
    {
        $errors = new ConfigurationErrorCollection();

        if (isset($data[UploadQuotaType::FIELD_MAX_SIZE_ATTACHED_FILES])) {
            $maxSizeAttachedFile = $data[UploadQuotaType::FIELD_MAX_SIZE_ATTACHED_FILES];
            if (!is_numeric($maxSizeAttachedFile) || $maxSizeAttachedFile < 0) {
                $errors->add(new CommonConfigurationError(CommonConfigurationError::ERROR_NOT_NUMERIC_OR_LOWER_THAN_ZERO, UploadQuotaType::FIELD_MAX_SIZE_ATTACHED_FILES));
            }
        }

        if (isset($data[UploadQuotaType::FIELD_MAX_SIZE_DOWNLOADABLE_FILE])) {
            $maxSizeDownloadableProduct = $data[UploadQuotaType::FIELD_MAX_SIZE_DOWNLOADABLE_FILE];
            if (!is_numeric($maxSizeDownloadableProduct) || $maxSizeDownloadableProduct < 0) {
                $errors->add(new CommonConfigurationError(CommonConfigurationError::ERROR_NOT_NUMERIC_OR_LOWER_THAN_ZERO, UploadQuotaType::FIELD_MAX_SIZE_DOWNLOADABLE_FILE));
            }
        }

        if (isset($data[UploadQuotaType::FIELD_MAX_SIZE_PRODUCT_IMAGE])) {
            $maxSizeProductImage = $data[UploadQuotaType::FIELD_MAX_SIZE_PRODUCT_IMAGE];

            if (!is_numeric($maxSizeProductImage) || $maxSizeProductImage < 0) {
                $errors->add(new CommonConfigurationError(CommonConfigurationError::ERROR_NOT_NUMERIC_OR_LOWER_THAN_ZERO, UploadQuotaType::FIELD_MAX_SIZE_PRODUCT_IMAGE));
            }
        }

        if (!$errors->isEmpty()) {
            throw new FormDataProviderException('Upload quota data is invalid', 0, null, $errors);
        }
    }
}
