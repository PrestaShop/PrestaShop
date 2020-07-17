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

namespace PrestaShop\PrestaShop\Adapter\Language\QueryHandler;

use Language;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Language\Query\GetLanguageForEditing;
use PrestaShop\PrestaShop\Core\Domain\Language\QueryHandler\GetLanguageForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Language\QueryResult\EditableLanguage;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\IsoCode;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\TagIETF;

/**
 * Gets language for editing
 *
 * @internal
 */
final class GetLanguageForEditingHandler implements GetLanguageForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetLanguageForEditing $query)
    {
        $language = $this->getLegacyLanguageObject($query->getLanguageId());

        return new EditableLanguage(
            $query->getLanguageId(),
            $language->name,
            new IsoCode($language->iso_code),
            new TagIETF($language->language_code),
            $language->date_format_lite,
            $language->date_format_full,
            (bool) $language->is_rtl,
            (bool) $language->active,
            array_map(function ($shopId) { return (int) $shopId; }, $language->getAssociatedShops())
        );
    }

    /**
     * @param LanguageId $languageId
     *
     * @return Language
     */
    private function getLegacyLanguageObject(LanguageId $languageId)
    {
        $language = new Language($languageId->getValue());

        if ($languageId->getValue() !== (int) $language->id) {
            throw new LanguageNotFoundException($languageId, sprintf('Language with id "%s" was not found', $languageId->getValue()));
        }

        return $language;
    }
}
