<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Translation;

use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface as SymfonyTranslatorInterface;

/**
 * Interface for PrestaShop translators.
 *
 * The core fetches its translator from the container by this interface and the debug environment
 * decorates it, so the concrete class differs between environments. Everything the core actually
 * calls on a translator therefore has to be declared here: setting the locale and reading the
 * catalogue are part of that, and both come from Symfony's own contracts, which every translator
 * built on the framework already satisfies.
 */
interface TranslatorInterface extends SymfonyTranslatorInterface, LocaleAwareInterface, TranslatorBagInterface
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
