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

namespace PrestaShop\PrestaShop\Adapter\Product\Repository;

use PrestaShop\PrestaShop\Adapter\AbstractObjectModelRepository;
use PrestaShop\PrestaShop\Adapter\Product\Validate\ProductDownloadValidator;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Exception\CannotAddVirtualProductFileException;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Exception\VirtualProductFileNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\ValueObject\VirtualProductFileId;
use ProductDownload;

/**
 * Provides access to ProductDownload data source
 *
 * @todo: rename to VirtualProductFileRepository or leave legacy name?
 */
class ProductDownloadRepository extends AbstractObjectModelRepository
{
    /**
     * @var ProductDownloadValidator
     */
    private $productDownloadValidator;

    /**
     * @param ProductDownloadValidator $productDownloadValidator
     */
    public function __construct(
        ProductDownloadValidator $productDownloadValidator
    ) {
        $this->productDownloadValidator = $productDownloadValidator;
    }

    /**
     * @param VirtualProductFileId $virtualProductFileId
     *
     * @return ProductDownload
     *
     * @throws VirtualProductFileNotFoundException
     */
    public function get(VirtualProductFileId $virtualProductFileId): ProductDownload
    {
        /** @var ProductDownload $productDownload */
        $productDownload = $this->getObjectModel(
            $virtualProductFileId->getValue(),
            ProductDownload::class,
            VirtualProductFileNotFoundException::class
        );

        return $productDownload;
    }

    /**
     * @param ProductDownload $productDownload
     *
     * @return VirtualProductFileId
     *
     * @throws CannotAddVirtualProductFileException
     */
    public function add(ProductDownload $productDownload): VirtualProductFileId
    {
        $this->productDownloadValidator->validate($productDownload);
        $id = $this->addObjectModel($productDownload, CannotAddVirtualProductFileException::class);

        return new VirtualProductFileId($id);
    }
}
