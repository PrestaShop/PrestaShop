<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation;

use Exception;
use PrestaShop\PrestaShop\Adapter\Localization\LegacyTranslator;
use Symfony\Component\Translation\Exception\InvalidArgumentException;

trait PrestaShopTranslatorTrait
{
    public static $regexSprintfParams = '#(?:%%|%(?:[0-9]+\$)?[+-]?(?:[ 0]|\'.)?-?[0-9]*(?:\.[0-9]+)?[bcdeufFosxX])#';
    public static $regexClassicParams = '/%\w+%/';

    /**
     * Translates the given message.
     *
     * @param string $id The message id (may also be an object that can be cast to string)
     * @param array $parameters An array of parameters for the message
     * @param string|null $domain The domain for the message or null to use the default
     * @param string|null $locale The locale or null to use the default
     *
     * @return string The translated string
     *
     * @throws InvalidArgumentException If the locale contains invalid characters
     */
    public function trans($id, array $parameters = [], $domain = null, $locale = null): string
    {
        $isSprintf = !empty($parameters) && $this->isSprintfString($id);

        if (empty($locale)) {
            $locale = null;
        }

        if ($this->shouldFallbackToLegacyModuleTranslation($id, $domain, $locale)) {
            return $this->translateUsingLegacySystem($id, $parameters, $domain, $locale);
        }

        $translated = parent::trans($id, $isSprintf ? [] : $parameters, $this->normalizeDomain($domain), $locale);

        if ($isSprintf) {
            $translated = vsprintf($translated, $parameters);
        }

        return $translated;
    }

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
    public function getSourceString($translated, $domain, $locale = null)
    {
        if (empty($domain)) {
            return $translated;
        }

        $domain = str_replace('.', '', $domain);
        $contextCatalog = $this->getCatalogue()->all($domain);

        if ($untranslated = array_search($translated, $contextCatalog)) {
            return $untranslated;
        }

        return $translated;
    }

    /**
     * Translates the given choice message by choosing a translation according to a number.
     *
     * @param string $id The message id (may also be an object that can be cast to string)
     * @param int $number The number to use to find the index of the message
     * @param array $parameters An array of parameters for the message
     * @param string|null $domain The domain for the message or null to use the default
     * @param string|null $locale The locale or null to use the default
     *
     * @return string The translated string
     *
     * @throws InvalidArgumentException If the locale contains invalid characters
     */
    public function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null)
    {
        if (null !== $domain) {
            $domain = str_replace('.', '', $domain);
        }

        if (!$this->isSprintfString($id)) {
            return parent::trans($id, array_merge($parameters, ['%count%' => $number]), $domain, $locale);
        }

        return vsprintf(parent::trans($id, ['%count%' => $number], $domain, $locale), $parameters);
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    private function isSprintfString($string)
    {
        return (bool) preg_match_all(static::$regexSprintfParams, $string)
            && !(bool) preg_match_all(static::$regexClassicParams, $string);
    }

    /**
     * Tries to translate the provided message using the legacy system
     *
     * @param string $message
     * @param array $parameters
     * @param string $domain
     * @param string|null $locale
     *
     * @return mixed|string
     *
     * @throws InvalidArgumentException If the locale contains invalid characters
     * @throws Exception
     */
    private function translateUsingLegacySystem($message, array $parameters, $domain, $locale = null)
    {
        $domainParts = explode('.', $domain);
        if (count($domainParts) < 2) {
            throw new InvalidArgumentException(sprintf('Invalid domain: "%s"', $domain));
        }

        $moduleName = strtolower($domainParts[1]);
        $sourceFile = (!empty($domainParts[2])) ? strtolower($domainParts[2]) : $moduleName;

        // translate using the legacy system WITHOUT fallback and escape to the new system (to avoid infinite loop)
        return (new LegacyTranslator())->translate($moduleName, $message, $sourceFile, $parameters, false, $locale, false, false);
    }

    /**
     * Indicates if we should try and translate the provided wording using the legacy system.
     *
     * @param string $message Message to translate
     * @param ?string $domain Translation domain
     * @param ?string $locale Translation locale
     *
     * @return bool
     */
    private function shouldFallbackToLegacyModuleTranslation(string $message, ?string $domain, ?string $locale): bool
    {
        return
            str_starts_with($domain ?? '', 'Modules.')
            && (
                !method_exists($this, 'getCatalogue')
                || !$this->getCatalogue($locale)->has($message, $this->normalizeDomain($domain))
            )
        ;
    }

    /**
     * Returns the domain without separating dots
     *
     * @param string|null $domain Domain name
     *
     * @return string|null
     */
    private function normalizeDomain($domain)
    {
        // remove up to two dots from the domain name
        // (because legacy domain translations CAN have dots in the third part)
        $normalizedDomain = (!empty($domain)) ?
            (new DomainNormalizer())->normalize($domain)
            : null;

        return $normalizedDomain;
    }
}
