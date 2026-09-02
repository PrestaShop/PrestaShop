<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Language;

use PrestaShop\PrestaShop\Adapter\Language\LanguageDataProvider;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;

/**
 * Fills empty/missing localized values with the default language value, for every active language.
 *
 * This reproduces the legacy back office auto-fill behavior at the CQRS level, so it applies whatever
 * builds the command: a form, an ajax call that only provides the current language, or the Admin API.
 */
class LocalizedNamesFiller
{
    public function __construct(
        private readonly LanguageDataProvider $languageDataProvider,
        private readonly ConfigurationInterface $configuration,
    ) {
    }

    /**
     * Returns the localized values with every active language filled in.
     *
     * Non-empty values from $localizedValues are applied on top of $existingValues, so a partial
     * update (e.g. the ajax call that only sends the current language) keeps the languages it does
     * not touch. Languages that are still empty afterwards are filled with the default value.
     *
     * @param array<int, string> $localizedValues lang-ID-keyed values to apply
     * @param array<int, string> $existingValues lang-ID-keyed values already stored (empty on creation)
     *
     * @return array<int, string>
     */
    public function fill(array $localizedValues, array $existingValues = []): array
    {
        $filledValues = $existingValues;
        foreach ($localizedValues as $languageId => $value) {
            if (!empty($value)) {
                $filledValues[(int) $languageId] = $value;
            }
        }

        $defaultValue = $this->resolveDefaultValue($filledValues);
        if (null === $defaultValue) {
            return $filledValues;
        }

        foreach ($this->languageDataProvider->getLanguages(false, false, true) as $languageId) {
            if (empty($filledValues[(int) $languageId])) {
                $filledValues[(int) $languageId] = $defaultValue;
            }
        }

        return $filledValues;
    }

    /**
     * Returns the value used to fill the empty languages: the default language value when set,
     * otherwise the first non-empty value provided (e.g. the ajax call only sends the current language).
     *
     * @param array<int, string> $localizedValues
     */
    private function resolveDefaultValue(array $localizedValues): ?string
    {
        $defaultLanguageId = (int) $this->configuration->get('PS_LANG_DEFAULT');
        if (!empty($localizedValues[$defaultLanguageId])) {
            return $localizedValues[$defaultLanguageId];
        }

        foreach ($localizedValues as $value) {
            if (!empty($value)) {
                return $value;
            }
        }

        return null;
    }
}
