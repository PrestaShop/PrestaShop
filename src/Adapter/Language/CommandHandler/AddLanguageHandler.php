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

use Db;
use Language;
use Context;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\AddLanguageCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\CommandHandler\AddLanguageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageWithGivenIsoCodeAlreadyExistsException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageException;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\IsoCode;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use Shop;

/**
 * Handles command which adds new language using legacy object model
 *
 * @internal
 */
final class AddLanguageHandler implements AddLanguageHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(AddLanguageCommand $command)
    {
        $this->assertLanguageWithIsoCodeDoesNotExist($command->getIsoCode());

        $language = $this->createLegacyLanguageObjectFromCommand($command);

        $this->addShopAssociation($language, $command);

        return new LanguageId((int) $language->id);
    }

    /**
     * @param IsoCode $isoCode
     *
     * @throws LanguageWithGivenIsoCodeAlreadyExistsException
     */
    private function assertLanguageWithIsoCodeDoesNotExist(IsoCode $isoCode)
    {
        if (Language::getIdByIso($isoCode)) {
            throw new LanguageWithGivenIsoCodeAlreadyExistsException(
                $isoCode,
                sprintf('Language with ISO code "%s" already exists', $isoCode->getValue())
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
        if (!Shop::isFeatureActive()) {
            return;
        }

        $languageTable = Language::$definition['table'];

        if (!Shop::isTableAssociated($languageTable)) {
            return;
        }

        // Get list of shop id we want to exclude from asso deletion
        $excludeIds = $command->getShopAssociation();
        foreach (Db::getInstance()->executeS('SELECT id_shop FROM ' . _DB_PREFIX_ . 'shop') as $row) {
            if (!Context::getContext()->employee->hasAuthOnShop($row['id_shop'])) {
                $excludeIds[] = $row['id_shop'];
            }
        }

        $excludeShopsCondtion = $excludeIds ?
            ' AND id_shop NOT IN (' . implode(', ', array_map('intval', $excludeIds)) . ')' :
            ''
        ;

        Db::getInstance()->delete(
            $languageTable . '_shop',
            '`id_lang` = ' . (int) $language->id . $excludeShopsCondtion
        );

        $insert = [];
        foreach ($command->getShopAssociation() as $shopId) {
            $insert[] = [
                'id_lang' => (int) $language->id,
                'id_shop' => (int) $shopId,
            ];
        }

        Db::getInstance()->insert(
            $languageTable . '_shop',
            $insert,
            false,
            true,
            Db::INSERT_IGNORE
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
        $language->language_code = $command->getTagIETF();
        $language->date_format_lite = $command->getShortDateFormat();
        $language->date_format_full = $command->getFullDateFormat();
        $language->is_rtl = $command->isRtlLanguage();
        $language->active = $command->isActive();

        if (false === $language->add()) {
            throw new LanguageException(
                sprintf('Failed to add new language "%s"', $command->getName())
            );
        }

        return $language;
    }
}
