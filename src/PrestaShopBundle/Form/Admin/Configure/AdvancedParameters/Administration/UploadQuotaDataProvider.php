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

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * This class is responsible of managing the data manipulated using forms
 * in "Configure > Advanced Parameters > Administration" page.
 */
final class UploadQuotaDataProvider implements FormDataProviderInterface
{
    public const ERROR_NOT_NUMERIC_OR_LOWER_THEN_0 = 1;

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
        if ($errors = $this->validate($data)) {
            return $errors;
        }

        return $this->dataConfiguration->updateConfiguration($data);
    }

    /**
     * Perform validations on form data.
     *
     * @param array $data
     *
     * @return array Array of errors if any
     */
    private function validate(array $data)
    {
        $errors = [];
        $maxSizeAttachedFile = $data['max_size_attached_files'];
        $maxSizeDownloadableProduct = $data['max_size_downloadable_product'];
        $maxSizeProductImage = $data['max_size_product_image'];

        if (!is_numeric($maxSizeAttachedFile) || $maxSizeAttachedFile < 0) {
            $errors[] = new FormError(self::ERROR_NOT_NUMERIC_OR_LOWER_THEN_0, UploadQuotaType::FIELD_MAX_SIZE_ATTACHED_FILES);
        }

        if (!is_numeric($maxSizeDownloadableProduct) || $maxSizeDownloadableProduct < 0) {
            $errors[] = new FormError(self::ERROR_NOT_NUMERIC_OR_LOWER_THEN_0, UploadQuotaType::FIELD_MAX_SIZE_DOWNLOADABLE_FILE);
        }

        if (!is_numeric($maxSizeProductImage) || $maxSizeProductImage < 0) {
            $errors[] = new FormError(self::ERROR_NOT_NUMERIC_OR_LOWER_THEN_0, UploadQuotaType::FIELD_MAX_SIZE_PRODUCT_IMAGE);
        }

        return $errors;
    }
}
