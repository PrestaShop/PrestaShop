<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Translation;

use Symfony\Contracts\Translation\TranslatorInterface as SymfonyTranslatorInterface;

/**
 * Interface for PrestaShop translators
 */
interface TranslatorInterface extends SymfonyTranslatorInterface
{
    /**
     * Performs a reverse search in the catalogue and returns the translation key if found.
     * AVOID USING THIS, IT PROVIDES APPROXIMATE RESULTS.
     *
     * @param string $translated Translated string
     * @param string $domain Translation domain
     * @param string|null $locale Unused
     *
     * @return string The translation
     *
     * @deprecated This method should not be used and will be removed
     */
    public function getSourceString($translated, $domain, $locale = null);

    /**
     * @param string $locale Locale code for the catalogue to check if loaded
     *
     * @return bool
     */
    public function isLanguageLoaded($locale);

    /**
     * @param string $locale Locale code for the catalogue to be cleared
     */
    public function clearLanguage($locale);
}
