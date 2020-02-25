<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace PrestaShop\PrestaShop\Adapter\Language\CommandHandler;

use Language;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\AddLanguageCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\CommandHandler\AddLanguageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageException;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\IsoCode;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;

/**
 * Handles command which adds new language using legacy object model
 *
 * @internal
 */
final class AddLanguageHandler extends AbstractLanguageHandler implements AddLanguageHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(AddLanguageCommand $command)
    {
        $this->assertLanguageWithIsoCodeDoesNotExist($command->getIsoCode());

        $this->copyNoPictureImage(
            $command->getIsoCode(),
            $command->getNoPictureImagePath()
        );

        $language = $this->createLegacyLanguageObjectFromCommand($command);

        $this->uploadFlagImage($language, $command);
        $this->addShopAssociation($language, $command);

        return new LanguageId((int) $language->id);
    }

    /**
     * @param IsoCode $isoCode
     *
     * @throws LanguageConstraintException
     */
    private function assertLanguageWithIsoCodeDoesNotExist(IsoCode $isoCode)
    {
        if (Language::getIdByIso($isoCode->getValue())) {
            throw new LanguageConstraintException(
                sprintf('Language with ISO code "%s" already exists', $isoCode->getValue()),
                LanguageConstraintException::DUPLICATE_ISO_CODE
            );
        }
    }

    /**
     * Add language and shop association
     *
     * @param Language $language
     * @param AddLanguageCommand $command
     */
    private function addShopAssociation(Language $language, AddLanguageCommand $command)
    {
        $this->associateWithShops(
            $language,
            $command->getShopAssociation()
        );
    }

    /**
     * @param AddLanguageCommand $command
     *
     * @return Language
     */
    private function createLegacyLanguageObjectFromCommand(AddLanguageCommand $command)
    {
        $language = new Language();
        $language->name = $command->getName();
        $language->iso_code = $command->getIsoCode()->getValue();
        if (false !== ($languageDetails = Language::getLangDetails($command->getIsoCode()->getValue()))) {
            $language->locale = $languageDetails['locale'];
        }
        $language->language_code = $command->getTagIETF()->getValue();
        $language->date_format_lite = $command->getShortDateFormat();
        $language->date_format_full = $command->getFullDateFormat();
        $language->is_rtl = $command->isRtl();
        $language->active = $command->isActive();

        if (false === $language->validateFields(false)) {
            throw new LanguageException('Cannot add language with invalid data');
        }

        if (false === $language->add()) {
            throw new LanguageException(
                sprintf('Failed to add new language "%s"', $command->getName())
            );
        }

        return $language;
    }

    /**
     * @param Language $language
     * @param AddLanguageCommand $command
     */
    private function uploadFlagImage(Language $language, AddLanguageCommand $command)
    {
        $this->uploadImage(
            $language->id,
            $command->getFlagImagePath(),
            'l' . DIRECTORY_SEPARATOR
        );
    }
}
