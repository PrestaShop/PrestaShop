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
use PrestaShop\PrestaShop\Core\Domain\Language\Command\AddLanguageCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\EditLanguageCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Handles submitted language form data
 */
final class LanguageFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $bus;

    /**
     * @param CommandBusInterface $bus
     */
    public function __construct(CommandBusInterface $bus)
    {
        $this->bus = $bus;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        if (!isset($data['shop_association']) || !$data['shop_association']) {
            $data['shop_association'] = [];
        }

        /** @var UploadedFile $uploadedFlagImage */
        $uploadedFlagImage = $data['flag_image'];
        /** @var UploadedFile $uploadedFlagImage */
        $uploadedNoPictureImage = $data['no_picture_image'];

        /** @var LanguageId $languageId */
        $languageId = $this->bus->handle(new AddLanguageCommand(
            $data['name'],
            $data['iso_code'],
            $data['tag_ietf'],
            $data['short_date_format'],
            $data['full_date_format'],
            $uploadedFlagImage->getPathname(),
            $uploadedNoPictureImage->getPathname(),
            $data['is_rtl'],
            $data['is_active'],
            $data['shop_association']
        ));

        return $languageId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($languageId, array $data)
    {
        $command = (new EditLanguageCommand($languageId))
            ->setName((string) $data['name'])
            ->setIsoCode((string) $data['iso_code'])
            ->setTagIETF((string) $data['tag_ietf'])
            ->setShortDateFormat((string) $data['short_date_format'])
            ->setFullDateFormat((string) $data['full_date_format'])
            ->setIsRtl($data['is_rtl'])
            ->setIsActive($data['is_active'])
        ;

        if ($data['flag_image'] instanceof UploadedFile) {
            $command->setFlagImagePath($data['flag_image']->getPathname());
        }

        if ($data['no_picture_image'] instanceof UploadedFile) {
            $command->setNoPictureImagePath($data['no_picture_image']->getPathname());
        }

        if (isset($data['shop_association'])) {
            $shopAssociation = $data['shop_association'] ?: [];
            $shopAssociation = array_map(function ($shopId) { return (int) $shopId; }, $shopAssociation);

            $command->setShopAssociation($shopAssociation);
        }

        $this->bus->handle($command);
    }
}
