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

namespace PrestaShop\PrestaShop\Adapter\Product\VirtualProduct\Repository;

use PrestaShop\PrestaShop\Adapter\AbstractObjectModelRepository;
use PrestaShop\PrestaShop\Adapter\Product\VirtualProduct\Validate\VirtualProductFileValidator;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Exception\CannotAddVirtualProductFileException;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Exception\CannotDeleteVirtualProductFileException;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Exception\CannotUpdateVirtualProductFileException;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Exception\VirtualProductFileNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\ValueObject\VirtualProductFileId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
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
