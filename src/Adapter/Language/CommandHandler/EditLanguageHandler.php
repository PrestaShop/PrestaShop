<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Language\CommandHandler;

use Language;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\EditLanguageCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\CommandHandler\EditLanguageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;

/**
 * Handles command which edits language using legacy object model
 *
 * @internal
 */
final class EditLanguageHandler implements EditLanguageHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(EditLanguageCommand $command)
    {
        $language = $this->getLegacyLanguageObject($command->getLanguageId());

        $this->updateLanguageWithCommandData($language, $command);
    }

    /**
     * @param LanguageId $languageId
     *
     * @return Language
     */
    private function getLegacyLanguageObject(LanguageId $languageId)
    {
        $language = new Language($languageId);

        if ($languageId->getValue() !== $language->id) {
            throw new LanguageNotFoundException(
                $languageId,
                sprintf('Language with id "%s" was not found', $languageId->getValue())
            );
        }

        return $language;
    }

    /**
     * @param Language $language
     * @param EditLanguageCommand $command
     */
    private function updateLanguageWithCommandData(Language $language, EditLanguageCommand $command)
    {
        if (null !== $command->getName()) {
            $language->name = $command->getName();
        }

        if (null !== $command->getIsoCode()) {
            $language->iso_code = $command->getIsoCode()->getValue();
        }

        if (null !== $command->getTagIETF()) {
            $language->language_code = $command->getTagIETF()->getValue();
        }

        if (null !== $command->getShortDateFormat()) {
            $language->date_format_lite = $command->getShortDateFormat();
        }

        if (null !== $command->getFullDateFormat()) {
            $language->date_format_full = $command->getFullDateFormat();
        }

        if (null !== $command->isRtl()) {
            $language->is_rtl = $command->isRtl();
        }

        if (null !== $command->isActive()) {
            $language->active = $command->isActive();
        }

        if (false === $language->update()) {
            throw new LanguageException(
                sprintf('Cannot update language with id "%s"', $language->id)
            );
        }
    }
}
