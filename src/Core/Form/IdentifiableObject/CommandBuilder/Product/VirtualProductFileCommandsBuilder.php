<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product;

use DateTime;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\AddVirtualProductFileCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\DeleteVirtualProductFileCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\UpdateVirtualProductFileCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class VirtualProductFileCommandsBuilder implements ProductCommandsBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildCommands(ProductId $productId, array $formData, ShopConstraint $singleShopConstraint): array
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
            isset($virtualProductFileData['expiration_date']) && $virtualProductFileData['expiration_date'] !== ''
             ? new DateTime($virtualProductFileData['expiration_date'])
             : null
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
            $command->setExpirationDate(DateTimeUtil::buildNullableDateTime($virtualProductFileData['expiration_date']));
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
