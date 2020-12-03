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
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\AddSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\EditSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;
use PrestaShop\PrestaShop\Core\Image\Uploader\ImageUploaderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Handles submitted supplier form data
 */
final class SupplierFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;
    /**
     * @var ImageUploaderInterface
     */
    private $imageUploader;

    /**
     * @param CommandBusInterface $commandBus
     * @param ImageUploaderInterface $imageUploader
     */
    public function __construct(
        CommandBusInterface $commandBus,
        ImageUploaderInterface $imageUploader
    ) {
        $this->commandBus = $commandBus;
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

        /** @var SupplierId $supplierId */
        $supplierId = $this->commandBus->handle(new AddSupplierCommand(
            $data['name'],
            $data['address'],
            $data['city'],
            (int) $data['id_country'],
            (bool) $data['is_enabled'],
            $data['description'],
            $data['meta_title'],
            $data['meta_description'],
            $data['meta_keyword'],
            $data['shop_association'],
            $data['address2'],
            $data['post_code'],
            isset($data['id_state']) ? (int) $data['id_state'] : null,
            $data['phone'],
            $data['mobile_phone'],
            $data['dni']
        ));

        /** @var UploadedFile $uploadedFlagImage */
        $uploadedLogo = $data['logo'];

        if ($uploadedLogo instanceof UploadedFile) {
            $this->imageUploader->upload($supplierId->getValue(), $uploadedLogo);
        }

        return $supplierId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($supplierId, array $data)
    {
        /** @var UploadedFile $uploadedFlagImage */
        $uploadedLogo = $data['logo'];
        $logo = null;

        if ($uploadedLogo instanceof UploadedFile) {
            $this->imageUploader->upload($supplierId, $uploadedLogo);
        }

        $command = new EditSupplierCommand($supplierId);
        $this->fillCommandWithData($command, $data);

        $this->commandBus->handle($command);
    }

    /**
     * Fills command with provided data
     *
     * @param EditSupplierCommand $command
     * @param array $data
     */
    private function fillCommandWithData(EditSupplierCommand $command, array $data)
    {
        if (null !== $data['name']) {
            $command->setName($data['name']);
        }
        if (null !== $data['description']) {
            $command->setLocalizedDescriptions($data['description']);
        }
        if (null !== $data['phone']) {
            $command->setPhone($data['phone']);
        }
        if (null !== $data['mobile_phone']) {
            $command->setMobilePhone($data['mobile_phone']);
        }
        if (null !== $data['address']) {
            $command->setAddress($data['address']);
        }
        if (null !== $data['address2']) {
            $command->setAddress2($data['address2']);
        }
        if (null !== $data['post_code']) {
            $command->setPostCode($data['post_code']);
        }
        if (null !== $data['city']) {
            $command->setCity($data['city']);
        }
        if (null !== $data['id_country']) {
            $command->setCountryId((int) $data['id_country']);
        }
        if (null !== $data['meta_title']) {
            $command->setLocalizedMetaTitles($data['meta_title']);
        }
        if (null !== $data['meta_description']) {
            $command->setLocalizedMetaDescriptions($data['meta_description']);
        }
        if (null !== $data['is_enabled']) {
            $command->setEnabled((bool) $data['is_enabled']);
        }
        if (null !== $data['dni']) {
            $command->setDni($data['dni']);
        }

        if (isset($data['id_state'])) {
            $command->setStateId((int) $data['id_state']);
        }

        if (isset($data['shop_association'])) {
            $shopAssociation = $data['shop_association'] ?: [];
            $shopAssociation = array_map(function ($shopId) { return (int) $shopId; }, $shopAssociation);

            $command->setAssociatedShops($shopAssociation);
        }
    }
}
