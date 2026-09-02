<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
