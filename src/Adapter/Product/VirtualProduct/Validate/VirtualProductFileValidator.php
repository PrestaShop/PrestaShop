<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
