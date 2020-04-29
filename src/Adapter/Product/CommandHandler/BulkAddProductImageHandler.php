<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Image\CommandHandler\AddProductImageHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\CommandResult\AddedImage;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\BulkAddProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\AddProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\CommandHandler\BulkAddProductImageHandlerInterface;

final class BulkAddProductImageHandler implements BulkAddProductImageHandlerInterface
{
    /**
     * @var AddProductImageHandler
     */
    private $addProductImageHandler;

    /**
     * @param AddProductImageHandler $addProductImageHandler
     */
    public function __construct(
        AddProductImageHandler $addProductImageHandler
    ) {
        $this->addProductImageHandler = $addProductImageHandler;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(BulkAddProductImageCommand $command): array
    {
        $addedImages = [];

        for ($i = 1; $i <= $command->getImageCount(); $i++) {
            $productIdValue = $command->getProductId()->getValue();
            $imageId = $this->addProductImageHandler->handle(new AddProductImageCommand($productIdValue));

            $addedImages[] = $imageId->getValue();
        }

        return $addedImages;
    }
}
