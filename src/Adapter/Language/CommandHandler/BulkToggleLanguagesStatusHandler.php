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

namespace PrestaShop\PrestaShop\Adapter\Language\CommandHandler;

use Configuration;
use Language;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\BulkToggleLanguagesStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\CommandHandler\BulkToggleLanguagesStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\DefaultLanguageException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageException;

/**
 * Toggles multiple languages status using legacy Language object model
 *
 * @internal
 */
final class BulkToggleLanguagesStatusHandler extends AbstractLanguageHandler implements BulkToggleLanguagesStatusHandlerInterface
{
    /**
     * @param BulkToggleLanguagesStatusCommand $command
     */
    public function handle(BulkToggleLanguagesStatusCommand $command)
    {
        foreach ($command->getLanguageIds() as $languageId) {
            $language = $this->getLegacyLanguageObject($languageId);

            $this->assertLanguageIsNotDefault($language, $command);

            $language->active = $command->getStatus();

            if (false === $language->update()) {
                throw new LanguageException(sprintf('Failed to toggle language "%s" to status %s', $language->id, var_export($command->getStatus(), true)));
            }
        }
    }

    /**
     * @param Language $language
     * @param BulkToggleLanguagesStatusCommand $command
     */
    private function assertLanguageIsNotDefault(Language $language, BulkToggleLanguagesStatusCommand $command)
    {
        if (true === $command->getStatus()) {
            return;
        }

        if ($language->id === (int) Configuration::get('PS_LANG_DEFAULT')) {
            throw new DefaultLanguageException(sprintf('Default language "%s" cannot be disabled', $language->iso_code), DefaultLanguageException::CANNOT_DISABLE_ERROR);
        }
    }
}
