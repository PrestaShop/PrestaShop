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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\VirtualProduct\Validate;

use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Exception\VirtualProductFileConstraintException;
use ProductDownload as VirtualProductFile;

/**
 * Validates VirtualProductFile properties using legacy object model validation
 * Legacy object ProductDownload is referred as VirtualProductFile in Core
 */
class VirtualProductFileValidator extends AbstractObjectModelValidator
{
    /**
     * @param VirtualProductFile $virtualProductFile
     */
    public function validate(VirtualProductFile $virtualProductFile): void
    {
        $this->validateVirtualProductFileProperty($virtualProductFile, 'id_product');
        $this->validateVirtualProductFileProperty($virtualProductFile, 'display_filename', VirtualProductFileConstraintException::INVALID_DISPLAY_NAME);
        $this->validateVirtualProductFileProperty($virtualProductFile, 'filename', VirtualProductFileConstraintException::INVALID_FILENAME);
        $this->validateVirtualProductFileProperty($virtualProductFile, 'date_add', VirtualProductFileConstraintException::INVALID_CREATION_DATE);
        $this->validateVirtualProductFileProperty($virtualProductFile, 'date_expiration', VirtualProductFileConstraintException::INVALID_EXPIRATION_DATE);
        $this->validateVirtualProductFileProperty($virtualProductFile, 'nb_days_accessible', VirtualProductFileConstraintException::INVALID_ACCESS_DAYS);
        $this->validateVirtualProductFileProperty($virtualProductFile, 'nb_downloadable', VirtualProductFileConstraintException::INVALID_DOWNLOAD_TIMES_LIMIT);
        $this->validateVirtualProductFileProperty($virtualProductFile, 'active', VirtualProductFileConstraintException::INVALID_ACTIVE);
        $this->validateVirtualProductFileProperty($virtualProductFile, 'is_shareable', VirtualProductFileConstraintException::INVALID_SHAREABLE);
    }

    /**
     * @param VirtualProductFile $virtualProductFile
     * @param string $propertyName
     * @param int $errorCode
     *
     * @throws VirtualProductFileConstraintException
     */
    private function validateVirtualProductFileProperty(VirtualProductFile $virtualProductFile, string $propertyName, int $errorCode = 0): void
    {
        $this->validateObjectModelProperty(
            $virtualProductFile,
            $propertyName,
            VirtualProductFileConstraintException::class,
            $errorCode
        );
    }
}
