<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Language;

use Doctrine\Persistence\ObjectRepository;

/**
 * Interface LanguageRepositoryInterface allows to fetch a LanguageInterface
 * via different methods.
 */
interface LanguageRepositoryInterface extends ObjectRepository
{
    /**
     * Returns a LanguageInterface whose locale matches the provided one.
     *
     * @param string $locale
     *
     * @return LanguageInterface
     */
    public function getOneByLocale($locale);

    /**
     * Returns a LanguageInterface which isoCode matches the provided one.
     *
     * @param string $isoCode
     *
     * @return LanguageInterface
     */
    public function getOneByIsoCode($isoCode);

    /**
     * Returns a LanguageInterface whose locale matches the provided one,
     * if no one is found try matching by isoCode (splitting the locale if
     * necessary).
     *
     * @param string $locale
     *
     * @return LanguageInterface|null
     */
    public function getOneByLocaleOrIsoCode($locale);
}
