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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product;

use DateTime;
use DateTimeImmutable;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\AddVirtualProductFileCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\DeleteVirtualProductFileCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\UpdateVirtualProductFileCommand;
use PrestaShop\PrestaShop\Core\Util\DateTime\NullDateTime;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class VirtualProductFileCommandsBuilder implements ProductCommandsBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildCommands(ProductId $productId, array $formData): array
    {
        if (!isset($formData['stock']['virtual_product_file'])) {
            return [];
        }

        $virtualProductFileData = $formData['stock']['virtual_product_file'];

        if ($addCommand = $this->buildAddCommand($productId, $virtualProductFileData)) {
            return [$addCommand];
        }

        if ($updateCommand = $this->buildUpdateCommand($virtualProductFileData)) {
            return [$updateCommand];
        }

        if ($deleteCommand = $this->buildDeleteCommand($virtualProductFileData)) {
            return [$deleteCommand];
        }

        return [];
    }

    /**
     * @param ProductId $productId
     * @param array<string, mixed> $virtualProductFileData
     *
     * @return AddVirtualProductFileCommand|null
     */
    public function buildAddCommand(ProductId $productId, array $virtualProductFileData): ?AddVirtualProductFileCommand
    {
        if (empty($virtualProductFileData['has_file']) || !empty($virtualProductFileData['virtual_product_file_id'])) {
            return null;
        }

        if (empty($virtualProductFileData['file'])) {
            return null;
        }

        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $virtualProductFileData['file'];

        return new AddVirtualProductFileCommand(
            $productId->getValue(),
            $uploadedFile->getPathname(),
            $virtualProductFileData['name'],
            isset($virtualProductFileData['access_days_limit']) ? (int) $virtualProductFileData['access_days_limit'] : null,
            isset($virtualProductFileData['download_times_limit']) ? (int) $virtualProductFileData['download_times_limit'] : null,
            isset($virtualProductFileData['expiration_date']) ? new DateTime($virtualProductFileData['expiration_date']) : null
        );
    }

    /**
     * @param array<string, mixed> $virtualProductFileData
     *
     * @return UpdateVirtualProductFileCommand|null
     */
    private function buildUpdateCommand(array $virtualProductFileData): ?UpdateVirtualProductFileCommand
    {
        $update = false;

        if (empty($virtualProductFileData['has_file']) || empty($virtualProductFileData['virtual_product_file_id'])) {
            return null;
        }

        $command = new UpdateVirtualProductFileCommand((int) $virtualProductFileData['virtual_product_file_id']);

        if (isset($virtualProductFileData['file'])) {
            $update = true;
            /** @var UploadedFile $newFile */
            $newFile = $virtualProductFileData['file'];
            $command->setFilePath($newFile->getPathname());
        }
        if (isset($virtualProductFileData['name'])) {
            $update = true;
            $command->setDisplayName($virtualProductFileData['name']);
        }
        if (isset($virtualProductFileData['access_days_limit'])) {
            $update = true;
            $command->setAccessDays((int) $virtualProductFileData['access_days_limit']);
        }
        if (isset($virtualProductFileData['download_times_limit'])) {
            $update = true;
            $command->setDownloadTimesLimit((int) $virtualProductFileData['download_times_limit']);
        }
        if (isset($virtualProductFileData['expiration_date'])) {
            $update = true;
            $command->setExpirationDate(
                empty($virtualProductFileData['expiration_date']) ?
                    new NullDateTime() :
                    new DateTimeImmutable($virtualProductFileData['expiration_date'])
            );
        }

        return $update ? $command : null;
    }

    /**
     * @param array<string, mixed> $virtualProductFileData
     *
     * @return DeleteVirtualProductFileCommand|null
     */
    private function buildDeleteCommand(array $virtualProductFileData): ?DeleteVirtualProductFileCommand
    {
        if (!empty($virtualProductFileData['has_file']) || empty($virtualProductFileData['virtual_product_file_id'])) {
            return null;
        }

        return new DeleteVirtualProductFileCommand((int) $virtualProductFileData['virtual_product_file_id']);
    }
}
