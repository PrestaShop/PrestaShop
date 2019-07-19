<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
    private $bus;
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
        $this->bus = $commandBus;
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
        $supplierId = $this->bus->handle(new AddSupplierCommand(
            $data['name'],
            $data['address'],
            $data['city'],
            $data['id_country'],
            $data['is_enabled'],
            $data['description'],
            $data['meta_title'],
            $data['meta_description'],
            $data['meta_keyword'],
            $data['shop_association'],
            $data['address2'],
            $data['post_code'],
            $data['id_state'],
            $data['phone'],
            $data['mobile_phone']
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

        $this->bus->handle($command);
    }

    /**
     * Fills command with provided data
     *
     * @param EditSupplierCommand $command
     * @param array $data
     */
    private function fillCommandWithData(EditSupplierCommand $command, array $data)
    {
        $command->setName((string) $data['name']);
        $command->setLocalizedDescriptions($data['description']);
        $command->setPhone($data['phone']);
        $command->setMobilePhone($data['mobile_phone']);
        $command->setMobilePhone($data['mobile_phone']);
        $command->setAddress($data['address']);
        $command->setAddress2($data['address2']);
        $command->setPostCode($data['post_code']);
        $command->setCity($data['city']);
        $command->setCountryId($data['id_country']);
        $command->setStateId($data['id_state']);
        $command->setLocalizedMetaTitles($data['meta_title']);
        $command->setLocalizedMetaDescriptions($data['meta_description']);
        $command->setLocalizedMetaKeywords($data['meta_keyword']);
        $command->setEnabled((bool) $data['is_enabled']);

        if (isset($data['shop_association'])) {
            $shopAssociation = $data['shop_association'] ?: [];
            $shopAssociation = array_map(function ($shopId) { return (int) $shopId; }, $shopAssociation);

            $command->setAssociatedShops($shopAssociation);
        }
    }
}
