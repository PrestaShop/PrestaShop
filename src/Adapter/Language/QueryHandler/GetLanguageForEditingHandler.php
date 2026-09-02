<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Language\QueryHandler;

use Language;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Language\Query\GetLanguageForEditing;
use PrestaShop\PrestaShop\Core\Domain\Language\QueryHandler\GetLanguageForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Language\QueryResult\EditableLanguage;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;

/**
 * Gets language for editing
 *
 * @internal
 */
#[AsQueryHandler]
final class GetLanguageForEditingHandler implements GetLanguageForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetLanguageForEditing $query)
    {
        $language = $this->getLegacyLanguageObject($query->getLanguageId());

        return new EditableLanguage(
            $query->getLanguageId()->getValue(),
            $language->name,
            $language->iso_code,
            $language->language_code,
            $language->locale,
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
    private function getLegacyLanguageObject(LanguageId $languageId): Language
    {
        $language = new Language($languageId->getValue());

        if ($languageId->getValue() !== (int) $language->id) {
            throw new LanguageNotFoundException($languageId, sprintf('Language with id "%s" was not found', $languageId->getValue()));
        }

        return $language;
    }
}
