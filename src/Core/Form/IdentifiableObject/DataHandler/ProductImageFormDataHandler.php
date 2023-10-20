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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Exception\FileUploadException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\AddProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\UpdateProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProductImageFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $bus;

    /**
     * @param CommandBusInterface $bus
     */
    public function __construct(
        CommandBusInterface $bus
    ) {
        $this->bus = $bus;
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data)
    {
        $uploadedFile = $data['file'] ?? null;

        if (!($uploadedFile instanceof UploadedFile)) {
            throw new FileUploadException('No file was uploaded', UPLOAD_ERR_NO_FILE);
        }

        $command = new AddProductImageCommand(
            (int) ($data['product_id'] ?? 0),
            $uploadedFile->getPathname(),
            !empty($data['shop_id']) ? ShopConstraint::shop((int) $data['shop_id']) : ShopConstraint::allShops()
        );

        /** @var ImageId $imageId */
        $imageId = $this->bus->handle($command);

        return $imageId->getValue();
    }

    /**
     * {@inheritDoc}
     */
    public function update($id, array $data)
    {
        if (!empty($data['shop_id'])) {
            $shopConstraint = ShopConstraint::shop((int) $data['shop_id']);
        } else {
            $shopConstraint = ShopConstraint::allShops();
        }

        $command = new UpdateProductImageCommand((int) $id, $shopConstraint);

        if (isset($data['is_cover'])) {
            $command->setIsCover($data['is_cover']);
        }

        if (isset($data['legend'])) {
            $command->setLocalizedLegends($data['legend']);
        }

        if (isset($data['file'])) {
            $uploadedFile = $data['file'];
            $command->setFilePath($uploadedFile->getPathname());
        }

        if (isset($data['position'])) {
            $command->setPosition((int) $data['position']);
        }

        $this->bus->handle($command);
    }
}
