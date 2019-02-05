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

use Configuration;
use Language;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\BulkDeleteLanguagesCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\CommandHandler\BulkDeleteLanguagesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\DefaultLanguageException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageException;

/**
 * Deletes languages using legacy Language object model
 *
 * @internal
 */
final class BulkDeleteLanguagesHandler extends AbstractLanguageHandler implements BulkDeleteLanguagesHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteLanguagesCommand $command)
    {
        foreach ($command->getLanguageIds() as $languageId) {
            $language = $this->getLegacyLanguageObject($languageId);

            $this->assertDefaultLanguageIsNotBeingDeleted($language);

            if (false === $language->delete()) {
                throw new LanguageException(sprintf('Failed to delele language "%s"', $language->iso_code));
            }
        }
    }

    /**
     * @param Language $language
     */
    private function assertDefaultLanguageIsNotBeingDeleted(Language $language)
    {
        if ($language->id === (int) Configuration::get('PS_LANG_DEFAULT')) {
            throw new LanguageConstraintException(
                sprintf('Default language "%s" cannot be deleted', $language->iso_code),
                DefaultLanguageException::CANNOT_DELETE_ERROR
            );
        }
    }
}
