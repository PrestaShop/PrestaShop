<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\VirtualProduct\Repository;

use PrestaShop\PrestaShop\Adapter\Product\VirtualProduct\Validate\VirtualProductFileValidator;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Exception\CannotAddVirtualProductFileException;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Exception\CannotDeleteVirtualProductFileException;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Exception\CannotUpdateVirtualProductFileException;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Exception\VirtualProductFileNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\ValueObject\VirtualProductFileId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;
use PrestaShopException;
use ProductDownload as VirtualProductFile;

/**
 * Provides access to VirtualProductFile data source
 * Legacy object ProductDownload is referred as VirtualProductFile in Core
 */
class VirtualProductFileRepository extends AbstractObjectModelRepository
{
    /**
     * @var VirtualProductFileValidator
     */
    private $virtualProductFileValidator;

    /**
     * @param VirtualProductFileValidator $virtualProductFileValidator
     */
    public function __construct(
        VirtualProductFileValidator $virtualProductFileValidator
    ) {
        $this->virtualProductFileValidator = $virtualProductFileValidator;
    }

    /**
     * @param VirtualProductFileId $virtualProductFileId
     *
     * @return VirtualProductFile
     *
     * @throws VirtualProductFileNotFoundException
     */
    public function get(VirtualProductFileId $virtualProductFileId): VirtualProductFile
    {
        /** @var VirtualProductFile $virtualProductFile */
        $virtualProductFile = $this->getObjectModel(
            $virtualProductFileId->getValue(),
            VirtualProductFile::class,
            VirtualProductFileNotFoundException::class
        );

        return $virtualProductFile;
    }

    /**
     * @param VirtualProductFileId $virtualProductFileId
     */
    public function delete(VirtualProductFileId $virtualProductFileId): void
    {
        $this->deleteObjectModel(
            $this->get($virtualProductFileId),
            CannotDeleteVirtualProductFileException::class
        );
    }

    /**
     * @param ProductId $productId
     *
     * @return VirtualProductFile
     *
     * @throws VirtualProductFileNotFoundException
     */
    public function findByProductId(ProductId $productId): VirtualProductFile
    {
        try {
            $id = (int) VirtualProductFile::getIdFromIdProduct($productId->getValue());
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred when trying to find VirtualProductFile by product id #%d', $productId->getValue()),
                0,
                $e
            );
        }

        if (!$id) {
            throw new VirtualProductFileNotFoundException(sprintf(
                'Cannot find VirtualProduct for product %d',
                $productId->getValue()
            ));
        }

        return $this->get(new VirtualProductFileId($id));
    }

    /**
     * @param VirtualProductFile $virtualProductFile
     *
     * @return VirtualProductFileId
     *
     * @throws CannotAddVirtualProductFileException
     */
    public function add(VirtualProductFile $virtualProductFile): VirtualProductFileId
    {
        $this->virtualProductFileValidator->validate($virtualProductFile);
        $id = $this->addObjectModel($virtualProductFile, CannotAddVirtualProductFileException::class);

        return new VirtualProductFileId($id);
    }

    /**
     * @param VirtualProductFile $virtualProductFile
     */
    public function update(VirtualProductFile $virtualProductFile): void
    {
        $this->virtualProductFileValidator->validate($virtualProductFile);
        $this->updateObjectModel($virtualProductFile, CannotUpdateVirtualProductFileException::class);
    }
}
