<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
