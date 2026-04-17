<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Language;

/**
 * Interface LanguageInterface defines a language object (iso code, locale,
 * if it is an RTL language, ...)
 */
interface LanguageInterface
{
    /**
     * Database id
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Explicit name of the language
     *
     * @return string
     */
    public function getName(): string;

    /**
     * 2-letter iso code
     *
     * @return string
     */
    public function getIsoCode(): string;

    /**
     * 5-letter iso code
     *
     * @return string
     */
    public function getLocale(): string;

    /**
     * 5-letter iso code
     *
     * @return string
     */
    public function getLanguageCode(): string;

    /**
     * Is the language RTL (read from right to left)
     *
     * @return bool
     */
    public function isRTL(): bool;

    /**
     * Returns format for a date.
     *
     * @return string
     */
    public function getDateFormat(): string;

    /**
     * Returns format for a date and time.
     *
     * @return string
     */
    public function getDateTimeFormat(): string;
}
