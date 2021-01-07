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

namespace PrestaShop\PrestaShop\Adapter\Product\Validate;

use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Exception\VirtualProductFileConstraintException;
use ProductDownload;

/**
 * Validates ProductDownload properties using legacy object model validation
 * ProductDownload is referred as VirtualProductFile in Core
 */
class ProductDownloadValidator extends AbstractObjectModelValidator
{
    /**
     * @param ProductDownload $productDownload
     */
    public function validate(ProductDownload $productDownload): void
    {
        $this->validateProductDownloadProperty($productDownload, 'id_product');
        $this->validateProductDownloadProperty($productDownload, 'display_filename', VirtualProductFileConstraintException::INVALID_DISPLAY_NAME);
        $this->validateProductDownloadProperty($productDownload, 'filename', VirtualProductFileConstraintException::INVALID_FILENAME);
        $this->validateProductDownloadProperty($productDownload, 'date_add', VirtualProductFileConstraintException::INVALID_CREATION_DATE);
        $this->validateProductDownloadProperty($productDownload, 'date_expiration', VirtualProductFileConstraintException::INVALID_EXPIRATION_DATE);
        $this->validateProductDownloadProperty($productDownload, 'nb_days_accessible', VirtualProductFileConstraintException::INVALID_ACCESS_DAYS);
        $this->validateProductDownloadProperty($productDownload, 'nb_downloadable', VirtualProductFileConstraintException::INVALID_DOWNLOAD_TIMES_LIMIT);
        $this->validateProductDownloadProperty($productDownload, 'active', VirtualProductFileConstraintException::INVALID_ACTIVE);
        $this->validateProductDownloadProperty($productDownload, 'is_shareable', VirtualProductFileConstraintException::INVALID_SHAREABLE);
    }

    /**
     * @param ProductDownload $productDownload
     * @param string $propertyName
     * @param int $errorCode
     *
     * @throws VirtualProductFileConstraintException
     */
    private function validateProductDownloadProperty(ProductDownload $productDownload, string $propertyName, int $errorCode = 0): void
    {
        $this->validateObjectModelProperty(
            $productDownload,
            $propertyName,
            VirtualProductFileConstraintException::class,
            $errorCode
        );
    }
}
