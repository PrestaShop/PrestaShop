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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\AddManufacturerCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\EditManufacturerCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;
use PrestaShop\PrestaShop\Core\Image\Uploader\ImageUploaderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Handles submitted manufacturer form data
 */
final class ManufacturerFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $bus;
    /**
     * @var ImageUploaderInterface
     */
    private $imageUploader;

    /**
     * @param CommandBusInterface $bus
     * @param ImageUploaderInterface $imageUploader
     */
    public function __construct(
        CommandBusInterface $bus,
        ImageUploaderInterface $imageUploader
    ) {
        $this->bus = $bus;
        $this->imageUploader = $imageUploader;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        if (!isset($data['shop_association']) || !$data['shop_association']) {
            $data['shop_association'] = [];
        }

        /** @var ManufacturerId $manufacturerId */
        $manufacturerId = $this->bus->handle(new AddManufacturerCommand(
            $data['name'],
            $data['is_enabled'],
            $data['short_description'],
            $data['description'],
            $data['meta_title'],
            $data['meta_description'],
            $data['meta_keyword'],
            $data['shop_association']
        ));

        /** @var UploadedFile $uploadedLogo */
        $uploadedLogo = $data['logo'];

        if ($uploadedLogo instanceof UploadedFile) {
            $this->imageUploader->upload($manufacturerId->getValue(), $uploadedLogo);
        }

        return $manufacturerId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($manufacturerId, array $data)
    {
        /** @var UploadedFile $uploadedLogo */
        $uploadedLogo = $data['logo'];
        $logo = null;

        if ($uploadedLogo instanceof UploadedFile) {
            $this->imageUploader->upload($manufacturerId, $uploadedLogo);
        }

        $command = (new EditManufacturerCommand($manufacturerId))
            ->setName((string) $data['name'])
            ->setLocalizedShortDescriptions($data['short_description'])
            ->setLocalizedDescriptions($data['description'])
            ->setLocalizedMetaDescriptions($data['meta_description'])
            ->setLocalizedMetaTitles($data['meta_title'])
            ->setLocalizedMetaKeywords($data['meta_keyword'])
            ->setEnabled((bool) $data['is_enabled'])
        ;

        if (isset($data['shop_association'])) {
            $shopAssociation = $data['shop_association'] ?: [];
            $shopAssociation = array_map(function ($shopId) { return (int) $shopId; }, $shopAssociation);

            $command->setAssociatedShops($shopAssociation);
        }

        $this->bus->handle($command);
    }
}
